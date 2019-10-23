<?php

    namespace pachno\core\entities;

    /**
     * Issue to Commit link class, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage vcs_integration
     */

    /**
     * Issue to Commit link class, vcs_integration
     *
     * @package pachno
     * @subpackage vcs_integration
     *
     * @Table(name="\pachno\core\entities\tables\IssueCommits")
     */
    class IssueCommit extends common\IdentifiableScoped
    {

        /**
         * Affected issue
         * @var Issue
         * @Column(type="integer", name="issue_no")
         * @Relates(class="\pachno\core\entities\Issue")
         */
        protected $_issue = null;

        /**
         * Associated commit
         * @var Commit
         * @Column(type="integer", name="commit_id")
         * @Relates(class="\pachno\core\entities\Commit")
         */
        protected $_commit = null;

        /**
         * Get the issue for this link
         * @return Issue
         */
        public function getIssue()
        {
            return $this->_b2dbLazyLoad('_issue');
        }

        /**
         * Set the issue in this link
         *
         * @param Issue $issue
         */
        public function setIssue(Issue $issue)
        {
            $this->_issue = $issue;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);

            if ($is_new) {
                $log_item = new LogItem();
                $log_item->setChangeType(LogItem::ACTION_ISSUE_UPDATE_COMMIT);
                $log_item->setTarget($this->getID());
                $log_item->setTargetType(LogItem::TYPE_ISSUE_COMMIT);
                $log_item->setProject($this->getCommit()->getProject());
                $log_item->setUser($this->getCommit()->getAuthor()->getID());
                $log_item->setTime($this->getCommit()->getDate());
                $log_item->save();
            }
        }

        /**
         * Get the commit with this link
         * @return Commit
         */
        public function getCommit()
        {
            return $this->_b2dbLazyLoad('_commit');
        }

        /**
         * Set the commit in this link
         *
         * @param Commit $commit
         */
        public function setCommit(Commit $commit)
        {
            $this->_commit = $commit;
        }

    }
