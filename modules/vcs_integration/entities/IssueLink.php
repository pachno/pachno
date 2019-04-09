<?php

    namespace pachno\modules\vcs_integration\entities;

    use \pachno\core\entities\Issue,
        pachno\modules\vcs_integration\entities\tables\IssueLinks;

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
     * @Table(name="\pachno\modules\vcs_integration\entities\tables\IssueLinks")
     */
    class IssueLink extends \pachno\core\entities\common\IdentifiableScoped
    {

        /**
         * Affected issue
         * @var \pachno\core\entities\Issue
         * @Column(type="integer", name="issue_no")
         * @Relates(class="\pachno\core\entities\Issue")
         */
        protected $_issue = null;

        /**
         * Associated commit
         * @var \pachno\modules\vcs_integration\entities\Commit
         * @Column(type="integer", name="commit_id")
         * @Relates(class="\pachno\modules\vcs_integration\entities\Commit")
         */
        protected $_commit = null;

        /**
         * Get the issue for this link
         * @return \pachno\core\entities\Issue
         */
        public function getIssue()
        {
            return $this->_b2dbLazyLoad('_issue');
        }

        /**
         * Get the commit with this link
         * @return \pachno\modules\vcs_integration\entities\Commit
         */
        public function getCommit()
        {
            return $this->_b2dbLazyLoad('_commit');
        }

        /**
         * Set the issue in this link
         * @param \pachno\core\entities\Issue $issue
         */
        public function setIssue(\pachno\core\entities\Issue $issue)
        {
            $this->_issue = $issue;
        }

        /**
         * Set the commit in this link
         * @param Commit $commit
         */
        public function setCommit(Commit $commit)
        {
            $this->_commit = $commit;
        }

        /**
         * Return all commits for a given issue
         * @param \pachno\core\entities\Issue $issue
         * @param integer $limit
         * @param integer $offset
         * @return array|\pachno\core\entities\Issue
         */
        public static function getCommitsByIssue(\pachno\core\entities\Issue $issue, $limit = null, $offset = null)
        {
            return tables\IssueLinks::getTable()->getByIssueID($issue->getID(), null, $limit, $offset);
        }

        /**
         * Return all issues for a given commit
         * @param \pachno\modules\vcs_integration\entities\Commit $commit
         * @return array
         */
        public static function getIssuesByCommit(Commit $commit)
        {
            return tables\IssueLinks::getTable()->getByCommitID($commit->getID());
        }

    }
