<?php

    namespace pachno\core\modules\main\cli;

    /**
     * CLI command class, main -> remove_scope
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */
    use pachno\core\entities\Scope;

    /**
     * CLI command class, main -> remove_scope
     *
     * @package pachno
     * @subpackage core
     */
    class RemoveScope extends \pachno\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'remove_scope';
            $this->_description = "Removes a scope";
            $this->addRequiredArgument('hostname', 'The hostname for the scope to remove');
            parent::_setup();
        }

        public function do_execute()
        {
            $scope = \pachno\core\entities\tables\Scopes::getTable()->getByHostname($this->getProvidedArgument('hostname'));
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

    }
