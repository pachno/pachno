<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\FormObject;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\tables\Projects;
    use pachno\core\framework\Context;
    use pachno\core\framework\Request;
    use pachno\core\modules\publish\Publish;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Role extends Datatype implements FormObject
    {

        public const ITEMTYPE = Datatype::ROLE;

        protected static $_items = null;

        protected $_itemtype = Datatype::ROLE;

        /**
         * @var Permission[]
         * @Relates(class="\pachno\core\entities\RolePermission", collection=true, foreign_column="role_id")
         */
        protected $_permissions = null;

        protected $_number_of_users = null;

        public static function loadFixtures(Scope $scope)
        {
            $roles = [];
            $roles['Developer'] = [
                ['permission' => Permission::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permission::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => Permission::PERMISSION_PROJECT_DEVELOPER],
                ['permission' => Permission::PERMISSION_MANAGE_PROJECT_LOCK_ISSUES],
                ['permission' => Permission::PERMISSION_EDIT_ISSUES],
                ['permission' => Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION],
                ['permission' => Permission::PERMISSION_PROJECT_CREATE_ISSUES],
            ];
            $roles['Project manager'] = [
                ['permission' => Permission::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permission::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => Permission::PERMISSION_MANAGE_PROJECT],
                ['permission' => Permission::PERMISSION_PROJECT_CREATE_ISSUES],
                ['permission' => Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION],
            ];
            $roles['Tester'] = [
                ['permission' => Permission::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permission::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => Permission::PERMISSION_EDIT_ISSUES],
                ['permission' => Permission::PERMISSION_PROJECT_DEVELOPER_DISCUSS_CODE],
                ['permission' => Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION_OWN],
            ];
            $roles['Documentation editor'] = [
                ['permission' => Permission::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permission::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => Permission::PERMISSION_EDIT_ISSUES_MODERATE_COMMENTS],
                ['permission' => Permission::PERMISSION_EDIT_ISSUES_COMMENTS],
                ['permission' => Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION],
                ['permission' => Permission::PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION],
            ];

            foreach ($roles as $name => $permissions) {
                $role = new self();
                $role->setName($name);
                $role->setScope($scope);
                $role->save();
                foreach ($permissions as $k => $permission) {
                    $p = new RolePermission();
                    $p->setPermission($permission['permission']);

                    if (array_key_exists('target_id', $permission)) $p->setTargetID($permission['target_id']);
                    if (array_key_exists('module', $permission)) $p->setModule($permission['module']);

                    $role->addPermission($p);
                }
            }
        }

        public function addPermission(RolePermission $permission)
        {
            $permission->setRole($this);
            $permission->save();
            if ($this->_permissions !== null) {
                $this->_permissions[$permission->getID()] = $permission;
            }
            tables\Permissions::getTable()->addRolePermission($this, $permission);
        }

        /**
         * Returns all project roles available
         *
         * @return Role[]
         */
        public static function getAll(): array
        {
            return tables\ListTypes::getTable()->getAllByItemTypeAndItemdata(self::ROLE, 0);
        }

        /**
         * Returns all project roles available
         *
         * @return Role[]
         */
        public static function getGlobalRoles(): array
        {
            $roles = self::getAll();

            $global_roles = [];
            foreach ($roles as $id => $role) {
                if ($role->isSystemRole()) {
                    $global_roles[$id] = $role;
                }
            }

            return $global_roles;
        }

        /**
         * Returns all project roles available for a specific project
         *
         * @return Role[]
         */
        public static function getByProjectID($project_id): array
        {
            return tables\ListTypes::getTable()->getAllByItemTypeAndItemdata(self::ROLE, $project_id);
        }

        public function isSystemRole()
        {
            return (bool) !$this->getItemdata();
        }

        /**
         * Return the associated project if any
         *
         * @return ?Project
         */
        public function getProject(): ?Project
        {
            return ($this->getItemdata()) ? Projects::getTable()->selectById((int)$this->getItemdata()) : null;
        }

        public function getProjectId(): int
        {
            $project = $this->getProject();

            return ($project instanceof Project) ? $project->getID() : 0;
        }

        public function setProject($project)
        {
            $this->setItemdata((is_object($project)) ? $project->getID() : $project);
        }

        /**
         * Removes a set of permissions
         *
         * @param array|RolePermission $permissions
         */
        public function removePermissions($permissions)
        {
            foreach ($permissions as $permission) {
                $this->removePermission($permission);
            }
        }

        /**
         * Removes permission from the role.
         *
         * @param permission \pachno\core\entities\RolePermission Role permission that should be removed.
         */
        public function removePermission(RolePermission $permission)
        {
            $permission_id = $permission->getID();
            if (is_array($this->_permissions)) {
                unset($this->_permissions[$permission_id]);
            }

            tables\Permissions::getTable()->deleteRolePermission($this->getID(), $permission->getModule(), $permission->getPermission());
            $permission->delete();
        }

        protected function _populatePermissions()
        {
            if ($this->_permissions === null) {
                $this->_b2dbLazyLoad('_permissions');
            }
        }

        public function hasPermission($permission_key, $module = 'core', $target_id = null)
        {
            foreach ($this->getPermissions() as $role_permission) {
                if ($role_permission->getPermission() == $permission_key && $role_permission->getModule() == $module && $role_permission->getTargetID() == $target_id) return true;
            }

            return false;
        }

        /**
         * Returns all permissions assigned to this role
         *
         * @return RolePermission[]
         */
        public function getPermissions()
        {
            $this->_populatePermissions();

            return $this->_permissions;
        }

        public function getNumberOfRoleUsers()
        {
            if ($this->_number_of_users === null) {
                $this->_number_of_users = Permissions::getTable()->countRolePermissionUsers($this->getID());
            }

            return $this->_number_of_users;
        }

        protected function _preDelete(): void
        {
            tables\Permissions::getTable()->deleteRolePermissions($this->getID());
            tables\RolePermissions::getTable()->clearPermissionsForRole($this->getID());
            tables\ProjectAssignedTeams::getTable()->deleteByRoleID($this->getID());
            tables\ProjectAssignedUsers::getTable()->deleteByRoleID($this->getID());
        }

        public function updateFromRequest(Request $request)
        {
            $this->setName($request['name']);
            $this->setProject($request['project_id']);
        }

        public function saveFromRequest(Request $request)
        {
            $this->save();
            $new_permissions = [];
            foreach ($request['permissions'] ?: [] as $new_permission) {
                $permission_details = explode(',', $new_permission);
                $new_permissions[$permission_details[2]] = ['module' => $permission_details[0], 'target_id' => $permission_details[1]];
            }
            $existing_permissions = [];
            foreach ($this->getPermissions() as $existing_permission) {
                if (!array_key_exists($existing_permission->getPermission(), $new_permissions)) {
                    $this->removePermission($existing_permission);
                } else {
                    $existing_permissions[$existing_permission->getPermission()] = $new_permissions[$existing_permission->getPermission()];
                    unset($new_permissions[$existing_permission->getPermission()]);
                }
            }
            foreach ($new_permissions as $permission_key => $details) {
                $p = new RolePermission();
                $p->setModule($details['module']);
                $p->setPermission($permission_key);
                if ($details['target_id']) {
                    $p->setTargetID($details['target_id']);
                }

                $this->addPermission($p);
            }
            foreach ($existing_permissions as $permission_key => $details) {
                $p = new RolePermission();
                $p->setModule($details['module']);
                $p->setPermission($permission_key);
                if ($details['target_id']) {
                    $p->setTargetID($details['target_id']);
                }

                tables\Permissions::getTable()->addRolePermission($this, $p);
            }

            Context::clearPermissionsCache();
            Context::cacheAllPermissions();
        }

    }
