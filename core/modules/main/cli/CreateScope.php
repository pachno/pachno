<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities;
    use pachno\core\entities\tables;
    use pachno\core\framework;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> list_scopes
     *
     * @package pachno
     * @subpackage core
     */
    class CreateScope extends Command
    {

        public function do_execute()
        {
            $hostname = $this->getProvidedArgument('hostname');
            $this->cliEcho('Checking scope availability ...');
            if (tables\ScopeHostnames::getTable()->getScopeIDForHostname($hostname) === null) {
                $this->cliEcho("available!\n");
                $this->cliEcho("Creating scope ...");
                $scope = new entities\Scope();
                $scope->addHostname($hostname);
                $scope->setName($this->getProvidedArgument('shortname'));
                $uploads_enabled = ($this->getProvidedArgument('enable_uploads', 'yes') == 'yes');
                $scope->setUploadsEnabled((bool)$uploads_enabled);
                $scope->setMaxUploadLimit($this->getProvidedArgument('upload_limit', 0));
                $scope->setMaxProjects($this->getProvidedArgument('projects', 0));
                $scope->setMaxUsers($this->getProvidedArgument('users', 0));
                $scope->setMaxTeams($this->getProvidedArgument('teams', 0));
                $scope->setMaxWorkflowsLimit($this->getProvidedArgument('workflows', 0));
                $scope->setEnabled();
                $this->cliEcho(".");
                $scope->save();
                $this->cliEcho(".done!\n");

                $admin_user = $this->getProvidedArgument('scope_admin');
                if ($admin_user) {
                    $this->cliEcho("Adding user {$admin_user} to scope\n");
                    $admin_group_id = (int)framework\Settings::get(framework\Settings::SETTING_ADMIN_GROUP, 'core', $scope->getID());
                    tables\UserScopes::getTable()->addUserToScope($admin_user, $scope->getID(), $admin_group_id, true);
                }
                if ($this->getProvidedArgument('remove_admin', 'no') == 'yes') {
                    $this->cliEcho("Removing administrator user from scope\n");
                    tables\UserScopes::getTable()->removeUserFromScope(1, $scope->getID());
                }

                foreach (framework\Context::getModules() as $module) {
                    $module_name = $module->getName();
                    if ($module_name == 'publish') continue;
                    if ($this->getProvidedArgument("install_module_{$module_name}", "no") == 'yes') {
                        $this->cliEcho("Installing module {$module_name}\n");
                        entities\Module::installModule($module_name, $scope);
                    }
                }
            } else {
                $this->cliEcho("not available\n", 'red');
            }
            $this->cliEcho("\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'create_scope';
            $this->_description = "Create a new scope";
            $this->addRequiredArgument('hostname', 'The default hostname for this scope');
            $this->addRequiredArgument('shortname', 'The short name for this scope');
            $this->addOptionalArgument('description', 'An optional description for this scope');
            $this->addOptionalArgument('enable_uploads', 'Whether uploads are enabled for this scope (yes/no)');
            $this->addOptionalArgument('upload_limit', 'The upload limit for this scope');
            $this->addOptionalArgument('projects', 'The number of available projects for this scope');
            $this->addOptionalArgument('users', 'The number of available users for this scope');
            $this->addOptionalArgument('teams', 'The number of available teams for this scope');
            $this->addOptionalArgument('workflows', 'The number of available workflows for this scope');
            foreach (framework\Context::getModules() as $module) {
                $module_name = $module->getName();
                if ($module_name == 'publish') continue;
                $this->addOptionalArgument("install_module_{$module_name}", "Install the {$module_name} module in this scope (yes/no)");
            }
            $this->addOptionalArgument('scope_admin', 'Add an admin user (username) to this scope');
            $this->addOptionalArgument('remove_admin', 'Remove admininistrator from this scope (yes/no)');
            parent::_setup();
        }

    }