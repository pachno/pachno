<?php

    namespace pachno\core\modules\mailing\controllers;

    use Exception;
    use pachno\core\entities\IncomingEmailAccount;
    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework;
    use pachno\core\framework\Context;
    use pachno\core\framework\Request;
    use pachno\core\modules\mailing\Mailing;

    /**
     * @Routes(name_prefix="mailing_")
     */
    class Main extends framework\Action
    {

        /**
         * @return Mailing
         * @throws Exception
         */
        public function getModule(): Mailing
        {
            return framework\Context::getModule('mailing');
        }

        /**
         * Forgotten password logic (AJAX call)
         *
         * @Route(url="/mailing/forgot")
         * @AnonymousRoute
         *
         * @param Request $request
         */
        public function runForgot(Request $request)
        {
            $i18n = $this->getI18n();

            try {
                $username_or_email = str_replace('%2E', '.', $request['forgot_password_username']);

                // Whether no username or email address was given.
                if (empty($username_or_email)) {
                    throw new Exception($i18n->__('Please enter an username or email address.'));
                }

                // Try retrieving the user.
                $user = User::getByUsername($username_or_email);
                if (!$user instanceof User) {
                    $user = User::getByEmail($username_or_email, false);
                }

                if ($user instanceof User) {
                    // Whether the user was deleted or is otherwise disabled.
                    if (!$user->isActivated() || !$user->isEnabled() || $user->isDeleted()) {
                        throw new Exception($i18n->__('Your user account has been disabled. Please contact your administrator.'));
                    }

                    // Whether the user has an email address.
                    if ($user->getEmail()) {
                        // Send password reset email.
                        $this->getModule()->sendForgottenPasswordEmail($user);
                    }
                }

                // Protect from user name/email guessing in not telling whether the username/email address exists.
                return $this->renderJSON(['message' => $i18n->__("If you are a registered user, we sent you an email. Please use the link in the email you received to reset your password.")]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Send a test email
         *
         * @Route(url="/mailing/test", name="testemail")
         * @param Request $request
         */
        public function runTestEmail(Request $request)
        {
            try {
                $result = $this->getModule()->sendTestEmail($this->getUser()->getEmail());
                if ($result) {
                    return $this->renderJSON(['message' => $this->getI18n()->__('The email was successfully accepted for delivery')]);
                }
            } catch (Exception $e) {
            }

            $this->getResponse()->setHttpStatus(400);
            return $this->renderJSON(['message' => $this->getI18n()->__('The email was not sent')]);
        }

        /**
         * Save incoming email account
         *
         * @Route(url="/mailing/:project_key/incoming_account/*", name="save_incoming_account")
         * @param Request $request
         *
         * @return type
         */
        public function runSaveIncomingAccount(Request $request)
        {
            $project = null;
            if ($project_key = $request['project_key']) {
                try {
                    $project = Project::getByKey($project_key);
                } catch (Exception $e) {

                }
            }
            if ($project instanceof Project) {
                try {
                    $account_id = $request['account_id'];
                    $account = ($account_id) ? new IncomingEmailAccount($account_id) : new IncomingEmailAccount();
                    $account->setIssuetype((integer)$request['issuetype']);
                    $account->setProject($project);
                    $account->setPort((integer)$request['port']);
                    $account->setName($request['name']);
                    $account->setFoldername($request['folder']);
                    $account->setKeepEmails($request['keepemail']);
                    $account->setServer($request['servername']);
                    $account->setUsername($request['username']);
                    $account->setPassword($request->getRawParameter('password'));
                    $account->setSSL((bool)$request['ssl']);
                    $account->setPreferHtml((bool)$request['prefer_html']);
                    $account->setIgnoreCertificateValidation((bool)$request['ignore_certificate_validation']);
                    $account->setUsePlaintextAuthentication((bool)$request['plaintext_authentication']);
                    $account->setServerType((integer)$request['account_type']);
                    $account->save();

                    if (!$account_id) {
                        return $this->renderComponent('mailing/incomingemailaccount', ['project' => $project, 'account' => $account]);
                    } else {
                        return $this->renderJSON(['name' => $account->getName()]);
                    }
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid mailing account')]);
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid project')]);
            }
        }

        /**
         * Check incoming email accounts for incoming emails
         *
         * @Route(url="/mailing/incoming_account/:account_id/check", name="check_account")
         * @param Request $request
         *
         * @return type
         * @throws Exception
         */
        public function runCheckIncomingAccount(Request $request)
        {
            Context::loadLibrary('common');
            if ($account_id = $request['account_id']) {
                try {
                    $account = new IncomingEmailAccount($account_id);
                    try {
                        if (!function_exists('imap_open')) {
                            throw new Exception($this->getI18n()->__('The php imap extension is not installed'));
                        }
                        $this->getModule()->processIncomingEmailAccount($account);
                    } catch (Exception $e) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => $e->getMessage()]);
                    }

                    return $this->renderJSON(['account_id' => $account->getID(), 'time' => $this->getI18n()->formatTime($account->getTimeLastFetched(), 6), 'count' => $account->getNumberOfEmailsLastFetched()]);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid mailing account')]);
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid mailing account')]);
            }
        }

        /**
         * Delete an incoming email account
         *
         * @Route(url="/mailing/incoming_account/:account_id/delete", name="delete_account")
         * @param Request $request
         *
         * @return type
         */
        public function runDeleteIncomingAccount(Request $request)
        {
            if ($account_id = $request['account_id']) {
                try {
                    $account = new IncomingEmailAccount($account_id);
                    $account->delete();

                    return $this->renderJSON(['message' => $this->getI18n()->__('Incoming email account deleted')]);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid mailing account')]);
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid mailing account')]);
            }
        }

        /**
         * Save project settings
         *
         * @Route(url="/configure/project/:project_id/mailing", name="configure_settings")
         * @Parameters(config_module="core", section=15)
         * @param Request $request
         *
         * @return type
         */
        public function runConfigureProjectSettings(Request $request)
        {
            $this->forward403unless($request->isPost());

            if ($this->access_level != framework\Settings::ACCESS_FULL) {
                $project_id = $request['project_id'];

                if (trim($request['mailing_from_address']) != '') {
                    if (filter_var(trim($request['mailing_from_address']), FILTER_VALIDATE_EMAIL) !== false) {
                        $this->getModule()->saveSetting(Mailing::SETTING_PROJECT_FROM_ADDRESS . $project_id, trim(mb_strtolower($request->getParameter('mailing_from_address'))));
                        if (trim($request['mailing_from_name']) !== '') {
                            $this->getModule()->saveSetting(Mailing::SETTING_PROJECT_FROM_NAME . $project_id, trim($request->getParameter('mailing_from_name')));
                        } else {
                            $this->getModule()->deleteSetting(Mailing::SETTING_PROJECT_FROM_NAME . $project_id);
                        }
                    } else {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['message' => $this->getI18n()->__('Please enter a valid email address')]);
                    }
                } elseif (trim($request['mailing_from_address']) == '') {
                    $this->getModule()->deleteSetting(Mailing::SETTING_PROJECT_FROM_ADDRESS . $project_id);
                    $this->getModule()->deleteSetting(Mailing::SETTING_PROJECT_FROM_NAME . $project_id);
                }

                return $this->renderJSON(['failed' => false, 'message' => $this->getI18n()->__('Settings saved')]);
            } else {
                $this->forward403();
            }
        }

    }
