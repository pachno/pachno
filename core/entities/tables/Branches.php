<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\QueryColumnSort;
    use pachno\core\entities\Branch;
    use pachno\core\entities\Project;

    /**
     * Branches table
     *
     * @method static Branches getTable()
     *
     * @package pachno
     * @subpackage core
     *
     * @Entity(class="\pachno\core\entities\Branch")
     * @Table(name="branches")
     */
    class Branches extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'branches';

        public const ID = 'branches.id';

        public const SCOPE = 'branches.scope';

        public const PROJECT_ID = 'branches.project_id';

        /**
         * Get all branches inside a project
         *
         * @param Project $project
         *
         * @return Branch[]
         */
        public function getByProject(Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where('branches.is_deleted', false);
            $query->addOrderBy('branches.name', QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        /**
         * Get all branches inside a project
         *
         * @param int[] $commit_ids
         * @param Project $project
         *
         * @return Branch[]
         */
        public function getByCommitsAndProject($commit_ids, Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where('branches.latest_commit_id', $commit_ids, Criterion::IN);
            $query->where('branches.is_deleted', false);

            $branches = [];
            foreach ($this->select($query) as $branch) {
                if (!isset($branches[$branch->getLatestCommitId()])) {
                    $branches[$branch->getLatestCommitId()] = [];
                }
                $branches[$branch->getLatestCommitId()][] = $branch;
            }

            return $branches;
        }

        public function getOrCreateByBranchNameAndProject($name, Project $project)
        {
            $branch = $this->getByBranchNameAndProject($name, $project);

            if (!$branch instanceof Branch) {
                $branch = new Branch();
                $branch->setName($name);
                $branch->setProject($project);
                $branch->save();
            }

            return $branch;
        }

        public function getByBranchNameAndProject($name, Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where('branches.name', $name);
            $query->where('branches.is_deleted', false);

            $branch = $this->selectOne($query);

            return $branch;
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('project', self::PROJECT_ID);
        }

    }
