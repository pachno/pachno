<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\FormObject;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\common\Timeable;
    use pachno\core\framework\Context;
    use pachno\core\framework\Request;

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
    class IssueSpentTime extends IdentifiableScoped implements FormObject
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
         * @Column(type="boolean", default=true)
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

        /**
         * @return User
         */
        public function getUser(): User
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
            return ['months' => (int)$this->_spent_months, 'weeks' => (int)$this->_spent_weeks, 'days' => (int)$this->_spent_days, 'hours' => $this->_spent_hours, 'minutes' => (int)$this->_spent_minutes, 'points' => (int)$this->_spent_points];
        }

        public function getComment()
        {
            return $this->_comment;
        }

        public function setComment($comment)
        {
            $this->_comment = $comment;
        }

        public function isMultiTime(): bool
        {
            $items = 0;
            if ($this->_spent_minutes) $items++;
            if ($this->_spent_hours) $items++;
            if ($this->_spent_days) $items++;
            if ($this->_spent_weeks) $items++;
            if ($this->_spent_points) $items++;

            return ($items > 1);
        }

        public function isAutomatic()
        {
            return $this->isPaused() || !$this->isCompleted();
        }

        public function updateFromRequest(Request $request)
        {
            if ($request['timespent_specified_value'] || $request['timespent_manual']) {
                if ($request['timespent_manual']) {
                    $times = Issue::convertFancyStringToTime($request['timespent_manual'], $this->getIssue());
                } else {
                    $times = Timeable::getZeroedUnitsWithPoints();
                    $times[$request['timespent_specified_type']] = $request['timespent_specified_value'];
                }
                $times['hours'] *= 100;
                $this->setSpentPoints($times['points']);
                $this->setSpentMinutes($times['minutes']);
                $this->setSpentHours($times['hours']);
                $this->setSpentDays($times['days']);
                $this->setSpentWeeks($times['weeks']);
                $this->setSpentMonths($times['months']);
                $this->setActivityType($request['timespent_activitytype']);
            }

            if ($request['timespent_comment']) {
                $this->setComment($request['timespent_comment']);
            }

            if ($request['is_paused'] || $request['is_completed']) {
                if (!$this->isPaused()) {
                    $time_spent = NOW - $this->getEditedAt();
                    $days_spent = floor($time_spent / 86400);
                    $hours_spent = floor(($time_spent - ($days_spent * 86400)) / 3600);
                    $minutes_spent = floor(($time_spent - ($days_spent * 86400) - ($hours_spent * 3600)) / 60);

                    $this->_spent_minutes += (int) $minutes_spent;
                    $this->_spent_hours += (int) $hours_spent;
                    $this->_spent_days += (int) $days_spent;
                }
            }

            $this->setEditedAt(time());
            $this->setCompleted($request->getParameter('is_completed', true));
            $this->setPaused($request->getParameter('is_paused', false));
        }

        public function saveFromRequest(Request $request)
        {
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
            if ($is_new) {
                $this->_edited_at = time();
                $this->_started_at = time();
            }
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

        public function getPrintableValue()
        {
            return array_sum($this->getSpentTime());
        }

        public function getSelectedTimeEntry(): string
        {
            if ($this->_spent_days) {
                return 'days';
            }

            if ($this->_spent_minutes) {
                return 'minutes';
            }

            if ($this->_spent_points) {
                return 'points';
            }

            if ($this->_spent_weeks) {
                return 'weeks';
            }

            if ($this->_spent_months) {
                return 'months';
            }

            return 'hours';
        }

        public function getElapsedTime(): int
        {
            $time = 0;
            $time += $this->getSpentMinutes() * 60;
            $time += $this->getSpentHours() * 60 * 60;
            $time += $this->getSpentDays() * 24 * 60 * 60;

            return $time;
        }

        public function getFormattedElapsedTimeTotal()
        {
            if ($this->isPaused()) {
                $days_spent = $this->_spent_days;
                $hours_spent = $this->_spent_hours;
                $minutes_spent = $this->_spent_minutes;
            } else {
                $time_start = $this->getEditedAt() - $this->getElapsedTime();
                $diff = NOW - $time_start;
                $days_spent = floor($diff / 86400);
                $hours_spent = floor(($diff - ($days_spent * 86400)) / 3600);
                $minutes_spent = floor(($diff - ($days_spent * 86400) - ($hours_spent * 3600)) / 60);
            }

            $time_string = str_pad($hours_spent, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes_spent, 2, '0', STR_PAD_LEFT);
            if ($days_spent > 0) {
                $time_string = str_pad($days_spent, 2, '0', STR_PAD_LEFT) . ':' . $time_string;
            }

            return $time_string;
        }

        public function toJSON($detailed = true)
        {
            $json = parent::toJSON($detailed);
            $json['started_at'] = $this->getStartedAt();
            $json['edited_at'] = $this->getEditedAt();
            $json['elapsed_time'] = [
                'time' => $this->getElapsedTime(),
                'days' => $this->getSpentDays(),
                'hours' => $this->getSpentHours(),
                'minutes' => $this->getSpentMinutes()
            ];
            $json['is_completed'] = $this->isCompleted();
            $json['is_paused'] = $this->isPaused();
            $json['user'] = $this->getUser()->toJSON();
            $json['url'] = Context::getRouting()->generate('issue_edittimespent', ['project_key' => $this->getIssue()->getProject()->getKey(), 'issue_id' => $this->getIssue()->getID(), 'entry_id' => $this->getId()]);

            return $json;
        }

    }
