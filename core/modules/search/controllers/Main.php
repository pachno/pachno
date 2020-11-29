<?php

    namespace pachno\core\modules\search\controllers;

    use Exception;
    use pachno\core\entities;
    use pachno\core\entities\tables;
    use pachno\core\framework;
    use pachno\core\framework\Context;

    /**
     * actions for the search module
     *
     * @property entities\SavedSearch $search_object
     */
    class Main extends framework\Action
    {

        protected $foundissues = [];

        protected $filters = [];

        /**
         * @var entities\SavedSearch
         * @property $search_object
         */

        static function resultGrouping(entities\Issue $issue, $groupby, $cc, $prevgroup_id)
        {
            $i18n = Context::getI18n();
            $showtablestart = false;
            $showheader = false;
            $groupby_id = 0;
            $groupby_description = '';
            if ($cc == 0)
                $showtablestart = true;

            if ($groupby != '') {
                switch ($groupby) {
                    case 'category':
                        if ($issue->getCategory() instanceof entities\Category) {
                            $groupby_id = $issue->getCategory()->getID();
                            $groupby_description = $issue->getCategory()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Unknown');
                        }
                        break;
                    case 'status':
                        if ($issue->getStatus() instanceof entities\Status) {
                            $groupby_id = $issue->getStatus()->getID();
                            $groupby_description = $issue->getStatus()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Unknown');
                        }
                        break;
                    case 'severity':
                        if ($issue->getSeverity() instanceof entities\Severity) {
                            $groupby_id = $issue->getSeverity()->getID();
                            $groupby_description = $issue->getSeverity()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Unknown');
                        }
                        break;
                    case 'resolution':
                        if ($issue->getResolution() instanceof entities\Resolution) {
                            $groupby_id = $issue->getResolution()->getID();
                            $groupby_description = $issue->getResolution()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Unknown');
                        }
                        break;
                    case 'edition':
                        if ($issue->getEditions()) {
                            $groupby_id = $issue->getFirstAffectedEdition()->getID();
                            $groupby_description = $issue->getFirstAffectedEdition()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('None');
                        }
                        break;
                    case 'build':
                        if ($issue->getBuilds()) {
                            $groupby_id = $issue->getFirstAffectedBuild()->getID();
                            $groupby_description = $issue->getFirstAffectedBuild()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('None');
                        }
                        break;
                    case 'component':
                        if ($issue->getComponents()) {
                            $groupby_id = $issue->getFirstAffectedComponent()->getID();
                            $groupby_description = $issue->getFirstAffectedComponent()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('None');
                        }
                        break;
                    case 'priority':
                        if ($issue->getPriority() instanceof entities\Priority) {
                            $groupby_id = $issue->getPriority()->getID();
                            $groupby_description = $issue->getPriority()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Unknown');
                        }
                        break;
                    case 'issuetype':
                        if ($issue->getIssueType() instanceof entities\Issuetype) {
                            $groupby_id = $issue->getIssueType()->getID();
                            $groupby_description = $issue->getIssueType()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Unknown');
                        }
                        break;
                    case 'milestone':
                        if ($issue->getMilestone() instanceof entities\Milestone) {
                            $groupby_id = $issue->getMilestone()->getID();
                            $groupby_description = $issue->getMilestone()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Not targetted');
                        }
                        break;
                    case 'assignee':
                        if ($issue->getAssignee() instanceof entities\common\Identifiable) {
                            $groupby_id = $issue->getAssignee()->getID();
                            $groupby_description = $issue->getAssignee()->getName();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Not assigned');
                        }
                        break;
                    case 'posted_by':
                        if ($issue->getPostedBy() instanceof entities\common\Identifiable) {
                            $groupby_id = $issue->getPostedByID();
                            $groupby_description = $issue->getPostedBy()->getNameWithUsername();
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('Unknown');
                        }
                        break;
                    case 'state':
                        if ($issue->isClosed()) {
                            $groupby_id = entities\Issue::STATE_CLOSED;
                            $groupby_description = $i18n->__('Closed');
                        } else {
                            $groupby_id = entities\Issue::STATE_OPEN;
                            $groupby_description = $i18n->__('Open');
                        }
                        break;
                    case 'posted':
                        $groupby_id = date('Ymd', $issue->getPosted());
                        $groupby_description = Context::getI18n()->formatTime($issue->getPosted(), 20);
                        break;
                    case 'time_spent':
                        if ($issue->getSpentTimes()) {
                            $issue_spent_time = reset($issue->getSpentTimes());
                            $groupby_id = date('Ymd', $issue_spent_time->getEditedAt());
                            $groupby_description = Context::getI18n()->formatTime($issue_spent_time->getEditedAt(), 20);
                        } else {
                            $groupby_id = 0;
                            $groupby_description = $i18n->__('None');
                        }
                        break;
                }
                if ($groupby_id !== $prevgroup_id) {
                    $showtablestart = true;
                    $showheader = true;
                }
                $prevgroup_id = $groupby_id;
            }

            return [$showtablestart, $showheader, $prevgroup_id, $groupby_description];
        }

        public static function userPainSort(entities\Issue $first_issue, entities\Issue $second_issue)
        {
            $first_issue_pain = $first_issue->getUserPain();
            $second_issue_pain = $second_issue->getUserPain();
            if ($first_issue_pain == $second_issue_pain) {
                return 0;
            }

            return ($first_issue_pain < $second_issue_pain) ? -1 : 1;
        }

        /**
         * Pre-execute function for search functions
         *
         * @param framework\Request $request
         */
        public function preExecute(framework\Request $request, $action)
        {
            $this->forward403unless(Context::getUser()->hasPageAccess('search') && Context::getUser()->canSearchForIssues());

            if ($project_key = $request['project_key']) {
                $project = entities\Project::getByKey($project_key);
            } elseif (is_numeric($request['project_id']) && $project_id = (int)$request['project_id']) {
                $project = tables\Projects::getTable()->selectById($project_id);
            } else {
                $project = false;
            }

            if ($project instanceof entities\Project) {
                $this->forward403unless(Context::getUser()->hasProjectPageAccess('project_issues', $project));
                Context::getResponse()->setPage('project_issues');
                Context::setCurrentProject($project);
            }
            $this->search_object = entities\SavedSearch::getFromRequest($request);
            $this->issavedsearch = ($this->search_object instanceof entities\SavedSearch && $this->search_object->getB2DBID());
            $this->show_results = ($this->issavedsearch || $request->hasParameter('quicksearch') || $request->hasParameter('fs') || $request->getParameter('search', false)) ? true : false;

            $this->searchterm = ($this->search_object instanceof entities\SavedSearch) ? $this->search_object->getSearchterm() : '';
            $this->searchtitle = ($this->search_object instanceof entities\SavedSearch) ? $this->search_object->getTitle() : '';

            if ($this->issavedsearch) {
                if (!($this->search_object instanceof entities\SavedSearch && Context::getUser()->canAccessSavedSearch($this->search_object))) {
                    Context::setMessage('search_error', Context::getI18n()->__("You don't have access to this saved search"));
                }
            }
        }

        /**
         * Performs quicksearch
         *
         * @param framework\Request $request The request object
         */
        public function runQuickSearch(framework\Request $request)
        {
            if ($this->getUser()->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_USERS)) {
                $this->found_users = tables\Users::getTable()->findInConfig($this->searchterm, 10, false);
                $this->found_teams = tables\Teams::getTable()->quickfind($this->searchterm);
                $this->found_clients = tables\Clients::getTable()->quickfind($this->searchterm);
                $this->num_users = count($this->found_users);
                $this->num_teams = count($this->found_teams);
                $this->num_clients = count($this->found_clients);
            }
            $found_projects = tables\Projects::getTable()->quickfind($this->searchterm);
            $projects = [];
            foreach ($found_projects as $project) {
                if ($project->hasAccess())
                    $projects[$project->getID()] = $project;
            }
            $this->found_projects = $projects;
            $this->num_projects = count($projects);
        }

        public function runSaveSearch(framework\Request $request)
        {
            $name = trim($request['name']);
            if (strlen($name) > 0) {
                if (!$request['update_saved_search'])
                    $this->search_object = new entities\SavedSearch();
                $this->search_object->setName($request['name']);
                $this->search_object->setDescription($request['description']);
                $this->search_object->setIsPublic((bool)$request['is_public']);
                $this->search_object->setUser((bool)$request['is_public'] ? 0 : $this->getUser());
                $this->search_object->setValuesFromRequest($request);
                if ($request['project_id'])
                    $this->search_object->setAppliesToProject((int)$request['project_id']);

                $this->search_object->save();
                Context::setMessage('search_message', 'saved_search');

                if ($request['project_id'])
                    return $this->renderJSON(['forward' => $this->getRouting()->generate('project_issues', ['project_key' => $this->search_object->getProject()->getKey(), 'saved_search_id' => $this->search_object->getID()], false)]);
                else
                    return $this->renderJSON(['forward' => $this->getRouting()->generate('search', ['saved_search_id' => $this->search_object->getID()], false)]);
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('Please provide a name for the saved search')]);
            }
        }

        /**
         * Performs the "find issues" action
         *
         * @param framework\Request $request
         */
        public function runFindIssues(framework\Request $request)
        {
            if ($request['delete_saved_search']) {
                return $this->runEditSavedSearch($request);
            }

            $this->resultcount = 0;
            if ($request['quicksearch'] == true) {
                if ($request->isAjaxCall()) {
                    return $this->redirect('quicksearch');
                }
            }
            if ($this->search_object->hasQuickfoundIssues()) {
                $issues = $this->search_object->getQuickfoundIssues();
                $issue = array_shift($issues);
                if ($issue instanceof entities\Issue) {
                    return $this->forward($this->getRouting()->generate('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]));
                }
            }
            $this->search_error = Context::getMessageAndClear('search_error');
            $this->search_message = Context::getMessageAndClear('search_message');
            $this->appliedfilters = $this->filters;
            $this->templates = entities\SavedSearch::getTemplates();
        }

        public function runEditSavedSearch(framework\Request $request)
        {
            if ($request->isPost()) {
                if ($request['delete_saved_search']) {
                    try {
                        if (!$this->search_object instanceof entities\SavedSearch || !$this->search_object->getB2DBID())
                            throw new Exception('not a saved search');

                        if ($this->search_object->getUserID() == Context::getUser()->getID() || $this->search_object->isPublic() && Context::getUser()->canCreatePublicSearches()) {
                            $this->search_object->delete();

                            return $this->renderJSON(['failed' => false, 'message' => Context::getI18n()->__('The saved search was deleted successfully')]);
                        }
                    } catch (Exception $e) {
                        return $this->renderJSON(['failed' => true, 'message' => Context::getI18n()->__('Cannot delete this saved search')]);
                    }
                } elseif ($request['saved_search_name'] != '') {
                    if (!$this->saved_search instanceof entities\SavedSearch)
                        $this->saved_search = new entities\SavedSearch();

                    $this->saved_search->setName($request['saved_search_name']);
                    $this->saved_search->setDescription($request['saved_search_description']);
                    $this->saved_search->setIsPublic((bool)$request['saved_search_public']);
                    $this->saved_search->save();

                    if ($request['saved_search_id']) {
                        Context::setMessage('search_message', Context::getI18n()->__('The saved search was updated'));
                    } else {
                        Context::setMessage('search_message', Context::getI18n()->__('The saved search has been created'));
                    }
                    $params = [];
                } else {
                    Context::setMessage('search_error', Context::getI18n()->__('You have to specify a name for the saved search'));
                    $params = ['fs' => $this->filters, 'groupby' => $this->groupby, 'grouporder' => $this->grouporder, 'templatename' => $this->templatename, 'saved_search' => $request['saved_search_id'], 'issues_per_page' => $this->ipp];
                }
                if (Context::isProjectContext()) {
                    $route = 'project_issues';
                    $params['project_key'] = Context::getCurrentProject()->getKey();
                } else {
                    $route = 'search';
                }
                $this->forward(Context::getRouting()->generate($route, $params));
            }
        }

        public function runFindIssuesPaginated(framework\Request $request)
        {
            $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);

            return $this->renderJSON([
                'content' => $this->getComponentHTML('search/issues_paginated', ['search_object' => $this->search_object, 'cc' => 1, 'prevgroup_id' => null]),
                'default_columns' => entities\SavedSearch::getDefaultVisibleColumns(),
                'available_columns' => entities\SavedSearch::getAvailableColumns(),
                'visible_columns' => $this->search_object->getColumns(),
                'applied_filters' => array_keys($this->search_object->getFilters()),
                'template' => entities\SavedSearch::getTemplate($this->search_object->getTemplateName()),
                'template_parameter' => $this->search_object->getTemplateParameter(),
                'num_issues' => $this->search_object->getTotalNumberOfIssues()
            ]);
        }

        public function runAddFilter(framework\Request $request)
        {
            if ($request['filter_name'] == 'project_id' && count(entities\Project::getAll()) == 0) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('No projects exist so this filter can not be added')]);
            } elseif (in_array($request['filter_name'], entities\SearchFilter::getValidSearchFilters()) || entities\CustomDatatype::doesKeyExist($request['filter_name'])) {
                return $this->renderJSON(['content' => $this->getComponentHTML('search/filter', ['filter' => $request['filter_name'], 'key' => $request->getParameter('key', 0)])]);
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => Context::getI18n()->__('This is not a valid search field')]);
            }
        }

        public function runFilterFindUsers(framework\Request $request)
        {
            $filter = $request['filter'];
            $filterkey = $request['filterkey'];
            $existing_users = $request['existing_id'];

            if (strlen($filter) < 3)
                return $this->renderJSON(['results' => '<li>' . $this->getI18n()->__('Please enter 3 characters or more') . '</li>']);

            $users = tables\Users::getTable()->getByDetails($filter, 10);
            foreach ($existing_users as $id) {
                if (isset($users[$id]))
                    unset($users[$id]);
            }

            return $this->renderJSON(['results' => $this->getComponentHTML('search/filterfindusers', compact('users', 'filterkey'))]);
        }

        public function runFilterFindTeams(framework\Request $request)
        {
            $filter = $request['filter'];
            $filterkey = $request['filterkey'];
            $existing_teams = $request['existing_id'];

            if (strlen($filter) < 3)
                return $this->renderJSON(['results' => '<li>' . $this->getI18n()->__('Please enter 3 characters or more') . '</li>']);

            $teams = tables\Teams::getTable()->quickfind($filter, 10);
            if (isset($existing_teams)) {
                foreach ($existing_teams as $id => $one) {
                    if (isset($teams[$id]))
                        unset($teams[$id]);
                }
            }

            return $this->renderJSON(['results' => $this->getComponentHTML('search/filterfindteams', compact('teams', 'filterkey'))]);
        }

        public function runFilterFindClients(framework\Request $request)
        {
            $filter = $request['filter'];
            $filterkey = $request['filterkey'];
            $existing_clients = $request['existing_id'];

            if (strlen($filter) < 3)
                return $this->renderJSON(['results' => '<li>' . $this->getI18n()->__('Please enter 3 characters or more') . '</li>']);

            $clients = tables\Clients::getTable()->quickfind($filter, 10);
            if (isset($existing_clients)) {
                foreach ($existing_clients as $id => $one) {
                    if (isset($clients[$id]))
                        unset($clients[$id]);
                }
            }

            return $this->renderJSON(['results' => $this->getComponentHTML('search/filterfindclients', compact('clients', 'filterkey'))]);
        }

        public function runFilterGetDynamicChoices(framework\Request $request)
        {
            $subproject_ids = explode(',', $request['subprojects']);
            $existing_ids = $request['existing_ids'];
            $results = [];
            $projects = ($request['project_id'] != '') ? entities\Project::getAllByIDs(explode(',', $request['project_id'])) : entities\Project::getAll();

            $items = ['build' => [], 'edition' => [], 'component' => [], 'milestone' => []];

            foreach ($projects as $project) {
                foreach ($project->getBuilds() as $build)
                    $items['build'][$build->getID()] = $build;

                foreach ($project->getEditions() as $edition)
                    $items['edition'][$edition->getID()] = $edition;

                foreach ($project->getComponents() as $component)
                    $items['component'][$component->getID()] = $component;

                foreach ($project->getMilestones() as $milestone)
                    $items['milestone'][$milestone->getID()] = $milestone;
            }

            $filters = [];
            $filters['build'] = entities\SearchFilter::createFilter('build');
            $filters['edition'] = entities\SearchFilter::createFilter('edition');
            $filters['component'] = entities\SearchFilter::createFilter('component');
            $filters['milestone'] = entities\SearchFilter::createFilter('milestone');
            if (isset($existing_ids['build'])) {
                foreach (tables\Builds::getTable()->getByIDs($existing_ids['build']) as $build)
                    $items['build'][$build->getID()] = $build;

                $filters['build']->setValue(join(',', $existing_ids['build']));
            }
            if (isset($existing_ids['edition'])) {
                foreach (tables\Editions::getTable()->getByIDs($existing_ids['edition']) as $edition)
                    $items['edition'][$edition->getID()] = $edition;

                $filters['edition']->setValue(join(',', $existing_ids['edition']));
            }
            if (isset($existing_ids['component'])) {
                foreach (tables\Components::getTable()->getByIDs($existing_ids['component']) as $component)
                    $items['component'][$component->getID()] = $component;

                $filters['component']->setValue(join(',', $existing_ids['component']));
            }
            if (isset($existing_ids['milestone'])) {
                foreach (tables\Milestones::getTable()->getByIDs($existing_ids['milestone']) as $milestone)
                    $items['milestone'][$milestone->getID()] = $milestone;

                $filters['milestone']->setValue(join(',', $existing_ids['milestone']));
            }

            foreach (['build', 'edition', 'component', 'milestone'] as $k) {
                $results[$k] = $this->getComponentHTML('search/interactivefilterdynamicchoicelist', ['filter' => $filters[$k], 'items' => $items[$k]]);
            }

            return $this->renderJSON(compact('results'));
        }

        public function extractIssues($matches)
        {
            $issue = entities\Issue::getIssueFromLink($matches["issues"]);
            if ($issue instanceof entities\Issue) {
                if (!Context::isProjectContext() || (Context::isProjectContext() && $issue->getProjectID() == Context::getCurrentProject()->getID())) {
                    $this->foundissues[$issue->getID()] = $issue;
                    $this->resultcount++;
                }
            }
        }

        public function runOpensearch(framework\Request $request)
        {

        }

        public function runSaveColumnSettings(framework\Request $request)
        {
            framework\Settings::saveSetting('search_scs_' . $request['template'], join(',', $request['columns']), 'core', 0, $this->getUser()->getID());

            return $this->renderJSON(['columns' => 'ok']);
        }

        public function runBulkUpdateIssues(framework\Request $request)
        {
            $issue_ids = $request['issue_ids'];
            $options = ['issue_ids' => array_values($issue_ids)];
            Context::loadLibrary('common');
            $options['last_updated'] = Context::getI18n()->formatTime(time(), 20);

            if (!empty($issue_ids)) {
                $options['bulk_action'] = $request['bulk_action'];
                switch ($request['bulk_action']) {
                    case 'assign_milestone':
                        $milestone = null;
                        if ($request['milestone'] == 'new') {
                            $milestone = new entities\Milestone();
                            $milestone->setProject(Context::getCurrentProject());
                            $milestone->setName($request['milestone_name']);
                            $milestone->save();
                            $options['milestone_url'] = Context::getRouting()->generate('agile_milestone', ['project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()]);
                        } elseif ($request['milestone']) {
                            $milestone = new entities\Milestone($request['milestone']);
                        }
                        $milestone_id = ($milestone instanceof entities\Milestone) ? $milestone->getID() : null;
                        foreach (array_keys($issue_ids) as $issue_id) {
                            if (is_numeric($issue_id)) {
                                $issue = new entities\Issue($issue_id);
                                $issue->setMilestone($milestone_id);
                                $issue->save();
                            }
                        }
                        $options['milestone_id'] = $milestone_id;
                        $options['milestone_name'] = ($milestone_id) ? $milestone->getName() : '-';
                        break;
                    case 'set_status':
                        if (is_numeric($request['status'])) {
                            $status = new entities\Status($request['status']);
                            foreach (array_keys($issue_ids) as $issue_id) {
                                if (is_numeric($issue_id)) {
                                    $issue = new entities\Issue($issue_id);
                                    $issue->setStatus($status->getID());
                                    $issue->save();
                                }
                            }
                            $options['status'] = ['color' => $status->getColor(), 'name' => $status->getName(), 'id' => $status->getID()];
                        }
                        break;
                    case 'set_severity':
                        if (is_numeric($request['severity'])) {
                            $severity = ($request['severity']) ? new entities\Severity($request['severity']) : null;
                            foreach (array_keys($issue_ids) as $issue_id) {
                                if (is_numeric($issue_id)) {
                                    $issue = new entities\Issue($issue_id);
                                    $severity_id = ($severity instanceof entities\Severity) ? $severity->getID() : 0;
                                    $issue->setSeverity($severity_id);
                                    $issue->save();
                                }
                            }
                            $options['severity'] = ['name' => ($severity instanceof entities\Severity) ? $severity->getName() : '-', 'id' => ($severity instanceof entities\Severity) ? $severity->getID() : 0];
                        }
                        break;
                    case 'set_resolution':
                        if (is_numeric($request['resolution'])) {
                            $resolution = ($request['resolution']) ? new entities\Resolution($request['resolution']) : null;
                            foreach (array_keys($issue_ids) as $issue_id) {
                                if (is_numeric($issue_id)) {
                                    $issue = new entities\Issue($issue_id);
                                    $resolution_id = ($resolution instanceof entities\Resolution) ? $resolution->getID() : 0;
                                    $issue->setResolution($resolution_id);
                                    $issue->save();
                                }
                            }
                            $options['resolution'] = ['name' => ($resolution instanceof entities\Resolution) ? $resolution->getName() : '-', 'id' => ($resolution instanceof entities\Resolution) ? $resolution->getID() : 0];
                        }
                        break;
                    case 'set_priority':
                        if (is_numeric($request['priority'])) {
                            $priority = ($request['priority']) ? new entities\Priority($request['priority']) : null;
                            foreach (array_keys($issue_ids) as $issue_id) {
                                if (is_numeric($issue_id)) {
                                    $issue = new entities\Issue($issue_id);
                                    $priority_id = ($priority instanceof entities\Priority) ? $priority->getID() : 0;
                                    $issue->setPriority($priority_id);
                                    $issue->save();
                                }
                            }
                            $options['priority'] = ['name' => ($priority instanceof entities\Priority) ? $priority->getName() : '-', 'id' => ($priority instanceof entities\Priority) ? $priority->getID() : 0];
                        }
                        break;
                    case 'set_category':
                        if (is_numeric($request['category'])) {
                            $category = ($request['category']) ? new entities\Category($request['category']) : null;
                            foreach (array_keys($issue_ids) as $issue_id) {
                                if (is_numeric($issue_id)) {
                                    $issue = new entities\Issue($issue_id);
                                    $category_id = ($category instanceof entities\Category) ? $category->getID() : 0;
                                    $issue->setCategory($category_id);
                                    $issue->save();
                                }
                            }
                            $options['category'] = ['name' => ($category instanceof entities\Category) ? $category->getName() : '-', 'id' => ($category instanceof entities\Category) ? $category->getID() : 0];
                        }
                        break;
                }
            }

            return $this->renderJSON($options);
        }

    }
