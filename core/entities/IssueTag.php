<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\common\Timeable;

    /**
     * Log item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Log item class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\IssueSpentTimes")
     */
    class IssueTag extends IdentifiableScoped
    {

        /**
         * The issue the tag is related to
         *
         * @var Issue
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Issue")
         */
        protected $_issue_id;

        /**
         * Who tagged it
         *
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
         * @var Tag
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Tag")
         */
        protected $_tag_id;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        public function setUser($user_id)
        {
            $this->_user_id = $user_id;
        }

        public function getUser()
        {
            return $this->_b2dbLazyLoad('_user_id');
        }

        public function setTag($tag_id)
        {
            $this->_tag_id = $tag_id;
        }

        public function getTag()
        {
            return $this->_b2dbLazyLoad('_tag_id');
        }

        public function setCreatedAt($time)
        {
            $this->_created_at = $time;
        }

        public function getCreatedAt()
        {
            return $this->_created_at;
        }

        public function setIssue($issue_id)
        {
            $this->_issue_id = $issue_id;
        }

        /**
         * @return Issue the related issue
         */
        public function getIssue()
        {
            return $this->_b2dbLazyLoad('_issue_id');
        }

        public function setProject($project)
        {
            $this->_project_id = $project;
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

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new && $this->_created_at == 0) {
                $this->_created_at = time();
            }
        }

    }
