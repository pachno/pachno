<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\QaLeadable;
    use pachno\core\entities\User;
    use pachno\core\helpers\MentionableProvider;

    /**
     * Project class
     *
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Projects")
     */
    class Project extends QaLeadable implements MentionableProvider
    {
        /**
         * New issues lock type project and category access
         *
         * @static integer
         */
        public const ISSUES_LOCK_TYPE_PUBLIC_CATEGORY = 0;

        /**
         * New issues lock type project access
         *
         * @static integer
         */
        public const ISSUES_LOCK_TYPE_PUBLIC = 1;

        /**
         * New issues lock type restricted access to poster
         *
         * @static integer
         */
        public const ISSUES_LOCK_TYPE_RESTRICTED = 2;

        public const SUMMARY_TYPE_MILESTONES = 'milestones';

        public const SUMMARY_TYPE_ISSUELIST = 'issuelist';

        public const SUMMARY_TYPE_ISSUETYPES = 'issuetypes';

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The project prefix
         *
         * @var string
         * @Column(type="string", length=25)
         */
        protected $_prefix = '';

        /**
         * Whether or not the project uses prefix
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_use_prefix = false;

        /**
         * Whether the item is locked or not
         *
         * @var boolean
         * @access protected
         * @Column(type="boolean")
         */
        protected $_locked = null;

        /**
         * New issues lock type
         *
         * @var integer
         * @access protected
         * @Column(type="integer", length=10)
         */
        protected $_issues_lock_type = null;

        /**
         * Whether or not the project uses builds
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_builds = true;

        /**
         * Whether or not the project uses editions
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_editions = null;

        /**
         * Whether or not the project uses components
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_components = null;

        /**
         * Project key
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_key = null;

        /**
         * List of editions for this project
         *
         * @Relates(class="\pachno\core\entities\Edition", collection=true, foreign_column="project")
         */
        protected $_editions = null;

        /**
         * The projects homepage
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_homepage = '';

        /**
         * List of milestones for this project
         *
         * @Relates(class="\pachno\core\entities\Milestone", collection=true, foreign_column="project", orderby="sort_order")
         */
        protected $_milestones = null;

        /**
         * List of components for this project
         *
         * @Relates(class="\pachno\core\entities\Component", collection=true, foreign_column="project", orderby="name")
         */
        protected $_components = null;

        /**
         * Count of issues registered for this project
         *
         * @var integer[][]
         */
        protected $_issuecounts = null;

        /**
         * The large project icon, if set
         *
         * @var File
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\File")
         */
        protected $_large_icon = null;

        /**
         * The projects documentation URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_doc_url = '';

        /**
         * The projects wiki URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_wiki_url = '';

        /**
         * The project description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description = '';

        /**
         * @Relates(class="\pachno\core\entities\User", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\ProjectAssignedUsers")
         */
        protected $_assigned_users;

        /**
         * @Relates(class="\pachno\core\entities\Team", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\ProjectAssignedTeams")
         */
        protected $_assigned_teams;

        /**
         * Whether a user can change details about an issue without working on the issue
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_allow_freelancing = false;

        /**
         * Is project deleted
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_deleted = 0;

        /**
         * The selected workflow scheme
         *
         * @var WorkflowScheme
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowScheme")
         */
        protected $_workflow_scheme_id = 1;

        /**
         * The selected workflow scheme
         *
         * @var IssuetypeScheme
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\IssuetypeScheme")
         */
        protected $_issuetype_scheme_id = 1;

        /**
         * Assigned client
         *
         * @var Client
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Client")
         */
        protected $_client = null;

        /**
         * Parent project
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_parent = null;

        /**
         * Whether to show a "Download" link and corresponding section
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_has_downloads = true;

        /**
         * Whether a project is archived (read-only mode)
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_archived = false;

        /**
         * List of project's dashboards
         *
         * @var Dashboard[]
         * @Relates(class="\pachno\core\entities\Dashboard", collection=true, foreign_column="project_id", orderby="name")
         */
        protected $_dashboards = null;

        public function getMentionableUsers()
        {
            // TODO: Implement getMentionableUsers() method.
        }

    }
