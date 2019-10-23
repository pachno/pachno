<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities\Scope;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> list_scopes
     *
     * @package pachno
     * @subpackage core
     */
    class ListScopes extends Command
    {

        public function do_execute()
        {
            $scopes = Scope::getAll();
            $this->cliEcho("The ID for the default scope has an asterisk next to it\n\n");

            $this->cliEcho("ID", 'white', 'bold');
            $this->cliEcho(" - hostname(s)\n", 'white', 'bold');
            foreach ($scopes as $scope_id => $scope) {
                $this->cliEcho($scope_id, 'white', 'bold');
                if ($scope->isDefault()) {
                    $this->cliEcho('*', 'white', 'bold');
                    $this->cliEcho(" - all unspecified hostnames\n");
                } else {
                    $this->cliEcho(" - " . join(', ', $scope->getHostnames()) . "\n");
                }
            }
            $this->cliEcho("\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'list_scopes';
            $this->_description = "List available scopes";
            parent::_setup();
        }

    }
