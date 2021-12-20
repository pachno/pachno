<?php

    namespace pachno\core\modules\mailing;

    use Exception;
    use League\HTMLToMarkdown\HtmlConverter;
    use pachno\core\entities\Article;
    use pachno\core\entities\Category;
    use pachno\core\entities\Comment;
    use pachno\core\entities\Commit;
    use pachno\core\entities\File;
    use pachno\core\entities\IncomingEmailAccount;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Project;
    use pachno\core\entities\Resolution;
    use pachno\core\entities\Status;
    use pachno\core\entities\tables\MailQueueTable;
    use pachno\core\entities\tables\Settings;
    use pachno\core\entities\User;
    use pachno\core\framework;
    use pachno\core\framework\CoreModule;
    use pachno\core\framework\Event;
    use pachno\core\framework\Settings as FrameworkSettings;
    use pachno\core\modules\mailing\upgrade_32\PachnoIncomingEmailAccountTable;
    use Swift_Mailer;
    use Swift_MailTransport;
    use Swift_Message;
    use Swift_SendmailTransport;
    use Swift_SmtpTransport;

    /**
     * @Table(name="\pachno\core\entities\tables\Modules")
     */
    class Mailing extends CoreModule
    {

        public const VERSION = '2.0.1';

        public const MAIL_TYPE_PHP = 1;

        public const MAIL_TYPE_SMTP = 2;

        public const MAIL_TYPE_SENDMAIL = 3;

        /**
         * Notify the user when a new issue is posted in his/her project(s)
         */
        public const NOTIFY_NEW_ISSUES_MY_PROJECTS = 'notify_new_issues_my_projects';

        public const NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY = 'notify_new_issues_my_projects_category';

        /**
         * Notify the user when a new article is created in his/her project(s)
         */
        public const NOTIFY_NEW_ARTICLES_MY_PROJECTS = 'notify_new_articles_my_projects';

        /**
         * Only notify me once per issue
         */
        public const NOTIFY_ITEM_ONCE = 'notify_issue_once';

        /**
         * Notify the user when an issue he/she subscribes to is updated or commented
         */
        public const NOTIFY_SUBSCRIBED_ISSUES = 'notify_subscribed_issues';

        /**
         * Notify the user when an article he/she subscribes to is updated or commented
         */
        public const NOTIFY_SUBSCRIBED_ARTICLES = 'notify_subscribed_articles';

        /**
         * Notify the user when a discussion he/she subscribes to is updated or commented
         */
        public const NOTIFY_SUBSCRIBED_DISCUSSIONS = 'notify_subscribed_discussions';

        /**
         * Notify the user when he updates an issue
         */
        public const NOTIFY_UPDATED_SELF = 'notify_updated_self';

        /**
         * Notify the user when he is mentioned
         */
        public const NOTIFY_MENTIONED = 'notify_mentioned';

        /**
         * Don't send email notification if user is active
         */
        public const NOTIFY_NOT_WHEN_ACTIVE = 'notify_not_when_active';

        public const MAIL_ENCODING_BASE64 = 3;

        public const MAIL_ENCODING_QUOTED = 4;

        public const MAIL_ENCODING_UTF7 = 0;

        public const SETTING_PROJECT_FROM_ADDRESS = 'project_from_address_';

        public const SETTING_PROJECT_FROM_NAME = 'project_from_name_';

        protected $_name = 'mailing';

        protected $_longname = 'Email communication';

        protected $_description = 'Enables in- and outgoing email functionality';

        protected $_module_config_title = 'Email communication';

        protected $_module_config_description = 'Set up in- and outgoing email communication from this section';

        protected $_account_settings_name = 'Notification settings';

        protected $_account_settings_logo = 'notification_settings.png';

        protected $_has_account_settings = false;

        protected $_has_config_settings = true;

        protected $_ssl_encryption_available;

        protected $_tls_encryption_available;

        protected $mailer = null;

        protected $enabled = true;

        /**
         * Get an instance of this module
         *
         * @return Mailing
         */
        public static function getModule()
        {
            return framework\Context::getModule('mailing');
        }

        public function postConfigSettings(framework\Request $request)
        {
            framework\Context::loadLibrary('common');
            $settings = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pwd', 'smtp_encryption', 'timeout', 'mail_type', 'enable_outgoing_notifications', 'cli_mailing_url',
                'from_name', 'from_addr', 'use_queue', 'activation_needed', 'sendmail_command'];
            foreach ($settings as $setting) {
                if ($request->getParameter($setting) !== null || $setting == 'no_dash_f' || $setting == 'activation_needed') {
                    $value = $request->getParameter($setting);
                    switch ($setting) {
                        case 'smtp_host':
                            if ($request['mail_type'] == self::MAIL_TYPE_SMTP && !pachno_check_syntax($value, "MAILSERVER")) {
                                throw new Exception(framework\Context::getI18n()->__('Please provide a valid setting for SMTP server address'));
                            }
                            break;
                        case 'from_addr':
                            if (!pachno_check_syntax($value, "EMAIL")) {
                                throw new Exception(framework\Context::getI18n()->__('Please provide a valid setting for email "from"-address'));
                            }
                            break;
                        case 'timeout':
                            if ($request['mail_type'] == self::MAIL_TYPE_SMTP && !is_numeric($value) || $value < 0) {
                                throw new Exception(framework\Context::getI18n()->__('Please provide a valid setting for SMTP server timeout'));
                            }
                            break;
                        case 'smtp_port':
                            if ($request['mail_type'] == self::MAIL_TYPE_SMTP && !is_numeric($value) || $value < 1) {
                                throw new Exception(framework\Context::getI18n()->__('Please provide a valid setting for SMTP server port'));
                            }
                            break;
                        case 'activation_needed':
                            $value = (int)$request->getParameter($setting, 0);
                            break;
                        case 'cli_mailing_url':
                            $value = rtrim(trim($request->getParameter($setting)), '/');
                            if (framework\Context::getScope()->isDefault() && !$value) {
                                throw new Exception(framework\Context::getI18n()->__('Please provide a valid setting for the issue tracker url'));
                            }
                            break;
                    }
                    $this->saveSetting($setting, $value);
                }
            }
        }

        public function listen_createUser(Event $event)
        {

        }

        public function isSSLEncryptionAvailable()
        {
            return $this->_ssl_encryption_available;
        }

        public function isTLSEncryptionAvailable()
        {
            return $this->_tls_encryption_available;
        }

        public function listen_getMailingUrl(Event $event)
        {
            $event->setProcessed(true);
            $event->setReturnValue($this->getMailingUrl());
        }

        public function getMailingUrl($clean = false)
        {
            $url = $this->getSetting('cli_mailing_url');
            if ($clean) {
                // a scheme is needed before php 5.4.7
                // thus, let's add the prefix http://
                if (!stristr($url, 'http'))
                    $url = parse_url('http://' . $url);
                else
                    $url = parse_url($url);

                return $url['host'];
            }

            return $url;
        }

        /**
         * @Listener(module='core', identifier='projectActions::addAssignee')
         * @param Event $event
         */
        public function listen_addAssignee(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            $project = $event->getSubject();
            $user = $event->getParameter('assignee');

            if ($user->getEmail()) {
                //                The following line is included for the i18n parser to pick up the translatable string:
                //                __('You have been invited to collaborate');
                $subject = 'You have been invited to collaborate';
                $link_to_activate = $this->generateURL('activate', ['user' => str_replace('.', '%2E', $user->getUsername()), 'key' => $user->getActivationKey()]);
                $parameters = compact('user', 'project', 'link_to_activate');
                $messages = $this->getTranslatedMessages('project_useradded', $parameters, [$user], $subject);

                foreach ($messages as $message) {
                    $this->sendMail($message);
                }
            }
            $event->setProcessed();
        }

        /**
         * @Listener(module='core', identifier='userdata::inviteUser')
         * @param Event $event
         */
        public function listen_inviteUser(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            $user = $event->getSubject();

            if ($user->getEmail()) {
                //                The following line is included for the i18n parser to pick up the translatable string:
                //                __('You have been invited to collaborate');
                $subject = 'You have been invited to collaborate';
                $link_to_activate = $this->generateURL('activate', ['user' => str_replace('.', '%2E', $user->getUsername()), 'key' => $user->getActivationKey()]);
                $parameters = compact('user', 'link_to_activate');
                $messages = $this->getTranslatedMessages('user_invited', $parameters, [$user], $subject);

                foreach ($messages as $message) {
                    $this->sendMail($message);
                }
            }
            $event->setProcessed();
        }

        public function listen_registerUser(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            if ($this->isActivationNeeded()) {
                $user = $event->getSubject();
                $password = User::createPassword(8);
                $user->setPassword($password);
                $user->setActivated(false);
                $user->save();

                if ($user->getEmail()) {
                    //                The following line is included for the i18n parser to pick up the translatable string:
                    //                __('User account registered with Pachno');
                    $subject = 'User account registered with Pachno';
                    $link_to_activate = $this->generateURL('activate', ['user' => str_replace('.', '%2E', $user->getUsername()), 'key' => $user->getActivationKey()]);
                    $parameters = compact('user', 'password', 'link_to_activate');
                    $messages = $this->getTranslatedMessages('registeruser', $parameters, [$user], $subject);

                    foreach ($messages as $message) {
                        $this->sendMail($message);
                    }
                }
                $event->setProcessed();
            }
        }

        public function isOutgoingNotificationsEnabled()
        {
            return (bool)$this->getSetting('enable_outgoing_notifications') && $this->enabled;
        }

        public function isActivationNeeded()
        {
            return (bool)$this->getSetting('activation_needed');
        }

        public function generateURL($route, $parameters = [])
        {
            $url = framework\Context::getRouting()->generate($route, $parameters);

            return $this->getMailingUrl() . $url;
        }

        public function getPrefixedUrl($url)
        {
            return $this->getMailingUrl() . $url;
        }

        public function getTranslatedMessages($template, $parameters, $users, $subject, $subject_parameters = [])
        {
            if (empty($users))
                return [];
            if (!is_array($parameters))
                $parameters = [];
            $langs = $this->getUsersAndLanguages($users);
            $messages = [];
            $parameters['module'] = $this;

            foreach ($langs as $language => $users) {
                $current_language = framework\Context::getI18n()->getCurrentLanguage();
                try {
                    $i18n = framework\Context::getI18n();
                    $i18n->setLanguage($language);
                    $body_parts = $this->getEmailTemplates($template, $parameters);
                    $translated_subject = $i18n->__($subject, $subject_parameters);
                    $message = $this->getSwiftMessage(html_entity_decode($translated_subject, ENT_NOQUOTES, $i18n->getCharset()), $body_parts[0], $body_parts[1]);
                    foreach ($users as $user) {
                        $message->addTo($user->getEmail(), $user->getName());
                    }
                    $messages[] = $message;
                    framework\Context::getI18n()->setLanguage($current_language);
                } catch (Exception $e) {
                    framework\Context::getI18n()->setLanguage($current_language);
                    throw $e;
                }
            }

            return $messages;
        }

        public function getUsersAndLanguages($users)
        {
            $langs = [];
            foreach ($users as $user) {
                if (is_numeric($user))
                    $user = User::getB2DBTable()->selectById($user);

                if ($user instanceof User && $user->getEmail() != '') {
                    $user_language = $user->getLanguage();
                    if (!array_key_exists($user_language, $langs))
                        $langs[$user_language] = [];
                    $langs[$user_language][] = $user;
                }
            }

            return $langs;
        }

        public function getEmailTemplates($template, $parameters = [])
        {
            if (!array_key_exists('module', $parameters))
                $parameters['module'] = $this;
            $message_plain = framework\Action::returnComponentHTML("mailing/{$template}.text", $parameters);
            $html = framework\Action::returnComponentHTML("mailing/{$template}.html", $parameters);
            $styles = file_get_contents(PACHNO_CORE_PATH . 'modules' . DS . 'mailing' . DS . 'fixtures' . DS . 'oxygen.css');
            $message_html = <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <html>
        <head>
            <meta http-equiv=Content-Type content="text/html; charset=utf-8">
            <style type="text/css">
                $styles
            </style>
        </head>
        <body>
            $html
        </body>
    </html>
EOT;

            return [$message_plain, $message_html];
        }

        /**
         * Gets a configured Swift_Message object
         *
         * @param string $subject
         * @param string $message_plain
         * @param string $message_html
         */
        public function getSwiftMessage($subject, $message_plain, $message_html)
        {
            $message = new Swift_Message();
            $message->setSubject($subject);
            $message->setFrom([$this->getEmailFromAddress() => $this->getEmailFromName()]);
            $message->setBody($message_plain);
            $message->addPart($message_html, 'text/html');

            return $message;
        }

        public function getEmailFromAddress()
        {
            return $this->getSetting('from_addr');
        }

        public function getEmailFromName()
        {
            return $this->getSetting('from_name');
        }

        public function sendMail(Swift_Message $email)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            if ($this->usesEmailQueue()) {
                MailQueueTable::getTable()->addMailToQueue($email);

                return true;
            } else {
                $result = $this->mail($email);
            }

            return $result;
        }

        public function usesEmailQueue()
        {
            return (bool)$this->getSetting('use_queue');
        }

        public function mail(Swift_Message $message)
        {
            $mailer = $this->getMailer();

            return $mailer->send($message);
        }

        /**
         * Retrieve the instantiated and configured mailer object
         *
         * @return Swift_Mailer
         */
        public function getMailer()
        {
            if ($this->mailer === null) {
                switch ($this->getMailerType()) {
                    case self::MAIL_TYPE_SMTP:
                        $transport = new Swift_SmtpTransport($this->getSmtpHost(), $this->getSmtpPort());
                        if ($this->getSmtpUsername()) {
                            $transport->setUsername($this->getSmtpUsername());
                            $transport->setPassword($this->getSmtpPassword());
                        }
                        if ($this->getSmtpTimeout()) $transport->setTimeout($this->getSmtpTimeout());
                        if (in_array($this->getSmtpEncryption(), ['ssl', 'tls'])) $transport->setEncryption($this->getSmtpEncryption());
                        break;
                    case self::MAIL_TYPE_SENDMAIL:
                    case self::MAIL_TYPE_PHP:
                    default:
                        $command = $this->getSendmailCommand();
                        $transport = new Swift_SendmailTransport($command);
                }
                $mailer = new Swift_Mailer($transport);
                $this->mailer = $mailer;
            }

            return $this->mailer;
        }

        public function getMailerType()
        {
            $setting = $this->getSetting('mail_type');

            return ($setting) ? $setting : self::MAIL_TYPE_PHP;
        }

        public function getSendmailCommand()
        {
            $setting = $this->getSetting('sendmail_command');

            return $setting ?? '/usr/sbin/sendmail -bs';
        }

        public function getSmtpHost()
        {
            return $this->getSetting('smtp_host');
        }

        public function getSmtpPort()
        {
            return $this->getSetting('smtp_port');
        }

        public function getSmtpUsername()
        {
            return $this->getSetting('smtp_user');
        }

        public function getSmtpPassword()
        {
            return $this->getSetting('smtp_pwd');
        }

        public function getSmtpTimeout()
        {
            return $this->getSetting('timeout');
        }

        public function getSmtpEncryption()
        {
            return $this->getSetting('smtp_encryption');
        }

        public function listen_addScope(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

//                The following line is included for the i18n parser to pick up the translatable string:
//                __('Your account in Pachno has been added to a new scope');
            $subject = 'Your account in Pachno has been added to a new scope';
            $user = $event->getSubject();
            $scope = $event->getParameter('scope');
            $parameters = compact('user', 'scope');
            $messages = $this->getTranslatedMessages('addtoscope', $parameters, [$user], $subject);

            foreach ($messages as $message) {
                $this->sendMail($message);
            }
            $event->setProcessed();
        }

        public function listen_forgottenPassword(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

//                The following line is included for the i18n parser to pick up the translatable string:
//                __('Password reset');
            $subject = 'Password reset';
            $user = $event->getSubject();
            $parameters = ['user' => $user, 'password' => $event->getParameter('password')];
            $messages = $this->getTranslatedMessages('passwordreset', $parameters, [$user], $subject);

            foreach ($messages as $message) {
                $this->sendMail($message);
            }
        }

        public function sendforgottenPasswordEmail($user)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

//                The following line is included for the i18n parser to pick up the translatable string:
//                __('Forgot your password?');
            $subject = 'Forgot your password?';
            $parameters = compact('user');
            $messages = $this->getTranslatedMessages('forgottenpassword', $parameters, [$user], $subject);

            foreach ($messages as $message) {
                $this->sendMail($message);
            }
        }

        public function sendTestEmail($email_address)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                throw new Exception(framework\Context::getI18n()->__('The email module is not configured for outgoing emails'));
            }

            $subject = framework\Context::getI18n()->__('Test email');
            $body_parts = $this->getEmailTemplates('testemail');
            $message = $this->getSwiftMessage($subject, $body_parts[0], $body_parts[1]);
            $message->addTo($email_address);

            return $this->sendMail($message);
        }

        public function listen_issueCreate(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            $issue = $event->getSubject();
            if ($issue instanceof Issue) {
                $subject = '[' . $issue->getProject()->getKey() . '] ' . $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . ' - ' . html_entity_decode($issue->getTitle(), ENT_COMPAT, framework\Context::getI18n()->getCharset());
                $parameters = compact('issue');
                $to_users = $issue->getRelatedUsers();
                if (!$this->getSetting(self::NOTIFY_UPDATED_SELF, framework\Context::getUser()->getID()))
                    unset($to_users[framework\Context::getUser()->getID()]);

                foreach ($to_users as $uid => $user) {
                    if ($user->getNotificationSetting(self::NOTIFY_NEW_ISSUES_MY_PROJECTS, true, 'mailing')->isOff() || !$issue->hasAccess($user) || ($user->getNotificationSetting(self::NOTIFY_NEW_ISSUES_MY_PROJECTS, true, 'mailing')->isOn() && $user->getNotificationSetting(self::NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY, false, 'mailing')->isOn() && ($issue->getCategory() instanceof Category && $user->getNotificationSetting(self::NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY, 0, 'mailing')->getValue() != $issue->getCategory()->getID()))) unset($to_users[$uid]);
                    if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive()) unset($to_users[$uid]);
                }
                $messages = $this->getTranslatedMessages('issuecreate', $parameters, $to_users, $subject);

                foreach ($messages as $message) {
                    $this->_addProjectEmailAddress($message, $issue->getProject());
                    $this->sendMail($message);
                }
            }
        }

        protected function _addProjectEmailAddress(Swift_Message $message, Project $project = null)
        {
            if ($project instanceof Project) {
                $address = $this->getSetting(self::SETTING_PROJECT_FROM_ADDRESS . $project->getID());
                $name = $this->getSetting(self::SETTING_PROJECT_FROM_NAME . $project->getID());
                if ($address != '') {
                    $message->setFrom($address, $name);
                }
            }
        }

        public function listen_Article_doSave(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            $article = $event->getSubject();
            $change_reason = $event->getParameter('reason');
            $revision = $event->getParameter('revision');
            $subject = 'Wiki article updated: %article_name';
            $user = User::getB2DBTable()->selectById((int)$event->getParameter('user_id'));
            $parameters = compact('article', 'change_reason', 'user', 'revision');
            $to_users = $this->_getArticleRelatedUsers($article, $user);

            if (!empty($to_users)) {
                $this->_markArticleSent($article, $to_users);
                $messages = $this->getTranslatedMessages('articleupdate', $parameters, $to_users, $subject, ['%article_name' => html_entity_decode($article->getTitle(), ENT_COMPAT, framework\Context::getI18n()->getCharset())]);

                foreach ($messages as $message) {
                    if ($project = $article->getProject()) {
                        $this->_addProjectEmailAddress($message, $project);
                    }
                    $this->sendMail($message);
                }
            }
        }

        protected function _getArticleRelatedUsers(Article $article, User $triggered_by_user = null)
        {
            $u_id = ($triggered_by_user instanceof User) ? $triggered_by_user->getID() : $triggered_by_user;
            $subscribers = $article->getSubscribers();
            $users = [];
            foreach ($subscribers as $user) {
                if ($user->getNotificationSetting(self::NOTIFY_SUBSCRIBED_ARTICLES, true, 'mailing')->isOff())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_UPDATED_SELF, true, 'mailing')->isOff() && $user->getID() == $u_id)
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn() && $user->getNotificationSetting(self::NOTIFY_ITEM_ONCE . '_article_' . $article->getID(), false, 'mailing')->isOn())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive())
                    unset($users[$user->getID()]);
            }
            $mentioned_users = $article->getMentionedUsers();
            foreach ($mentioned_users as $user) {
                $users[$user->getID()] = $user;

                if ($user->getNotificationSetting(self::NOTIFY_MENTIONED, true, 'mailing')->isOff())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn() && $user->getNotificationSetting(self::NOTIFY_ITEM_ONCE . '_article_' . $article->getID(), false, 'mailing')->isOn())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive())
                    unset($users[$user->getID()]);
            }

            return $users;
        }

        protected function _getCommitRelatedUsers(Commit $commit, User $triggered_by_user = null)
        {
            $u_id = ($triggered_by_user instanceof User) ? $triggered_by_user->getID() : $triggered_by_user;
            $subscribers = $commit->getSubscribers();
            $users = [];
            foreach ($subscribers as $user) {
                if ($user->getNotificationSetting(self::NOTIFY_SUBSCRIBED_DISCUSSIONS, true, 'mailing')->isOff())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_UPDATED_SELF, true, 'mailing')->isOff() && $user->getID() == $u_id)
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn() && $user->getNotificationSetting(self::NOTIFY_ITEM_ONCE . '_commit_' . $commit->getID(), false, 'mailing')->isOn())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive())
                    unset($users[$user->getID()]);
            }
            $mentioned_users = $commit->getMentionedUsers();
            foreach ($mentioned_users as $user) {
                $users[$user->getID()] = $user;

                if ($user->getNotificationSetting(self::NOTIFY_MENTIONED, true, 'mailing')->isOff())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn() && $user->getNotificationSetting(self::NOTIFY_ITEM_ONCE . '_commit_' . $commit->getID(), false, 'mailing')->isOn())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive())
                    unset($users[$user->getID()]);
            }

            return $users;
        }

        /**
         * Adds "notify once" settings for necessary articles
         *
         * @param Article $article
         * @param array|User $users
         */
        protected function _markArticleSent(Article $article, $users)
        {
            foreach ($users as $user) {
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn()) {
                    $user->setNotificationSetting(self::NOTIFY_ITEM_ONCE . '_article_' . $article->getID(), true, 'mailing');
                }
            }
        }

        /**
         * Adds "notify once" settings for necessary commits
         *
         * @param Commit $commit
         * @param array|User $users
         */
        protected function _markCommitSent(Commit $commit, $users)
        {
            foreach ($users as $user) {
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn()) {
                    $user->setNotificationSetting(self::NOTIFY_ITEM_ONCE . '_commit_' . $commit->getID(), true, 'mailing');
                }
            }
        }

        public function listen_pachno_core_entities_Comment_createNew(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            $comment = $event->getSubject();
            if ($comment instanceof Comment) {
                switch ($comment->getTargetType()) {
                    case Comment::TYPE_ISSUE:
                        $issue = $event->getParameter('issue');
                        $project = $issue->getProject();
                        $subject = 'Re: [' . $issue->getProject()->getKey() . '] ' . $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . ' - ' . html_entity_decode($issue->getTitle(), ENT_COMPAT, framework\Context::getI18n()->getCharset());
                        $parameters = compact('issue', 'comment');
                        $to_users = $this->_getIssueRelatedUsers($issue, $comment->getPostedBy());
                        $this->_markIssueSent($issue, $to_users);
                        $messages = $this->getTranslatedMessages('issuecomment', $parameters, $to_users, $subject);
                        break;
                    case Comment::TYPE_ARTICLE:
                        $article = $event->getParameter('article');
                        $project = $article->getProject();
                        $subject = 'Comment posted on article %article_name';
                        $parameters = compact('article', 'comment');
                        $to_users = $this->_getArticleRelatedUsers($article, $comment->getPostedBy());
                        $this->_markArticleSent($article, $to_users);
                        $messages = (empty($to_users)) ? [] : $this->getTranslatedMessages('articlecomment', $parameters, $to_users, $subject, ['%article_name' => html_entity_decode($article->getTitle(), ENT_COMPAT, framework\Context::getI18n()->getCharset())]);
                        break;
                    case Comment::TYPE_COMMIT:
                        $commit = $event->getParameter('commit');
                        $project = $commit->getProject();
                        $subject = 'Comment posted on commit %commit_hash';
                        $parameters = compact('commit', 'comment');
                        $to_users = $this->_getCommitRelatedUsers($commit, $comment->getPostedBy());
                        $this->_markCommitSent($commit, $to_users);
                        $messages = (empty($to_users)) ? [] : $this->getTranslatedMessages('commitcomment', $parameters, $to_users, $subject, ['%commit_hash' => html_entity_decode($commit->getShortRevision(), ENT_COMPAT, framework\Context::getI18n()->getCharset())]);
                        break;
                }

                foreach ($messages as $message) {
                    if (isset($project) && $project instanceof Project) {
                        $this->_addProjectEmailAddress($message, $project);
                    }
                    $this->sendMail($message);
                }
            }
        }

        protected function _getIssueRelatedUsers(Issue $issue, $postedby = null)
        {
            $u_id = ($postedby instanceof User) ? $postedby->getID() : $postedby;
            $subscribers = $issue->getSubscribers();
            $users = [];
            foreach ($subscribers as $user) {
                $users[$user->getID()] = $user;

                if ($user->getNotificationSetting(self::NOTIFY_SUBSCRIBED_ISSUES, true, 'mailing')->isOff())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_UPDATED_SELF, true, 'mailing')->isOff() && $user->getID() == $u_id)
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn() && $user->getNotificationSetting(self::NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), false, 'mailing')->isOn())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive())
                    unset($users[$user->getID()]);
            }
            $mentioned_users = $issue->getMentionedUsers();
            foreach ($mentioned_users as $user) {
                $users[$user->getID()] = $user;

                if ($user->getNotificationSetting(self::NOTIFY_MENTIONED, true, 'mailing')->isOff())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn() && $user->getNotificationSetting(self::NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), false, 'mailing')->isOn())
                    unset($users[$user->getID()]);
                if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive())
                    unset($users[$user->getID()]);
            }

            return $users;
        }

        /**
         * Adds "notify once" settings for necessary issues
         *
         * @param Issue $issue
         * @param array|User $users
         */
        protected function _markIssueSent(Issue $issue, $users)
        {
            foreach ($users as $user) {
                if ($user->getNotificationSetting(self::NOTIFY_ITEM_ONCE, false, 'mailing')->isOn()) {
                    $user->setNotificationSetting(self::NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), true, 'mailing');
                }
            }
        }

        public function listen_issueSave(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            $issue = $event->getSubject();
            if ($issue instanceof Issue) {
                $subject = 'Re: [' . $issue->getProject()->getKey() . '] ' . $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . ' - ' . html_entity_decode($issue->getTitle(), ENT_COMPAT, framework\Context::getI18n()->getCharset());
                $parameters = ['issue' => $issue, 'comment' => $event->getParameter('comment'), 'log_items' => $event->getParameter('log_items'), 'updated_by' => $event->getParameter('updated_by')];
                $to_users = $this->_getIssueRelatedUsers($issue, $parameters['updated_by']);
                if (!$this->getSetting(self::NOTIFY_UPDATED_SELF, framework\Context::getUser()->getID()))
                    unset($to_users[framework\Context::getUser()->getID()]);

                foreach ($to_users as $uid => $user) {
                    if (!$issue->hasAccess($user))
                        unset($to_users[$uid]);
                    if ($user->getNotificationSetting(self::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn() && $user->isActive())
                        unset($to_users[$uid]);
                }
                $this->_markIssueSent($issue, $to_users);
                $messages = $this->getTranslatedMessages('issueupdate', $parameters, $to_users, $subject);

                foreach ($messages as $message) {
                    $this->_addProjectEmailAddress($message, $issue->getProject());
                    $this->sendMail($message);
                }
            }
        }

        public function listen_issueSubscribeUser(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            $issue = $event->getSubject();
            $user = $event->getParameter('user');
            if ($issue instanceof Issue) {
                $subject = 'Re: [' . $issue->getProject()->getKey() . '] ' . $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . ' - ' . html_entity_decode($issue->getTitle(), ENT_COMPAT, framework\Context::getI18n()->getCharset());
                $parameters = ['issue' => $issue];
                $to_users = [$user];
                $messages = $this->getTranslatedMessages('issuesubscribed', $parameters, $to_users, $subject);

                foreach ($messages as $message) {
                    $this->_addProjectEmailAddress($message, $issue->getProject());
                    $this->sendMail($message);
                }
                $user->setNotificationSetting(self::NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), false, 'mailing');
            }
        }

        public function listen_viewissue(Event $event)
        {
            if (!$event->getSubject() instanceof Issue)
                return;

            framework\Context::getUser()->setNotificationSetting(self::NOTIFY_ITEM_ONCE . '_issue_' . $event->getSubject()->getID(), false, 'mailing');
        }

        public function listen_loginPane(Event $event)
        {
            if ($this->isOutgoingNotificationsEnabled()) {
                framework\ActionComponent::includeComponent('mailing/forgotPasswordPane', $event->getParameters());
            }
        }

        public function listen_loginButtonContainer(Event $event)
        {
            if ($this->isOutgoingNotificationsEnabled()) {
                framework\ActionComponent::includeComponent('mailing/forgotPasswordLink', $event->getParameters());
            }
        }

        public function listen_userDropdownAnon(Event $event)
        {
            if ($this->isOutgoingNotificationsEnabled()) {
                framework\ActionComponent::includeComponent('mailing/userDropdownAnon', $event->getParameters());
            }
        }

        public function listen_projectconfig_tab_settings(Event $event)
        {
            //framework\ActionComponent::includeComponent('mailing/projectconfig_tab_settings', ['selected_tab' => $event->getParameter('selected_tab')]);
        }

        public function listen_projectconfig_tab_other(Event $event)
        {
            //framework\ActionComponent::includeComponent('mailing/projectconfig_tab_other', ['selected_tab' => $event->getParameter('selected_tab')]);
        }

        public function listen_projectconfig_panel(Event $event)
        {
            //framework\ActionComponent::includeComponent('mailing/projectconfig_panels', ['selected_tab' => $event->getParameter('selected_tab'), 'access_level' => $event->getParameter('access_level'), 'project' => $event->getParameter('project')]);
        }

        public function listen_accountNotificationSettings(Event $event)
        {
            framework\ActionComponent::includeComponent('mailing/accountsettings', ['notificationsettings' => $this->_getNotificationSettings()]);
        }

        protected function _getNotificationSettings()
        {
            $i18n = framework\Context::getI18n();
            $notificationsettings = [];
            $notificationsettings[self::NOTIFY_SUBSCRIBED_ISSUES] = $i18n->__('Notify when there are updates to my subscribed issues');
            $notificationsettings[self::NOTIFY_SUBSCRIBED_ARTICLES] = $i18n->__('Notify when there are updates to my subscribed articles');
            $notificationsettings[self::NOTIFY_NEW_ISSUES_MY_PROJECTS] = $i18n->__('Notify when new issues are created in my project(s)');
            $notificationsettings[self::NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY] = $i18n->__('New created issues have category');
            $notificationsettings[self::NOTIFY_NEW_ARTICLES_MY_PROJECTS] = $i18n->__('Notify when new articles are created in my project(s)');
            $notificationsettings[self::NOTIFY_ITEM_ONCE] = $i18n->__('Only notify once per issue or article until I view the issue or article in my browser');
            $notificationsettings[self::NOTIFY_UPDATED_SELF] = $i18n->__('Notify also when I am the one making the changes');
            $notificationsettings[self::NOTIFY_MENTIONED] = $i18n->__('Notify when I am mentioned in issue or article or their comment');
            $notificationsettings[self::NOTIFY_NOT_WHEN_ACTIVE] = $i18n->__("Don't send email notification if I'm currently logged in and active");

            return $notificationsettings;
        }

        public function listen_accountNotificationSettingsThead(Event $event)
        {
            framework\ActionComponent::includeComponent('mailing/accountsettings_thead');
        }

        public function listen_accountNotificationSettingsCell(Event $event)
        {
            framework\ActionComponent::includeComponent('mailing/accountsettings_cell', ['notificationsettings' => $this->_getNotificationSettings(), 'key' => $event->getParameter('key')]);
        }

        public function listen_accountNotificationSettingsNotificationCategories(Event $event)
        {
            framework\ActionComponent::includeComponent('mailing/accountsettings_notificationcategories', ['categories' => $event->getParameter('categories')]);
        }

        public function listen_accountNotificationSettingsSubscriptions(Event $event)
        {
            framework\ActionComponent::includeComponent('mailing/accountsettings_subscriptions');
        }

        public function listen_configCreateuserEmail(Event $event)
        {
            framework\ActionComponent::includeComponent('mailing/configcreateuseremail');
        }

        public function listen_configCreateuserSave(Event $event)
        {
            if (!$this->isOutgoingNotificationsEnabled()) {
                return;
            }

            if (framework\Context::getRequest()->getParameter('send_login_details')) {
                $user = $event->getSubject();
                if ($user instanceof User) {
                    $subject = 'User account created';
                    $parameters = ['user' => $user, 'password' => $event->getParameter('password')];
                    $to_users = [$user];
                    $messages = $this->getTranslatedMessages('config_usercreated', $parameters, $to_users, $subject);

                    foreach ($messages as $message) {
                        $this->sendMail($message);
                    }
                }
            }
        }

        public function listen_accountSaveNotificationSettings(Event $event)
        {
            $request = $event->getParameter('request');
            $notificationsettings = $this->_getNotificationSettings();
            $category_key = self::NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY;

            foreach ($notificationsettings as $setting => $description) {
                if ($setting == $category_key) continue;
                if ($request->hasParameter('mailing_' . $setting)) {
                    framework\Context::getUser()->setNotificationSetting($setting, true, 'mailing');
                } else {
                    framework\Context::getUser()->setNotificationSetting($setting, false, 'mailing');
                }
            }

            foreach ($event->getParameter('categories') as $category_id => $category) {
                if ($request->hasParameter('mailing_' . $category_key . '_' . $category_id)) {
                    framework\Context::getUser()->setNotificationSetting($category_key . '_' . $category_id, true, 'mailing');
                } else {
                    framework\Context::getUser()->setNotificationSetting($category_key . '_' . $category_id, false, 'mailing');
                }
            }
        }

        /**
         * @Listener(module='core', identifier='get_backdrop_partial')
         * @param Event $event
         */
        public function listen_get_backdrop_partial(Event $event)
        {
            if ($event->getSubject() == 'mailing_editincomingemailaccount') {
                $account = new IncomingEmailAccount(framework\Context::getRequest()->getParameter('account_id'));
                $event->addToReturnList($account, 'account');
                $event->setReturnValue('mailing/editincomingemailaccount');
                $event->setProcessed();
            }
        }

        public function temporarilyDisable()
        {
            $this->enabled = false;
        }

        public function removeTemporarilyDisable()
        {
            $this->enabled = true;
        }

        public function setOutgoingNotificationsEnabled($enabled = true)
        {
            $this->saveSetting('enable_outgoing_notifications', $enabled);
        }

        function getMailPart($stream, $msg_number, $mime_type, $structure, $part_number = false)
        {
            if ($mime_type == $this->getMailMimeType($structure)) {
                if (!$part_number) {
                    $part_number = "1";
                }
                $text = imap_fetchbody($stream, $msg_number, $part_number);
                if ($structure->encoding == self::MAIL_ENCODING_BASE64) {
                    $ret_val = imap_base64($text);
                } elseif ($structure->encoding == self::MAIL_ENCODING_QUOTED) {
                    $ret_val = imap_qprint($text);
                } else {
                    $ret_val = $text;
                }

                return $ret_val;
            }

            if ($structure->type == 1) /* multipart */ {
                while (list($index, $sub_structure) = each($structure->parts)) {
                    if ($part_number) {
                        $prefix = $part_number . '.';
                    }
                    $data = $this->getMailPart($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
                    if ($data) {
                        return $data;
                    }
                } // END OF WHILE
            } // END OF MULTIPART

            return false;
        }

        function getMailMimeType($structure)
        {
            $primary_mime_type = ["TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"];
            if ($structure->subtype) {
                $type = $primary_mime_type[(int)$structure->type] . '/' . $structure->subtype;
            } else {
                $type = "TEXT/PLAIN";
            }

            return $type;
        }

        function getMailAttachments($structure, $connection, $message_number)
        {
            $attachments = [];
            if (isset($structure->parts) && count($structure->parts)) {
                for ($i = 0; $i < count($structure->parts); $i++) {
                    $attachments[$i] = [
                        'is_attachment' => false,
                        'filename' => '',
                        'name' => '',
                        'mimetype' => '',
                        'attachment' => ''];

                    if ($structure->parts[$i]->ifdparameters) {
                        foreach ($structure->parts[$i]->dparameters as $object) {
                            if (strtolower($object->attribute) == 'filename') {
                                $attachments[$i]['is_attachment'] = true;
                                $attachments[$i]['filename'] = $object->value;
                            }
                        }
                    }

                    if ($structure->parts[$i]->ifparameters) {
                        foreach ($structure->parts[$i]->parameters as $object) {
                            if (strtolower($object->attribute) == 'name') {
                                $attachments[$i]['is_attachment'] = true;
                                $attachments[$i]['name'] = $object->value;
                            }
                        }
                    }

                    if ($attachments[$i]['is_attachment']) {
                        $attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i + 1);
                        if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                            $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                        } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                            $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                        }
                        $attachments[$i]['mimetype'] = $structure->parts[$i]->type . "/" . $structure->parts[$i]->subtype;
                    } else {
                        unset($attachments[$i]);
                    }
                } // for($i = 0; $i < count($structure->parts); $i++)
            } // if(isset($structure->parts) && count($structure->parts))

            return $attachments;
        }

        public function getIncomingEmailAccounts()
        {
            return IncomingEmailAccount::getAll();
        }

        public function getIncomingEmailAccountsForProject(Project $project)
        {
            return IncomingEmailAccount::getAllByProjectID($project->getID());
        }

        public function processIncomingEmailAccount(IncomingEmailAccount $account)
        {
            $count = 0;
            if ($emails = $account->getUnprocessedEmails()) {
                try {
                    $current_user = framework\Context::getUser();
                    foreach ($emails as $email) {
                        $user = $this->getOrCreateUserFromEmailString($email->from);

                        if ($user instanceof User) {
                            if (framework\Context::isCLI() || framework\Context::getUser()->getID() != $user->getID())
                                framework\Context::switchUserContext($user);

                            $message = $account->getMessage($email);

                            if ($message->getBodyPlain() && !$account->prefersHtml()) {
                                $data = $message->getBodyPlain();
                            } else {
                                $converter = new HtmlConverter(['strip_tags' => true]);
                                $data = $converter->convert($message->getBodyHTML());
                            }

                            // Parse the subject, and obtain the issues.
                            $parsed_commit = Issue::getIssuesFromTextByRegex(mb_decode_mimeheader($email->subject));
                            $issues = $parsed_commit["issues"];

                            // If any issues were found, add new comment to each issue.
                            if ($issues) {
                                foreach ($issues as $issue) {
                                    $text = preg_replace('#(^\w.+:\n)?(^>.*(\n|$))+#mi', "", $data);
                                    $text = trim($text);
                                    if (!$this->processIncomingEmailCommand($text, $issue) && $user->canPostComments(Comment::TYPE_ISSUE, $issue->getProject())) {
                                        $comment = new Comment();
                                        $comment->setSyntax(FrameworkSettings::SYNTAX_MD);
                                        $comment->setContent($text);
                                        $comment->setPostedBy($user);
                                        $comment->setTargetID($issue->getID());
                                        $comment->setTargetType(Comment::TYPE_ISSUE);
                                        $comment->save();
                                    }
                                }
                            }
                            // If not issues were found, open a new issue if user has the
                            // proper permissions.
                            else {
                                if ($user->canReportIssues($account->getProject())) {
                                    $issue = new Issue();
                                    $issue->setProject($account->getProject());
                                    $issue->setTitle(mb_decode_mimeheader($email->subject));
                                    $issue->setDescriptionSyntax(FrameworkSettings::SYNTAX_MD);
                                    $issue->setDescription($data);
                                    $issue->setPostedBy($user);
                                    $issue->setIssuetype($account->getIssuetype()->getID());
                                    $issue->save();
                                    // Append the new issue to the list of affected issues. This
                                    // is necessary in order to process the attachments properly.
                                    $issues[] = $issue;
                                }
                            }

                            // If there was at least a single affected issue, and mail
                            // contains attachments, add those attachments to related issues.
                            if ($issues && $message->hasAttachments() && framework\Settings::isUploadsEnabled()) {
                                foreach ($message->getAttachments() as $attachment_no => $attachment) {
                                    $name = iconv_mime_decode($attachment['filename'], ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
                                    $new_filename = framework\Context::getUser()->getID() . '_' . NOW . '_' . basename($name);
                                    if (framework\Settings::getUploadStorage() == 'files') {
                                        $files_dir = framework\Settings::getUploadsLocalpath();
                                        $filename = $files_dir . $new_filename;
                                    } else {
                                        $filename = $name;
                                    }
                                    framework\Logging::log('Creating issue attachment ' . $filename . ' from attachment ' . $attachment_no);
                                    $content_type = $attachment['type'] . '/' . $attachment['subtype'];
                                    $file = new File();
                                    $file->setRealFilename($new_filename);
                                    $file->setOriginalFilename(basename($name));
                                    $file->setContentType($content_type);
                                    $file->setDescription($name);
                                    $file->setUploadedBy(framework\Context::getUser());
                                    if (framework\Settings::getUploadStorage() == 'database') {
                                        $file->setContent($attachment['data']);
                                    } else {
                                        framework\Logging::log('Saving file ' . $new_filename . ' with content from attachment ' . $attachment_no);
                                        file_put_contents($new_filename, $attachment['data']);
                                    }
                                    $file->save();
                                    // Attach file to each related issue.
                                    foreach ($issues as $issue) {
                                        $issue->attachFile($file);
                                    }
                                }
                            }

                            $count++;
                        }
                    }
                } catch (Exception $e) {

                }
                if (!framework\Context::isCLI())
                    if (framework\Context::getUser()->getID() != $current_user->getID())
                        framework\Context::switchUserContext($current_user);
            }
            $account->setTimeLastFetched(time());
            $account->setNumberOfEmailsLastFetched($count);
            $account->save();

            return $count;
        }

        public function getOrCreateUserFromEmailString($email_string)
        {
            $email = $this->getEmailAdressFromSenderString($email_string);
            if (!$user = User::findUser($email, true)) {
                $name = $email;

                if (($q_pos = strpos($email_string, "<")) !== false) {
                    $name = trim(substr($email_string, 0, $q_pos - 1));
                }

                $user = new User();

                try {
                    $user->setBuddyname($name);
                    $user->setEmail($email);
                    $user->setUsername($email);
                    $user->setValidated();
                    $user->setActivated();
                    $user->setEnabled();
                    $user->save();
                } catch (Exception $e) {
                    return null;
                }
            }

            return $user;
        }

        public function getEmailAdressFromSenderString($from)
        {
            $tokens = explode(" ", $from);
            foreach ($tokens as $email) {
                $email = str_replace(["<", ">"], ["", ""], $email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL))
                    return $email;
            }
        }

        public function processIncomingEmailCommand($content, Issue $issue)
        {
            if (!$issue->isWorkflowTransitionsAvailable())
                return false;

            $lines = preg_split("/(\r?\n)/", $content);
            $first_line = array_shift($lines);
            $commands = explode(" ", trim($first_line));
            $command = array_shift($commands);
            foreach ($issue->getAvailableWorkflowTransitions() as $transition) {
                if (strpos(str_replace([' ', '/'], ['', ''], mb_strtolower($transition->getName())), str_replace([' ', '/'], ['', ''], mb_strtolower($command))) !== false) {
                    foreach ($commands as $single_command) {
                        if (mb_strpos($single_command, '=')) {
                            list($key, $val) = explode('=', $single_command);
                            switch ($key) {
                                case 'resolution':
                                    if (($resolution = Resolution::getByKeyish($val)) instanceof Resolution) {
                                        framework\Context::getRequest()->setParameter('resolution_id', $resolution->getID());
                                    }
                                    break;
                                case 'status':
                                    if (($status = Status::getByKeyish($val)) instanceof Status) {
                                        framework\Context::getRequest()->setParameter('status_id', $status->getID());
                                    }
                                    break;
                            }
                        }
                    }
                    framework\Context::getRequest()->setParameter('comment_body', join("\n", $lines));

                    return $transition->transitionIssueToOutgoingStepWithoutRequest($issue);
                }
            }
        }

        public function getFontAwesomeIcon()
        {
            return 'envelope';
        }

        public function getFontAwesomeColor()
        {
            return '#555';
        }

        protected function _initialize()
        {
            $transports = stream_get_transports();
            $this->_ssl_encryption_available = in_array('ssl', $transports);
            $this->_tls_encryption_available = in_array('tls', $transports);
        }

        protected function _addListeners()
        {
            Event::listen('core', 'pachno\core\entities\User::_postSave', [$this, 'listen_registerUser']);
            Event::listen('core', 'password_reset', [$this, 'listen_forgottenPassword']);
            Event::listen('core', 'login_form_pane', [$this, 'listen_loginPane']);
            Event::listen('core', 'login_button_container', [$this, 'listen_loginButtonContainer']);
            Event::listen('core', 'pachno\core\entities\User::addScope', [$this, 'listen_addScope']);
            Event::listen('core', 'pachno\core\entities\Issue::createNew', [$this, 'listen_issueCreate']);
            Event::listen('core', 'pachno\core\entities\User::_postSave', [$this, 'listen_createUser']);
            Event::listen('core', 'pachno\core\entities\Issue::save', [$this, 'listen_issueSave']);
            Event::listen('core', 'pachno\core\entities\Comment::createNew', [$this, 'listen_pachno_core_entities_Comment_createNew']);
            Event::listen('core', 'pachno\core\entities\Comment::_postSave', [$this, 'listen_pachno_core_entities_Comment_createNew']);
            Event::listen('core', 'pachno\core\entities\Article::doSave', [$this, 'listen_Article_doSave']);
            Event::listen('core', 'viewissue', [$this, 'listen_viewissue']);
            Event::listen('core', 'issue_subscribe_user', [$this, 'listen_issueSubscribeUser']);
            Event::listen('core', 'user_dropdown_anon', [$this, 'listen_userDropdownAnon']);
            Event::listen('core', 'config_project_tabs_settings', [$this, 'listen_projectconfig_tab_settings']);
            Event::listen('core', 'config_project_tabs_other', [$this, 'listen_projectconfig_tab_other']);
            Event::listen('core', 'config_project_panes', [$this, 'listen_projectconfig_panel']);
            Event::listen('core', 'account_pane_notificationsettings', [$this, 'listen_accountNotificationSettings']);
            Event::listen('core', 'account_pane_notificationsettings_table_header', [$this, 'listen_accountNotificationSettingsThead']);
            Event::listen('core', 'account_pane_notificationsettings_cell', [$this, 'listen_accountNotificationSettingsCell']);
            Event::listen('core', 'account_pane_notificationsettings_notification_categories', [$this, 'listen_accountNotificationSettingsNotificationCategories']);
            Event::listen('core', 'account_pane_notificationsettings_subscriptions', [$this, 'listen_accountNotificationSettingsSubscriptions']);
            Event::listen('core', 'config.createuser.email', [$this, 'listen_configCreateuserEmail']);
            Event::listen('core', 'config.createuser.save', [$this, 'listen_configCreateuserSave']);
            Event::listen('core', 'mainActions::myAccount::saveNotificationSettings', [$this, 'listen_accountSaveNotificationSettings']);
            Event::listen('core', 'pachno\core\framework\helpers\TextParser::_parseIssuelink::urlPrefix', [$this, 'listen_getMailingUrl']);
        }

        protected function _install($scope)
        {
            $this->saveSetting('smtp_host', '', 0, $scope);
            $this->saveSetting('smtp_port', 25, 0, $scope);
            $this->saveSetting('smtp_user', '', 0, $scope);
            $this->saveSetting('smtp_pwd', '', 0, $scope);
            $this->saveSetting('from_name', 'Pachno Automailer', 0, $scope);
            $this->saveSetting('from_addr', '', 0, $scope);
            $this->saveSetting('ehlo', 1, 0, $scope);
        }

        protected function _uninstall()
        {
            parent::_uninstall();
        }

        protected function _upgrade()
        {
            switch ($this->_version) {
                case '1.0':
                    IncomingEmailAccount::getB2DBTable()->upgrade(PachnoIncomingEmailAccountTable::getTable());
                    Settings::getTable()->deleteAllUserModuleSettings('mailing');
                    break;
            }
        }

    }
