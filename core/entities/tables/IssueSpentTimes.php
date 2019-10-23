<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Row;
    use b2db\Update;

    /**
     * Issue spent times table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Issue spent times table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issue_spenttimes")
     * @Entity(class="\pachno\core\entities\IssueSpentTime")
     */
    class IssueSpentTimes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'issue_spenttimes';

        const ID = 'issue_spenttimes.id';

        const SCOPE = 'issue_spenttimes.scope';

        const ISSUE_ID = 'issue_spenttimes.issue_id';

        const EDITED_BY = 'issue_spenttimes.edited_by';

        const EDITED_AT = 'issue_spenttimes.edited_at';

        const SPENT_MONTHS = 'issue_spenttimes.spent_months';

        const SPENT_WEEKS = 'issue_spenttimes.spent_weeks';

        const SPENT_DAYS = 'issue_spenttimes.spent_days';

        const SPENT_HOURS = 'issue_spenttimes.spent_hours';

        const SPENT_MINUTES = 'issue_spenttimes.spent_minutes';

        const SPENT_POINTS = 'issue_spenttimes.spent_points';

        const ACTIVITY_TYPE = 'issue_spenttimes.activity_type';

        public function getSpentTimesByDateAndIssueIDs($startdate, $enddate, $issue_ids)
        {
            $points_retarr = [];
            $hours_retarr = [];
            $minutes_retarr = [];
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
                $points_retarr_keys = array_keys($points_retarr);
                $hours_retarr_keys = array_keys($hours_retarr);
                $minutes_retarr_keys = array_keys($minutes_retarr);

                if ($startdate && $enddate) {
                    $query->where(self::EDITED_AT, $startdate, Criterion::GREATER_THAN_EQUAL);
                    $query->where(self::EDITED_AT, $enddate, Criterion::LESS_THAN_EQUAL);
                }

                $query->where(self::ISSUE_ID, $issue_ids, Criterion::IN);
                $query->addOrderBy(self::EDITED_AT, QueryColumnSort::SORT_ASC);

                if ($res = $this->rawSelect($query)) {
                    while ($row = $res->getNextRow()) {
                        if ($startdate && $enddate) {
                            $sd = $row->get(self::EDITED_AT);
                            $date = mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd));
                            foreach ($points_retarr_keys as $k => $key) {
                                if ($key < $date) continue;
                                if (array_key_exists($k + 1, $points_retarr_keys)) {
                                    if ($sd >= $key && $sd < $points_retarr_keys[$k + 1])
                                        $points_retarr[$key][] = $row->get(self::SPENT_POINTS);
                                } else {
                                    if ($sd >= $key)
                                        $points_retarr[$key][] = $row->get(self::SPENT_POINTS);
                                }
                            }
                            foreach ($hours_retarr_keys as $k => $key) {
                                if ($key < $date) continue;
                                if (array_key_exists($k + 1, $hours_retarr_keys)) {
                                    if ($sd >= $key && $sd < $hours_retarr_keys[$k + 1])
                                        $hours_retarr[$key][] = $row->get(self::SPENT_HOURS);
                                } else {
                                    if ($sd >= $key)
                                        $hours_retarr[$key][] = $row->get(self::SPENT_HOURS);
                                }
                            }
                            foreach ($minutes_retarr_keys as $k => $key) {
                                if ($key < $date) continue;
                                if (array_key_exists($k + 1, $minutes_retarr_keys)) {
                                    if ($sd >= $key && $sd < $minutes_retarr_keys[$k + 1])
                                        $minutes_retarr[$key][] = $row->get(self::SPENT_MINUTES);
                                } else {
                                    if ($sd >= $key)
                                        $minutes_retarr[$key][] = $row->get(self::SPENT_MINUTES);
                                }
                            }
                        } else {
                            if (!isset($hours_retarr[$row->get(self::ISSUE_ID)])) $hours_retarr[$row->get(self::ISSUE_ID)] = [];
                            if (!isset($points_retarr[$row->get(self::ISSUE_ID)])) $hours_retarr[$row->get(self::ISSUE_ID)] = [];
                            if (!isset($minutes_retarr[$row->get(self::ISSUE_ID)])) $minutes_retarr[$row->get(self::ISSUE_ID)] = [];
                            if (!isset($points_retarr[$row->get(self::ISSUE_ID)])) $minutes_retarr[$row->get(self::ISSUE_ID)] = [];
                            $hours_retarr[$row->get(self::ISSUE_ID)][] = $row->get(self::SPENT_HOURS);
                            $minutes_retarr[$row->get(self::ISSUE_ID)][] = $row->get(self::SPENT_MINUTES);
                            $points_retarr[$row->get(self::ISSUE_ID)][] = $row->get(self::SPENT_POINTS);
                        }
                    }
                }
            }

            foreach ($points_retarr as $key => $vals)
                $points_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;

            foreach ($hours_retarr as $key => $vals)
                $hours_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;

            foreach ($minutes_retarr as $key => $vals)
                $minutes_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;

            $returnarr = ['points' => $points_retarr, 'hours' => $hours_retarr, 'minutes' => $minutes_retarr];

            if ($startdate && $enddate) {
                $query2 = $this->getQuery();
                $query2->addSelectionColumn(self::SPENT_POINTS, 'spent_points', Query::DB_SUM);
                $query2->addSelectionColumn(self::SPENT_HOURS, 'spent_hours', Query::DB_SUM);
                $query2->addSelectionColumn(self::SPENT_MINUTES, 'spent_minutes', Query::DB_SUM);
                $query2->where(self::EDITED_AT, $startdate, Criterion::LESS_THAN);

                if (count($issue_ids)) $query2->where(self::ISSUE_ID, $issue_ids, Criterion::IN);

                if ($res2 = $this->rawSelectOne($query2)) {
                    $returnarr['points_spent_before'] = $res2->get('spent_points');
                    $returnarr['hours_spent_before'] = $res2->get('spent_hours');
                    $returnarr['minutes_spent_before'] = $res2->get('spent_minutes');
                }
            }

            return $returnarr;
        }

        public function getAllSpentTimesForFixing()
        {
            $query = $this->getQuery();
            $query->addOrderBy(self::ISSUE_ID, QueryColumnSort::SORT_ASC);
            $query->addOrderBy(self::ID, QueryColumnSort::SORT_ASC);

            $res = $this->rawSelect($query);
            $ret_arr = [];

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $ret_arr[$row[self::ISSUE_ID]][] = $row;
                }
            }

            return $ret_arr;
        }

        public function fixRow($row, $prev_times)
        {
            $update = new Update();
            $update->add(self::SPENT_POINTS, $row[self::SPENT_POINTS] - $prev_times['points']);
            $update->add(self::SPENT_MINUTES, $row[self::SPENT_MINUTES] - $prev_times['minutes']);
            $update->add(self::SPENT_HOURS, $row[self::SPENT_HOURS] - $prev_times['hours']);
            $update->add(self::SPENT_DAYS, $row[self::SPENT_DAYS] - $prev_times['days']);
            $update->add(self::SPENT_WEEKS, $row[self::SPENT_WEEKS] - $prev_times['weeks']);
            $update->add(self::SPENT_MONTHS, $row[self::SPENT_MONTHS] - $prev_times['months']);

            $this->rawUpdateById($update, $row[self::ID]);
        }

        public function fixHours($row)
        {
            if ($row[self::SPENT_HOURS] == 0) return;

            $update = new Update();
            $update->add(self::SPENT_HOURS, $row[self::SPENT_HOURS] * 100);
            $this->rawUpdateById($update, $row[self::ID]);
        }

        public function fixScopes()
        {
            $issue_scopes = [];
            $issue_query = Issues::getTable()->getQuery();
            $issue_query->addSelectionColumn(Issues::SCOPE);
            $issue_query->addSelectionColumn(Issues::ID);

            $issues_res = Issues::getTable()->rawSelect($issue_query);

            if (!$issues_res) {
                return;
            }

            while ($row = $issues_res->getNextRow()) {
                $issue_scopes[$row->getID()] = $row->get(Issues::SCOPE);
            }

            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID);
            $query->addSelectionColumn(self::ISSUE_ID);
            $query->where(self::SCOPE, 0);
            $res = $this->rawSelect($query);

            $fixRow = function (Row $row) use ($issue_scopes) {
                $issue_id = $row->get(self::ISSUE_ID);
                if (!isset($issue_scopes[$issue_id])) {
                    return;
                }

                $update = new Update();
                $update->add(self::SCOPE, $issue_scopes[$issue_id]);
                $this->rawUpdateById($update, $row->getID());
            };

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $fixRow($row);
                }
            }
        }

        public function getSpentTimeSumsByIssueId($issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_id);
            $query->addSelectionColumn(self::SPENT_POINTS, 'points', Query::DB_SUM);
            $query->addSelectionColumn(self::SPENT_MINUTES, 'minutes', Query::DB_SUM);
            $query->addSelectionColumn(self::SPENT_HOURS, 'hours', Query::DB_SUM);
            $query->addSelectionColumn(self::SPENT_DAYS, 'days', Query::DB_SUM);
            $query->addSelectionColumn(self::SPENT_MONTHS, 'months', Query::DB_SUM);
            $query->addSelectionColumn(self::SPENT_WEEKS, 'weeks', Query::DB_SUM);

            return $this->rawSelectOne($query);
        }

    }
