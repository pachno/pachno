<?php

    namespace pachno\core\modules\project;

    use pachno\core\entities;
    use pachno\core\entities\Milestone;
    use pachno\core\entities\Status;
    use pachno\core\entities\tables;
    use pachno\core\entities\tables\Issues;
    use pachno\core\framework;

    /**
     * Project action components
     *
     * @property entities\Issue $issue
     */
    class Components extends framework\ActionComponent
    {

        public function componentOverview()
        {
            $this->issuetypes = $this->project->getIssuetypeScheme()->getReportableIssuetypes();
        }

        public function componentProjectHeader()
        {
            $pagename_event = \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader::pagename', $this->project);
            $pagename_event->setReturnValue(framework\Context::getModule('project')->getPageName());
            $pagename_event->triggerUntilProcessed();
            $this->pagename = $pagename_event->getReturnValue();
        }

        public function componentProjectDevelopers()
        {
            $this->user_group = framework\Settings::getDefaultGroup();
        }

        public function componentMilestoneVirtualStatusDetails()
        {
            $this->statuses = Status::getAll();
            $this->allowed_status_ids = isset($this->allowed_status_ids) ? $this->allowed_status_ids : [];
            if ($this->milestone instanceof Milestone)
                $this->status_details = Issues::getTable()->getMilestoneDistributionDetails($this->milestone->getID(), $this->allowed_status_ids);
        }

        public function componentRecentActivities()
        {
            $this->default_displayed = isset($this->default_displayed) ? $this->default_displayed : false;
        }

        public function componentTimeline()
        {
            $this->prev_date = null;
            $this->prev_timestamp = null;
            $this->prev_issue = null;
        }

        public function componentViewIssueCard()
        {
            $this->statuses = ($this->issue->getProject()->useStrictWorkflowMode()) ? $this->issue->getProject()->getAvailableStatuses() : $this->issue->getAvailableStatuses();
            $this->set_field_route = framework\Context::getRouting()->generate('edit_issue', array('project_key' => $this->issue->getProject()->getKey(), 'issue_id' => $this->issue->getID()));
        }

        public function componentIssueFieldStatus()
        {
            $this->statuses = ($this->issue->getProject()->useStrictWorkflowMode()) ? $this->issue->getProject()->getAvailableStatuses() : $this->issue->getAvailableStatuses();
        }

        public function componentViewIssueHeader()
        {
            $this->statuses = ($this->issue->getProject()->useStrictWorkflowMode()) ? $this->issue->getProject()->getAvailableStatuses() : $this->issue->getAvailableStatuses();
        }

        public function componentMilestone()
        {
            if (!isset($this->milestone)) {
                $this->milestone = new Milestone();
                $this->milestone->setProject($this->project);
            }
        }

        public function componentMilestoneBox()
        {
            $this->board = $this->board ?? null;
            $this->include_counts = (isset($this->include_counts)) ? $this->include_counts : false;
            $this->include_buttons = (isset($this->include_buttons)) ? $this->include_buttons : true;
        }

        public function componentMilestoneDetails()
        {
            $this->total_estimated_points = 0;
            $this->total_spent_points = 0;
            $this->total_estimated_hours = 0;
            $this->total_spent_hours = 0;
            $this->burndown_data = $this->milestone->getBurndownData(true, true);
        }

        public function componentDashboardViewProjectInfo()
        {

        }

        public function componentDashboardViewProjectTeam()
        {
            $assignees = [];
            foreach (framework\Context::getCurrentProject()->getAssignedUsers() as $user) {
                $assignees[] = $user;
            }
            foreach (framework\Context::getCurrentProject()->getAssignedTeams() as $team) {
                $assignees[] = $team;
            }
            $this->assignees = $assignees;
            $this->project = framework\Context::getCurrentProject();
        }

        public function componentDashboardViewProjectClient()
        {
            $this->client = framework\Context::getCurrentProject()->getClient();
        }

        public function componentDashboardViewProjectSubprojects()
        {
            $this->subprojects = framework\Context::getCurrentProject()->getChildren(false);
        }

        public function componentDashboardViewProjectStatisticsLast15()
        {
            $this->issues = framework\Context::getCurrentProject()->getLast15Counts();
        }

        public function componentDashboardViewProjectStatistics()
        {
            switch ($this->view->getType()) {
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_PRIORITY:
                    $counts = framework\Context::getCurrentProject()->getPriorityCount();
                    $items = entities\Priority::getAll();
                    $key = 'priority';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_SEVERITY:
                    $counts = framework\Context::getCurrentProject()->getSeverityCount();
                    $items = entities\Severity::getAll();
                    $key = 'priority';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_CATEGORY:
                    $counts = framework\Context::getCurrentProject()->getCategoryCount();
                    $items = entities\Category::getAll();
                    $key = 'category';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_RESOLUTION:
                    $counts = framework\Context::getCurrentProject()->getResolutionCount();
                    $items = entities\Resolution::getAll();
                    $key = 'resolution';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_STATUS:
                    $counts = framework\Context::getCurrentProject()->getStatusCount();
                    $items = framework\Context::getCurrentProject()->getAvailableStatuses();
                    $key = 'status';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP:
                    $counts = framework\Context::getCurrentProject()->getWorkflowCount();
                    $items = entities\WorkflowStep::getAllByWorkflowSchemeID(framework\Context::getCurrentProject()->getWorkflowScheme()->getID());
                    $key = 'workflowstep';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_STATE:
                    $counts = framework\Context::getCurrentProject()->getStateCount();
                    $items = ['open' => $this->getI18n()->__('Open'), 'closed' => $this->getI18n()->__('Closed')];
                    $key = 'state';
                    break;
            }
            $this->counts = $counts;
            $this->key = $key;
            $this->items = $items;
        }

        public function componentDashboardViewProjectUpcoming()
        {
            $this->project = framework\Context::getCurrentProject();
            $this->upcoming_milestones = $this->project->getUpcomingMilestones(21);
            $this->starting_milestones = $this->project->getStartingMilestones(21);
        }

        public function componentDashboardViewProjectRecentIssues()
        {
            $this->project = framework\Context::getCurrentProject();
            $this->issues = $this->project->getRecentIssues($this->view->getDetail());
        }

        public function componentDashboardViewProjectRecentActivities()
        {
            $this->project = framework\Context::getCurrentProject();
            $this->recent_activities = $this->project->getRecentActivities(30, false);
        }

        public function componentDashboardViewProjectDownloads()
        {
            $this->project = framework\Context::getCurrentProject();
            $builds = $this->project->getBuilds();
            $active_builds = [];

            foreach ($this->project->getEditions() as $edition_id => $edition) {
                $active_builds[$edition_id] = [];
            }

            foreach ($builds as $build) {
                if ($build->isReleased() && $build->hasFile())
                    $active_builds[$build->getEditionID()][] = $build;
            }

            $this->editions = $active_builds;
        }

        public function componentEditProject()
        {
            $this->access_level = ($this->getUser()->canEditProjectDetails(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
            $this->roles = entities\Role::getAll();
            $assignee = $this->getUser();
            $this->assignee_name = $assignee->getRealname();
            if ($this->assignee_type && $this->assignee_id) {
                if ($this->assignee_type == 'team') {
                    $assignee = tables\Teams::getTable()->selectById($this->assignee_id);
                    $this->assignee_name = $assignee->getName();
                }
            }
            $this->assignee_type = ($assignee instanceof entities\User) ? 'user' : 'team';
            $this->assignee_id = $assignee->getID();
        }

        public function componentProjectConfig()
        {
            $this->access_level = ($this->getUser()->canEditProjectDetails(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
            $this->statustypes = Status::getAll();
            $this->selected_tab = isset($this->section) ? $this->section : 'info';
        }

        public function componentProjectInfo()
        {
            $this->valid_subproject_targets = entities\Project::getValidSubprojects($this->project);
        }

        public function componentProjectSettings()
        {
            $this->statustypes = Status::getAll();
        }

        public function componentProjectEdition()
        {
            $this->access_level = ($this->getUser()->canManageProject(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
        }

        public function componentProjecticons()
        {
            $this->custom_icons = tables\Files::getTable()->getByType(entities\File::TYPE_PROJECT_ICON);
        }

        public function componentProjectworkflow()
        {

        }

        public function componentProjectPermissions()
        {
            $this->roles = entities\Role::getAll();
            $this->project_roles = entities\Role::getByProjectID($this->project->getID());
        }

        public function componentBuildbox()
        {
            $this->access_level = ($this->getUser()->canManageProject(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
        }

        public function componentBuild()
        {
            if (!isset($this->build)) {
                $this->build = new entities\Build();
                $this->build->setProject(framework\Context::getCurrentProject());
                $this->build->setName(framework\Context::getI18n()->__('%project_name version 0.0.0', ['%project_name' => $this->project->getName()]));
                if (framework\Context::getRequest()->getParameter('edition_id') && $edition = entities\Edition::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('edition_id'))) {
                    $this->build->setEdition($edition);
                }
            }
        }

        public function componentFindAssignee()
        {
            $this->users = tables\Users::getTable()->getByDetails($this->find_by, 10, true);
            $this->teams = tables\Teams::getTable()->quickfind($this->find_by);
            $this->global_roles = entities\Role::getGlobalRoles();
            $this->project_roles = entities\Role::getByProjectID($this->selected_project->getID());

            if (filter_var($this->find_by, FILTER_VALIDATE_EMAIL) == $this->find_by) {
                $this->email = $this->find_by;
            }

            if (!count($this->users) && isset($this->email)) {
                $email_user = new entities\User();
                $email_user->setEmail($this->email);
                $this->email_user = $email_user;
            }
        }

    }
