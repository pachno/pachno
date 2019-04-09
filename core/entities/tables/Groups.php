<?php

    namespace pachno\core\entities\tables;

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
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="groups")
     * @Entity(class="\pachno\core\entities\Group")
     */
    class Groups extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'groups';
        const ID = 'groups.id';
        const NAME = 'groups.name';
        const SCOPE = 'groups.scope';

        public function getAll($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            
            $res = $this->select($query);
            
            return $res;
        }

        public function doesGroupNameExist($group_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $group_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->count($query);
        }
        
    }
