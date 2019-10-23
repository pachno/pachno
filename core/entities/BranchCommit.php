<?php

    namespace pachno\core\entities;

    /**
     * Branch commit
     *
     * @package pachno
     * @subpackage livelink
     *
     * @Table(name="\pachno\core\entities\tables\BranchCommits")
     */
    class BranchCommit extends common\IdentifiableScoped
    {

        /**
         * Associated branch
         * @var Branch
         *
         * @Column(type="integer")
         * @Relates(class="\pachno\core\entities\Branch")
         */
        protected $_branch_id = null;

        /**
         * Associated commit
         * @var Commit
         *
         * @Column(type="integer")
         * @Relates(class="\pachno\core\entities\Commit")
         */
        protected $_commit_id = null;

        /**
         * Associated commit sha
         *
         * @var string
         * @Column(type="text", length=100)
         */
        protected $_commit_sha = '';

        /**
         * Set the commit this change applies to
         *
         * @param Commit $commit
         */
        public function setCommit(Commit $commit)
        {
            $this->_commit_id = $commit;
        }

        /**
         * Get the branch
         * @return Branch
         */
        public function getBranch()
        {
            return $this->_b2dbLazyLoad('_branch_id');
        }

        /**
         * Set the branch this change applies to
         *
         * @param Branch $branch
         */
        public function setBranch(Branch $branch)
        {
            $this->_branch_id = $branch;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new) {
                $this->_commit_sha = $this->getCommit()->getRevision();
            }
        }

        /**
         * Get the commit
         * @return Commit
         */
        public function getCommit()
        {
            return $this->_b2dbLazyLoad('_commit_id');
        }

    }
