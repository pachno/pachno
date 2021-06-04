<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\common\Timeable;

    /**
     * Log item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Log item class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\IssueSpentTimes")
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
         * @Column(type="integer", length=10)
         */
        protected $_started_at;

        /**
         * @Column(type="boolean", default=false)
         */
        protected $_completed = false;

        /**
         * @Column(type="boolean", default=false)
         */
        protected $_paused = false;

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

        public static function getSpentHoursValue($hours)
        {
            $hours = trim(str_replace([','], ['.'], $hours));
            $hours *= 100;

            return $hours;
        }

        public function getUser()
        {
            return $this->_b2dbLazyLoad('_edited_by');
        }

        public function getActivityTypeID()
        {
            return ($this->getActivityType() instanceof ActivityType) ? $this->getActivityType()->getID() : 0;
        }

        public function getActivityType()
        {
            return $this->_b2dbLazyLoad('_activity_type');
        }

        public function setActivityType($activity_type)
        {
            $this->_activity_type = $activity_type;
        }

        public function getEditedAt()
        {
            return $this->_edited_at;
        }

        public function setEditedAt($time)
        {
            $this->_edited_at = $time;
        }

        /**
         * Returns the spent months
         *
         * @return integer
         */
        public function getSpentMonths()
        {
            return (int)$this->_spent_months;
        }

        /**
         * Set spent months
         *
         * @param integer $months The number of months spent
         */
        public function setSpentMonths($months)
        {
            $this->_spent_months = $months;
        }

        /**
         * Returns the spent weeks
         *
         * @return integer
         */
        public function getSpentWeeks()
        {
            return (int)$this->_spent_weeks;
        }

        /**
         * Set spent weeks
         *
         * @param integer $weeks The number of weeks spent
         */
        public function setSpentWeeks($weeks)
        {
            $this->_spent_weeks = $weeks;
        }

        /**
         * Returns the spent days
         *
         * @return integer
         */
        public function getSpentDays()
        {
            return (int)$this->_spent_days;
        }

        /**
         * Set spent days
         *
         * @param integer $days The number of days spent
         */
        public function setSpentDays($days)
        {
            $this->_spent_days = $days;
        }

        /**
         * Returns the spent hours
         *
         * @return integer
         */
        public function getSpentHours()
        {
            return (int)$this->_spent_hours;
        }

        /**
         * Set spent hours
         *
         * @param integer $hours The number of hours spent
         */
        public function setSpentHours($hours)
        {
            $this->_spent_hours = $hours;
        }

        /**
         * Returns the spent minutes
         *
         * @return integer
         */
        public function getSpentMinutes()
        {
            return (int)$this->_spent_minutes;
        }

        /**
         * Set spent minutes
         *
         * @param integer $minutes The number of minutes spent
         */
        public function setSpentMinutes($minutes)
        {
            $this->_spent_minutes = $minutes;
        }

        /**
         * Returns the spent points
         *
         * @return integer
         */
        public function getSpentPoints()
        {
            return (int)$this->_spent_points;
        }

        /**
         * Set spent points
         *
         * @param integer $points The number of points spent
         */
        public function setSpentPoints($points)
        {
            $this->_spent_points = $points;
        }

        /**
         * Returns an array with the spent time
         *
         * @return array
         * @see getSpentTime()
         *
         */
        public function getTimeSpent()
        {
            return $this->getSpentTime();
        }

        /**
         * Returns an array with the spent time
         *
         * @return array
         */
        public function getSpentTime()
        {
            return ['months' => (int)$this->_spent_months, 'weeks' => (int)$this->_spent_weeks, 'days' => (int)$this->_spent_days, 'hours' => round($this->_spent_hours / 100, 2), 'minutes' => (int)$this->_spent_minutes, 'points' => (int)$this->_spent_points];
        }

        public function getComment()
        {
            return $this->_comment;
        }

        public function setComment($comment)
        {
            $this->_comment = $comment;
        }

        public function editOrAdd(Issue $issue, User $user, $data = [])
        {
            if (!$this->getID()) {
                if ($data['timespent_manual']) {
                    $times = Issue::convertFancyStringToTime($data['timespent_manual'], $issue);
                } else {
                    $times = Timeable::getZeroedUnitsWithPoints();
                    $times[$data['timespent_specified_type']] = $data['timespent_specified_value'];
                }
                $this->setIssue($issue);
                $this->setUser($user);
            } else {
                $times = ['points' => $data['points'],
                    'minutes' => $data['minutes'],
                    'hours' => $data['hours'],
                    'days' => $data['days'],
                    'weeks' => $data['weeks'],
                    'months' => $data['months']];
                $edited_at = $data['edited_at'];
                $this->setEditedAt(mktime(0, 0, 1, $edited_at['month'], $edited_at['day'], $edited_at['year']));
            }
            $times['hours'] *= 100;
            $this->setSpentPoints($times['points']);
            $this->setSpentMinutes($times['minutes']);
            $this->setSpentHours($times['hours']);
            $this->setSpentDays($times['days']);
            $this->setSpentWeeks($times['weeks']);
            $this->setSpentMonths($times['months']);
            $this->setActivityType($data['timespent_activitytype']);
            $this->setComment($data['timespent_comment']);
            $this->save();

            $this->getIssue()->save();
        }

        public function setIssue($issue_id)
        {
            $this->_issue_id = $issue_id;
        }

        public function setUser($uid)
        {
            $this->_edited_by = $uid;
        }

        protected function _preSave(bool $is_new): void
        {
            parent::_preSave($is_new);
            if ($is_new && $this->_edited_at == 0) $this->_edited_at = time();
        }

        protected function _postSave(bool $is_new): void
        {
            $this->_recalculateIssueTimes();
        }

        protected function _recalculateIssueTimes()
        {
            $times = tables\IssueSpentTimes::getTable()->getSpentTimeSumsByIssueId($this->getIssueID());
            $this->getIssue()->setSpentPoints($times['points']);
            $this->getIssue()->setSpentMinutes($times['minutes']);
            $this->getIssue()->setSpentHours($times['hours']);
            $this->getIssue()->setSpentDays($times['days']);
            $this->getIssue()->setSpentWeeks($times['weeks']);
            $this->getIssue()->setSpentMonths($times['months']);
        }

        public function getIssueID()
        {
            return (is_object($this->_issue_id)) ? $this->_issue_id->getID() : (int)$this->_issue_id;
        }

        /**
         * @return Issue the related issue
         */
        public function getIssue()
        {
            return $this->_b2dbLazyLoad('_issue_id');
        }

        protected function _postDelete(): void
        {
            $this->_recalculateIssueTimes();
        }

        /**
         * @return mixed
         */
        public function getStartedAt()
        {
            return $this->_started_at;
        }

        /**
         * @param mixed $started_at
         */
        public function setStartedAt($started_at)
        {
            $this->_started_at = $started_at;
        }

        /**
         * @return bool
         */
        public function isCompleted(): bool
        {
            return $this->_completed;
        }

        /**
         * @param bool $completed
         */
        public function setCompleted(bool $completed)
        {
            $this->_completed = $completed;
        }

        /**
         * @return bool
         */
        public function isPaused(): bool
        {
            return $this->_paused;
        }

        /**
         * @param bool $paused
         */
        public function setPaused(bool $paused)
        {
            $this->_paused = $paused;
        }

    }
