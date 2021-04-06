<?php

    namespace pachno\core\entities;

    /**
     * Permission class
     *
     * @Table(name="\pachno\core\entities\tables\Permissions")
     */
    class Permission extends common\IdentifiableScoped
    {

        public const PERMISSION_PROJECT_INTERNAL_ACCESS_BUILDS = 'canseeallprojectbuilds';
        public const PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION = 'canmoderatearticlesandcomments';
        public const PERMISSION_EDIT_DOCUMENTATION = 'caneditdocumentation';
        public const PERMISSION_EDIT_ISSUES_TRIAGE = 'caneditissuetriage';
        public const PERMISSION_EDIT_ISSUES_COMMENTS = 'canpostandeditissuecomments';
        public const PERMISSION_PROJECT_ACCESS_CODE = 'project_code_access';
        public const PERMISSION_PROJECT_ACCESS_DASHBOARD = 'project_dashboard_access';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_EDITIONS = 'canseeallprojecteditions';
        public const PERMISSION_PROJECT_ACCESS = 'canseeproject';
        public const PERMISSION_PROJECT_ACCESS_BOARDS = 'project_board_access';
        public const PERMISSION_MANAGE_PROJECT_DETAILS = 'caneditprojectdetails';
        public const PERMISSION_EDIT_ISSUES_PEOPLE = 'caneditissuepeople';
        public const PERMISSION_EDIT_ISSUES_TRANSITION = 'cantransitionissue';
        public const PERMISSION_PROJECT_CREATE_ISSUES = 'cancreateissues';
        public const PERMISSION_EDIT_ISSUES_CUSTOM_FIELDS = 'caneditissuecustomfields';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_MILESTONES = 'canseeallprojectmilestones';
        public const PERMISSION_EDIT_ISSUES = 'caneditissue';
        public const PERMISSION_MANAGE_PROJECT_RELEASES = 'canmanageprojectreleases';
        public const PERMISSION_PROJECT_ACCESS_DOCUMENTATION = 'project_documentation_access';
        public const PERMISSION_EDIT_ISSUES_ADDITIONAL = 'canaddextrainformationtoissues';
        public const PERMISSION_MANAGE_PROJECT = 'canmanageproject';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS = 'canseeprojectinernalresources';
        public const PERMISSION_SAVE_CONFIGURATION = 'cansaveconfig';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_COMPONENTS = 'canseeallprojectcomponents';
        public const PERMISSION_PAGE_ACCESS_SEARCH = 'page_account_search';
        public const PERMISSION_EDIT_ISSUES_BASIC = 'caneditissuebasic';
        public const PERMISSION_ACCESS_CONFIGURATION = 'canviewconfig';
        public const PERMISSION_PROJECT_ACCESS_TIME_LOGGING = 'canseetimespent';
        public const PERMISSION_PROJECT_ACCESS_ISSUES = 'project_issues_access';
        public const PERMISSION_ACCESS_GROUP_ISSUES = 'canseegroupissues';
        public const PERMISSION_EDIT_ISSUES_MODERATE_COMMENTS = 'canpostseeandeditallissuecomments';
        public const PERMISSION_PROJECT_ACCESS_RELEASES = 'project_releases_access';
        public const PERMISSION_EDIT_ISSUES_DELETE = 'candeleteissues';
        public const PERMISSION_EDIT_DOCUMENTATION_POST_COMMENTS = 'canpostandeditarticlecomments';
        public const PERMISSION_EDIT_ISSUES_TIME_TRACKING = 'caneditissuespent_time';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_COMMENTS = 'canseeallprojectcomments';
        public const PERMISSION_OWN_SUFFIX = 'own';
        public const PERMISSION_EDIT_DOCUMENTATION_OWN = 'caneditdocumentationown';
        public const PERMISSION_PAGE_ACCESS_ACCOUNT = 'page_account_access';
        public const PERMISSION_PAGE_ACCESS_DASHBOARD = 'page_dashboard_access';
        public const PERMISSION_PROJECT_ACCESS_ALL_ISSUES = 'canseeallissues';
        public const PERMISSION_PAGE_ACCESS_PROJECT_LIST = 'page_project_list_access';
        public const PERMISSION_PAGE_ACCESS_DOCUMENTATION = 'page_documentation_access';
        public const PERMISSION_MANAGE_SITE_DOCUMENTATION = 'manage_site_documentation';

        /**
         * The applicable team
         *
         * @var Team
         * @Column(type="integer", length=10, name="tid")
         * @Relates(class="\pachno\core\entities\Team")
         */
        protected $_team_id;

        /**
         * The applicable user
         *
         * @var User
         * @Column(type="integer", length=10, name="uid")
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * The applicable group
         *
         * @var Group
         * @Column(type="integer", length=10, name="gid")
         * @Relates(class="\pachno\core\entities\Group")
         */
        protected $_group_id;

        /**
         * The applicable role
         *
         * @var Role
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Role")
         */
        protected $_role_id;

        /**
         * @var string
         * @Column(type="string", length=50, name="module")
         */
        protected $_module_name;

        /**
         * @var string
         * @Column(type="string", length=100, name="permission_type")
         */
        protected $_permission_name;

        /**
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_target_id;

        /**
         * @var boolean
         * @Column(type="boolean", default=true)
         */
        protected $_allowed = true;

        public static function loadFixtures(Scope $scope, Group $user_group, Group $admin_group, Group $guest_group = null)
        {
            $scope_id = $scope->getID();

            // Common pages, everyone.
            foreach ([$user_group, $admin_group, $guest_group] as $group) {
                if (!$group instanceof Group)
                    continue;

                $group->addPermission(self::PERMISSION_PAGE_ACCESS_PROJECT_LIST, 'core', $scope_id);
                $group->addPermission(self::PERMISSION_PAGE_ACCESS_DOCUMENTATION, 'core', $scope_id);
            }

            foreach ([$user_group, $admin_group] as $group) {
                $group->addPermission(self::PERMISSION_PAGE_ACCESS_ACCOUNT, 'core', $scope_id);
                $group->addPermission(self::PERMISSION_PAGE_ACCESS_DASHBOARD, 'core', $scope_id);
                $group->addPermission(self::PERMISSION_PAGE_ACCESS_SEARCH, 'core', $scope_id);
            }

            foreach (Project::getDefaultPermissions() as $permission) {
                $admin_group->addPermission($permission, 'core', $scope_id);
            }

            $admin_group->addPermission(self::PERMISSION_SAVE_CONFIGURATION, 'core', $scope_id);
            $admin_group->addPermission(self::PERMISSION_MANAGE_SITE_DOCUMENTATION, 'core', $scope_id);
        }

        public function setUser(User $user = null)
        {
            $this->_user_id = $user;
        }

        public function setUserId(int $user_id = 0)
        {
            $this->_user_id = $user_id;
        }

        public function getUser(): ?User
        {
            return $this->_b2dbLazyload('_user_id');
        }

        public function setGroup(Group $group = null)
        {
            $this->_group_id = $group;
        }

        public function setGroupId(int $group_id = 0)
        {
            $this->_group_id = $group_id;
        }

        public function getGroup(): ?Group
        {
            return $this->_b2dbLazyload('_group_id');
        }

        public function setRole(Role $role = null)
        {
            $this->_role_id = $role;
        }

        public function setRoleId(int $role_id = 0)
        {
            $this->_role_id = $role_id;
        }

        public function getRole(): ?Role
        {
            return $this->_b2dbLazyload('_role_id');
        }

        public function setTeam(Team $team = null)
        {
            $this->_team_id = $team;
        }

        public function setTeamId(int $team_id = 0)
        {
            $this->_team_id = $team_id;
        }

        public function getTeam(): ?Team
        {
            return $this->_b2dbLazyload('_team_id');
        }

        public function setTargetId($target_id = 0)
        {
            $this->_target_id = $target_id;
        }

        public function getTargetId()
        {
            return $this->_target_id;
        }

        /**
         * @return string
         */
        public function getModuleName(): string
        {
            return $this->_module_name;
        }

        /**
         * @param string $module_name
         */
        public function setModuleName(string $module_name)
        {
            $this->_module_name = $module_name;
        }

        /**
         * @return string
         */
        public function getPermissionName(): string
        {
            return $this->_permission_name;
        }

        /**
         * @param string $permission_type
         */
        public function setPermissionName(string $permission_type)
        {
            $this->_permission_name = $permission_type;
        }

        /**
         * @return bool
         */
        public function isAllowed(): bool
        {
            return $this->_allowed;
        }

        /**
         * @param bool $allowed
         */
        public function setAllowed(bool $allowed)
        {
            $this->_allowed = $allowed;
        }

    }
