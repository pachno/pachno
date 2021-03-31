<?php

    namespace pachno\core\modules\livelink\controllers;

    use Exception;
    use pachno\core\entities\Project;
    use pachno\core\entities\tables\CommitFiles;
    use pachno\core\entities\tables\Commits;
    use pachno\core\entities\tables\IssueCommits;
    use pachno\core\entities\tables\Projects;
    use pachno\core\framework;
    use pachno\core\modules\livelink\Livelink;

    /**
     * Main controller for the livelink module
     */
    class Main extends framework\Action
    {

        public function getAuthenticationMethodForAction($action)
        {
            switch ($action) {
                case 'webhook':
                    return framework\Action::AUTHENTICATION_METHOD_DUMMY;
                default:
                    return framework\Action::AUTHENTICATION_METHOD_CORE;
            }
        }

        /**
         * @Route(name="livelink_webhook", url="/livelink/hooks/:project_id/:secret")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runWebhook(framework\Request $request)
        {
            Commits::getTable()->create();
            IssueCommits::getTable()->create();
            CommitFiles::getTable()->create();
            $project = Projects::getTable()->selectById($request['project_id']);

            if (!$project instanceof Project) {
                $this->getResponse()->setHttpStatus(404);

                return $this->renderJSON(['error' => 'Project not found']);
            }

            $secret = $request['secret'];

            if ($secret != $this->getModule()->getProjectSecret($project)) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => 'Invalid secret']);
            }

            $connector = $this->getModule()->getProjectConnector($project);

            return $this->getModule()->getConnectorModule($connector)->webhook($request, $project);
        }

        /**
         * @return Livelink
         */
        protected function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        /**
         * @Route(name="get_project_connector_template", url="/livelink/project/:project_id", methods="GET")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runGetProjectConnectorTemplate(framework\Request $request)
        {
            $project = Projects::getTable()->selectById($request['project_id']);
            if (!$project instanceof Project) {
                throw new Exception('Invalid project id');
            }

            $options = [
                'selected_tab' => 'livelink',
                'access_level' => framework\Settings::getConfigurationAccessLevel(framework\Settings::CONFIGURATION_SECTION_PROJECTS),
                'project' => $project,
                'connector' => $this->getModule()->getProjectConnector($project)
            ];

            return $this->renderComponent('livelink/projectconfig_panel', $options);
        }

        /**
         * @Route(name="livelink_remove_project_connector", url="/livelink/project/:project_id", methods="POST")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runRemoveProjectLivelinkConnector(framework\Request $request)
        {
            $project = Projects::getTable()->selectById($request['project_id']);
            if (!$project instanceof Project) {
                throw new Exception('Invalid project id');
            }

            try {
                $this->getModule()->removeProjectLiveLinkSettings($project);

                return $this->renderJSON(['removed' => 'ok']);
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__($e->getMessage())]);
            }
        }

        /**
         * @Route(name="configure_livelink_connector", url="/livelink/connector/:connector")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runConfigureLivelinkConnector(framework\Request $request)
        {
            $connector = $request['connector'];
            try {
                $livelink = $this->getModule();
                $connector_module = $livelink->getConnectorModule($connector);

                return $this->renderJSON($connector_module->postConnectorSettings($request));
            } catch (Exception $e) {
                throw $e;
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__($e->getMessage())]);
            }
        }

        /**
         * @Route(name="disconnect_livelink_connector", url="/livelink/disconnect")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runDisconnectLivelinkConnector(framework\Request $request)
        {
            $connector = $request['connector'];
            try {
                $livelink = $this->getModule();
                $connector_module = $livelink->getConnectorModule($connector);

                return $this->renderJSON($connector_module->removeConnectorSettings($request));
            } catch (Exception $e) {
                $this->getResponse()->setHttpStatus(400);

                return $this->renderJSON(['error' => framework\Context::getI18n()->__($e->getMessage())]);
            }
        }

    }

