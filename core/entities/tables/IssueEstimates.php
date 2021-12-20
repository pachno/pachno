<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\QueryColumnSort;
    use pachno\core\framework;

    /**
     * Issue estimates table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Issue estimates table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name='issue_estimates')
     */
    class IssueEstimates extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'issue_estimates';

        public const ID = 'issue_estimates.id';

        public const SCOPE = 'issue_estimates.scope';

        public const ISSUE_ID = 'issue_estimates.issue_id';

        public const EDITED_BY = 'issue_estimates.edited_by';

        public const EDITED_AT = 'issue_estimates.edited_at';

        public const ESTIMATED_MONTHS = 'issue_estimates.estimated_months';

        public const ESTIMATED_WEEKS = 'issue_estimates.estimated_weeks';

        public const ESTIMATED_DAYS = 'issue_estimates.estimated_days';

        public const ESTIMATED_HOURS = 'issue_estimates.estimated_hours';

        public const ESTIMATED_MINUTES = 'issue_estimates.estimated_minutes';

        public const ESTIMATED_POINTS = 'issue_estimates.estimated_points';

        public function saveEstimate($issue_id, $months, $weeks, $days, $hours, $minutes, $points)
        {
            $insertion = new Insertion();
            $insertion->add(self::ESTIMATED_MONTHS, $months);
            $insertion->add(self::ESTIMATED_WEEKS, $weeks);
            $insertion->add(self::ESTIMATED_DAYS, $days);
            $insertion->add(self::ESTIMATED_HOURS, $hours);
            $insertion->add(self::ESTIMATED_MINUTES, $minutes);
            $insertion->add(self::ESTIMATED_POINTS, $points);
            $insertion->add(self::ISSUE_ID, $issue_id);
            $insertion->add(self::EDITED_AT, NOW);
            $insertion->add(self::EDITED_BY, framework\Context::getUser()->getID());
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawInsert($insertion);
        }

        public function getEstimatesByDateAndIssueIDs($startdate, $enddate, $issue_ids)
        {
            $points_retarr = [];
            $points_issues_retarr = [];
            $hours_retarr = [];
            $hours_issues_retarr = [];
            $minutes_retarr = [];
            $minutes_issues_retarr = [];
            if ($startdate && $enddate) {
                $sd = $startdate;
                while ($sd <= $enddate) {
                    $points_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = [];
                    $hours_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = [];
                    $minutes_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = [];
                    $sd += 86400;
                }
            }

            if (count($issue_ids)) {
                $query = $this->getQuery();
                if ($startdate && $enddate) {
                    $query->where(self::EDITED_AT, $enddate, Criterion::LESS_THAN_EQUAL);
                }

                $query->where(self::ISSUE_ID, $issue_ids, Criterion::IN);
                $query->addOrderBy(self::EDITED_AT, QueryColumnSort::SORT_ASC);

                if ($res = $this->rawSelect($query)) {
                    while ($row = $res->getNextRow()) {
                        if ($startdate && $enddate) {
                            $points_issues_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_POINTS);
                            $hours_issues_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_HOURS);
                            $minutes_issues_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_MINUTES);
                        } else {
                            $hours_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_HOURS);
                            $points_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_POINTS);
                            $minutes_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_MINUTES);
                        }
                    }
                }
            }

            if ($startdate && $enddate) {
                foreach ($points_retarr as $key => $vals)
                    $points_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;
                reset($points_retarr);
                $points_retarr[key($points_retarr)] = array_sum($points_issues_retarr);

                foreach ($hours_retarr as $key => $vals)
                    $hours_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;
                reset($hours_retarr);
                $hours_retarr[key($hours_retarr)] = array_sum($hours_issues_retarr);

                foreach ($minutes_retarr as $key => $vals)
                    $minutes_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;
                reset($minutes_retarr);
                $minutes_retarr[key($minutes_retarr)] = array_sum($minutes_issues_retarr);
            }

            return ['points' => $points_retarr, 'hours' => $hours_retarr, 'minutes' => $minutes_retarr];
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::EDITED_BY, Users::getTable(), Users::ID);
            parent::addInteger(self::EDITED_AT, 10);
            parent::addInteger(self::ESTIMATED_MONTHS, 10);
            parent::addInteger(self::ESTIMATED_WEEKS, 10);
            parent::addInteger(self::ESTIMATED_DAYS, 10);
            parent::addInteger(self::ESTIMATED_HOURS, 10);
            parent::addInteger(self::ESTIMATED_MINUTES, 10);
            parent::addFloat(self::ESTIMATED_POINTS);
        }

    }
