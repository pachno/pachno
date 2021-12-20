<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * User commits table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * User commits table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="usercommits")
     */
    class UserCommits extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'usercommits';

        public const ID = 'usercommits.id';

        public const SCOPE = 'usercommits.scope';

        public const COMMIT = 'usercommits.commit';

        public const USER_ID = 'usercommits.uid';

        public function _setupIndexes(): void
        {
            $this->_addIndex('uid_scope', [self::USER_ID, self::SCOPE]);
        }

        public function getUserIDsByCommitID($commit_id)
        {
            $uids = [];
            $query = $this->getQuery();

            $query->where(self::COMMIT, $commit_id);

            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $uid = $row->get(self::USER_ID);
                    $uids[$uid] = $uid;
                }
            }

            return $uids;
        }

        public function copyStarrers($from_commit_id, $to_commit_id)
        {
            $old_watchers = $this->getUserIDsByIssueID($from_commit_id);
            $new_watchers = $this->getUserIDsByIssueID($to_commit_id);

            if (count($old_watchers)) {
                $insertion = new Insertion();
                $insertion->add(self::COMMIT, $to_commit_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                foreach ($old_watchers as $uid) {
                    if (!in_array($uid, $new_watchers)) {
                        $insertion->add(self::USER_ID, $uid);
                        $this->rawInsert($insertion);
                    }
                }
            }
        }

        public function getUserStarredCommits($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->join(Commits::getTable(), Commits::ID, self::COMMIT);
            $query->where(Commits::DELETED, 0);

            $res = $this->select($query);

            return $res;
        }

        public function addStarredCommit($user_id, $commit_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::COMMIT, $commit_id);
            $insertion->add(self::USER_ID, $user_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawInsert($insertion);
        }

        public function removeStarredCommit($user_id, $commit_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMMIT, $commit_id);
            $query->where(self::USER_ID, $user_id);

            $this->rawDelete($query);

            return true;
        }

        public function hasStarredCommit($user_id, $commit_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMMIT, $commit_id);
            $query->where(self::USER_ID, $user_id);

            return $this->count($query);
        }

        protected function initialize(): void
        {
            $this->setup(self::B2DBNAME, self::ID);
            $this->addForeignKeyColumn(self::COMMIT, Commits::getTable(), Commits::ID);
            $this->addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
        }

    }
