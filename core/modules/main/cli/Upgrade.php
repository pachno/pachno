<?php

    namespace pachno\core\modules\main\cli;

    use Exception;
    use pachno\core\framework;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> clear-cache
     *
     * @package pachno
     * @subpackage core
     */
    class Upgrade extends Command
    {

        public function do_execute()
        {
            if ($this->getProvidedArgument('prepare', '') !== '') {
                $this->cliEcho('Preparing for upgrade ...');
                framework\Context::loadModules();
                foreach (framework\Context::getModules() as $module) {
                    if ($module->hasComposerDependencies()) {
                        $module->removeSectionsFromComposerJson();
                    }
                }
                $this->cliEcho("... done.\n");
                return;
            }

            list ($current_version, $upgrade_available) = framework\Settings::getUpgradeStatus();

            $this->cliEcho('Performing upgrade: ');
            $this->cliEcho($current_version, 'white', 'bold');
            $this->cliEcho(' -> ');
            $this->cliEcho(framework\Settings::getVersion(false), 'green', 'bold');
            $this->cliEcho("\n\n");

            if (!$upgrade_available) {
                $this->cliEcho('No upgrade necessary!', 'green');
                $this->cliEcho("\n");

                return;
            } else {
                try {
                    $upgrader = new \pachno\core\modules\installation\Upgrade();
                    $result = $upgrader->upgrade();
                    $this->cliEcho("\n");
                    if ($result) {
                        $this->cliEcho("Upgrade complete!\n");
                        $this->cliEcho("\n---------------------\n");
                        $this->cliEcho("Remember to complete the upgrade by running ", 'white', 'bold');
                        $this->cliEcho("composer update", 'green', 'bold');
                        $this->cliEcho(" to re-install module dependencies\n", 'white', 'bold');
                        $this->cliEcho("\n---------------------\n");
                        unlink(PACHNO_PATH . 'upgrade');
                    } else {
                        $this->cliEcho("Upgrade failed!\n", 'red');
                    }
                } catch (Exception $e) {
                    $this->cliEcho("\n");
                    $this->cliEcho("\n---------------------\n");
                    if (isset($result) && $result === true) {
                        $this->cliEcho("The upgrade procedure ran correctly, but an error occured trying to remove the upgrade file.\n", 'red', 'bold');
                        $this->cliEcho("It should be safe to manually remove the file 'upgrade' and continue as if the upgrade completed.\n", 'red', 'bold');
                    } else {
                        $this->cliEcho("An error occured during the upgrade:\n", 'red', 'bold');
                    }
                    $this->cliEcho($e->getMessage() . "\n");
                    if ($e->getCode() === framework\exceptions\ConfigurationException::UPGRADE_NON_RESET_COMPOSER_JSON) {
                        $this->cliEcho("Please revert any local changes to composer.json before continuing the upgrade.\n");
                    }
                    $this->cliEcho("---------------------\n");
                }
            }
        }

        protected function _setup()
        {
            $this->_command_name = 'upgrade';
            $this->addOptionalArgument('prepare', 'Prepare for upgrade by clearing out local changes in composer.json');
            $this->_description = "Upgrades the installation to the current version.";
        }

    }
