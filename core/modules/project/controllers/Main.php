<?php

    namespace pachno\core\modules\project\controllers;

    use Exception;
    use InvalidArgumentException;
    use pachno\core\entities;
    use pachno\core\entities\tables;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\framework;
    use pachno\core\framework\Context;
    use pachno\core\helpers;

    /**
     * actions for the project module
     *
     * @property entities\Client $selected_client
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
            $this->forward403unless($this->_checkProjectPageAccess('project_dashboard'));

            if ($request->isPost() && $request['setup_default_dashboard'] && $this->getUser()->canEditProjectDetails($this->selected_project)) {
                entities\DashboardView::getB2DBTable()->setDefaultViews($this->selected_project->getID(), entities\DashboardView::TYPE_PROJECT);
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
         *
         * @param framework\Request $request
         */
        public function runRoadmap(framework\Request $request)
        {
            $this->mode = $request->getParameter('mode', 'upcoming');
            if ($this->mode == 'milestone' && $request['milestone_id']) {
                $this->selected_milestone = Milestones::getTable()->selectById((int)$request['milestone_id']);
            }
            $this->forward403unless($this->_checkProjectPageAccess('project_roadmap'));
            $this->milestones = $this->selected_project->getMilestonesForRoadmap();
        }

        /**
         * The project planning page
         *
         * @param framework\Request $request
         */
        public function runTimeline(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_timeline'));
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
         * @Route(url="/:project_key/milestones/sort/:csrf_token", name="project_sort_milestones")
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
         * The project scrum page
         *
         * @param framework\Request $request
         */
        public function runMilestoneDetails(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_scrum'));
            $milestone = null;
            if ($m_id = $request['milestone_id']) {
                $milestone = Milestones::getTable()->selectById((int)$m_id);
            }

            return $this->renderComponent('project/milestonedetails', compact('milestone'));
        }

        /**
         * Show the scrum burndown chart for a specified sprint
         *
         * @param framework\Request $request
         */
        public function runScrumShowBurndownImage(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_scrum'));

            $milestone = null;
            $maxEstimation = 0;

            if ($m_id = $request['sprint_id']) {
                $milestone = entities\Milestone::getB2DBTable()->selectById($m_id);
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
            $this->forward403unless($this->_checkProjectPageAccess('project_scrum'));
            $issue = entities\Issue::getB2DBTable()->selectById((int)$request['story_id']);
            try {
                if ($issue instanceof entities\Issue) {
                    switch ($request['detail']) {
                        case 'color':
                            $this->forward403unless($issue->canEditColor());
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
            $this->forward403unless($this->_checkProjectPageAccess('project_scrum'));
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
            $this->forward403unless($this->_checkProjectPageAccess('project_issues'));
        }

        /**
         * The project team page
         *
         * @param framework\Request $request
         */
        public function runTeam(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_team'));
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
            $this->forward403unless($this->_checkProjectPageAccess('project_statistics'));
        }

        public function runStatisticsLast15(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_statistics'));

            if (!function_exists('imagecreatetruecolor')) {
                return $this->return404(Context::getI18n()->__('The libraries to generate images are not installed. Please see https://pachno.com for more information'));
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
            $this->forward403unless($this->_checkProjectPageAccess('project_statistics'));
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
            $this->forward403unless($this->_checkProjectPageAccess('project_statistics'));

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
                                $item = entities\Status::getB2DBTable()->selectById($item_id);
                                break;
                            case 'issues_per_priority':
                                $item = entities\Priority::getB2DBTable()->selectById($item_id);
                                break;
                            case 'issues_per_category':
                                $item = entities\Category::getB2DBTable()->selectById($item_id);
                                break;
                            case 'issues_per_resolution':
                                $item = entities\Resolution::getB2DBTable()->selectById($item_id);
                                break;
                            case 'issues_per_reproducability':
                                $item = entities\Reproducability::getB2DBTable()->selectById($item_id);
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

        public function runMenuLinks(framework\Request $request)
        {

        }

        public function runTransitionIssue(framework\Request $request)
        {
            try {
                $transition = entities\WorkflowTransition::getB2DBTable()->selectById($request['transition_id']);
                $issue = entities\Issue::getB2DBTable()->selectById((int)$request['issue_id']);
                if (!$issue->isWorkflowTransitionsAvailable()) {
                    throw new Exception(Context::getI18n()->__('You are not allowed to perform any workflow transitions on this issue'));
                }

                if ($transition->validateFromRequest($request)) {
                    $transition->transitionIssueToOutgoingStepFromRequest($issue, $request);
                } else {
                    Context::setMessage('issue_error', 'transition_error');
                    Context::setMessage('issue_workflow_errors', $transition->getValidationErrors());

                    if ($request->isResponseFormatAccepted('application/json', false)) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => Context::getI18n()->__('There was an error trying to move this issue to the next step in the workflow'), 'message' => preg_replace('/\s+/', ' ', $this->getComponentHTML('main/issue_transition_error'))]);
                    }
                }
                $this->forward(Context::getRouting()->generate('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]));
            } catch (Exception $e) {
                return $this->return404();
            }
        }

        public function runTransitionIssues(framework\Request $request)
        {
            try {
                try {
                    $transition = entities\WorkflowTransition::getB2DBTable()->selectById($request['transition_id']);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('This is not a valid transition')]);
                }
                $issue_ids = $request['issue_ids'];
                $status = null;
                $closed = false;
                foreach ($issue_ids as $issue_id) {
                    $issue = entities\Issue::getB2DBTable()->selectById((int)$issue_id);
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
                    if ($status === null)
                        $status = $issue->getStatus();

                    $closed = $issue->isClosed();
                }

                Context::loadLibrary('common');
                $options = ['issue_ids' => array_keys($issue_ids), 'last_updated' => Context::getI18n()->formatTime(time(), 20), 'closed' => $closed];
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

        public function runReleaseCenter(framework\Request $request)
        {
            $this->forward403if(Context::getCurrentProject()->isArchived() || !$this->getUser()->canManageProjectReleases(Context::getCurrentProject()));
            $this->build_error = Context::getMessageAndClear('build_error');
        }

        public function runReleases(framework\Request $request)
        {
            $this->_setupBuilds();
        }

        protected function _setupBuilds()
        {
            $builds = $this->selected_project->getBuilds();

            $active_builds = [0 => []];
            $archived_builds = [0 => []];

            foreach ($this->selected_project->getEditions() as $edition_id => $edition) {
                $active_builds[$edition_id] = [];
                $archived_builds[$edition_id] = [];
            }

            foreach ($builds as $build) {
                if ($build->isLocked())
                    $archived_builds[$build->getEditionID()][$build->getID()] = $build;
                else
                    $active_builds[$build->getEditionID()][$build->getID()] = $build;
            }

            $this->active_builds = $active_builds;
            $this->archived_builds = $archived_builds;
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
            if ($find_by) {
                $this->selected_project = entities\Project::getB2DBTable()->selectById($request['project_id']);
                $this->users = tables\Users::getTable()->getByDetails($find_by, 10, true);
                $this->teams = tables\Teams::getTable()->quickfind($find_by);
                $this->global_roles = entities\Role::getGlobalRoles();
                $this->project_roles = entities\Role::getByProjectID($this->selected_project->getID());

                if (filter_var($find_by, FILTER_VALIDATE_EMAIL) == $find_by) {
                    $this->email = $find_by;
                }
            } else {
                $this->message = true;
            }
        }

        /**
         * Adds a user or team to a project
         *
         * @param framework\Request $request The request object
         */
        public function runAssignToProject(framework\Request $request)
        {
            if ($this->getUser()->canEditProjectDetails($this->selected_project)) {
                $assignee_type = $request['assignee_type'];
                $assignee_id = $request['assignee_id'];

                try {
                    switch ($assignee_type) {
                        case 'user':
                            $assignee = entities\User::getB2DBTable()->selectById($assignee_id);
                            break;
                        case 'team':
                            $assignee = entities\Team::getB2DBTable()->selectById($assignee_id);
                            break;
                        default:
                            throw new Exception('Invalid assignee');
                    }
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => Context::getI18n()->__('An error occurred when trying to assign user/team to this project')]);
                }

                $assignee_role = new entities\Role($request['role_id']);
                $this->selected_project->addAssignee($assignee, $assignee_role);

                return $this->renderComponent('projects_assignees', ['project' => $this->selected_project]);
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
                        $item = entities\Project::getB2DBTable()->selectById($request['project_id']);
                        break;
                    case 'edition':
                        $item = entities\Edition::getB2DBTable()->selectById($request['edition_id']);
                        break;
                    case 'component':
                        $item = entities\Component::getB2DBTable()->selectById($request['component_id']);
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
                                $identified = entities\User::getB2DBTable()->selectById($request['value']);
                                break;
                            case 'team':
                                $identified = entities\Team::getB2DBTable()->selectById($request['value']);
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
                    return $this->renderJSON(['field' => (($item->hasOwner()) ? ['id' => $item->getOwner()->getID(), 'name' => (($item->getOwner() instanceof entities\User) ? $this->getComponentHTML('main/userdropdown', ['user' => $item->getOwner()]) : $this->getComponentHTML('main/teamdropdown', ['team' => $item->getOwner()]))] : ['id' => 0])]);
                elseif ($request['field'] == 'lead_by')
                    return $this->renderJSON(['field' => (($item->hasLeader()) ? ['id' => $item->getLeader()->getID(), 'name' => (($item->getLeader() instanceof entities\User) ? $this->getComponentHTML('main/userdropdown', ['user' => $item->getLeader()]) : $this->getComponentHTML('main/teamdropdown', ['team' => $item->getLeader()]))] : ['id' => 0])]);
                elseif ($request['field'] == 'qa_by')
                    return $this->renderJSON(['field' => (($item->hasQaResponsible()) ? ['id' => $item->getQaResponsible()->getID(), 'name' => (($item->getQaResponsible() instanceof entities\User) ? $this->getComponentHTML('main/userdropdown', ['user' => $item->getQaResponsible()]) : $this->getComponentHTML('main/teamdropdown', ['team' => $item->getQaResponsible()]))] : ['id' => 0])]);
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
                        $this->selected_project->setClient(entities\Client::getB2DBTable()->selectById($request['client']));
                    }
                }

                if ($request->hasParameter('subproject_id')) {
                    if ($request['subproject_id'] == 0) {
                        $this->selected_project->clearParent();
                    } else {
                        $this->selected_project->setParent(entities\Project::getB2DBTable()->selectById($request['subproject_id']));
                    }
                }

                if ($request->hasParameter('workflow_scheme')) {
                    try {
                        $workflow_scheme = entities\WorkflowScheme::getB2DBTable()->selectById($request['workflow_scheme']);
                        $this->selected_project->setWorkflowScheme($workflow_scheme);
                    } catch (Exception $e) {

                    }
                }

                if ($request->hasParameter('issuetype_scheme')) {
                    try {
                        $issuetype_scheme = entities\IssuetypeScheme::getB2DBTable()->selectById($request['issuetype_scheme']);
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
                        $build = entities\Build::getB2DBTable()->selectById($b_id);
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

            if ($this->getUser()->canManageProjectReleases($this->selected_project)) {
                try {
                    if (Context::getUser()->canManageProjectReleases($this->selected_project)) {
                        if (($b_name = $request['build_name']) && trim($b_name) != '') {
                            $build = new entities\Build($request['build_id']);
                            $build->setName($b_name);
                            $build->setVersion($request->getParameter('ver_mj', 0), $request->getParameter('ver_mn', 0), $request->getParameter('ver_rev', 0));
                            $build->setReleased((bool)$request['isreleased']);
                            $build->setLocked((bool)$request['locked']);
                            if ($request['milestone'] && $milestone = entities\Milestone::getB2DBTable()->selectById($request['milestone'])) {
                                $build->setMilestone($milestone);
                            } else {
                                $build->clearMilestone();
                            }
                            if ($request['edition'] && $edition = entities\Edition::getB2DBTable()->selectById($request['edition'])) {
                                $build->setEdition($edition);
                            } else {
                                $build->clearEdition();
                            }
                            $release_date = null;
                            if ($request['has_release_date']) {
                                $release_date = mktime($request['release_hour'], $request['release_minute'], 1, $request['release_month'], $request['release_day'], $request['release_year']);
                            }
                            $build->setReleaseDate($release_date);
                            switch ($request->getParameter('download', 'leave_file')) {
                                case '0':
                                    $build->clearFile();
                                    $build->setFileURL('');
                                    break;
                                case 'upload_file':
                                    if ($build->hasFile()) {
                                        $build->getFile()->delete();
                                        $build->clearFile();
                                    }
                                    $file = Context::getRequest()->handleUpload('upload_file');
                                    $build->setFile($file);
                                    $build->setFileURL('');
                                    break;
                                case 'url':
                                    $build->clearFile();
                                    $build->setFileURL($request['file_url']);
                                    break;
                            }

                            if (!$build->getID())
                                $build->setProject($this->selected_project);

                            $build->save();
                        } else {
                            throw new Exception($i18n->__('You need to specify a name for the release'));
                        }
                    } else {
                        throw new Exception($i18n->__('You do not have access to this project'));
                    }
                } catch (Exception $e) {
                    Context::setMessage('build_error', $e->getMessage());
                }
                $this->forward(Context::getRouting()->generate('project_release_center', ['project_key' => $this->selected_project->getKey()]));
            }

            return $this->forward403($i18n->__("You don't have access to add releases"));
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
                    $edition = entities\Edition::getB2DBTable()->selectById($request['edition_id']);
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
                    $this->selected_project = entities\Project::getB2DBTable()->selectById($request['project_id']);
                } else {
                    $this->selected_project = new entities\Project();
                }
            } catch (Exception $e) {

            }

            if (!$this->selected_project instanceof entities\Project)
                return $this->return404(Context::getI18n()->__("This project doesn't exist"));

            $this->selected_project->setName($request['project_name']);

            return $this->renderJSON(['content' => $this->selected_project->getKey()]);
        }

        public function runUnassignFromProject(framework\Request $request)
        {
            if ($this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project)) {
                try {
                    $assignee = ($request['assignee_type'] == 'user') ? new entities\User($request['assignee_id']) : new entities\Team($request['assignee_id']);
                    $this->selected_project->removeAssignee($assignee);

                    return $this->renderJSON(['message' => Context::getI18n()->__('The assignee has been removed')]);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['message' => $e->getMessage()]);
                }
            }
            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['error' => $this->getI18n()->__("You don't have access to perform this action")]);
        }

        /**
         * @Route(url="/configure/project/:project_id/icons/:csrf_token", name="configure_projects_icons")
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

                    $this->selected_project->setWorkflowScheme(entities\WorkflowScheme::getB2DBTable()->selectById($request['workflow_id']));
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
            $this->selected_project = entities\Project::getB2DBTable()->selectById($request['project_id']);
            if ($request->isPost()) {
                try {
                    $workflow_scheme = entities\WorkflowScheme::getB2DBTable()->selectById($request['new_workflow']);

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
            tables\Permissions::getTable()
                ->deleteByPermissionTargetIDAndModule('canviewissue', $issue->getID());

            $al_users = $request->getParameter('access_list_users', []);
            $al_teams = $request->getParameter('access_list_teams', []);
            $i_al = $issue->getAccessList();
            foreach ($i_al as $k => $item) {
                if ($item['target'] instanceof entities\Team) {
                    $tid = $item['target']->getID();
                    if (array_key_exists($tid, $al_teams)) {
                        unset($i_al[$k]);
                    }
                } elseif ($item['target'] instanceof entities\User) {
                    $uid = $item['target']->getID();
                    if (array_key_exists($uid, $al_users)) {
                        unset($i_al[$k]);
                    }
                }
            }
            foreach ($al_users as $uid) {
                Context::setPermission('canviewissue', $issue->getID(), 'core', $uid, 0, 0, true);
            }
            foreach ($al_teams as $tid) {
                Context::setPermission('canviewissue', $issue->getID(), 'core', 0, 0, $tid, true);
            }
        }

    }
