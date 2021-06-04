<?php

    namespace pachno\core\entities\tables;

    use pachno\core\framework;

    /**
     * Commit files table
     *
     * @method static CommitFiles getTable()
     *
     * @Entity(class="\pachno\core\entities\CommitFile")
     * @Table(name="commitfiles")
     */
    class CommitFiles extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'commitfiles';

        public const ID = 'commitfiles.id';

        public const SCOPE = 'commitfiles.scope';

        public const COMMIT_ID = 'commitfiles.commit_id';

        public const FILE_NAME = 'commitfiles.file_name';

        public const ACTION = 'commitfiles.action';

        /**
         * Get all affected files by commit
         *
         * @param integer $id
         */
        public function getByCommitID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::COMMIT_ID, $id);

            return $this->select($query);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('commit', self::COMMIT_ID);
        }

    }
