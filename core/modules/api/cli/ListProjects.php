<?php

    namespace pachno\core\modules\api\cli;

    /**
     * CLI command class, api -> list_projects
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * CLI command class, api -> list_projects
     *
     * @package pachno
     * @subpackage core
     */
    class ListProjects extends \pachno\core\modules\api\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'list_projects';
            $this->_description = "Query a remote server for a list of available projects";
            parent::_setup();
        }

        public function do_execute()
        {
            $this->cliEcho('Querying ');
            $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
            $this->cliEcho(" for list of projects ...\n\n");

            $response = $this->getRemoteResponse($this->getRemoteURL('api_projects_list'));

            if (!empty($response) || !$response->count)
            {
                $this->cliEcho("Returned ");
                $this->cliEcho($response->count, 'green');
                $this->cliEcho(" projects\n");
                $this->cliEcho("[project_id]", 'green', 'bold');
                $this->cliEcho(" (key) ", 'white', 'bold');
                $this->cliEcho("name\n", 'white');
                foreach ($response->projects as $project)
                {
                    $this->cliEcho("[$project->id]", 'green');
                    $this->cliEcho(" ($project->key) ", 'white', 'bold');
                    $this->cliEcho("$project->name\n");
                }
                $this->cliEcho("\n");
            }
            else
            {
                $this->cliEcho("No projects available.\n\n");
            }
        }

    }
