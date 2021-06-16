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
    use pachno\core\entities\tables\Articles;
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
                $this->forward($found_issue->getUrl());
            } else {
                framework\Context::setMessage('issue_message', $this->getI18n()->__('There are no more issues in that direction.'));
                $this->forward($issue->getUrl());
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

//        /**
//         * View an issue
//         *
//         * @param Request $request
//         */
//        public function runViewIssue(Request $request)
//        {
//            framework\Logging::log('Loading issue');
//
//            $issue = $this->_getIssueFromRequest($request);
//
//            if ($issue instanceof Issue) {
//                if (!array_key_exists('viewissue_list', $_SESSION) || !is_array($_SESSION['viewissue_list'])) {
//                    $_SESSION['viewissue_list'] = [];
//                }
//
//                $k = array_search($issue->getID(), $_SESSION['viewissue_list']);
//                if ($k !== false)
//                    unset($_SESSION['viewissue_list'][$k]);
//
//                array_push($_SESSION['viewissue_list'], $issue->getID());
//
//                if (count($_SESSION['viewissue_list']) > 10)
//                    array_shift($_SESSION['viewissue_list']);
//
//                $this->getUser()->markNotificationsRead('issue', $issue->getID());
//
//                framework\Context::getUser()->setNotificationSetting(Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), false);
//
//                framework\Event::createNew('core', 'viewissue', $issue)->trigger();
//            }
//
//            $message = framework\Context::getMessageAndClear('issue_saved');
//            $uploaded = framework\Context::getMessageAndClear('issue_file_uploaded');
//
//            if (framework\Context::hasMessage('issue_deleted_shown') && (is_null($issue) || ($issue instanceof Issue && $issue->isDeleted()))) {
//                $request_referer = ($request['referer'] ?: (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null));
//
//                if ($request_referer) {
//                    return $this->forward($request_referer);
//                }
//            } elseif (framework\Context::hasMessage('issue_deleted')) {
//                $this->issue_deleted = framework\Context::getMessageAndClear('issue_deleted');
//                framework\Context::setMessage('issue_deleted_shown', true);
//            } elseif ($message == true) {
//                $this->issue_saved = true;
//            } elseif ($uploaded == true) {
//                $this->issue_file_uploaded = true;
//            } elseif (framework\Context::hasMessage('issue_error')) {
//                $this->error = framework\Context::getMessageAndClear('issue_error');
//            } elseif (framework\Context::hasMessage('issue_message')) {
//                $this->issue_message = framework\Context::getMessageAndClear('issue_message');
//            }
//
//            $this->issue = $issue;
//            $event = framework\Event::createNew('core', 'viewissue', $issue)->trigger();
//            $this->listenViewIssuePostError($event);
//        }

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

                return $this->renderJSON(['error' => $this->getI18n()->__("You don't have permission to move this issue")]);
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

                    return $this->renderJSON(['error' => $this->getI18n()->__("You don't have permission to move this issue")]);
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

            return $this->renderJSON(['forward' => $issue->getUrl()]);
        }

        /**
         * @param Request $request
         * @Route(url="/projects/list/:list_mode/:project_state/*", name="project_list")
         */
        public function runProjectList(Request $request)
        {
            $this->getResponse()->setDecoration(Response::DECORATE_NONE);
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

            return $this->renderJSON([
                'content' => $this->getComponentHTML('main/projectlistcontainer', [
                    'pagination' => $pagination,
                    'projects' => $pagination->getPageItems(),
                    'project_count' => count($projects),
                    'list_mode' => $list_mode,
                    'project_state' => $project_state,
                    'show_project_config_link' => $this->getUser()->canSaveConfiguration() && framework\Context::getScope()->hasProjectsAvailable(),
                ])
            ]);
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
                                        return $this->renderJSON(['title' => $this->getI18n()->__('Error extracting module zip'), 'message' => $this->getI18n()->__('Could not extract the module into the destination folder. Make sure you have write access to the modules folder and try again.')]);
                                        break;
                                }
                            } catch (Exception $e) {
                                $this->getResponse()->setHttpStatus(400);

                                return $this->renderJSON(['message' => $this->getI18n()->__('An error occured when trying to install the module: ' . $e->getMessage())]);
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
                        case 'invite-user':
                            $inviteUser = new entities\User();
                            $inviteUser->setUsername($request['email']);
                            $inviteUser->setRealname($request['email']);
                            $inviteUser->setEmail($request['email']);
                            $inviteUser->setGroup(framework\Settings::get(framework\Settings::SETTING_USER_GROUP));
                            $password = entities\User::createPassword();
                            $inviteUser->setPassword($password);
                            $inviteUser->save();
                            $inviteUser->setActivated(false);
                            $inviteUser->save();

                            framework\Event::createNew('core', 'userdata::inviteUser', $inviteUser)->trigger();
                            break;
                    }
                } else {
                    switch ($request['say']) {
                        case 'get_module_updates':
                            $addons_param = [];
                            $addons_json = [];
                            foreach ($request->getParameter('addons', []) as $addon) {
                                $addons_param[] = 'addons[]=' . $addon;
                            }
                            if (count($addons_param)) {
                                try {
                                    $client = new Net_Http_Client();
                                    $client->get('https://pachno.local/addons/index.json?' . implode('&', $addons_param));
                                    $addons_json = json_decode($client->getBody(), true);
                                } catch (Exception $e) {
                                }

                                return $this->renderJSON($addons_json);
                            }

                            return $this->renderJSON([]);
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
                            return $this->renderJSON(['component' => $this->getComponentHTML('configuration/onlinemodules')]);
                            break;
                        case 'get_themes':
                            return $this->renderComponent('configuration/onlinethemes');
                            break;
                        case 'get_usernames':
                            $users = tables\Users::getTable()->getByUserIDs(explode(',', $request['user_ids']));
                            $data['users'] = [];
                            foreach ($users as $user) {
                                $data['users'][] = ['id' => $user->getID(), 'name' => $user->getName()];
                            }
                            break;
                        case 'get_articlenames':
                            $articles = tables\Articles::getTable()->getByArticleIds(explode(',', $request['article_ids']));
                            $data['articles'] = [];
                            foreach ($articles as $article) {
                                $data['articles'][] = ['id' => $article->getID(), 'name' => $article->getName(), 'url' => $article->getLink()];
                            }
                            break;
                        case 'get_issues':
                            $issues = tables\Issues::getTable()->getByIssueIDs(explode(',', $request['issue_ids']));
                            $data['issues'] = [];
                            foreach ($issues as $issue) {
                                $data['issues'][] = ['id' => $issue->getID(), 'name' => $issue->getName(), 'title' => $issue->getFormattedTitle(true), 'issue_no' => $issue->getIssueNo(), 'url' => $issue->getUrl(), 'closed' => ($issue->isClosed()), 'issue_type' => $issue->getIssueType()->toJSON(), 'status' => $issue->getStatus()->toJSON()];
                            }
                            break;
                        case 'get_mentionables':
                            $mentionables = [];
                            if ($request->hasParameter('value')) {
                                switch ($request['type']) {
                                    case 'user':
                                        $users = tables\Users::getTable()->getByDetails($request['value'], 10);

                                        foreach ($users as $user) {
                                            if ($user->isOpenIdLocked())
                                                continue;

                                            $mentionables[$user->getID()] = ['id' => $user->getID(), 'username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                        }

                                        break;
                                    case 'article':
                                        $target = tables\Articles::getTable()->selectById($request['target_id']);

                                        foreach (Articles::getTable()->findArticles($request['value'], $target->getProject(), null, 10) as $article) {
                                            $mentionables[] = ['id' => $article->getID(), 'name' => $article->getName(), 'icon' => ['name' => 'file', 'type' => 'far']];
                                        }

                                        break;
                                }
                            } else {
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
                                switch ($request['type']) {
                                    case 'user':
                                        if (isset($target) && $target instanceof MentionableProvider) {
                                            foreach ($target->getMentionableUsers() as $user) {
                                                if ($user->isOpenIdLocked())
                                                    continue;

                                                $mentionables[$user->getID()] = ['id' => $user->getID(), 'username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                            }
                                        }
                                        foreach ($this->getUser()->getFriends() as $user) {
                                            if ($user->isOpenIdLocked())
                                                continue;

                                            $mentionables[$user->getID()] = ['id' => $user->getID(), 'username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                        }
                                        foreach ($this->getUser()->getTeams() as $team) {
                                            foreach ($team->getMembers() as $user) {
                                                if ($user->isOpenIdLocked())
                                                    continue;

                                                $mentionables[$user->getID()] = ['id' => $user->getID(), 'username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                            }
                                        }
                                        foreach ($this->getUser()->getClients() as $client) {
                                            foreach ($client->getMembers() as $user) {
                                                if ($user->isOpenIdLocked())
                                                    continue;

                                                $mentionables[$user->getID()] = ['id' => $user->getID(), 'username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL()];
                                            }
                                        }
                                        break;
                                    case 'article':
                                        $overview_article = Articles::getTable()->getOrCreateMainPage($target->getProject());
                                        $mentionables[] = ['id' => $overview_article->getID(), 'name' => $overview_article->getName(), 'icon' => ['name' => 'file', 'type' => 'far']];
                                        foreach (Articles::getTable()->getManualSidebarArticles(false, $target->getProject()) as $article) {
                                            $mentionables[] = ['id' => $article->getID(), 'name' => $article->getName(), 'icon' => ['name' => 'file', 'type' => 'far']];
                                        }
                                        if (count($mentionables) > 10) {
                                            $mentionables = array_slice($mentionables, 0, 10);
                                        }

                                        break;
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
                                case entities\Comment::TYPE_COMMIT:
                                    $target = tables\Commits::getTable()->selectById($request['target_id']);
                                    $data['comments'] = $this->getComponentHTML('main/commentlist', [
                                        'comment_count_div' => 'commit_comment_count',
                                        'mentionable_target_type' => 'commit',
                                        'target_type' => Comment::TYPE_COMMIT,
                                        'target_id' => $target->getID(),
                                        'commit' => $target
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
                            $data['poll_interval'] = 0;
                    }
                }

                return $this->renderJSON($data);
            }
        }

        /**
         * Frontpage
         *
         * @Route(url="/projects", name="projects_list")
         *
         * @param Request $request
         */
        public function runProjectsList(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPermission(entities\Permission::PERMISSION_PAGE_ACCESS_PROJECT_LIST));
        }

        /**
         * User homepage
         *
         * @param Request $request
         */
        public function runIndex(Request $request)
        {
            if ($this->getUser()->isGuest() || !$this->getUser()->hasPermission(entities\Permission::PERMISSION_PAGE_ACCESS_DASHBOARD)) {
                if ($this->getUser()->isGuest()) {
                    return $this->forward($this->getRouting()->generate('projects_list'));
                } else {
                    $this->forward403();
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

            if (!$available) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON([
                    'error' => $this->getI18n()->__('Woops, that username is unavailable'),
                    'errors' => ['username' => $this->getI18n()->__('Oh no, this username is unavailable!')]
                ]);
            }

            return $this->renderJSON(['available' => (bool) $available]);
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
                $email = mb_strtolower(trim($request['email_address']));
                $confirmemail = mb_strtolower(trim($request['email_confirm']));
                $security = $request['verification_no'];

                $available = tables\Users::getTable()->isUsernameAvailable($username);

                if (!$available) {
                    throw new Exception($i18n->__('This username is in use'));
                }

                if (empty($username)) {
                    $fields['username'] = $i18n->__('Please enter your desired username.');
                }

                if (empty($email)) {
                    $fields['email_address'] = $i18n->__('You need to fill out this field.');
                }
                if (empty($confirmemail)) {
                    $fields['email_confirm'] = $i18n->__('You need to fill out this field.');
                }
                if (empty($security)) {
                    $fields['verification_no'] = $i18n->__('You need to fill out this field.');
                }

                if (!empty($email) && !empty($confirmemail) && $email != $confirmemail) {
                    $fields['email_address'] = $i18n->__('Please enter the email address twice');
                    $fields['email_confirm'] = $i18n->__('Please enter the email address twice');
                    throw new Exception($i18n->__('The email address must be valid, and must be typed twice.'));
                }

                if (!empty($security) && $security != $_SESSION['activation_number']) {
                    $fields['verification_no'] = $i18n->__('Woops, that seems like a wrong number');
                    throw new Exception($i18n->__('To prevent automatic sign-ups, enter the verification number shown in the image.'));
                }

                $email_ok = false;

                if (pachno_check_syntax($email, "EMAIL")) {
                    $email_ok = true;
                }

                if ($email_ok && Settings::hasRegistrationDomainWhitelist()) {
                    $allowed_domains = preg_replace('/[[:space:]]*,[[:space:]]*/', '|', Settings::getRegistrationDomainWhitelist());
                    if (preg_match('/@(' . $allowed_domains . ')$/i', $email) == false) {
                        $fields['email_address'] = $i18n->__('Please enter a valid email address');
                        throw new Exception($i18n->__('Email adresses from this domain can not be used.'));
                    }
                }

                if ($email_ok == false) {
                    $fields['email_address'] = $i18n->__('Please enter the email address twice');
                    $fields['email_confirm'] = $i18n->__('Please enter the email address twice');
                    throw new Exception($i18n->__('The email address must be valid, and must be typed twice.'));
                }

                if (count($fields)) {
                    throw new Exception($i18n->__('You need to fill out all fields correctly.'));
                }

                $password = entities\User::createPassword();
                $user = new entities\User();
                $user->setUsername($username);
                $user->setRealname($realname);
                $user->setGroup(Settings::getDefaultGroup());
                $user->setEnabled();
                $user->setPassword($password);
                $user->setEmail($email);
                $user->setJoined();
                $user->save();

                unset($_SESSION['activation_number']);

                if ($user->isActivated()) {
                    framework\Context::setMessage('auto_password', $password);

                    return $this->renderJSON(['loginmessage' => $i18n->__('After pressing %continue, you need to set your password.', ['%continue' => $i18n->__('Continue')]), 'one_time_password' => $password, 'activated' => true]);
                }

                return $this->renderJSON(['loginmessage' => $i18n->__('The account has now been registered - check your email inbox for the activation email. Please be patient - this email can take up to two hours to arrive.'), 'activated' => false]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $i18n->__($e->getMessage()), 'errors' => $fields]);
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

            $user = tables\Users::getTable()->getByUsername(str_replace(['%2E', '%2B', ' '], ['.', '+', '+'], $request->getRawParameter('user')));
            if ($user instanceof entities\User) {
                if ($user->getActivationKey() != $request['key']) {
                    framework\Context::setMessage('login_message_err', framework\Context::getI18n()->__('This activation link is not valid'));
                } else {
                    $user->setValidated(true);
                    $user->save();

                    $authentication_backend = framework\Settings::getAuthenticationBackend();
                    if ($authentication_backend->getAuthenticationMethod() == framework\AuthenticationBackend::AUTHENTICATION_TYPE_TOKEN) {
                        $token = $user->createUserSession();
                        $authentication_backend->persistTokenSession($user, $token, false);
                    } else {
                        $password = $user->getHashPassword();
                        $authentication_backend->persistPasswordSession($user, $password, false);
                    }

                    framework\Context::setMessage('auto_password', entities\User::createPassword());

                    return $this->forward($this->getRouting()->generate('profile_account'));
//                    framework\Context::setMessage('login_message', framework\Context::getI18n()->__('Your account has been activated! You can now log in with the username %user and the password in your activation email.', ['%user' => $user->getUsername()]));
                }
            } else {
                framework\Context::setMessage('login_message_err', framework\Context::getI18n()->__('This activation link is not valid'));
            }
            $this->forward($this->getRouting()->generate('auth_login_page'));
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

            return $this->forward($this->getRouting()->generate('profile_account'));
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
                if ($this->getUser()->isGuest()) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__("You're not allowed to change your password.")]);
                }
                if (!$request->hasParameter('new_password_1') || !$request['new_password_1']) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter a new password'), 'errors' => ['new_password_1' => $this->getI18n()->__('Please enter a password')]]);
                }
                if (!$request->hasParameter('new_password_2') || !$request['new_password_2']) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter the new password twice'), 'errors' => ['new_password_1' => $this->getI18n()->__("These passwords don't match"), 'new_password_2' => $this->getI18n()->__("These passwords don't match")]]);
                }
                if ($request['new_password_1'] != $request['new_password_2']) {
                    $this->getResponse()->setHttpStatus(400);

                    return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please enter the new password twice'), 'errors' => ['new_password_2' => $this->getI18n()->__('Please enter the password again, here')]]);
                }
                $this->getUser()->setPassword($request['new_password_1']);
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
                framework\Context::setPermission('canaccessrestrictedissues', $issue->getID(), 'core', $user_id, 0, 0);
            }
            foreach ($al_teams as $team_id) {
                framework\Context::setPermission('canaccessrestrictedissues', $issue->getID(), 'core', 0, 0, $team_id);
            }
        }

        /**
         * @param Request $request
         * @param                   $issue
         */
        protected function _lockIssueAfter(Request $request, $issue)
        {
            framework\Context::setPermission('canaccessrestrictedissues', $issue->getID(), 'core', $this->getUser()->getID(), 0, 0);

            $al_users = $request->getParameter('access_list_users', []);
            $al_teams = $request->getParameter('access_list_teams', []);
            $i_al = $issue->getAccessList();
            foreach ($i_al as $k => $item) {
                if ($item['target'] instanceof entities\Team) {
                    $team_id = $item['target']->getID();
                    if (array_key_exists($team_id, $al_teams)) {
                        unset($i_al[$k]);
                    } else {
                        framework\Context::removePermission('canaccessrestrictedissues', $issue->getID(), 'core', 0, 0, $team_id);
                    }
                } elseif ($item['target'] instanceof entities\User) {
                    $user_id = $item['target']->getID();
                    if (array_key_exists($user_id, $al_users)) {
                        unset($i_al[$k]);
                    } elseif ($user_id != $this->getUser()->getID()) {
                        framework\Context::removePermission('canaccessrestrictedissues', $issue->getID(), 'core', $user_id, 0, 0);
                    }
                }
            }
            foreach ($al_users as $user_id) {
                framework\Context::setPermission('canaccessrestrictedissues', $issue->getID(), 'core', $user_id, 0, 0);
            }
            foreach ($al_teams as $team_id) {
                framework\Context::setPermission('canaccessrestrictedissues', $issue->getID(), 'core', 0, 0, $team_id);
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
                        $json_issues = [];
                        $component = '';
                        if ($issue->isChildIssue()) {
                            foreach ($issue->getParentIssues() as $parent_issue) {
                                $json_issues[] = $parent_issue->toJSON();
                                $component = $this->getComponentHTML('main/relatedissue', ['issue' => $issue, 'related_issue' => $parent_issue]);
                            }
                        }
                        return $this->renderJSON([
                            'issue' => $issue->toJSON(),
                            'component' => $component,
                            'issues' => $json_issues
                        ]);
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
                $this->selected_issuetype = tables\IssueTypes::getTable()->selectById($request['issuetype']);

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

                if ($category_id = (int) $request['category_id']) {
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
                            case entities\DatatypeBase::INPUT_TEXTAREA_MAIN:
                            case entities\DatatypeBase::INPUT_TEXTAREA_SMALL:
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
                        if ((array_key_exists($field, entities\DatatypeBase::getAvailableFields(true)) && ($this->$var_name === null || $this->$var_name === 0)) || (!array_key_exists($field, entities\DatatypeBase::getAvailableFields(true)) && !in_array($field, ['pain_bug_type', 'pain_likelihood', 'pain_effect']) && (array_key_exists($field, $selected_customdatatype) && $selected_customdatatype[$field] === null))) {
                            $errors[$field] = true;
                        }
                    } else {
                        if (array_key_exists($field, entities\DatatypeBase::getAvailableFields(true)) || in_array($field, ['pain_bug_type', 'pain_likelihood', 'pain_effect'])) {
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

        protected function _getStatusesFromRequest($request)
        {
            if ($request->hasParameter('status_ids')) {
                try {
                    $statuses = $this->selected_project->getAvailableStatuses();
                    $request_statuses = explode(',', $request['status_ids']);
                    $selected_statuses = [];
                    foreach ($statuses as $status) {
                        if (in_array($status->getID(), $request_statuses)) {
                            $selected_statuses[$status->getID()] = $status;
                        }
                    }

                    return $selected_statuses;
                } catch (Exception $e) {
                }
            }
        }

        protected function _getPrioritiesFromRequest($request)
        {
            if ($request->hasParameter('priority_ids')) {
                try {
                    $priorities = entities\Priority::getAll();
                    $request_priorities = explode(',', $request['priority_ids']);
                    $selected_priorities = [];
                    foreach ($priorities as $priority) {
                        if (in_array($priority->getID(), $request_priorities)) {
                            $selected_priorities[$priority->getID()] = $priority;
                        }
                    }

                    return $selected_priorities;
                } catch (Exception $e) {
                }
            }
        }

        protected function _getCategoriesFromRequest($request)
        {
            if ($request->hasParameter('category_ids')) {
                try {
                    $categories = entities\Category::getAll();
                    $request_categories = explode(',', $request['category_ids']);
                    $selected_categories = [];
                    foreach ($categories as $category) {
                        if (in_array($category->getID(), $request_categories)) {
                            $selected_categories[$category->getID()] = $category;
                        }
                    }

                    return $selected_categories;
                } catch (Exception $e) {
                }
            }
        }

        protected function _getSeveritiesFromRequest($request)
        {
            if ($request->hasParameter('severity_ids')) {
                try {
                    $severities = entities\Severity::getAll();
                    $request_severities = explode(',', $request['severity_ids']);
                    $selected_severities = [];
                    foreach ($severities as $severity) {
                        if (in_array($severity->getID(), $request_severities)) {
                            $selected_severities[$severity->getID()] = $severity;
                        }
                    }

                    return $selected_severities;
                } catch (Exception $e) {
                }
            }
        }

        protected function _postIssue(Request $request)
        {
            $fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);
            $issue = new Issue();
            $issue->setProject($this->selected_project);
            $issue->setIssuetype($this->issuetype_id);
            $issue->setTitle($this->title);

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

            if (isset($fields_array['category']) || $this->selected_category instanceof entities\Datatype)
                $issue->setCategory($this->selected_category);

            if ($this->selected_status instanceof entities\Datatype)
                $issue->setStatus($this->selected_status);

            if (isset($fields_array['reproducability']) && $this->selected_reproducability instanceof entities\Datatype)
                $issue->setReproducability($this->selected_reproducability->getID());

            if (isset($fields_array['resolution']) && $this->selected_resolution instanceof entities\Datatype)
                $issue->setResolution($this->selected_resolution->getID());

            if (isset($fields_array['severity']) || $this->selected_severity instanceof entities\Datatype)
                $issue->setSeverity($this->selected_severity);

            if (isset($fields_array['priority']) || $this->selected_priority instanceof entities\Datatype)
                $issue->setPriority($this->selected_priority);

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
            $available_fields = array_keys(entities\DatatypeBase::getAvailableFields());
            $available_fields[] = 'pain_bug_type';
            $available_fields[] = 'pain_likelihood';
            $available_fields[] = 'pain_effect';

            return $this->renderJSON(['available_fields' => $available_fields, 'fields' => $fields_array]);
        }

        /**
         * Toggle favourite issue (starring)
         *
         * @param Request $request
         * @return framework\JsonOutput
         */
        public function runToggleFavouriteIssue(Request $request): framework\JsonOutput
        {
            try {
                $issue = Issues::getTable()->selectById($request['issue_id']);
                $user = tables\Users::getTable()->selectById($request['user_id']);

                if (!$issue instanceof Issue) {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => $this->getI18n()->__('This issue does not exist')]);
                }

                if (!$user instanceof entities\User) {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => $this->getI18n()->__('This user does not exist')]);
                }

                if ($user->isIssueStarred($issue->getID())) {
                    $user->removeStarredIssue($issue->getID());
                    $starred = false;
                } else {
                    $user->addStarredIssue($issue->getID());
                    $starred = true;
                    if ($user->getID() != $this->getUser()->getID()) {
                        framework\Event::createNew('core', 'issue_subscribe_user', $issue, compact('user'))->trigger();
                    }
                }

                return $this->renderJSON([
                    'starred' => $starred,
                    'subscriber' => $this->getComponentHTML('main/issuesubscriber', ['user' => $user, 'issue' => $issue]),
                    'count' => count($issue->getSubscribers())
                ]);
            } catch (Exception $e) {
                return $this->renderJSON(['error' => $this->getI18n()->__('An error occurred trying to toggle issue star status')]);
            }
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

        /**
         * @Route(name="issue_edittimespent", url="/:project_key/issues/:issue_id/timespent/:entry_id/:csrf_token")
         * @CsrfProtected
         *
         * @param Request $request
         *
         * @return framework\JsonOutput
         */
        public function runIssueEditTimeSpent(Request $request): framework\JsonOutput
        {
            $entry_id = $request['entry_id'];
            $issue_id = $request['issue_id'];
            $issue = Issues::getTable()->selectById($issue_id);

            if (!$issue instanceof Issue) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $this->getI18n()->__('This issue does not exist')]);
            }

            if ($entry_id) {
                $entry = tables\IssueSpentTimes::getTable()->selectById($entry_id);
            } else {
                $entry = new entities\IssueSpentTime();
                $entry->setIssue($issue);
                $entry->setUser($this->getUser());
            }

            if (!$entry instanceof entities\IssueSpentTime) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $this->getI18n()->__('This entry does not exist')]);
            }

            if ($request->isDelete()) {
                $entry->delete();
                $issue->save();

                return $this->renderJSON([
                    'message' => $this->getI18n()->__('The time entry was deleted'),
                    'issue' => $issue->toJSON()
                ]);
            }

            try {
                $entry->updateFromRequest($request);
                $entry->saveFromRequest($request);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $e->getMessage()]);
            }

            return $this->renderJSON([
                'component' => $this->getComponentHTML('main/editspenttimeentry', ['entry' => $entry, 'issue' => $issue]),
                'issue' => $issue->toJSON(),
                'entry' => $entry->toJSON()
            ]);
        }

        /**
         * Sets an issue field to a specified value
         * @Route(name="edit_issue", url="/:project_key/issues/:issue_id/:csrf_token", methods="POST")
         * @CsrfProtected
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
                    $issue->setShortname($request->getRawParameter('value'));
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
                    $issue->setPercentCompleted($request['value']);
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
                        'value' => Issue::getFormattedTime($issue->getEstimatedTime()),
                        'values' => $issue->getEstimatedTime()
                    ];
                    break;
                case 'posted_by':
                case 'owned_by':
                case 'assigned_to':
                    $identifiable_type = ($request->hasParameter('additional_value')) ? $request['additional_value'] : 'user';
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
                case 'parent_issue_id':
                    $new_parent_issue = tables\Issues::getTable()->selectById($request['value']);
                    foreach ($issue->getParentIssues() as $parent_issue) {
                        if (!$new_parent_issue instanceof entities\Issue || $parent_issue->getID() !== $new_parent_issue->getID()) {
                            $issue->removeDependantIssue($parent_issue);
                        }
                    }

                    if ($new_parent_issue instanceof entities\Issue) {
                        $issue->addParentIssue($new_parent_issue);
                    }

                    $return_details['changed']['parent_issue_id'] = $request['parent_issue_id'];
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

                        $parameter_id = $request->getParameter('value');
                        if ($parameter_id !== 0) {
                            $is_valid = ($is_pain) ? in_array($parameter_id, array_keys(Issue::getPainTypesOrLabel($parameter_name))) : ($parameter_id == 0 || (($parameter = $lab_function_name::getB2DBTable()->selectByID($parameter_id)) instanceof $classname));
                        }
                        if ($parameter_id == 0 || $is_valid) {
                            $issue->$set_function_name($parameter_id);

                            $return_details['changed'][$request['field']] = [
                                'value' => $parameter_id
                            ];
                        }
                    } catch (Exception $e) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => $e->getMessage()]);
                    }
                    break;
                case 'cover_image':
                    if (!$request['value']) {
                        $issue->setCoverImageFile(null);
                    }
                    foreach ($issue->getFiles() as $file) {
                        if ($file->getID() == $request['value']) {
                            $file->setType(entities\File::TYPE_COVER);
                            $file->save();
                            $issue->setCoverImageFile($file);
                        } elseif ($file->getType() == entities\File::TYPE_COVER) {
                            $file->setType(entities\File::TYPE_ATTACHMENT);
                            $file->save();
                        }
                    }

                    $issue->save();
                    break;
                case 'blocking':
                    $issue->setBlocking($request['value']);
                    $issue->save();
                    break;
                case 'locked':
                    $issue->setLocked($request['value']);
                    $issue->save();
                    break;
                default:
                    $custom_field = entities\CustomDatatype::getByKey($request['field']);
                    if (!$custom_field instanceof entities\CustomDatatype) {
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => 'Invalid custom field']);
                    }

                    $key = $custom_field->getKey();
                    $custom_field_value = $request->getRawParameter('value');
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
            $return_details['issue'] = $issue->toJSON();

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
                case 'parent_issue_id':
                    return $issue->canAddRelatedIssues();
                case 'cover_image':
                    return $issue->canAttachFiles();
                case 'pain_bug_type':
                case 'pain_likelihood':
                case 'pain_effect':
                    return $issue->canEditUserPain();
                case 'blocking':
                    return $issue->canEditBlockerStatus();
                case 'locked':
                    return $issue->canEditAccessPolicy();
                default:
                    if ($customdatatype = entities\CustomDatatype::getByKey($field)) {
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
            if ($request['dont_show'] == 1) {
                Settings::hideInfoBox($request['key']);
            }

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
                        $comment = $target->attachFile($file);

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
            $attachmentcount = ($request['target'] == 'issue') ? $target->getNumberOfFiles() + $target->countLinks() : $target->getNumberOfFiles();

            return $this->renderJSON(['attached' => 'ok', 'container_id' => $container_id, 'files' => array_reverse(array_merge($files, $image_files)), 'attachmentcount' => $attachmentcount, 'comments' => $comments]);
        }

        public function runUploadFile(Request $request)
        {
            if (!isset($_SESSION['upload_files'])) {
                $_SESSION['upload_files'] = [];
            }

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
                    $saved_file = new entities\File();
                    $saved_file->setRealFilename($new_filename);
                    $saved_file->setOriginalFilename(basename($file['name']));
                    $saved_file->setContentType($content_type);
                    $saved_file->setType($request['type']);
                    $saved_file->setProject($request['project_id']);
                    $saved_file->setUploadedBy($this->getUser());
                    if (Settings::getUploadStorage() == 'database') {
                        $saved_file->setContent(file_get_contents($filename));
                    }
                    $saved_file->save();

                    $json = ['file' => $saved_file->toJSON()];
                    if ($request['issue_id']) {
                        $issue = Issues::getTable()->selectById($request['issue_id']);
                        if ($issue instanceof Issue && $issue->hasAccess() && $issue->canAttachFiles()) {
                            $issue->attachFile($saved_file);
                            $json['element'] = $this->getComponentHTML('main/attachedfile', ['mode' => 'issue', 'issue' => $issue, 'file' => $saved_file]);
                        }
                    } elseif ($request['article_id']) {
                        $article = tables\Articles::getTable()->selectById($request['article_id']);
                        if ($article instanceof entities\Article && $article->canEdit()) {
                            $article->attachFile($saved_file);
                            $json['element'] = $this->getComponentHTML('main/attachedfile', ['mode' => 'article', 'article' => $article, 'file' => $saved_file]);
                        }
                    } elseif ($request->hasParameter('build_id')) {
                        if ($request['build_id']) {
                            $build = tables\Builds::getTable()->selectById($request['build_id']);
                            if ($build instanceof entities\Build && $this->getUser()->canManageProjectReleases($build->getProject())) {
                                $build->attachFile($saved_file);
                            }
                        } else {
                            $build = null;
                        }
                        $json['element'] = $this->getComponentHTML('project/editbuildfile', ['build' => $build, 'file' => $saved_file]);
                    }

                    return $this->renderJSON($json);
                }
            }

            return $this->renderJSON(['error' => $this->getI18n()->__('An error occurred when uploading the file')]);
        }

        public function runDetachFile(Request $request)
        {
            try {
                $file = tables\Files::getTable()->selectById((int)$request['file_id']);
                switch ($request['mode']) {
                    case 'issue':
                        $issue = Issues::getTable()->selectById($request['issue_id']);
                        if ($issue instanceof Issue && $file instanceof entities\File && $issue->canRemoveAttachment($this->getUser(), $file)) {
                            $issue->detachFile($file);

                            return $this->renderJSON(['file_id' => $request['file_id'], 'message' => framework\Context::getI18n()->__('The attachment has been removed'), 'issue' => $issue->toJSON()]);
                        }
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('You can not remove items from this issue')]);
                    case 'article':
                        $article = tables\Articles::getTable()->selectById($request['article_id']);
                        if ($article instanceof entities\Article && $file instanceof entities\File && $article->canEdit()) {
                            $article->detachFile($file);

                            return $this->renderJSON(['file_id' => $request['file_id'], 'attachments' => count($article->getFiles()), 'message' => framework\Context::getI18n()->__('The attachment has been removed')]);
                        }
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('You can not remove items from this article')]);
                    case 'build':
                        $build = tables\Builds::getTable()->selectById($request['build_id']);
                        if ($build instanceof entities\Build && $file instanceof entities\File && $this->getUser()->canManageProjectReleases($build->getProject())) {
                            $build->detachFile($file);

                            return $this->renderJSON(['file_id' => $request['file_id'], 'attachments' => $build->getNumberOfDownloads(), 'message' => framework\Context::getI18n()->__('The attachment has been removed')]);
                        } elseif ($file->getUploadedBy()->getID() == $this->getUser()->getID()) {
                            $file->delete();
                            return $this->renderJSON(['file_id' => $request['file_id'], 'message' => framework\Context::getI18n()->__('The attachment has been removed')]);
                        }
                        $this->getResponse()->setHttpStatus(400);

                        return $this->renderJSON(['error' => framework\Context::getI18n()->__('You can not remove items from this release')]);
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
                    $comment = new entities\Comment();
                    $comment->setPostedBy($this->getUser()->getID());
                    $comment->setTargetID($request['comment_applies_id']);
                    $comment->setTargetType($request['comment_applies_type']);
                    $comment->setReplyToComment($request['reply_to_comment_id']);
                    $comment->setModuleName($request['comment_module']);

                    if (!$this->getUser()->canPostComments($comment_applies_type, $comment->getTarget()->getProject())) {
                        throw new Exception($i18n->__('You are not allowed to do this'));
                    }
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
                } elseif ($comment_applies_type == entities\Comment::TYPE_COMMIT) {
                    $commit = tables\Commits::getTable()->selectById((int)$request['comment_applies_id']);
                    framework\Event::createNew('core', 'pachno\core\entities\Comment::createNew', $comment, compact('commit'))->trigger();
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
                    case entities\Comment::TYPE_COMMIT:
                        if ($is_new) {
                            $comment_html = $this->getComponentHTML($component_name, ['comment' => $comment, 'mentionable_target_type' => 'commit', 'options' => ['commit' => $commit], 'comment_count_div' => 'commit_comment_count']);
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
            if ($field_key == 'title' || array_key_exists($field_key, entities\DatatypeBase::getAvailableFields(true))) {
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
         * Find users and show selection box
         *
         * @Route(name="find_invite_users", url="/invite/find", methods="POST")
         *
         * @param framework\Request $request The request object
         */
        public function runInviteUsers(framework\Request $request)
        {
            $this->message = false;

            $find_by = trim($request['find_by']);
            if (!$find_by) {
                return $this->renderJSON(['error' => $this->getI18n()->__('Please enter something to search for')]);
            }
            if (filter_var($find_by, FILTER_VALIDATE_EMAIL) != $find_by) {
                return $this->renderJSON(['error' => $this->getI18n()->__('Please enter a valid email address')]);
            }

            $options = [
                'users' => tables\Users::getTable()->getByDetails($find_by, 1, true)
            ];

            if (!count($options['users'])) {
                $email_user = new entities\User();
                $email_user->setEmail($find_by);
                $options['email_user'] = $email_user;
            }


            return $this->renderJSON(['content' => $this->getComponentHTML('main/invite_user', $options)]);
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
            if (!$request->isResponseFormatAccepted('application/json')) {
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
                    case 'attachlink':
                        $template_name = 'main/attachlink';
                        break;
                    case 'notifications':
                        $template_name = 'main/notifications';
                        break;
                    case 'timers':
                        $template_name = 'main/timers';
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
                        if ($request->hasParameter('board_id')) {
                            $options['board'] = tables\AgileBoards::getTable()->selectById($request['board_id']);
                            $options['milestone'] = ($request['milestone_id']) ? Milestones::getTable()->selectById($request['milestone_id']) : null;
                            $options['swimlane_identifier'] = $request['swimlane_identifier'];
                        }
                        $options['show'] = true;
                        $options['interactive'] = true;
                        $options['project'] = $this->selected_project;
                        break;
                    case 'reportissue':
                        $this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);
                        if (!$this->selected_project instanceof Project) {
                            throw new Exception($this->getI18n()->__('This project does not exist'));
                        }

                        if ($this->selected_project->isLocked()) {
                            throw new Exception($this->getI18n()->__('This project is locked'));
                        }

                        if ($this->selected_project instanceof Project && !$this->selected_project->isLocked() && $this->getUser()->canReportIssues($this->selected_project)) {
                            $template_name = 'main/reportissuecontainer';
                            $options['selected_project'] = $this->selected_project;
                            $options['selected_issuetype'] = $this->selected_issuetype;
                            $options['locked_issuetype'] = $this->locked_issuetype;
                            $options['selected_milestone'] = $this->_getMilestoneFromRequest($request);
                            $options['selected_statuses'] = $this->_getStatusesFromRequest($request);
                            $options['selected_priorities'] = $this->_getPrioritiesFromRequest($request);
                            $options['selected_severities'] = $this->_getSeveritiesFromRequest($request);
                            $options['selected_categories'] = $this->_getCategoriesFromRequest($request);
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
                    case 'issue_estimate':
                        $template_name = 'main/issueestimate';
                        break;
                    case 'issue_spenttime':
                        $template_name = 'main/issuespenttime';
                        $options['entry_id'] = $request->getParameter('entry_id');
                        break;
                    case 'relate_issue':
                        $template_name = 'main/relateissue';
                        break;
                    case 'project_build':
                        $template_name = 'project/editbuild';
                        $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        if ($request->getParameter('build_id')) {
                            $build = tables\Builds::getTable()->selectById($request['build_id']);
                        } else {
                            $build = new entities\Build();
                            $build->setProject($options['project']);
                        }
                        $options['build'] = $build;
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
                        $options['invite'] = ($request->getParameter('invite') == 1);
                        break;
                    case 'invite_users':
                        $template_name = 'main/inviteusers';
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
                            if ($request->hasParameter('project_id')) {
                                $role->setProject($request['project_id']);
                            }
                        }
                        $options['role'] = $role;
                        break;
                    case 'edit_group':
                        $template_name = 'configuration/editgroup';
                        if ($request['group_id']) {
                            $group = tables\Groups::getTable()->selectById($request['group_id']);
                        } else {
                            $group = new entities\Group();
                        }
                        $options['group'] = $group;
                        break;
                    case 'edit_team':
                        $template_name = 'configuration/editteam';
                        if ($request['team_id']) {
                            $team = tables\Teams::getTable()->selectById($request['team_id']);
                        } else {
                            $team = new entities\Team();
                        }
                        $options['team'] = $team;
                        break;
                    case 'edit_client':
                        $template_name = 'configuration/editclient';
                        if ($request['client_id']) {
                            $client = tables\Clients::getTable()->selectById($request['client_id']);
                        } else {
                            $client = new entities\Client();
                        }
                        $options['client'] = $client;
                        break;
                    case 'client_add_member':
                        $template_name = 'configuration/clientaddmembers';
                        $options['client'] = tables\Clients::getTable()->selectById($request['client_id']);
                        break;
                    case 'edit_workflow_scheme':
                        $template_name = 'configuration/editworkflowscheme';
                        if ($request['scheme_id']) {
                            $scheme = tables\WorkflowSchemes::getTable()->selectById($request['scheme_id']);
                        } else {
                            $scheme = new entities\WorkflowScheme();
                        }
                        if ($request->hasParameter('clone')) {
                            $options['clone'] = true;
                        }
                        $options['scheme'] = $scheme;
                        break;
                    case 'edit_workflow_step':
                        $template_name = 'configuration/editworkflowstep';
                        if ($request['step_id']) {
                            $step = tables\WorkflowSteps::getTable()->selectById($request['step_id']);
                        } else {
                            $step = new entities\WorkflowStep();
                            $step->setWorkflowId($request['workflow_id']);
                        }
                        if ($request->hasParameter('clone')) {
                            $options['clone'] = true;
                        }
                        $options['step'] = $step;
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
                            if (array_key_exists($type, entities\DatatypeBase::getAvailableFields(true))) {
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
                        $options['dashboard_id'] = $request['dashboard_id'];
                        $options['target_type'] = $request['target_type'];
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
                    case 'viewissue':
                        $template_name = 'project/viewissuecard';
                        if ($request->hasParameter('board_id')) {
                            $options['board'] = tables\AgileBoards::getTable()->selectById($request['board_id']);
                        }
                        break;
                    case 'userscopes':
                        if (!framework\Context::getScope()->isDefault())
                            throw new Exception($this->getI18n()->__('This is not allowed outside the default scope'));

                        $template_name = 'configuration/userscopes';
                        $options['user'] = new entities\User((int)$request['user_id']);
                        break;
                    case 'milestone':
                        $template_name = 'project/editmilestone';
                        $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        if ($request->hasParameter('milestone_id')) {
                            $options['milestone'] = Milestones::getTable()->selectById($request['milestone_id']);
                        }
                        break;
                    default:
                        $event = new framework\Event('core', 'get_backdrop_partial', $request['key'], ['request' => $request]);
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

        /**
         * @Route(name="viewissue_remove_parent_issue", url="/:project_key/issues/:issue_id/parent_issue/:csrf_token", methods="DELETE")
         * @CsrfProtected

         * @param Request $request
         * @return framework\JsonOutput
         */
        public function runRemoveParentIssue(Request $request): framework\JsonOutput
        {
            try {
                $issue = Issues::getTable()->selectById($request['issue_id']);
                if (!$issue instanceof Issue) {
                    throw new Exception('This issue does not exist');
                }

                $json_issues = [];

                foreach ($issue->getParentIssues() as $parent_issue) {
                    $issue->removeDependantIssue($parent_issue);
                    tables\IssueRelations::getTable()->clearRelationCache();
                    $json_issues[] = $parent_issue->toJSON();
                }

                $issue->clearCachedItems();
                tables\IssueRelations::getTable()->clearRelationCache();
                $json_issues[] = $issue->toJSON();

                return $this->renderJSON([
                    'message' => $this->getI18n()->__('The issues are no longer related'),
                    'issues' => $json_issues
                ]);
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
            $json_issues = [];
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
                        $json_issues[] = $related_issue->toJSON();
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
                $json_issues[] = $issue->toJSON();
                return $this->renderJSON([
                    'content' => $content,
                    'issues' => $json_issues,
                    'message' => ($cc > 1) ? framework\Context::getI18n()->__('The related issues were added') : framework\Context::getI18n()->__('The related issue was added')
                ]);
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

        public function runRemoveAffected(Request $request)
        {
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

                return $this->renderJSON(['message' => $message, 'issue' => $issue->toJSON()]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__('An internal error has occured')]);
            }
        }

        public function runAddAffected(Request $request)
        {
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
                            $content = $this->getComponentHTML('main/affecteditem', ['item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses]);
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
                            $content = $this->getComponentHTML('main/affecteditem', ['item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses]);
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
                            $content = $this->getComponentHTML('main/affecteditem', ['item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses]);
                        }

                        $message = framework\Context::getI18n()->__('Release <b>%build</b> is now affected by this issue', ['%build' => $build->getName()], true);

                        break;
                    default:
                        throw new Exception('Internal error');
                }

                return $this->renderJSON([
                    'content' => $content,
                    'issue' => $issue->toJSON(),
                    'message' => $message
                ]);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => $e->getMessage()]);
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
                $this->forward($this->getRouting()->generate('profile_account'));
            }

            framework\Context::setMessage('error', $this->getI18n()->__('Could not pick the username "%username"', ['%username' => $request['selected_username']]));
            $this->forward($this->getRouting()->generate('profile_account'));
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

        /**
         * @Route(name="issue_load_dynamic_choices", url="/:project_key/choices/:issue_id")
         *
         * @param Request $request
         */
        public function runIssueLoadChoices(Request $request)
        {
            $issue = Issues::getTable()->getIssueById((int)$request['issue_id']);
            if ($issue instanceof Issue) {
                $json = $issue->getChoiceValues($request['field']);

                return $this->renderJSON(['data' => $json]);
            }
        }

        public function runIssueMoreactions(Request $request)
        {
            try {
                $issue = Issues::getTable()->getIssueById((int)$request['issue_id']);
                if ($request['board_id']) $board = tables\AgileBoards::getTable()->selectById((int)$request['board_id']);

                $times = (!isset($board) || $board->getType() != entities\AgileBoard::TYPE_KANBAN);
                $estimator_mode = isset($request['estimator_mode']) ? $request['estimator_mode'] : null;
                $options = compact('issue', 'times', 'estimator_mode');
                if (isset($board)) {
                    $options['board'] = $board;
                }

                return $this->renderJSON(['menu' => $this->getComponentHTML('main/issuemoreactions', $options)]);
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
         * Milestone actions
         *
         * @param Request $request
         */
        public function runMilestone(Request $request)
        {
            $milestone_id = ($request['milestone_id']) ? $request['milestone_id'] : null;
            $milestone = new entities\Milestone($milestone_id);
            $action_option = str_replace($this->selected_project->getKey() . '/milestone/' . $request['milestone_id'] . '/', '', $request['url']);

            try {
                if (!$this->getUser()->canManageProjectReleases($this->selected_project))
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
                            $component = $this->getComponentHTML('project/milestone', ['milestone' => $milestone, 'include_counts' => true]);
                        }
                        $message = framework\Context::getI18n()->__('Milestone saved');

                        return $this->renderJSON(['message' => $message, 'component' => $component, 'milestone' => $milestone->toJSON(false)]);
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
