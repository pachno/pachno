<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;

    /**
     * Roles <- permissions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Roles <- permissions table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="rolepermissions")
     * @Entity(class="\pachno\core\entities\RolePermission")
     */
    class RolePermissions extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'rolepermissions';

        public const ID = 'rolepermissions.id';

        public const ROLE_ID = 'rolepermissions.role_id';

        public const PERMISSION = 'rolepermissions.permission';

        public const MODULE = 'rolepermissions.module';

        public const TARGET_ID = 'rolepermissions.target_id';

        public function clearPermissionsForRole($role_id)
        {
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $this->rawDelete($query);
        }

        public function addPermissionForRole($role_id, $permission, $module, $target_id = null)
        {
            $insertion = new Insertion();
            $insertion->add(self::ROLE_ID, $role_id);
            $insertion->add(self::PERMISSION, $permission);
            $insertion->add(self::MODULE, $module);
            $insertion->add(self::TARGET_ID, $target_id);

            $this->rawInsert($insertion);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('role_id', self::ROLE_ID);
        }

        public function updateRole($current_role_id, $new_role_id)
        {
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $current_role_id);

            $update = new Update();
            $update->add(self::ROLE_ID, $new_role_id);

            $this->rawUpdate($update, $query);
        }

    }
