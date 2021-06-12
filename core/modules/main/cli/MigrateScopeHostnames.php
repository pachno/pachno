<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities\Scope;
    use pachno\core\entities\tables\ScopeHostnames;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> migrate_scope_hostnames
     *
     * @package pachno
     * @subpackage core
     */
    class MigrateScopeHostnames extends Command
    {

        public function do_execute()
        {
            $scopes = Scope::getAll();
            $new_domain = $this->getProvidedArgument('domain');

            foreach ($scopes as $scope_id => $scope) {
                foreach ($scope->getHostnames() as $id => $hostname) {
                    $parts = explode('.', $hostname);
                    $new_hostname = $parts[0] . '.' . $new_domain;
                    $this->cliEcho($new_hostname . "\n");
                    ScopeHostnames::getTable()->setHostnameById($id, $new_hostname);
                }
            }
            $this->cliEcho("Done\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'migrate_scope_hostnames';
            $this->_description = "Migrate all scopes from one domain to another";
            $this->addRequiredArgument('domain', 'The new domain for all scopes');
            parent::_setup();
        }

    }
