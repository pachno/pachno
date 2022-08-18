<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\Saveable;
    use pachno\core\entities\Issuetype;
    use pachno\core\framework;

    /**
     * Issue types table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Issue types table
     *
     * @method static IssueTypes getTable()
     * @method Issuetype[] select(Query $query, $join = 'all')
     * @method Issuetype[] selectAll()
     * @method Issuetype selectOne(Query $query, $join = 'all')
     * @method Issuetype selectById($id, Query $query = null, $join = 'all')
     *
     * @Table(name="issuetypes")
     * @Entity(class="\pachno\core\entities\Issuetype")
     */
    class IssueTypes extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'issuetypes';

        public const ID = 'issuetypes.id';

        public const SCOPE = 'issuetypes.scope';

        public const NAME = 'issuetypes.name';

        public const DESCRIPTION = 'issuetypes.description';

        public const ICON = 'issuetypes.icon';

        public const TASK = 'issuetypes.task';
    
        /**
         * @return Issuetype[]
         */
        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }
    
        /**
         * @param $ids
         * @return Issuetype[]
         */
        public function getByIds($ids)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, Criterion::IN);
            $query->indexBy(self::ID);

            return $this->select($query);
        }

        public function getAllIDsByScopeID($scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope_id);
            $query->addSelectionColumn(self::ID, 'id');
            $res = $this->rawSelect($query);

            $ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $id = $row->get('id');
                    $ids[$id] = $id;
                }
            }

            return $ids;
        }

        public function getBugReportTypeIDs()
        {
            $query = $this->getQuery();
            $query->where(self::ICON, 'bug_report');
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            $retarr = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $retarr[] = $row->get(self::ID);
                }
            }

            return $retarr;
        }

    }
