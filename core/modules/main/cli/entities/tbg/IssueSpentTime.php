<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Log item class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\IssueSpentTimes")
     */
    class IssueSpentTime extends IdentifiableScoped
    {

        /**
         * The issue time is logged against
         *
         * @var Issue
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Issue")
         */
        protected $_issue_id;

        /**
         * Who logged time
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_edited_by;

        /**
         * The type of activity time is logged for
         *
         * @var ActivityType
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\ActivityType")
         */
        protected $_activity_type;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_edited_at;

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
         * @Column(type="text")
         */
        protected $_comment;

    }
