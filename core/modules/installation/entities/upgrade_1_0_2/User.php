<?php

    namespace pachno\core\modules\installation\entities\upgrade_1_0_2;

    use DateTimeZone;
    use pachno\core\entities\ApplicationPassword;
    use pachno\core\entities\common\IdentifiableEventContainer;
    use pachno\core\entities\IssueSpentTime;
    use pachno\core\entities\Notification;
    use pachno\core\entities\NotificationSetting;
    use pachno\core\entities\UserSession;
    use pachno\core\entities\Userstate;
    use pachno\core\framework;

    /**
     * User class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\modules\installation\entities\upgrade_1_0_2\tables\Users")
     */
    class User extends IdentifiableEventContainer
    {

        /**
         * Unique username (login name)
         *
         * @var string
         * @Column(type="string", length=50)
         */
        protected $_username = '';

        /**
         * Hashed password
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_password = '';

        /**
         * User real name
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_realname = '';

        /**
         * User short name (buddyname)
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_buddyname = '';

        /**
         * User email
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_email = '';

        /**
         * Is email private?
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_private_email = true;

        /**
         * Is 2FA enabled
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_2fa = false;

        /**
         * 2FA token
         *
         * @var string
         * @Column(type="string", length="200")
         */
        protected $_2fa_token = '';

        /**
         * The user state
         *
         * @var Userstate
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Userstate")
         */
        protected $_userstate = null;

        /**
         * Whether the user has a custom userstate set
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_customstate = false;

        /**
         * User homepage
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_homepage = '';

        /**
         * Users language
         *
         * @var string
         * @Column(type="string", length=20)
         */
        protected $_language = '';

        /**
         * Array of team ids where the current user is a member
         *
         * @var array
         * @Relates(class="\pachno\core\entities\Team", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\TeamMembers")
         */
        protected $teams = null;

        protected $_teams = null;

        /**
         * Array of client ids where the current user is a member
         *
         * @var array
         * @Relates(class="\pachno\core\entities\Client", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\ClientMembers")
         */
        protected $clients = null;

        /**
         * The users avatar
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_avatar = null;

        /**
         * Whether to use the users gravatar or not
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_use_gravatar = true;

        /**
         * Array of scopes this user is a member of
         *
         * @var array
         * @Relates(class="\pachno\core\entities\Scope", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\UserScopes")
         */
        protected $_scopes = null;

        /**
         * Array of issues to watch
         *
         * @var array
         * @Relates(class="\pachno\core\entities\Issue", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\UserIssues")
         */
        protected $_starredissues = null;

        /**
         * Timestamp of when the user was last seen
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_lastseen = 0;

        /**
         * The timezone this user is in
         *
         * @var DateTimeZone
         * @Column(type="string", length=100)
         */
        protected $_timezone = null;

        /**
         * This users upload quota (MB)
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_quota;

        /**
         * When this user joined
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_joined = 0;

        /**
         * Whether the user is enabled
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enabled = false;

        /**
         * Whether the user is autogenerated via openid
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_openid_locked = false;

        /**
         * Whether the user is activated
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_activated = false;

        /**
         * Whether the user is deleted
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_deleted = false;

        /**
         * The users preferred formatting syntax for issues
         *
         * @var integer
         * @Column(type="integer", length=3, default=2)
         */
        protected $_preferred_issues_syntax = framework\Settings::SYNTAX_MD;

        /**
         * The users preferred formatting syntax for articles
         *
         * @var integer
         * @Column(type="integer", length=3, default=1)
         */
        protected $_preferred_wiki_syntax = framework\Settings::SYNTAX_MW;

        /**
         * The users preferred formatting syntax for comments
         *
         * @var integer
         * @Column(type="integer", length=3, default=2)
         */
        protected $_preferred_comments_syntax = framework\Settings::SYNTAX_MD;

        /**
         * Whether the user wants to default to markdown in wiki pages
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_prefer_wiki_markdown = false;

        /**
         * List of user's notification settings
         *
         * @var NotificationSetting[]
         * @Relates(class="\pachno\core\entities\NotificationSetting", collection=true, foreign_column="user_id")
         */
        protected $_notification_settings = [];

        /**
         * List of user's notifications
         *
         * @var Notification[]
         * @Relates(class="\pachno\core\entities\Notification", collection=true, foreign_column="user_id", orderby="created_at")
         */
        protected $_notifications = null;

        /**
         * List of user's timers
         *
         * @var IssueSpentTime[]
         * @Relates(class="\pachno\core\entities\IssueSpentTime", collection=true, foreign_column="user_id", orderby="edited_at")
         */
        protected $_timers = null;

        /**
         * List of user's application-specific passwords
         *
         * @var ApplicationPassword[]
         * @Relates(class="\pachno\core\entities\ApplicationPassword", collection=true, foreign_column="user_id", orderby="created_at")
         */
        protected $_application_passwords = null;

        /**
         * List of user's session tokens
         *
         * @var UserSession[]
         * @Relates(class="\pachno\core\entities\UserSession", collection=true, foreign_column="user_id", orderby="created_at")
         */
        protected $_user_sessions = null;

    }
