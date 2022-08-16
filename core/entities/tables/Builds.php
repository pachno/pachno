<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Saveable;
    use b2db\Table;
    use b2db\Update;
    use pachno\core\entities\Build;
    use pachno\core\framework;

    /**
     * Builds table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Builds getTable() Retrieves an instance of this table
     *
     * @method Build selectById($id)
     * @method Build selectOne(Query $query, $join = 'all')
     * @method Build[] select(Query $query, $join = 'all')
     *
     * @Table(name="builds")
     * @Entity(class="\pachno\core\entities\Build")
     */
    class Builds extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'builds';

        public const ID = 'builds.id';

        public const SCOPE = 'builds.scope';

        public const NAME = 'builds.name';

        public const VERSION_MAJOR = 'builds.version_major';

        public const VERSION_MINOR = 'builds.version_minor';

        public const VERSION_REVISION = 'builds.version_revision';

        public const EDITION = 'builds.edition';

        public const RELEASE_DATE = 'builds.release_date';

        public const LOCKED = 'builds.locked';

        public const PROJECT = 'builds.project';

        public const MILESTONE = 'builds.milestone';

        public const RELEASED = 'builds.isreleased';

        public const FILE_ID = 'builds.file_id';

        public const FILE_URL = 'builds.file_url';

        public function preloadBuilds($build_ids)
        {
            if (!count($build_ids))
                return;

            $query = $this->getQuery();
            $query->where(self::ID, $build_ids, Criterion::IN);
            $this->select($query);
        }
    
        /**
         * @param $project_id
         * @return Build[]
         */
        public function getByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);
            $query->addOrderBy(self::RELEASE_DATE, QueryColumnSort::SORT_DESC);

            return $this->select($query);
        }

        public function getByFileID($file_id)
        {
            $query = $this->getQuery();
            $query->where(self::FILE_ID, $file_id);

            return $this->select($query);
        }

        public function getByEditionID($edition_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $query->addOrderBy(self::RELEASE_DATE, QueryColumnSort::SORT_DESC);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query);

            return $row;
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return [];

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, Criterion::IN);

            return $this->select($query);
        }

        /**
         * @return Build[]
         */
        public function selectAll(): array
        {
            $query = $this->getQuery();

            $query->join(Projects::getTable(), Projects::ID, self::PROJECT);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(Projects::NAME, QueryColumnSort::SORT_ASC);
            $query->addOrderBy(self::NAME, QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        protected function migrateData(Table $old_table): void
        {
            $query = $this->getQuery();
            $query->where(self::FILE_ID, null, Criterion::IS_NOT_NULL);
            $query->where(self::FILE_ID, 0, Criterion::NOT_EQUALS);

            $results = $this->rawSelect($query);
            if ($results) {
                while($row = $results->getNextRow()) {
                    BuildFiles::getTable()->addByBuildIDandFileID($row[self::ID], $row[self::FILE_ID], null, $row[self::SCOPE]);
                }
            }
        }

    }
