<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use pachno\core\entities\Group;
    use pachno\core\framework;

    /**
     * Groups table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Groups table
     *
     * @method static Groups getTable()
     * @method Group selectById($id, Query $query = null, $join = 'all')
     * @method Group selectOne(Query $query = null, $join = 'all')
     * @method Group[] select(Query $query, $join = 'all')
     *
     * @Table(name="groups")
     * @Entity(class="\pachno\core\entities\Group")
     */
    class Groups extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'groups';

        public const ID = 'groups.id';

        public const NAME = 'groups.name';

        public const SCOPE = 'groups.scope';
    
        /**
         * @param $scope
         * @return Group[]
         */
        public function getAll($scope = null)
        {
            $scope = $scope ?? framework\Context::getScope()->getID();

            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);

            return $this->select($query);
        }

        /**
         * @param $group_name
         * @param null $scope
         * @return Group
         */
        public function getByName($group_name, $scope = null): ?Group
        {
            $scope = $scope ?? framework\Context::getScope()->getID();

            $query = $this->getQuery();
            $query->where(self::NAME, $group_name);
            $query->where(self::SCOPE, $scope);

            return $this->selectOne($query);
        }

        public function doesGroupNameExist($group_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $group_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (bool)$this->count($query);
        }

    }
