<?php

    namespace pachno\core\modules\project\controllers;

    use Exception;
    use InvalidArgumentException;
    use pachno\core\entities;
    use pachno\core\entities\Project;
    use pachno\core\entities\tables;
    use pachno\core\entities\tables\BuildFiles;
    use pachno\core\entities\tables\Builds;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\framework;
    use pachno\core\framework\Context;
    use pachno\core\framework\Request;
    use pachno\core\framework\Settings;
    use pachno\core\helpers;
    use pachno\core\modules\main\cli\entities\tbg\AgileBoard;

    /**
     * actions for the project module
     *
     * @Routes(name_prefix="project_", url_prefix="/:project_key")
     *
     * @property entities\Client $selected_client
     * @property entities\Build[][] $active_builds
     * @property entities\Build[][] $archived_builds
     * @property entities\Build[][] $upcoming_builds
     */
    class Main extends helpers\ProjectActions
    {

        protected $anonymous_project_routes = [
            'rungetUpdatedProjectKey',
            'runconfigureProjectSettings'
        ];

        /**
         * The project dashboard
         *
         * @param framework\Request $request
         */
        public function runDashboard(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD));

            if ($request->isPost() && $request['setup_default_dashboard'] && $this->getUser()->canEditProjectDetails($this->selected_project)) {
                tables\DashboardViews::getTable()->setDefaultViews($this->selected_project->getID(), entities\DashboardView::TYPE_PROJECT);
                $this->forward($this->getRouting()->generate('project_dashboard', ['project_key' => $this->selected_project->getKey()]));
            }
            if ($request['dashboard_id']) {
                foreach ($this->selected_project->getDashboards() as $db) {
                    if ($db->getID() == (int)$request['dashboard_id']) {
                        $dashboard = $db;
                        break;
                    }
                }
            }

            if (!isset($dashboard) || !$dashboard instanceof entities\Dashboard) {
                $dashboard = $this->selected_project->getDefaultDashboard();
            }

            $this->dashboard = $dashboard;
        }

        /**
         * The project files page
         *
         * @param framework\Request $request
         */
        public function runFiles(framework\Request $request)
        {

        }

        /**
         * The project roadmap page
         * @Route(name="roadmap", url="/roadmap")
         *
         * @param framework\Request $request
         */
        public function runRoadmap(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_RELEASES));
        }

        /**
         * @Route(name="milestone", url="/milestones/:milestone_id", methods="GET")
         * @param framework\Request $request
         */
        public function runGetMilestone(framework\Request $request)
        {
            $milestone = Milestones::getTable()->selectById($request['milestone_id']);
            return $this->renderJSON(['milestone' => $milestone->toJSON(true)]);
        }

        /**
         * @Route(name="post_milestone", url="/milestones/:milestone_id", methods="POST")
         * @param framework\Request $request
         */
        public function runEditMilestone(framework\Request $request)
        {
            $milestone_id = $request['milestone_id'];
            if ($milestone_id) {
                $milestone = Milestones::getTable()->selectById($request['milestone_id']);
            } else {
                $milestone = new entities\Milestone();
            }
            if (!$request['name'])
                throw new \Exception($this->getI18n()->__('You must provide a valid milestone name'));

            $milestone->setName($request['name']);
            $milestone->setProject($this->selected_project);
            $milestone->setStarting((bool) $request['is_starting']);
            $milestone->setScheduled((bool) $request['is_scheduled']);

            if ($request['is_starting'] && $request['is_scheduled']) {
                $milestone->setStartingDate($request['dates'][0]);
                $milestone->setScheduledDate($request['dates'][1]);
            } elseif ($request['is_starting']) {
                $milestone->setStartingDate($request['dates']);
            } elseif ($request['is_scheduled']) {
                $milestone->setScheduledDate($request['dates']);
            }

            $milestone->save();

            return $this->renderJSON(['milestone' => $milestone->toJSON(true)]);
        }

        /**
         * @Route(name="milestones", url="/milestones")
         * @param framework\Request $request
         */
        public function runGetMilestones(framework\Request $request)
        {
            $json = ['milestones' => []];
            try {
                foreach ($this->selected_project->getMilestones() as $milestone) {
                    if ($request['state'] == 'open' && $milestone->isClosed()) {
                        continue;
                    }

                    if ($request['state'] == 'closed' && !$milestone->isClosed()) {
                        continue;
                    }

                    if (!$request['milestone_type'] || $request['milestone_type'] == 'all') {
                        $json['milestones'][] = $milestone->toJSON(false);
                        continue;
                    }

                    if ($request['milestone_type'] == 'sprint' && $milestone->isSprint()) {
                        $json['milestones'][] = $milestone->toJSON(false);
                    }

                    if ($request['milestone_type'] == 'regular' && !$milestone->isSprint()) {
                        $json['milestones'][] = $milestone->toJSON(false);
                    }
                }
            } catch (\Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $e->getMessage()]);
            }

            return $this->renderJSON($json);
        }

        /**
         * The project planning page
         *
         * @param framework\Request $request
         */
        public function runTimeline(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD));
            $offset = $request->getParameter('offset', 0);
            if ($request['show'] == 'important') {
                $this->recent_activities = $this->selected_project->getRecentActivities(40, true, $offset);
                $this->important = true;
            } else {
                $this->important = false;
                $this->recent_activities = $this->selected_project->getRecentActivities(40, false, $offset);
            }

            if ($offset) {
                return $this->renderJSON(['content' => $this->getComponentHTML('project/timeline', ['activities' => $this->recent_activities]), 'offset' => $offset + 40]);
            }
        }

        /**
         * Sorting milestones
         *
         * @Route(url="/milestones/sort/:csrf_token", name="sort_milestones")
         * @CsrfProtected
         *
         * @param framework\Request $request
         */
        public function runSortMilestones(framework\Request $request)
        {
            $this->forward403unless($this->getUser()->canManageProjectReleases($this->selected_project));
            $milestones = $request->getParameter('milestone_ids', []);

            try {
                if (is_array($milestones)) {
                    foreach ($milestones as $order => $milestone_id) {
                        $milestone = Milestones::getTable()->selectByID($milestone_id);

                        if ($milestone->getProject()->getID() != $this->selected_project->getID())
                            continue;

                        $milestone->setOrder($order);
                        $milestone->save();
                    }
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('An error occurred when trying to save the milestone order')]);
            }

            return $this->renderJSON(['sorted' => 'ok']);
        }

        /**
         * Show the scrum burndown chart for a specified sprint
         *
         * @param framework\Request $request
         */
        public function runScrumShowBurndownImage(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_BOARDS));

            $milestone = null;
            $maxEstimation = 0;

            if ($m_id = $request['sprint_id']) {
                $milestone = entities\tables\Milestones::getTable()->selectById($m_id);
            } else {
                $milestones = $this->selected_project->getUpcomingMilestones();
                if (count($milestones)) {
                    $milestone = array_shift($milestones);
                }
            }

            $this->getResponse()->setContentType('image/png');
            $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);
            if ($milestone instanceof entities\Milestone) {
                $datasets = [];

                $burndown_data = $milestone->getBurndownData();

                if (count($burndown_data['estimations']['hours'])) {
                    foreach ($burndown_data['estimations']['hours'] as $key => $e) {
                        if (array_key_exists($key, $burndown_data['spent_times']['hours'])) {
                            $burndown_data['estimations']['hours'][$key] -= $burndown_data['spent_times']['hours'][$key];
                            if ($burndown_data['estimations']['hours'][$key] > $maxEstimation)
                                $maxEstimation = $burndown_data['estimations']['hours'][$key];
                        }
                    }
                    $datasets[] = ['values' => array_values($burndown_data['estimations']['hours']), 'label' => Context::getI18n()->__('Remaining effort'), 'burndown' => ['maxEstimation' => $maxEstimation, 'label' => "Burndown Line"]];
                    $this->labels = array_keys($burndown_data['estimations']['hours']);
                } else {
                    $datasets[] = ['values' => [0], 'label' => Context::getI18n()->__('Remaining effort'), 'burndown' => ['maxEstimation' => $maxEstimation, 'label' => "Burndown Line"]];
                    $this->labels = [0];
                }
                $this->datasets = $datasets;
                $this->milestone = $milestone;
            } else {
                return $this->renderText('');
            }
        }

        /**
         * Set color on a user story
         *
         * @param framework\Request $request
         */
        public function runScrumSetStoryDetail(framework\Request $request)
        {
            $this->forward403if(Context::getCurrentProject()->isArchived());
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            $issue = tables\Issues::getTable()->selectById((int)$request['story_id']);
            try {
                if ($issue instanceof entities\Issue) {
                    switch ($request['detail']) {
                        case 'color':
                            $issue->setCoverColor($request['color']);
                            $issue->save();

                            return $this->renderJSON(['failed' => false, 'text_color' => $issue->getAgileTextColor()]);
                            break;
                    }
                }

            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }

            return $this->renderJSON(['failed' => true, 'error' => Context::getI18n()->__('Invalid user story')]);
        }

        /**
         * Add a new sprint type milestone to a project
         *
         * @param framework\Request $request
         */
        public function runScrumAddSprint(framework\Request $request)
        {
            $this->forward403if(Context::getCurrentProject()->isArchived());
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            if (($sprint_name = $request['sprint_name']) && trim($sprint_name) != '') {
                $sprint = new entities\Milestone();
                $sprint->setName($sprint_name);
                $sprint->setType(entities\Milestone::TYPE_SCRUMSPRINT);
                $sprint->setProject($this->selected_project);
                $sprint->setStartingDate(mktime(0, 0, 1, $request['starting_month'], $request['starting_day'], $request['starting_year']));
                $sprint->setScheduledDate(mktime(23, 59, 59, $request['scheduled_month'], $request['scheduled_day'], $request['scheduled_year']));
                $sprint->save();

                return $this->renderJSON(['failed' => false, 'content' => $this->getComponentHTML('sprintbox', ['sprint' => $sprint]), 'sprint_id' => $sprint->getID()]);
            }

            return $this->renderJSON(['failed' => true, 'error' => Context::getI18n()->__('Please specify a sprint name')]);
        }

        /**
         * The project issue list page
         *
         * @param framework\Request $request
         */
        public function runIssues(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_ISSUES));
        }

        /**
         * The project team page
         *
         * @param framework\Request $request
         */
        public function runTeam(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD));
            $this->assigned_users = $this->selected_project->getAssignedUsers();
            $this->assigned_teams = $this->selected_project->getAssignedTeams();
        }

        /**
         * The project statistics page
         *
         * @param framework\Request $request
         */
        public function runStatistics(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD));
        }

        public function runStatisticsLast15(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD));

            if (!function_exists('imagecreatetruecolor')) {
                return $this->return404(Context::getI18n()->__('The libraries to generate images are not installed. Please see https://projects.pach.no/pachno/docs/r/faq for more information'));
            }

            $this->getResponse()->setContentType('image/png');
            $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);
            $datasets = [];
            $issues = $this->selected_project->getLast15Counts();
            $datasets[] = ['values' => $issues['open'], 'label' => Context::getI18n()->__('Open issues', [], true)];
            $datasets[] = ['values' => $issues['closed'], 'label' => Context::getI18n()->__('Issues closed', [], true)];
            $this->datasets = $datasets;
            $this->labels = [15, '', '', '', '', 10, '', '', '', '', 5, '', '', '', '', 0];
        }

        public function runStatisticsImagesets(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD));
            try {
                if (!in_array($request['set'], ['issues_per_status', 'issues_per_state', 'issues_per_priority', 'issues_per_category', 'issues_per_resolution', 'issues_per_reproducability'])) {
                    throw new InvalidArgumentException(Context::getI18n()->__('Invalid image set'));
                }

                $base_url = Context::getRouting()->generate('project_statistics_image', ['project_key' => $this->selected_project->getKey(), 'key' => '%key', 'mode' => '%mode', 'image_number' => '%image_number']);
                $key = urlencode('%key');
                $mode = urlencode('%mode');
                $image_number = urlencode('%image_number');
                $set = $request['set'];
                if ($set != 'issues_per_state') {
                    $images = ['main' => str_replace([$key, $mode, $image_number], [$set, 'main', 1], $base_url),
                        'mini_1_small' => str_replace([$key, $mode, $image_number], [$set, 'mini', 1], $base_url),
                        'mini_1_large' => str_replace([$key, $mode, $image_number], [$set, 'main', 1], $base_url),
                        'mini_2_small' => str_replace([$key, $mode, $image_number], [$set, 'mini', 2], $base_url),
                        'mini_2_large' => str_replace([$key, $mode, $image_number], [$set, 'main', 2], $base_url),
                        'mini_3_small' => str_replace([$key, $mode, $image_number], [$set, 'mini', 3], $base_url),
                        'mini_3_large' => str_replace([$key, $mode, $image_number], [$set, 'main', 3], $base_url)];
                } else {
                    $images = ['main' => str_replace([$key, $mode, $image_number], [$set, 'main', 1], $base_url)];
                }
                $this->getResponse()->setHttpStatus(200);

                return $this->renderJSON(['images' => $images]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        public function runStatisticsGetImage(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD));

            if (!function_exists('imagecreatetruecolor')) {
                return $this->return404(Context::getI18n()->__('The libraries to generate images are not installed. Please see https://pachno.com for more information'));
            }

            $this->getResponse()->setContentType('image/png');
            $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);

            $this->key = $request['key'];
            $this->image_number = (int)$request['image_number'];
            $this->_generateImageDetailsFromKey($request['mode']);
        }

        protected function _generateImageDetailsFromKey($mode = null)
        {
            $this->graphmode = null;
            $i18n = Context::getI18n();
            if ($mode == 'main') {
                $this->width = 695;
                $this->height = 310;
            } else {
                $this->width = 230;
                $this->height = 150;
            }
            switch ($this->key) {
                case 'issues_per_status':
                    $this->graphmode = 'piechart';
                    $counts = tables\Issues::getTable()->getStatusCountByProjectID($this->selected_project->getID());
                    if ($this->image_number == 1) {
                        $this->title = $i18n->__('Total number of issues per status type');
                    } elseif ($this->image_number == 2) {
                        $this->title = $i18n->__('Open issues per status type');
                    } elseif ($this->image_number == 3) {
                        $this->title = $i18n->__('Closed issues per status type');
                    }
                    break;
                case 'issues_per_priority':
                    $this->graphmode = 'piechart';
                    $counts = tables\Issues::getTable()->getPriorityCountByProjectID($this->selected_project->getID());
                    if ($this->image_number == 1) {
                        $this->title = $i18n->__('Total number of issues per priority level');
                    } elseif ($this->image_number == 2) {
                        $this->title = $i18n->__('Open issues per priority level');
                    } elseif ($this->image_number == 3) {
                        $this->title = $i18n->__('Closed issues per priority level');
                    }
                    break;
                case 'issues_per_category':
                    $this->graphmode = 'piechart';
                    $counts = tables\Issues::getTable()->getCategoryCountByProjectID($this->selected_project->getID());
                    if ($this->image_number == 1) {
                        $this->title = $i18n->__('Total number of issues per category');
                    } elseif ($this->image_number == 2) {
                        $this->title = $i18n->__('Open issues per category');
                    } elseif ($this->image_number == 3) {
                        $this->title = $i18n->__('Closed issues per category');
                    }
                    break;
                case 'issues_per_resolution':
                    $this->graphmode = 'piechart';
                    $counts = tables\Issues::getTable()->getResolutionCountByProjectID($this->selected_project->getID());
                    if ($this->image_number == 1) {
                        $this->title = $i18n->__('Total number of issues per resolution');
                    } elseif ($this->image_number == 2) {
                        $this->title = $i18n->__('Open issues per resolution');
                    } elseif ($this->image_number == 3) {
                        $this->title = $i18n->__('Closed issues per resolution');
                    }
                    break;
                case 'issues_per_reproducability':
                    $this->graphmode = 'piechart';
                    $counts = tables\Issues::getTable()->getReproducabilityCountByProjectID($this->selected_project->getID());
                    if ($this->image_number == 1) {
                        $this->title = $i18n->__('Total number of issues per reproducability level');
                    } elseif ($this->image_number == 2) {
                        $this->title = $i18n->__('Open issues per reproducability level');
                    } elseif ($this->image_number == 3) {
                        $this->title = $i18n->__('Closed issues per reproducability level');
                    }
                    break;
                case 'issues_per_state':
                    $this->graphmode = 'piechart';
                    $counts = tables\Issues::getTable()->getStateCountByProjectID($this->selected_project->getID());
                    if ($this->image_number == 1) {
                        $this->title = $i18n->__('Total number of issues (open / closed)');
                    }
                    break;
                default:
                    throw new Exception(__("unknown key '%key'", ['%key' => $this->key]));
            }
            $this->title = html_entity_decode($this->title);
            list ($values, $labels, $colors) = $this->_calculateImageDetails($counts);
            $this->values = $values;
            $this->labels = $labels;
            $this->colors = $colors;
        }

        protected function _calculateImageDetails($counts)
        {
            $i18n = Context::getI18n();
            $labels = [];
            $values = [];
            $colors = [];
            foreach ($counts as $item_id => $details) {
                if ($this->image_number == 1) {
                    $value = $details['open'] + $details['closed'];
                }
                if ($this->image_number == 2) {
                    $value = $details['open'];
                }
                if ($this->image_number == 3) {
                    $value = $details['closed'];
                }
                if (isset($value) && $value > 0) {
                    if ($item_id != 0 || $this->key == 'issues_per_state') {
                        switch ($this->key) {
                            case 'issues_per_status':
                            case 'issues_per_priority':
                            case 'issues_per_category':
                            case 'issues_per_resolution':
                            case 'issues_per_reproducability':
                                $item = tables\ListTypes::getTable()->selectById($item_id);
                                break;
                            case 'issues_per_state':
                                $item = ($item_id == entities\Issue::STATE_OPEN) ? $i18n->__('Open', [], true) : $i18n->__('Closed', [], true);
                                break;
                            default:
                                $item = null;
                        }
                        if ($this->key != 'issues_per_state') {
                            $labels[] = ($item instanceof entities\Datatype) ? html_entity_decode($item->getName()) : $i18n->__('Unknown', [], true);
                            Context::loadLibrary('common');
                            if ($item instanceof entities\common\Colorizable) {
                                $colors[] = pachno_hex_to_rgb($item->getColor());
                            }
                        } else {
                            $labels[] = $item;
                        }
                    } else {
                        $labels[] = $i18n->__('Not determined', [], true);
                    }
                    $values[] = $value;
                }
            }

            return [$values, $labels, $colors];
        }

        public function runGetMilestoneRoadmapIssues(framework\Request $request)
        {
            try {
                $i18n = Context::getI18n();
                if ($request->hasParameter('milestone_id')) {
                    $milestone = Milestones::getTable()->selectById($request['milestone_id']);

                    return $this->renderJSON(['content' => $this->getComponentHTML('project/milestoneissues', ['milestone' => $milestone])]);
                } else {
                    throw new Exception($i18n->__('Invalid milestone'));
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        public function runTransitionIssue(framework\Request $request)
        {
            try {
                $transition = tables\WorkflowTransitions::getTable()->selectById($request['transition_id']);
                $issue = tables\Issues::getTable()->selectById((int)$request['issue_id']);
                if (!$issue->isWorkflowTransitionsAvailable()) {
                    throw new Exception(Context::getI18n()->__('You are not allowed to perform any workflow transitions on this issue'));
                }

                $validation_results = $transition->validateFromRequest($request);
                if ($validation_results === true) {
                    $validation_results = $transition->transitionIssueToOutgoingStepFromRequest($issue, $request);
                }

                if ($validation_results !== true) {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => Context::getI18n()->__('There was an error trying to move this issue to the next step in the workflow'), 'message' => preg_replace('/\s+/', ' ', $this->getComponentHTML('main/issue_transition_error', ['errors' => $validation_results]))]);
                }

                if ($request->hasParameter('board_id')) {
                    $board = tables\AgileBoards::getTable()->selectById($request['board_id']);
                    $milestone = ($request['milestone_id']) ? Milestones::getTable()->selectById($request['milestone_id']) : null;

                    if ($board->usesSwimlanes()) {
                        switch ($board->getSwimlaneType()) {
                            case entities\AgileBoard::SWIMLANES_ISSUES:
                                foreach ($board->getMilestoneSwimlanes($milestone) as $swimlane) {
                                    if ($swimlane->getIdentifier() != $request['swimlane_identifier'])
                                        continue;

                                    foreach ($issue->getParentIssues() as $parent_issue) {
                                        if (!$swimlane->getIdentifierIssue() instanceof entities\Issue || $parent_issue->getID() !== $swimlane->getIdentifierIssue()->getID()) {
                                            $issue->removeDependantIssue($parent_issue);
                                        }
                                    }

                                    if ($swimlane->getIdentifierIssue() instanceof entities\Issue) {
                                        $issue->addParentIssue($swimlane->getIdentifierIssue());
                                    }

                                    break;
                                }
                                break;
                            case entities\AgileBoard::SWIMLANES_EXPEDITE:
                            case entities\AgileBoard::SWIMLANES_GROUPING:
                                foreach ($board->getMilestoneSwimlanes($milestone) as $swimlane) {
                                    if ($swimlane->getIdentifier() != $request['swimlane_identifier'])
                                        continue;

                                    if (!$swimlane->hasIssue($issue)) {
                                        $identifiables = $swimlane->getIdentifiables();
                                        $identifiable = array_shift($identifiables);

                                        if ($swimlane->getIdentifierGrouping() == 'priority') {
                                            $issue->setPriority($identifiable);
                                        } elseif ($swimlane->getIdentifierGrouping() == 'category') {
                                            $issue->setCategory($identifiable);
                                        } elseif ($swimlane->getIdentifierGrouping() == 'severity') {
                                            $issue->setSeverity($identifiable);
                                        }

                                        $issue->save();
                                        break;
                                    }
                                }
                                break;
                            default:
                                throw new Exception('Woops');
                        }
                    }
                } elseif ($request->hasParameter('parent_issue_id')) {
                    $new_parent_issue = tables\Issues::getTable()->selectById($request['parent_issue_id']);
                    foreach ($issue->getParentIssues() as $parent_issue) {
                        if (!$new_parent_issue instanceof entities\Issue || $parent_issue->getID() !== $new_parent_issue->getID()) {
                            $issue->removeDependantIssue($parent_issue);
                        }
                    }

                    if ($new_parent_issue instanceof entities\Issue) {
                        $issue->addParentIssue($new_parent_issue);
                    }
                }

                return $this->renderJSON(['last_updated' => Context::getI18n()->formatTime(time(), 20), 'issues' => [$issue->toJSON()]]);
            } catch (Exception $e) {
                return $this->return404();
            }
        }

        public function runTransitionIssues(framework\Request $request)
        {
            try {
                try {
                    $transition = entities\tables\WorkflowTransitions::getTable()->selectById($request['transition_id']);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid transition')]);
                }
                $issue_ids = $request['issue_ids'];
                $status = null;
                $closed = false;
                $issues = [];
                foreach ($issue_ids as $issue_id) {
                    $issue = tables\Issues::getTable()->selectById((int)$issue_id);
                    if (!$issue->isWorkflowTransitionsAvailable() || !$transition->validateFromRequest($request)) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => Context::getI18n()->__('The transition could not be applied to issue %issue_number because of %errors', ['%issue_number' => $issue->getFormattedIssueNo(), '%errors' => join(', ', $transition->getValidationErrors())])]);
                    }

                    try {
                        $transition->transitionIssueToOutgoingStepFromRequest($issue, $request);
                    } catch (Exception $e) {
                        $this->getResponse()->setHttpStatus(400);
                        framework\Logging::log(framework\Logging::LEVEL_WARNING, 'Transition ' . $transition->getID() . ' failed for issue ' . $issue_id);
                        framework\Logging::log(framework\Logging::LEVEL_WARNING, $e->getMessage());

                        return $this->renderJSON(['error' => $this->getI18n()->__('The transition failed because of an error in the workflow. Check your workflow configuration.')]);
                    }
                    if ($status === null) {
                        $status = $issue->getStatus();
                    }

                    if ($request->hasParameter('parent_issue_id')) {
                        $new_parent_issue = tables\Issues::getTable()->selectById($request['parent_issue_id']);
                        foreach ($issue->getParentIssues() as $parent_issue) {
                            if (!$new_parent_issue instanceof entities\Issue || $parent_issue->getID() !== $new_parent_issue->getID()) {
                                $issue->removeDependantIssue($parent_issue);
                            }
                        }

                        if ($new_parent_issue instanceof entities\Issue) {
                            $issue->addParentIssue($new_parent_issue);
                        }
                    }
                    if ($request->hasParameter('priority_id')) {
                        $priority = ($request['priority_id']) ? tables\ListTypes::getTable()->selectById($request['priority_id']) : null;
                        $issue->setPriority($priority);
                    }
                    if ($request->hasParameter('severity_id')) {
                        $severity = ($request['severity_id']) ? tables\ListTypes::getTable()->selectById($request['severity_id']) : null;
                        $issue->setSeverity($severity);
                    }
                    if ($request->hasParameter('category_id')) {
                        $category = ($request['category_id']) ? tables\ListTypes::getTable()->selectById($request['category_id']) : null;
                        $issue->setCategory($category);
                    }
                    $issue->save();

                    $closed = $issue->isClosed();
                    $issues[] = $issue->toJSON();
                }

                Context::loadLibrary('common');
                $options = ['last_updated' => Context::getI18n()->formatTime(time(), 20), 'closed' => $closed, 'issues' => $issues];
                $options['status'] = ['color' => $status->getColor(), 'name' => $status->getName(), 'id' => $status->getID()];
                if ($request->hasParameter('milestone_id')) {
                    $milestone = new entities\Milestone($request['milestone_id']);
                    $options['milestone_id'] = $milestone->getID();
                    $options['milestone_name'] = $milestone->getName();
                }
                foreach (['resolution', 'priority', 'category', 'severity'] as $item) {
                    $class = "\\pachno\\core\\entities\\" . ucfirst($item);
                    if ($request->hasParameter($item . '_id')) {
                        if ($item_id = $request[$item . '_id']) {
                            $itemobject = new $class($item_id);
                            $itemname = $itemobject->getName();
                        } else {
                            $item_id = 0;
                            $itemname = '-';
                        }
                        $options[$item] = ['name' => $itemname, 'id' => $item_id];
                    } else {
                        $method = 'get' . ucfirst($item);
                        $itemname = ($issue->$method() instanceof $class) ? $issue->$method()->getName() : '-';
                        $item_id = ($issue->$method() instanceof $class) ? $issue->$method()->getID() : 0;
                        $options[$item] = ['name' => $itemname, 'id' => $item_id];
                    }
                }

                return $this->renderJSON($options);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                framework\Logging::log(framework\Logging::LEVEL_WARNING, $e->getMessage());

                return $this->renderJSON(['error' => $this->getI18n()->__('An error occured when trying to apply the transition')]);
            }
        }

        public function runSettings(framework\Request $request)
        {
            $this->forward403if(Context::getCurrentProject()->isArchived() || !$this->getUser()->canEditProjectDetails(Context::getCurrentProject()));
            $this->access_level = ($this->getUser()->canEditProjectDetails(Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
        }

        /**
         * @Route(url="/releases")
         *
         * @param framework\Request $request
         */
        public function runReleases(framework\Request $request)
        {
            $builds = $this->selected_project->getBuilds();

            $active_builds = [0 => []];
            $active_builds_count = 0;
            $archived_builds = [0 => []];
            $archived_builds_count = 0;
            $upcoming_builds = [0 => []];
            $upcoming_builds_count = 0;

            foreach ($this->selected_project->getEditions() as $edition_id => $edition) {
                $active_builds[$edition_id] = [];
                $archived_builds[$edition_id] = [];
                $upcoming_builds[$edition_id] = [];
            }

            foreach ($builds as $build) {
                if ($build->isInternal() && (!$this->getUser()->canManageProjectReleases($build->getProject()) || !$build->getProject()->canSeeInternalBuilds())) {
                    continue;
                }

                if ((!$build->hasReleaseDate() || $build->getReleaseDate() > NOW) && !$build->isReleased()) {
                    $upcoming_builds[$build->getEditionID()][$build->getID()] = $build;
                    $upcoming_builds_count++;
                } elseif ($build->isArchived()) {
                    $archived_builds[$build->getEditionID()][$build->getID()] = $build;
                    $archived_builds_count++;
                } else {
                    $active_builds[$build->getEditionID()][$build->getID()] = $build;
                    $active_builds_count++;
                }
            }

            $this->active_builds = $active_builds;
            $this->active_builds_count = $active_builds_count;
            $this->archived_builds = $archived_builds;
            $this->archived_builds_count = $archived_builds_count;
            $this->upcoming_builds = $upcoming_builds;
            $this->upcoming_builds_count = $upcoming_builds_count;
        }

        /**
         * Find users and show selection box
         *
         * @param framework\Request $request The request object
         */
        public function runFindAssignee(framework\Request $request)
        {
            $this->message = false;

            $find_by = trim($request['find_by']);
            if (!$find_by) {
                return $this->renderJSON(['error' => $this->getI18n()->__('Please enter something to search for')]);
            }

            $selected_project = tables\Projects::getTable()->selectById($request['project_id']);
            return $this->renderJSON(['content' => $this->getComponentHTML('project/findassignee', ['selected_project' => $selected_project, 'find_by' => $find_by])]);
        }

        /**
         * Adds a user or team to a project
         *
         * @param framework\Request $request The request object
         */
        public function runAssignToProject(framework\Request $request)
        {
            if ($this->getUser()->canEditProjectDetails($this->selected_project)) {
                if ($request->hasParameter('permission')) {
                    if ($request['value'] == 1) {
                        Settings::getDefaultGroup()->addPermission($request['permission'], 'core', null, $this->selected_project->getID());
                    } else {
                        Settings::getDefaultGroup()->removePermission($request['permission'], $this->selected_project->getID());
                    }
                    framework\Context::clearPermissionsCache();

                    return $this->renderJSON(['message' => $this->getI18n()->__('Permission saved')]);
                }

                $assignee_type = $request['assignee_type'];
                $assignee_id = $request['assignee_id'];

                $assignee_role = tables\ListTypes::getTable()->selectById($request['role_id']);

                if (!$assignee_role instanceof entities\Role) {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => Context::getI18n()->__('You have to specify a role for this assignee')]);
                }

                try {
                    switch ($assignee_type) {
                        case 'user':
                            if (is_numeric($assignee_id)) {
                                $assignee = tables\Users::getTable()->selectById($assignee_id);
                            } else {
                                $assignee = new entities\User();
                                $assignee->setUsername($assignee_id);
                                $assignee->setRealname($assignee_id);
                                $assignee->setEmail($assignee_id);
                                $assignee->setGroup(framework\Settings::get(framework\Settings::SETTING_USER_GROUP));
                                $password = entities\User::createPassword();
                                $assignee->setPassword($password);
                                $assignee->save();
                                $assignee->setActivated(false);
                                $assignee->save();
                            }

                            framework\Event::createNew('core', 'projectActions::addAssignee', $this->selected_project)->trigger(['assignee' => $assignee]);
                            break;
                        case 'team':
                            $assignee = tables\Teams::getTable()->selectById($assignee_id);
                            break;
                        default:
                            throw new Exception('Invalid assignee');
                    }
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => Context::getI18n()->__('An error occurred when trying to assign user/team to this project: ' . $e->getMessage())]);
                }

                $this->selected_project->addAssignee($assignee, $assignee_role);

                return $this->renderJSON(['content' => $this->getComponentHTML('project/settings_project_assignee', ['project' => $this->selected_project, 'assignee' => $assignee])]);
            } else {
                $this->getResponse()->setHttpStatus(403);

                return $this->renderJSON(['error' => Context::getI18n()->__("You don't have access to save project settings")]);
            }
        }

        /**
         * Configure project leaders
         *
         * @param framework\Request $request The request object
         */
        public function runSetItemLead(framework\Request $request)
        {
            try {
                switch ($request['item_type']) {
                    case 'project':
                        $item = tables\Projects::getTable()->selectById($request['project_id']);
                        break;
                    case 'edition':
                        $item = tables\Editions::getTable()->selectById($request['edition_id']);
                        break;
                    case 'component':
                        $item = tables\Components::getTable()->selectById($request['component_id']);
                        break;
                }
            } catch (Exception $e) {

            }

            $this->forward403unless(isset($item) && $item instanceof entities\common\Identifiable);

            if ($request->hasParameter('value')) {
                $this->forward403unless(($request['item_type'] == 'project' && $this->getUser()->canEditProjectDetails($this->selected_project)) || ($request['item_type'] != 'project' && $this->getUser()->canManageProjectReleases($this->selected_project)));
                if ($request->hasParameter('identifiable_type')) {
                    if (in_array($request['identifiable_type'], ['team', 'user']) && $request['value']) {
                        switch ($request['identifiable_type']) {
                            case 'user':
                                $identified = tables\Users::getTable()->selectById($request['value']);
                                break;
                            case 'team':
                                $identified = tables\Teams::getTable()->selectById($request['value']);
                                break;
                        }
                        if ($identified instanceof entities\common\Identifiable) {
                            if ($request['field'] == 'owned_by')
                                $item->setOwner($identified);
                            elseif ($request['field'] == 'qa_by')
                                $item->setQaResponsible($identified);
                            elseif ($request['field'] == 'lead_by')
                                $item->setLeader($identified);
                            $item->save();
                        }
                    } else {
                        if ($request['field'] == 'owned_by')
                            $item->clearOwner();
                        elseif ($request['field'] == 'qa_by')
                            $item->clearQaResponsible();
                        elseif ($request['field'] == 'lead_by')
                            $item->clearLeader();
                        $item->save();
                    }
                }
                if ($request['field'] == 'owned_by')
                    return $this->renderJSON(['field' => (($item->hasOwner()) ? ['id' => $item->getOwner()->getID(), 'name' => (($item->getOwner() instanceof entities\User) ? $this->getComponentHTML('main/userdropdown', ['user' => $item->getOwner()]) : $this->getComponentHTML('main/teamdropdown', ['team' => $item->getOwner()]))] : ['id' => 0, 'name' => $this->getI18n()->__('No project owner assigned')])]);
                elseif ($request['field'] == 'lead_by')
                    return $this->renderJSON(['field' => (($item->hasLeader()) ? ['id' => $item->getLeader()->getID(), 'name' => (($item->getLeader() instanceof entities\User) ? $this->getComponentHTML('main/userdropdown', ['user' => $item->getLeader()]) : $this->getComponentHTML('main/teamdropdown', ['team' => $item->getLeader()]))] : ['id' => 0, 'name' => $this->getI18n()->__('No project leader assigned')])]);
                elseif ($request['field'] == 'qa_by')
                    return $this->renderJSON(['field' => (($item->hasQaResponsible()) ? ['id' => $item->getQaResponsible()->getID(), 'name' => (($item->getQaResponsible() instanceof entities\User) ? $this->getComponentHTML('main/userdropdown', ['user' => $item->getQaResponsible()]) : $this->getComponentHTML('main/teamdropdown', ['team' => $item->getQaResponsible()]))] : ['id' => 0, 'name' => $this->getI18n()->__('No QA responsible assigned')])]);
            }
        }

        /**
         * Configure project settings
         *
         * @param framework\Request $request The request object
         */
        public function runConfigureProjectSetting(framework\Request $request)
        {
            switch ($request['setting_key']) {
                case 'enable_editions':
                    $this->selected_project->setEditionsEnabled((bool)$request['value']);
                    $this->selected_project->save();

                    return $this->renderJSON(['item' => $this->selected_project->toJSON(false)]);

                    break;
                case 'enable_components':
                    $this->selected_project->setComponentsEnabled((bool)$request['value']);
                    $this->selected_project->save();

                    return $this->renderJSON(['item' => $this->selected_project->toJSON(false)]);

                    break;
                default:
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('Invalid setting')]);
            }
        }

        /**
         * Configure project settings
         *
         * @param framework\Request $request The request object
         */
        public function runConfigureProjectSettings(framework\Request $request)
        {
            if ($request->isPost()) {
                $this->forward403unless($this->getUser()->canEditProjectDetails($this->selected_project), Context::getI18n()->__('You do not have access to update these settings'));

                $old_key = $this->selected_project->getKey();

                if ($request->hasParameter('project_name')) {
                    if (trim($request['project_name']) == '') {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => Context::getI18n()->__('Please specify a valid project name')]);
                    } else {
                        $this->selected_project->setName($request['project_name']);
                    }
                }


                $message = ($old_key != $this->selected_project->getKey()) ? Context::getI18n()->__('%IMPORTANT: The project key has changed. Remember to replace the current url with the new project key', ['%IMPORTANT' => '<b>' . Context::getI18n()->__('IMPORTANT') . '</b>']) : '';

                if ($request->hasParameter('project_key')) {
                    $this->selected_project->setKey($request['project_key']);
                }

                if ($request->hasParameter('use_prefix'))
                    $this->selected_project->setUsePrefix((bool)$request['use_prefix']);

                if ($request->hasParameter('use_prefix') && $this->selected_project->doesUsePrefix()) {
                    if (!$this->selected_project->setPrefix($request['prefix'])) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => Context::getI18n()->__("Project prefixes may only contain letters and numbers")]);
                    }
                }

                if ($request->hasParameter('client')) {
                    if ($request['client'] == 0) {
                        $this->selected_project->setClient(null);
                    } else {
                        $this->selected_project->setClient(tables\Clients::getTable()->selectById($request['client']));
                    }
                }

                if ($request->hasParameter('subproject_id')) {
                    if ($request['subproject_id'] == 0) {
                        $this->selected_project->clearParent();
                    } else {
                        $this->selected_project->setParent(tables\Projects::getTable()->selectById($request['subproject_id']));
                    }
                }

                if ($request->hasParameter('workflow_scheme')) {
                    try {
                        $workflow_scheme = tables\WorkflowSchemes::getTable()->selectById($request['workflow_scheme']);
                        $this->selected_project->setWorkflowScheme($workflow_scheme);
                    } catch (Exception $e) {

                    }
                }

                if ($request->hasParameter('issuetype_scheme')) {
                    try {
                        $issuetype_scheme = tables\IssuetypeSchemes::getTable()->selectById($request['issuetype_scheme']);
                        $this->selected_project->setIssuetypeScheme($issuetype_scheme);
                    } catch (Exception $e) {

                    }
                }

                if ($request->hasParameter('description'))
                    $this->selected_project->setDescription($request->getParameter('description', null, false));

                if ($request->hasParameter('homepage'))
                    $this->selected_project->setHomepage($request['homepage']);

                if ($request->hasParameter('doc_url'))
                    $this->selected_project->setDocumentationURL($request['doc_url']);

                if ($request->hasParameter('wiki_url'))
                    $this->selected_project->setWikiURL($request['wiki_url']);

                if ($request->hasParameter('released'))
                    $this->selected_project->setReleased((int)$request['released']);

                if ($request->hasParameter('locked'))
                    $this->selected_project->setLocked((bool)$request['locked']);

                if ($request->hasParameter('issues_lock_type'))
                    $this->selected_project->setIssuesLockType($request['issues_lock_type']);

                if ($request->hasParameter('enable_builds'))
                    $this->selected_project->setBuildsEnabled((bool)$request['enable_builds']);

                if ($request->hasParameter('enable_editions'))
                    $this->selected_project->setEditionsEnabled((bool)$request['enable_editions']);

                if ($request->hasParameter('enable_components'))
                    $this->selected_project->setComponentsEnabled((bool)$request['enable_components']);

                if ($request->hasParameter('strict_workflow_mode'))
                    $this->selected_project->setStrictWorkflowMode((bool)$request['strict_workflow_mode']);

                if ($request->hasParameter('mark_as_owner'))
                    $this->selected_project->setOwner($this->getUser());

                $apply_template = (!$this->selected_project->getID());

                try {
                    $this->selected_project->save();

                    if ($request->hasParameter('assignee_id')) {
                        $assignee = ($request['assignee_type'] == 'user') ? tables\Users::getTable()->selectById($request['assignee_id']) : tables\Teams::getTable()->selectById($request['assignee_id']);
                        if ($request->hasParameter('role_id') && $request['role_id']) {
                            $assignee_role = new entities\Role($request['role_id']);
                            $this->selected_project->addAssignee($assignee, $assignee_role);
                        }
                    }

                    if ($apply_template) {
                        $this->selected_project->applyTemplate($request['project_type']);
                        $this->selected_project->save();
                    }

                    framework\Event::createNew('core', 'projectActions::configureProjectSettings::postSave', $this->selected_project)->trigger(['request' => $request]);

                    $response = ['message' => $this->getI18n()->__('Settings saved')];

                    if (!$request['project_id'] && !$request['project_key']) {
                        return $this->forward($this->getRouting()->generate('project_dashboard', ['project_key' => $this->selected_project->getKey()]));
                    }
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);
                    $response = ['message' => $e->getMessage()];
                }

                return $this->renderJSON($response);
            }
        }

        /**
         * Add an edition (AJAX call)
         *
         * @param framework\Request $request The request object
         */
        public function runAddEdition(framework\Request $request)
        {
            $i18n = Context::getI18n();

            if ($this->getUser()->canEditProjectDetails($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project)) {
                try {
                    if (Context::getUser()->canManageProjectReleases($this->selected_project)) {
                        if (($e_name = $request['e_name']) && trim($e_name) != '') {
                            if (in_array($e_name, $this->selected_project->getEditions())) {
                                throw new Exception($i18n->__('This edition already exists for this project'));
                            }
                            $edition = $this->selected_project->addEdition($e_name);

                            return $this->renderJSON(['html' => $this->getComponentHTML('editionbox', ['edition' => $edition, 'access_level' => framework\Settings::ACCESS_FULL])]);
                        } else {
                            throw new Exception($i18n->__('You need to specify a name for the new edition'));
                        }
                    } else {
                        throw new Exception($i18n->__('You do not have access to this project'));
                    }
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $i18n->__('The edition could not be added') . ", " . $e->getMessage()]);
                }
            }
            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['error' => $i18n->__("You don't have access to add project editions")]);
        }

        /**
         * Perform actions on a build (AJAX call)
         *
         * @param framework\Request $request The request object
         */
        public function runDeleteBuild(framework\Request $request)
        {
            $i18n = Context::getI18n();

            try {
                if ($this->getUser()->canManageProjectReleases($this->selected_project)) {
                    if ($b_id = $request['build_id']) {
                        $build = tables\Builds::getTable()->selectById($b_id);
                        if ($build->hasAccess()) {
                            $build->delete();

                            return $this->renderJSON(['deleted' => true, 'message' => $i18n->__('The release was deleted')]);
                        } else {
                            throw new Exception($i18n->__('You do not have access to this release'));
                        }
                    } else {
                        throw new Exception($i18n->__('You need to specify a release'));
                    }
                } else {
                    throw new Exception($i18n->__("You don't have access to manage releases"));
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Add a build (AJAX call)
         *
         * @param framework\Request $request The request object
         */
        public function runProjectBuild(framework\Request $request)
        {
            $i18n = Context::getI18n();

            try {
                if (!$this->getUser()->canManageProjectReleases($this->selected_project)) {
                    throw new Exception($i18n->__('You do not have access to manage project releases'));
                }
                if (trim($request['name']) == '') {
                    throw new Exception($i18n->__('You need to specify a name for the release'));
                }

                if (!$request['build_id']) {
                    $build = new entities\Build();
                } else {
                    $build = tables\Builds::getTable()->selectById($request['build_id']);
                }

                if (!$build instanceof entities\Build) {
                    throw new Exception('This release does not exist');
                }

                $build->setName($request['name']);
                $build->setVersion($request->getParameter('ver_mj', 0), $request->getParameter('ver_mn', 0), $request->getParameter('ver_rev', 0));
                $build->setReleased((bool) $request['released']);
                $build->setLocked((bool) $request['locked']);
                $build->setActive((bool) $request['active']);
                $build->setFileURL($request['file_url']);
                $build->setReleaseDate($request['date']);
                $build->setProject($this->selected_project);

                if ($request['milestone'] && $milestone = Milestones::getTable()->selectById($request['milestone'])) {
                    $build->setMilestone($milestone);
                } else {
                    $build->clearMilestone();
                }
                if ($request['edition'] && $edition = tables\Editions::getTable()->selectById($request['edition'])) {
                    $build->setEdition($edition);
                } else {
                    $build->clearEdition();
                }

                $build->save();

                if ($request->hasParameter('files')) {
                    $file_ids = $request->getParameter('files');
                    foreach ($file_ids as $file_id) {
                        BuildFiles::getTable()->addByBuildIDandFileID($build->getID(), $file_id);
                    }
                }
                return $this->renderJSON(['build' => $build->toJSON(), 'component' => $this->getComponentHTML('project/release', ['build' => $build])]);

            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Add or remove a component to/from an edition (AJAX call)
         *
         * @param framework\Request $request The request object
         */
        public function runEditEditionComponent(framework\Request $request)
        {
            $i18n = Context::getI18n();

            if ($this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project)) {
                try {
                    $edition = tables\Editions::getTable()->selectById($request['edition_id']);
                    if ($request['mode'] == 'add') {
                        $edition->addComponent($request['component_id']);
                    } elseif ($request['mode'] == 'remove') {
                        $edition->removeComponent($request['component_id']);
                    }

                    return $this->renderJSON('ok');
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $i18n->__('The component could not be added to this edition') . ", " . $e->getMessage()]);
                }
            }
            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['error' => $i18n->__("You don't have access to modify components")]);
        }

        /**
         * Delete a component
         *
         * @param framework\Request $request The request object
         */
        public function runDeleteComponent(framework\Request $request)
        {
            $i18n = Context::getI18n();
            $can_manage_components = $this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project);

            if (!$can_manage_components) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__("You don't have access to modify components")]);
            }

            if (!$request['component_id']) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__("Can't remove this component")]);
            }

            $component = tables\Components::getTable()->selectById($request['component_id']);

            if (!$component instanceof entities\Component) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('Could not remove this component')]);
            }

            $component->delete();

            return $this->renderJSON(['removed' => 'ok']);
        }

        /**
         * Edit a component
         *
         * @param framework\Request $request The request object
         */
        public function runEditComponent(framework\Request $request)
        {
            $i18n = Context::getI18n();
            $can_manage_components = $this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project);

            if (!$can_manage_components) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__("You don't have access to modify components")]);
            }

            try {
                if ($request['component_id']) {
                    $component = tables\Components::getTable()->selectById($request['component_id']);
                } else {
                    $component = new entities\Component();
                    $component->setProject($this->selected_project);
                }

                if (!$component instanceof entities\Component) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => Context::getI18n()->__('Could not edit this component')]);
                }

                $name = trim($request['name']);
                if (!$name) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => Context::getI18n()->__('Please provide a valid name')]);
                }
                $component->setName($name);
                $component->save();

                $content = $this->getComponentHTML('project/component', ['component' => $component, 'access_level' => $this->access_level]);

                return $this->renderJSON(['component' => $content, 'item' => $component->toJSON()]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('Could not edit this component')]);
            }
        }

        /**
         * Delete an edition
         *
         * @param framework\Request $request The request object
         */
        public function runDeleteEdition(framework\Request $request)
        {
            $i18n = Context::getI18n();
            $can_manage_editions = $this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project);

            if (!$can_manage_editions) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__("You don't have access to modify editions")]);
            }

            if (!$request['edition_id']) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__("Can't remove this edition")]);
            }

            $edition = tables\Editions::getTable()->selectById($request['edition_id']);

            if (!$edition instanceof entities\Edition) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('Could not remove this edition')]);
            }

            $edition->delete();

            return $this->renderJSON(['removed' => 'ok']);
        }

        public function runEditEdition(framework\Request $request)
        {
            $i18n = Context::getI18n();
            $can_manage_editions = $this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project);

            if (!$can_manage_editions) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__("You don't have access to modify editions")]);
            }

            try {
                if ($request['edition_id']) {
                    $edition = tables\Editions::getTable()->selectById($request['edition_id']);
                } else {
                    $edition = new entities\Edition();
                    $edition->setProject($this->selected_project);
                }

                if (!$edition instanceof entities\Edition) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => Context::getI18n()->__('Could not edit this edition')]);
                }

                if (!$request->isPost()) {
                    return $this->renderJSON(['content' => $this->getComponentHTML('project/editedition', ['edition' => $edition, 'access_level' => $this->access_level])]);
                }

                $name = trim($request['name']);
                $description = trim($request['description']);
                $url = trim($request['documentation_url']);

                if (!$name) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => Context::getI18n()->__('Please provide a valid name')]);
                }
                $edition->setName($name);
                $edition->setDescription($description);
                $edition->setDocumentationURL($url);
                $edition->save();

                if ($request->hasParameter('components')) {
                    $components = $request['components'];
                    if (is_array($components)) {
                        foreach ($edition->getProject()->getComponents() as $component) {
                            $hasComponent = $edition->hasComponent($component);

                            if (array_key_exists($component->getID(), $components)) {
                                if (!$hasComponent) {
                                    $edition->addComponent($component);
                                }
                            } elseif ($hasComponent) {
                                $edition->removeComponent($component);
                            }
                        }
                    }
                }

                $content = $this->getComponentHTML('project/edition', ['edition' => $edition, 'access_level' => $this->access_level]);

                return $this->renderJSON(['edition' => $content, 'item' => $edition->toJSON()]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('Could not edit this edition: %error', ['%error' => $e->getMessage()])]);
            }
        }

        public function runGetUpdatedProjectKey(framework\Request $request)
        {
            try {
                if ($request['project_id']) {
                    $this->selected_project = tables\Projects::getTable()->selectById($request['project_id']);
                } else {
                    $this->selected_project = new entities\Project();
                }
            } catch (Exception $e) {

            }

            if (!$this->selected_project instanceof entities\Project)
                return $this->return404(Context::getI18n()->__("This project doesn't exist"));

            $this->selected_project->setName($request['project_name']);

            return $this->renderJSON(['new_values' => ['project_key' => $this->selected_project->getKey()]]);
        }

        public function runUnassignFromProject(framework\Request $request)
        {
            if ($this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project)) {
                try {
                    $assignee = ($request['assignee_type'] == 'user') ? new entities\User($request['assignee_id']) : new entities\Team($request['assignee_id']);
                    $this->selected_project->removeAssignee($assignee);

                    return $this->renderJSON(['message' => Context::getI18n()->__('The assignee has been removed'), 'assignee_type' => $request['assignee_type'], 'assignee_id' => $request['assignee_id']]);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['message' => $e->getMessage()]);
                }
            }
            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['error' => $this->getI18n()->__("You don't have access to perform this action")]);
        }

        /**
         * @Route(url="/configure/project/:project_id/icons/:csrf_token", name="configure_icons")
         * @CsrfProtected
         *
         * @param framework\Request $request
         */
        public function runProjectIcons(framework\Request $request)
        {
            if (!$this->getUser()->canManageProject($this->selected_project)) {
                return $this->forward403($this->getI18n()->__("You don't have access to perform this action"));
            }

            if ($request->isPost()) {
                if ($request['file_id']) {
                    $file = tables\Files::getTable()->selectById($request['file_id']);
                    $this->selected_project->setIcon($file);
                } else {
                    $this->selected_project->setIcon(null);
                    $this->selected_project->setIconName($request['project_icon']);
                }
                $this->selected_project->save();
            }

            if ($request->isResponseFormatAccepted('application/json', false)) {
                return $this->renderJSON(['project' => $this->selected_project->toJSON(false)]);
            }

            $route = $this->getRouting()->generate('project_settings', ['project_key' => $this->selected_project->getKey()]);
            $this->forward($route);
        }

        public function runProjectWorkflow(framework\Request $request)
        {
            if ($this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project)) {
                try {
                    foreach ($this->selected_project->getIssuetypeScheme()->getIssuetypes() as $type) {
                        $data = [];
                        foreach ($this->selected_project->getWorkflowScheme()->getWorkflowForIssuetype($type)->getSteps() as $step) {
                            $data[] = [(string)$step->getID(), $request->getParameter('new_step_' . $type->getID() . '_' . $step->getID())];
                        }
                        $this->selected_project->convertIssueStepPerIssuetype($type, $data);
                    }

                    $this->selected_project->setWorkflowScheme(tables\WorkflowSchemes::getTable()->selectById($request['workflow_id']));
                    $this->selected_project->save();

                    return $this->renderJSON(['message' => Context::geti18n()->__('Workflow scheme changed and issues updated')]);
                } catch (Exception $e) {
                    $this->getResponse()->setHTTPStatus(400);

                    return $this->renderJSON(['error' => Context::geti18n()->__('An internal error occured')]);
                }
            }
            $this->getResponse()->setHTTPStatus(400);

            return $this->renderJSON(['error' => Context::geti18n()->__("You don't have access to perform this action")]);
        }

        public function runProjectWorkflowTable(framework\Request $request)
        {
            $this->selected_project = tables\Projects::getTable()->selectById($request['project_id']);
            if ($request->isPost()) {
                try {
                    $workflow_scheme = tables\WorkflowSchemes::getTable()->selectById($request['new_workflow']);

                    return $this->renderJSON(['content' => $this->getComponentHTML('projectworkflow_table', ['project' => $this->selected_project, 'new_workflow' => $workflow_scheme])]);
                } catch (Exception $e) {
                    $this->getResponse()->setHTTPStatus(400);

                    return $this->renderJSON(['error' => Context::geti18n()->__('This workflow scheme is not valid')]);
                }
            }
        }

        public function runAddRole(framework\Request $request)
        {
            if ($this->getUser()->canManageProject($this->selected_project)) {
                if ($request['role_name']) {
                    $role = new entities\Role();
                    $role->setName($request['role_name']);
                    $role->setProject($this->selected_project);
                    $role->save();

                    return $this->renderJSON(['content' => $this->getComponentHTML('configuration/role', ['role' => $role])]);
                }
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['message' => $this->getI18n()->__('You must provide a role name')]);
            }
            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['message' => $this->getI18n()->__('You do not have access to create new project roles')]);
        }

        public function listen_issueCreate(framework\Event $event)
        {
            $request = Context::getRequest();
            $issue = $event->getSubject();

            if ($issue->isUnlocked()) {
                $this->_unlockIssueAfter($request, $issue);
            } elseif ($issue->isLocked()) {
                $this->_lockIssueAfter($request, $issue);
            }
        }

        /**
         * @param framework\Request $request
         * @param                   $issue
         */
        protected function _unlockIssueAfter(framework\Request $request, $issue)
        {
            tables\Permissions::getTable()->deleteByPermissionTargetIDAndModule('canaccessrestrictedissues', $issue->getID());

            $al_users = $request->getParameter('access_list_users', []);
            $al_teams = $request->getParameter('access_list_teams', []);
            $i_al = $issue->getAccessList();
            foreach ($i_al as $k => $item) {
                if ($item['target'] instanceof entities\Team) {
                    $team_id = $item['target']->getID();
                    if (array_key_exists($team_id, $al_teams)) {
                        unset($i_al[$k]);
                    }
                } elseif ($item['target'] instanceof entities\User) {
                    $user_id = $item['target']->getID();
                    if (array_key_exists($user_id, $al_users)) {
                        unset($i_al[$k]);
                    }
                }
            }
            foreach ($al_users as $user_id) {
                Context::setPermission('canaccessrestrictedissues', $issue->getID(), 'core', $user_id, 0, 0);
            }
            foreach ($al_teams as $team_id) {
                Context::setPermission('canaccessrestrictedissues', $issue->getID(), 'core', 0, 0, $team_id);
            }
        }

    }
