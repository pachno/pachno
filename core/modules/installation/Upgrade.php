<?php

    namespace pachno\core\modules\installation;

    use Exception;
    use Nadar\PhpComposerReader\ComposerReader;
    use pachno\core\entities\Scope;
    use pachno\core\framework;
    use pachno\core\framework\cli\Command;
    use pachno\core\modules\installation\entities\upgrade_1_0_0\tables\LivelinkImports;
    use pachno\core\modules\installation\entities\upgrade_1_0_2\tables\Users;
    use pachno\core\modules\installation\entities\upgrade_1_0_4\tables\AgileBoards;
    use pachno\core\modules\installation\entities\upgrade_1_0_5\tables\WorkflowSteps;
    use pachno\core\modules\installation\entities\upgrade_1_0_5\tables\WorkflowStepTransitions;

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

        protected function _upgradeFrom1_0_2(framework\Request $request = null): bool
        {
            $this->cliEchoUpgradeTable(Users::getTable());
            \pachno\core\entities\tables\Users::getTable()->upgrade(Users::getTable());

            $this->current_version = '1.0.3';

            return true;
        }

        protected function _upgradeFrom1_0_4(framework\Request $request = null): bool
        {
            $this->cliEchoUpgradeTable(AgileBoards::getTable());
            \pachno\core\entities\tables\AgileBoards::getTable()->upgrade(AgileBoards::getTable());

            $this->current_version = '1.0.5';

            return true;
        }

        protected function _upgradeFrom1_0_5(framework\Request $request = null): bool
        {
            $this->cliEchoUpgradeTable(WorkflowStepTransitions::getTable());
            \pachno\core\entities\tables\WorkflowStepTransitions::getTable()->upgrade(WorkflowStepTransitions::getTable());
            $this->cliEchoUpgradeTable(WorkflowSteps::getTable());
            \pachno\core\entities\tables\WorkflowSteps::getTable()->upgrade(WorkflowSteps::getTable());

            $this->current_version = '1.0.6';

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

            $reader = new ComposerReader(PACHNO_PATH . 'composer.json');
            $repositories = $reader->contentSection('repositories', null);
            if (count($repositories)) {
                throw new framework\exceptions\ConfigurationException('Invalid composer.json contents', framework\exceptions\ConfigurationException::UPGRADE_NON_RESET_COMPOSER_JSON);
            }

            switch ($this->current_version) {
                case '1.0.0':
                    $this->_upgradeFrom1_0_0($request);
                case '1.0.2':
                    $this->_upgradeFrom1_0_2($request);
                case '1.0.3':
                case '1.0.4':
                    $this->_upgradeFrom1_0_4($request);
                case '1.0.5':
                    $this->_upgradeFrom1_0_5($request);
            }

            framework\Context::loadModules();
            foreach (framework\Context::getModules() as $module) {
                if ($module->hasComposerDependencies()) {
                    $module->addSectionsToComposerJson();
                }
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
