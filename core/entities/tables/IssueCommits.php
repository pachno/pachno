<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Saveable;
    use pachno\core\entities\IssueCommit;
    use pachno\core\framework;

    /**
     * Issue commits table
     *
     * @method static IssueCommits getTable()
     * @method IssueCommit[] select(Query $query, $join = 'all')
     * @method IssueCommit selectById($id, Query $query = null, $join = 'all')
     * @method IssueCommit selectOne(Query $query, $join = 'all')
     *
     * @Entity(class="\pachno\core\entities\IssueCommit")
     * @Table(name="issuecommits")
     */
    class IssueCommits extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'issuecommits';

        public const ID = 'issuecommits.id';

        public const SCOPE = 'issuecommits.scope';

        public const ISSUE_NO = 'issuecommits.issue_no';

        public const COMMIT_ID = 'issuecommits.commit_id';

        /**
         * Get all rows by commit ID
         *
         * @param integer $id
         *
         * @return IssueCommit[]
         */
        public function getByCommitID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::COMMIT_ID, $id);

            return $this->select($query);
        }

        /**
         * Get all rows by issue ID
         *
         * @param integer $id
         * @param integer $scope
         * @param integer $limit
         * @param integer $offset
         *
         * @return IssueCommit[]
         */
        public function getByIssueID($id, $limit = null, $offset = null, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::ISSUE_NO, $id);
            $query->addOrderBy(Commits::DATE, QueryColumnSort::SORT_DESC);

            if ($limit !== null)
                $query->setLimit($limit);

            if ($offset !== null)
                $query->setOffset($offset);

            return $this->select($query);
        }

        /**
         * Count all rows by issue ID
         *
         * @param integer $id
         *
         * @return integer
         */
        public function countByIssueID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::ISSUE_NO, $id);

            return $this->count($query);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('commit', self::COMMIT_ID);
            $this->addIndex('issue', self::ISSUE_NO);
        }

    }
