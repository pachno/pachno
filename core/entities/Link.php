<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\Links;
    use pachno\core\framework;
    use pachno\core\helpers\TextParser;

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
     * @Table(name="\pachno\core\entities\tables\Links")
     */
    class Link extends IdentifiableScoped
    {

        const TYPE_MENU = 'main_menu';

        const TYPE_ISSUE = 'issue';

        const TYPE_WIKI = 'wiki';

        /**
         * Who created the link
         *
         * @var User
         * @Column(type="integer", length=10, name="uid")
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_id;

        /**
         * @var IdentifiableScoped
         */
        protected $_target;

        /**
         * @Column(type="varchar", length=30)
         */
        protected $_target_type;

        /**
         * @Column(type="varchar", length=100)
         */
        protected $_description;

        /**
         * @Column(type="text")
         */
        protected $_url;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_link_order = 0;

        /**
         * @return User
         */
        public function getUser()
        {
            return $this->_b2dbLazyLoad('_user_id');
        }

        /**
         * @param User|int $user_id
         */
        public function setUserId($user_id)
        {
            $this->_user_id = $user_id;
        }

        /**
         * @return mixed
         */
        public function getTargetId()
        {
            return $this->_target_id;
        }

        /**
         * @param mixed $target_id
         */
        public function setTargetId($target_id)
        {
            $this->_target_id = $target_id;
        }

        /**
         * @return IdentifiableScoped
         */
        public function getTarget()
        {
            return $this->_target;
        }

        /**
         * @param IdentifiableScoped $target
         */
        public function setTarget($target)
        {
            $this->_target = $target;
        }

        /**
         * @return mixed
         */
        public function getTargetType()
        {
            return $this->_target_type;
        }

        /**
         * @param mixed $target_type
         */
        public function setTargetType($target_type)
        {
            $this->_target_type = $target_type;
        }

        /**
         * @return mixed
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * @param mixed $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        public function getParsedDescription()
        {
            return TextParser::parseText($this->_description, false, null, ['embedded' => true]);
        }

        public function isSeparator()
        {
            return (!$this->hasUrl() && !$this->hasDescription());
        }

        public function hasUrl()
        {
            return (bool)$this->getUrl();
        }

        /**
         * @return mixed
         */
        public function getUrl()
        {
            return $this->_url;
        }

        /**
         * @param mixed $url
         */
        public function setUrl($url)
        {
            $this->_url = $url;
        }

        public function hasDescription()
        {
            return (bool)$this->_description != '';
        }

        public function getFinalUrl()
        {
            if ($this->isInternalLink()) {
                return framework\Context::getRouting()->generate($this->_url);
            } else {
                return $this->getUrl();
            }
        }

        public function isInternalLink()
        {
            return ($this->hasUrl() && mb_substr($this->getUrl(), 0, 1) == '@');
        }

        /**
         * @return mixed
         */
        public function getLinkOrder()
        {
            return $this->_link_order;
        }

        /**
         * @param mixed $link_order
         */
        public function setLinkOrder($link_order)
        {
            $this->_link_order = $link_order;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);

            if ($is_new) {
                if (!$this->_link_order) {
                    $this->_link_order = Links::getTable()->getNextOrder($this->_target_type, $this->_target_id, $this->_scope);
                }
            }
        }

    }
