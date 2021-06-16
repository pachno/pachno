<?php

    namespace pachno\core\modules\main;

    use Exception;
    use pachno\core\entities;
    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\Comment;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Issuetype;
    use pachno\core\entities\tables;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use pachno\core\framework\interfaces\AuthenticationProvider;
    use pachno\core\framework\Settings;
    use PragmaRX\Google2FA\Google2FA;

    /**
     * Main action components
     *
     * @property entities\User $user
     * @property entities\Issue $issue
     * @property entities\Client $client
     * @property entities\Team $team
     * @property entities\Issue[] $issues
     * @property entities\Project $project
     * @property entities\WorkflowTransition $transition
     * @property entities\LogItem $item
     * @property entities\IssueSpentTime $entry
     * @property entities\IssueSpentTime[] $timers
     *
     */
    class Components extends framework\ActionComponent
    {

        public function componentIssueLogItem()
        {
            $this->showtrace = (date('YmdHis', $this->previous_time) != date('YmdHis', $this->item->getTime()));
        }

        public function componentIssueMoreActions()
        {
            if (!isset($this->show_workflow_transitions)) {
                $this->show_workflow_transitions = true;
            }
            if (!isset($this->multi)) {
                $this->multi = false;
            }
        }

        public function componentUserdropdown_Inline()
        {
            $this->componentUserdropdown();
        }

        public function componentUserdropdown()
        {
            framework\Logging::log('user dropdown component');
            $this->rnd_no = rand();
            try {
                if (!$this->user instanceof entities\User) {
                    framework\Logging::log('loading user object in dropdown');
                    if (is_numeric($this->user)) {
                        $this->user = tables\Users::getTable()->getByUserId($this->user);
                    } else {
                        $this->user = tables\Users::getTable()->getByUsername($this->user);
                    }
                    framework\Logging::log('done (loading user object in dropdown)');
                }
            } catch (Exception $e) {

            }
            $this->show_avatar = (isset($this->show_avatar)) ? $this->show_avatar : true;
            $this->show_name = (isset($this->show_name)) ? $this->show_name : true;
            framework\Logging::log('done (user dropdown component)');
        }

        public function componentClientusers()
        {
            try {
                if (!$this->client instanceof entities\Client) {
                    framework\Logging::log('loading user object in dropdown');
                    $this->client = tables\Clients::getTable()->selectById($this->client);
                    framework\Logging::log('done (loading user object in dropdown)');
                }
                $this->clientusers = $this->client->getMembers();
            } catch (Exception $e) {

            }
        }

        public function componentTeamdropdown()
        {
            framework\Logging::log('team dropdown component');
            $this->rnd_no = rand();
            try {
                $this->team = (isset($this->team)) ? $this->team : null;
                if (!$this->team instanceof entities\Team) {
                    framework\Logging::log('loading team object in dropdown');
                    $this->team = tables\Teams::getTable()->selectById($this->team);
                    framework\Logging::log('done (loading team object in dropdown)');
                }
            } catch (Exception $e) {

            }
            framework\Logging::log('done (team dropdown component)');
        }

        public function componentIdentifiableselector()
        {
            $this->include_teams = (isset($this->include_teams)) ? $this->include_teams : false;
            $this->include_clients = (isset($this->include_clients)) ? $this->include_clients : false;
            $this->include_users = (isset($this->include_users)) ? $this->include_users : true;
            $this->allow_clear = (isset($this->allow_clear)) ? $this->allow_clear : true;
        }

        public function componentIdentifiableselectorresults()
        {
            $this->include_teams = (framework\Context::getRequest()->hasParameter('include_teams')) ? framework\Context::getRequest()->getParameter('include_teams') : false;
            $this->include_clients = (framework\Context::getRequest()->hasParameter('include_clients')) ? framework\Context::getRequest()->getParameter('include_clients') : false;
        }

        public function componentMyfriends()
        {
            $this->friends = framework\Context::getUser()->getFriends();
        }

        public function componentViewIssueFields()
        {
            $this->setupVariables();
        }

        protected function setupVariables()
        {
            $i18n = framework\Context::getI18n();
            if ($this->issue instanceof entities\Issue) {
                $this->project = $this->issue->getProject();
                $this->statuses = ($this->project->useStrictWorkflowMode()) ? $this->project->getAvailableStatuses() : $this->issue->getAvailableStatuses();

                $fields_list = [];
                $fields_list['category'] = ['title' => $i18n->__('Category'), 'fa_icon' => 'chart-pie', 'fa_icon_style' => 'fas', 'choices' => [], 'visible' => $this->issue->isCategoryVisible(), 'value' => (($this->issue->getCategory() instanceof entities\Category) ? $this->issue->getCategory()->getId() : 0), 'icon' => false, 'change_tip' => $i18n->__('Click to change category'), 'change_header' => $i18n->__('Change category'), 'clear' => $i18n->__('No category selected'), 'select' => $i18n->__('%clear_the_category or click to select a new category', ['%clear_the_category' => ''])];

                if ($this->issue->canEditCategory()) {
                    $fields_list['category']['choices'] = entities\Category::getAll();
                }

                $fields_list['resolution'] = ['title' => $i18n->__('Resolution'), 'choices' => [], 'visible' => $this->issue->isResolutionVisible(), 'value' => (($this->issue->getResolution() instanceof entities\Resolution) ? $this->issue->getResolution()->getId() : 0), 'icon' => false, 'change_tip' => $i18n->__('Click to change resolution'), 'change_header' => $i18n->__('Change resolution'), 'clear' => $i18n->__('No resolution selected'), 'select' => $i18n->__('%clear_the_resolution or click to select a new resolution', ['%clear_the_resolution' => ''])];

                if ($this->issue->canEditResolution()) {
                    $fields_list['resolution']['choices'] = entities\Resolution::getAll();
                }

                $has_priority = $this->issue->getPriority() instanceof entities\Priority;
                $fields_list['priority'] = ['title' => $i18n->__('Priority'), 'choices' => [], 'visible' => $this->issue->isPriorityVisible(), 'extra_classes' => (($has_priority) ? 'priority_' . $this->issue->getPriority()->getItemdata() : ''), 'value' => (($has_priority) ? $this->issue->getPriority()->getId() : 0), 'fa_icon' => (($has_priority) ? $this->issue->getPriority()->getFontAwesomeIcon() : ''), 'fa_icon_style' => (($has_priority) ? $this->issue->getPriority()->getFontAwesomeIconStyle() : ''), 'icon' => false, 'change_tip' => $i18n->__('Click to change priority'), 'change_header' => $i18n->__('Change priority'), 'clear' => $i18n->__('No priority selected'), 'select' => $i18n->__('%clear_the_priority or click to select a new priority', ['%clear_the_priority' => ''])];

                if ($this->issue->canEditPriority()) {
                    $fields_list['priority']['choices'] = entities\Priority::getAll();
                }

                $fields_list['reproducability'] = ['title' => $i18n->__('Reproducability'), 'choices' => [], 'visible' => $this->issue->isReproducabilityVisible(), 'value' => (($this->issue->getReproducability() instanceof entities\Reproducability) ? $this->issue->getReproducability()->getId() : 0), 'icon' => false, 'change_tip' => $i18n->__('Click to change reproducability'), 'change_header' => $i18n->__('Change reproducability'), 'clear' => $i18n->__('No reproducability selected'), 'select' => $i18n->__('%clear_the_reproducability or click to select a new reproducability', ['%clear_the_reproducability' => ''])];

                if ($this->issue->canEditReproducability()) {
                    $fields_list['reproducability']['choices'] = entities\Reproducability::getAll();
                }

                $fields_list['severity'] = ['title' => $i18n->__('Severity'), 'choices' => [], 'visible' => $this->issue->isSeverityVisible(), 'value' => (($this->issue->getSeverity() instanceof entities\Severity) ? $this->issue->getSeverity()->getId() : 0), 'icon' => false, 'change_tip' => $i18n->__('Click to change severity'), 'change_header' => $i18n->__('Change severity'), 'clear' => $i18n->__('No severity selected'), 'select' => $i18n->__('%clear_the_severity or click to select a new severity', ['%clear_the_severity' => ''])];

                if ($this->issue->canEditSeverity()) {
                    $fields_list['severity']['choices'] = entities\Severity::getAll();
                }

                $fields_list['milestone'] = ['title' => $i18n->__('Targetted for'), 'fa_icon' => 'list-alt', 'fa_style' => 'far', 'choices' => [], 'visible' => $this->issue->isMilestoneVisible(), 'value' => (($this->issue->getMilestone() instanceof entities\Milestone) ? $this->issue->getMilestone()->getId() : 0), 'icon' => true, 'icon_name' => 'icon_milestones.png', 'change_tip' => $i18n->__('Click to change which milestone this issue is targetted for'), 'change_header' => $i18n->__('Set issue target / milestone'), 'clear' => $i18n->__('Set as not targetted'), 'select' => $i18n->__('%set_as_not_targetted or click to set a new target milestone', ['%set_as_not_targetted' => '']), 'url' => true, 'current_url' => (($this->issue->getMilestone() instanceof entities\Milestone) ? $this->getRouting()->generate('project_roadmap', ['project_key' => $this->issue->getProject()->getKey()]) . '#roadmap_milestone_' . $this->issue->getMilestone()->getID() : '')];

                if ($this->issue->canEditMilestone()) {
                    $fields_list['milestone']['choices'] = $this->project->getMilestonesForIssues();
                }

                $customfields_list = [];
                foreach (entities\CustomDatatype::getAll() as $key => $customdatatype) {
                    $customvalue = $this->issue->getCustomField($key);
                    $customfields_list[$key] = ['type' => $customdatatype->getType(),
                        'title' => $i18n->__($customdatatype->getDescription()),
                        'visible' => $this->issue->isFieldVisible($key),
                        'editable' => $customdatatype->isEditable(),
                        'change_tip' => $i18n->__($customdatatype->getInstructions()),
                        'change_header' => $i18n->__($customdatatype->getDescription()),
                        'clear' => $i18n->__('Clear this field'),
                        'select' => $i18n->__('%clear_this_field or click to set a new value', ['%clear_this_field' => ''])];

                    if ($customdatatype->getType() == entities\DatatypeBase::CALCULATED_FIELD) {
                        $result = $this->issue->getCustomField($key);
                        $customfields_list[$key]['value'] = $result;
                    } elseif ($customdatatype->hasCustomOptions()) {
                        $customfields_list[$key]['value'] = ($customvalue instanceof entities\CustomDatatypeOption) ? $customvalue->getId() : 0;
                        $customfields_list[$key]['choices'] = $customdatatype->getOptions();
                    } elseif ($customdatatype->hasPredefinedOptions()) {
                        $customfields_list[$key]['value'] = ($customvalue instanceof entities\common\Identifiable) ? $customvalue->getId() : '';
                        $customfields_list[$key]['identifiable'] = ($customvalue instanceof entities\common\Identifiable) ? $customvalue : null;
                        $customfields_list[$key]['choices'] = $customdatatype->getOptions();
                    } else {
                        $customfields_list[$key]['value'] = $customvalue;
                    }
                }
                $this->customfields_list = $customfields_list;
                $this->editions = ($this->issue->getProject()->isEditionsEnabled()) ? $this->issue->getEditions() : [];
                $this->components = ($this->issue->getProject()->isComponentsEnabled()) ? $this->issue->getComponents() : [];
                $this->builds = ($this->issue->getProject()->isBuildsEnabled()) ? $this->issue->getBuilds() : [];
                $this->issuetypes = $this->project->getIssuetypeScheme()->getIssuetypes();
            } else {
                $fields_list = [];
                $fields_list['category'] = ['choices' => entities\Category::getAll()];
                $fields_list['resolution'] = ['choices' => entities\Resolution::getAll()];
                $fields_list['priority'] = ['choices' => entities\Priority::getAll()];
                $fields_list['reproducability'] = ['choices' => entities\Reproducability::getAll()];
                $fields_list['severity'] = ['choices' => entities\Severity::getAll()];
                $fields_list['milestone'] = ['choices' => $this->project->getMilestonesForIssues()];

                if (isset($this->issues)) {
                    $all_statuses = [];
                    $project_statuses = $this->project->getAvailableStatuses();
                    foreach ($this->issues as $issue) {
                        $statuses = ($this->project->useStrictWorkflowMode()) ? $project_statuses : $issue->getAvailableStatuses();
                        foreach ($statuses as $status_id => $status) {
                            $all_statuses[$status_id] = $status;
                        }
                    }
                    $this->statuses = $all_statuses;
                }

            }

            $this->fields_list = $fields_list;
            if (isset($this->transition) && $this->transition->hasAction(entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE)) {
                $available_assignees = [];
                foreach (framework\Context::getUser()->getTeams() as $team) {
                    foreach ($team->getMembers() as $user) {
                        $available_assignees[$user->getID()] = $user->getNameWithUsername();
                    }
                }
                foreach (framework\Context::getUser()->getFriends() as $user) {
                    $available_assignees[$user->getID()] = $user->getNameWithUsername();
                }
                $this->available_assignees = $available_assignees;
            }
        }

        public function componentIssuemaincustomfields()
        {
            $this->setupVariables();
        }

        public function componentHideableInfoBox()
        {
            $this->show_box = framework\Settings::isInfoBoxVisible($this->key);
        }

        public function componentHideableInfoBoxModal()
        {
            if (!isset($this->options))
                $this->options = [];
            if (!isset($this->button_label))
                $this->button_label = $this->getI18n()->__('Hide');
            $this->show_box = framework\Settings::isInfoBoxVisible($this->key);
        }

        public function componentUploader()
        {
            switch (true) {
                case isset($this->issue):
                    $this->target = $this->issue;
                    $this->existing_files = array_reverse($this->issue->getFiles());
                    break;
                case isset($this->article):
                    $this->target = $this->article;
                    $this->existing_files = array_reverse($this->article->getFiles());
                    break;
                default:
                    // @todo: dispatch a framework\Event that allows us to retrieve the
                    // necessary variables from anyone catching it
                    break;
            }
        }

        public function componentAttachedfile()
        {
            if ($this->mode == 'issue' && !isset($this->issue)) {
                $this->issue = tables\Issues::getTable()->selectById($this->issue_id);
            } elseif ($this->mode == 'article' && !isset($this->article)) {
                $this->article = entities\Article::getByName($this->article_name);
            }
            $this->file_id = $this->file->getID();
        }

        public function componentUpdateissueproperties()
        {
            $this->issue = $this->issue ?? null;
            $this->setupVariables();
            if (isset($this->board) && $this->board instanceof AgileBoard) {
                if ($this->board->usesSwimlanes()) {
                    switch ($this->board->getSwimlaneType()) {
                        case entities\AgileBoard::SWIMLANES_ISSUES:
                            foreach ($this->board->getMilestoneSwimlanes($this->milestone) as $swimlane) {
                                if ($swimlane->getIdentifier() != $this->swimlane_identifier)
                                    continue;

                                $this->parent_issue = $swimlane->getIdentifierIssue();
                            }
                            break;
                        case entities\AgileBoard::SWIMLANES_EXPEDITE:
                        case entities\AgileBoard::SWIMLANES_GROUPING:
                            foreach ($this->board->getMilestoneSwimlanes($this->milestone) as $swimlane) {
                                if ($swimlane->getIdentifier() != $this->swimlane_identifier)
                                    continue;

                                if ($swimlane->getIdentifierGrouping() == 'priority') {
                                    $this->selected_priorities = ($swimlane->hasIdentifiables()) ? $swimlane->getIdentifiables() : null;
                                } elseif ($swimlane->getIdentifierGrouping() == 'severity') {
                                    $this->selected_severities = ($swimlane->hasIdentifiables()) ? $swimlane->getIdentifiables() : null;
                                } elseif ($swimlane->getIdentifierGrouping() == 'category') {
                                    $this->selected_categories = ($swimlane->hasIdentifiables()) ? $swimlane->getIdentifiables() : null;
                                }
                            }
                            break;
                        default:
                            throw new Exception('Woops');
                    }
                }
            }

            if (isset($this->interactive) && $this->interactive && $this->issue instanceof Issue) {
                $this->form_url = $this->getRouting()->generate('transition_issues', array('project_key' => $this->project->getKey(), 'transition_id' => $this->transition->getID()));
                $this->form_id = "workflow_transition_form";
            } elseif ($this->issue instanceof Issue) {
                $this->form_url = $this->getRouting()->generate('transition_issue', array('project_key' => $this->project->getKey(), 'issue_id' => $this->issue->getID(), 'transition_id' => $this->transition->getID()));
                $this->form_id = "workflow_transition_{$this->transition->getID()}_form";
            } else {
                $this->form_url = $this->getRouting()->generate('transition_issues', array('project_key' => $this->project->getKey(), 'transition_id' => $this->transition->getID()));
                $this->form_id = "bulk_workflow_transition_form";
            }

        }

        public function componentNotifications()
        {
            $this->notifications = $this->getUser()->getNotifications();
            $this->num_unread = $this->getUser()->getNumberOfUnreadNotifications();
            $this->num_read = $this->getUser()->getNumberOfReadNotifications();
        }

        public function componentTimers()
        {
            $this->timers = $this->getUser()->getTimers();
        }

        public function componentNotification_text()
        {
            $this->return_notification = true;

            if ($this->notification->isShown()) {
                $this->return_notification = false;
            } else {
                $this->notification->showOnce();
                $this->notification->save();
            }
        }

        public function componentFindduplicateissues()
        {
            $this->setupVariables();
        }

        public function componentLogitem()
        {
            if (!isset($this->include_issue_title)) {
                $this->include_issue_title = true;
            }
            if (!isset($this->include_time)) {
                $this->include_time = $this->include_issue_title;
            }
            if (!isset($this->include_project)) {
                $this->include_project = false;
            }
        }

        public function componentComments()
        {
            $this->comment_count = Comment::countComments($this->target_id, $this->target_type);
        }

        public function componentCommentitem()
        {
            if ($this->comment->getTargetType() == Comment::TYPE_ISSUE) {
                try {
                    $this->issue = tables\Issues::getTable()->selectById($this->comment->getTargetID());
                } catch (Exception $e) {
                }
            }
        }

        public function componentIssueaffected()
        {
            $this->editions = ($this->issue->getProject()->isEditionsEnabled()) ? $this->issue->getEditions() : [];
            $this->components = ($this->issue->getProject()->isComponentsEnabled()) ? $this->issue->getComponents() : [];
            $this->builds = ($this->issue->getProject()->isBuildsEnabled()) ? $this->issue->getBuilds() : [];
            $this->statuses = entities\Status::getAll();
            $this->count = count($this->editions) + count($this->components) + count($this->builds);
        }

        public function componentRelatedissue()
        {
            $this->backdrop = $this->backdrop ?? false;
            $this->link_url = ($this->backdrop) ? 'javascript:void(0);' : $this->issue->getUrl();
            $this->link_data = ($this->backdrop) ? 'data-url="' . $this->issue->getCardUrl() . '"' : '';
        }

        public function componentIssueDetails()
        {
            $this->backdrop = $this->backdrop ?? false;
        }

        public function componentDuplicateissues()
        {
            $this->duplicate_issues = $this->issue->getDuplicateIssues();
        }

        public function componentIssueadditem()
        {
            $project = $this->issue->getProject();
            $this->editions = $project->getEditions();
            $this->components = $project->getComponents();
            $this->builds = $project->getActiveBuilds();
        }

        public function componentDashboardview()
        {
            if ($this->view->hasJS()) {
                foreach ($this->view->getJS() as $js)
                    $this->getResponse()->addJavascript($js);
            }
        }

        public function componentDashboardConfig()
        {
            $this->dashboard = tables\Dashboards::getTable()->selectById($this->dashboard_id);
            $this->views = entities\DashboardView::getAvailableViews($this->target_type);
        }

        public function componentReportIssueContainer()
        {
            if (isset($this->board) && $this->board instanceof AgileBoard && isset($this->selected_issuetype) && $this->selected_issuetype instanceof Issuetype && $this->board->isIssuetypeSwimlaneIdentifier($this->selected_issuetype)) {
                $this->title = $this->getI18n()->__('Add swimlane');
            } elseif (isset($this->parent_issue) && $this->parent_issue instanceof Issue) {
                $this->title = $this->getI18n()->__('Add card');
            } else {
                $this->title = $this->getI18n()->__('Add an issue');
            }
        }

        public function componentReportIssue()
        {
            $introarticle = tables\Articles::getTable()->getArticleByName(ucfirst(framework\Context::getCurrentProject()->getKey()) . ':ReportIssueIntro');
            $this->introarticle = ($introarticle instanceof entities\Article) ? $introarticle : tables\Articles::getTable()->getArticleByName('ReportIssueIntro');
            $reporthelparticle = tables\Articles::getTable()->getArticleByName(ucfirst(framework\Context::getCurrentProject()->getKey()) . ':ReportIssueHelp');
            $this->reporthelparticle = ($reporthelparticle instanceof entities\Article) ? $reporthelparticle : tables\Articles::getTable()->getArticleByName('ReportIssueHelp');
            $this->uniqid = framework\Context::getRequest()->getParameter('uniqid', uniqid());
            $this->_setupReportIssueProperties();
            $dummyissue = new entities\Issue();
            $dummyissue->setProject(framework\Context::getCurrentProject());
            $this->canupload = (framework\Settings::isUploadsEnabled() && $dummyissue->canAttachFiles());
        }

        protected function _setupReportIssueProperties()
        {
            $this->locked_issuetype = $this->locked_issuetype ?? null;
            $this->selected_issuetype = $this->selected_issuetype ?? null;
            $this->selected_edition = $this->selected_edition ?? null;
            $this->selected_build = $this->selected_build ?? null;
            $this->selected_milestone = $this->selected_milestone ?? null;
            $this->selected_statuses = $this->selected_statuses ?? null;
            $this->parent_issue = $this->parent_issue ?? null;
            $this->selected_component = $this->selected_component ?? null;
            $this->selected_category = $this->selected_category ?? null;
            $this->selected_categories = $this->selected_categories ?? null;
            $this->selected_status = $this->selected_status ?? null;
            $this->selected_resolution = $this->selected_resolution ?? null;
            $this->selected_priority = $this->selected_priority ?? null;
            $this->selected_priorities = $this->selected_priorities ?? null;
            $this->selected_reproducability = $this->selected_reproducability ?? null;
            $this->selected_severity = $this->selected_severity ?? null;
            $this->selected_severities = $this->selected_severities ?? null;
            $this->selected_estimated_time = $this->selected_estimated_time ?? null;
            $this->selected_spent_time = $this->selected_spent_time ?? null;
            $this->selected_percent_complete = $this->selected_percent_complete ?? null;
            $this->selected_pain_bug_type = $this->selected_pain_bug_type ?? null;
            $this->selected_pain_likelihood = $this->selected_pain_likelihood ?? null;
            $this->selected_pain_effect = $this->selected_pain_effect ?? null;
            $selected_customdatatype = $this->selected_customdatatype ?? [];
            foreach (entities\CustomDatatype::getAll() as $customdatatype) {
                $selected_customdatatype[$customdatatype->getKey()] = isset($selected_customdatatype[$customdatatype->getKey()]) ? $selected_customdatatype[$customdatatype->getKey()] : null;
            }
            $this->selected_customdatatype = $selected_customdatatype;
            $this->issuetype_id = $this->issuetype_id ?? null;
            $this->issue = $this->issue ?? null;
            $this->categories = entities\Category::getAll();
            $this->severities = entities\Severity::getAll();
            $this->priorities = entities\Priority::getAll();
            $this->reproducabilities = entities\Reproducability::getAll();
            $this->resolutions = entities\Resolution::getAll();
            $this->statuses = entities\Status::getAll();
            $this->milestones = framework\Context::getCurrentProject()->getMilestonesForIssues();
            $this->al_items = [];
        }

        public function componentIssueSubscribers()
        {
            $this->users = $this->issue->getSubscribers();
        }

        public function componentIssueSpenttime()
        {
            $this->entry = tables\IssueSpentTimes::getTable()->selectById($this->entry_id);
        }

        public function componentDashboardViewRecentComments()
        {
            $this->comments = entities\Comment::getRecentCommentsByAuthor($this->getUser()->getID());
        }

        public function componentDashboardViewTimers()
        {
            $this->timers = $this->getUser()->getTimers();
        }

        public function componentDashboardViewLoggedActions()
        {
            $this->log_items = tables\LogItems::getTable()->getByUserID($this->getUser()->getID(), 35);
            $this->prev_date = null;
            $this->prev_timestamp = null;
            $this->prev_issue = null;
        }

        public function componentDashboardViewUserProjects()
        {
            $routing = $this->getRouting();
            $i18n = $this->getI18n();
            framework\Context::loadLibrary('ui');
            $links = [
                ['url' => $routing->generate('project_open_issues', ['project_key' => '%project_key%']), 'text' => $i18n->__('Issues')],
                ['url' => $routing->generate('project_roadmap', ['project_key' => '%project_key%']), 'text' => $i18n->__('Roadmap')],
            ];
            $event = Event::createNew('core', 'main\Components::DashboardViewUserProjects::links', null, [], $links);
            $event->trigger();
            $this->links = $event->getReturnList();
        }

        public function componentIssueEstimator()
        {
            $times['months'] = $this->issue->getEstimatedMonths();
            $times['weeks'] = $this->issue->getEstimatedWeeks();
            $times['days'] = $this->issue->getEstimatedDays();
            $times['hours'] = $this->issue->getEstimatedHours();
            $times['minutes'] = $this->issue->getEstimatedMinutes();
            $this->points = $this->issue->getEstimatedPoints();
            $this->times = $times;
            $this->project_key = $this->issue->getProject()->getKey();
            $this->issue_id = $this->issue->getID();
        }

        public function componentAddDashboardView()
        {
            $request = framework\Context::getRequest();
            $this->dashboard = tables\Dashboards::getTable()->selectById($request['dashboard_id']);
            $this->column = $request['column'];
            $this->views = entities\DashboardView::getAvailableViews($this->dashboard->getType());
            $this->savedsearches = tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID(), ($this->dashboard->getProject() instanceof entities\Project) ? $this->dashboard->getProject()->getID() : 0);
        }


        public function componentProjectList()
        {
            $url_options = ['project_state' => 'active', 'list_mode' => $this->list_mode];

            if ($this->list_mode == 'team') {
                $url_options['team_id'] = $this->team_id;
            } elseif ($this->list_mode == 'client') {
                $url_options['client_id'] = $this->client_id;
            }

            $this->active_url = $this->getRouting()->generate('project_list', $url_options);
            $url_options['project_state'] = 'archived';
            $this->archived_url = $this->getRouting()->generate('project_list', $url_options);
        }

        public function componentMenuLink()
        {
            $this->link_id = $this->link->getId();
        }

        public function componentTextarea()
        {
            $this->syntax = $this->syntax ?? Settings::SYNTAX_MD;
            $this->syntaxClass = (is_numeric($this->syntax)) ? Settings::getSyntaxClass($this->syntax) : $this->syntax;
            $this->base_id = $this->area_id ?? $this->area_name;
            $this->invisible = $this->invisible ?? false;
            $this->mentionable = isset($this->target_type) && isset($this->target_id);
            $this->markuppable = $this->markuppable ?? ($this->syntaxClass == Settings::getSyntaxClass(Settings::SYNTAX_MD));
        }

        public function componentEditSpentTimeEntry()
        {
            $this->entry = $this->entry ?? new entities\IssueSpentTime();
            $this->url = $this->getRouting()->generate('issue_edittimespent', ['project_key' => $this->issue->getProject()->getKey(), 'issue_id' => $this->issue->getID(), 'entry_id' => $this->entry->getId()]);
        }

    }
