<?php

    namespace pachno\core\modules\installation\entities\upgrade_1_0_4;

    use pachno\core\entities\BoardColumn;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\File;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Issuetype;
    use pachno\core\entities\Project;
    use pachno\core\entities\SavedSearch;
    use pachno\core\entities\User;

    /**
     * Agile board class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\modules\installation\entities\upgrade_1_0_4\tables\AgileBoards")
     */
    class AgileBoard extends IdentifiableScoped
    {

        public const TYPE_GENERIC = 0;

        public const TYPE_SCRUM = 1;

        public const TYPE_KANBAN = 2;

        public const SWIMLANES_NONE = '';
        public const SWIMLANES_ISSUES = 'issues';
        public const SWIMLANES_EPICS = 'epics';
        public const SWIMLANES_GROUPING = 'grouping';
        public const SWIMLANES_EXPEDITE = 'expedite';

        public const SWIMLANE_IDENTIFIER_ISSUETYPE = 'issuetype';

        public const BACKGROUND_COLOR_DEFAULT = '#00ADC7';
        public const BACKGROUND_COLOR_ONE = '#7d7c84';
        public const BACKGROUND_COLOR_TWO = '#00aa7f';
        public const BACKGROUND_COLOR_THREE = '#d62246';
        public const BACKGROUND_COLOR_FOUR = '#4b1d3f';
        public const BACKGROUND_COLOR_FIVE = '#dbd56e';
        public const BACKGROUND_COLOR_SIX = '#88ab75';
        public const BACKGROUND_COLOR_SEVEN = '#de8f6e';

        /**
         * The name of the board
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Board description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * Whether this board is the private
         *
         * @var boolean
         * @Column(type="boolean", default=1)
         */
        protected $_is_private = true;

        /**
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * @var Issuetype
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Issuetype")
         */
        protected $_epic_issuetype_id;

        /**
         * @var Issuetype
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Issuetype")
         */
        protected $_task_issuetype_id;

        /**
         * @var SavedSearch
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\SavedSearch")
         */
        protected $_backlog_search_id;

        /**
         * @var File
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\File")
         */
        protected $_background_file_id;

        /**
         * @var string
         * @Column(type="string", length=10)
         */
        protected $_background_color = '';

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_autogenerated_search = SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS;

        /**
         * The board type
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_type = self::TYPE_GENERIC;

        /**
         * Whether to use swimlanes
         *
         * @var boolean
         * @Column(type="boolean", default=false)
         */
        protected $_use_swimlanes = false;

        protected $_swimlanes = [];

        /**
         * Swimlane type
         *
         * @var string
         * @Column(type="string", length=50, default="")
         */
        protected $_swimlane_type = self::SWIMLANES_NONE;

        /**
         * Swimlane identifier field
         *
         * @var string
         * @Column(type="string", length=50, default="")
         */
        protected $_swimlane_identifier = "";

        /**
         * Swimlane field value
         *
         * @var array
         * @Column(type="serializable", length=500)
         */
        protected $_swimlane_field_values = [];

        /**
         * Board columns
         *
         * @var BoardColumn[]
         * @Relates(class="\pachno\core\entities\BoardColumn", collection=true, foreign_column="board_id", orderby="sort_order")
         */
        protected $_board_columns = null;

        /**
         * Issue field value
         *
         * @var array
         * @Column(type="serializable", length=500)
         */
        protected $_issue_field_values = [];

    }
