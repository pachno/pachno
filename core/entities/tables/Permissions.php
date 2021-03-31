<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Insertion;
    use pachno\core\entities\common\Identifiable;
    use pachno\core\entities\Group;
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
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="permissions")
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

        const ALLOWED = 'permissions.allowed';

        const MODULE = 'permissions.module';

        const ROLE_ID = 'permissions.role_id';

        const PERMISSION_ACCESS_GROUP_ISSUES = 'canseegroupissues';

        const PERMISSION_ACCESS_CONFIGURATION = 'canviewconfig';

        const PERMISSION_SAVE_CONFIGURATION = 'cansaveconfig';

        const PERMISSION_PAGE_ACCESS_PROJECT_LIST = 'page_project_list_access';

        const PERMISSION_PAGE_ACCESS_DASHBOARD = 'page_dashboard_access';

        const PERMISSION_PAGE_ACCESS_ACCOUNT = 'page_account_access';

        const PERMISSION_PAGE_ACCESS_SEARCH = 'page_account_search';

        const PERMISSION_PROJECT_ACCESS = 'canseeproject';

        const PERMISSION_PROJECT_INTERNAL_ACCESS = 'canseeprojectinernalresources';

        const PERMISSION_PROJECT_INTERNAL_ACCESS_EDITIONS = 'canseeallprojecteditions';

        const PERMISSION_PROJECT_INTERNAL_ACCESS_COMPONENTS = 'canseeallprojectcomponents';

        const PERMISSION_PROJECT_INTERNAL_ACCESS_BUILDS = 'canseeallprojectbuilds';

        const PERMISSION_PROJECT_INTERNAL_ACCESS_MILESTONES = 'canseeallprojectmilestones';

        const PERMISSION_PROJECT_INTERNAL_ACCESS_COMMENTS = 'canseeallprojectcomments';

        const PERMISSION_PROJECT_ACCESS_TIME_LOGGING = 'canseetimespent';

        const PERMISSION_PROJECT_ACCESS_ALL_ISSUES = 'canseeallissues';

        const PERMISSION_PROJECT_ACCESS_BOARDS = 'project_board_access';

        const PERMISSION_PROJECT_ACCESS_CODE = 'project_code_access';

        const PERMISSION_PROJECT_ACCESS_DASHBOARD = 'project_dashboard_access';

        const PERMISSION_PROJECT_ACCESS_DOCUMENTATION = 'project_documentation_access';

        const PERMISSION_PROJECT_ACCESS_ISSUES = 'project_issues_access';

        const PERMISSION_PROJECT_ACCESS_RELEASES = 'project_releases_access';

        const PERMISSION_PROJECT_CREATE_ISSUES = 'cancreateissues';

        const PERMISSION_EDIT_DOCUMENTATION = 'caneditdocumentation';

        const PERMISSION_EDIT_DOCUMENTATION_OWN = 'caneditdocumentationown';

        const PERMISSION_EDIT_DOCUMENTATION_POST_COMMENTS = 'canpostandeditarticlecomments';

        const PERMISSION_MANAGE_PROJECT = 'canmanageproject';

        const PERMISSION_MANAGE_PROJECT_DETAILS = 'caneditprojectdetails';

        const PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION = 'canmoderatearticlesandcomments';

        const PERMISSION_MANAGE_PROJECT_RELEASES = 'canmanageprojectreleases';

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

        public function removeGroupPermission($group_id, $permission_type, $module)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, 0);
            $query->where(self::TEAM_ID, 0);
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::MODULE, $module);
            $query->where(self::PERMISSION_TYPE, $permission_type);
            $query->where(self::TARGET_ID, 0);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawDelete($query);
        }

        public function deleteAllPermissionsForCombination($user_id, $group_id, $team_id, $target_id = 0, $role_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::TEAM_ID, $team_id);
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

        public function loadFixtures(Scope $scope, Group $user_group, Group $admin_group, Group $guest_group)
        {
            $scope_id = $scope->getID();

            // Common pages, everyone.
            foreach ([$user_group, $admin_group, $guest_group] as $group) {
                $group->addPermission(self::PERMISSION_PAGE_ACCESS_PROJECT_LIST, 'core', $scope_id);
            }

            foreach ([$user_group, $admin_group] as $group) {
                $group->addPermission(self::PERMISSION_PAGE_ACCESS_ACCOUNT, 'core', $scope_id);
                $group->addPermission(self::PERMISSION_PAGE_ACCESS_DASHBOARD, 'core', $scope_id);
            }

            foreach (Project::getDefaultPermissions() as $permission) {
                $admin_group->addPermission($permission, 'core', $scope_id);
            }
            $admin_group->addPermission('caneditissue', 'core', $scope_id);
        }

        public function setPermission($user_id, $group_id, $team_id, $module, $permission_type, $target_id, $scope = null, $role_id = null)
        {
            if ($scope === null) {
                $scope = framework\Context::getScope()->getID();
            }

            $insertion = new Insertion();
            $insertion->add(self::USER_ID, (int)$user_id);
            $insertion->add(self::GROUP_ID, (int)$group_id);
            $insertion->add(self::TEAM_ID, (int)$team_id);
            $insertion->add(self::ALLOWED, true);
            $insertion->add(self::MODULE, $module);
            $insertion->add(self::PERMISSION_TYPE, $permission_type);
            $insertion->add(self::TARGET_ID, $target_id);
            $insertion->add(self::SCOPE, $scope);
            if ($role_id !== null) {
                $insertion->add(self::ROLE_ID, $role_id);
            }

            $res = $this->rawInsert($insertion);

            return $res->getInsertID();
        }

        public function getPermissionsByGroupId($group_id)
        {
            $query = $this->getQuery();
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::ALLOWED, 1);
            $permissions = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $permissions[] = ['permission' => $row->get(self::PERMISSION_TYPE), 'allowed' => $row->get(self::ALLOWED), 'module' => $row->get(self::MODULE)];
                }
            }

            return $permissions;
        }

        public function cloneGroupPermissions($cloned_group_id, $new_group_id)
        {
            return $this->_clonePermissions($cloned_group_id, $new_group_id, 'group');
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

        public function cloneTeamPermissions($cloned_group_id, $new_group_id)
        {
            return $this->_clonePermissions($cloned_group_id, $new_group_id, 'group');
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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::PERMISSION_TYPE, 100);
            parent::addVarchar(self::TARGET_ID, 200, 0);
            parent::addBoolean(self::ALLOWED);
            parent::addVarchar(self::MODULE, 50);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable());
            parent::addForeignKeyColumn(self::GROUP_ID, Groups::getTable());
            parent::addForeignKeyColumn(self::TEAM_ID, Teams::getTable());
            parent::addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope', [self::SCOPE]);
        }

    }
