<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Join;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Resultset;
    use b2db\Table;
    use b2db\Update;
    use Exception;
    use pachno\core\entities\common\Timeable;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Issuetype;
    use pachno\core\entities\Project;
    use pachno\core\entities\SearchFilter;
    use pachno\core\framework;

    /**
     * Issues table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Issues getTable() Retrieves an instance of this table
     * @method Issue selectById(integer $id, Criteria $query = null, $join = 'all') Retrieves an issue
     *
     * @Entity(class="\pachno\core\entities\Issue")
     * @Table(name='issues')
     */
    class Issues extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 3;

        public const B2DBNAME = 'issues';

        public const ID = 'issues.id';

        public const SCOPE = 'issues.scope';

        public const ISSUE_NO = 'issues.issue_no';

        public const TITLE = 'issues.name';

        public const POSTED = 'issues.posted';

        public const LAST_UPDATED = 'issues.last_updated';

        public const PROJECT_ID = 'issues.project_id';

        public const DESCRIPTION = 'issues.description';

        public const REPRODUCTION_STEPS = 'issues.reproduction_steps';

        public const ISSUE_TYPE = 'issues.issuetype';

        public const RESOLUTION = 'issues.resolution';

        public const STATE = 'issues.state';

        public const POSTED_BY = 'issues.posted_by';

        public const OWNER_USER = 'issues.owner_user';

        public const OWNER_TEAM = 'issues.owner_team';

        public const STATUS = 'issues.status';

        public const PRIORITY = 'issues.priority';

        public const SEVERITY = 'issues.severity';

        public const CATEGORY = 'issues.category';

        public const REPRODUCABILITY = 'issues.reproducability';

        public const cover_color = 'issues.cover_color';

        public const ESTIMATED_MONTHS = 'issues.estimated_months';

        public const ESTIMATED_WEEKS = 'issues.estimated_weeks';

        public const ESTIMATED_DAYS = 'issues.estimated_days';

        public const ESTIMATED_HOURS = 'issues.estimated_hours';

        public const ESTIMATED_MINUTES = 'issues.estimated_minutes';

        public const ESTIMATED_POINTS = 'issues.estimated_points';

        public const SPENT_MONTHS = 'issues.spent_months';

        public const SPENT_WEEKS = 'issues.spent_weeks';

        public const SPENT_DAYS = 'issues.spent_days';

        public const SPENT_HOURS = 'issues.spent_hours';

        public const SPENT_MINUTES = 'issues.spent_minutes';

        public const SPENT_POINTS = 'issues.spent_points';

        public const PERCENT_COMPLETE = 'issues.percent_complete';

        public const ASSIGNEE_USER = 'issues.assignee_user';

        public const ASSIGNEE_TEAM = 'issues.assignee_team';

        public const BEING_WORKED_ON_BY_USER = 'issues.being_worked_on_by_user';

        public const BEING_WORKED_ON_BY_USER_SINCE = 'issues.being_worked_on_by_user_since';

        public const USER_PAIN = 'issues.user_pain';

        public const PAIN_BUG_TYPE = 'issues.pain_bug_type';

        public const PAIN_EFFECT = 'issues.pain_effect';

        public const PAIN_LIKELIHOOD = 'issues.pain_likelihood';

        public const DUPLICATE_OF = 'issues.duplicate_of';

        public const DELETED = 'issues.deleted';

        public const BLOCKING = 'issues.blocking';

        public const LOCKED = 'issues.locked';

        public const LOCKED_CATEGORY = 'issues.locked_category';

        public const WORKFLOW_STEP_ID = 'issues.workflow_step_id';

        public const MILESTONE = 'issues.milestone';

        public const VOTES_TOTAL = 'issues.votes_total';

        public const MILESTONE_ORDER = 'issues.milestone_order';

        /**
         * @return Issue[]
         */
        public static function getSessionIssues()
        {
            static $issues;

            if ($issues === null) {
                $issues = [];
                if (isset($_SESSION['viewissue_list']) && is_array($_SESSION['viewissue_list'])) {
                    foreach ($_SESSION['viewissue_list'] as $issue_id) {
                        try {
                            $issue = Issues::getTable()->getIssueByID($issue_id);
                            if ($issue instanceof Issue) {
                                $issues[$issue->getID()] = $issue;
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            }

            return $issues;
        }

        public function getIssueByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->selectById($id, $query);
        }

        public function getCountsByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $query2 = clone $query;

            $query->where(self::STATE, Issue::STATE_CLOSED);
            $query2->where(self::STATE, Issue::STATE_OPEN);

            return [$this->count($query), $this->count($query2)];
        }

        public function getCountsByProjectIDandIssuetype($project_id, $issuetype_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ISSUE_TYPE, $issuetype_id);

            $query2 = clone $query;

            $query->where(self::STATE, Issue::STATE_CLOSED);
            $query2->where(self::STATE, Issue::STATE_OPEN);

            return [$this->count($query), $this->count($query2)];
        }

        /**
         * @param array $allowed_status_ids
         */
        public function getMilestoneDistributionDetails($milestone_id, $allowed_status_ids = [])
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::MILESTONE, $milestone_id);
            if (count($allowed_status_ids)) {
                $query->where(self::STATUS, $allowed_status_ids, Criterion::IN);
            }
            $total = $this->count($query);

            $query = $this->getQuery();
            $query->addSelectionColumn(self::STATUS);
            $query->addSelectionColumn(self::ID, 'counts', Query::DB_COUNT);
            $query->where(self::DELETED, false);
            $query->where(self::MILESTONE, $milestone_id);
            if (count($allowed_status_ids)) {
                $query->where(self::STATUS, $allowed_status_ids, Criterion::IN);
            }
            $query->addGroupBy(self::STATUS);

            $res = $this->rawSelect($query);
            $statuses = ['total' => $total, 'details' => []];

            if ($res) {
                $multiplier = 100 / $total;
                $total_percent = 0;
                while ($row = $res->getNextRow()) {
                    $counts = $row['counts'];
                    $pct = round($counts * $multiplier, 2);
                    $total_percent += $pct;
                    if ($total_percent > 100) $pct -= $total_percent - 100;
                    $status = $row[self::STATUS];
                    $statuses['details'][$status] = ['id' => $status, 'count' => $counts, 'percent' => $pct];
                }
            }

            return $statuses;
        }

        /**
         * @param array $allowed_status_ids
         */
        public function getCountsByProjectIDandMilestone($project_id, $milestone_id, $allowed_status_ids = [])
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if (!$milestone_id) {
                $criteria = new Criteria();
                $criteria->where(self::MILESTONE, null);
                $criteria->or(self::MILESTONE, 0);
                $query->and($criteria);
            } else {
                $query->where(self::MILESTONE, $milestone_id);
            }
            if (count($allowed_status_ids)) {
                $query->where(self::STATUS, $allowed_status_ids, Criterion::IN);
            }

            $query2 = clone $query;
            $query->where(self::STATE, Issue::STATE_CLOSED);
            $query2->where(self::STATE, Issue::STATE_OPEN);
            if (count($allowed_status_ids)) {
                $query2->where(self::STATUS, $allowed_status_ids, Criterion::IN);
            }

            return [$this->count($query), $this->count($query2)];
        }

        public function getPriorityCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::PRIORITY);
        }

        protected function _getCountByProjectIDAndColumn($project_id, $column)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->addSelectionColumn(self::ID, 'column_count', Query::DB_COUNT);
            $query->addSelectionColumn($column);
            $query->where(self::PROJECT_ID, $project_id);
            $query->addGroupBy($column);

            $query2 = clone $query;

            $query->where(self::STATE, Issue::STATE_CLOSED);
            $query2->where(self::STATE, Issue::STATE_OPEN);

            $res1 = $this->rawSelect($query, 'none');
            $res2 = $this->rawSelect($query2, 'none');
            $retarr = [];

            if ($res1) {
                while ($row = $res1->getNextRow()) {
                    $col = (int)$row->get($column);
                    if (!array_key_exists($col, $retarr)) $retarr[$col] = ['open' => 0, 'closed' => 0];
                    $retarr[$col]['closed'] = $row->get('column_count');
                }
            }
            if ($res2) {
                while ($row = $res2->getNextRow()) {
                    $col = (int)$row->get($column);
                    if (!array_key_exists($col, $retarr)) $retarr[$col] = ['open' => 0, 'closed' => 0];
                    $retarr[$col]['open'] = $row->get('column_count');
                }
            }

            foreach ($retarr as $column_id => $details) {
                if (array_sum($details) > 0) {
                    $multiplier = 100 / array_sum($details);
                    $retarr[$column_id]['percentage'] = $details['open'] * $multiplier;
                } else {
                    $retarr[$column_id]['percentage'] = 0;
                }
            }

            return $retarr;
        }

        public function getSeverityCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::SEVERITY);
        }

        public function getStatusCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::STATUS);
        }

        public function getCategoryCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::CATEGORY);
        }

        public function getWorkflowStepCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::WORKFLOW_STEP_ID);
        }

        public function getResolutionCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::RESOLUTION);
        }

        public function getReproducabilityCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::REPRODUCABILITY);
        }

        public function getStateCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::STATE);
        }

        public function getOpenIssuesByProjectIDAndIssuetypeID($project_id, $issuetype_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::STATE, Issue::STATE_OPEN);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::ISSUE_TYPE, $issuetype_id);
            $issues = $this->select($query);

            foreach ($issues as $key => $issue) {
                if (!$issue->hasAccess()) unset($issues[$key]);
            }

            return $issues;
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->rawSelectById($id, $query);
        }

        public function getCountByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $project_id);
            $res = $this->count($query);

            return $res;
        }

        public function getNextIssueNumberForProductID($p_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $p_id);
            $query->addSelectionColumn(self::ISSUE_NO, 'issueno', Query::DB_MAX, '', '+1');
            $row = $this->rawSelectOne($query, 'none');
            $issue_no = $row->get('issueno');

            return ($issue_no < 1) ? 1 : $issue_no;
        }

        /**
         * @param $prefix
         * @param $issue_no
         *
         * @return Issue
         */
        public function getByPrefixAndIssueNo($prefix, $issue_no)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->join(Projects::getTable(), Projects::ID, self::PROJECT_ID);
            $query->where(Projects::PREFIX, mb_strtolower($prefix), Criterion::EQUALS, '', '', Query::DB_LOWER);
            $query->where(Projects::DELETED, false);
            $query->where(self::ISSUE_NO, $issue_no);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->selectOne($query, false);
        }

        /**
         * @param $project_id
         * @param $issue_no
         *
         * @return Issue
         */
        public function getByProjectIDAndIssueNo($project_id, $issue_no)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::ISSUE_NO, $issue_no);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->selectOne($query, false);
        }

        public function setDuplicate($issue_id, $duplicate_of)
        {
            $update = new Update();
            $update->add(self::DUPLICATE_OF, $duplicate_of);
            $this->rawUpdateById($update, $issue_id);
        }

        public function getByMilestone($milestone_id, $project_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            if (!$milestone_id) {
                $criteria = new Criteria();
                $criteria->where(self::MILESTONE, null);
                $criteria->or(self::MILESTONE, 0);
                $query->and($criteria);

                $query->where(self::STATE, Issue::STATE_OPEN);
                $query->where(self::PROJECT_ID, $project_id);
            } else {
                $query->where(self::MILESTONE, $milestone_id);
            }
            $query->addOrderBy(self::MILESTONE_ORDER, QueryColumnSort::SORT_DESC);

            return $this->select($query);
        }

        public function setOrderByIssueId($order, $issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ID, $issue_id);

            $update = new Update();
            $update->add(self::MILESTONE_ORDER, $order);
            $this->rawUpdate($update, $query);
        }

        /**
         * @param array $allowed_status_ids
         */
        public function getPointsAndTimeByMilestone($milestone_id, $allowed_status_ids = [])
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            if (!$milestone_id) {
                $query->where(self::MILESTONE, null);
            } else {
                $query->where(self::MILESTONE, $milestone_id);
            }
            if (count($allowed_status_ids)) {
                $query->where(self::STATUS, $allowed_status_ids, Criterion::IN);
            }
            $query->addSelectionColumn(self::STATE, 'state');
            $query->addSelectionColumn(self::ESTIMATED_POINTS, 'estimated_points');
            $query->addSelectionColumn(self::ESTIMATED_MINUTES, 'estimated_minutes');
            $query->addSelectionColumn(self::ESTIMATED_HOURS, 'estimated_hours');
            $query->addSelectionColumn(self::ESTIMATED_DAYS, 'estimated_days');
            $query->addSelectionColumn(self::ESTIMATED_WEEKS, 'estimated_weeks');
            $query->addSelectionColumn(self::ESTIMATED_MONTHS, 'estimated_months');
            $query->addSelectionColumn(self::SPENT_POINTS, 'spent_points');
            $query->addSelectionColumn(self::SPENT_MINUTES, 'spent_minutes');
            $query->addSelectionColumn(self::SPENT_HOURS, 'spent_hours');
            $query->addSelectionColumn(self::SPENT_DAYS, 'spent_days');
            $query->addSelectionColumn(self::SPENT_WEEKS, 'spent_weeks');
            $query->addSelectionColumn(self::SPENT_MONTHS, 'spent_months');
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getByProjectIDandNoMilestone($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::MILESTONE, null);
            $query->where(self::PROJECT_ID, $project_id);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getByProjectIDandNoMilestoneandTypes($project_id, $issuetypes)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::MILESTONE, null);
            $query->where(self::ISSUE_TYPE, $issuetypes, Criterion::IN);
            $query->where(self::PROJECT_ID, $project_id);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getByProjectIDandNoMilestoneandTypesandState($project_id, $issuetypes, $state)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::MILESTONE, null);
            $query->where(self::ISSUE_TYPE, $issuetypes, Criterion::IN);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::STATE, $state);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function clearMilestone($milestone_id)
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::MILESTONE, 0);
            $update->add(self::LAST_UPDATED, time());

            $query->where(self::MILESTONE, $milestone_id);

            $this->rawUpdate($update, $query);
        }

        public function getOpenIssuesByTeamAssigned($team_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ASSIGNED_TEAM, $team_id);
            $query->where(self::STATE, Issue::STATE_OPEN);

            $res = $this->select($query);

            return $res;
        }

        public function getOpenIssuesByUserAssigned($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ASSIGNEE_USER, $user_id);
            $query->where(self::STATE, Issue::STATE_OPEN);

            $res = $this->select($query);

            return $res;
        }

        public function getOpenIssuesByProjectIDAndIssueTypes($project_id, $issuetypes, $order_by = null)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::ISSUE_TYPE, $issuetypes, Criterion::IN);
            $query->where(self::STATE, Issue::STATE_OPEN);

            if ($order_by != null) {
                $query->addOrderBy($order_by);
            }

            $res = $this->rawSelect($query);

            return $res;
        }

        public function getRecentByProjectIDandIssueType($project_id, $issuetype, $limit = 10)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::ISSUE_TYPE, $issuetype);
            $query->addOrderBy(self::POSTED, QueryColumnSort::SORT_DESC);
            if ($limit !== null)
                $query->setLimit($limit);

            $res = $this->rawSelect($query);

            return $res;
        }

        public function getTotalPointsByMilestoneID($milestone_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->addSelectionColumn(self::ESTIMATED_POINTS, 'estimated_points', Query::DB_SUM);
            $query->addSelectionColumn(self::SPENT_POINTS, 'spent_points', Query::DB_SUM);
            if (!$milestone_id) {
                $query->where(self::MILESTONE, null);
            } else {
                $query->where(self::MILESTONE, $milestone_id);
            }
            if ($res = $this->rawSelectOne($query)) {
                return [$res->get('estimated_points'), $res->get('spent_points')];
            } else {
                return [0, 0];
            }
        }

        /**
         * @param array $allowed_status_ids
         */
        public function getTotalPercentCompleteByProjectIDAndMilestoneID($project_id, $milestone_id, $allowed_status_ids = [])
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->addSelectionColumn(self::PERCENT_COMPLETE, 'percent_complete', Query::DB_SUM);
            if (!$project_id) {
                $query->where(self::PROJECT_ID, null);
            } else {
                $query->where(self::PROJECT_ID, $project_id);
            }
            if (!$milestone_id) {
                $query->where(self::MILESTONE, null);
            } else {
                $query->where(self::MILESTONE, $milestone_id);
            }
            if (count($allowed_status_ids)) {
                $query->where(self::STATUS, $allowed_status_ids, Criterion::IN);
            }
            if ($res = $this->rawSelectOne($query)) {
                return $res->get('percent_complete');
            } else {
                return 0;
            }
        }

        public function getTotalHoursByMilestoneID($milestone_id)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->addSelectionColumn(self::ESTIMATED_HOURS, 'estimated_hours', Query::DB_SUM);
            $query->addSelectionColumn(self::SPENT_HOURS, 'spent_hours', Query::DB_SUM);
            if (!$milestone_id) {
                $query->where(self::MILESTONE, null);
            } else {
                $query->where(self::MILESTONE, $milestone_id);
            }
            if ($res = $this->rawSelectOne($query)) {
                return [$res->get('estimated_hours'), $res->get('spent_hours')];
            } else {
                return [0, 0];
            }
        }

        public function getUpdatedIssueIDsByTimestampAndProjectIDAndMilestoneID($last_updated, $project_id, $milestone_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'id');
            $query->addSelectionColumn(self::LAST_UPDATED, 'last_updated');
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::MILESTONE, $milestone_id, Criterion::EQUALS);
            $query->where(self::LAST_UPDATED, $last_updated, Criterion::GREATER_THAN_EQUAL);

            $res = $this->rawSelect($query);

            return ($res) ? $this->_getLastUpdatedArrayFromResultset($res) : [];
        }

        protected function _getLastUpdatedArrayFromResultset(Resultset $res)
        {
            $ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $ids[] = ['issue_id' => $row->get('id'), 'last_updated' => $row->get('last_updated')];
                }
            }

            return $ids;
        }

        public function getUpdatedIssueIDsByTimestampAndProjectIDAndIssuetypeID($last_updated, $project_id, $issuetype_id = null)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'id');
            $query->addSelectionColumn(self::LAST_UPDATED, 'last_updated');
            $query->where(self::PROJECT_ID, $project_id);
            if ($issuetype_id === null) {
                $query->where(self::MILESTONE, 0, Criterion::NOT_EQUALS);
            } else {
                $query->where(self::ISSUE_TYPE, $issuetype_id);
            }
            $query->where(self::LAST_UPDATED, $last_updated, Criterion::GREATER_THAN_EQUAL);

            $res = $this->rawSelect($query);

            return ($res) ? $this->_getLastUpdatedArrayFromResultset($res) : [];
        }

        public function markIssuesDeletedByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);

            $update = new Update();
            $update->add(self::DELETED, true);

            $this->rawUpdate($update, $query);
        }

        public function findIssues($filters = [], $results_per_page = 30, $offset = 0, $groupby = null, $grouporder = null, $sortfields = [self::LAST_UPDATED => 'asc'], $include_deleted = false)
        {
            $query = $this->getQuery();
            if (!$include_deleted) {
                $query->where(self::DELETED, false);
            }

            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            if (count($filters) > 0) {
                $query->join(IssueCustomFields::getTable(), IssueCustomFields::ISSUE_ID, self::ID);
                $query->join(IssueAffectsComponent::getTable(), IssueAffectsComponent::ISSUE, self::ID);
                $query->join(IssueAffectsEdition::getTable(), IssueAffectsEdition::ISSUE, self::ID);
                $query->join(IssueAffectsBuild::getTable(), IssueAffectsBuild::ISSUE, self::ID);

                $filter_keys = array_keys($filters);
                foreach ($filters as $filter) {
                    self::parseFilter($query, $filter, $filter_keys);
                }
            }

            $query->addSelectionColumn(self::ID);
            $query->setIsDistinct();

            if ($offset != 0)
                $query->setOffset($offset);

            $query2 = clone $query;
            $count = $this->count($query2);

            if ($count > 0) {
                $query3 = $this->getQuery();

                if ($results_per_page != 0)
                    $query->setLimit($results_per_page);

                if ($offset != 0)
                    $query->setOffset($offset);

                if ($groupby !== null) {
                    $grouporder = ($grouporder !== null) ? (($grouporder == 'asc') ? QueryColumnSort::SORT_ASC : QueryColumnSort::SORT_DESC) : QueryColumnSort::SORT_ASC;
                    switch ($groupby) {
                        case 'category':
                            $query->join(ListTypes::getTable(), ListTypes::ID, self::CATEGORY);
                            $query->addSelectionColumn(ListTypes::NAME);
                            $query->addOrderBy(ListTypes::NAME, $grouporder);
                            $query3->join(ListTypes::getTable(), ListTypes::ID, self::CATEGORY);
                            $query3->addOrderBy(ListTypes::NAME, $grouporder);
                            break;
                        case 'status':
                            $query->join(ListTypes::getTable(), ListTypes::ID, self::STATUS);
                            $query->addSelectionColumn(self::STATUS);
                            $query->addSelectionColumn(ListTypes::ORDER);
                            $query->addOrderBy(ListTypes::ORDER, QueryColumnSort::SORT_DESC);
                            $query3->join(ListTypes::getTable(), ListTypes::ID, self::STATUS);
                            $query3->addOrderBy(ListTypes::ORDER, QueryColumnSort::SORT_DESC);
                            break;
                        case 'milestone':
                            $query->addSelectionColumn(self::MILESTONE);
                            $query->addSelectionColumn(self::PERCENT_COMPLETE);
                            $query->addOrderBy(self::MILESTONE, $grouporder);
                            $query->addOrderBy(self::PERCENT_COMPLETE, 'desc');
                            $query3->addOrderBy(self::MILESTONE, $grouporder);
                            $query3->addOrderBy(self::PERCENT_COMPLETE, 'desc');
                            break;
                        case 'assignee':
                            $query->addSelectionColumn(self::ASSIGNEE_TEAM);
                            $query->addSelectionColumn(self::ASSIGNEE_USER);
                            $query->addOrderBy(self::ASSIGNEE_TEAM);
                            $query->addOrderBy(self::ASSIGNEE_USER, $grouporder);
                            $query3->addOrderBy(self::ASSIGNEE_TEAM);
                            $query3->addOrderBy(self::ASSIGNEE_USER, $grouporder);
                            break;
                        case 'posted_by':
                            $query->join(Users::getTable(), Users::ID, self::POSTED_BY);
                            $query3->join(Users::getTable(), Users::ID, self::POSTED_BY);
                            $query->addSelectionColumn(self::POSTED_BY);
                            $query->addSelectionColumn(Users::USERNAME);
                            $query->addOrderBy(Users::USERNAME, $grouporder);
                            $query3->addOrderBy(Users::USERNAME, $grouporder);
                            break;
                        case 'state':
                            $query->addSelectionColumn(self::STATE);
                            $query->addOrderBy(self::STATE, $grouporder);
                            $query3->addOrderBy(self::STATE, $grouporder);
                            break;
                        case 'posted':
                            $query->addSelectionColumn(self::POSTED);
                            $query->addOrderBy(self::POSTED, $grouporder);
                            $query3->addOrderBy(self::POSTED, $grouporder);
                            break;
                        case 'severity':
                            $query->join(ListTypes::getTable(), ListTypes::ID, self::SEVERITY);
                            $query->addSelectionColumn(self::SEVERITY);
                            $query->addSelectionColumn(ListTypes::ORDER);
                            $query->addOrderBy(ListTypes::ORDER, $grouporder);
                            $query3->join(ListTypes::getTable(), ListTypes::ID, self::SEVERITY);
                            $query3->addOrderBy(ListTypes::ORDER, $grouporder);
                            break;
                        case 'user_pain':
                            $query->addSelectionColumn(self::USER_PAIN);
                            $query->addOrderBy(self::USER_PAIN, $grouporder);
                            $query3->addOrderBy(self::USER_PAIN, $grouporder);
                            break;
                        case 'votes':
                            $query->addSelectionColumn(self::VOTES_TOTAL);
                            $query->addOrderBy(self::VOTES_TOTAL, $grouporder);
                            $query3->addOrderBy(self::VOTES_TOTAL, $grouporder);
                            break;
                        case 'resolution':
                            $query->join(ListTypes::getTable(), ListTypes::ID, self::RESOLUTION);
                            $query->addSelectionColumn(self::RESOLUTION);
                            $query->addSelectionColumn(ListTypes::ORDER);
                            $query->addOrderBy(ListTypes::ORDER, $grouporder);
                            $query3->join(ListTypes::getTable(), ListTypes::ID, self::RESOLUTION);
                            $query3->addOrderBy(ListTypes::ORDER, $grouporder);
                            break;
                        case 'priority':
                            $query->join(ListTypes::getTable(), ListTypes::ID, self::PRIORITY);
                            $query->addSelectionColumn(self::PRIORITY);
                            $query->addSelectionColumn(ListTypes::ORDER);
                            $query->addOrderBy(ListTypes::ORDER, $grouporder);
                            $query3->join(ListTypes::getTable(), ListTypes::ID, self::PRIORITY);
                            $query3->addOrderBy(ListTypes::ORDER, $grouporder);
                            break;
                        case 'issuetype':
                            $query->join(IssueTypes::getTable(), IssueTypes::ID, self::ISSUE_TYPE);
                            $query->addSelectionColumn(IssueTypes::NAME);
                            $query->addOrderBy(IssueTypes::NAME, $grouporder);
                            $query3->join(IssueTypes::getTable(), IssueTypes::ID, self::ISSUE_TYPE);
                            $query3->addOrderBy(IssueTypes::NAME, $grouporder);
                            break;
                        case 'edition':
                            $query->join(IssueAffectsEdition::getTable(), IssueAffectsEdition::ISSUE, self::ID);
                            $query->join(Editions::getTable(), Editions::ID, IssueAffectsEdition::EDITION, [], Join::LEFT, IssueAffectsEdition::getTable());
                            $query->addSelectionColumn(Editions::NAME);
                            $query->addOrderBy(Editions::NAME, $grouporder);
                            $query3->join(IssueAffectsEdition::getTable(), IssueAffectsEdition::ISSUE, self::ID);
                            $query3->join(Editions::getTable(), Editions::ID, IssueAffectsEdition::EDITION, [], Join::LEFT, IssueAffectsEdition::getTable());
                            $query3->addOrderBy(Editions::NAME, $grouporder);
                            break;
                        case 'build':
                            $query->join(IssueAffectsBuild::getTable(), IssueAffectsBuild::ISSUE, self::ID);
                            $query->join(Builds::getTable(), Builds::ID, IssueAffectsBuild::BUILD, [], Join::LEFT, IssueAffectsBuild::getTable());
                            $query->addSelectionColumn(Builds::NAME);
                            $query->addOrderBy(Builds::NAME, $grouporder);
                            $query3->join(IssueAffectsBuild::getTable(), IssueAffectsBuild::ISSUE, self::ID);
                            $query3->join(Builds::getTable(), Builds::ID, IssueAffectsBuild::BUILD, [], Join::LEFT, IssueAffectsBuild::getTable());
                            $query3->addOrderBy(Builds::NAME, $grouporder);
                            break;
                        case 'component':
                            $query->join(IssueAffectsComponent::getTable(), IssueAffectsComponent::ISSUE, self::ID);
                            $query->join(Components::getTable(), Components::ID, IssueAffectsComponent::COMPONENT, [], Join::LEFT, IssueAffectsComponent::getTable());
                            $query->addSelectionColumn(Components::NAME);
                            $query->addOrderBy(Components::NAME, $grouporder);
                            $query3->join(IssueAffectsComponent::getTable(), IssueAffectsComponent::ISSUE, self::ID);
                            $query3->join(Components::getTable(), Components::ID, IssueAffectsComponent::COMPONENT, [], Join::LEFT, IssueAffectsComponent::getTable());
                            $query3->addOrderBy(Components::NAME, $grouporder);
                            break;
                        case 'time_spent':
                            $query->join(IssueSpentTimes::getTable(), IssueSpentTimes::ISSUE_ID, self::ID);
                            $query->addSelectionColumn(IssueSpentTimes::EDITED_AT);
                            $query->addOrderBy(IssueSpentTimes::EDITED_AT, $grouporder);
                            $query3->join(IssueSpentTimes::getTable(), IssueSpentTimes::ISSUE_ID, self::ID);
                            $query3->addOrderBy(IssueSpentTimes::EDITED_AT, $grouporder);
                            break;
                    }
                }

                foreach ($sortfields as $field => $sortorder) {
                    $query->addSelectionColumn($field);
                    $query->addOrderBy($field, $sortorder);
                }

                $res = $this->rawSelect($query, 'none');
                $ids = [];
                $sums = [];

                if ($res) {
                    while ($row = $res->getNextRow()) {
                        $ids[] = $row->get(self::ID);
                        $sum = [];

                        foreach (Timeable::getUnits() as $time_unit) {
                            if (!isset($row['spent_' . $time_unit . '_sum']))
                                continue;

                            $sum['spent_' . $time_unit] = $row->get('spent_' . $time_unit . '_sum');
                        }

                        $sums[$row->get(self::ID)] = $sum;
                    }

                    $query3->where(self::ID, $ids, Criterion::IN);
                    foreach ($sortfields as $field => $sortorder) {
                        if ($field == IssueSpentTimes::EDITED_AT) {
                            $query3->join(IssueSpentTimes::getTable(), IssueSpentTimes::ISSUE_ID, self::ID);
                            $query3->addGroupBy(self::ID);
                            $query3->addSelectionColumn($field);
                        }

                        $query3->addOrderBy($field, $sortorder);
                    }

                    $res3 = $this->rawSelect($query3);
                    $rows = $res3->getAllRows();
                } else {
                    $rows = [];
                }

                unset($res);
                unset($res3);

                return [$rows, $count, $ids, $sums];
            } else {
                return [null, 0, [], []];
            }

        }

        public static function parseFilter(Query $query, $filter, $filters, $criteria = null)
        {
            if (is_array($filter)) {
                foreach ($filter as $single_filter) {
                    $criteria = self::parseFilter($query, $single_filter, $filters, $criteria);
                }
            } elseif ($filter instanceof SearchFilter) {
                if ($filter->hasValue()) {
                    $criteria = $filter->addToCriteria($query, $filters, $criteria);
                    if ($criteria !== null) $query->where($criteria);
                }
            }
        }

        public function getDuplicateIssuesByIssueNo($issue_no)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->addSelectionColumn(self::ID);
            $query->where(self::DUPLICATE_OF, $issue_no);

            return $this->select($query);
        }

        public function saveVotesTotalForIssueID($votes_total, $issue_id)
        {
            $update = new Update();
            $update->add(self::VOTES_TOTAL, $votes_total);
            $this->rawUpdateById($update, $issue_id);
        }

        /**
         * Return list of issue reported by an user
         *
         * @param int $user_id user ID
         * @param int $limit [optional] number of issues to retrieve
         *
         * @return Issue[]
         */
        public function getIssuesPostedByUser($user_id, $limit = 15)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::POSTED_BY, $user_id);
            $query->addOrderBy(self::POSTED, QueryColumnSort::SORT_DESC);
            $query->setLimit($limit);

            return $this->select($query);
        }

        /**
         * Move issues from one step to another for a given issue type and conversions
         *
         * @param Project $project
         * @param Issuetype $type
         * @param array $conversions
         *
         * $conversions should be an array containing arrays:
         * array (
         *         array(oldstep, newstep)
         *         ...
         * )
         */
        public function convertIssueStepByIssuetype(Project $project, Issuetype $type, array $conversions)
        {
            foreach ($conversions as $conversion) {
                $query = $this->getQuery();
                $query->where(self::PROJECT_ID, $project->getID());
                $query->where(self::ISSUE_TYPE, $type->getID());
                $query->where(self::WORKFLOW_STEP_ID, $conversion[0]);

                $update = new Update();
                $update->add(self::WORKFLOW_STEP_ID, $conversion[1]);
                $this->rawUpdate($update, $query);
            }
        }

        public function getNextIssueFromIssueIDAndProjectID($issue_id, $project_id, $only_open = false)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $issue_id, Criterion::GREATER_THAN);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::DELETED, false);
            if ($only_open) $query->where(self::STATE, Issue::STATE_OPEN);

            $query->addOrderBy(self::ISSUE_NO, QueryColumnSort::SORT_ASC);

            return $this->selectOne($query);
        }

        public function getNextIssueFromIssueMilestoneOrderAndMilestoneID($milestone_order, $milestone_id, $only_open = false)
        {
            $query = $this->getQuery();
            $query->where(self::MILESTONE_ORDER, $milestone_order, Criterion::GREATER_THAN);
            $query->where(self::MILESTONE, $milestone_id);
            $query->where(self::DELETED, false);
            if ($only_open) $query->where(self::STATE, Issue::STATE_OPEN);

            $query->addOrderBy(self::MILESTONE_ORDER, QueryColumnSort::SORT_ASC);
            $query->addOrderBy(self::ID, QueryColumnSort::SORT_ASC);

            return $this->selectOne($query);
        }

        public function getPreviousIssueFromIssueIDAndProjectID($issue_id, $project_id, $only_open = false)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $issue_id, Criterion::LESS_THAN);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::DELETED, false);
            if ($only_open) $query->where(self::STATE, Issue::STATE_OPEN);

            $query->addOrderBy(self::ISSUE_NO, QueryColumnSort::SORT_DESC);

            return $this->selectOne($query);
        }

        public function getPreviousIssueFromIssueMilestoneOrderAndMilestoneID($milestone_order, $milestone_id, $only_open = false)
        {
            $query = $this->getQuery();
            $query->where(self::MILESTONE_ORDER, $milestone_order, Criterion::LESS_THAN);
            $query->where(self::MILESTONE, $milestone_id);
            $query->where(self::DELETED, false);
            if ($only_open) $query->where(self::STATE, Issue::STATE_OPEN);

            $query->addOrderBy(self::MILESTONE_ORDER, QueryColumnSort::SORT_DESC);
            $query->addOrderBy(self::ID, QueryColumnSort::SORT_DESC);

            return $this->selectOne($query);
        }

        public function fixHours($issue_id)
        {
            $times = IssueSpentTimes::getTable()->getSpentTimeSumsByIssueId($issue_id);
            $update = new Update();
            $update->add(self::SPENT_HOURS, $times['hours']);
            $this->rawUpdateById($update, $issue_id);
        }

        public function touchIssue($issue_id, $last_updated = null)
        {
            $update = new Update();
            $update->add(self::LAST_UPDATED, isset($last_updated) ? $last_updated : time());
            $this->rawUpdateById($update, $issue_id);
        }

        public function reAssignIssuesByMilestoneIds($current_milestone_id, $new_milestone_id, $milestone_order = null)
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::LAST_UPDATED, time());
            $update->add(self::MILESTONE, $new_milestone_id);

            if ($milestone_order !== null) $update->add(self::MILESTONE_ORDER, $milestone_order);

            $query->where(self::MILESTONE, $current_milestone_id);
            $query->where(self::STATE, Issue::STATE_OPEN);
            $this->rawUpdate($update, $query);
        }

        public function assignMilestoneIDbyIssueIDs($milestone_id, $issue_ids)
        {
            if (!empty($issue_ids)) {
                $query = $this->getQuery();
                $update = new Update();

                $update->add(self::LAST_UPDATED, time());
                $update->add(self::MILESTONE, $milestone_id);

                $query->where(self::ID, $issue_ids, Criterion::IN);
                $this->rawUpdate($update, $query);
            }
        }

        /**
         * @param $issue_ids
         * @return Issue[]
         */
        public function getByIssueIDs($issue_ids)
        {
            if (empty($issue_ids))
                return [];

            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ID, $issue_ids, Criterion::IN);
            return $this->select($query);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('project', self::PROJECT_ID);
            $this->addIndex('project', self::PROJECT_ID);
            $this->addIndex('last_updated', self::LAST_UPDATED);
            $this->addIndex('deleted', self::DELETED);
            $this->addIndex('deleted_project', [self::DELETED, self::PROJECT_ID]);
            $this->addIndex('deleted_state_project', [self::DELETED, self::STATE, self::PROJECT_ID]);
            $this->addIndex('deleted_project_issueno', [self::DELETED, self::ISSUE_NO, self::PROJECT_ID]);
            $this->addIndex('duplicateof', [self::DUPLICATE_OF]);
        }

        protected function migrateData(Table $old_table): void
        {
            $update = new Update();
            $update->add('issues.locked_category', true);

            $this->rawUpdate($update);
        }

        public function updateIssueField($field, $current_field_value, $new_field_value)
        {
            $query = $this->getQuery();
            $query->where("issues.{$field}", $current_field_value);

            $update = new Update();
            $update->add("issues.{$field}", $new_field_value);

            $this->rawUpdate($update, $query);
        }

    }
