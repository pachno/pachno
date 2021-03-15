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

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'usercommits';

        const ID = 'usercommits.id';

        const SCOPE = 'usercommits.scope';

        const COMMIT = 'usercommits.commit';

        const UID = 'usercommits.uid';

        public function _setupIndexes()
        {
            $this->_addIndex('uid_scope', [self::UID, self::SCOPE]);
        }

        public function getUserIDsByCommitID($commit_id)
        {
            $uids = [];
            $query = $this->getQuery();

            $query->where(self::COMMIT, $commit_id);

            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $uid = $row->get(self::UID);
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
                        $insertion->add(self::UID, $uid);
                        $this->rawInsert($insertion);
                    }
                }
            }
        }

        public function getUserStarredCommits($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
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
            $insertion->add(self::UID, $user_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawInsert($insertion);
        }

        public function removeStarredCommit($user_id, $commit_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMMIT, $commit_id);
            $query->where(self::UID, $user_id);

            $this->rawDelete($query);

            return true;
        }

        public function hasStarredCommit($user_id, $commit_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMMIT, $commit_id);
            $query->where(self::UID, $user_id);

            return $this->count($query);
        }

        protected function initialize()
        {
            $this->setup(self::B2DBNAME, self::ID);
            $this->addForeignKeyColumn(self::COMMIT, Commits::getTable(), Commits::ID);
            $this->addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
        }

    }
