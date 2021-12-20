<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\QueryColumnSort;
    use pachno\core\entities\Commit;
    use pachno\core\entities\Project;

    /**
     * Commits table
     *
     * @method static Commits getTable()
     * @method Commit selectById($id)
     *
     * @package pachno
     * @subpackage vcs_integration
     *
     * @Entity(class="\pachno\core\entities\Commit")
     * @Table(name="commits")
     */
    class Commits extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'commits';

        public const ID = 'commits.id';

        public const SCOPE = 'commits.scope';

        public const LOG = 'commits.log';

        public const OLD_REV = 'commits.old_rev';

        public const NEW_REV = 'commits.new_rev';

        public const AUTHOR = 'commits.author';

        public const DATE = 'commits.date';

        public const DATA = 'commits.data';

        public const PROJECT_ID = 'commits.project_id';

        /**
         * Get commit for a given commit id
         *
         * @param string $hash
         * @param Project $project
         *
         * @return Commit
         */
        public function getCommitByHash($hash, Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::NEW_REV, $hash);
            $query->where(self::PROJECT_ID, $project->getID());

            return $this->selectOne($query);
        }

        /**
         * Get unlinked commits
         *
         * @param Project $project
         *
         * @return Commit[]
         */
        public function getUnprocessedCommitsByProject(Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where(self::OLD_REV, '', Criterion::NOT_EQUALS);
            $query->where('commits.previous_commit_id', 0);

            return $this->select($query);
        }

        /**
         * Whether a commit is already processed
         *
         * @param string $id
         * @param integer $project
         */
        public function isProjectCommitProcessed($id, $project)
        {
            $query = $this->getQuery();

            $query->where(self::NEW_REV, $id);
            $query->where(self::PROJECT_ID, $project);

            return (bool)$this->count($query);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('project', self::PROJECT_ID);
            $this->addIndex('project_commit', [self::PROJECT_ID, self::NEW_REV]);
        }

    }
