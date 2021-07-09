<?php

    namespace pachno\core\modules\api\cli;

    use pachno\core\framework\Context;

    /**
     * CLI command class, api -> list_issues
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * CLI command class, api -> list_issues
     *
     * @package pachno
     * @subpackage core
     */
    class ListIssues extends \pachno\core\modules\api\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'list_issues';
            $this->_description = "Query a remote server for a list of available issues for a project";
            $this->addRequiredArgument('project_id', 'The project id to show available issue fields for');
            $this->addOptionalArgument("state", "Filter show only [open/closed/all] issues");
            $this->addOptionalArgument("issuetype", "Filter show only issues of type [<issue type>] (see api_list_issuetypes)");
            $this->addOptionalArgument("assigned_to", "Filter show only issues assigned to [<username>/me/none/all]");
            $this->addOptionalArgument("detailed", 'Whether to show a detailed issue list or not [yes/no] (default <no>)');
            parent::_setup();
        }

        public function do_execute()
        {
            $this->cliEcho('Querying ');
            $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
            $this->cliEcho(" for list of issues ...\n\n");

            $this->cliEcho("Filters:\n", 'white', 'bold');
            $options = array('format' => 'json');
            $options["state"] = $this->getProvidedArgument("state", "open");
            $this->cliEcho("State: ");
            $this->cliEcho($options["state"], "yellow", "bold");
            $this->cliEcho("\n");

            $options["issuetype"] = $this->getProvidedArgument("issuetype", "all");
            $this->cliEcho("Issuetypes: ");
            $this->cliEcho($options["issuetype"], "yellow", "bold");
            $this->cliEcho("\n");

            $options["assigned_to"] = $this->getProvidedArgument("assigned_to", "all");
            $this->cliEcho("Assigned to: ");
            $this->cliEcho($options["assigned_to"], "yellow", "bold");
            $this->cliEcho("\n");
            $options["assigned"] = $this->getProvidedArgument("state", "all");

            $project_id = $this->getProvidedArgument('project_id');
            $options['project_id'] = $project_id;

            $response = $this->getRemoteResponse($this->getRemoteURL('api_projects_issues_list', $options));

            $this->cliEcho("\n");
            if (!empty($response) && $response->count > 0)
            {
                Context::loadLibrary('common');
                if ($response->count > 50) {
                    $this->cliEcho("Showing max 50 of {$response->count} found issue(s):\n", 'white', 'bold');
                } else {
                    $this->cliEcho("The following {$response->count} issue(s) were found:\n", 'white', 'bold');
                }

                foreach ($response->issues as $issue)
                {
                    //$this->cliEcho("ID: {$issue->id} ", 'yellow');
                    if (mb_strtolower($options['state']) == 'all')
                    {
                        $this->cliEcho(($issue->state == \pachno\core\entities\Issue::STATE_OPEN) ? "[open] " : "[closed] ");
                    }
                    if ($this->getProvidedArgument('detailed', 'no') != 'yes') {
                        $this->cliEcho('[' . trim(Context::getI18n()->formatTime($issue->updated_at, 21, true)) . '] ', 'cyan');
                    }
                    $this->cliEcho($issue->issue_no, 'green', 'bold');
                    $this->cliEcho(" - ");
                    $this->cliEcho(html_entity_decode($issue->title), 'white', 'bold');
                    $this->cliEcho("\n");
                    if ($this->getProvidedArgument('detailed', 'no') == 'yes')
                    {
                        $this->cliEcho("Updated: ", 'blue', 'bold');
                        $this->cliEcho(Context::getI18n()->formatTime($issue->updated_at, 21, true));
                        $this->cliEcho("\n");
                        $this->cliEcho("Posted: ", 'blue', 'bold');
                        $this->cliEcho(Context::getI18n()->formatTime($issue->created_at, 21, true));
                        $this->cliEcho("by ");
                        $this->cliEcho($issue->posted_by->name, 'cyan');
                        $this->cliEcho("\n");
                        $this->cliEcho("Assigned to: ", 'blue', 'bold');
                        if ($issue->assigned_to) {
                            $this->cliEcho($issue->assigned_to, 'yellow', 'bold');
                        } else {
                            $this->cliEcho('-', 'yellow', 'bold');
                        }
                        $this->cliEcho(" | ", 'white', 'bold');
                        $this->cliEcho("Status: ", 'blue', 'bold');
                        $this->cliEcho($issue->status->name);
                        $this->cliEcho("\n\n");
                    }
                }
                $this->cliEcho("\n");
                $this->cliEcho("If you are going to update or query any of these issues, use the \n");
                $this->cliEcho("issue number shown in front of the issue (do not include the \n");
                $this->cliEcho("issue number hash), ex:\n");
                $this->cliEcho("./bin/pachno", 'green');
                $this->cliEcho(" api:update_issue {$project_id} ");
                $this->cliEcho("300\n", 'white', 'bold');
                $this->cliEcho("./bin/pachno", 'green');
                $this->cliEcho(" api:show_issue {$project_id} ");
                $this->cliEcho("300\n", 'white', 'bold');
                $this->cliEcho("./bin/pachno", 'green');
                $this->cliEcho(" api:list_transitions {$project_id} ");
                $this->cliEcho("300\n", 'white', 'bold');
                $this->cliEcho("\nor\n");
                $this->cliEcho("./bin/pachno", 'green');
                $this->cliEcho(" api:update_issue {$project_id} ");
                $this->cliEcho("PREFIX-12\n", 'white', 'bold');
                $this->cliEcho("./bin/pachno", 'green');
                $this->cliEcho(" api:show_issue {$project_id} ");
                $this->cliEcho("PREFIX-12\n", 'white', 'bold');
                $this->cliEcho("./bin/pachno", 'green');
                $this->cliEcho(" api:list_transitions {$project_id} ");
                $this->cliEcho("PREFIX-12\n", 'white', 'bold');
                $this->cliEcho("\n");
                $this->cliEcho("\n");
            }
            else
            {
                $this->cliEcho("No issues available matching your filters.\n\n");
            }
        }

    }
