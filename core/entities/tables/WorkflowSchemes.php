<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use b2db\QueryColumnSort;
    use pachno\core\entities\WorkflowScheme;
    use pachno\core\framework;

    /**
     * Workflow schemes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Workflow schemes table
     *
     * @method WorkflowScheme selectById($id, Query $query = null, $join = 'all')
     *
     * @Table(name="workflow_schemes")
     * @Entity(class="\pachno\core\entities\WorkflowScheme")
     */
    class WorkflowSchemes extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'workflow_schemes';

        public const ID = 'workflow_schemes.id';

        public const SCOPE = 'workflow_schemes.scope';

        public const NAME = 'workflow_schemes.name';

        public const DESCRIPTION = 'workflow_schemes.description';

        public function getAll($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->addOrderBy(self::ID, QueryColumnSort::SORT_ASC);

            $res = $this->select($query);

            return $res;
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query, false);

            return $row;
        }

        public function getFirstIdByScope($scope_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'id');
            $query->where(self::SCOPE, $scope_id);
            $query->addOrderBy(self::ID);
            $row = $this->rawSelectOne($query);

            return ($row) ? $row->get('id') : 0;
        }

    }
