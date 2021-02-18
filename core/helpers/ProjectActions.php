<?php

    namespace pachno\core\helpers;

    use Exception;
    use pachno\core\entities;
    use pachno\core\entities\tables;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use pachno\core\framework\Request;

    /**
     * actions for the project module
     *
     * @property entities\Project $selected_project
     */
    class ProjectActions extends framework\Action
    {

        protected $anonymous_project_routes = [];

        /**
         * Pre-execute function
         *
         * @param Request $request
         * @param string $action
         */
        public function preExecute(Request $request, $action)
        {
            $project_id = $request['project_id'];
            $project_key = $request['project_key'];

            if (!$project_id && !$project_key && in_array($action, $this->anonymous_project_routes)) {
                $this->selected_project = new entities\Project();

            } else {
                try {
                    if ($project_id)
                        $this->selected_project = entities\Project::getB2DBTable()->selectById($project_id);
                    elseif ($project_key)
                        $this->selected_project = entities\Project::getByKey($project_key);
                } catch (Exception $e) {
                }
            }

            if (!$this->selected_project instanceof entities\Project)
                return $this->return404(framework\Context::getI18n()->__('This project does not exist'));

            framework\Context::setCurrentProject($this->selected_project);
            $this->project_key = $this->selected_project->getKey();
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

            if ($issue instanceof entities\Issue) {
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

                framework\Context::getUser()->setNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), false)->save();

                Event::createNew('core', 'viewissue', $issue)->trigger();
            }

            $message = framework\Context::getMessageAndClear('issue_saved');
            $uploaded = framework\Context::getMessageAndClear('issue_file_uploaded');

            if (framework\Context::hasMessage('issue_deleted_shown') && (is_null($issue) || ($issue instanceof entities\Issue && $issue->isDeleted()))) {
                $request_referer = ($request['referer'] ?: (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null));

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
            $event = Event::createNew('core', 'viewissue', $issue)->trigger();
            $this->listenViewIssuePostError($event);
        }

        protected function _getIssueFromRequest(Request $request)
        {
            $issue = null;
            if ($issue_no = framework\Context::getRequest()->getParameter('issue_no')) {
                $issue = entities\Issue::getIssueFromLink($issue_no);
            } elseif ($issue_id = framework\Context::getRequest()->getParameter('issue_id')) {
                $issue = entities\tables\Issues::getTable()->selectById($request['issue_id']);
            }

            if ($issue instanceof entities\Issue) {
                if (!$this->selected_project instanceof entities\Project || $issue->getProjectID() != $this->selected_project->getID()) {
                    $issue = null;
                }
            } else {
                framework\Logging::log("Issue no [$issue_no] not a valid issue no", 'main', framework\Logging::LEVEL_WARNING_RISK);
            }

            framework\Logging::log('done (Loading issue)');
            if ($issue instanceof entities\Issue && (!$issue->hasAccess() || $issue->isDeleted()))
                $issue = null;

            return $issue;
        }

        public function listenViewIssuePostError(Event $event)
        {
            if (framework\Context::hasMessage('comment_error')) {
                $this->comment_error = true;
                $this->error = framework\Context::getMessageAndClear('comment_error');
                $this->comment_error_body = framework\Context::getMessageAndClear('comment_error_body');
            }
        }

        /**
         * Go to the next/previous open issue
         *
         * @param Request $request
         */
        public function runNavigateIssue(Request $request)
        {
            $issue = $this->_getIssueFromRequest($request);

            if (!$issue instanceof entities\Issue) {
                $this->getResponse()->setTemplate('viewissue');

                return;
            }

            do {
                if ($issue->getMilestone() instanceof entities\Milestone) {
                    if ($request['direction'] == 'next') {
                        $found_issue = tables\Issues::getTable()->getNextIssueFromIssueMilestoneOrderAndMilestoneID($issue->getMilestoneOrder(), $issue->getMilestone()->getID(), $request['mode'] == 'open');
                    } else {
                        $found_issue = tables\Issues::getTable()->getPreviousIssueFromIssueMilestoneOrderAndMilestoneID($issue->getMilestoneOrder(), $issue->getMilestone()->getID(), $request['mode'] == 'open');
                    }
                } else {
                    if ($request['direction'] == 'next') {
                        $found_issue = tables\Issues::getTable()->getNextIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
                    } else {
                        $found_issue = tables\Issues::getTable()->getPreviousIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
                    }
                }
                if (is_null($found_issue))
                    break;
            } while ($found_issue instanceof entities\Issue && !$found_issue->hasAccess());

            if ($found_issue instanceof entities\Issue) {
                $this->forward($found_issue->getUrl());
            } else {
                framework\Context::setMessage('issue_message', $this->getI18n()->__('There are no more issues in that direction.'));
                $this->forward($issue->getUrl());
            }
        }

        protected function _checkProjectPageAccess($page)
        {
            return framework\Context::getUser()->hasProjectPageAccess($page, $this->selected_project);
        }


    }
