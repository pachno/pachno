<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\QueryColumnSort;
    use pachno\core\entities\IssuetypeScheme;
    use pachno\core\framework;

    /**
     * Issuetype schemes table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static IssuetypeSchemes getTable()
     * @method IssuetypeScheme selectById(integer $id, Criteria $query = null, $join = 'all') Retrieves an issue
     *
     * @Table(name="issuetype_schemes")
     * @Entity(class="\pachno\core\entities\IssuetypeScheme")
     */
    class IssuetypeSchemes extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'issuetype_schemes';

        public const ID = 'issuetype_schemes.id';

        public const SCOPE = 'issuetype_schemes.scope';

        public const NAME = 'issuetype_schemes.name';

        public const DESCRIPTION = 'issuetype_schemes.description';

        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::ID, QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        public function getNumberOfSchemesInCurrentScope()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (int)$this->count($query);
        }

        public function getFirstIdByScope($scope_id = null)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'id');

            if ($scope_id === null) {
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
            } else {
                $query->where(self::SCOPE, $scope_id);
            }
            $query->addOrderBy(self::ID);
            $row = $this->rawSelectOne($query);

            return ($row) ? $row->get('id') : 0;
        }

    }
