<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\Ownable;
    use pachno\core\entities\File;
    use pachno\core\entities\traits\Commentable;
    use pachno\core\entities\User;
    use \pachno\core\framework;
    use pachno\core\helpers\Attachable;
    use pachno\core\helpers\MentionableProvider;

    /**
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Issues")
     */
    class Issue extends Ownable implements MentionableProvider, Attachable
    {

        use Commentable;

        /**
         * Open issue state
         *
         * @static integer
         */
        public const STATE_OPEN = 0;

        /**
         * Closed issue state
         *
         * @static integer
         */
        public const STATE_CLOSED = 1;

        /**
         * @Column(type="string", name="name", length=255)
         */
        protected $_title;

        /**
         * @Column(type="string", name="shortname", length=255)
         */
        protected $_shortname;

        /**
         * The issue number
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_issue_no;

        /**
         * The issue type
         *
         * @var Issuetype
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Issuetype")
         */
        protected $_issuetype;

        /**
         * The project which this issue affects
         *
         * @var Project
         * @access protected
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * This issues long description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description;

        /**
         * The syntax used for this issue's long description
         *
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_description_syntax;

        /**
         * This issues reproduction steps
         *
         * @var string
         * @Column(type="text")
         */
        protected $_reproduction_steps;

        /**
         * The syntax used for this issue's reproduction steps
         *
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_reproduction_steps_syntax;

        /**
         * When the issue was posted
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_posted;

        /**
         * When the issue was last updated
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_last_updated;

        /**
         * Who posted the issue
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_posted_by;

        /**
         * The project assignee if team
         *
         * @var Team
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Team")
         */
        protected $_assignee_team;

        /**
         * The project assignee if user
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_assignee_user;

        /**
         * What kind of bug this is
         *
         * @var integer
         * @Column(type="integer", length=3)
         */
        protected $_pain_bug_type;

        /**
         * What effect this bug has on users
         *
         * @var integer
         * @Column(type="integer", length=3)
         */
        protected $_pain_effect;

        /**
         * How likely users are to experience this bug
         *
         * @var integer
         * @Column(type="integer", length=3)
         */
        protected $_pain_likelihood;

        /**
         * Calculated user pain score
         *
         * @var float
         * @Column(type="float")
         */
        protected $_user_pain = 0.00;

        /**
         * The resolution
         *
         * @var Resolution
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Resolution")
         */
        protected $_resolution;

        /**
         * The issues' state (open or closed)
         *
         * @var integer
         * @Column(type="integer", length=2)
         */
        protected $_state = self::STATE_OPEN;

        /**
         * The category
         *
         * @var Category
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Category")
         */
        protected $_category;

        /**
         * The status
         *
         * @var Status
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Status")
         */
        protected $_status;

        /**
         * The prioroty
         *
         * @var Priority
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Priority")
         */
        protected $_priority;

        /**
         * The reproducability
         *
         * @var Reproducability
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Reproducability")
         */
        protected $_reproducability;

        /**
         * The severity
         *
         * @var Severity
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Severity")
         */
        protected $_severity;

        /**
         * The estimated time (months) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_months;

        /**
         * The estimated time (weeks) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_weeks;

        /**
         * The estimated time (days) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_days;

        /**
         * The estimated time (hours) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_hours;

        /**
         * The estimated time (minutes) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_minutes;

        /**
         * The estimated time (points) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_points;

        /**
         * The time spent (months) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_months;

        /**
         * The time spent (weeks) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_weeks;

        /**
         * The time spent (days) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_days;

        /**
         * The time spent (hours) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_hours;

        /**
         * The time spent (minutes) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_minutes;

        /**
         * The time spent (points) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_points;

        /**
         * How far along the issus is
         *
         * @var integer
         * @Column(type="integer", length=2)
         */
        protected $_percent_complete;

        /**
         * Which user is currently working on this issue
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_being_worked_on_by_user;

        /**
         * When the last user started working on the issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_being_worked_on_by_user_since;

        /**
         * Whether the issue is deleted
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_deleted = false;

        /**
         * Whether the issue is blocking the next release
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_blocking = false;

        /**
         * Sum of votes for this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_votes_total = null;

        /**
         * Milestone sorting order for this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_milestone_order = null;

        /**
         * The issue this issue is a duplicate of
         *
         * @var Issue
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Issue")
         */
        protected $_duplicate_of;

        /**
         * The milestone this issue is assigned to
         *
         * @var Milestone
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Milestone")
         */
        protected $_milestone;

        /**
         * List of issues which are duplicates of this one
         *
         * @var Issue[]
         * @Relates(class="\pachno\core\entities\Issue", collection=true, foreign_column="duplicate_of")
         */
        protected $_duplicate_issues;

        /**
         * Whether the issue is locked for changes
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_locked;

        /**
         * Whether the issue is locked for changes to category
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_locked_category;

        /**
         * The issues current step in the associated workflow
         *
         * @var WorkflowStep
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowStep")
         */
        protected $_workflow_step_id;

        /**
         * An array of \pachno\core\entities\IssueSpentTimes
         *
         * @var array
         * @Relates(class="\pachno\core\entities\IssueSpentTime", collection=true, foreign_column="issue_id")
         */
        protected $_spent_times;

        /**
         * Array of users that are subscribed to this issue
         *
         * @var array
         * @Relates(class="\pachno\core\entities\User", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\UserIssues")
         */
        protected $_subscribers = null;

        public function attachFile(File $file, $timestamp = null)
        {
        }

        public function detachFile(File $file)
        {
        }

        public function getMentionableUsers()
        {
        }


    }
