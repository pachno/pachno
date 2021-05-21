<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Permission class
     *
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Permissions")
     */
    class Permission extends IdentifiableScoped
    {

        public const PERMISSION_ACCESS_GROUP_ISSUES = 'canseegroupissues';
        public const PERMISSION_ACCESS_CONFIGURATION = 'canviewconfig';
        public const PERMISSION_SAVE_CONFIGURATION = 'cansaveconfig';

        public const PERMISSION_CREATE_PROJECTS = 'can_create_projects';

        public const PERMISSION_MANAGE_SITE_DOCUMENTATION = 'manage_site_documentation';
        public const PERMISSION_PROJECT_EDIT_DOCUMENTATION = 'caneditdocumentation';
        public const PERMISSION_PROJECT_EDIT_DOCUMENTATION_OWN = 'caneditdocumentationown';
        public const PERMISSION_EDIT_ISSUES_COMMENTS = 'canpostandeditissuecomments';
        public const PERMISSION_EDIT_ISSUES_TRIAGE = 'caneditissuetriage';
        public const PERMISSION_EDIT_ISSUES_PEOPLE = 'caneditissuepeople';
        public const PERMISSION_EDIT_ISSUES_TRANSITION = 'cantransitionissue';
        public const PERMISSION_EDIT_ISSUES_CUSTOM_FIELDS = 'caneditissuecustomfields';
        public const PERMISSION_EDIT_ISSUES = 'caneditissue';
        public const PERMISSION_EDIT_ISSUES_ADDITIONAL = 'canaddextrainformationtoissues';
        public const PERMISSION_EDIT_ISSUES_BASIC = 'caneditissuebasic';
        public const PERMISSION_EDIT_ISSUES_MODERATE_COMMENTS = 'canpostseeandeditallissuecomments';
        public const PERMISSION_EDIT_ISSUES_DELETE = 'candeleteissues';
        public const PERMISSION_EDIT_ISSUES_TIME_TRACKING = 'caneditissuespent_time';

        public const PERMISSION_PROJECT_DEVELOPER = 'project_developer_access';
        public const PERMISSION_PROJECT_DEVELOPER_DISCUSS_CODE = 'project_developer_access_discuss_code';

        public const PERMISSION_MANAGE_PROJECT = 'canmanageproject';
        public const PERMISSION_MANAGE_PROJECT_DETAILS = 'caneditprojectdetails';
        public const PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION = 'canmoderatearticlesandcomments';
        public const PERMISSION_MANAGE_PROJECT_RELEASES = 'canmanageprojectreleases';
        public const PERMISSION_MANAGE_PROJECT_BOARDS = 'cancreatepublicboards';
        public const PERMISSION_MANAGE_PROJECT_SAVED_SEARCHES = 'cancreatepublicsavedsearches';
        public const PERMISSION_MANAGE_PROJECT_LOCK_ISSUES = 'canlockandeditlockedissues';

        public const PERMISSION_PROJECT_ACCESS = 'canseeproject';
        public const PERMISSION_PROJECT_ACCESS_ALL_ISSUES = 'canseeallissues';
        public const PERMISSION_PROJECT_ACCESS_BOARDS = 'project_board_access';
        public const PERMISSION_PROJECT_ACCESS_CODE = 'project_code_access';
        public const PERMISSION_PROJECT_ACCESS_DASHBOARD = 'project_dashboard_access';
        public const PERMISSION_PROJECT_ACCESS_DOCUMENTATION = 'project_documentation_access';
        public const PERMISSION_PROJECT_ACCESS_ISSUES = 'project_issues_access';
        public const PERMISSION_PROJECT_ACCESS_TIME_LOGGING = 'canseetimespent';
        public const PERMISSION_PROJECT_ACCESS_RELEASES = 'project_releases_access';
        public const PERMISSION_PROJECT_CREATE_ISSUES = 'cancreateissues';

        public const PERMISSION_PROJECT_INTERNAL_ACCESS = 'canseeprojectinernalresources';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_BUILDS = 'canseeallprojectbuilds';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_COMPONENTS = 'canseeallprojectcomponents';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_COMMENTS = 'canseeallprojectcomments';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_EDITIONS = 'canseeallprojecteditions';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_ISSUES = 'canaccessrestrictedissues';
        public const PERMISSION_PROJECT_INTERNAL_ACCESS_MILESTONES = 'canseeallprojectmilestones';
        public const PERMISSION_PROJECT_EDIT_DOCUMENTATION_POST_COMMENTS = 'canpostandeditarticlecomments';

        public const PERMISSION_PAGE_ACCESS_ACCOUNT = 'page_account_access';
        public const PERMISSION_PAGE_ACCESS_DASHBOARD = 'page_dashboard_access';
        public const PERMISSION_PAGE_ACCESS_DOCUMENTATION = 'page_documentation_access';
        public const PERMISSION_PAGE_ACCESS_PROJECT_LIST = 'page_project_list_access';
        public const PERMISSION_PAGE_ACCESS_SEARCH = 'page_account_search';

        public const PERMISSION_OWN_SUFFIX = 'own';

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

    }
