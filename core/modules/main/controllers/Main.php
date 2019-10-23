<?php

    namespace pachno\core\modules\main\controllers;

    use Exception;
    use Net_Http_Client;
    use pachno\core\entities;
    use pachno\core\entities\Category;
    use pachno\core\entities\Comment;
    use pachno\core\entities\common\Timeable;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Project;
    use pachno\core\entities\SavedSearch;
    use pachno\core\entities\tables;
    use pachno\core\entities\tables\Issues;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\entities\tables\Projects;
    use pachno\core\framework;
    use pachno\core\framework\Request;
    use pachno\core\framework\Response;
    use pachno\core\framework\Settings;
    use pachno\core\helpers\MentionableProvider;
    use pachno\core\helpers\Pagination;
    use PragmaRX\Google2FA\Google2FA;

    /**
     * @property Project $selected_project
     * @property entities\Client $client
     * @property entities\Team $team
     *
     * actions for the main module
     */
    class Main extends framework\Action
    {

        /**
         * The currently selected project in actions where there is one
         *
         * @access protected
         *
         * @param Request $request
         * @param $action
         *
         * @property Project $selected_project
         */
        public function preExecute(Request $request, $action)
        {
            try {
                if ($project_key = $request['project_key'])
                    $this->selected_project = Project::getByKey($project_key);
                elseif ($project_id = (int)$request['project_id'])
                    $this->selected_project = Projects::getTable()->selectById($project_id);

                if ($this->selected_project instanceof Project && !$this->selected_project->hasAccess($this->getUser())) {
                    $this->selected_project = null;
                }

                framework\Context::setCurrentProject($this->selected_project);
            } catch (Exception $e) {

            }
        }

        /**
         * Go to the next/previous open issue
         *
         * @param Request $request
         *
         * @throws Exception
         */
        public function runNavigateIssue(Request $request)
        {
            $issue = $this->_getIssueFromRequest($request);

            if (!$issue instanceof Issue) {
                $this->getResponse()->setTemplate('viewissue');

                return;
            }

            do {
                if ($issue->getMilestone() instanceof entities\Milestone) {
                    if ($request['direction'] == 'next') {
                        $found_issue = Issues::getTable()->getNextIssueFromIssueMilestoneOrderAndMilestoneID($issue->getMilestoneOrder(), $issue->getMilestone()->getID(), $request['mode'] == 'open');
                    } else {
                        $found_issue = Issues::getTable()->getPreviousIssueFromIssueMilestoneOrderAndMilestoneID($issue->getMilestoneOrder(), $issue->getMilestone()->getID(), $request['mode'] == 'open');
                    }
                } else {
                    if ($request['direction'] == 'next') {
                        $found_issue = Issues::getTable()->getNextIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
                    } else {
                        $found_issue = Issues::getTable()->getPreviousIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
                    }
                }
                if ($found_issue === null)
                    break;
            } while ($found_issue instanceof Issue && !$found_issue->hasAccess());

            if ($found_issue instanceof Issue) {
                $this->forward($this->getRouting()->generate('viewissue', ['project_key' => $found_issue->getProject()->getKey(), 'issue_no' => $found_issue->getFormattedIssueNo()]));
            } else {
                framework\Context::setMessage('issue_message', $this->getI18n()->__('There are no more issues in that direction.'));
                $this->forward($this->getRouting()->generate('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]));
            }
        }

        /**
         * Detect and return the requested issue
         *
         * @param Request $request
         *
         * @return Issue
         * @throws Exception
         */
        protected function _getIssueFromRequest(Request $request)
        {
            $issue = null;
            if ($issue_no = framework\Context::getRequest()->getParameter('issue_no')) {
                $issue = Issue::getIssueFromLink($issue_no);
                if ($issue instanceof Issue) {
                    if (!$this->selected_project instanceof Project || $issue->getProjectID() != $this->selected_project->getID()) {
                        $issue = null;
                    }
                } else {
                    framework\Logging::log("Issue no [$issue_no] not a valid issue no", 'main', framework\Logging::LEVEL_WARNING_RISK);
                }
            }
            framework\Logging::log('done (Loading issue)');
            if ($issue instanceof Issue && (!$issue->hasAccess() || $issue->isDeleted()))
                $issue = null;

            return $issue;
        }

        /**
         * View an issue
         *
         * @param Request $request
         */
        public function runEditIssue(Request $request)
        {
        }

        /**
         * View an issue
         *
         * @param Request $request
         */
        public function runViewIssue(Request $request)
        {
            framework\Logging::log('Loading issue');

            $issue = $this->_getIssueFromRequest($request);

            if ($issue instanceof Issue) {
                if (!array_key_exists('viewissue_list', $_SESSION) || !is_array($_SESSION['viewissue_list'])) {
                    $_SESSION['viewissue_list'] = [];
                }

                $k = array_search($issue->getID(), $_SESSION['viewissue_list']);
                if ($k !== false)
                    unset($_SESSION['viewissue_list'][$k]);

                array_push($_SESSION['viewissue_list'], $issue->getID());

                if (count($_SESSION['viewissue_list']) > 10)
                    array_shift($_SESSION['viewissue_list']);

                $this->getUser()->markNotificationsRead('issue', $issue->getID());

                framework\Context::getUser()->setNotificationSetting(Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), false);

                framework\Event::createNew('core', 'viewissue', $issue)->trigger();
            }

            $message = framework\Context::getMessageAndClear('issue_saved');
            $uploaded = framework\Context::getMessageAndClear('issue_file_uploaded');

            if (framework\Context::hasMessage('issue_deleted_shown') && (is_null($issue) || ($issue instanceof Issue && $issue->isDeleted()))) {
                $request_referer = ($request['referer'] ?: isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

                if ($request_referer) {
                    return $this->forward($request_referer);
                }
            } elseif (framework\Context::hasMessage('issue_deleted')) {
                $this->issue_deleted = framework\Context::getMessageAndClear('issue_deleted');
                framework\Context::setMessage('issue_deleted_shown', true);
            } elseif ($message == true) {
                $this->issue_saved = true;
            } elseif ($uploaded == true) {
                $this->issue_file_uploaded = true;
            } elseif (framework\Context::hasMessage('issue_error')) {
                $this->error = framework\Context::getMessageAndClear('issue_error');
            } elseif (framework\Context::hasMessage('issue_message')) {
                $this->issue_message = framework\Context::getMessageAndClear('issue_message');
            }

            $this->issue = $issue;
            $event = framework\Event::createNew('core', 'viewissue', $issue)->trigger();
            $this->listenViewIssuePostError($event);
        }

        public function listenViewIssuePostError(framework\Event $event)
        {
            if (framework\Context::hasMessage('comment_error')) {
                $this->comment_error = true;
                $this->error = framework\Context::getMessageAndClear('comment_error');
                $this->comment_error_body = framework\Context::getMessageAndClear('comment_error_body');
            }
        }

        public function runMoveIssue(Request $request)
        {
            $issue = null;
            $project = null;
            $multi = (bool)$request->getParameter('multi', false);
            try {
                if ($issue_id = $request['issue_id']) {
                    $issue = Issues::getTable()->selectById($issue_id);
                }
                if ($project_id = $request['project_id']) {
                    $project = Projects::getTable()->selectById($project_id);
                }
            } catch (Exception $e) {
            }

            if ($issue instanceof Issue && !$issue->hasAccess()) {
                $issue = null;
            }
            if ($project instanceof Project && !$project->hasAccess()) {
                $project = null;
            }

            if (!$issue instanceof Issue) {
                if ($multi) {
                    $this->getResponse()->setHttpStatus(404);

                    return $this->renderJSON(['error' => $this->getI18n()->__('Cannot find the issue specified')]);
                }

                return $this->return404(framework\Context::getI18n()->__('Cannot find the issue specified'));
            }

            if (!$issue->canEditIssueDetails()) {
                $this->getResponse()->setHttpStatus(403);

                return $this->forward403($this->getI18n()->__("You don't have permission to move this issue"));
            }

            if (!$project instanceof Project) {
                if ($multi) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('Cannot find the project specified')]);
                }

                return $this->return404(framework\Context::getI18n()->__('Cannot find the project specified'));
            }

            if ($issue->getProject()->getID() != $project->getID()) {
                $issue->setProject($project);

                if (!$issue->canEditIssueDetails()) {
                    $this->getResponse()->setHttpStatus(403);

                    return $this->forward403($this->getI18n()->__("You don't have permission to move this issue"));
                }

                $issue->clearUserWorkingOnIssue();
                $issue->clearAssignee();
                $issue->clearOwner();
                $issue->setPercentCompleted(0);
                $issue->setMilestone(null);
                $issue->setIssueNumber(Issues::getTable()->getNextIssueNumberForProductID($project->getID()));
                $step = $issue->getProject()->getWorkflowScheme()->getWorkflowForIssuetype($issue->getIssueType())->getFirstStep();
                $step->applyToIssue($issue);
                $issue->save();
                if ($multi) {
                    return $this->renderJSON(['content' => $this->getComponentHTML('issuemoved', compact('issue', 'project'))]);
                }
                framework\Context::setMessage('issue_message', framework\Context::getI18n()->__('The issue was moved'));
            } else {
                if ($multi) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => $this->getI18n()->__('The issue was not moved, since the project is the same')]);
                }
                framework\Context::setMessage('issue_error', framework\Context::getI18n()->__('The issue was not moved, since the project is the same'));
            }

            return $this->forward($this->getRouting()->generate('viewissue', ['project_key' => $project->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]));
        }

        /**
         * Frontpage
         *
         * @param Request $request
         */
        public function runIndex(Request $request)
        {
            if (Settings::isSingleProjectTracker()) {
                if (($projects = Project::getAllRootProjects(false)) && $project = array_shift($projects)) {
                    $this->forward($this->getRouting()->generate('project_dashboard', ['project_key' => $project->getKey()]));
                }
            }
            $this->forward403unless($this->getUser()->hasPageAccess('home'));
            $this->links = tables\Links::getTable()->getMainLinks();
            $this->show_project_list = Settings::isFrontpageProjectListVisible();
        }

        /**
         * @param Request $request
         * @Route(url="/projects/list/:list_mode/:project_state/*", name="project_list")
         */
        public function runProjectList(Request $request)
        {
            $list_mode = $request->getParameter('list_mode', 'all');
            $project_state = $request->getParameter('project_state', 'active');
            $paginationOptions = [
                'list_mode' => $list_mode,
                'project_state' => $project_state
            ];

            switch ($list_mode) {
                case 'all':
                    $projects = Project::getAllRootProjects(($project_state === 'archived'));
                    break;
                case 'team':
                    $paginationOptions['team_id'] = $request['team_id'];
                    $this->team = tables\Teams::getTable()->selectById($request['team_id']);
                    list ($activeProjects, $archivedProjects) = $this->team->getProjects();
                    $projects = ($project_state === 'active') ? $activeProjects : $archivedProjects;
                    break;
                case 'client':
                    $paginationOptions['client_id'] = $request['client_id'];
                    $this->client = tables\Clients::getTable()->selectById($request['client_id']);
                    list ($activeProjects, $archivedProjects) = $this->client->getProjects();
                    $projects = ($project_state === 'active') ? $activeProjects : $archivedProjects;
                    break;
            }

            $pagination = new Pagination($projects, $this->getRouting()->generate('project_list', $paginationOptions), $request);
            $this->pagination = $pagination;
            $this->projects = $pagination->getPageItems();
            $this->project_count = count($projects);
            $this->list_mode = $list_mode;
            $this->project_state = $project_state;
            $this->show_project_config_link = $this->getUser()->canAccessConfigurationPage(Settings::CONFIGURATION_SECTION_PROJECTS) && framework\Context::getScope()->hasProjectsAvailable();
        }

        public function runUserdata(Request $request)
        {
            if ($this->getUser()->isGuest()) {
                return $this->renderJSON([]);
            } else {
                $data = [];
                if ($request->isPost()) {
                    switch ($request['say']) {
                        case 'install-module':
                            try {
                                entities\Module::downloadModule($request['module_key']);
                                $module = entities\Module::installModule($request['module_key']);
                                $data['installed'] = true;
                                $data['module_key'] = $request['module_key'];
                                $data['module'] = $this->getComponentHTML('configuration/modulebox', ['module' => $module]);
                            } catch (framework\exceptions\ModuleDownloadException $e) {
                                $this->getResponse()->setHttpStatus(400);
                                switch ($e->getCode()) {
                                    case framework\exceptions\ModuleDownloadException::JSON_NOT_FOUND:
                                        return $this->renderJSON(['message' => $this->getI18n()->__('An error occured when trying to retrieve the module data')]);
                                        break;
                                    case framework\exceptions\ModuleDownloadException::FILE_NOT_FOUND:
                                        return $this->renderJSON(['message' => $this->getI18n()->__('The module could not be downloaded')]);
                                        break;
                                    case framework\exceptions\ModuleDownloadException::READONLY_TARGET:
                                        return $this->renderJSON(['title' => $this->getI18n()->__('Error extracting module zip'), 'message' => $this->getI18n()->__('Could not extract the module into the destination folder. Please check permissions.')]);
                                        break;
                                }
                            } catch (Exception $e) {
                                $this->getResponse()->setHttpStatus(400);

                                return $this->renderJSON(['message' => $this->getI18n()->__('An error occured when trying to install the module')]);
                            }
                            break;
                        case 'install-theme':
                            try {
                                entities\Module::downloadTheme($request['theme_key']);
                                $data['installed'] = true;
                                $data['theme_key'] = $request['theme_key'];
                                $themes = framework\Context::getThemes();
                                $data['theme'] = $this->getComponentHTML('configuration/theme', ['theme' => $themes[$request['theme_key']]]);
                            } catch (framework\exceptions\ModuleDownloadException $e) {
                                $this->getResponse()->setHttpStatus(400);
                                switch ($e->getCode()) {
                                    case framework\exceptions\ModuleDownloadException::JSON_NOT_FOUND:
                                        return $this->renderJSON(['message' => $this->getI18n()->__('An error occured when trying to retrieve the module data')]);
                                        break;
                                    case framework\exceptions\ModuleDownloadException::FILE_NOT_FOUND:
                                        return $this->renderJSON(['message' => $this->getI18n()->__('The module could not be downloaded')]);
                                        break;
                                }
                            } catch (Exception $e) {
                                $this->getResponse()->setHttpStatus(400);

                                return $this->renderJSON(['message' => $this->getI18n()->__('An error occured when trying to install the module')]);
                            }
                            break;
                        case 'notificationstatus':
                            $notification = tables\Notifications::getTable()->selectById($request['notification_id']);
                            $data['notification_id'] = $request['notification_id'];
                            $data['is_read'] = 1;
                            if ($notification instanceof entities\Notification) {
                                $notification->setIsRead(!$notification->isRead());
                                $notification->save();
                                $data['is_read'] = (int)$notification->isRead();
                                $this->getUser()->markNotificationGroupedNotificationsRead($notification);
                            }
                            break;
                        case 'notificationsread':
                            $this->getUser()->markAllNotificationsRead();
                            $data['all'] = 'read';
                            break;
                        case 'togglecommentsorder':
                            $direction = $this->getUser()->getCommentSortOrder();
                            $new_direction = ($direction == 'asc') ? 'desc' : 'asc';

                            $this->getUser()->setCommentSortOrder($new_direction);
                            $data['new_direction'] = $new_direction;
                            break;
                    }
                } else {
                    switch ($request['say']) {
                        case 'get_module_updates':
                            $addons_param = [];
                            $addons_json = [];
                            foreach ($request['addons'] as $addon) {
                                $addons_param[] = 'addons[]=' . $addon;
                            }
                            try {
                                $client = new Net_Http_Client();
                                $client->get('https://pachno.com/addons.json?' . join('&', $addons_param));
                                $addons_json = json_decode($client->getBody(), true);
                            } catch (Exception $e) {
                            }

                            return $this->renderJSON($addons_json);
                            break;
                        case 'getsearchcounts':
                            $counts_json = [];
                            foreach ($request['search_ids'] as $search_id) {
                                if (is_numeric($search_id)) {
                                    $search = tables\SavedSearches::getTable()->selectById($search_id);
                                } else {
                                    $predefined_id = str_replace('predefined_', '', $search_id);
                                    $search = SavedSearch::getPredefinedSearchObject($predefined_id);
                                }
                                if ($search instanceof SavedSearch) {
                                    $counts_json[$search_id] = $search->getTotalNumberOfIssues();
                                }
                            }

                            return $this->renderJSON($counts_json);
                            break;
                        case 'get_theme_updates':
                            $addons_param = [];
                            $addons_json = [];
                            foreach ($request['addons'] as $addon) {
                                $addons_param[] = 'themes[]=' . $addon;
                            }
                            try {
                                $client = new Net_Http_Client();
                                $client->get('https://pachno.com/themes.json?' . join('&', $addons_param));
                                $addons_json = json_decode($client->getBody(), true);
                            } catch (Exception $e) {
                            }

                            return $this->renderJSON($addons_json);
                            break;
                        case 'verify_module_update_file':
                            $filename = PACHNO_CACHE_PATH . $request['module_key'] . '.zip';
                            $exists = file_exists($filename) && dirname($filename) . DS == PACHNO_CACHE_PATH;

                            return $this->renderJSON(['verified' => (int)$exists]);
                            break;
                        case 'get_modules':
                            return $this->renderComponent('configuration/onlinemodules');
                            break;
                        case 'get_themes':
                            return $this->renderComponent('configuration/onlinethemes');
                            break;
                        case 'get_mentionables':
                            switch ($request['target_type']) {
                                case 'issue':
                                    $target = Issues::getTable()->selectById($request['target_id']);
                                    break;
                                case 'article':
                                    $target = tables\Articles::getTable()->selectById($request['target_id']);
                                    break;
                                case 'project':
                                    $target = Projects::getTable()->selectById($request['target_id']);
                                    break;
                            }
                            $mentionables = [];
                            if (isset($target) && $target instanceof MentionableProvider) {
                                foreach ($target->getMentionableUsers() as $user) {
                                    if ($user->isOpenIdLocked())
                                        continue;
                                    $mentionables[$user->getID()] = ['username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                }
                            }
                            foreach ($this->getUser()->getFriends() as $user) {
                                if ($user->isOpenIdLocked())
                                    continue;
                                $mentionables[$user->getID()] = ['username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                            }
                            foreach ($this->getUser()->getTeams() as $team) {
                                foreach ($team->getMembers() as $user) {
                                    if ($user->isOpenIdLocked())
                                        continue;
                                    $mentionables[$user->getID()] = ['username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                }
                            }
                            foreach ($this->getUser()->getClients() as $client) {
                                foreach ($client->getMembers() as $user) {
                                    if ($user->isOpenIdLocked())
                                        continue;
                                    $mentionables[$user->getID()] = ['username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                }
                            }
                            $data['mentionables'] = array_values($mentionables);
                            break;
                        case 'loadcomments':
                            switch ($request['target_type']) {
                                case entities\Comment::TYPE_ISSUE:
                                    $target = Issues::getTable()->selectById($request['target_id']);
                                    $data['comments'] = $this->getComponentHTML('main/commentlist', [
                                        'comment_count_div' => 'viewissue_comment_count',
                                        'mentionable_target_type' => 'issue',
                                        'target_type' => Comment::TYPE_ISSUE,
                                        'target_id' => $target->getID(),
                                        'issue' => $target
                                    ]);
                                    break;
                                case entities\Comment::TYPE_ARTICLE:
                                    $target = tables\Articles::getTable()->selectById($request['target_id']);
                                    $data['comments'] = $this->getComponentHTML('main/commentlist', [
                                        'comment_count_div' => 'article_comment_count',
                                        'mentionable_target_type' => 'article',
                                        'target_type' => Comment::TYPE_ARTICLE,
                                        'target_id' => $target->getID(),
                                        'article' => $target
                                    ]);
                                    break;
                            }
                            break;
                        default:
                            $data['unread_notifications_count'] = $this->getUser()->getNumberOfUnreadNotifications();
                            $data['unread_notifications'] = [];
                            foreach ($this->getUser()->getUnreadNotifications() as $unread_notification) {
                                $data['unread_notifications'][] = $unread_notification->getID();
                            }
                            $data['poll_interval'] = Settings::getNotificationPollInterval();
                    }
                }

                return $this->renderJSON($data);
            }
        }

        /**
         * Developer dashboard
         *
         * @param Request $request
         */
        public function runDashboard(Request $request)
        {
            $this->forward403unless(!$this->getUser()->isGuest() && $this->getUser()->hasPageAccess('dashboard'));
            if (Settings::isSingleProjectTracker()) {
                if (($projects = Project::getAll()) && $project = array_shift($projects)) {
                    framework\Context::setCurrentProject($project);
                }
            }
            if ($request['dashboard_id']) {
                $dashboard = tables\Dashboards::getTable()->selectById((int)$request['dashboard_id']);
                if ($dashboard->getType() == entities\Dashboard::TYPE_PROJECT && !$dashboard->getProject()->hasAccess()) {
                    unset($dashboard);
                } elseif ($dashboard->getType() == entities\Dashboard::TYPE_USER && $dashboard->getUser()->getID() != framework\Context::getUser()->getID()) {
                    unset($dashboard);
                }
            }

            if (!isset($dashboard) || !$dashboard instanceof entities\Dashboard) {
                $dashboard = $this->getUser()->getDefaultDashboard();
            }

            if ($request->isPost()) {
                switch ($request['mode']) {
                    case 'add_view':
                        $sort_order = 1;
                        foreach ($dashboard->getViews() as $view) {
                            if ($view->getColumn() == $request['column'])
                                $sort_order++;
                        }
                        $view = new entities\DashboardView();
                        $view->setDashboard($dashboard);
                        $view->setType($request['view_type']);
                        $view->setDetail($request['view_subtype']);
                        $view->setColumn($request['column']);
                        $view->setSortOrder($sort_order);
                        $view->save();

                        framework\Context::setCurrentProject($view->getProject());

                        return $this->renderJSON(['view_content' => $this->getComponentHTML('main/dashboardview', ['view' => $view, 'show' => false]), 'view_id' => $view->getID()]);
                    case 'remove_view':
                        $deleted = 0;
                        foreach ($dashboard->getViews() as $view) {
                            if ($view->getID() == $request['view_id']) {
                                $deleted = $view->getID();
                                $view->delete();
                            }
                        }

                        return $this->renderJSON(['deleted_view' => $deleted]);
                }
            }

            $this->dashboard = $dashboard;
        }

        /**
         * Save dashboard configuration (AJAX call)
         *
         * @param Request $request
         */
        public function runDashboardSort(Request $request)
        {
            $column = $request['column'];
            foreach ($request['view_ids'] as $order => $view_id) {
                $view = tables\DashboardViews::getTable()->selectById($view_id);
                $view->setSortOrder($order);
                $view->setColumn($column);
                $view->save();
            }

            return $this->renderText('ok');
        }

        /**
         * Client Dashboard
         *
         * @param Request $request
         */
        public function runClientDashboard(Request $request)
        {
            $this->client = null;
            try {
                $this->client = tables\Clients::getTable()->selectById($request['client_id']);

                if (!$this->client instanceof entities\Client) {
                    return $this->return404(framework\Context::getI18n()->__('This client does not exist'));
                }
                $this->forward403unless($this->client->hasAccess());

                $this->users = $this->client->getMembers();
            } catch (Exception $e) {
                framework\Logging::log($e->getMessage(), 'core', framework\Logging::LEVEL_WARNING);

                return $this->return404(framework\Context::getI18n()->__('This client does not exist'));
            }
        }

        /**
         * Team Dashboard
         *
         * @param Request $request
         */
        public function runTeamDashboard(Request $request)
        {
            try {
                $this->team = tables\Teams::getTable()->selectById($request['team_id']);

                if (!$this->team instanceof entities\Team) {
                    return $this->return404(framework\Context::getI18n()->__('This team does not exist'));
                }

                $this->forward403unless($this->team->hasAccess());

                $this->users = $this->team->getMembers();
            } catch (Exception $e) {
                framework\Logging::log($e->getMessage(), 'core', framework\Logging::LEVEL_WARNING);

                return $this->return404(framework\Context::getI18n()->__('This team does not exist'));
            }
        }

        /**
         * User 2FA verification action
         * @Route(name="account_disable_2fa", url="/disable_2fa/:csrf_token", methods="POST")
         *
         * @param Request $request
         */
        public function runDisable2FA(Request $request)
        {
            $this->getUser()->set2faToken('');
            $this->getUser()->set2FaEnabled(false);
            $this->getUser()->save();

            foreach ($this->getUser()->getUserSessions() as $userSession) {
                $userSession->setIs2FaVerified(false);
                $userSession->save();
            }

            return $this->renderJSON(['disabled' => 'ok']);
        }

        public function runDisableTutorial(Request $request)
        {
            if (strlen(trim($request['key'])))
                $this->getUser()->disableTutorial($request['key']);

            return $this->renderJSON(['disabled' => $request['key']]);
        }

        /**
         * Registration logic
         *
         * @Route(name="register_check_username", url="/check/username")
         * @AnonymousRoute
         *
         * @param Request $request
         */
        public function runRegisterCheckUsernameAvailability(Request $request)
        {
            $username = mb_strtolower(trim($request['username']));
            $available = ($username != '') ? tables\Users::getTable()->isUsernameAvailable($username) : false;

            return $this->renderJSON(['available' => (bool)$available]);
        }

        /**
         * Registration logic
         *
         * @Route(name="register", url="/do/register")
         * @AnonymousRoute
         *
         * @param Request $request
         */
        public function runRegister(Request $request)
        {
            framework\Context::loadLibrary('common');
            $i18n = framework\Context::getI18n();
            $fields = [];

            try {
                $username = mb_strtolower(trim($request['username']));
                $buddyname = $request['buddyname'];
                $email = mb_strtolower(trim($request['email_address']));
                $confirmemail = mb_strtolower(trim($request['email_confirm']));
                $security = $request['verification_no'];
                $realname = $request['realname'];

                $available = tables\Users::getTable()->isUsernameAvailable($username);

                if (!$available) {
                    throw new Exception($i18n->__('This username is in use'));
                }

                if (!empty($buddyname) && !empty($email) && !empty($confirmemail) && !empty($security)) {
                    if ($email != $confirmemail) {
                        array_push($fields, 'email_address', 'email_confirm');
                        throw new Exception($i18n->__('The email address must be valid, and must be typed twice.'));
                    }

                    if ($security != $_SESSION['activation_number']) {
                        array_push($fields, 'verification_no');
                        throw new Exception($i18n->__('To prevent automatic sign-ups, enter the verification number shown below.'));
                    }

                    $email_ok = false;

                    if (pachno_check_syntax($email, "EMAIL")) {
                        $email_ok = true;
                    }

                    if ($email_ok && Settings::hasRegistrationDomainWhitelist()) {

                        $allowed_domains = preg_replace('/[[:space:]]*,[[:space:]]*/', '|', Settings::getRegistrationDomainWhitelist());
                        if (preg_match('/@(' . $allowed_domains . ')$/i', $email) == false) {
                            array_push($fields, 'email_address', 'email_confirm');
                            throw new Exception($i18n->__('Email adresses from this domain can not be used.'));
                        }
                    }

                    if ($email_ok == false) {
                        array_push($fields, 'email_address', 'email_confirm');
                        throw new Exception($i18n->__('The email address must be valid, and must be typed twice.'));
                    }

                    if ($security != $_SESSION['activation_number']) {
                        array_push($fields, 'verification_no');
                        throw new Exception($i18n->__('To prevent automatic sign-ups, enter the verification number shown below.'));
                    }

                    $password = entities\User::createPassword();
                    $user = new entities\User();
                    $user->setUsername($username);
                    $user->setRealname($realname);
                    $user->setBuddyname($buddyname);
                    $user->setGroup(Settings::getDefaultGroup());
                    $user->setEnabled();
                    $user->setPassword($password);
                    $user->setEmail($email);
                    $user->setJoined();
                    $user->save();

                    $_SESSION['activation_number'] = pachno_get_activation_number();

                    if ($user->isActivated()) {
                        framework\Context::setMessage('auto_password', $password);

                        return $this->renderJSON(['loginmessage' => $i18n->__('After pressing %continue, you need to set your password.', ['%continue' => $i18n->__('Continue')]), 'one_time_password' => $password, 'activated' => true]);
                    }

                    return $this->renderJSON(['loginmessage' => $i18n->__('The account has now been registered - check your email inbox for the activation email. Please be patient - this email can take up to two hours to arrive.'), 'activated' => false]);
                } else {
                    array_push($fields, 'email_address', 'email_confirm', 'buddyname', 'verification_no');
                    throw new Exception($i18n->__('You need to fill out all fields correctly.'));
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__($e->getMessage()), 'fields' => $fields]);
            }
        }

        /**
         * Activate newly registered account
         *
         * @Route(name="activate", url="/activate/:user/:key")
         * @AnonymousRoute
         *
         * @param Request $request
         */
        public function runActivate(Request $request)
        {
            $this->getResponse()->setPage('login');

            $user = tables\Users::getTable()->getByUsername(str_replace('%2E', '.', $request['user']));
            if ($user instanceof entities\User) {
                if ($user->getActivationKey() != $request['key']) {
                    framework\Context::setMessage('login_message_err', framework\Context::getI18n()->__('This activation link is not valid'));
                } else {
                    $user->setValidated(true);
                    $user->save();
                    framework\Context::setMessage('login_message', framework\Context::getI18n()->__('Your account has been activated! You can now log in with the username %user and the password in your activation email.', ['%user' => $user->getUsername()]));
                }
            } else {
                framework\Context::setMessage('login_message_err', framework\Context::getI18n()->__('This activation link is not valid'));
            }
            $this->forward($this->getRouting()->generate('login_page'));
        }

        /**
         * "My account" page
         *
         * @param Request $request
         */
        public function runMyAccount(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPageAccess('account'));
            $categories = Category::getAll();
            $projects = [];
            $project_subscription_key = Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS;
            $category_subscription_key = Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY;
            $category_notification_key = Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY;
            $subscriptionssettings = Settings::getSubscriptionsSettings();
            $notificationsettings = Settings:: getNotificationSettings();
            $selected_project_subscriptions = [];
            $selected_category_subscriptions = [];
            $selected_category_notifications = [];
            $this->all_projects_subscription = $this->getUser()->getNotificationSetting($project_subscription_key, false)->isOn();
            foreach (Project::getAll() as $project_id => $project) {
                if ($project->hasAccess()) {
                    $projects[$project_id] = $project;
                    if ($this->getUser()->getNotificationSetting($project_subscription_key . '_' . $project_id, false)->isOn()) {
                        $selected_project_subscriptions[] = $project_id;
                    }
                }
            }
            foreach ($categories as $category_id => $category) {
                if ($this->getUser()->getNotificationSetting($category_subscription_key . '_' . $category_id, false)->isOn()) {
                    $selected_category_subscriptions[] = $category_id;
                }
                if ($this->getUser()->getNotificationSetting($category_notification_key . '_' . $category_id, false)->isOn()) {
                    $selected_category_notifications[] = $category_id;
                }
            }
            $this->selected_project_subscriptions = ($this->all_projects_subscription) ? [] : $selected_project_subscriptions;
            $this->projects = $projects;
            $this->selected_category_subscriptions = $selected_category_subscriptions;
            $this->selected_category_notifications = $selected_category_notifications;
            $this->categories = $categories;
            $this->subscriptionssettings = $subscriptionssettings;
            $this->notificationsettings = $notificationsettings;
            $this->has_autopassword = framework\Context::hasMessage('auto_password');
            if ($this->has_autopassword) {
                $this->autopassword = framework\Context::getMessage('auto_password');
            }

            if ($request->isPost() && $request->hasParameter('mode')) {
                switch ($request['mode']) {
                    case 'information':
                        if (!$request['buddyname'] || !$request['email']) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please fill out all the required fields')]);
                        }
                        $this->getUser()->setBuddyname($request['buddyname']);
                        $this->getUser()->setRealname($request['realname']);
                        $this->getUser()->setHomepage($request['homepage']);
                        $this->getUser()->setEmailPrivate((bool)$request['email_private']);
                        $this->getUser()->setUsesGravatar((bool)$request['use_gravatar']);
                        $this->getUser()->setTimezone($request->getRawParameter('timezone'));
                        $this->getUser()->setLanguage($request['profile_language']);

                        if ($this->getUser()->getEmail() != $request['email']) {
                            if (framework\Event::createNew('core', 'changeEmail', $this->getUser(), ['email' => $request['email']])->triggerUntilProcessed()->isProcessed() == false) {
                                $this->getUser()->setEmail($request['email']);
                            }
                        }

                        $this->getUser()->save();

                        return $this->renderJSON(['title' => framework\Context::getI18n()->__('Profile information saved')]);
                        break;
                    case 'settings':
                        $this->getUser()->setPreferredWikiSyntax($request['syntax_articles']);
                        $this->getUser()->setPreferredIssuesSyntax($request['syntax_issues']);
                        $this->getUser()->setPreferredCommentsSyntax($request['syntax_comments']);
                        $this->getUser()->setKeyboardNavigationEnabled($request['enable_keyboard_navigation']);
                        $this->getUser()->save();

                        return $this->renderJSON(['title' => framework\Context::getI18n()->__('Profile settings saved')]);
                        break;
                    case 'notificationsettings':
                        $this->getUser()->setDesktopNotificationsNewTabEnabled($request['enable_desktop_notifications_new_tab']);
                        foreach ($subscriptionssettings as $setting => $description) {
                            if ($setting == Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY) {
                                foreach ($categories as $category_id => $category) {
                                    if ($request->hasParameter('core_' . $setting . '_' . $category_id)) {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, true);
                                    } else {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, false);
                                    }
                                }
                            } elseif ($setting == Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS) {
                                if ($request->hasParameter('core_' . $setting . '_all')) {
                                    $this->getUser()->setNotificationSetting($setting, true);
                                    foreach (Project::getAll() as $project_id => $project) {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $project_id, false);
                                    }
                                } else {
                                    $this->getUser()->setNotificationSetting($setting, false);
                                    foreach (Project::getAll() as $project_id => $project) {
                                        if ($request->hasParameter('core_' . $setting . '_' . $project_id)) {
                                            $this->getUser()->setNotificationSetting($setting . '_' . $project_id, true);
                                        } else {
                                            $this->getUser()->setNotificationSetting($setting . '_' . $project_id, false);
                                        }
                                    }
                                }
                            } else {
                                if ($request->hasParameter('core_' . $setting)) {
                                    $this->getUser()->setNotificationSetting($setting, true);
                                } else {
                                    $this->getUser()->setNotificationSetting($setting, false);
                                }
                            }
                        }

                        foreach ($notificationsettings as $setting => $description) {
                            if ($setting == Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY) {
                                foreach ($categories as $category_id => $category) {
                                    if ($request->hasParameter('core_' . $setting . '_' . $category_id)) {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, true);
                                    } else {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, false);
                                    }
                                }
                            } else {
                                if ($request->hasParameter('core_' . $setting)) {
                                    if ($setting == Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS) {
                                        $this->getUser()->setNotificationSetting($setting, $request->getParameter('core_' . $setting));
                                    } else {
                                        $this->getUser()->setNotificationSetting($setting, true);
                                    }
                                } else {
                                    $this->getUser()->setNotificationSetting($setting, false);
                                }
                            }
                        }

                        framework\Event::createNew('core', 'mainActions::myAccount::saveNotificationSettings')->trigger(compact('request', 'categories'));
                        $this->getUser()->save();

                        return $this->renderJSON(['title' => framework\Context::getI18n()->__('Notification settings saved')]);
                        break;
                    case 'module':
                        foreach (framework\Context::getAllModules() as $modules) {
                            foreach ($modules as $module_name => $module) {
                                if ($request['target_module'] == $module_name && $module->hasAccountSettings()) {
                                    try {
                                        if ($module->postAccountSettings($request)) {
                                            return $this->renderJSON(['title' => framework\Context::getI18n()->__('Settings saved')]);
                                        } else {
                                            $this->getResponse()->setHttpStatus(400);

                                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('An error occured')]);
                                        }
                                    } catch (Exception $e) {
                                        $this->getResponse()->setHttpStatus(400);

                                        return $this->renderJSON(['error' => framework\Context::getI18n()->__($e->getMessage())]);
                                    }
                                }
                            }
                        }
                        break;
                }
            }
            $this->rnd_no = rand();
            $this->languages = framework\I18n::getLanguages();
            $this->timezones = framework\I18n::getTimezones();
            $this->error = framework\Context::getMessageAndClear('error');
            $this->username_chosen = framework\Context::getMessageAndClear('username_chosen');
            $this->openid_used = framework\Context::getMessageAndClear('openid_used');
            $this->rsskey_generated = framework\Context::getMessageAndClear('rsskey_generated');

            $this->selected_tab = 'profile';
            if ($this->rsskey_generated)
                $this->selected_tab = 'security';
        }

        /**
         * Change password ajax action
         *
         * @param Request $request
         */
        public function runAccountRegenerateRssKey(Request $request)
        {
            $this->getUser()->regenerateRssKey();
            framework\Context::setMessage('rsskey_generated', true);

            return $this->forward($this->getRouting()->generate('account'));
        }

        /**
         * Change password ajax action
         *
         * @param Request $request
         */
        public function runAccountRemovePassword(Request $request)
        {
            $passwords = $this->getUser()->getApplicationPasswords();
            foreach ($passwords as $password) {
                if ($password->getID() == $request['id']) {
                    $password->delete();

                    return $this->renderJSON(['message' => $this->getI18n()->__('The application password has been deleted')]);
                }
            }

            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['error' => $this->getI18n()->__('Cannot delete this application-specific password')]);
        }

        /**
         * Add a new application password: ajax action
         *
         * @param Request $request
         */
        public function runAccountAddPassword(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPageAccess('account'));
            $name = trim($request['name']);
            if ($name) {
                framework\Logging::log('Adding new application password for user.', 'account', framework\Logging::LEVEL_INFO);
                $password = new entities\ApplicationPassword();
                $password->setUser($this->getUser());
                $password->setName($name);
                $visible_password = strtolower(entities\User::createPassword());
                // Internally creates a hash from this visible password & crypts that hash for storage
                $password->setPassword($visible_password);
                $password->save();
                $spans = '';

                for ($cc = 0; $cc < 4; $cc++) {
                    $spans .= '<span>' . substr($visible_password, $cc * 4, 4) . '</span>';
                }

                return $this->renderJSON(['password' => $spans]);
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('Please enter a valid name')]);
            }
        }

        /**
         * Change password ajax action
         *
         * @param Request $request
         */
        public function runAccountChangePassword(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPageAccess('account'));
            if ($request->isPost()) {
                if ($this->getUser()->canChangePassword() == false) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__("You're not allowed to change your password.")]);
                }
                if (!$request->hasParameter('current_password') || !$request['current_password']) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter your current password')]);
                }
                if (!$request->hasParameter('new_password_1') || !$request['new_password_1']) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter a new password')]);
                }
                if (!$request->hasParameter('new_password_2') || !$request['new_password_2']) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter the new password twice')]);
                }
                if (!$this->getUser()->hasPassword($request['current_password'])) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter your current password')]);
                }
                if ($request['new_password_1'] != $request['new_password_2']) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter the new password twice')]);
                }
                $this->getUser()->changePassword($request['new_password_1']);
                $this->getUser()->save();
                framework\Context::clearMessage('auto_password');

                return $this->renderJSON(['title' => framework\Context::getI18n()->__('Your new password has been saved')]);
            }
        }

        public function listen_issueCreate(framework\Event $event)
        {
            $request = framework\Context::getRequest();
            $issue = $event->getSubject();

            if ($issue->isUnlocked()) {
                $this->_unlockIssueAfter($request, $issue);
            } elseif ($issue->isLocked()) {
                $this->_lockIssueAfter($request, $issue);
            }
        }

        /**
         * @param Request $request
         * @param                   $issue
         */
        protected function _unlockIssueAfter(Request $request, $issue)
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
                framework\Context::setPermission('canviewissue', $issue->getID(), 'core', $uid, 0, 0, true);
            }
            foreach ($al_teams as $tid) {
                framework\Context::setPermission('canviewissue', $issue->getID(), 'core', 0, 0, $tid, true);
            }
        }

        /**
         * @param Request $request
         * @param                   $issue
         */
        protected function _lockIssueAfter(Request $request, $issue)
        {
            framework\Context::setPermission('canviewissue', $issue->getID(), 'core', 0, 0, 0, false);
            framework\Context::setPermission('canviewissue', $issue->getID(), 'core', $this->getUser()
                ->getID(), 0, 0, true);

            $al_users = $request->getParameter('access_list_users', []);
            $al_teams = $request->getParameter('access_list_teams', []);
            $i_al = $issue->getAccessList();
            foreach ($i_al as $k => $item) {
                if ($item['target'] instanceof entities\Team) {
                    $tid = $item['target']->getID();
                    if (array_key_exists($tid, $al_teams)) {
                        unset($i_al[$k]);
                    } else {
                        framework\Context::removePermission('canviewissue', $issue->getID(), 'core', 0, 0, $tid);
                    }
                } elseif ($item['target'] instanceof entities\User) {
                    $uid = $item['target']->getID();
                    if (array_key_exists($uid, $al_users)) {
                        unset($i_al[$k]);
                    } elseif ($uid != $this->getUser()
                            ->getID()
                    ) {
                        framework\Context::removePermission('canviewissue', $issue->getID(), 'core', $uid, 0, 0);
                    }
                }
            }
            foreach ($al_users as $uid) {
                framework\Context::setPermission('canviewissue', $issue->getID(), 'core', $uid, 0, 0, true);
            }
            foreach ($al_teams as $tid) {
                framework\Context::setPermission('canviewissue', $issue->getID(), 'core', 0, 0, $tid, true);
            }
        }

        /**
         * "Report issue" page
         *
         * @param Request $request
         */
        public function runReportIssue(Request $request)
        {
            $i18n = framework\Context::getI18n();
            $errors = [];
            $permission_errors = [];
            $this->issue = null;
            $this->getResponse()->setPage('reportissue');

            $this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);

            $this->forward403unless(framework\Context::getCurrentProject() instanceof Project && framework\Context::getCurrentProject()->hasAccess() && $this->getUser()->canReportIssues(framework\Context::getCurrentProject()));

            if ($request->isPost()) {
                if ($this->_postIssueValidation($request, $errors, $permission_errors)) {
                    try {
                        $issue = $this->_postIssue($request);
                        if ($request->hasParameter('files') && $request->hasParameter('file_description')) {
                            $files = $request['files'];
                            $file_descriptions = $request['file_description'];
                            foreach ($files as $file_id => $nothing) {
                                $file = tables\Files::getTable()->selectById((int)$file_id);
                                $file->setDescription($file_descriptions[$file_id]);
                                $file->save();
                                tables\IssueFiles::getTable()->addByIssueIDandFileID($issue->getID(), $file->getID());
                            }
                        }
                        if ($request['return_format'] == 'planning') {
                            $this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);
                            $options = [];
                            $options['selected_issuetype'] = $issue->getIssueType();
                            $options['selected_project'] = $this->selected_project;
                            $options['issuetypes'] = $this->issuetypes;
                            $options['issue'] = $issue;
                            $options['errors'] = $errors;
                            $options['permission_errors'] = $permission_errors;
                            $options['selected_milestone'] = $this->_getMilestoneFromRequest($request);
                            $options['selected_build'] = $this->_getBuildFromRequest($request);
                            $options['parent_issue'] = $this->_getParentIssueFromRequest($request);
                            $options['medium_backdrop'] = 1;

                            return $this->renderJSON(['content' => $this->getComponentHTML('main/reportissuecontainer', $options)]);
                        }
                        if ($request->getRequestedFormat() != 'json' && $issue->getProject()->getIssuetypeScheme()->isIssuetypeRedirectedAfterReporting($this->selected_issuetype)) {
                            $this->forward($this->getRouting()->generate('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]), 303);
                        } else {
                            $this->_clearReportIssueProperties();
                            $this->issue = $issue;
                        }
                    } catch (Exception $e) {
                        if ($request['return_format'] == 'planning') {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => $e->getMessage()]);
                        }
                        $errors[] = $e->getMessage();
                    }
                }
            }
            if ($request['return_format'] == 'planning') {
                $err_msg = [];
                foreach ($errors as $field => $value) {
                    $err_msg[] = $i18n->__('Please provide a value for the %field_name field', ['%field_name' => $field]);
                }
                foreach ($permission_errors as $field => $value) {
                    $err_msg[] = $i18n->__("The %field_name field is marked as required, but you don't have permission to set it", ['%field_name' => $field]);
                }
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__('An error occured while creating this story: %errors', ['%errors' => '']), 'message' => join('<br>', $err_msg)]);
            }
            $this->errors = $errors;
            $this->permission_errors = $permission_errors;
            $this->options = $this->getParameterHolder();
        }

        protected function _loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction(Request $request)
        {
            try {
                if ($project_key = $request['project_key'])
                    $this->selected_project = Project::getByKey($project_key);
                elseif ($project_id = $request['project_id'])
                    $this->selected_project = Projects::getTable()->selectById($project_id);
            } catch (Exception $e) {

            }

            if ($this->selected_project instanceof Project)
                framework\Context::setCurrentProject($this->selected_project);
            if ($this->selected_project instanceof Project)
                $this->issuetypes = $this->selected_project->getIssuetypeScheme()->getIssuetypes();
            else
                $this->issuetypes = entities\Issuetype::getAll();

            $this->selected_issuetype = null;
            if ($request->hasParameter('issuetype'))
                $this->selected_issuetype = entities\Issuetype::getByKeyish($request['issuetype']);

            $this->locked_issuetype = (bool)$request['lock_issuetype'];

            if (!$this->selected_issuetype instanceof entities\Issuetype) {
                $this->issuetype_id = $request['issuetype_id'];
                if ($this->issuetype_id) {
                    try {
                        $this->selected_issuetype = tables\Issuetypes::getTable()->selectById($this->issuetype_id);
                    } catch (Exception $e) {

                    }
                }
            } else {
                $this->issuetype_id = $this->selected_issuetype->getID();
            }
        }

        protected function _postIssueValidation(Request $request, &$errors, &$permission_errors)
        {
            $i18n = framework\Context::getI18n();
            if (!$this->selected_project instanceof Project)
                $errors['project'] = $i18n->__('You have to select a valid project');
            if (!$this->selected_issuetype instanceof entities\Issuetype)
                $errors['issuetype'] = $i18n->__('You have to select a valid issue type');
            if (empty($errors)) {
                $fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);

                $this->title = $request->getRawParameter('title');
                $this->selected_shortname = $request->getRawParameter('shortname', null);
                $this->selected_description = $request->getRawParameter('description', null);
                $this->selected_description_syntax = $request->getRawParameter('description_syntax', null);
                $this->selected_reproduction_steps = $request->getRawParameter('reproduction_steps', null);
                $this->selected_reproduction_steps_syntax = $request->getRawParameter('reproduction_steps_syntax', null);

                if ($edition_id = (int)$request['edition_id'])
                    $this->selected_edition = tables\Editions::getTable()->selectById($edition_id);
                if ($build_id = (int)$request['build_id'])
                    $this->selected_build = tables\Builds::getTable()->selectById($build_id);
                if ($component_id = (int)$request['component_id'])
                    $this->selected_component = tables\Components::getTable()->selectById($component_id);

                if (trim($this->title) == '' || $this->title == $this->default_title)
                    $errors['title'] = true;
                if (isset($fields_array['shortname']) && $fields_array['shortname']['required'] && trim($this->selected_shortname) == '')
                    $errors['shortname'] = true;
                if (isset($fields_array['description']) && $fields_array['description']['required'] && trim($this->selected_description) == '')
                    $errors['description'] = true;
                if (isset($fields_array['reproduction_steps']) && !$request->isAjaxCall() && $fields_array['reproduction_steps']['required'] && trim($this->selected_reproduction_steps) == '')
                    $errors['reproduction_steps'] = true;

                if (isset($fields_array['edition']) && $edition_id && !in_array($edition_id, array_keys($fields_array['edition']['values'])))
                    $errors['edition'] = true;

                if (isset($fields_array['build']) && $build_id && !in_array($build_id, array_keys($fields_array['build']['values'])))
                    $errors['build'] = true;

                if (isset($fields_array['component']) && $component_id && !in_array($component_id, array_keys($fields_array['component']['values'])))
                    $errors['component'] = true;

                if ($category_id = (int)$request['category_id']) {
                    $category = tables\ListTypes::getTable()->selectById($category_id);

                    if (!$category->hasAccess()) {
                        $errors['category'] = true;
                    } else {
                        $this->selected_category = $category;
                    }
                }

                if ($status_id = (int)$request['status_id'])
                    $this->selected_status = tables\ListTypes::getTable()->selectById($status_id);

                if ($reproducability_id = (int)$request['reproducability_id'])
                    $this->selected_reproducability = tables\ListTypes::getTable()->selectById($reproducability_id);

                if ($milestone_id = (int)$request['milestone_id']) {
                    $milestone = $this->_getMilestoneFromRequest($request);

                    if (!$milestone instanceof entities\Milestone) {
                        $errors['milestone'] = true;
                    } else {
                        $this->selected_milestone = $milestone;
                    }
                }

                if ($parent_issue_id = (int)$request['parent_issue_id'])
                    $this->parent_issue = Issues::getTable()->selectById($parent_issue_id);

                if ($resolution_id = (int)$request['resolution_id'])
                    $this->selected_resolution = tables\ListTypes::getTable()->selectById($resolution_id);

                if ($severity_id = (int)$request['severity_id'])
                    $this->selected_severity = tables\ListTypes::getTable()->selectById($severity_id);

                if ($priority_id = (int)$request['priority_id'])
                    $this->selected_priority = tables\ListTypes::getTable()->selectById($priority_id);

                if ($request['estimated_time'])
                    $this->selected_estimated_time = $request['estimated_time'];

                if ($request['spent_time'])
                    $this->selected_spent_time = $request['spent_time'];

                if (is_numeric($request['percent_complete']))
                    $this->selected_percent_complete = (int)$request['percent_complete'];

                if ($pain_bug_type_id = (int)$request['pain_bug_type_id'])
                    $this->selected_pain_bug_type = $pain_bug_type_id;

                if ($pain_likelihood_id = (int)$request['pain_likelihood_id'])
                    $this->selected_pain_likelihood = $pain_likelihood_id;

                if ($pain_effect_id = (int)$request['pain_effect_id'])
                    $this->selected_pain_effect = $pain_effect_id;

                $selected_customdatatype = [];
                foreach (entities\CustomDatatype::getAll() as $customdatatype) {
                    $customdatatype_id = $customdatatype->getKey() . '_id';
                    $customdatatype_value = $customdatatype->getKey() . '_value';
                    if ($customdatatype->hasCustomOptions()) {
                        $selected_customdatatype[$customdatatype->getKey()] = null;
                        if ($request->hasParameter($customdatatype_id)) {
                            $customdatatype_id = (int)$request->getParameter($customdatatype_id);
                            $selected_customdatatype[$customdatatype->getKey()] = new entities\CustomDatatypeOption($customdatatype_id);
                        }
                    } else {
                        $selected_customdatatype[$customdatatype->getKey()] = null;
                        switch ($customdatatype->getType()) {
                            case entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                            case entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                if ($request->hasParameter($customdatatype_value))
                                    $selected_customdatatype[$customdatatype->getKey()] = $request->getParameter($customdatatype_value, null, false);

                                break;
                            default:
                                if ($request->hasParameter($customdatatype_value))
                                    $selected_customdatatype[$customdatatype->getKey()] = $request->getParameter($customdatatype_value);
                                elseif ($request->hasParameter($customdatatype_id))
                                    $selected_customdatatype[$customdatatype->getKey()] = $request->getParameter($customdatatype_id);

                                break;
                        }
                    }
                }
                $this->selected_customdatatype = $selected_customdatatype;

                foreach ($fields_array as $field => $info) {
                    if ($field == 'user_pain') {
                        if ($info['required']) {
                            if (!($this->selected_pain_bug_type != 0 && $this->selected_pain_likelihood != 0 && $this->selected_pain_effect != 0)) {
                                $errors['user_pain'] = true;
                            }
                        }
                    } elseif ($info['required']) {
                        $var_name = "selected_{$field}";
                        if ((in_array($field, entities\Datatype::getAvailableFields(true)) && ($this->$var_name === null || $this->$var_name === 0)) || (!in_array($field, entities\DatatypeBase::getAvailableFields(true)) && !in_array($field, ['pain_bug_type', 'pain_likelihood', 'pain_effect']) && (array_key_exists($field, $selected_customdatatype) && $selected_customdatatype[$field] === null))) {
                            $errors[$field] = true;
                        }
                    } else {
                        if (in_array($field, entities\Datatype::getAvailableFields(true)) || in_array($field, ['pain_bug_type', 'pain_likelihood', 'pain_effect'])) {
                            if (!$this->selected_project->fieldPermissionCheck($field)) {
                                $permission_errors[$field] = true;
                            }
                        } elseif (!$this->selected_project->fieldPermissionCheck($field, true, true)) {
                            $permission_errors[$field] = true;
                        }
                    }
                }
                $event = new framework\Event('core', 'mainActions::_postIssueValidation', null, [], $errors);
                $event->trigger();
                $errors = $event->getReturnList();
            }

            return !(bool)(count($errors) + count($permission_errors));
        }

        protected function _getMilestoneFromRequest($request)
        {
            if ($request->hasParameter('milestone_id')) {
                try {
                    $milestone = Milestones::getTable()->selectById((int)$request['milestone_id']);
                    if ($milestone instanceof entities\Milestone && !$milestone->hasAccess()) $milestone = null;

                    return $milestone;
                } catch (Exception $e) {
                }
            }
        }

        protected function _postIssue(Request $request)
        {
            $fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);
            $issue = new Issue();
            $issue->setTitle($this->title);
            $issue->setIssuetype($this->issuetype_id);
            $issue->setProject($this->selected_project);
            if (isset($fields_array['shortname']))
                $issue->setShortname($this->selected_shortname);
            if (isset($fields_array['description'])) {
                $issue->setDescription($this->selected_description);
                $issue->setDescriptionSyntax($this->selected_description_syntax);
            }
            if (isset($fields_array['reproduction_steps'])) {
                $issue->setReproductionSteps($this->selected_reproduction_steps);
                $issue->setReproductionStepsSyntax($this->selected_reproduction_steps_syntax);
            }
            if (isset($fields_array['category']) && $this->selected_category instanceof entities\Datatype)
                $issue->setCategory($this->selected_category->getID());
            if (isset($fields_array['status']) && $this->selected_status instanceof entities\Datatype)
                $issue->setStatus($this->selected_status->getID());
            if (isset($fields_array['reproducability']) && $this->selected_reproducability instanceof entities\Datatype)
                $issue->setReproducability($this->selected_reproducability->getID());
            if (isset($fields_array['resolution']) && $this->selected_resolution instanceof entities\Datatype)
                $issue->setResolution($this->selected_resolution->getID());
            if (isset($fields_array['severity']) && $this->selected_severity instanceof entities\Datatype)
                $issue->setSeverity($this->selected_severity->getID());
            if (isset($fields_array['priority']) && $this->selected_priority instanceof entities\Datatype)
                $issue->setPriority($this->selected_priority->getID());
            if (isset($fields_array['estimated_time']))
                $issue->setEstimatedTime($this->selected_estimated_time);
            if (isset($fields_array['spent_time']))
                $issue->setSpentTime($this->selected_spent_time);
            if (isset($fields_array['milestone']) || isset($this->selected_milestone))
                $issue->setMilestone($this->selected_milestone);
            if (isset($fields_array['percent_complete']))
                $issue->setPercentCompleted($this->selected_percent_complete);
            if (isset($fields_array['pain_bug_type']))
                $issue->setPainBugType($this->selected_pain_bug_type);
            if (isset($fields_array['pain_likelihood']))
                $issue->setPainLikelihood($this->selected_pain_likelihood);
            if (isset($fields_array['pain_effect']))
                $issue->setPainEffect($this->selected_pain_effect);
            foreach (entities\CustomDatatype::getAll() as $customdatatype) {
                if (!isset($fields_array[$customdatatype->getKey()]))
                    continue;
                if ($customdatatype->hasCustomOptions()) {
                    if (isset($fields_array[$customdatatype->getKey()]) && $this->selected_customdatatype[$customdatatype->getKey()] instanceof entities\CustomDatatypeOption) {
                        $selected_option = $this->selected_customdatatype[$customdatatype->getKey()];
                        $issue->setCustomField($customdatatype->getKey(), $selected_option->getID());
                    }
                } else {
                    $issue->setCustomField($customdatatype->getKey(), $this->selected_customdatatype[$customdatatype->getKey()]);
                }
            }

            // FIXME: If we set the issue assignee during report issue, this needs to be set INSTEAD of this
            if ($this->selected_project->canAutoassign()) {
                if (isset($fields_array['component']) && $this->selected_component instanceof entities\Component && $this->selected_component->hasLeader()) {
                    $issue->setAssignee($this->selected_component->getLeader());
                } elseif (isset($fields_array['edition']) && $this->selected_edition instanceof entities\Edition && $this->selected_edition->hasLeader()) {
                    $issue->setAssignee($this->selected_edition->getLeader());
                } elseif ($this->selected_project->hasLeader()) {
                    $issue->setAssignee($this->selected_project->getLeader());
                }
            }

            if ($request->hasParameter('custom_issue_access') && $this->selected_project->permissionCheck('canlockandeditlockedissues')) {
                switch ($request->getParameter('issue_access')) {
                    case 'public':
                    case 'public_category':
                        $issue->setLocked(false);
                        $issue->setLockedCategory($request->hasParameter('public_category'));
                        break;
                    case 'restricted':
                        $issue->setLocked();
                        break;
                }
            } else {
                $issue->setLockedFromProject($this->selected_project);
            }

            framework\Event::listen('core', 'pachno\core\entities\Issue::createNew_pre_notifications', [$this, 'listen_issueCreate']);
            $issue->save();

            if (isset($this->parent_issue))
                $issue->addParentIssue($this->parent_issue);
            if (isset($fields_array['edition']) && $this->selected_edition instanceof entities\Edition)
                $issue->addAffectedEdition($this->selected_edition);
            if (isset($fields_array['build']) && $this->selected_build instanceof entities\Build)
                $issue->addAffectedBuild($this->selected_build);
            if (isset($fields_array['component']) && $this->selected_component instanceof entities\Component)
                $issue->addAffectedComponent($this->selected_component);

            return $issue;
        }

        protected function _getBuildFromRequest($request)
        {
            if ($request->hasParameter('build_id')) {
                try {
                    $build = tables\Builds::getTable()->selectById((int)$request['build_id']);

                    return $build;
                } catch (Exception $e) {
                }
            }
        }

        protected function _getParentIssueFromRequest($request)
        {
            if ($request->hasParameter('parent_issue_id')) {
                try {
                    $parent_issue = Issues::getTable()->selectById((int)$request['parent_issue_id']);

                    return $parent_issue;
                } catch (Exception $e) {
                }
            }
        }

        protected function _clearReportIssueProperties()
        {
            $this->title = null;
            $this->description = null;
            $this->description_syntax = null;
            $this->reproduction_steps = null;
            $this->reproduction_steps_syntax = null;
            $this->selected_category = null;
            $this->selected_status = null;
            $this->selected_reproducability = null;
            $this->selected_resolution = null;
            $this->selected_severity = null;
            $this->selected_priority = null;
            $this->selected_edition = null;
            $this->selected_build = null;
            $this->selected_component = null;
            $this->selected_estimated_time = null;
            $this->selected_spent_time = null;
            $this->selected_percent_complete = null;
            $this->selected_pain_bug_type = null;
            $this->selected_pain_likelihood = null;
            $this->selected_pain_effect = null;
            $selected_customdatatype = [];
            foreach (entities\CustomDatatype::getAll() as $customdatatype) {
                $selected_customdatatype[$customdatatype->getKey()] = null;
            }
            $this->selected_customdatatype = $selected_customdatatype;
        }

        /**
         * Retrieves the fields which are valid for that product and issue type combination
         *
         * @param Request $request
         */
        public function runReportIssueGetFields(Request $request)
        {
            if (!$this->selected_project instanceof Project) {
                return $this->renderText('invalid project');
            }

            $fields_array = $this->selected_project->getReportableFieldsArray($request['issuetype_id'], true);
            $available_fields = entities\DatatypeBase::getAvailableFields();
            $available_fields[] = 'pain_bug_type';
            $available_fields[] = 'pain_likelihood';
            $available_fields[] = 'pain_effect';

            return $this->renderJSON(['available_fields' => $available_fields, 'fields' => $fields_array]);
        }

        /**
         * Toggle favourite issue (starring)
         *
         * @param Request $request
         */
        public function runToggleFavouriteIssue(Request $request)
        {
            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                    $user = tables\Users::getTable()->selectById($request['user_id']);
                } catch (Exception $e) {
                    return $this->renderText('fail');
                }
            } else {
                return $this->renderText('no issue');
            }

            if ($user->isIssueStarred($issue_id)) {
                $retval = !$user->removeStarredIssue($issue_id);
            } else {
                $retval = $user->addStarredIssue($issue_id);
                if ($user->getID() != $this->getUser()->getID()) {
                    framework\Event::createNew('core', 'issue_subscribe_user', $issue, compact('user'))->trigger();
                }
            }


            return $this->renderText(json_encode(['starred' => $retval, 'subscriber' => $this->getComponentHTML('main/issuesubscriber', ['user' => $user, 'issue' => $issue]), 'count' => count($issue->getSubscribers())]));
        }

        public function runIssueDeleteTimeSpent(Request $request)
        {
            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                    $spenttime = tables\IssueSpentTimes::getTable()->selectById($request['entry_id']);

                    if ($spenttime instanceof entities\IssueSpentTime) {
                        $spenttime->delete();
                        $spenttime->getIssue()->save();
                    }

                    return $this->renderJSON(['deleted' => 'ok', 'issue_id' => $issue_id, 'timesum' => array_sum($issue->getSpentTime()), 'spenttime' => Issue::getFormattedTime($issue->getSpentTime(true, true)), 'percentbar' => $this->getComponentHTML('main/percentbar', ['percent' => $issue->getEstimatedPercentCompleted(), 'height' => 3])]);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderText('fail');
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderText('no issue');
            }
        }

        public function runIssueEditTimeSpent(Request $request)
        {
            $entry_id = $request['entry_id'];
            $spenttime = ($entry_id) ? tables\IssueSpentTimes::getTable()->selectById($entry_id) : new entities\IssueSpentTime();

            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderText('fail');
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderText('no issue');
            }

            framework\Context::loadLibrary('common');
            $spenttime->editOrAdd($issue, $this->getUser(), array_only_with_default($request->getParameters(), array_merge(['timespent_manual', 'timespent_specified_type', 'timespent_specified_value', 'timespent_activitytype', 'timespent_comment', 'edited_at'], Timeable::getUnitsWithPoints())));

            return $this->renderJSON(['edited' => 'ok', 'issue_id' => $issue_id, 'timesum' => array_sum($spenttime->getIssue()->getSpentTime()), 'spenttime' => Issue::getFormattedTime($spenttime->getIssue()->getSpentTime(true, true)), 'percentbar' => $this->getComponentHTML('main/percentbar', ['percent' => $issue->getEstimatedPercentCompleted(), 'height' => 3]), 'timeentries' => $this->getComponentHTML('main/issuespenttimes', ['issue' => $spenttime->getIssue()])]);
        }

        /**
         * Sets an issue field to a specified value
         *
         * @param Request $request
         */
        public function runIssueSetField(Request $request)
        {
            $issue_id = $request['issue_id'];
            try {
                $issue = Issues::getTable()->selectById($issue_id);
            } catch (Exception $e) {
            }

            framework\Context::loadLibrary('common');

            if (!isset($issue) || !$issue instanceof Issue) {
                $this->getResponse()->setHttpStatus(404);

                return $this->renderText('Issue not found');
            }

            $return_details = [
                'issue_id' => $issue->getId(),
                'changed' => []
            ];

            if (!$this->verifyCanEditField($issue, $request['field'])) {
                $this->getResponse()->setHttpStatus(403);

                return $this->renderJSON(['error' => 'You are not allowed to edit this field']);
            }

            switch ($request['field']) {
                case 'description':
                    $issue->setDescription($request->getRawParameter('value'));
                    $issue->setDescriptionSyntax($request->getParameter('value_syntax'));
                    $return_details['changed']['description'] = [
                        'value' => $issue->getParsedDescription(compact('issue'))
                    ];
                    break;
                case 'shortname':
                    $issue->setShortname($request->getRawParameter('shortname_value'));
                    $return_details['changed']['shortname'] = [
                        'value' => $issue->getShortname()
                    ];
                    break;
                case 'reproduction_steps':
                    $issue->setReproductionSteps($request->getRawParameter('value'));
                    $issue->setReproductionStepsSyntax($request->getParameter('value_syntax'));
                    $return_details['changed']['reproduction_steps'] = [
                        'value' => $issue->getParsedReproductionSteps(compact('issue'))
                    ];
                    break;
                case 'title':
                    if (!trim($request['value'])) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('You have to provide a title')]);
                    }

                    $issue->setTitle($request->getRawParameter('value'));
                    $return_details['changed']['title'] = [
                        'value' => strip_tags($issue->getTitle())
                    ];
                    break;
                case 'percent_complete':
                    $issue->setPercentCompleted($request['percent']);
                    $return_details['changed']['percent_complete'] = [
                        'value' => $issue->getPercentCompleted()
                    ];
                    break;
                case 'estimated_time':
                    if ($request['estimated_time']) {
                        $issue->setEstimatedTime($request['estimated_time']);
                    } elseif ($request->hasParameter('value')) {
                        $issue->setEstimatedTime($request['value']);
                    } else {
                        if ($request->hasParameter('months')) $issue->setEstimatedMonths($request['months']);
                        if ($request->hasParameter('weeks')) $issue->setEstimatedWeeks($request['weeks']);
                        if ($request->hasParameter('days')) $issue->setEstimatedDays($request['days']);
                        if ($request->hasParameter('hours')) $issue->setEstimatedHours($request['hours']);
                        if ($request->hasParameter('minutes')) $issue->setEstimatedMinutes($request['minutes']);
                        if ($request->hasParameter('points')) $issue->setEstimatedPoints($request['points']);
                    }
                    $return_details['changed']['estimated_time'] = [
                        'value' => Issue::getFormattedTime($issue->getEstimatedTime(true, true)),
                        'values' => $issue->getEstimatedTime(true, true)
                    ];
                    break;
                case 'posted_by':
                case 'owned_by':
                case 'assigned_to':
                    $identifiable_type = ($request->hasParameter('identifiable_type')) ? $request['identifiable_type'] : 'user';
                    if ($request['value'] && in_array($identifiable_type, ['team', 'user'])) {
                        switch ($identifiable_type) {
                            case 'user':
                                $identified = tables\Users::getTable()->selectById($request['value']);
                                break;
                            case 'team':
                                $identified = tables\Teams::getTable()->selectById($request['value']);
                                break;
                        }
                        if ($identified instanceof entities\common\Identifiable) {
                            if ($identified instanceof entities\User && (bool)$request->getParameter('teamup', false)) {
                                $team = new entities\Team();
                                $team->setName($identified->getBuddyname() . ' & ' . $this->getUser()->getBuddyname());
                                $team->setOndemand();
                                $team->save();
                                $team->addMember($identified);
                                $team->addMember($this->getUser());
                                $identified = $team;
                            }

                            if ($request['field'] == 'owned_by') {
                                $issue->setOwner($identified);
                                $return_details['changed']['owned_by'] = [
                                    'value' => $issue->getOwner()->getID()
                                ];
                            } elseif ($request['field'] == 'assigned_to') {
                                $issue->setAssignee($identified);
                                $return_details['changed']['assigned_to'] = [
                                    'value' => $issue->getAssignee()->getID()
                                ];
                            } elseif ($request['field'] == 'posted_by') {
                                $issue->setPostedBy($identified);
                                $return_details['changed']['posted_by'] = [
                                    'value' => $issue->getPostedBy()->getID()
                                ];
                            }
                        }
                    } elseif (!$request['value'] && $request['field'] != 'posted_by') {
                        if ($request['field'] == 'owned_by') {
                            $issue->clearOwner();
                        } elseif ($request['field'] == 'assigned_to') {
                            $issue->clearAssignee();
                        }
                        $return_details['changed'][$request['field']] = [
                            'value' => 0
                        ];
                    }
                    break;
                case 'category':
                case 'resolution':
                case 'severity':
                case 'reproducability':
                case 'priority':
                case 'milestone':
                case 'issuetype':
                case 'status':
                case 'pain_bug_type':
                case 'pain_likelihood':
                case 'pain_effect':
                    try {
                        $classname = null;
                        $parameter_name = mb_strtolower($request['field']);
                        $parameter_id_name = "{$parameter_name}_id";
                        $is_pain = in_array($parameter_name, ['pain_bug_type', 'pain_likelihood', 'pain_effect']);
                        if ($is_pain) {
                            switch ($parameter_name) {
                                case 'pain_bug_type':
                                    $set_function_name = 'setPainBugType';
                                    break;
                                case 'pain_likelihood':
                                    $set_function_name = 'setPainLikelihood';
                                    break;
                                case 'pain_effect':
                                    $set_function_name = 'setPainEffect';
                                    break;
                            }
                        } else {
                            $classname = "\\pachno\\core\\entities\\" . ucfirst($parameter_name);
                            $lab_function_name = $classname;
                            $set_function_name = 'set' . ucfirst($parameter_name);
                        }

                        if ($request->hasParameter($parameter_id_name)) {
                            $parameter_id = $request->getParameter($parameter_id_name);
                            if ($parameter_id !== 0) {
                                $is_valid = ($is_pain) ? in_array($parameter_id, array_keys(Issue::getPainTypesOrLabel($parameter_name))) : ($parameter_id == 0 || (($parameter = $lab_function_name::getB2DBTable()->selectByID($parameter_id)) instanceof $classname));
                            }
                            if ($parameter_id == 0 || $is_valid) {
                                $issue->$set_function_name($parameter_id);

                                $return_details['changed'][$request['field']] = [
                                    'value' => $parameter_id
                                ];
                            }
                        }
                    } catch (Exception $e) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => $e->getMessage()]);
                    }
                    break;
                default:
                    $custom_field = entities\CustomDatatype::getByKey($request['field']);
                    if (!$custom_field instanceof entities\CustomDatatype) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => 'Invalid custom field']);
                    }

                    $key = $custom_field->getKey();
                    $custom_field_value = $request->getRawParameter("{$key}_value");
                    if (!$custom_field_value) {
                        $issue->clearCustomField($key);
                    } else {
                        $issue->setCustomField($key, $custom_field_value);
                    }

                    $return_details['changed'][$request['field']] = [
                        'value' => $issue->getCustomField($key)
                    ];
                    break;
            }

            $issue->getWorkflow()->moveIssueToMatchingWorkflowStep($issue);
            // Currently if category is changed we want to regenerate permissions since category is used for granting user access.
            if ($issue->isPropertyChanged('category')) {
                framework\Event::listen('core', 'pachno\core\entities\Issue::save_pre_notifications', [$this, 'listen_issueCreate']);
            }
            $issue->save();

            return $this->renderJSON($return_details);
        }

        protected function verifyCanEditField(Issue $issue, $field)
        {
            switch ($field) {
                case 'description':
                    return $issue->canEditDescription();
                case 'shortname':
                    return $issue->canEditShortname();
                case 'reproduction_steps':
                    return $issue->canEditReproductionSteps();
                case 'title':
                    return $issue->canEditTitle();
                case 'percent_complete':
                    return $issue->canEditPercentage();
                case 'estimated_time':
                    return $issue->canEditEstimatedTime();
                case 'posted_by':
                    return $issue->canEditPostedBy();
                case 'owned_by':
                    return $issue->canEditOwner();
                case 'assigned_to':
                    return $issue->canEditAssignee();
                case 'category':
                    return $issue->canEditCategory();
                case 'resolution':
                    return $issue->canEditResolution();
                case 'severity':
                    return $issue->canEditSeverity();
                case 'reproducability':
                    return $issue->canEditReproducability();
                case 'priority':
                    return $issue->canEditPriority();
                case 'milestone':
                    return $issue->canEditMilestone();
                case 'issuetype':
                    return $issue->canEditIssuetype();
                case 'status':
                    return $issue->canEditStatus();
                case 'pain_bug_type':
                case 'pain_likelihood':
                case 'pain_effect':
                    return $issue->canEditUserPain();
                default:
                    if ($customdatatype = entities\CustomDatatype::getByKey($request['field'])) {
                        $key = $customdatatype->getKey();

                        return $issue->canEditCustomFields($key);
                    } else {
                        return false;
                    }
            }
        }

        /**
         * Unlock the issue
         *
         * @param Request $request
         */
        public function runUnlockIssue(Request $request)
        {
            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
                }

                if (!$issue->canEditAccessPolicy()) {
                    $this->forward403($this->getI18n()->__("You don't have access to update the issue access policy"));

                    return;
                }

                framework\Event::listen('core', 'pachno\core\entities\Issue::save_pre_notifications', [$this, 'listen_issueSaveUnlock']);
                $issue->setLocked(false);
                $issue->setLockedCategory($request->hasParameter('public_category'));
                $issue->save();
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
            }

            return $this->renderJSON(['message' => $this->getI18n()->__('Issue access policy updated')]);
        }

        public function listen_issueSaveUnlock(framework\Event $event)
        {
            $this->_unlockIssueAfter(framework\Context::getRequest(), $event->getSubject());
        }

        /**
         * Unlock the issue
         *
         * @param Request $request
         */
        public function runLockIssue(Request $request)
        {
            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
                }

                if (!$issue->canEditAccessPolicy()) {
                    $this->forward403($this->getI18n()->__("You don't have access to update the issue access policy"));

                    return;
                }

                framework\Event::listen('core', 'pachno\core\entities\Issue::save_pre_notifications', [$this, 'listen_issueSaveLock']);
                $issue->setLocked();
                $issue->save();
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
            }

            return $this->renderJSON(['message' => $this->getI18n()->__('Issue access policy updated')]);
        }

        public function listen_issueSaveLock(framework\Event $event)
        {
            $this->_lockIssueAfter(framework\Context::getRequest(), $event->getSubject());
        }

        /**
         * Mark the issue as not blocking the next release
         *
         * @param Request $request
         */
        public function runMarkAsNotBlocker(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPermission('caneditissue') || $this->getUser()->hasPermission('caneditissuebasic'));

            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
            }

            $issue->setBlocking(false);
            $issue->save();

            return $this->renderJSON('not blocking');
        }

        /**
         * Mark the issue as blocking the next release
         *
         * @param Request $request
         */
        public function runMarkAsBlocker(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPermission('caneditissue') || $this->getUser()->hasPermission('caneditissuebasic'));

            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['message' => framework\Context::getI18n()->__('This issue does not exist')]);
            }

            $issue->setBlocking();
            $issue->save();

            return $this->renderJSON('blocking');
        }

        /**
         * Delete an issue
         *
         * @param Request $request
         */
        public function runDeleteIssue(Request $request)
        {
            $request_referer = ($request['referer'] ?: isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    if ($request_referer) {
                        return $this->forward($request_referer);
                    }

                    return $this->return404(framework\Context::getI18n()->__('This issue does not exist'));
                }
            } else {
                if ($request_referer) {
                    return $this->forward($request_referer);
                }

                return $this->return404(framework\Context::getI18n()->__('This issue does not exist'));
            }

            if ($issue->isDeleted()) {
                return $this->forward($request_referer);
            }

            $this->forward403unless($issue->canDeleteIssue());
            $issue->deleteIssue();
            $issue->save();

            framework\Context::setMessage('issue_deleted', true);
            $this->forward($this->getRouting()->generate('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]) . '?referer=' . $request_referer);
        }

        /**
         * Find users and show selection links
         *
         * @param Request $request The request object
         */
        public function runFindIdentifiable(Request $request)
        {
            $this->forward403unless($request->isPost());
            $users = [];

            if ($find_identifiable_by = $request['find_identifiable_by']) {
                if ($request['include_clients']) {
                    $clients = tables\Clients::getTable()->quickfind($find_identifiable_by);
                } else {
                    $users = tables\Users::getTable()->getByDetails($find_identifiable_by, 10);
                    if ($request['include_teams']) {
                        $teams = tables\Teams::getTable()->quickfind($find_identifiable_by);
                    } else {
                        $teams = [];
                    }
                }
            }
            $teamup_callback = $request['teamup_callback'];
            $team_callback = $request['team_callback'];
            $callback = $request['callback'];

            return $this->renderComponent('identifiableselectorresults', compact('users', 'teams', 'clients', 'callback', 'teamup_callback', 'team_callback'));
        }

        /**
         * Hides an infobox with a specific key
         *
         * @param Request $request The request object
         */
        public function runHideInfobox(Request $request)
        {
            Settings::hideInfoBox($request['key']);

            return $this->renderJSON(['hidden' => true]);
        }

        public function runSetToggle(Request $request)
        {
            Settings::setToggle($request['key'], $request['state']);

            return $this->renderJSON(['state' => $request['state']]);
        }

        public function runGetUploadStatus(Request $request)
        {
            $id = $request->getParameter('upload_id', 0);

            framework\Logging::log('requesting status for upload with id ' . $id);
            $status = framework\Context::getRequest()->getUploadStatus($id);
            framework\Logging::log('status was: ' . (int)$status['finished'] . ', pct: ' . (int)$status['percent']);
            if (array_key_exists('file_id', $status) && $request['mode'] == 'issue') {
                $file = tables\Files::getTable()->selectById($status['file_id']);
                $status['content_uploader'] = $this->getComponentHTML('main/attachedfile', ['base_id' => 'uploaded_files', 'mode' => 'issue', 'issue_id' => $request['issue_id'], 'file' => $file]);
                $status['content_inline'] = $this->getComponentHTML('main/attachedfile', ['base_id' => 'viewissue_files', 'mode' => 'issue', 'issue_id' => $request['issue_id'], 'file' => $file]);
                $issue = Issues::getTable()->selectById($request['issue_id']);
                $status['attachmentcount'] = count($issue->getFiles()) + count($issue->getLinks());
            } elseif (array_key_exists('file_id', $status) && $request['mode'] == 'article') {
                $file = tables\Files::getTable()->selectById($status['file_id']);
                $status['content_uploader'] = $this->getComponentHTML('main/attachedfile', ['base_id' => 'article_' . mb_strtolower($request['article_name']) . '_files', 'mode' => 'article', 'article_name' => $request['article_name'], 'file' => $file]);
                $status['content_inline'] = $this->getComponentHTML('main/attachedfile', ['base_id' => 'article_' . mb_strtolower($request['article_name']) . '_files', 'mode' => 'article', 'article_name' => $request['article_name'], 'file' => $file]);
                $article = entities\Article::getByName($request['article_name']);
                $status['attachmentcount'] = count($article->getFiles());
            }

            return $this->renderJSON($status);
        }

        public function runUpdateAttachments(Request $request)
        {
            switch ($request['target']) {
                case 'issue':
                    $target = Issues::getTable()->selectById($request['target_id']);
                    $base_id = 'viewissue_files';
                    $container_id = 'viewissue_uploaded_files';
                    $target_identifier = 'issue_id';
                    $target_id = $target->getID();
                    break;
                case 'article':
                    $target = tables\Articles::getTable()->selectById($request['target_id']);
                    $container_id = 'article_' . $target->getID() . '_files';
                    $base_id = $container_id;
                    $target_identifier = 'article_name';
                    $target_id = $request['article_name'];
                    break;
            }
            $saved_file_ids = $request['files'];
            $files = $image_files = [];
            $comments = '';
            foreach ($request['file_description'] ?: [] as $file_id => $description) {
                $file = tables\Files::getTable()->selectById($file_id);

                if (!$file instanceof entities\File) continue;

                $file->setDescription($description);
                $file->save();
                if (in_array($file_id, $saved_file_ids)) {
                    if ($target instanceof Issue) {
                        $comment = $target->attachFile($file, '', '', true);

                        if ($comment instanceof entities\Comment) $comments = $this->getComponentHTML('main/comment', ['comment' => $comment, 'issue' => $target, 'mentionable_target_type' => 'issue', 'comment_count_div' => 'viewissue_comment_count']) . $comments;
                    } else {
                        $target->attachFile($file);
                    }
                } else {
                    $target->detachFile($file);
                }
                if ($file->isImage()) {
                    $image_files[] = $this->getComponentHTML('main/attachedfile', ['base_id' => $base_id, 'mode' => $request['target'], $request['target'] => $target, $target_identifier => $target_id, 'file' => $file]);
                } else {
                    $files[] = $this->getComponentHTML('main/attachedfile', ['base_id' => $base_id, 'mode' => $request['target'], $request['target'] => $target, $target_identifier => $target_id, 'file' => $file]);
                }
            }
            $attachmentcount = ($request['target'] == 'issue') ? $target->countFiles() + $target->countLinks() : $target->countFiles();

            return $this->renderJSON(['attached' => 'ok', 'container_id' => $container_id, 'files' => array_reverse(array_merge($files, $image_files)), 'attachmentcount' => $attachmentcount, 'comments' => $comments]);
        }

        public function runUploadFile(Request $request)
        {
            if (!isset($_SESSION['upload_files'])) {
                $_SESSION['upload_files'] = [];
            }

            $files = [];
            $files_dir = Settings::getUploadsLocalpath();

            foreach ($request->getUploadedFiles() as $key => $file) {
                $file['name'] = str_replace(['[', ']'], ['(', ')'], $file['name']);
                $new_filename = framework\Context::getUser()->getID() . '_' . NOW . '_' . basename($file['name']);
                if (Settings::getUploadStorage() == 'files') {
                    $filename = $files_dir . $new_filename;
                } else {
                    $filename = $file['tmp_name'];
                }
                framework\Logging::log('Moving uploaded file to ' . $filename);
                if (Settings::getUploadStorage() == 'files' && !move_uploaded_file($file['tmp_name'], $filename)) {
                    framework\Logging::log('Moving uploaded file failed!');
                    throw new Exception(framework\Context::getI18n()->__('An error occured when saving the file'));
                } else {
                    framework\Logging::log('Upload complete and ok, storing upload status and returning filename ' . $new_filename);
                    $content_type = entities\File::getMimeType($filename);
                    if (Settings::getUploadStorage() == 'database') {
                        $file_object_id = tables\Files::getTable()->saveFile($new_filename, basename($file['name']), $content_type, null, file_get_contents($filename));
                    } else {
                        $file_object_id = tables\Files::getTable()->saveFile($new_filename, basename($file['name']), $content_type);
                    }

                    return $this->renderJSON(['file_id' => $file_object_id]);
                }
            }

            return $this->renderJSON(['error' => $this->getI18n()->__('An error occurred when uploading the file')]);
        }

        public function runUpload(Request $request)
        {
            $apc_exists = Request::CanGetUploadStatus();
            if ($apc_exists && !$request['APC_UPLOAD_PROGRESS']) {
                $request->setParameter('APC_UPLOAD_PROGRESS', $request['upload_id']);
            }
            $this->getResponse()->setDecoration(Response::DECORATE_NONE);

            $canupload = false;

            if ($request['mode'] == 'issue') {
                $issue = Issues::getTable()->selectById($request['issue_id']);
                $canupload = (bool)($issue instanceof Issue && $issue->hasAccess() && $issue->canAttachFiles());
            } elseif ($request['mode'] == 'article') {
                $article = entities\Article::getByName($request['article_name']);
                $canupload = (bool)($article instanceof entities\Article && $article->canEdit());
            } else {
                $event = framework\Event::createNew('core', 'upload', $request['mode']);
                $event->triggerUntilProcessed();

                $canupload = ($event->isProcessed()) ? (bool)$event->getReturnValue() : true;
            }

            if ($canupload) {
                try {
                    $file = framework\Context::getRequest()->handleUpload('uploader_file');
                    if ($file instanceof entities\File) {
                        switch ($request['mode']) {
                            case 'issue':
                                if (!$issue instanceof Issue)
                                    break;
                                $issue->attachFile($file, $request->getRawParameter('comment'), $request['uploader_file_description']);
                                $issue->save();
                                break;
                            case 'article':
                                if (!$article instanceof entities\Article)
                                    break;

                                $article->attachFile($file);
                                break;
                        }
                        if ($apc_exists)
                            return $this->renderText('ok');
                    }
                    $this->error = framework\Context::getI18n()->__('An unhandled error occured with the upload');
                } catch (Exception $e) {
                    $this->getResponse()->setHttpStatus(400);
                    $this->error = $e->getMessage();
                }
            } else {
                $this->error = framework\Context::getI18n()->__('You are not allowed to attach files here');
            }
            if (!$apc_exists) {
                switch ($request['mode']) {
                    case 'issue':
                        if (!$issue instanceof Issue)
                            break;

                        $this->forward($this->getRouting()->generate('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]));
                        break;
                    case 'article':
                        if (!$article instanceof entities\Article)
                            break;

                        $this->forward($this->getRouting()->generate('publish_article_attachments', ['article_name' => $article->getName()]));
                        break;
                }
            }
            framework\Logging::log('marking upload ' . $request['APC_UPLOAD_PROGRESS'] . ' as completed with error ' . $this->error);
            $request->markUploadAsFinishedWithError($request['APC_UPLOAD_PROGRESS'], $this->error);

            return $this->renderText($request['APC_UPLOAD_PROGRESS'] . ': ' . $this->error);
        }

        public function runDetachFile(Request $request)
        {
            try {
                $file = tables\Files::getTable()->selectById((int)$request['file_id']);
                switch ($request['mode']) {
                    case 'issue':
                        $issue = Issues::getTable()->selectById($request['issue_id']);
                        if ($issue instanceof Issue && $issue->canRemoveAttachments() && (int)$request->getParameter('file_id', 0)) {
                            $issue->detachFile($file);

                            return $this->renderJSON(['file_id' => $request['file_id'], 'attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'message' => framework\Context::getI18n()->__('The attachment has been removed')]);
                        }
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('You can not remove items from this issue')]);
                    case 'article':
                        $article = entities\Article::getByName($request['article_name']);
                        if ($article instanceof entities\Article && $article->canEdit() && (int)$request->getParameter('file_id', 0)) {
                            $article->detachFile($file);

                            return $this->renderJSON(['file_id' => $request['file_id'], 'attachmentcount' => count($article->getFiles()), 'message' => framework\Context::getI18n()->__('The attachment has been removed')]);
                        }
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('You can not remove items from this issue')]);
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('An error occurred when removing the file')]);
            }
            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Invalid mode')]);
        }

        public function runGetFile(Request $request)
        {
            $file = new entities\File((int)$request['id']);
            if ($file instanceof entities\File) {
                if ($file->hasAccess()) {
                    $disableCache = true;
                    $isFile = false;
                    $this->getResponse()->cleanBuffer();
                    $this->getResponse()->clearHeaders();
                    $this->getResponse()->setDecoration(Response::DECORATE_NONE);

                    if ($file->isImage() && Settings::isUploadsImageCachingEnabled()) {
                        $this->getResponse()->addHeader('Pragma: public');
                        $this->getResponse()->addHeader('Cache-Control: public, max-age: 15768000');
                        $this->getResponse()->addHeader("Expires: " . gmdate('D, d M Y H:i:s', time() + 15768000) . " GMT");
                        $disableCache = false;
                    }

                    $this->getResponse()->addHeader('Content-disposition: ' . (($request['mode'] == 'download') ? 'attachment' : 'inline') . '; filename="' . $file->getOriginalFilename() . '"');
                    $this->getResponse()->setContentType($file->getContentType());
                    if (Settings::getUploadStorage() == 'files') {
                        $fh = fopen(Settings::getUploadsLocalpath() . $file->getRealFilename(), 'r');
                        $isFile = true;
                    } else {
                        $fh = $file->getContent();
                    }
                    if (is_resource($fh)) {
                        if ($isFile && Settings::isUploadsDeliveryUseXsend()) {
                            $this->getResponse()->addHeader('X-Sendfile: ' . Settings::getUploadsLocalpath() . $file->getRealFilename());
                            $this->getResponse()->addHeader('X-Accel-Redirect: /private/' . $file->getRealFilename());

                            $this->getResponse()->renderHeaders($disableCache);
                        } else {
                            $this->getResponse()->renderHeaders($disableCache);
                            fpassthru($fh);
                        }
                    } else {
                        $this->getResponse()->renderHeaders($disableCache);
                        echo $fh;
                    }
                    exit();
                }
            }
            $this->return404(framework\Context::getI18n()->__('This file does not exist'));
        }

        public function runAttachLinkToIssue(Request $request)
        {
            $issue = Issues::getTable()->selectById($request['issue_id']);
            if ($issue instanceof Issue && $issue->canAttachLinks()) {
                if ($request['link_url'] != '') {
                    $link_id = $issue->attachLink($request['link_url'], $request['description']);

                    return $this->renderJSON(['message' => framework\Context::getI18n()->__('Link attached!'), 'attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'content' => $this->getComponentHTML('main/attachedlink', ['issue' => $issue, 'link_id' => $link_id, 'link' => ['description' => $request['description'], 'url' => $request['link_url']]])]);
                }
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('You have to provide a link URL, otherwise we have nowhere to link to!')]);
            }
            $this->getResponse()->setHttpStatus(400);

            return $this->renderJSON(['error' => framework\Context::getI18n()->__('You can not attach links to this issue')]);
        }

        public function runRemoveLinkFromIssue(Request $request)
        {
            $issue = Issues::getTable()->selectById($request['issue_id']);
            if ($issue instanceof Issue && $issue->canRemoveAttachments()) {
                if ($request['link_id'] != 0) {
                    $issue->removeLink($request['link_id']);

                    return $this->renderJSON(['attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'message' => framework\Context::getI18n()->__('Link removed!')]);
                }
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('You have to provide a valid link id')]);
            }

            return $this->renderJSON(['error' => framework\Context::getI18n()->__('You can not remove items from this issue')]);
        }

        public function runAttachLink(Request $request)
        {
            $link = tables\Links::getTable()->addLink($request['target_type'], $request['target_id'], $request['link_url'], $request->getRawParameter('description'));

            return $this->renderJSON(['message' => framework\Context::getI18n()->__('Link added!'), 'content' => $this->getComponentHTML('main/menulink', ['link_id' => $link->getID(), 'link' => ['target_type' => $request['target_type'], 'target_id' => $request['target_id'], 'description' => $request->getRawParameter('description'), 'url' => $request['link_url']]])]);
        }

        public function runRemoveLink(Request $request)
        {
            if (!$this->getUser()->canEditMainMenu($request['target_type'])) {
                $this->getResponse()->setHttpStatus(403);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('You do not have access to removing links')]);
            }

            if (!$request['link_id']) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('You have to provide a valid link id')]);
            }

            tables\Links::getTable()->removeByTargetTypeTargetIDandLinkID($request['target_type'], $request['target_id'], $request['link_id']);

            return $this->renderJSON(['message' => framework\Context::getI18n()->__('Link removed!')]);
        }

        public function runSaveMenuOrder(Request $request)
        {
            $target_type = $request['target_type'];
            $target_id = $request['target_id'];
            tables\Links::getTable()->saveLinkOrder($request[$target_type . '_' . $target_id . '_links']);

            return $this->renderJSON('ok');
        }

        public function runDeleteComment(Request $request)
        {
            $comment = tables\Comments::getTable()->selectById($request['comment_id']);
            if ($comment instanceof entities\Comment) {
                if (!$comment->canUserDelete(framework\Context::getUser())) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('You are not allowed to do this')]);
                } else {
                    unset($comment);
                    $comment = tables\Comments::getTable()->selectById((int)$request['comment_id']);
                    $comment->delete();

                    return $this->renderJSON(['title' => framework\Context::getI18n()->__('Comment deleted!')]);
                }
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('Comment ID is invalid')]);
            }
        }

        public function listenIssueSaveAddComment(framework\Event $event)
        {
            $this->comment_lines = $event->getParameter('comment_lines');
            $this->comment = $event->getParameter('comment');
        }

        public function runEditComment(Request $request)
        {
            $i18n = framework\Context::getI18n();
            $comment_applies_type = $request['comment_applies_type'];
            $is_new = !$request->hasParameter('comment_id');

            try {
                if (!$is_new) {
                    $comment = tables\Comments::getTable()->selectById($request['comment_id']);
                } else {
                    if (!$this->getUser()->canPostComments()) {
                        throw new Exception($i18n->__('You are not allowed to do this'));
                    }

                    $comment = new entities\Comment();
                    $comment->setPostedBy($this->getUser()->getID());
                    $comment->setTargetID($request['comment_applies_id']);
                    $comment->setTargetType($request['comment_applies_type']);
                    $comment->setReplyToComment($request['reply_to_comment_id']);
                    $comment->setModuleName($request['comment_module']);
                }

                if (!trim($request['comment_body'])) {
                    throw new Exception($i18n->__('You cannot post an empty comment'));
                }

                $comment->setContent($request->getParameter('comment_body', null, false));
                $comment->setIsPublic((bool)$request['comment_visibility']);
                $comment->setSyntax($request['comment_body_syntax']);
                $comment->save();

                if ($comment_applies_type == entities\Comment::TYPE_ISSUE) {
                    $issue = Issues::getTable()->selectById((int)$request['comment_applies_id']);
                    framework\Event::createNew('core', 'pachno\core\entities\Comment::createNew', $comment, compact('issue'))->trigger();
                } elseif ($comment_applies_type == entities\Comment::TYPE_ARTICLE) {
                    $article = tables\Articles::getTable()->selectById((int)$request['comment_applies_id']);
                    framework\Event::createNew('core', 'pachno\core\entities\Comment::createNew', $comment, compact('article'))->trigger();
                }

                $component_name = ($comment->isReply()) ? 'main/comment' : 'main/commentwrapper';
                switch ($comment_applies_type) {
                    case entities\Comment::TYPE_ISSUE:
                        $issue = Issues::getTable()->selectById($request['comment_applies_id']);

                        framework\Context::setCurrentProject($issue->getProject());
                        if ($is_new) {
                            $comment_html = $this->getComponentHTML($component_name, ['comment' => $comment, 'issue' => $issue, 'options' => ['issue' => $issue], 'mentionable_target_type' => 'issue', 'comment_count_div' => 'viewissue_comment_count']);
                        } else {
                            $comment_html = $comment->getParsedContent();
                        }
                        break;
                    case entities\Comment::TYPE_ARTICLE:
                        if ($is_new) {
                            $comment_html = $this->getComponentHTML($component_name, ['comment' => $comment, 'mentionable_target_type' => 'article', 'options' => [], 'comment_count_div' => 'article_comment_count']);
                        } else {
                            $comment_html = $comment->getParsedContent();
                        }
                        break;
                    default:
                        $comment_html = 'OH NO!';
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }

            return $this->renderJSON(['title' => $i18n->__('Comment added!'), 'comment_data' => $comment_html, 'comment_id' => $comment->getID(), 'commentcount' => entities\Comment::countComments($request['comment_applies_id'], $request['comment_applies_type']/* , $request['comment_module'] */)]);
        }

        public function runListProjects(Request $request)
        {
            $projects = Project::getAll();

            $return_array = [];
            foreach ($projects as $project) {
                $return_array[$project->getKey()] = $project->getName();
            }

            $this->projects = $return_array;
        }

        public function runListIssuetypes(Request $request)
        {
            $issuetypes = entities\Issuetype::getAll();

            $return_array = [];
            foreach ($issuetypes as $issuetype) {
                $return_array[$issuetype->getKey()] = $issuetype->getName();
            }

            $this->issuetypes = $return_array;
        }

        public function runListFieldvalues(Request $request)
        {
            $field_key = $request['field_key'];
            $return_array = ['description' => null, 'type' => null, 'choices' => null];
            if ($field_key == 'title' || in_array($field_key, entities\DatatypeBase::getAvailableFields(true))) {
                switch ($field_key) {
                    case 'title':
                    case 'shortname':
                        $return_array['description'] = framework\Context::getI18n()->__('Single line text input without formatting');
                        $return_array['type'] = 'single_line_input';
                        break;
                    case 'description':
                    case 'reproduction_steps':
                        $return_array['description'] = framework\Context::getI18n()->__('Text input with wiki formatting capabilities');
                        $return_array['type'] = 'wiki_input';
                        break;
                    case 'status':
                    case 'resolution':
                    case 'reproducability':
                    case 'priority':
                    case 'severity':
                    case 'category':
                        $return_array['description'] = framework\Context::getI18n()->__('Choose one of the available values');
                        $return_array['type'] = 'choice';

                        $classname = "\\pachno\\core\\entities\\" . ucfirst($field_key);
                        $choices = $classname::getAll();
                        foreach ($choices as $choice_key => $choice) {
                            $return_array['choices'][$choice_key] = $choice->getName();
                        }
                        break;
                    case 'percent_complete':
                        $return_array['description'] = framework\Context::getI18n()->__('Value of percentage completed');
                        $return_array['type'] = 'choice';
                        $return_array['choices'][] = "1-100%";
                        break;
                    case 'owner':
                    case 'assignee':
                        $return_array['description'] = framework\Context::getI18n()->__('Select an existing user or <none>');
                        $return_array['type'] = 'select_user';
                        break;
                    case 'estimated_time':
                    case 'spent_time':
                        $return_array['description'] = framework\Context::getI18n()->__('Enter time, such as points, hours, minutes, etc or <none>');
                        $return_array['type'] = 'time';
                        break;
                    case 'milestone':
                        $return_array['description'] = framework\Context::getI18n()->__('Select from available project milestones');
                        $return_array['type'] = 'choice';
                        if ($this->selected_project instanceof Project) {
                            $milestones = $this->selected_project->getAvailableMilestones();
                            foreach ($milestones as $milestone) {
                                $return_array['choices'][$milestone->getID()] = $milestone->getName();
                            }
                        }
                        break;
                }
            } else {

            }

            $this->field_info = $return_array;
        }

        /**
         * Partial backdrop loader
         *
         * @Route(name="get_partial_for_backdrop", url="/get/partials/:key/*")
         * @AnonymousRoute
         *
         * @param Request $request
         *
         * @return bool
         */
        public function runGetBackdropPartial(Request $request)
        {
            if (!$request->isAjaxCall()) {
                return $this->return404($this->getI18n()->__('You need to enable javascript for Pachno to work properly'));
            }
            try {
                $template_name = null;
                if ($request->hasParameter('issue_id')) {
                    $issue = Issues::getTable()->selectById($request['issue_id']);
                    $options = ['issue' => $issue];
                } else {
                    $options = [];
                }
                switch ($request['key']) {
                    case 'usercard':
                        $template_name = 'main/usercard';
                        if ($user_id = $request['user_id']) {
                            $user = tables\Users::getTable()->selectById($user_id);
                            $options['user'] = $user;
                        }
                        break;
                    case 'login':
                        $template_name = 'main/loginpopup';
                        $options = $request->getParameters();
                        $options['content'] = $this->getComponentHTML('login', ['section' => $request->getParameter('section', 'login')]);
                        $options['mandatory'] = false;
                        break;
                    case 'uploader':
                        $template_name = 'main/uploader';
                        $options = $request->getParameters();
                        $options['uploader'] = ($request['uploader'] == 'dynamic') ? 'dynamic' : 'standard';
                        break;
                    case 'attachlink':
                        $template_name = 'main/attachlink';
                        break;
                    case 'notifications':
                        $template_name = 'main/notifications';
                        $options['first_notification_id'] = $request['first_notification_id'];
                        $options['last_notification_id'] = $request['last_notification_id'];
                        break;
                    case 'workflow_transition':
                        $transition = tables\WorkflowTransitions::getTable()->selectById($request['transition_id']);
                        $template_name = $transition->getTemplate();
                        $options['transition'] = $transition;
                        if ($request->hasParameter('issue_ids')) {
                            $options['issues'] = [];
                            foreach ($request['issue_ids'] as $issue_id) {
                                $options['issues'][$issue_id] = new Issue($issue_id);
                            }
                        } else {
                            $options['issue'] = new Issue($request['issue_id']);
                        }
                        $options['show'] = true;
                        $options['interactive'] = true;
                        $options['project'] = $this->selected_project;
                        break;
                    case 'reportissue':
                        $this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);
                        if ($this->selected_project instanceof Project && !$this->selected_project->isLocked() && $this->getUser()->canReportIssues($this->selected_project)) {
                            $template_name = 'main/reportissuecontainer';
                            $options['selected_project'] = $this->selected_project;
                            $options['selected_issuetype'] = $this->selected_issuetype;
                            $options['locked_issuetype'] = $this->locked_issuetype;
                            $options['selected_milestone'] = $this->_getMilestoneFromRequest($request);
                            $options['parent_issue'] = $this->_getParentIssueFromRequest($request);
                            $options['board'] = $this->_getBoardFromRequest($request);
                            $options['selected_build'] = $this->_getBuildFromRequest($request);
                            $options['issuetypes'] = $this->issuetypes;
                            $options['errors'] = [];
                        } else {
                            throw new Exception($this->getI18n()->__('You are not allowed to do this'));
                        }
                        break;
                    case 'move_issue':
                        $template_name = 'main/moveissue';
                        $options['multi'] = (bool)$request->getParameter('multi', false);
                        break;
                    case 'issue_permissions':
                        $template_name = 'main/issuepermissions';
                        break;
                    case 'issue_subscribers':
                        $template_name = 'main/issuesubscribers';
                        break;
                    case 'issue_spenttimes':
                        $template_name = 'main/issuespenttimes';
                        $options['initial_view'] = $request->getParameter('initial_view', 'list');
                        break;
                    case 'issue_spenttime':
                        $template_name = 'main/issuespenttime';
                        $options['entry_id'] = $request->getParameter('entry_id');
                        break;
                    case 'relate_issue':
                        $template_name = 'main/relateissue';
                        break;
                    case 'project_build':
                        $template_name = 'project/build';
                        $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        if ($request->hasParameter('build_id'))
                            $options['build'] = tables\Builds::getTable()->selectById($request['build_id']);
                        break;
                    case 'project_icons':
                        $template_name = 'project/projecticons';
                        $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        break;
                    case 'project_workflow':
                        $template_name = 'project/projectworkflow';
                        $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        break;
                    case 'project_add_people':
                        $template_name = 'project/projectaddpeople';
                        $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        break;
                    case 'permissions':
                        $options['key'] = $request['permission_key'];
                        $target_module = ($request['target_module'] !== 'core') ? $request['target_module'] : null;
                        if ($details = framework\Context::getPermissionDetails($options['key'], null, $target_module)) {
                            $template_name = 'configuration/permissionspopup';
                            $options['mode'] = $request['mode'];
                            $options['module'] = $request['target_module'];
                            $options['target_id'] = $request['target_id'];
                            $options['item_name'] = $details['description'];
                            $options['access_level'] = $request['access_level'];
                        }
                        break;
                    case 'issuefield_permissions':
                        $options['item_key'] = $request['item_key'];
                        if ($details = framework\Context::getPermissionDetails($options['item_key'])) {
                            $template_name = 'configuration/issuefieldpermissions';
                            $options['item_name'] = $details['description'];
                            $options['item_id'] = $request['item_id'];
                            $options['access_level'] = $request['access_level'];
                        }
                        break;
                    case 'site_icons':
                        $template_name = 'configuration/siteicons';
                        break;
                    case 'edit_role':
                        $template_name = 'configuration/editrole';
                        if ($request['role_id']) {
                            $role = tables\ListTypes::getTable()->selectById($request['role_id']);
                        } else {
                            $role = new entities\Role();
                        }
                        $options['role'] = $role;
                        break;
                    case 'edit_workflow_scheme':
                        $template_name = 'configuration/editworkflowscheme';
                        if ($request['scheme_id']) {
                            $scheme = tables\WorkflowSchemes::getTable()->selectById($request['scheme_id']);
                        } else {
                            $scheme = new entities\WorkflowScheme();
                        }
                        $options['clone'] = $request->hasParameter('clone');
                        $options['scheme'] = $scheme;
                        break;
                    case 'edit_workflow_transition':
                        $template_name = 'configuration/editworkflowtransitionpopup';
                        if ($request['transition_id']) {
                            $scheme = tables\WorkflowTransitions::getTable()->selectById($request['transition_id']);
                        } else {
                            $scheme = new entities\WorkflowTransition();
                        }
                        $options['step'] = tables\WorkflowSteps::getTable()->selectById($request['step_id']);
                        $options['transition'] = $scheme;
                        break;
                    case 'edit_issuetype':
                        $template_name = 'configuration/editissuetype';
                        if ($request['issuetype_id']) {
                            $issuetype = tables\IssueTypes::getTable()->selectById($request['issuetype_id']);
                        } else {
                            $issuetype = new entities\Issuetype();
                        }
                        if ($request['scheme_id']) {
                            $scheme = tables\IssuetypeSchemes::getTable()->selectById($request['scheme_id']);
                            $options['scheme'] = $scheme;
                        }
                        $options['type'] = $issuetype;
                        break;
                    case 'edit_issuefield':
                        $template_name = 'configuration/editissuefieldpopup';
                        if ($request['issue_type_id']) {
                            $issue_type = tables\IssueTypes::getTable()->selectById($request['issue_type_id']);
                            $options['issue_type'] = $issue_type;
                        }
                        if ($request['scheme_id']) {
                            $scheme = tables\IssuetypeSchemes::getTable()->selectById($request['scheme_id']);
                            $options['scheme'] = $scheme;
                        }
                        if ($request['type']) {
                            $type = $request['type'];
                            if (in_array($type, entities\DatatypeBase::getAvailableFields(true))) {
                                $item = $type;
                            } else {
                                $item = entities\CustomDatatype::getByKey($type);
                            }
                            $options['item'] = $item;
                        } else {
                            $options['item'] = new entities\CustomDatatype();
                        }
                        break;
                    case 'scope_config':
                        $template_name = 'configuration/editscope';
                        if ($request['scope_id']) {
                            $scope = tables\Scopes::getTable()->selectById($request['scope_id']);
                        } else {
                            $scope = new entities\Scope();
                        }
                        $options['scope'] = $scope;
                        break;
                    case 'enable_2fa':
                        $template_name = 'main/enable2fa';
                        break;
                    case 'project_config':
                        $template_name = 'project/editproject';
                        if ($request['project_id']) {
                            $project = Projects::getTable()->selectById($request['project_id']);
                        } else {
                            $project = new Project();
                            framework\Context::setCurrentProject($project);
                        }
                        $options['assignee_type'] = $request['assignee_type'];
                        $options['assignee_id'] = $request['assignee_id'];
                        $options['project'] = $project;
                        $options['section'] = $request->getParameter('section', 'info');
                        if ($request->hasParameter('edition_id')) {
                            $edition = tables\Editions::getTable()->selectById($request['edition_id']);
                            $options['edition'] = $edition;
                            $options['selected_section'] = $request->getParameter('section', 'general');
                        }
                        break;
                    case 'issue_add_item':
                        $issue = Issues::getTable()->selectById($request['issue_id']);
                        $template_name = 'main/issueadditem';
                        break;
                    case 'client_users':
                        $options['client'] = tables\Clients::getTable()->selectById($request['client_id']);
                        $template_name = 'main/clientusers';
                        break;
                    case 'dashboard_config':
                        $template_name = 'main/dashboardconfig';
                        $options['tid'] = $request['tid'];
                        $options['target_type'] = $request['target_type'];
                        $options['previous_route'] = $request['previous_route'];
                        $options['mandatory'] = true;
                        break;
                    case 'bulk_workflow':
                        $template_name = 'search/bulkworkflow';
                        $options['issue_ids'] = $request['issue_ids'];
                        break;
                    case 'confirm_username':
                        $template_name = 'main/confirmusername';
                        $options['username'] = $request['username'];
                        break;
                    case 'add_dashboard_view':
                        $template_name = 'main/adddashboardview';
                        break;
                    case 'userscopes':
                        if (!framework\Context::getScope()->isDefault())
                            throw new Exception($this->getI18n()->__('This is not allowed outside the default scope'));

                        $template_name = 'configuration/userscopes';
                        $options['user'] = new entities\User((int)$request['user_id']);
                        break;
                    case 'milestone':
                        $template_name = 'project/milestone';
                        $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        if ($request->hasParameter('milestone_id'))
                            $options['milestone'] = Milestones::getTable()->selectById($request['milestone_id']);
                        break;
                    default:
                        $event = new framework\Event('core', 'get_backdrop_partial', $request['key']);
                        $event->triggerUntilProcessed();
                        $options = $event->getReturnList();
                        $template_name = $event->getReturnValue();
                }
                if ($template_name !== null) {
                    return $this->renderJSON(['content' => $this->getComponentHTML($template_name, $options)]);
                }
            } catch (Exception $e) {
                $this->getResponse()->cleanBuffer();
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('An error occured: %error_message', ['%error_message' => $e->getMessage()])]);
            }
            $this->getResponse()->cleanBuffer();
            $this->getResponse()->setHttpStatus(400);
            $error = (framework\Context::isDebugMode()) ? framework\Context::getI18n()->__('Invalid template or parameter') : $this->getI18n()->__('Could not show the requested popup');

            return $this->renderJSON(['error' => $error]);
        }

        protected function _getBoardFromRequest($request)
        {
            if ($request->hasParameter('board_id')) {
                try {
                    $board = tables\AgileBoards::getTable()->selectById((int)$request['board_id']);

                    return $board;
                } catch (Exception $e) {
                }
            }
        }

        /**
         * Find issues that might be related to selected issue.
         *
         * @param Request $request
         */
        public function runFindRelatedIssues(Request $request)
        {
            $issue_id = $request['issue_id'];
            $searchfor = trim($request['searchfor']);

            // Verify request parameters.
            if ($issue_id === null) {
                return $this->return400(framework\Context::getI18n()->__('Please provide an issue number'));
            } elseif ($searchfor === null or $searchfor === "") {
                return $this->return400(framework\Context::getI18n()->__('Please provide search text'));
            }
            // Valid project has been referenced, user has permissions
            // to access it (preExecute performs access check already).
            elseif ($this->selected_project instanceof Project) {
                $issue = Issues::getTable()->selectById($issue_id);

                if ($issue instanceof Issue && $issue->hasAccess($this->getUser())) {
                    // Try to get both exact match and text-based search.
                    $exact_match = Issue::getIssue($searchfor);
                    $found_issues = Issue::findIssuesByText($searchfor, null);

                    // Exclude selected issue from search results.
                    if ($exact_match == $issue) {
                        $exact_match = null;
                    }

                    if (($key = array_search($issue, $found_issues)) !== false) {
                        unset($found_issues[$key]);
                    }

                    // Exclude exact match from general search results.
                    if (($key = array_search($exact_match, $found_issues)) !== false) {
                        unset($found_issues[$key]);
                    }

                    // Sort issues by project. Selected project at top.
                    usort($found_issues, [$this, 'compareIssuesByProject']);

                    // Group issues, exact match first, followed by
                    // current project, then other projects.
                    $grouped_issues = [];

                    foreach ($found_issues as $found_issue) {
                        $project_name = $found_issue->getProject()->getName();

                        if (!array_key_exists($project_name, $found_issues)) {
                            $grouped_issues[$project_name] = [];
                        }

                        $grouped_issues[$project_name][] = $found_issue;
                    }

                    if ($exact_match instanceof Issue) {
                        $grouped_issues = [$this->getI18n()->__('Exact match') => [$exact_match]] + $grouped_issues;
                    }
                } else {
                    return $this->return404(framework\Context::getI18n()->__('Could not find this issue'));
                }
            } else {
                return $this->return404(framework\Context::getI18n()->__('This project does not exist'));
            }

            // Prepare response and return it.
            $this->getResponse()->setHttpStatus(Response::HTTP_STATUS_OK);

            $parameters = [
                'selected_project' => $this->selected_project,
                'issue' => $issue,
                'grouped_issues' => $grouped_issues
            ];

            return $this->renderJSON(['content' => $this->getComponentHTML('main/findrelatedissues', $parameters)]);
        }

        /**
         * Find issues that might be duplicates of selected issue.
         *
         * @param Request $request
         */
        public function runFindDuplicatedIssue(Request $request)
        {
            $issue_id = $request['issue_id'];
            $searchfor = trim($request['searchfor']);

            // Verify request parameters.
            if ($issue_id === null) {
                return $this->return400(framework\Context::getI18n()->__('Please provide an issue number'));
            } elseif ($searchfor === null or $searchfor === "") {
                return $this->return400(framework\Context::getI18n()->__('Please provide search text'));
            }
            // Valid project has been referenced, user has permissions
            // to access it (preExecute performs access check already).
            elseif ($this->selected_project instanceof Project) {
                $issue = Issues::getTable()->selectById($issue_id);

                if ($issue instanceof Issue && $issue->hasAccess($this->getUser())) {
                    // Try to get both exact match and text-based search.
                    $exact_match = Issue::getIssue($searchfor);
                    $matched_issues = Issue::findIssuesByText($searchfor, $this->selected_project);

                    // Exclude selected issue from search results.
                    if ($exact_match == $issue) {
                        $exact_match = null;
                    }

                    if (($key = array_search($issue, $matched_issues)) !== false) {
                        unset($matched_issues[$key]);
                    }

                    // Exclude exact match from general search results.
                    if (($key = array_search($exact_match, $matched_issues)) !== false) {
                        unset($matched_issues[$key]);
                    }

                    // Add exact match to top of the list.
                    if ($exact_match instanceof Issue) {
                        array_unshift($matched_issues, $exact_match);
                    }
                } else {
                    return $this->return404(framework\Context::getI18n()->__('Could not find this issue'));
                }
            } else {
                return $this->return404(framework\Context::getI18n()->__('This project does not exist'));
            }

            // Prepare response and return it.
            $this->getResponse()->setHttpStatus(Response::HTTP_STATUS_OK);

            $parameters = [
                'selected_project' => $this->selected_project,
                'issue' => $issue,
                'matched_issues' => $matched_issues
            ];

            return $this->renderJSON(['content' => $this->getComponentHTML('main/findduplicateissues', $parameters)]);
        }

        public function runRemoveRelatedIssue(Request $request)
        {
            try {
                try {
                    $issue_id = (int)$request['issue_id'];
                    $related_issue_id = (int)$request['related_issue_id'];
                    $issue = null;
                    $related_issue = null;
                    if ($issue_id && $related_issue_id) {
                        $issue = Issues::getTable()->selectById($issue_id);
                        $related_issue = Issues::getTable()->selectById($related_issue_id);
                    }
                    if (!$issue instanceof Issue || !$related_issue instanceof Issue) {
                        throw new Exception('');
                    }
                    $issue->removeDependantIssue($related_issue->getID());
                } catch (Exception $e) {
                    throw new Exception($this->getI18n()->__('Please provide a valid issue number and a valid related issue number'));
                }

                return $this->renderJSON(['message' => $this->getI18n()->__('The issues are no longer related')]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        public function runRemoveDuplicatedIssue(Request $request)
        {
            try {
                try {
                    $issue_id = (int)$request['issue_id'];
                    $duplicated_issue_id = (int)$request['duplicated_issue_id'];
                    $issue = null;
                    $duplicated_issue = null;
                    if ($issue_id && $duplicated_issue_id) {
                        $issue = Issues::getTable()->selectById($issue_id);
                        $duplicated_issue = Issues::getTable()->selectById($duplicated_issue_id);
                    }
                    if (!$issue instanceof Issue || !$duplicated_issue instanceof Issue || !$duplicated_issue->isDuplicate() || $duplicated_issue->getDuplicateOf()->getID() != $issue_id) {
                        throw new Exception('');
                    }
                    $duplicated_issue->clearDuplicate();
                } catch (Exception $e) {
                    throw new Exception($this->getI18n()->__('Please provide a valid issue number and a valid duplicated issue number'));
                }

                return $this->renderJSON(['message' => $this->getI18n()->__('The issues are no longer duplications')]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        public function runRelateIssues(Request $request)
        {
            $status = 200;
            $message = null;

            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    $status = 400;
                    $message = framework\Context::getI18n()->__('Could not find this issue');
                }
            } else {
                $status = 400;
                $message = framework\Context::getI18n()->__('Please provide an issue number');
            }

            if ($issue instanceof Issue && !$issue->canAddRelatedIssues()) {
                $status = 400;
                $message = framework\Context::getI18n()->__('You are not allowed to relate issues');
            }

            $this->getResponse()->setHttpStatus($status);
            if ($status == 400) {
                return $this->renderJSON(['error' => $message]);
            }

            $related_issues = $request->getParameter('relate_issues', []);

            $cc = 0;
            $message = framework\Context::getI18n()->__('Unknown error');
            $content = '';
            if (count($related_issues)) {
                $mode = $request['relate_action'];
                foreach ($related_issues as $issue_id) {
                    try {
                        $related_issue = Issues::getTable()->selectById((int)$issue_id);
                        if ($mode == 'relate_children') {
                            $issue->addChildIssue($related_issue);
                        } else {
                            $issue->addParentIssue($related_issue);
                        }
                        $cc++;
                        $content .= $this->getComponentHTML('main/relatedissue', ['issue' => $related_issue, 'related_issue' => $issue]);
                    } catch (Exception $e) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('An error occured when relating issues: %error', ['%error' => $e->getMessage()])]);
                    }
                }
            } else {
                $message = framework\Context::getI18n()->__('Please select at least one issue');
            }

            if ($cc > 0) {
                return $this->renderJSON(['content' => $content, 'message' => framework\Context::getI18n()->__('The related issue was added'), 'count' => count($issue->getChildIssues())]);
            } else {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('An error occured when relating issues: %error', ['%error' => $message])]);
            }
        }

        public function runRelatedIssues(Request $request)
        {
            if ($issue_id = $request['issue_id']) {
                try {
                    $this->issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {

                }
            }
        }

        public function runVoteForIssue(Request $request)
        {
            $i18n = framework\Context::getI18n();
            $issue = Issues::getTable()->selectById($request['issue_id']);
            $vote_direction = $request['vote'];
            if ($issue instanceof Issue && !$issue->hasUserVoted($this->getUser()->getID(), ($vote_direction == 'up'))) {
                $issue->vote(($vote_direction == 'up'));

                return $this->renderJSON(['content' => $issue->getVotes(), 'message' => $i18n->__('Vote added')]);
            }
        }

        public function runToggleFriend(Request $request)
        {
            try {
                $friend_user = tables\Users::getTable()->selectById($request['user_id']);
                $mode = $request['mode'];
                if ($mode == 'add') {
                    if ($friend_user instanceof entities\User && $friend_user->isDeleted()) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('This user has been deleted')]);
                    }
                    $this->getUser()->addFriend($friend_user);
                } else {
                    $this->getUser()->removeFriend($friend_user);
                }

                return $this->renderJSON(['mode' => $mode]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('Could not add or remove friend')]);
            }
        }

        public function runSetState(Request $request)
        {
            try {
                $state = tables\Userstates::getTable()->selectById($request['state_id']);
                $this->getUser()->setState($state);
                $this->getUser()->save();

                return $this->renderJSON(['userstate' => $this->getI18n()->__($state->getName())]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('An error occured while trying to update your status')]);
            }
        }

        public function runToggleAffectedConfirmed(Request $request)
        {
            try {
                $issue = Issues::getTable()->selectById($request['issue_id']);
                $itemtype = $request['affected_type'];

                if (!(($itemtype == 'build' && $issue->canEditAffectedBuilds()) || ($itemtype == 'component' && $issue->canEditAffectedComponents()) || ($itemtype == 'edition' && $issue->canEditAffectedEditions()))) {
                    throw new Exception($this->getI18n()->__('You are not allowed to do this'));
                }

                $affected_id = $request['affected_id'];
                $confirmed = true;

                switch ($itemtype) {
                    case 'edition':
                        if (!$issue->getProject()->isEditionsEnabled()) {
                            throw new Exception($this->getI18n()->__('Editions are disabled'));
                        }

                        $editions = $issue->getEditions();
                        if (!array_key_exists($affected_id, $editions)) {
                            throw new Exception($this->getI18n()->__('This edition is not affected by this issue'));
                        }
                        $edition = $editions[$affected_id];

                        if ($edition['confirmed'] == true) {
                            $issue->confirmAffectedEdition($edition['edition'], false);
                            $confirmed = false;
                        } else {
                            $issue->confirmAffectedEdition($edition['edition']);
                            $confirmed = true;
                        }

                        break;
                    case 'component':
                        if (!$issue->getProject()->isComponentsEnabled()) {
                            throw new Exception($this->getI18n()->__('Components are disabled'));
                        }

                        $components = $issue->getComponents();
                        if (!array_key_exists($affected_id, $components)) {
                            throw new Exception($this->getI18n()->__('This component is not affected by this issue'));
                        }
                        $component = $components[$affected_id];

                        if ($component['confirmed'] == true) {
                            $issue->confirmAffectedComponent($component['component'], false);
                            $confirmed = false;
                        } else {
                            $issue->confirmAffectedComponent($component['component']);
                            $confirmed = true;
                        }

                        break;
                    case 'build':
                        if (!$issue->getProject()->isBuildsEnabled()) {
                            throw new Exception($this->getI18n()->__('Releases are disabled'));
                        }

                        $builds = $issue->getBuilds();
                        if (!array_key_exists($affected_id, $builds)) {
                            throw new Exception($this->getI18n()->__('This release is not affected by this issue'));
                        }
                        $build = $builds[$affected_id];

                        if ($build['confirmed'] == true) {
                            $issue->confirmAffectedBuild($build['build'], false);
                            $confirmed = false;
                        } else {
                            $issue->confirmAffectedBuild($build['build']);
                            $confirmed = true;
                        }

                        break;
                    default:
                        throw new Exception('Internal error');
                }

                return $this->renderJSON(['confirmed' => $confirmed, 'text' => ($confirmed) ? $this->getI18n()->__('Confirmed') : $this->getI18n()->__('Unconfirmed')]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        public function runRemoveAffected(Request $request)
        {
            framework\Context::loadLibrary('ui');
            try {
                $issue = Issues::getTable()->selectById($request['issue_id']);

                if (!$issue->canEditIssue()) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('You are not allowed to do this')]);
                }

                switch ($request['affected_type']) {
                    case 'edition':
                        if (!$issue->getProject()->isEditionsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Editions are disabled')]);
                        }

                        $editions = $issue->getEditions();
                        $edition = $editions[$request['affected_id']];

                        $issue->removeAffectedEdition($edition['edition']);

                        $message = framework\Context::getI18n()->__('Edition <b>%edition</b> is no longer affected by this issue', ['%edition' => $edition['edition']->getName()], true);

                        break;
                    case 'component':
                        if (!$issue->getProject()->isComponentsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Components are disabled')]);
                        }

                        $components = $issue->getComponents();
                        $component = $components[$request['affected_id']];

                        $issue->removeAffectedComponent($component['component']);

                        $message = framework\Context::getI18n()->__('Component <b>%component</b> is no longer affected by this issue', ['%component' => $component['component']->getName()], true);

                        break;
                    case 'build':
                        if (!$issue->getProject()->isBuildsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Releases are disabled')]);
                        }

                        $builds = $issue->getBuilds();
                        if (isset($builds[$request['affected_id']])) {
                            $build = $builds[$request['affected_id']];

                            $issue->removeAffectedBuild($build['build']);
                            $message = framework\Context::getI18n()->__('Release <b>%build</b> is no longer affected by this issue', ['%build' => $build['build']->getName()], true);
                        } else {
                            $message = framework\Context::getI18n()->__('The release is no longer affected by this issue');
                        }

                        break;
                    default:
                        throw new Exception('Internal error');
                }

                $editions = [];
                $components = [];
                $builds = [];

                if ($issue->getProject()->isEditionsEnabled()) {
                    $editions = $issue->getEditions();
                }

                if ($issue->getProject()->isComponentsEnabled()) {
                    $components = $issue->getComponents();
                }

                if ($issue->getProject()->isBuildsEnabled()) {
                    $builds = $issue->getBuilds();
                }

                $count = count($editions) + count($components) + count($builds) - 1;

                return $this->renderJSON(['message' => $message, 'itemcount' => $count]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('An internal error has occured')]);
            }
        }

        public function runStatusAffected(Request $request)
        {
            framework\Context::loadLibrary('ui');
            try {
                $issue = Issues::getTable()->selectById($request['issue_id']);
                $status = tables\ListTypes::getTable()->selectById($request['status_id']);
                if (!$issue->canEditIssue()) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('You are not allowed to do this')]);
                }

                switch ($request['affected_type']) {
                    case 'edition':
                        if (!$issue->getProject()->isEditionsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Editions are disabled')]);
                        }
                        $editions = $issue->getEditions();
                        $edition = $editions[$request['affected_id']];

                        $issue->setAffectedEditionStatus($edition['edition'], $status);
                        break;
                    case 'component':
                        if (!$issue->getProject()->isComponentsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Components are disabled')]);
                        }
                        $components = $issue->getComponents();
                        $component = $components[$request['affected_id']];

                        $issue->setAffectedcomponentStatus($component['component'], $status);
                        break;
                    case 'build':
                        if (!$issue->getProject()->isBuildsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Releases are disabled')]);
                        }
                        $builds = $issue->getBuilds();
                        $build = $builds[$request['affected_id']];

                        $issue->setAffectedbuildStatus($build['build'], $status);
                        break;
                    default:
                        throw new Exception('Internal error');
                }

                return $this->renderJSON(['colour' => $status->getColor(), 'name' => $status->getName()]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('An internal error has occured')]);
            }
        }

        public function runAddAffected(Request $request)
        {
            framework\Context::loadLibrary('ui');
            try {
                $issue = Issues::getTable()->selectById($request['issue_id']);
                $statuses = entities\Status::getAll();

                switch ($request['item_type']) {
                    case 'edition':
                        if (!$issue->getProject()->isEditionsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Editions are disabled')]);
                        } elseif (!$issue->canEditAffectedEditions()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('You are not allowed to do this')]);
                        }

                        $edition = tables\Editions::getTable()->selectById($request['which_item_edition']);

                        if (tables\IssueAffectsEdition::getTable()->getByIssueIDandEditionID($issue->getID(), $edition->getID())) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('%item is already affected by this issue', ['%item' => $edition->getName()])]);
                        }

                        $result = $issue->addAffectedEdition($edition);

                        if ($result !== false) {
                            $itemtype = 'edition';
                            $item = $result;
                            $itemtypename = framework\Context::getI18n()->__('Edition');
                            $content = get_component_html('main/affecteditem', ['item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses]);
                        }

                        $message = framework\Context::getI18n()->__('Edition <b>%edition</b> is now affected by this issue', ['%edition' => $edition->getName()], true);

                        break;
                    case 'component':
                        if (!$issue->getProject()->isComponentsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Components are disabled')]);
                        } elseif (!$issue->canEditAffectedComponents()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('You are not allowed to do this')]);
                        }

                        $component = tables\Components::getTable()->selectById($request['which_item_component']);

                        if (tables\IssueAffectsComponent::getTable()->getByIssueIDandComponentID($issue->getID(), $component->getID())) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('%item is already affected by this issue', ['%item' => $component->getName()])]);
                        }

                        $result = $issue->addAffectedComponent($component);

                        if ($result !== false) {
                            $itemtype = 'component';
                            $item = $result;
                            $itemtypename = framework\Context::getI18n()->__('Component');
                            $content = get_component_html('main/affecteditem', ['item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses]);
                        }

                        $message = framework\Context::getI18n()->__('Component <b>%component</b> is now affected by this issue', ['%component' => $component->getName()], true);

                        break;
                    case 'build':
                        if (!$issue->getProject()->isBuildsEnabled()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Releases are disabled')]);
                        } elseif (!$issue->canEditAffectedBuilds()) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('You are not allowed to do this')]);
                        }

                        $build = tables\Builds::getTable()->selectById($request['which_item_build']);

                        if (tables\IssueAffectsBuild::getTable()->getByIssueIDandBuildID($issue->getID(), $build->getID())) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('%item is already affected by this issue', ['%item' => $build->getName()])]);
                        }

                        $result = $issue->addAffectedBuild($build);

                        if ($result !== false) {
                            $itemtype = 'build';
                            $item = $result;
                            $itemtypename = framework\Context::getI18n()->__('Release');
                            $content = get_component_html('main/affecteditem', ['item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses]);
                        }

                        $message = framework\Context::getI18n()->__('Release <b>%build</b> is now affected by this issue', ['%build' => $build->getName()], true);

                        break;
                    default:
                        throw new Exception('Internal error');
                }

                $editions = [];
                $components = [];
                $builds = [];

                if ($issue->getProject()->isEditionsEnabled()) {
                    $editions = $issue->getEditions();
                }

                if ($issue->getProject()->isComponentsEnabled()) {
                    $components = $issue->getComponents();
                }

                if ($issue->getProject()->isBuildsEnabled()) {
                    $builds = $issue->getBuilds();
                }

                $count = count($editions) + count($components) + count($builds);

                return $this->renderJSON(['content' => $content, 'message' => $message, 'itemcount' => $count]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * Reset user password
         *
         * @Route(name="reset_password", url="/reset/password/:user/:reset_hash")
         * @AnonymousRoute
         *
         * @param Request $request The request object
         */
        public function runResetPassword(Request $request)
        {
            $i18n = framework\Context::getI18n();

            try {
                if ($request->hasParameter('user') && $request->hasParameter('reset_hash')) {
                    $user = entities\User::getByUsername(str_replace('%2E', '.', $request['user']));
                    if ($user instanceof entities\User) {
                        if ($request['reset_hash'] == $user->getActivationKey()) {
                            $this->error = false;
                            if ($request->isPost()) {
                                $p1 = trim($request['password_1']);
                                $p2 = trim($request['password_2']);

                                if ($p1 && $p2 && $p1 == $p2) {
                                    $user->setPassword($p1);
                                    $user->regenerateActivationKey();
                                    $user->save();
                                    framework\Context::setMessage('login_message', $i18n->__('Your password has been reset. Please log in.'));
                                    framework\Context::setMessage('login_referer', $this->getRouting()->generate('home'));

                                    return $this->forward($this->getRouting()->generate('login_page'));
                                } else {
                                    $this->error = true;
                                }
                            } else {
                                $user->regenerateActivationKey();
                            }
                            $this->user = $user;
                        } else {
                            throw new Exception('Your password recovery token is either invalid or has expired');
                        }
                    } else {
                        throw new Exception('User is invalid or does not exist');
                    }
                } else {
                    throw new Exception('An internal error has occured');
                }
            } catch (Exception $e) {
                framework\Context::setMessage('login_message_err', $i18n->__($e->getMessage()));

                return $this->forward($this->getRouting()->generate('login_page'));
            }
        }

        public function runIssueGetTempFieldValue(Request $request)
        {
            switch ($request['field']) {
                case 'assigned_to':
                    if ($request['identifiable_type'] == 'user') {
                        $identifiable = tables\Users::getTable()->selectById($request['value']);
                        $content = $this->getComponentHTML('main/userdropdown', ['user' => $identifiable]);
                    } elseif ($request['identifiable_type'] == 'team') {
                        $identifiable = tables\Teams::getTable()->selectById($request['value']);
                        $content = $this->getComponentHTML('main/teamdropdown', ['team' => $identifiable]);
                    } else {
                        $content = '';
                    }

                    return $this->renderJSON(['content' => $content]);
            }
        }

        public function runAccountCheckUsername(Request $request)
        {
            if ($request['desired_username'] && entities\User::isUsernameAvailable($request['desired_username'])) {
                return $this->renderJSON(['available' => true, 'url' => $this->getRouting()->generate('get_partial_for_backdrop', ['key' => 'confirm_username', 'username' => $request['desired_username']])]);
            } else {
                return $this->renderJSON(['available' => false]);
            }
        }

        public function runAccountPickUsername(Request $request)
        {
            if (entities\User::isUsernameAvailable($request['selected_username'])) {
                $authentication_backend = Settings::getAuthenticationBackend();

                $user = $this->getUser();
                $user->setUsername($request['selected_username']);
                $user->setOpenIdLocked(false);
                $user->setPassword(entities\User::createPassword());
                $user->save();

                if ($authentication_backend->getAuthenticationMethod() == framework\AuthenticationBackend::AUTHENTICATION_TYPE_TOKEN) {
                    $this->getResponse()->setCookie('username', $user->getUsername());
                    $this->getResponse()->setCookie('session_token', $user->createUserSession()->getToken());
                } else {
                    $this->getResponse()->setCookie('username', $user->getUsername());
                    $this->getResponse()->setCookie('password', $user->getHashPassword());
                }

                framework\Context::setMessage('username_chosen', true);
                $this->forward($this->getRouting()->generate('account'));
            }

            framework\Context::setMessage('error', $this->getI18n()->__('Could not pick the username "%username"', ['%username' => $request['selected_username']]));
            $this->forward($this->getRouting()->generate('account'));
        }

        public function runDashboardView(Request $request)
        {
            $view = tables\DashboardViews::getTable()->selectById($request['view_id']);
            if (!$view instanceof entities\DashboardView) {
                return $this->renderJSON(['content' => 'invalid view']);
            }

            if ($view->getTargetType() == entities\DashboardView::TYPE_PROJECT) {
                framework\Context::setCurrentProject($view->getDashboard()->getProject());
            }

            return $this->renderJSON(['content' => $this->returnComponentHTML($view->getTemplate(), ['view' => $view])]);
        }

        public function runGetTempIdentifiable(Request $request)
        {
            if ($request['i_type'] == 'user')
                return $this->renderComponent('main/userdropdown', ['user' => $request['i_id']]);
            else
                return $this->renderComponent('main/teamdropdown', ['team' => $request['i_id']]);
        }

        public function runGetACLFormEntry(Request $request)
        {
            switch ($request['identifiable_type']) {
                case 'user':
                    $target = tables\Users::getTable()->selectById((int)$request['identifiable_value']);
                    break;
                case 'team':
                    $target = tables\Teams::getTable()->selectById((int)$request['identifiable_value']);
                    break;
            }

            if (!$target instanceof entities\common\Identifiable) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $this->getI18n()->__('Could not show permissions list')]);
            }

            return $this->renderJSON(['content' => $this->getComponentHTML('main/issueaclformentry', ['target' => $target])]);
        }

        public function runRemoveScope(Request $request)
        {
            $this->getUser()->removeScope((int)$request['scope_id']);

            return $this->renderJSON('ok');
        }

        public function runConfirmScope(Request $request)
        {
            $this->getUser()->confirmScope((int)$request['scope_id']);

            return $this->renderJSON('ok');
        }

        public function runAddScope(Request $request)
        {
            if ($request->isPost()) {
                $scope = framework\Context::getScope();
                $this->getUser()->addScope($scope, false);
                $this->getUser()->confirmScope($scope->getID());
                $route = (Settings::getLoginReturnRoute() != 'referer') ? Settings::getLoginReturnRoute() : 'home';
                $this->forward($this->getRouting()->generate($route));
            }
        }

        public function runIssueLog(Request $request)
        {
            try {
                $this->issue = Issues::getTable()->getIssueById((int)$request['issue_id']);
                $this->log_items = $this->issue->getLogEntries();
                if ($this->issue->isDeleted() || !$this->issue->hasAccess())
                    $this->issue = null;
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function runIssueMoreactions(Request $request)
        {
            try {
                $issue = Issues::getTable()->getIssueById((int)$request['issue_id']);
                if ($request['board_id']) $board = tables\AgileBoards::getTable()->selectById((int)$request['board_id']);

                $times = (!isset($board) || $board->getType() != entities\AgileBoard::TYPE_KANBAN);
                $estimator_mode = isset($request['estimator_mode']) ? $request['estimator_mode'] : null;

                return $this->renderJSON(['menu' => $this->getComponentHTML('main/issuemoreactions', compact('issue', 'times', 'board', 'estimator_mode'))]);
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
         * Milestone actions
         *
         * @Route(url="/:project_key/milestone/:milestone_id/actions/*", name='project_milestone')
         *
         * @param Request $request
         */
        public function runMilestone(Request $request)
        {
            $milestone_id = ($request['milestone_id']) ? $request['milestone_id'] : null;
            $milestone = new entities\Milestone($milestone_id);
            $action_option = str_replace($this->selected_project->getKey() . '/milestone/' . $request['milestone_id'] . '/', '', $request['url']);

            try {
                if (!($this->getUser()->canEditMilestones($this->selected_project) || ($this->getUser()->canManageProjectReleases($this->selected_project) && $this->getUser()->canManageProject($this->selected_project))))
                    throw new Exception($this->getI18n()->__("You don't have access to modify milestones"));

                switch (true) {
                    case $request->isDelete():
                        $milestone->delete();

                        $no_milestone = new entities\Milestone(0);
                        $no_milestone->setProject($milestone->getProject());

                        return $this->renderJSON(['issue_count' => $no_milestone->countIssues(), 'hours' => $no_milestone->getHoursEstimated(), 'points' => $no_milestone->getPointsEstimated()]);
                    case $request->isPost():
                        $this->_saveMilestoneDetails($request, $milestone);

                        if ($request->hasParameter('issues') && $request['include_selected_issues'])
                            Issues::getTable()->assignMilestoneIDbyIssueIDs($milestone->getID(), $request['issues']);

                        $event = framework\Event::createNew('project', 'runMilestone::post', $milestone);
                        $event->triggerUntilProcessed();

                        if ($event->isProcessed()) {
                            $component = $event->getReturnValue();
                        } else {
                            $component = $this->getComponentHTML('project/milestonebox', ['milestone' => $milestone, 'include_counts' => true]);
                        }
                        $message = framework\Context::getI18n()->__('Milestone saved');

                        return $this->renderJSON(['message' => $message, 'component' => $component, 'milestone_id' => $milestone->getID()]);
                    case $action_option == 'details':
                        throw new Exception('Yikes');
//                    \pachno\core\framework\Context::performAction(
//                        new \pachno\core\modules\project\controllers\Main(),
//                        'project',
//                        'MilestoneDetails'
//                    );
                        return true;
                    default:
                        return $this->forward($this->getRouting()->generate('project_roadmap', ['project_key' => $this->selected_project->getKey()]));
                }
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        protected function _saveMilestoneDetails(Request $request, $milestone = null)
        {
            if (!$request['name'])
                throw new Exception($this->getI18n()->__('You must provide a valid milestone name'));

            if ($milestone === null) $milestone = new entities\Milestone();
            $milestone->setName($request['name']);
            $milestone->setProject($this->selected_project);
            $milestone->setStarting((bool)$request['is_starting']);
            $milestone->setScheduled((bool)$request['is_scheduled']);
            $milestone->setDescription($request['description']);
            $milestone->setVisibleRoadmap($request['visibility_roadmap']);
            $milestone->setVisibleIssues($request['visibility_issues']);
            $milestone->setType($request->getParameter('milestone_type', entities\Milestone::TYPE_REGULAR));
            $milestone->setPercentageType($request->getParameter('percentage_type', entities\Milestone::PERCENTAGE_TYPE_REGULAR));
            if ($request->hasParameter('sch_month') && $request->hasParameter('sch_day') && $request->hasParameter('sch_year')) {
                $scheduled_date = mktime(23, 59, 59, framework\Context::getRequest()->getParameter('sch_month'), framework\Context::getRequest()->getParameter('sch_day'), framework\Context::getRequest()->getParameter('sch_year'));
                $milestone->setScheduledDate($scheduled_date);
            } else
                $milestone->setScheduledDate(0);

            if ($request->hasParameter('starting_month') && $request->hasParameter('starting_day') && $request->hasParameter('starting_year')) {
                $starting_date = mktime(0, 0, 1, framework\Context::getRequest()->getParameter('starting_month'), framework\Context::getRequest()->getParameter('starting_day'), framework\Context::getRequest()->getParameter('starting_year'));
                $milestone->setStartingDate($starting_date);
            } else
                $milestone->setStartingDate(0);

            $milestone->save();
        }

        /**
         * Delete an issue todos item.
         *
         * @param Request $request
         */
        public function runDeleteTodo(Request $request)
        {
            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('This issue does not exist')]);
                }
            } else {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('This issue does not exist')]);
            }

            if (!isset($request['comment_id']) || !is_numeric($request['comment_id'])) {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('Invalid "comment_id" parameter')]);
            }

            $this->forward403unless(($request['comment_id'] == 0 && $issue->canEditDescription()) || ($request['comment_id'] != 0 && $issue->getComments()[$request['comment_id']]->canUserEditComment()));

            if (!isset($request['todo']) || $request['todo'] == '') {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('Invalid "todo" parameter')]);
            }

            framework\Context::loadLibrary('common');
            $issue->deleteTodo($request['comment_id'], $request['todo']);

            return $this->renderJSON([
                'content' => $this->getComponentHTML('todos', compact('issue'))
            ]);
        }

        /**
         * Toggle done for issue todos item.
         *
         * @param Request $request
         */
        public function runToggleDoneTodo(Request $request)
        {
            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('This issue does not exist')]);
                }
            } else {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('This issue does not exist')]);
            }

            if (!isset($request['comment_id']) || !is_numeric($request['comment_id'])) {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('Invalid "comment_id" parameter')]);
            }

            if (!isset($request['todo']) || $request['todo'] == '') {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('Invalid "todo" parameter')]);
            }

            if (!isset($request['mark']) || !in_array($request['mark'], ['done', 'not_done'])) {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('Invalid "mark" parameter')]);
            }

            framework\Context::loadLibrary('common');
            $issue->markTodo($request['comment_id'], $request['todo'], $request['mark']);

            return $this->renderJSON([
                'content' => $this->getComponentHTML('todos', compact('issue'))
            ]);
        }

        /**
         * Add an issue todos item.
         *
         * @param Request $request
         */
        public function runAddTodo(Request $request)
        {
            // If todos item is submitted via form and not ajax forward 403 error.
            $this->forward403unless($request->isAjaxCall());

            if ($issue_id = $request['issue_id']) {
                try {
                    $issue = Issues::getTable()->selectById($issue_id);
                } catch (Exception $e) {
                    return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('This issue does not exist')]);
                }
            } else {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('This issue does not exist')]);
            }

            if (!$issue->canEditDescription()) {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('You do not have permission to perform this action')]);
            }

            if (!isset($request['todo_body']) || !trim($request['todo_body'])) {
                return $this->renderJSON(['failed' => true, 'error' => framework\Context::getI18n()->__('Invalid "todo_body" parameter')]);
            }

            framework\Context::loadLibrary('common');
            $issue->addTodo($request['todo_body']);

            return $this->renderJSON([
                'content' => $this->getComponentHTML('todos', compact('issue'))
            ]);
        }

        /**
         * Helper method for sorting two issues based on their project and
         * IDs.
         *
         * Sorting algorithm takes into account the following factors (in
         * decreasing weight of importance):
         *
         * - Issue belongs to selected project.
         * - Issue project name (alphabetical sorting).
         * - Issue ID.
         *
         * @param Issue $a Issue to compare.
         * @param Issue $b Issue to compare.
         *
         * @return -1 if issue $a comes ahead of issue $b, 0 if issue $a is the same as issue $b, 1 if issue $b comes ahead of issue $a.
         */
        protected function compareIssuesByProject($a, $b)
        {
            $project_a = $a->getProject();
            $project_b = $b->getProject();

            if ($project_a == $this->selected_project && $project_b != $this->selected_project) {
                $result = -1;
            } elseif ($project_b == $this->selected_project && $project_a != $this->selected_project) {
                $result = 1;
            } else {
                $result = strcasecmp($a->getProject()->getName(), $b->getProject()->getName());

                if ($result == 0) {
                    $result = $a->getID() > $b->getID();
                }
            }

            return $result;
        }
    }
