<?php

    namespace pachno\core\entities;

    use b2db\QueryColumnSort;
    use Exception;
    use pachno\core\entities\common\Identifiable;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\traits\TextParserTodo;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use pachno\core\helpers\ContentParser;
    use pachno\core\helpers\MentionableProvider;
    use pachno\core\helpers\TextParser;
    use pachno\core\helpers\TextParserMarkdown;

    /**
     * Class used for comments
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Class used for comments
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\Comments")
     */
    class Comment extends IdentifiableScoped implements MentionableProvider
    {

        /**
         * Issue comment
         */
        const TYPE_ISSUE = 1;

        /**
         * Article comment
         */
        const TYPE_ARTICLE = 2;

        /**
         * Commit comment
         */
        const TYPE_COMMIT = 3;

        protected static $_comment_count = [];

        /**
         * @Column(type="text")
         */
        protected $_content;

        /**
         * Who posted the comment
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_posted_by;

        /**
         * Who last updated the comment
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_updated_by;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_posted;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_updated;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_id;

        /**
         * @var IdentifiableScoped
         */
        protected $_target;

        /**
         * @Column(type="integer", length=5)
         */
        protected $_target_type = self::TYPE_ISSUE;

        /**
         * @Column(type="boolean")
         */
        protected $_is_public = true;

        /**
         * @Column(type="string", length=100)
         */
        protected $_module = 'core';

        /**
         * @Column(type="boolean")
         */
        protected $_deleted = false;

        /**
         * @Column(type="boolean")
         */
        protected $_system_comment = false;

        /**
         * @Column(type="boolean")
         */
        protected $_has_associated_changes = false;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_comment_number = 0;

        /**
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Comment")
         */
        protected $_reply_to_comment = 0;

        /**
         * @Column(type="integer", length=10, default=1)
         */
        protected $_syntax = framework\Settings::SYNTAX_MW;

        /**
         * List of replies linked to this comment
         *
         * @var array
         * @Relates(class="\pachno\core\entities\Comment", collection=true, foreign_column="reply_to_comment")
         */
        protected $_replies;

        protected $_replies_count;

        /**
         * List of log items linked to this comment
         *
         * @var array
         * @Relates(class="\pachno\core\entities\LogItem", collection=true, foreign_column="comment_id")
         */
        protected $_log_items;

        protected $_log_item_count = null;

        protected $_parser = null;

        public static function getPlaceholderTextForType($target_type)
        {
            $i18n = framework\Context::getI18n();

            switch ($target_type) {
                case self::TYPE_ISSUE:
                    return $i18n->__('Issue created');
                case self::TYPE_COMMIT:
                    return $i18n->__('Commit pushed');
                case self::TYPE_ARTICLE:
                    return $i18n->__('Article created');
            }
        }

        /**
         * Returns all comments for a given item
         *
         * @param $target_id
         * @param $target_type
         * @param string $sort_order
         *
         * @return Comment[]
         */
        public static function getComments($target_id, $target_type, $sort_order = QueryColumnSort::SORT_ASC)
        {
            $comments = tables\Comments::getTable()->getComments($target_id, $target_type, $sort_order);

            return $comments;
        }

        /**
         * Returns all recent comments for a given item
         *
         * @param $user_id
         * @param int $target_type
         * @param int $limit
         *
         * @return Comment[]
         */
        public static function getRecentCommentsByAuthor($user_id, $target_type = self::TYPE_ISSUE, $limit = 10)
        {
            $comments = tables\Comments::getTable()->getRecentCommentsByUserIDandTargetType($user_id, $target_type, $limit);

            return $comments;
        }

        public static function countComments($target_id, $target_type)
        {
            if (!array_key_exists($target_type, self::$_comment_count))
                self::$_comment_count[$target_type] = [];

            if (!array_key_exists($target_id, self::$_comment_count[$target_type]))
                self::$_comment_count[$target_type][$target_id] = (int)tables\Comments::getTable()->countComments($target_id, $target_type);

            return (int)self::$_comment_count[$target_type][$target_id];
        }

        public function setPublic($var)
        {
            $this->_is_public = (bool)$var;
        }

        public function setIsPublic($var)
        {
            $this->_is_public = (bool)$var;
        }

        public function hasMentions()
        {
            return $this->_getParser()->hasMentions();
        }

        /**
         * Returns the associated parser object
         *
         * @return ContentParser|TextParserTodo
         */
        protected function _getParser()
        {
            if (is_null($this->_parser)) {
                $this->_parseContent();
            }

            return $this->_parser;
        }

        protected function _parseContent($options = [])
        {
            switch ($this->_syntax) {
                case framework\Settings::SYNTAX_MD:
                    $parser = new TextParserMarkdown();
                    $text = $parser->transform($this->_content);
                    break;
                case framework\Settings::SYNTAX_PT:
                    $options = ['plain' => true];
                case framework\Settings::SYNTAX_MW:
                default:
                    $parser = new TextParser($this->_content);
                    foreach ($options as $option => $value) {
                        $parser->setOption($option, $value);
                    }
                    if (!array_key_exists('issue', $options) && $this->getTarget() instanceof Issue) $parser->setOption('issue', $this->getTarget());
                    $text = $parser->getParsedText();
                    break;
            }

            if (isset($parser)) {
                $this->_parser = $parser;
            }

            return $text;
        }

        public function getTarget()
        {
            if ($this->_target === null) {
                switch ($this->getTargetType()) {
                    case self::TYPE_ISSUE:
                        $this->_target = Issue::getB2DBTable()->selectById($this->_target_id);
                        break;
                    case self::TYPE_ARTICLE:
                        $this->_target = tables\Articles::getTable()->selectById($this->_target_id);
                        break;
                    default:
                        $event = Event::createNew('core', 'Comment::getTarget', $this);
                        $event->trigger();
                        $this->_target = $event->getReturnValue();
                }
            }

            return $this->_target;
        }

        public function getTargetType()
        {
            return $this->_target_type;
        }

        public function setTargetType($var)
        {
            $this->_target_type = $var;
        }

        /**
         * Return if the user can delete comment
         *
         * @param User $user A User
         *
         * @return boolean
         */
        public function canUserDelete(User $user)
        {
            $can_delete = false;

            try {
                // Delete comment if valid user and...
                if ($user instanceof User) {
                    if (($this->postedByUser($user->getID()) && $this->canUserDeleteOwnComment()) // the user posted the comment AND the user can delete own comments
                        || $this->canUserDeleteComment()) // OR the user can delete all comments
                    {
                        $can_delete = true;
                    }//endif
                }//endif
            }//endtry
            catch (Exception $e) {
            }

            return $can_delete;
        }

/**
         * Return the whether or not the user owns this comment
         *
         * @param int $user_id A user's ID
         *
         * @return bool
         */
        public function postedByUser($user_id)
        {
            $posted_by_id = null;

            try {
                $posted_by_id = $this->getPostedByID();

                if (!empty($posted_by_id) && !empty($user_id)) {
                    if ($posted_by_id == $user_id) {
                        return true;
                    }//endif
                }//endif
                else {
                    return false;
                }//endelse
            }//endtry
            catch (Exception $e) {
            }

            return false;
        }

        /**
         * Return if the user can delete own comment
         *
         * @return boolean
         */
        public function canUserDeleteOwnComment()
        {
            $retval = $this->_canPermissionOrSeeAndEditComments('candeletecommentsown');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        protected function _canPermissionOrSeeAndEditComments($permission)
        {
            $retval = $this->_permissionCheck($permission);
            $retval = ($retval === null) ? $this->_permissionCheck('canpostandeditcomments', true) : $retval;

            return $retval;
        }

        /**
         * Perform a permission check based on a key, and whether or not to
         * check for the equivalent "*own" permission if the comment is posted
         * by the same user
         *
         * @param string $key The permission key to check for
         * @param boolean $exclusive Whether to perform a similar check for "own"
         *
         * @return boolean
         */
        protected function _permissionCheck($key, $exclusive = false)
        {
            $retval = ($this->getPostedByID() == framework\Context::getUser()->getID() && !$exclusive) ? $this->_permissionCheckWithID($key . 'own') : null;
            $retval = ($retval !== null) ? $retval : $this->_permissionCheckWithID($key);

            return ($retval !== null) ? $retval : null;
        }

        /**
         * Perform a permission check based on a key, and whether or not to
         * check if the permission is explicitly set
         *
         * @param string $key The permission key to check for
         *
         * @return boolean
         */
        protected function _permissionCheckWithID($key)
        {
            $retval = framework\Context::getUser()->hasPermission($key, $this->getID(), 'core');
            $retval = ($retval !== null) ? $retval : framework\Context::getUser()->hasPermission($key, 0, 'core');

            return $retval;
        }

        /**
         * Return if the user can delete this comment
         *
         * @return boolean
         */
        public function canUserDeleteComment()
        {
            if ($this->isSystemComment()) return false;
            $retval = $this->_canPermissionOrSeeAndEditAllComments('candeletecomments');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        protected function _canPermissionOrSeeAndEditAllComments($permission)
        {
            $retval = $this->_permissionCheck($permission);
            $retval = ($retval === null) ? $this->_permissionCheck('canpostseeandeditallcomments', true) : $retval;

            return $retval;
        }

        /**
         * Return if the user can edit comment
         *
         * @param User $user A User
         *
         * @return boolean
         */
        public function canUserEdit(User $user)
        {
            $can_edit = false;

            try {
                // Edit comment if valid user and...
                if ($user instanceof User) {
                    if (($this->postedByUser($user->getID()) && $this->canUserEditOwnComment()) // the user posted the comment AND the user can edit own comments
                        || $this->canUserEditComment()) // OR the user can edit all comments
                    {
                        $can_edit = true;
                    }//endif
                }//endif
            }//endtry
            catch (Exception $e) {
            }

            return $can_edit;
        }

        /**
         * Return if the user can edit own comment
         *
         * @return boolean
         */
        public function canUserEditOwnComment()
        {
            $retval = $this->_canPermissionOrSeeAndEditComments('caneditcommentsown');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        /**
         * Return if the user can edit this comment
         *
         * @return boolean
         */
        public function canUserEditComment()
        {
            if ($this->isSystemComment()) return false;
            $retval = $this->_canPermissionOrSeeAndEditAllComments('caneditcomments');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        /**
         * Return if the specified user can view this comment
         *
         * @param User $user A User
         *
         * @return boolean
         */
        public function isViewableByUser(User $user)
        {
            $can_view = false;

            try {
                // Show comment if valid user and...
                if ($user instanceof User) {

                    if ((!$this->isPublic() && $user->canSeeNonPublicComments()) // the comment is hidden and the user can see hidden comments
                        || ($this->isPublic() && $user->canViewComments()) // OR the comment is public and  user can see public comments
                        || ($this->postedByUser($user->getID()))) // OR the user posted the comment
                    {
                        $can_view = true;
                    }//endif

                }//endif
            }//endtry
            catch (Exception $e) {
            }

            return $can_view;
        }

        public function isPublic()
        {
            return $this->_is_public;
        }

        /**
         * Returns the user who last updated the comment
         *
         * @return User
         */
        public function getUpdatedBy()
        {
            return ($this->_updated_by instanceof User) ? $this->_updated_by : User::getB2DBTable()->selectById($this->_updated_by);
        }

        public function setUpdatedBy($var)
        {
            $this->_updated = NOW;
            $this->_updated_by = $var;
        }

        public function getName()
        {
            return '';
        }

        public function getParsedContent($options = [])
        {
            return $this->_parseContent($options);
        }

        public function getUpdated()
        {
            return $this->_updated;
        }

        public function getModuleName()
        {
            return $this->_module;
        }

        public function setModuleName($var)
        {
            $this->_module = $var;
        }

                public function toJSON($detailed = true)
        {
            $return_values = [
                'id' => $this->getID(),
                'created_at' => $this->getPosted(),
                'comment_number' => $this->getCommentNumber(),
                'posted_by' => ($this->getPostedBy() instanceof Identifiable) ? $this->getPostedBy()->toJSON() : null,
                'content' => $this->getContent(),
                'system_comment' => $this->isSystemComment(),
            ];

            return $return_values;
        }//end postedByUser

        public function getPosted()
        {
            return $this->_posted;
        }

        public function setPosted($timestamp)
        {
            $this->_posted = $timestamp;
        }

        public function getCommentNumber()
        {
            return (int)$this->_comment_number;
        }

        public function getContent()
        {
            return $this->_content;
        }

        public function setContent($var)
        {
            $this->_content = $var;
        }

        public function isReply()
        {
            return (bool)($this->getReplyToComment() instanceof Comment);
        }

        public function getReplyToComment()
        {
            if (!is_object($this->_reply_to_comment) && $this->_reply_to_comment) {
                $this->_b2dbLazyLoad('_reply_to_comment');
            }

            return $this->_reply_to_comment;
        }

        public function setReplyToComment($reply_to_comment_id)
        {
            $this->_reply_to_comment = $reply_to_comment_id;
        }

        public function hasAssociatedChanges()
        {
            return $this->_has_associated_changes;
        }

        public function setHasAssociatedChanges($value = true)
        {
            $this->_has_associated_changes = $value;
        }

        public function getAssociatedChanges()
        {
            return $this->getLogItems();
        }

        public function getLogItems()
        {
            return $this->_b2dbLazyLoad('_log_items');
        }

        public function getReplies()
        {
            return $this->_b2dbLazyLoad('_replies');
        }

        public function getSyntax()
        {
            return $this->_syntax;
        }

        public function setSyntax($syntax)
        {
            if (!is_numeric($syntax)) $syntax = framework\Settings::getSyntaxValue($syntax);

            $this->_syntax = (int)$syntax;
        }

        public function getMentionableUsers()
        {
            $users = [$this->getPostedByID() => $this->getPostedBy()];
            foreach ($this->getMentions() as $user) {
                $users[$user->getID()] = $user;
            }

            return $users;
        }

        public function getMentions()
        {
            return $this->_getParser()->getMentions();
        }

        /**
         * Get todos from comment content.
         *
         * @return array
         */
        public function getTodos()
        {
            return $this->_getParser()->getTodos();
        }

        /**
         * Reset "cached" todos.
         *
         * @return void
         */
        public function resetTodos()
        {
            $this->_parser = null;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new) {
                if (!$this->_posted) {
                    $this->_posted = NOW;
                }
                if (!$this->_comment_number) {
                    $this->_comment_number = tables\Comments::getTable()->getNextCommentNumber($this->_target_id, $this->_target_type);
                }
            }
        }

        protected function _postSave($is_new)
        {
            if ($is_new) {
                $tty = $this->getTargetType();
                $tid = $this->getTargetID();
                if (array_key_exists($tty, self::$_comment_count) && array_key_exists($tid, self::$_comment_count[$tty]) && array_key_exists((int)$this->isSystemComment(), self::$_comment_count[$tty][$tid]))
                    self::$_comment_count[$tty][$tid][(int)$this->isSystemComment()]++;

                if (!$this->isSystemComment()) {
                    if ($this->_getParser()->hasMentions()) {
                        foreach ($this->_getParser()->getMentions() as $user) {
                            if ($user->getID() == framework\Context::getUser()->getID()) continue;

                            if (($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_MENTIONED, false)->isOn())) $this->_addNotificationIfNotNotified(Notification::TYPE_COMMENT_MENTIONED, $user, $this->getPostedBy());
                        }
                    }
                    if ($this->getTargetType() == self::TYPE_ISSUE) {
                        if (framework\Settings::getUserSetting(framework\Settings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ISSUES, $this->getPostedByID()))
                            $this->getTarget()->addSubscriber($this->getPostedByID());
                    }
                    if ($this->getTargetType() == self::TYPE_ARTICLE) {
                        if (framework\Settings::getUserSetting(framework\Settings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ARTICLES, $this->getPostedByID()))
                            $this->getTarget()->addSubscriber($this->getPostedByID());
                    }
                    $this->_addTargetNotifications();
                }

                switch ($this->getTargetType()) {
                    case self::TYPE_ISSUE:
                        Event::createNew('core', 'pachno\core\entities\Comment::_postSave', $this, ['issue' => $this->getTarget()])->trigger();
                        break;
                    case self::TYPE_ARTICLE:
                        Event::createNew('core', 'pachno\core\entities\Comment::_postSave', $this, ['article' => $this->getTarget()])->trigger();
                        break;
                    default:
                        return;
                }
            }
            $this->touchTargetIfItsIssue();
        }

        public function getTargetID()
        {
            return $this->_target_id;
        }

        public function setTargetID($var)
        {
            $this->_target_id = $var;
        }

        public function isSystemComment()
        {
            return $this->_system_comment;
        }

        public function setSystemComment($val = true)
        {
            $this->_system_comment = $val;
        }

        protected function _addNotificationIfNotNotified($type, $user, $updated_by)
        {
            if (!$this->shouldUserBeNotified($user, $updated_by)) return;

            $this->_addNotification($type, $user);
        }

        public function shouldUserBeNotified($user, $updated_by)
        {
            if (!$this->getTarget()->hasAccess($user)) return false;

            if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_UPDATED_SELF, false)->isOff() && $user->getID() === $updated_by->getID()) return false;

            if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE, false)->isOff()) return true;

            switch ($this->getTargetType()) {
                case self::TYPE_ISSUE:
                    $target_type_string = '_issue_';
                    break;
                case self::TYPE_ARTICLE:
                    $target_type_string = '_article_';
                    break;
            }

            if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . $target_type_string . $this->getTargetID(), false)->isOff()) {
                $user->setNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . $target_type_string . $this->getTargetID(), true);

                return true;
            }

            return false;
        }

        protected function _addNotification($type, $user)
        {
            $notification = new Notification();
            $notification->setTarget($this);
            $notification->setTriggeredByUser($this->getPostedByID());
            $notification->setUser($user);
            $notification->setNotificationType($type);
            $notification->save();
        }

        /**
         * Return the poster id
         *
         * @return integer
         */
        public function getPostedByID()
        {
            $poster = null;
            try {
                $poster = $this->getPostedBy();
            } catch (Exception $e) {
            }

            return ($poster instanceof Identifiable) ? $poster->getID() : null;
        }

        /**
         * Returns the user who posted the comment
         *
         * @return User
         */
        public function getPostedBy()
        {
            try {
                return ($this->_posted_by instanceof User) ? $this->_posted_by : User::getB2DBTable()->selectById($this->_posted_by);
            } catch (Exception $e) {
                return null;
            }
        }

        protected function _addTargetNotifications()
        {
            foreach ($this->getTarget()->getSubscribers() as $user) {
                switch ($this->getTargetType()) {
                    case self::TYPE_ISSUE:
                        if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_SUBSCRIBED_ISSUES, false)->isOn()) {
                            $this->_addNotificationIfNotNotified(Notification::TYPE_ISSUE_COMMENTED, $user, $this->getPostedBy());
                        }
                        break;
                    case self::TYPE_ARTICLE:
                        if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_SUBSCRIBED_ARTICLES, false)->isOn()) {
                            $this->_addNotificationIfNotNotified(Notification::TYPE_ARTICLE_COMMENTED, $user, $this->getPostedBy());
                        }
                        break;
                }
            }
        }

        protected function touchTargetIfItsIssue()
        {
            if ($this->getTargetType() === self::TYPE_ISSUE) $this->getTarget()->touch();
        }

        public function setPostedBy($var)
        {
            if (is_object($var)) {
                $var = $var->getID();
            }
            $this->_posted_by = $var;
        }

        protected function _postDelete()
        {
            $this->touchTargetIfItsIssue();
        }

    }
