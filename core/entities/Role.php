<?php

    namespace pachno\core\entities;

    use pachno\core\entities\tables\Permissions;
    use pachno\core\modules\publish\Publish;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Role extends Datatype
    {

        const ITEMTYPE = Datatype::ROLE;

        protected static $_items = null;

        protected $_itemtype = Datatype::ROLE;

        /**
         * @Relates(class="\pachno\core\entities\RolePermission", collection=true, foreign_column="role_id")
         */
        protected $_permissions = null;

        protected $_number_of_users = null;

        public static function loadFixtures(Scope $scope)
        {
            $roles = [];
            $roles['Developer'] = [
                ['permission' => Permissions::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => 'canvoteforissues'],
                ['permission' => 'canlockandeditlockedissues'],
                ['permission' => 'cancreateandeditissues'],
                ['permission' => 'caneditissue'],
                ['permission' => 'caneditissuecustomfields'],
                ['permission' => 'canaddextrainformationtoissues'],
                ['permission' => 'canpostseeandeditallcomments'],
                ['permission' => Permissions::PERMISSION_EDIT_DOCUMENTATION],
            ];
            $roles['Project manager'] = [
                ['permission' => Permissions::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => 'canvoteforissues'],
                ['permission' => 'canlockandeditlockedissues'],
                ['permission' => 'cancreateandeditissues'],
                ['permission' => 'caneditissue'],
                ['permission' => 'caneditissuecustomfields'],
                ['permission' => 'canaddextrainformationtoissues'],
                ['permission' => 'canpostseeandeditallcomments'],
                ['permission' => Permissions::PERMISSION_EDIT_DOCUMENTATION],
            ];
            $roles['Tester'] = [
                ['permission' => Permissions::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => 'canvoteforissues'],
                ['permission' => 'cancreateandeditissues'],
                ['permission' => 'caneditissuecustomfields'],
                ['permission' => 'canaddextrainformationtoissues'],
                ['permission' => 'canpostandeditcomments'],
                ['permission' => Permissions::PERMISSION_EDIT_DOCUMENTATION_OWN],
            ];
            $roles['Documentation editor'] = [
                ['permission' => Permissions::PERMISSION_PROJECT_ACCESS],
                ['permission' => Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS],
                ['permission' => 'canvoteforissues'],
                ['permission' => 'cancreateandeditissues'],
                ['permission' => 'canaddextrainformationtoissues'],
                ['permission' => 'canpostandeditcomments'],
                ['permission' => Permissions::PERMISSION_EDIT_DOCUMENTATION],
                ['permission' => Permissions::PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION],
            ];

            foreach ($roles as $name => $permissions) {
                $role = new Role();
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
        public static function getAll()
        {
            return tables\ListTypes::getTable()->getAllByItemTypeAndItemdata(self::ROLE, null);
        }

        /**
         * Returns all project roles available
         *
         * @return Role[]
         */
        public static function getGlobalRoles()
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
        public static function getByProjectID($project_id)
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
         * @return Project
         */
        public function getProject()
        {
            return ($this->getItemdata()) ? Project::getB2DBTable()->selectById((int)$this->getItemdata()) : null;
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
            $this->_populatePermissions();
            $permission_id = $permission->getID();
            unset($this->_permissions[$permission_id]);
            tables\Permissions::getTable()->deleteRolePermission($this->getID(), $permission->getModule(), $permission->getPermission());
            $permission->delete();
        }

        protected function _populatePermissions()
        {
            if ($this->_permissions === null) {
                $this->_b2dbLazyLoad('_permissions');
            }
        }

        public function addPermissions($permissions)
        {
            foreach ($permissions as $permission) {
                $this->addPermission($permission);
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

        protected function _preDelete()
        {
            tables\Permissions::getTable()->deleteRolePermissions($this->getID());
            tables\RolePermissions::getTable()->clearPermissionsForRole($this->getID());
            tables\ProjectAssignedTeams::getTable()->deleteByRoleID($this->getID());
            tables\ProjectAssignedUsers::getTable()->deleteByRoleID($this->getID());
        }

    }
