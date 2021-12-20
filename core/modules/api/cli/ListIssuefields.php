<?php

    namespace pachno\core\modules\api\cli;

    /**
     * CLI command class, api -> list_issuefields
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * CLI command class, api -> list_issuefields
     *
     * @package pachno
     * @subpackage core
     */
    class ListIssuefields extends \pachno\core\modules\api\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'list_issuefields';
            $this->_description = "Query a remote server for a list of available issue fields per types";
            $this->addRequiredArgument('project_id', 'The project id to show available issue fields for');
            $this->addRequiredArgument('issuetype', 'An issue type to show available issue fields for');
            parent::_setup();
        }

        public function do_execute()
        {
            $issuetype = $this->getProvidedArgument('issuetype', null);
            $project_id = $this->getProvidedArgument('project_id', null);

            $this->cliEcho('Querying ');
            $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
            $this->cliEcho(" for issuefields valid for issue types {$issuetype} for project {$project_id}\n\n");

            $response = $this->getRemoteResponse($this->getRemoteURL('api_list_issuefields', array('issuetype' => $issuetype, 'project_id' => $project_id, 'format' => 'json')));

            if (!empty($response))
            {
                $this->cliEcho($issuetype, 'yellow', 'bold');
                $this->cliEcho(" has the following available issue fields:\n");
                foreach ($response->issuefields as $field_key)
                {
                    $this->cliEcho("  {$field_key}\n", 'yellow');
                }
                $this->cliEcho("\n");
                $this->cliEcho("When using ");
                $this->cliEcho('api:update_issue', 'green');
                $this->cliEcho(" to update an issue, pass any of these issue fields\n");
                $this->cliEcho("as a valid parameter to update the issue details.\n");
                $this->cliEcho("\n");
                $this->cliEcho("Check the documentation for ");
                $this->cliEcho('api:update_issue', 'green');
                $this->cliEcho(" for more information.\n");

            }
            else
            {
                $this->cliEcho("No issue fields available.\n\n");
            }
        }

    }
