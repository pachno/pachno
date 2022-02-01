<?php

    namespace pachno\core\modules\agile\controllers;

    use b2db\Criterion;
    use Exception;
    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\BoardColumn;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Milestone;
    use pachno\core\entities\Permission;
    use pachno\core\entities\SavedSearch;
    use pachno\core\entities\SearchFilter;
    use pachno\core\entities\tables\AgileBoards;
    use pachno\core\entities\tables\BoardColumns;
    use pachno\core\entities\tables\Builds;
    use pachno\core\entities\tables\Issues;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\tables\WorkflowTransitions;
    use pachno\core\framework;
    use pachno\core\framework\Context;
    use pachno\core\framework\Request;
    use pachno\core\helpers;

    /**
     * Actions for the agile module
     *
     * @property AgileBoard $board
     * @property Milestone $selected_milestone
     *
     * @Routes(name_prefix="agile_", url_prefix="/:project_key/agile")
     */
    class Main extends helpers\ProjectActions
    {
    
        /**
         * Action for marking a milestone as completed, optionally moving issues across to a new milestone
         *
         * @Route(url="/boards/:board_id/milestone/:milestone_id/markfinished")
         *
         * @param Request $request
         * @return framework\JsonOutput
         */
        public function runMarkMilestoneFinished(Request $request): framework\JsonOutput
        {
            try {
                if (!($this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project))) {
                    throw new Exception($this->getI18n()->__("You don't have access to modify milestones"));
                }
                $board = AgileBoards::getTable()->selectById($request['board_id']);
                $milestone = Milestones::getTable()->selectById($request['milestone_id']);
                $reached_date = mktime(23, 59, 59, Context::getRequest()->getParameter('milestone_finish_reached_month'), Context::getRequest()->getParameter('milestone_finish_reached_day'), Context::getRequest()->getParameter('milestone_finish_reached_year'));
                $milestone->setReachedDate($reached_date);
                $milestone->setReached();
                $milestone->setClosed(true);
                $milestone->save();
                $return_options = [
                    'milestone' => $milestone->toJSON(),
                    'finished' => 'ok',
                    'unresolved_action' => $request['unresolved_issues_action']
                ];
                switch ($request['unresolved_issues_action']) {
                    case 'backlog':
                        Issues::getTable()->reAssignIssuesByMilestoneIds($milestone->getID(), null, 0);
                        break;
                    case 'reassign':
                        $new_milestone = Milestones::getTable()->selectById($request['assign_issues_milestone_id']);
                        if ($request['assign_issues_milestone_id'] === '' || !$new_milestone instanceof Milestone || $new_milestone->isClosed()) {
                            switch ($board->getType()) {
                                case AgileBoard::TYPE_GENERIC:
                                    throw new Exception($this->getI18n()->__('You must select an existing, unfinished milestone'));
                                case AgileBoard::TYPE_SCRUM:
                                case AgileBoard::TYPE_KANBAN:
                                    throw new Exception($this->getI18n()->__('You must select an existing, unfinished sprint'));
                            }
                        }
                        $return_options['new_milestone_id'] = $new_milestone->getID();
                        $return_options['number_of_reassigned_issues'] = $milestone->countOpenIssues();
                        break;
                    case 'add_new':
                        $new_milestone = $this->_saveMilestoneDetails($request);
                        $new_milestone->setClosed(false);
                        $new_milestone->setVisibleIssues(true);
                        $new_milestone->setVisibleRoadmap(true);
                        $new_milestone->save();
                        $return_options['number_of_reassigned_issues'] = $milestone->countOpenIssues();
                        break;
                }
                if (isset($new_milestone) && $new_milestone instanceof Milestone) {
                    Issues::getTable()->reAssignIssuesByMilestoneIds($milestone->getID(), $new_milestone->getID());
                    $return_options['new_milestone'] = $new_milestone->toJSON();
                }

                return $this->renderJSON($return_options);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * @param Request $request
         * @param ?Milestone $milestone
         *
         * @return null|Milestone
         * @throws Exception
         */
        protected function _saveMilestoneDetails(Request $request, Milestone $milestone = null)
        {
            if (!$request['name'])
                throw new Exception($this->getI18n()->__('You must provide a valid milestone name'));

            if ($milestone === null) $milestone = new Milestone();
            $milestone->setName($request['name']);
            $milestone->setProject($this->selected_project);
            $milestone->setStarting((bool) $request['is_starting']);
            $milestone->setScheduled((bool) $request['is_scheduled']);
            $milestone->setDescription($request['description']);
            $milestone->setVisibleRoadmap((bool) $request['visibility_roadmap']);
            $milestone->setVisibleIssues((bool) $request['visibility_issues']);
            $milestone->setType($request->getParameter('milestone_type', Milestone::TYPE_REGULAR));
            $milestone->setPercentageType($request->getParameter('percentage_type', Milestone::PERCENTAGE_TYPE_REGULAR));
            if ($request->hasParameter('sch_month') && $request->hasParameter('sch_day') && $request->hasParameter('sch_year')) {
                $scheduled_date = mktime(23, 59, 59, Context::getRequest()->getParameter('sch_month'), Context::getRequest()->getParameter('sch_day'), Context::getRequest()->getParameter('sch_year'));
                $milestone->setScheduledDate($scheduled_date);
            } else
                $milestone->setScheduledDate(0);

            if ($request->hasParameter('starting_month') && $request->hasParameter('starting_day') && $request->hasParameter('starting_year')) {
                $starting_date = mktime(0, 0, 1, Context::getRequest()->getParameter('starting_month'), Context::getRequest()->getParameter('starting_day'), Context::getRequest()->getParameter('starting_year'));
                $milestone->setStartingDate($starting_date);
            } else
                $milestone->setStartingDate(0);

            $milestone->save();

            return $milestone;
        }

        /**
         * The agile boards list
         *
         * @Route
         *
         * @param Request $request
         */
        public function runIndex(Request $request)
        {
            $boards = AgileBoards::getTable()->getAvailableProjectBoards($this->getUser()->getID(), $this->selected_project->getID());
            $project_boards = [];
            $user_boards = [];
            foreach ($boards as $board) {
                if ($board->isPrivate())
                    $user_boards[$board->getID()] = $board;
                else
                    $project_boards[$board->getID()] = $board;
            }
            $this->project_boards = $project_boards;
            $this->user_boards = $user_boards;
        }

        /**
         * The project planning page
         *
         * @Route(url="/boards/:board_id")
         *
         * @param Request $request
         */
        public function runBoard(Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            $this->board = ($request['board_id']) ? AgileBoards::getTable()->selectById($request['board_id']) : new AgileBoard();

            if (!$this->board instanceof AgileBoard) {
                return $this->return404();
            }

            if ($request->isDelete()) {
                $board_id = $this->board->getID();
                $this->board->delete();

                return $this->renderJSON(['message' => $this->getI18n()->__('The board has been deleted'), 'board' => ['id' => $board_id]]);
            } elseif ($request->isPost()) {
                if ($request->hasParameter('name')) {
                    $this->board->setName($request['name']);
                }
                $this->board->setType($request['type']);
                $this->board->setProject($this->selected_project);
                $this->board->setIsPrivate($request['is_private']);
                $this->board->setBackgroundColor($request['background_color']);
                $this->board->setUser(Context::getUser());

                if ($this->board->getId()) {
                    $this->board->setWorkflowEnforcementMode($request['workflow_enforcement_mode']);
                    $this->board->setDescription($request['description']);
                    $this->board->setEpicIssuetype($request['epic_issuetype_id']);
                    $this->board->setTaskIssuetype($request['task_issuetype_id']);
                    list($type, $id) = explode('_', $request['backlog_search']);
                    if ($type == 'predefined') {
                        $this->board->setAutogeneratedSearch($id);
                    } else {
                        $this->board->setBacklogSearch($id);
                    }
                    $this->board->setUseSwimlanes((bool) $request['use_swimlane'] != "");
                    if ($this->board->usesSwimlanes()) {
                        $details = $request['swimlane_' . $request['use_swimlane'] . '_details'];
                        $this->board->setSwimlaneType($request['use_swimlane']);
                        $this->board->setSwimlaneIdentifier($request['swimlane_' . $request['use_swimlane'] . '_identifier']);
                        if (isset($details[$this->board->getSwimlaneIdentifier()])) {
                            $this->board->setSwimlaneFieldValues($details[$this->board->getSwimlaneIdentifier()]);
                        }
                    } else {
                        $this->board->clearSwimlaneType();
                        $this->board->clearSwimlaneIdentifier();
                        $this->board->clearSwimlaneFieldValues();
                    }
                    $details = $request['issue_field_details'];
                    if (isset($details['issuetype'])) {
                        $this->board->setIssueFieldValues(explode(',', $details['issuetype']));
                    } else {
                        $this->board->clearIssueFieldValues();
                    }
                }
                $this->board->save();

                return $this->renderJSON([
                    'component' => $this->getComponentHTML('agile/boardbox', ['board' => $this->board]),
                    'board' => $this->board->toJSON(),
                    'saved' => 'ok'
                ]);
            }
        }
    
        /**
         * Whiteboard column edit
         *
         * @Route(url="/boards/:board_id/whiteboard/column/:column_id")
         *
         * @param Request $request
         * @return framework\JsonOutput
         */
        public function runWhiteboardColumn(Request $request): framework\JsonOutput
        {
            if (!$this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS)) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $this->getI18n()->__("You don't have access to perform this action")]);
            }

            $board = AgileBoards::getTable()->selectById($request['board_id']);
            if (!$board instanceof AgileBoard) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $this->getI18n()->__("You don't have access to this board or it doesn't exist")]);
            }
            
            if ($board->getUser()->getID() !== $this->getUser()->getID() && !$this->_checkProjectAccess(Permission::PERMISSION_MANAGE_PROJECT_BOARDS)) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $this->getI18n()->__("You don't have access to this board or it doesn't exist")]);
            }
            
            if ($request['column_id']) {
                $column = BoardColumns::getTable()->selectById($request['column_id']);
            } else {
                $column = new BoardColumn();
                $column->setBoard($board);
                $column->setSortOrder(count($board->getColumns()) + 1);
            }
            
            if ($request->isDelete()) {
                $column->delete();
                $board->reorderColumns(true);
                return $this->renderJSON([
                    'headers' => $this->getComponentHTML('agile/boardcolumnheaders', compact('board')),
                    'deleted' => 'ok'
                ]);
            }

            if ($request->isPost()) {
                $reorder = false;
                $old_sort_order = 0;
                if (!$column instanceof BoardColumn) {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => $this->getI18n()->__('There was an error trying to save column %column', ['%column' => $request['column_id']])]);
                }
    
                if ($request->hasParameter('name')) {
                    $column->setName($request['name']);
                }
                if ($request->hasParameter('sort_order')) {
                    $old_sort_order = $column->getSortOrder();
                    $column->setSortOrder($request['sort_order']);
                    $reorder = true;
                }
                if ($request->hasParameter('min_workitems')) {
                    $column->setMinWorkitems($request['min_workitems']);
                }
                if ($request->hasParameter('max_workitems')) {
                    $column->setMaxWorkitems($request['max_workitems']);
                }
                if ($request->hasParameter('status_id')) {
                    $column->setStatusIds([$request['status_id']]);
                } elseif ($request->hasParameter('status_ids')) {
                    $column->setStatusIds($request['status_ids']);
                }

                $column->save();
                if ($reorder) {
                    BoardColumns::getTable()->updateSortOrderByBoardId($board->getID(), $column->getSortOrder(), $old_sort_order, $column->getID());
                }
            }
            
            $columns = [];
            foreach ($board->getColumns() as $board_column) {
                $columns[] = $board_column->toJSON();
            }

            $options = [
                'component' => $this->getComponentHTML('agile/boardcolumnheader', compact('column')),
                'headers' => $this->getComponentHTML('agile/boardcolumnheaders', compact('board')),
                'columns' => $columns,
                'column' => $column->toJSON(),
            ];

            if ($request->isPost() && $request['milestone_id']) {
                $milestone = Milestones::getTable()->selectById((int)$request['milestone_id']);
                if ($milestone instanceof Milestone) {
                    $swimlanes_json = $board->toMilestoneJSON($milestone, $column->getID());
                    $options['swimlanes'] = $swimlanes_json['swimlanes'];
                }
            }

            return $this->renderJSON($options);
        }

        /**
         * The project board whiteboard page
         *
         * @Route(url="/boards/:board_id/whiteboard/issues/*")
         *
         * @param Request $request
         */
        public function runWhiteboardIssues(Request $request): framework\JsonOutput
        {
            $this->forward403unless($this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            $this->board = AgileBoards::getTable()->selectById($request['board_id']);

            $this->forward403unless($this->board instanceof AgileBoard);

            try {
                if ($request->isPost()) {
                    $issue_table = Issues::getTable();
                    $issue_ids = explode(',', $request['issue_ids']);
                    $orders = array_keys($issue_ids);
                    foreach ($issue_ids as $issue_id) {
                        $issue_table->setOrderByIssueId(array_pop($orders), $issue_id);
                    }
    
                    return $this->renderJSON(['sorted' => 'ok']);
                }
                
                $milestone = Milestones::getTable()->selectById((int)$request['milestone_id']);
                
                if ($request['mode'] == 'backlog') {
                    if ($request['milestone_id'] > 0 && $milestone instanceof Milestone) {
                        return $this->renderJSON(['milestone' => $milestone->toJSON(true)]);
                    } else {
                        return $this->renderJSON(['milestone' => $this->board->toBacklogJSON()]);
                    }
                } elseif ($milestone instanceof Milestone || $this->board->getType() !== AgileBoard::TYPE_SCRUM) {
                    return $this->renderJSON($this->board->toMilestoneJSON($milestone));
                } else {
                    return $this->renderJSON($this->board->toJSON(false));
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Get milestone status for a board
         *
         * @Route(url="/milestonestatus")
         *
         * @param Request $request
         */
        public function runWhiteboardMilestoneStatus(Request $request)
        {
            $milestone = Milestones::getTable()->selectById((int)$request['milestone_id']);
            $board = AgileBoards::getTable()->selectById($request['board_id']);
            $allowed_status_ids = [];
            foreach ($board->getColumns() as $column) {
                $allowed_status_ids = array_merge($allowed_status_ids, $column->getStatusIds());
            }

            return $this->renderJSON(['content' => $this->getComponentHTML('project/milestonevirtualstatusdetails', compact('milestone', 'allowed_status_ids'))]);
        }
    
        /**
         * The project board whiteboard page
         *
         * @Route(url="/boards/:board_id/whiteboard")
         *
         * @param Request $request
         * @return framework\JsonOutput|void
         */
        public function runWhiteboard(Request $request)
        {
            if (!$this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS)) {
                $this->forward403($this->getI18n()->__("You don't have access to perform this action"));
            }
    
            $this->board = AgileBoards::getTable()->selectById($request['board_id']);
            if (!$this->board instanceof AgileBoard) {
                $this->forward403($this->getI18n()->__("You don't have access to this board or it doesn't exist"));
            }
    
            if ($this->board->getUser()->getID() !== $this->getUser()->getID() && !$this->_checkProjectAccess(Permission::PERMISSION_MANAGE_PROJECT_BOARDS)) {
                $this->forward403($this->getI18n()->__("You don't have access to this board or it doesn't exist"));
            }

            if ($request->getRequestedFormat() == 'json') {
                return $this->renderJSON(['board' => $this->board->toJSON()]);
            }

            $this->selected_milestone = $this->board->getDefaultSelectedMilestone();
        }

        /**
         * Retrieves a list of all releases on a board
         *
         * @Route(url="/boards/:board_id/getreleases")
         *
         * @param Request $request
         */
        public function runGetReleases(Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            $board = AgileBoards::getTable()->selectById($request['board_id']);

            return $this->renderComponent('agile/releasestrip', compact('board'));
        }

        /**
         * Retrieves a list of all epics on a board
         *
         * @Route(url="/boards/:board_id/getepics")
         *
         * @param Request $request
         */
        public function runGetEpics(Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            $board = AgileBoards::getTable()->selectById($request['board_id']);

            return $this->renderComponent('agile/epicstrip', compact('board'));
        }

        /**
         * Adds an epic
         *
         * @Route(url="/boards/:board_id/addepic")
         *
         * @param Request $request
         */
        public function runAddEpic(Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            $board = AgileBoards::getTable()->selectById($request['board_id']);

            try {
                $title = trim($request['title']);
                $shortname = trim($request['shortname']);
                if (!$title)
                    throw new Exception($this->getI18n()->__('You have to provide a title'));
                if (!$shortname)
                    throw new Exception($this->getI18n()->__('You have to provide a label'));

                $issue = new Issue();
                $issue->setTitle($title);
                $issue->setShortname($shortname);
                $issue->setIssuetype($board->getEpicIssuetypeID());
                $issue->setProject($board->getProject());
                $issue->setPostedBy($this->getUser());
                $issue->save();

                return $this->renderJSON(['issue_details' => $issue->toJSON()]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Retrieving or sorting milestone issues
         *
         * @Route(url="/boards/:board_id/milestone/:milestone_id/issues")
         *
         * @param Request $request
         */
        public function runMilestoneIssues(Request $request)
        {
            try {
                switch (true) {
                    case $request->isPost():
                        $issue_table = Issues::getTable();
                        $orders = array_keys($request["issue_ids"] ?: []);
                        foreach ($request["issue_ids"] ?: [] as $issue_id) {
                            $issue_table->setOrderByIssueId(array_pop($orders), $issue_id);
                        }

                        return $this->renderJSON(['sorted' => 'ok']);
                    default:
                        $milestone = Milestones::getTable()->selectById($request['milestone_id']);

                        $board = ($request['board_id']) ? AgileBoards::getTable()->selectById($request['board_id']) : new AgileBoard();
                        $component = (isset($milestone) && $milestone instanceof Milestone) ? 'milestoneissues' : 'backlog';

                        return $this->renderJSON(['content' => $this->getComponentHTML("agile/{$component}", compact('milestone', 'board'))]);
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Assign a user story to a milestone id
         *
         * @Route(url="/assign/issue/milestone/:milestone_id")
         *
         * @param Request $request
         */
        public function runAssignMilestone(Request $request)
        {
            $this->forward403if(Context::getCurrentProject()->isArchived());
            $this->forward403unless($this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS));

            try {
                $issue = Issue::getB2DBTable()->selectById((int)$request['issue_id']);
                $milestone = Milestones::getTable()->selectById($request['milestone_id']);

                if (!$issue instanceof Issue)
                    throw new Exception($this->getI18n()->__('This is not a valid issue'));

                $issue->setMilestone($milestone);
                $issue->save();
                foreach ($issue->getChildIssues() as $child_issue) {
                    $child_issue->setMilestone($milestone);
                    $child_issue->save();
                }
                $new_issues = ($milestone instanceof Milestone) ? $milestone->countIssues() : 0;
                $new_e_points = ($milestone instanceof Milestone) ? $milestone->getPointsEstimated() : 0;
                $new_e_hours = ($milestone instanceof Milestone) ? $milestone->getHoursAndMinutesEstimated(true, true) : 0;

                return $this->renderJSON(['issue_id' => $issue->getID(), 'issues' => $new_issues, 'points' => $new_e_points, 'hours' => $new_e_hours]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Assign a user story to a release
         *
         * @Route(url="/assign/issue/release/:release_id")
         *
         * @param Request $request
         */
        public function runAssignRelease(Request $request)
        {
            try {
                $issue = Issue::getB2DBTable()->selectById((int)$request['issue_id']);
                $release = Builds::getTable()->selectById((int)$request['release_id']);

                $issue->addAffectedBuild($release);

                return $this->renderJSON(['issue_id' => $issue->getID(), 'release_id' => $release->getID(), 'closed_pct' => $release->getPercentComplete()]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('An error occured when trying to assign the issue to the release')]);
            }
        }

        /**
         * Assign an issue to an epic
         *
         * @Route(url="/assign/issue/epic/:epic_id")
         *
         * @param Request $request
         */
        public function runAssignEpic(Request $request)
        {
            try {
                $epic = Issue::getB2DBTable()->selectById((int)$request['epic_id']);
                $issue = Issue::getB2DBTable()->selectById((int)$request['issue_id']);

                $epic->addChildIssue($issue, true);

                return $this->renderJSON(['issue_id' => $issue->getID(), 'epic_id' => $epic->getID(), 'closed_pct' => $epic->getEstimatedPercentCompleted(), 'num_child_issues' => $epic->countChildIssues(), 'estimate' => Issue::getFormattedTime($epic->getEstimatedTime())]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('An error occured when trying to assign the issue to the epic')]);
            }
        }

        /**
         * Milestone actions
         *
         * @Route(url="/milestone/:milestone_id/*")
         *
         * @param Request $request
         */
        public function runMilestone(Request $request)
        {
            $milestone_id = ($request['milestone_id']) ? $request['milestone_id'] : null;
            $milestone = new Milestone($milestone_id);

            try {
                if (!$this->getUser()->canManageProject($this->selected_project) || !$this->getUser()->canManageProjectReleases($this->selected_project))
                    throw new Exception($this->getI18n()->__("You don't have access to modify milestones"));

                switch (true) {
                    case $request->isDelete():
                        $milestone->delete();

                        $no_milestone = new Milestone(0);
                        $no_milestone->setProject($milestone->getProject());

                        return $this->renderJSON(['issue_count' => $no_milestone->countIssues(), 'hours' => $no_milestone->getHoursAndMinutesEstimated(true, true), 'points' => $no_milestone->getPointsEstimated()]);
                    case $request->isPost():
                        $this->_saveMilestoneDetails($request, $milestone);
                        $board = AgileBoards::getTable()->selectById($request['board_id']);

                        if ($request->hasParameter('issues') && $request['include_selected_issues'])
                            Issues::getTable()->assignMilestoneIDbyIssueIDs($milestone->getID(), $request['issues']);

                        $message = Context::getI18n()->__('Milestone saved');

                        return $this->renderJSON(['message' => $message, 'component' => $this->getComponentHTML('agile/milestonebox', ['milestone' => $milestone, 'board' => $board]), 'milestone_id' => $milestone->getID()]);
                    default:
                        return $this->renderJSON(['content' => framework\Action::returnComponentHTML('agile/milestonebox', ['milestone' => $milestone]), 'milestone_id' => $milestone->getID(), 'milestone_name' => $milestone->getName(), 'milestone_order' => array_keys($milestone->getProject()->getMilestonesForRoadmap())]);
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Poller for the planning page
         *
         * @Route(url="/boards/:board_id/poll/:mode")
         *
         * @param Request $request
         */
        public function runPoll(Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permission::PERMISSION_PROJECT_ACCESS_BOARDS));
            $last_refreshed = $request['last_refreshed'];
            $board = AgileBoards::getTable()->selectById($request['board_id']);
            $search_object = $board->getBacklogSearchObject();
            if ($search_object instanceof SavedSearch) {
                $search_object->setFilter('last_updated', SearchFilter::createFilter('last_updated', ['o' => Criterion::GREATER_THAN_EQUAL, 'v' => $last_refreshed - 2]));
            }

            if ($request['mode'] == 'whiteboard') {
                $milestone_id = $request['milestone_id'];
                $ids = Issues::getTable()->getUpdatedIssueIDsByTimestampAndProjectIDAndMilestoneID($last_refreshed - 2, $this->selected_project->getID(), $milestone_id);
            } else {
                $ids = Issues::getTable()->getUpdatedIssueIDsByTimestampAndProjectIDAndIssuetypeID($last_refreshed - 2, $this->selected_project->getID());
                $epic_ids = ($board->getEpicIssuetypeID()) ? Issues::getTable()->getUpdatedIssueIDsByTimestampAndProjectIDAndIssuetypeID($last_refreshed - 2, $this->selected_project->getID(), $board->getEpicIssuetypeID()) : [];
            }

            $backlog_ids = [];
            if ($search_object instanceof SavedSearch) {
                foreach ($search_object->getIssues(true) as $backlog_issue) {
                    foreach ($ids as $id_issue) {
                        if ($id_issue['issue_id'] == $backlog_issue->getID()) continue 2;
                    }

                    $backlog_ids[] = ['issue_id' => $backlog_issue->getID(), 'last_updated' => $backlog_issue->getLastUpdatedTime()];
                }
            }

            Context::loadLibrary('ui');
            $whiteboard_url = make_url('agile_whiteboardissues', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]);

            return $this->renderJSON(compact('ids', 'backlog_ids', 'epic_ids', 'milestone_id', 'whiteboard_url'));
        }

    }

