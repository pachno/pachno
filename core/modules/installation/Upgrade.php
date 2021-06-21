<?php

    namespace pachno\core\modules\installation;

    use Exception;
    use pachno\core\entities\Scope;
    use pachno\core\framework;
    use pachno\core\framework\cli\Command;
    use pachno\core\modules\installation\entities\upgrade_1_0_0\tables\LivelinkImports;

    class Upgrade
    {

        protected $upgrade_complete = false;

        protected $upgrade_options = [];

        protected $current_version;

        protected function _upgradeFrom1_0_0(framework\Request $request = null): bool
        {
            $this->cliEchoUpgradeTable(LivelinkImports::getTable());
            \pachno\core\entities\tables\LivelinkImports::getTable()->upgrade(LivelinkImports::getTable());

            $this->current_version = '1.0.1';

            return true;
        }

        /**
         * Perform the actual upgrade
         *
         * @param ?framework\Request $request
         */
        public function upgrade(framework\Request $request = null): bool
        {
            set_time_limit(0);

            list ($this->current_version, $this->upgrade_available) = framework\Settings::getUpgradeStatus();

            $scope = new Scope();
            $scope->setID(1);
            $scope->setEnabled();
            framework\Context::setScope($scope);

            $this->upgrade_complete = false;

            if (framework\Context::isCLI()) {
                Command::cli_echo("Gathering information before upgrading...\n\n");
            }

            switch ($this->current_version) {
                case '1.0.0':
                    $this->_upgradeFrom1_0_0($request);
            }

            $existing_installed_content = file_get_contents(PACHNO_PATH . 'installed');
            file_put_contents(PACHNO_PATH . 'installed', framework\Settings::getVersion(false, true) . ', upgraded ' . date('d.m.Y H:i') . "\n" . $existing_installed_content);
            $this->current_version = framework\Settings::getVersion(false, false);

            return true;
        }

        protected function cliEchoUpgradeTable($table, $time_warning = false)
        {
            if (!framework\Context::isCLI()) {
                return;
            }

            $namespaces = explode('\\', get_class($table));
            $classname = array_pop($namespaces);
            Command::cli_echo('Upgrading', 'white', 'bold');
            Command::cli_echo(' table ');
            Command::cli_echo($classname, 'yellow');
            if ($time_warning) {
                Command::cli_echo(' - data migration may take a little while ...');
            }
            Command::cli_echo("\n");
        }

        protected function cliEchoCreateTable($table)
        {
            if (!framework\Context::isCLI()) {
                return;
            }

            $namespaces = explode('\\', get_class($table));
            $classname = array_pop($namespaces);
            Command::cli_echo('Creating', 'white', 'bold');
            Command::cli_echo(' table ' . implode('\\', $namespaces) . '\\');
            Command::cli_echo($classname, 'yellow');
            Command::cli_echo("\n");
        }

        protected function cliEchoAddIndexTable($table)
        {
            if (!framework\Context::isCLI()) {
                return;
            }

            $namespaces = explode('\\', get_class($table));
            $classname = array_pop($namespaces);
            Command::cli_echo('Adding indexes', 'white', 'bold');
            Command::cli_echo(' for table ' . implode('\\', $namespaces) . '\\');
            Command::cli_echo($classname, 'yellow');
            Command::cli_echo("\n");
        }

        protected function cliEchoUpgradedVersion($version_number)
        {
            if (!framework\Context::isCLI()) {
                return;
            }

            Command::cli_echo("Successfully upgraded to version ");
            Command::cli_echo($version_number, 'green', 'bold');
            Command::cli_echo("\n");
        }

    }
