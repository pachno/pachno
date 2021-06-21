<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Dashboard class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Dashboard class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\LivelinkImports")
     */
    class LivelinkImport extends IdentifiableScoped
    {

        public const STATUS_CREATED = 0;
        public const STATUS_IMPORTING = 1;
        public const STATUS_IMPORTED = 2;
        public const STATUS_IMPORTED_ERROR = 3;

        /**
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_completed_at;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_status = self::STATUS_CREATED;

        /**
         * @Column(type="serializable", length=1000)
         */
        protected $_data;

        /**
         * Returns the associated user
         *
         * @return User
         */
        public function getUser()
        {
            return $this->_b2dbLazyLoad('_user_id');
        }

        public function setUser($user)
        {
            $this->_user_id = $user;
        }

        /**
         * Returns the associated project
         *
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project_id');
        }

        public function setProject($project)
        {
            $this->_project_id = $project;
        }

        /**
         * @return array
         */
        public function getData()
        {
            return $this->_data;
        }

        /**
         * @param array $data
         */
        public function setData($data)
        {
            $this->_data = $data;
        }

        /**
         * @return mixed
         */
        public function getCompletedAt()
        {
            return $this->_completed_at;
        }

        /**
         * @param mixed $completed_at
         */
        public function setCompletedAt($completed_at)
        {
            $this->_completed_at = $completed_at;
        }

        protected function _preSave(bool $is_new): void
        {
            parent::_preSave($is_new);
            if ($is_new) {
                $this->_created_at = NOW;
            }
        }

        public function setStatus(int $status)
        {
            $this->_status = $status;
        }

        public function getStatus(): int
        {
            return $this->_status;
        }

    }
