<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities\Scope;
    use pachno\core\entities\tables\Scopes;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> remove_scope
     *
     * @package pachno
     * @subpackage core
     */
    class RemoveScope extends Command
    {

        public function do_execute()
        {
            $scope = Scopes::getTable()->getByHostname($this->getProvidedArgument('hostname'));
            if ($scope instanceof Scope) {
                $message = "Removing scope with ID " . $scope->getID() . " for hostname " . $this->getProvidedArgument('hostname');
                $this->cliEcho($message . "\n\n");
                $scope->delete();
                $this->cliEcho("Done", 'white', 'bold');
            } else {
                $message = "No scope found for hostname " . $this->getProvidedArgument('hostname');
                $this->cliEcho($message . "\n\n", 'white', 'bold');
            }

            $this->cliEcho("\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'remove_scope';
            $this->_description = "Removes a scope";
            $this->addRequiredArgument('hostname', 'The hostname for the scope to remove');
            parent::_setup();
        }

    }
