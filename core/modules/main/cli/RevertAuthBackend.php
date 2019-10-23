<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\framework\cli\Command;
    use pachno\core\framework\Settings;

    /**
     * CLI command class, main -> revert_auth_backend
     *
     * @package pachno
     * @subpackage core
     */
    class RevertAuthBackend extends Command
    {

        public function do_execute()
        {
            $this->cliEcho("\n");
            $this->cliEcho("Revert authentication backend\n", 'white', 'bold');
            $this->cliEcho("This command is useful if you've managed to lock yourself.\n");
            $this->cliEcho("out due to an authentication backend change gone bad.\n\n");

            if (Settings::getAuthenticationBackendIdentifier() == 'default' || Settings::getAuthenticationBackendIdentifier() == null) {
                $this->cliEcho("You are currently using the default authentication backend.\n\n");
            } else {
                $this->cliEcho("Please type 'yes' if you want to revert to the default authentication backend: ");
                $this->cliEcho("\n");
                if ($this->getInput() == 'yes') {
                    Settings::saveSetting(Settings::SETTING_AUTH_BACKEND, 'default');
                    $this->cliEcho("Authentication backend reverted.\n\n");
                } else {
                    $this->cliEcho("No changes made.\n\n");
                }
            }
        }

        protected function _setup()
        {
            $this->_command_name = 'revert_auth_backend';
            $this->_description = "Reverts the auth backend back to the default";
//            $this->addOptionalArgument('accept_license', 'Set to "yes" to auto-accept license');
        }

    }
