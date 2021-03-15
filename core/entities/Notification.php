<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\Articles;
    use pachno\core\framework\Event;

    /**
     * Notification item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Notification item class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\Notifications")
     */
    class Notification extends IdentifiableScoped
    {

        const TYPE_ISSUE_CREATED = 'issue_created';

        const TYPE_ISSUE_UPDATED = 'issue_updated';

        const TYPE_ISSUE_COMMENTED = 'issue_commented';

        const TYPE_ISSUE_MENTIONED = 'issue_mentioned';

        const TYPE_ARTICLE_CREATED = 'article_created';

        const TYPE_ARTICLE_UPDATED = 'article_updated';

        const TYPE_ARTICLE_COMMENTED = 'article_commented';

        const TYPE_ARTICLE_MENTIONED = 'article_mentioned';

        const TYPE_COMMENT_MENTIONED = 'comment_mentioned';

        const TYPE_COMMIT_COMMENTED = 'commit_commented';

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_id;

        /**
         * The notification target
         *
         * @var IdentifiableScoped
         */
        protected $_target;

        /**
         * @Column(type="string", length=100)
         */
        protected $_notification_type;

        /**
         * @Column(type="string", length=50, default="core")
         */
        protected $_module_name = 'core';

        /**
         * @Column(type="boolean", default="0")
         */
        protected $_is_read = false;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * Who triggered the notification
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_triggered_by_user_id;

        /**
         * Who the notification is for
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_shown_at;

        public function getName()
        {
            return '';
        }

        public function getTargetID()
        {
            return $this->_target_id;
        }

        public function getTriggeredByUser()
        {
            return $this->_b2dbLazyLoad('_triggered_by_user_id');
        }

        public function setTriggeredByUser($uid)
        {
            $this->_triggered_by_user_id = $uid;
        }

        public function getUser()
        {
            return $this->_b2dbLazyLoad('_user_id');
        }

        public function setUser($uid)
        {
            $this->_user_id = $uid;
        }

        public function getModuleName()
        {
            return $this->_module_name;
        }

        public function setModuleName($module_name)
        {
            $this->_module_name = $module_name;
        }

        public function getShownAt()
        {
            return $this->_shown_at;
        }

        public function setShownAt($shown_at)
        {
            $this->_shown_at = $shown_at;
        }

        public function isShown()
        {
            return (bool)$this->_shown_at;
        }

        public function showOnce()
        {
            $this->_shown_at = time();
        }

        public function getTargetUrl()
        {
            switch ($this->getNotificationType()) {
                case self::TYPE_ISSUE_CREATED:
                case self::TYPE_ISSUE_UPDATED:
                case self::TYPE_ISSUE_MENTIONED:
                    $url = $issue->getUrl(false);
                    break;
                case self::TYPE_ISSUE_COMMENTED:
                    $url = $this->getTarget()->getTarget()->getUrl(false) . '#comment_' . $this->getTarget()->getID();
                    break;
                case self::TYPE_COMMENT_MENTIONED:
                    if ($this->getTarget()->getTargetType() == Comment::TYPE_ISSUE) {
                        $url = $this->getTarget()->getTarget()->getUrl(false) . '#comment_' . $this->getTarget()->getID();
                    } else {
                        $url = make_url('publish_article', ['article_name' => $this->getTarget()->getTarget()->getName()], false) . '#comment_' . $this->getTarget()->getID();
                    }
                    break;
                case self::TYPE_ARTICLE_COMMENTED:
                    $url = make_url('publish_article', ['article_name' => $this->getTarget()->getTarget()->getName()], false) . '#comment_' . $this->getTarget()->getID();
                    break;
                case self::TYPE_ARTICLE_CREATED:
                case self::TYPE_ARTICLE_UPDATED:
                case self::TYPE_ARTICLE_MENTIONED:
                    $url = make_url('publish_article', ['article_name' => $this->getTarget()->getName()], false);
                    break;
                default:
                    $event = Event::createNew('core', 'pachno\core\entities\Notification::getTargetUrl', $this);
                    $event->triggerUntilProcessed();
                    $url = $event->getReturnValue();
            }

            return $url;
        }

        public function getNotificationType()
        {
            return $this->_notification_type;
        }

        public function setNotificationType($notification_type)
        {
            $this->_notification_type = $notification_type;
        }

        /**
         * Returns the object which the notification is for
         *
         * @return IdentifiableScoped|Project|Issue|Article
         */
        public function getTarget()
        {
            if ($this->_target === null) {
                if ($this->_module_name == 'core') {
                    switch ($this->_notification_type) {
                        case self::TYPE_ARTICLE_COMMENTED:
                        case self::TYPE_ISSUE_COMMENTED:
                        case self::TYPE_COMMENT_MENTIONED:
                            $this->_target = tables\Comments::getTable()->selectById((int)$this->_target_id);
                            break;
                        case self::TYPE_ISSUE_UPDATED:
                        case self::TYPE_ISSUE_CREATED:
                        case self::TYPE_ISSUE_MENTIONED:
                            $this->_target = Issue::getB2DBTable()->selectById((int)$this->_target_id);
                            break;
                        case self::TYPE_ARTICLE_CREATED:
                        case self::TYPE_ARTICLE_UPDATED:
                        case self::TYPE_ARTICLE_MENTIONED:
                            $this->_target = Articles::getTable()->selectById((int)$this->_target_id);
                            break;
                    }
                } else {
                    $event = new Event('core', 'pachno\core\entities\Notification::getTarget', $this);
                    $event->triggerUntilProcessed();
                    $this->_target = $event->getReturnValue();
                }
            }

            return $this->_target;
        }

        public function setTarget($target)
        {
            $this->_target_id = $target->getID();
            $this->_target = $target;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new) {
                $this->_created_at = NOW;
            }
        }

        protected function _postSave($is_new)
        {
            if (!$is_new) {
                if ($this->isRead() && $this->getCreatedAt() < NOW - (86400 * 30)) {
                    $this->delete();
                }
            }
        }

        public function isRead()
        {
            return $this->getIsRead();
        }

        public function getIsRead()
        {
            return $this->_is_read;
        }

        public function setIsRead($is_read)
        {
            $this->_is_read = $is_read;
        }

        public function getCreatedAt()
        {
            return $this->_created_at;
        }

        public function setCreatedAt($created_at)
        {
            $this->_created_at = $created_at;
        }

    }
