<?php

    namespace pachno\core\modules\livelink;

    use DateTime;
    use Exception;
    use pachno\core\entities\Branch;
    use pachno\core\entities\BranchCommit;
    use pachno\core\entities\Commit;
    use pachno\core\entities\CommitFile;
    use pachno\core\entities\CommitFileDiff;
    use pachno\core\entities\Issue;
    use pachno\core\entities\IssueCommit;
    use pachno\core\entities\LivelinkImport;
    use pachno\core\entities\Permission;
    use pachno\core\entities\Priority;
    use pachno\core\entities\Project;
    use pachno\core\entities\Resolution;
    use pachno\core\entities\Status;
    use pachno\core\entities\tables\BranchCommits;
    use pachno\core\entities\tables\Commits;
    use pachno\core\entities\tables\IssueCommits;
    use pachno\core\entities\tables\LivelinkImports;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\tables\Projects;
    use pachno\core\entities\tables\Users;
    use pachno\core\entities\User;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use Ramsey\Uuid\Uuid;

    /**
     * Pachno Live Link module
     *
     * @package livelink
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Modules")
     */
    class Livelink extends framework\CoreModule
    {

        public const SETTINGS_WORKFLOW_ACTIONS = 'workflow_actions_';

        public const SETTINGS_PROJECT_CONNECTOR = 'connector_project_';

        public const SETTINGS_PROJECT_LIVELINK_ENABLED = 'connector_livelink_enabled_project_';

        public const SETTINGS_PROJECT_CONNECTOR_SECRET = 'connector_secret_project_';

        public const NOTIFICATION_COMMIT_MENTIONED = 'commit_mentioned';

        /**
         * @var ConnectorProvider[]
         */
        protected $_connectors = [];

        protected $long_name = 'External accounts';

        public function hasAccountSettings()
        {
            return true;
        }

        public function getAccountSettingsLogo()
        {
            return 'laptop-code';
        }

        public function getAccountSettingsName()
        {
            return 'External accounts';
        }

        /**
         * @Listener(module='core', identifier='get_backdrop_partial')
         * @param Event $event
         */
        public function listen_get_backdrop_partial(Event $event)
        {
            $request = framework\Context::getRequest();
            $connector = $request['connector'];
            $connector_module = $this->getConnectorModule($connector);
            $options = ['connector' => $this->getConnector($connector)];

            if ($this->hasConnector($connector)) {
                switch ($event->getSubject()) {
                    case 'livelink-import_project':
                        $template_name = $connector_module->getName() . "/import_project";
                        $options['project'] = null;
                        if ($request['project_id']) {
                            $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        }

                        if (!$options['project'] instanceof Project) {
                            $options['project'] = new Project();
                        }

                        break;
                    case 'livelink-configure_connector':
                        $template_name = $connector_module->getName() . "/configureconnector";
                        break;
                    default:
                        return;
                }

                foreach ($options as $key => $value) {
                    $event->addToReturnList($value, $key);
                }
                $event->setReturnValue($template_name);
                $event->setProcessed();
            }
        }

        /**
         * @param $key
         *
         * @return ConnectorProvider
         */
        public function getConnectorModule($key)
        {
            return (isset($this->_connectors[$key])) ? $this->_connectors[$key] : null;
        }

        /**
         * @param $key
         *
         * @return BaseConnector
         */
        public function getConnector($key)
        {
            return (isset($this->_connectors[$key])) ? $this->_connectors[$key]->getConnector() : null;
        }

        public function hasConnector($key)
        {
            return array_key_exists($key, $this->_connectors);
        }

        /**
         * @Listener(module='core', identifier='project/templates/projectheader/namelabel')
         * @param Event $event
         */
        public function listen_projectHeaderNameLabel(Event $event)
        {
            /** @var Project $project */
            $project = $event->getSubject();

            $project_connector = $this->getSetting(self::SETTINGS_PROJECT_CONNECTOR . $project->getID());
            if ($project_connector && $this->isLiveLinkEnabledForProject($project)) {
                include_component($this->getConnectorModule($project_connector)->getName() . '/projectbadge', ['project' => $project]);
            }
        }

        /**
         * @param Project $project
         *
         * @return bool
         */
        public function isLiveLinkEnabledForProject(Project $project)
        {
            $setting = $this->getSetting(Livelink::SETTINGS_PROJECT_LIVELINK_ENABLED . $project->getID());

            return (bool)$setting;
        }

        /**
         * @Listener(module='core', identifier='projectActions::configureProjectSettings::postSave')
         * @param Event $event
         */
        public function listen_projectSettingsPostSave(Event $event)
        {
            if (!$this->isEnabled()) {
                return;
            }

            /** @var Project $project */
            $project = $event->getSubject();
            /** @var framework\Request $request */
            $request = $event->getParameter('request');

            $this->saveProjectLiveLinkSettings($project, $request);
        }

        public function saveProjectLiveLinkSettings(Project $project, framework\Request $request)
        {
            $connector = $this->getConnectorModule($request['connector']);

            if ($connector instanceof ConnectorProvider) {
                $secret = Uuid::uuid4()->toString();
                $connector->saveProjectConnectorSettings($request, $project, $secret);
                $this->saveSetting(self::SETTINGS_PROJECT_CONNECTOR . $project->getID(), $request['connector']);
                $this->saveSetting(self::SETTINGS_PROJECT_CONNECTOR_SECRET . $project->getID(), $secret);
            }
        }

        public function removeProjectLiveLinkSettings(Project $project)
        {
            $connector_key = $this->getProjectConnector($project);
            $connector = $this->getConnectorModule($connector_key);

            if ($connector instanceof ConnectorProvider) {
                $secret = $this->getProjectSecret($project);
                $connector->removeProjectConnectorSettings($project, $secret);
                $this->deleteSetting(self::SETTINGS_PROJECT_CONNECTOR . $project->getID());
                $this->deleteSetting(self::SETTINGS_PROJECT_CONNECTOR_SECRET . $project->getID());
            }
        }

        public function getProjectConnector(Project $project)
        {
            return $this->getSetting(self::SETTINGS_PROJECT_CONNECTOR . $project->getID());
        }

        public function getProjectSecret(Project $project)
        {
            return $this->getSetting(self::SETTINGS_PROJECT_CONNECTOR_SECRET . $project->getID());
        }

        /**
         * @Listener(module='core', identifier='project/editproject::sidebar_tabs')
         * @param Event $event
         */
        public function listen_editProjectTabs(Event $event)
        {
            if (!$this->isEnabled()) {
                return;
            }

            $selected = $this->hasConnectors();
            $event->setReturnValue(!$selected);

            include_component('livelink/editproject_tab', ['selected' => $selected]);
        }

        /**
         * @Listener(module='core', identifier='project/editproject::above_content')
         * @param Event $event
         */
        public function listen_project_template(Event $event)
        {
            if (!$this->isEnabled()) {
                return;
            }

            $request = framework\Context::getRequest();
            $options = [
                'project' => $event->getParameter('project'),
                'module' => $this,
                'partial_options' => $this->getCurrentPartialOptions()
            ];

            if ($request->hasParameter('connector')) {
                $options['connector'] = $this->getModule()->getConnectorModule($request['connector']);
                $options['display_name'] = $options['connector']->getImportDisplayNameForProjectEdit($request);
                $options['input'] = $options['connector']->getInputOptionsForProjectEdit($request);
                $event->getParameter('project')->setName($options['connector']->getImportProjectNameForProjectEdit($request));
            } elseif ($this->hasConnectors()) {
                $event->setReturnValue('livelink');
            }

            include_component('livelink/projectconfig_template', $options);
        }

        public function getCurrentPartialOptions()
        {
            $partial_options = ['key' => 'project_config'];
            $request = framework\Context::getRequest();

            if ($request->hasParameter('assignee_type')) {
                $partial_options['assignee_type'] = $request['assignee_type'];
            }
            if ($request->hasParameter('assignee_id')) {
                $partial_options['assignee_id'] = $request['assignee_id'];
            }

            return $partial_options;
        }

        /**
         * Return an instance of this module
         *
         * @return Livelink
         */
        public static function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        /**
         * @Listener(module='core', identifier='project/editproject::additional_form_elements')
         * @param Event $event
         */
        public function listen_project_template_additional_form_elements(Event $event)
        {
            if (!$this->isEnabled()) {
                return;
            }

            $request = framework\Context::getRequest();
            if ($request->hasParameter('connector')) {
                $options = [
                    'project' => $event->getParameter('project'),
                    'module' => $this,
                ];

                $options['connector'] = $this->getModule()->getConnectorModule($request['connector']);
                $options['inputs'] = $options['connector']->getInputOptionsForProjectEdit($request);

                include_component('livelink/projectconfig_template_additional_form_elements', $options);
            }

        }

        /**
         * @Listener(module='core', identifier='viewissue_before_tabs')
         * @param Event $event
         */
        public function listen_viewissue_panel_tab(Event $event)
        {
            if (!$this->getProjectConnector($event->getSubject()->getProject()))
                return;

            $commits_count = IssueCommits::getTable()->countByIssueID($event->getSubject()->getID());
            include_component('livelink/viewissue_activities_tab', ['count' => $commits_count]);
        }

        /**
         * @Listener(module='core', identifier='viewissue_after_tabs')
         * @param Event $event
         */
        public function listen_viewissue_panel(Event $event)
        {
            if (!$this->getProjectConnector($event->getSubject()->getProject()))
                return;

            $commits = IssueCommits::getTable()->getByIssueID($event->getSubject()->getID());
            $commits_count = IssueCommits::getTable()->countByIssueID($event->getSubject()->getID());
            include_component('livelink/viewissue_commits', ['issue' => $event->getSubject(), 'commits' => $commits, 'commits_count' => $commits_count, 'selected_project' => $event->getSubject()->getProject()]);
        }

        /**
         * @Listener(module='core', identifier='config_project_tabs_other')
         * @param Event $event
         */
        public function listen_projectconfig_tab(Event $event)
        {
            if (!$this->isEnabled()) {
                return;
            }

            include_component('livelink/projectconfig_tab', ['selected_tab' => $event->getParameter('selected_tab')]);
        }

        /**
         * Header wiki menu and search dropdown / list
         *
         * @Listener(module="core", identifier="templates/header::projectmenulinks")
         *
         * @param Event $event
         */
        public function listen_MenustripLinks(Event $event)
        {
            if (framework\Context::getUser()->hasProjectPermission(Permission::PERMISSION_PROJECT_ACCESS_CODE, $event->getSubject())) {
                framework\ActionComponent::includeComponent('livelink/menustriplinks', ['project' => $event->getSubject()]);
            }
        }

        /**
         * @Listener(module='core', identifier='config_project_panes')
         * @param Event $event
         */
        public function listen_projectconfig_panel(Event $event)
        {
            if (!$this->isEnabled()) {
                return;
            }

            $options = [
                'selected_tab' => $event->getParameter('selected_tab'),
                'access_level' => $event->getParameter('access_level'),
                'project' => $event->getParameter('project'),
                'connector' => $event->getParameter('connector')
            ];

            include_component('livelink/projectconfig_panel', $options);
        }

        public function postConnectorSettings(framework\Request $request)
        {

            return true;
        }

        public function addConnector($key, ConnectorProvider $connector)
        {
            $this->_connectors[$key] = $connector;
        }

        /**
         * @return ConnectorProvider[]
         */
        public function getConnectorModules()
        {
            return $this->_connectors;
        }

        public function hasConnectors()
        {
            return (bool)count($this->_connectors);
        }

        /**
         * @param Project $project
         * @param Branch $branch
         * @param $message
         * @param $author
         * @param DateTime $date
         * @param $previous_hash
         * @param $current_hash
         * @param $changes
         * @param $update_branch
         * @param array $additional_data
         *
         * @return Commit
         */
        public function processCommit(Project $project, Branch $branch, $message, $author, DateTime $date, $previous_hash, $current_hash, $changes, $update_branch, $additional_data = [], $force = false)
        {
            if ($project->isArchived())
                return;

            if (Commits::getTable()->isProjectCommitProcessed($current_hash, $project->getID())) {
                if (!$force) {
                    return;
                } else {
                    $commit = Commits::getTable()->getCommitByHash($current_hash, $project);
                }
            }

            framework\Context::setCurrentProject($project);

            // Parse the commit message, and obtain the issues and transitions for issues.
            $parsed_commit = Issue::getIssuesFromTextByRegex($message);
            /** @var Issue[] $issues */
            $issues = $parsed_commit["issues"];
            $transitions = $parsed_commit["transitions"];
            $workflow_actions_enabled = $this->isWorkflowActionsEnabledForProject($project);
            $user = $this->getUserByCommitAuthor($author);

            framework\Context::switchUserContext($user);

            framework\Logging::log('[' . $project->getKey() . '] Commit to be logged by user ' . $user->getName(), $this->getName());

            // Create the commit data
            if (!isset($commit)) {
                $commit = new Commit();
            }
            $commit->setAuthor($user);
            $commit->setLog($message);
            if ($previous_hash) {
                $commit->setPreviousRevision($previous_hash);
                $previous_commit = Commits::getTable()->getCommitByHash($previous_hash, $project);
                if ($previous_commit instanceof Commit) {
                    $commit->setPreviousCommit($previous_commit);
                }
            }
            $commit->setRevision($current_hash);
            $commit->setProject($project);
            $commit->setDate($date->getTimestamp());

            if (!empty($additional_data)) {
                $commit->setMiscData($additional_data);
            }

            $commit->save();

            if ($branch->getID() && !BranchCommits::getTable()->hasBranchCommitSha($branch, $current_hash)) {
                $branchCommit = new BranchCommit();
                $branchCommit->setCommit($commit);
                $branchCommit->setBranch($branch);
                $branchCommit->save();
            }

            if ($update_branch) {
                $branch->setLatestCommit($commit);
                $branch->save();
            }

            framework\Logging::log('[' . $project->getKey() . '] Commit logged with revision ' . $commit->getRevision(), $this->getName());

            // Iterate over affected issues and update them.
            foreach ($issues as $issue) {
                $commit_file = new IssueCommit();
                $commit_file->setIssue($issue);
                $commit_file->setCommit($commit);
                $commit_file->save();

                if ($workflow_actions_enabled) {
                    // Process all commit-message transitions for an issue.
                    foreach ($transitions[$issue->getFormattedIssueNo()] as $transition) {
                        if ($this->getSetting(self::SETTINGS_WORKFLOW_ACTIONS . $project->getID(), 'vcs_integration')) {
                            $this->applyWorkflowTransition($issue, $transition);
                        }
                    }
                }

                $issue->addSystemComment(framework\Context::getI18n()->__('This issue has been updated with the latest changes from the code repository.%commit_msg', ['%commit_msg' => '<div class="commit_main">' . $message . '</div>']), $user->getID(), 'vcs_integration');
                framework\Logging::log('[' . $project->getKey() . '] Updated issue ' . $issue->getFormattedIssueNo(), $this->getName());
            }

            // Create file links
            foreach ($changes as $change) {
                $commit_file = new CommitFile();
                $commit_file->setAction($change['action']);
                $commit_file->setPath($change['filename']);
                $commit_file->setCommit($commit);
                if (isset($change['previous_filename'])) {
                    $commit_file->setData([
                        'previous_filename' => $change['previous_filename']
                    ]);
                }
                $commit_file->save();

                framework\Logging::log('[' . $project->getKey() . '] Added with action ' . $change['action'] . ' file ' . $change['filename'], $this->getName());

                if (isset($change['diff'])) {
                    $diffs = preg_split('/^@@/m', $change['diff']);

//                    var_dump($change['action']);
                    foreach ($diffs as $single_diff) {
                        if (trim($single_diff)) {
                            try {
                                $diff = new CommitFileDiff();
                                $diff->setCommitFile($commit_file);
                                $diff->setDiff('@@' . $single_diff);
                                $diff->save();
                            } catch (Exception $e) {
                                $diff->setDiff('');
                                $diff->save();
                            }
                        }
                    }
                }
            }
//            die();

            Event::createNew('livelink', 'commit', $commit)->trigger();

            return $commit;
        }

        /**
         * @param Project $project
         *
         * @return bool
         */
        public function isWorkflowActionsEnabledForProject(Project $project)
        {
            return (bool)$this->getSetting(self::SETTINGS_WORKFLOW_ACTIONS . $project->getID());
        }

        /**
         * Find author of commit, fallback is guest
         * Some VCSes use a different format of storing the committer's name. Systems like bzr, git and hg use the format
         * Joe Bloggs <me@example.com>, instead of a classic username. Therefore a user will be found via 4 queries:
         * a) First we extract the email if there is one, and find a user with that email
         * b) If one is not found - or if no email was specified, then instead test against the real name (using the name part if there was an email)
         * c) the username or full name is checked against the friendly name field
         * d) and if we still havent found one, then we check against the username
         * e) and if we STILL havent found one, we use the guest user
         *
         * @param string $author
         *
         * @return User
         */
        public function getUserByCommitAuthor($author)
        {
            $user = Users::getTable()->getByEmail($author);

            if (!$user instanceof User && preg_match("/(?<=<)(.*)(?=>)/", $author, $matches)) {
                $email = $matches[0];

                // a2)
                $user = Users::getTable()->getByEmail($email);

                if (!$user instanceof User) {
                    // Not found by email
                    preg_match("/(?<=^)(.*)(?= <)/", $author, $matches);
                    $author = $matches[0];
                }
            }

            // b)
            if (!$user instanceof User)
                $user = Users::getTable()->getByRealname($author);

            // c)
            if (!$user instanceof User)
                $user = Users::getTable()->getByBuddyname($author);

            // d)
            if (!$user instanceof User)
                $user = Users::getTable()->getByUsername($author);

            // e)
            if (!$user instanceof User)
                $user = framework\Settings::getDefaultUser();

            return $user;
        }

        /**
         * @param Issue $issue
         * @param $transition
         */
        public function applyWorkflowTransition(Issue $issue, $transition)
        {
            if ($issue->isWorkflowTransitionsAvailable()) {
                // Go through the list of possible transitions for an issue. Only
                // process transitions that are applicable to issue's workflow.
                foreach ($issue->getAvailableWorkflowTransitions() as $possible_transition) {
                    if (mb_strtolower($possible_transition->getName()) == mb_strtolower($transition[0])) {
                        framework\Logging::log('[' . $issue->getProject()->getKey() . '] Running transition ' . $transition[0] . ' on issue ' . $issue->getFormattedIssueNo(), $this->getName());
                        // String representation of parameters. Used for log message.
                        $parameters_string = "";

                        // Iterate over the list of this transition's parameters, and
                        // set them.
                        foreach ($transition[1] as $parameter => $value) {
                            $parameters_string .= "$parameter=$value ";

                            switch ($parameter) {
                                case 'resolution':
                                    if (($resolution = Resolution::getByKeyish($value)) instanceof Resolution) {
                                        framework\Context::getRequest()->setParameter('resolution_id', $resolution->getID());
                                    }
                                    break;
                                case 'status':
                                    if (($status = Status::getByKeyish($value)) instanceof Status) {
                                        framework\Context::getRequest()->setParameter('status_id', $status->getID());
                                    }
                                    break;
                                case 'priority':
                                    if (($priority = Priority::getByKeyish($value)) instanceof Priority) {
                                        framework\Context::getRequest()->setParameter('priority_id', $priority->getID());
                                    }
                                    break;
                            }
                        }

                        // Run the transition.
                        $possible_transition->transitionIssueToOutgoingStepFromRequest($issue, framework\Context::getRequest());

                        framework\Logging::log('[' . $issue->getProject()->getKey() . '] Ran transition ' . $possible_transition->getName() . ' with parameters \'' . $parameters_string . '\' on issue ' . $issue->getFormattedIssueNo(), $this->getName());
                    }
                }
            }
        }

        public function performImport(LivelinkImport $import)
        {
            $project = $import->getProject();
            $user = $import->getUser();
            $connector = $this->getProjectConnector($project);
            $module = $this->getConnectorModule($connector);

            if ($module instanceof ConnectorProvider) {
                return $this->getConnectorModule($connector)->importProject($project, $user);
            }
        }

        public function setLiveLinkEnabledForProject(Project $project, $enabled = true)
        {
            if ($enabled) {
                $this->saveSetting(Livelink::SETTINGS_PROJECT_LIVELINK_ENABLED . $project->getID(), true);
            } else {
                $this->deleteSetting(Livelink::SETTINGS_PROJECT_LIVELINK_ENABLED . $project->getID());
            }
        }

        public function isProjectImportInProgress(Project $project)
        {
            return LivelinkImports::getTable()->hasPendingByProject($project);
        }

        /**
         * Listen to header menu strip
         *
         * @Listener(module="core", identifier="header_menu_strip")
         *
         * @param Event $event
         */
        public function listenerMainMenustrip(Event $event)
        {
            $route = $event->getSubject();

            if (!$route instanceof framework\routing\Route)
                return;

            if ($route->getModuleName() == 'livelink' && framework\Context::getCurrentProject() instanceof Project) {
                $component = framework\Action::returnComponentHTML('project/projectheader');
                $event->setReturnValue($component);
                $event->setProcessed();
            }
        }

    }
