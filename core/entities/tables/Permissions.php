<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Insertion;
    use b2db\Query;
    use pachno\core\entities\common\Identifiable;
    use pachno\core\entities\Group;
    use pachno\core\entities\Permission;
    use pachno\core\entities\Project;
    use pachno\core\entities\Role;
    use pachno\core\entities\RolePermission;
    use pachno\core\entities\Scope;
    use pachno\core\entities\Team;
    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * Permissions table
     *
     * @method static Permissions getTable()
     * @method Permission selectById($id, Query $query = null, $join = 'all')
     * @method Permission[] select(Query $query, $join = 'all')
     *
     * @Table(name="permissions")
     * @Entity(class="\pachno\core\entities\Permission")
     */
    class Permissions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'permissions';

        const ID = 'permissions.id';

        const SCOPE = 'permissions.scope';

        const PERMISSION_TYPE = 'permissions.permission_type';

        const TARGET_ID = 'permissions.target_id';

        const USER_ID = 'permissions.uid';

        const GROUP_ID = 'permissions.gid';

        const TEAM_ID = 'permissions.tid';

        const CLIENT_ID = 'permissions.client_id';

        const ALLOWED = 'permissions.allowed';

        const MODULE = 'permissions.module';

        const ROLE_ID = 'permissions.role_id';

        public function getAll($scope_id = null)
        {
            $scope_id = ($scope_id === null) ? framework\Context::getScope()->getID() : $scope_id;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope_id);
            $res = $this->rawSelect($query, 'none');

            return $res;
        }

        public function removeSavedPermission($user_id, $group_id, $team_id, $module, $permission_type, $target_id, $scope, $role_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::TEAM_ID, $team_id);
            $query->where(self::MODULE, $module);
            $query->where(self::PERMISSION_TYPE, $permission_type);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::SCOPE, $scope);
            if ($role_id !== null) {
                $query->where(self::ROLE_ID, $role_id);
            }

            $res = $this->rawDelete($query);
        }

        public function removeGroupPermission($group_id, $permission_type = null, $module = 'core', $target_id = 0)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, 0);
            $query->where(self::TEAM_ID, 0);
            $query->where(self::CLIENT_ID, 0);
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::MODULE, $module);
            if ($permission_type !== null) {
                $query->where(self::PERMISSION_TYPE, $permission_type);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawDelete($query);
        }

        public function removeTeamPermission($team_id, $permission_type = null, $module = 'core', $target_id = 0)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, 0);
            $query->where(self::TEAM_ID, $team_id);
            $query->where(self::CLIENT_ID, 0);
            $query->where(self::GROUP_ID, 0);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::MODULE, $module);
            if ($permission_type !== null) {
                $query->where(self::PERMISSION_TYPE, $permission_type);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawDelete($query);
        }

        public function removeClientPermission($client_id, $permission_type = null, $module = 'core', $target_id = 0)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, 0);
            $query->where(self::CLIENT_ID, $client_id);
            $query->where(self::CLIENT_ID, 0);
            $query->where(self::GROUP_ID, 0);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::MODULE, $module);
            if ($permission_type !== null) {
                $query->where(self::PERMISSION_TYPE, $permission_type);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawDelete($query);
        }

        public function deleteAllPermissionsForCombination($user_id, $group_id, $team_id, $target_id = 0, $role_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::TEAM_ID, $team_id);
            $query->where(self::CLIENT_ID, 0);
            if ($target_id == 0) {
                $query->where(self::TARGET_ID, $target_id);
            } else {
                $criteria = new Criteria();
                $criteria->where(self::TARGET_ID, $target_id);
                $criteria->or(self::TARGET_ID, 0);
                $query->and($criteria);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($role_id !== null) {
                $query->where(self::ROLE_ID, $role_id);
            }

            $res = $this->rawDelete($query);
        }

        public function deleteModulePermissions($module_name, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::MODULE, $module_name);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deleteRolePermissions($role_id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deleteAllRolePermissions($scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope_id);
            $this->rawDelete($query);
        }

        public function countRolePermissionUsers($role_id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::SCOPE, $scope);
            $query->addGroupBy(self::USER_ID);

            return (int) $this->count($query);
        }

        /**
         * Removes the specified permission associated with the role.
         *
         * @param integer $role_id Role ID.
         * @param string $module Module.
         * @param mixed $permission_type Permission type.
         * @param integer|Scope $scope Scope. If null, current scope will be used.
         */
        public function deleteRolePermission($role_id, $module, $permission_type, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::MODULE, $module);
            $query->where(self::PERMISSION_TYPE, $permission_type);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deletePermissionsByRoleAndUser($role_id, $user_id, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deletePermissionsByRoleAndTeam($role_id, $team_id, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::TEAM_ID, $team_id);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        /**
         * @param $group_id
         * @return Permission[]
         */
        public function getPermissionsByGroupId($group_id)
        {
            $query = $this->getQuery();
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::ALLOWED, 1);

            return $this->select($query);
        }

        protected function _clonePermissions($cloned_id, $new_id, $mode)
        {
            $query = $this->getQuery();
            switch ($mode) {
                case 'group':
                    $mode = self::GROUP_ID;
                    break;
                case 'team':
                    $mode = self::TEAM_ID;
                    break;
                case 'client':
                    $mode = self::CLIENT_ID;
                    break;
            }
            $query->where($mode, $cloned_id);
            $permissions_to_add = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $permissions_to_add[] = ['target_id' => $row->get(self::TARGET_ID), 'permission_type' => $row->get(self::PERMISSION_TYPE), 'allowed' => $row->get(self::ALLOWED), 'module' => $row->get(self::MODULE)];
                }
            }

            foreach ($permissions_to_add as $permission) {
                $insertion = new Insertion();
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $insertion->add(self::PERMISSION_TYPE, $permission['permission_type']);
                $insertion->add(self::TARGET_ID, $permission['target_id']);
                $insertion->add($mode, $new_id);
                $insertion->add(self::ALLOWED, $permission['allowed']);
                $insertion->add(self::MODULE, $permission['module']);
                $res = $this->rawInsert($insertion);
            }
        }

        public function cloneGroupPermissions($cloned_group_id, $new_group_id)
        {
            return $this->_clonePermissions($cloned_group_id, $new_group_id, 'group');
        }

        public function cloneTeamPermissions($cloned_team_id, $new_team_id)
        {
            return $this->_clonePermissions($cloned_team_id, $new_team_id, 'team');
        }

        public function cloneClientPermissions($cloned_client_id, $new_client_id)
        {
            return $this->_clonePermissions($cloned_client_id, $new_client_id, 'client');
        }

        /**
         * Adds permission for the specified role to permission table based on
         * user and group role memberships in projects.
         *
         * @param role Role to which permission should be granted.
         * @param rolepermission Role permission to grant.
         */
        public function addRolePermission(Role $role, RolePermission $rolepermission)
        {
            // NOTE: When updating this method, make sure to update both user
            // and team-specific code. They are reperatitive, but kept separate
            // for clarity.

            // Retrieve user assignments based on role.
            $assigned_users = ProjectAssignedUsers::getTable()->getAssignmentsByRoleID($role->getID());

            // Iterate over assignments.
            foreach ($assigned_users as $assigned_user) {
                // Extract project entity.
                $project_id = $assigned_user->get(ProjectAssignedUsers::PROJECT_ID);
                $projects = Project::getAllByIDs([$project_id]);
                if (!isset($projects[$project_id])) {
                    continue;
                }
                $project = $projects[$project_id];

                // Determine values that need to be inserted.
                $target_id = $rolepermission->getExpandedTargetIDForProject($project);
                $user_id = $assigned_user->get(ProjectAssignedUsers::USER_ID);
                $module = $rolepermission->getModule();
                $role_id = $role->getID();
                $permission_type = $rolepermission->getPermission();
                $scope_id = framework\Context::getScope()->getID();

                // Determine if permission already exists.
                $query = $this->getQuery();
                $query->where(self::SCOPE, $scope_id);
                $query->where(self::PERMISSION_TYPE, $permission_type);
                $query->where(self::TARGET_ID, $target_id);
                $query->where(self::USER_ID, $user_id);
                $query->where(self::ALLOWED, true);
                $query->where(self::MODULE, $module);
                $query->where(self::ROLE_ID, $role_id);
                $res = $this->rawSelect($query, 'none');

                // If permission does not exist, add it.
                if (!$res) {
                    $insertion = new Insertion();
                    $insertion->add(self::SCOPE, $scope_id);
                    $insertion->add(self::PERMISSION_TYPE, $permission_type);
                    $insertion->add(self::TARGET_ID, $target_id);
                    $insertion->add(self::USER_ID, $user_id);
                    $insertion->add(self::ALLOWED, true);
                    $insertion->add(self::MODULE, $module);
                    $insertion->add(self::ROLE_ID, $role_id);
                    $this->rawInsert($insertion);
                }
            }

            // Retrieve team assignments based on role.
            $assigned_teams = ProjectAssignedTeams::getTable()->getAssignmentsByRoleID($role->getID());

            // Iterate over assignments.
            foreach ($assigned_teams as $assigned_team) {
                // Extract project entity.
                $project_id = $assigned_team->get(ProjectAssignedTeams::PROJECT_ID);
                $project = Project::getAllByIDs([$project_id])[$project_id];

                // Determine values that need to be inserted.
                $target_id = $rolepermission->getExpandedTargetIDForProject($project);
                $team_id = $assigned_team->get(ProjectAssignedTeams::TEAM_ID);
                $module = $rolepermission->getModule();
                $role_id = $role->getID();
                $permission_type = $rolepermission->getPermission();
                $scope_id = framework\Context::getScope()->getID();

                // Determine if permission already exists.
                $query = $this->getQuery();
                $query->where(self::SCOPE, $scope_id);
                $query->where(self::PERMISSION_TYPE, $permission_type);
                $query->where(self::TARGET_ID, $target_id);
                $query->where(self::TEAM_ID, $team_id);
                $query->where(self::ALLOWED, true);
                $query->where(self::MODULE, $module);
                $query->where(self::ROLE_ID, $role_id);
                $res = $this->rawSelect($query, 'none');

                // If permission does not exist, add it.
                if (!$res) {
                    $insertion = new Insertion();
                    $insertion->add(self::SCOPE, $scope_id);
                    $insertion->add(self::PERMISSION_TYPE, $permission_type);
                    $insertion->add(self::TARGET_ID, $target_id);
                    $insertion->add(self::TEAM_ID, $team_id);
                    $insertion->add(self::ALLOWED, true);
                    $insertion->add(self::MODULE, $module);
                    $insertion->add(self::ROLE_ID, $role_id);
                    $this->rawInsert($insertion);
                }
            }
        }

        public function getByPermissionTargetIDAndModule($permission, $target_id, $module = 'core')
        {
            $query = $this->getQuery();
            $query->where(self::PERMISSION_TYPE, $permission);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::MODULE, $module);

            $permissions = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $target = null;
                    if ($uid = $row->get(self::USER_ID)) {
                        $target = User::getB2DBTable()->selectById($uid);
                    }
                    if ($tid = $row->get(self::TEAM_ID)) {
                        $target = Team::getB2DBTable()->selectById($tid);
                    }
                    if ($gid = $row->get(self::GROUP_ID)) {
                        $target = Group::getB2DBTable()->selectById($gid);
                    }
                    if ($target instanceof Identifiable) {
                        $permissions[] = ['target' => $target, 'allowed' => (boolean)$row->get(self::ALLOWED), 'user_id' => $row->get(self::USER_ID), 'team_id' => $row->get(self::TEAM_ID), 'group_id' => $row->get(self::GROUP_ID)];
                    }
                }
            }

            return $permissions;
        }

        public function deleteByPermissionTargetIDAndModule($permission, $target_id, $module = 'core')
        {
            $query = $this->getQuery();
            $query->where(self::PERMISSION_TYPE, $permission);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::MODULE, $module);
            $this->rawDelete($query);
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope', [self::SCOPE]);
        }

    }
