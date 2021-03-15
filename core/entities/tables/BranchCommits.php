<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\QueryColumnSort;
    use pachno\core\entities\Branch;
    use pachno\core\entities\Commit;

    /**
     * Branch commits table
     *
     * @method static BranchCommits getTable()
     *
     * @Entity(class="\pachno\core\entities\BranchCommit")
     * @Table(name="branchcommits")
     */
    class BranchCommits extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'branchcommits';

        const ID = 'branchcommits.id';

        const SCOPE = 'branchcommits.scope';

        const BRANCH_ID = 'branchcommits.branch_id';

        const COMMIT_ID = 'branchcommits.commit_id';

        const COMMIT_SHA = 'branchcommits.commit_sha';

        public function hasBranchCommitSha(Branch $branch, $commit_sha)
        {
            $query = $this->getQuery();
            $query->where('branchcommits.branch_id', $branch->getID());
            $query->where('branchcommits.commit_sha', $commit_sha);

            return (bool)$this->count($query);
        }

        public function hasCommitInDifferentBranch(Commit $commit, Branch $branch)
        {
            $query = $this->getQuery();
            $query->where('branchcommits.branch_id', $branch->getID(), Criterion::NOT_EQUALS);
            $query->where('branchcommits.commit_id', $commit->getID());

            return (bool)$this->count($query);
        }

        protected function setupIndexes()
        {
            $this->addIndex('commit', self::COMMIT_ID);
            $this->addIndex('branch', self::BRANCH_ID);
        }

        /**
         * @param $branch_id
         * @return int
         */
        public function getPaginationItemCount($branch_id)
        {
            $query = $this->getQuery();
            $query->addOrderBy('branchcommits.id', QueryColumnSort::SORT_ASC);
            $query->where('branchcommits.branch_id', $branch_id);

            return $this->count($query);
        }

    }
