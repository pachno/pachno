<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use b2db\Row;

    /**
     * Scopes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Scopes table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="scopehostnames")
     */
    class ScopeHostnames extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'scopehostnames';

        public const ID = 'scopehostnames.id';

        public const SCOPE_ID = 'scopehostnames.scope_id';

        public const SCOPE = 'scopehostnames.scope_id';

        public const HOSTNAME = 'scopehostnames.hostname';

        public function removeHostnameFromScope($hostname, $scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::HOSTNAME, $hostname);
            $query->where(self::SCOPE_ID, $scope_id);
            $res = $this->rawDelete($query);
        }

        public function saveScopeHostnames($hostnames, $scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE_ID, $scope_id);
            $res = $this->rawDelete($query);
            foreach ($hostnames as $hostname) {
                $this->addHostnameToScope($hostname, $scope_id);
            }
        }

        public function addHostnameToScope($hostname, $scope_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::HOSTNAME, $hostname);
            $insertion->add(self::SCOPE_ID, $scope_id);
            $res = $this->rawInsert($insertion);
        }

        public function getHostnamesForScope($scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE_ID, $scope_id);

            $hostnames = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $hostnames[$row->get(self::ID)] = $row->get(self::HOSTNAME);
                }
            }

            return $hostnames;
        }

        public function getScopeIDForHostname($hostname)
        {
            $query = $this->getQuery();
            $query->where(self::HOSTNAME, $hostname);

            $row = $this->rawSelectOne($query);

            return ($row instanceof Row) ? (int)$row->get(self::SCOPE_ID) : null;
        }

        public function addIndexes()
        {
            $this->setupIndexes();
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('id_hostname', [self::ID, self::HOSTNAME]);
            $this->addIndex('scopeid_hostname', [self::SCOPE_ID, self::HOSTNAME]);
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::HOSTNAME, 200, '');
        }

    }
