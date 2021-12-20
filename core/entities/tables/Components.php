<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\QueryColumnSort;
    use pachno\core\entities\Component;
    use pachno\core\framework;

    /**
     * Components table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Components table
     *
     * @method static Components getTable()
     * @method Component selectById($id)
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="components")
     * @Entity(class="\pachno\core\entities\Component")
     */
    class Components extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'components';

        public const ID = 'components.id';

        public const SCOPE = 'components.scope';

        public const NAME = 'components.name';

        public const VERSION_MAJOR = 'components.version_major';

        public const VERSION_MINOR = 'components.version_minor';

        public const VERSION_REVISION = 'components.version_revision';

        public const PROJECT = 'components.project';

        public const LEAD_BY = 'components.leader';

        public const LEAD_TYPE = 'components.leader_type';

        public function preloadComponents($component_ids)
        {
            if (!count($component_ids))
                return;

            $query = $this->getQuery();
            $query->where(self::ID, $component_ids, Criterion::IN);
            $this->select($query);
        }

        public function getByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);
            $res = $this->rawSelect($query, false);

            return $res;
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return [];

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, Criterion::IN);

            return $this->select($query);
        }

        public function selectAll(): array
        {
            $query = $this->getQuery();

            $query->join(Projects::getTable(), Projects::ID, self::PROJECT);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(Projects::NAME, QueryColumnSort::SORT_ASC);
            $query->addOrderBy(self::NAME, QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

    }
