<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * User issues table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * User issues table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="userissues")
     */
    class UserIssues extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'userissues';

        const ID = 'userissues.id';

        const SCOPE = 'userissues.scope';

        const ISSUE_ID = 'userissues.issue';

        const USER_ID = 'userissues.uid';

        public function copyStarrers($from_issue_id, $to_issue_id)
        {
            $old_watchers = $this->getUserIDsByIssueID($from_issue_id);
            $new_watchers = $this->getUserIDsByIssueID($to_issue_id);

            if (count($old_watchers)) {
                $insertion = new Insertion();
                $insertion->add(self::ISSUE_ID, $to_issue_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                foreach ($old_watchers as $uid) {
                    if (!in_array($uid, $new_watchers)) {
                        $insertion->add(self::USER_ID, $uid);
                        $this->rawInsert($insertion);
                    }
                }
            }
        }

        public function getUserIDsByIssueID($issue_id)
        {
            $uids = [];
            $query = $this->getQuery();

            $query->where(self::ISSUE_ID, $issue_id);

            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $uid = $row->get(UserIssues::USER_ID);
                    $uids[$uid] = $uid;
                }
            }

            return $uids;
        }

        public function getUserStarredIssues($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->join(Issues::getTable(), Issues::ID, self::ISSUE_ID);
            $query->where(Issues::DELETED, 0);
            $query->addSelectionColumn(Issues::ID, 'issue_id');

            $res = $this->rawSelect($query);
            $issues = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $issue_id = $row['issue_id'];
                    $issues[$issue_id] = $issue_id;
                }
            }

            return $issues;
        }

        public function addStarredIssue($user_id, $issue_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::ISSUE_ID, $issue_id);
            $insertion->add(self::USER_ID, $user_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawInsert($insertion);
        }

        public function removeStarredIssue($user_id, $issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_id);
            $query->where(self::USER_ID, $user_id);

            $this->rawDelete($query);

            return true;
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
        }

        protected function setupIndexes()
        {
            $this->addIndex('uid_scope', [self::USER_ID, self::SCOPE]);
        }
    }
