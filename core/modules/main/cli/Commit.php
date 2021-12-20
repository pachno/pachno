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
    class Commit extends Command
    {

        public function do_execute()
        {
            if (framework\Context::isInstallmode()) {
                return;
            }

            framework\Context::loadModules();
            if ($this->getProvidedArgument('mode') === 'pre_commit') {
                foreach (framework\Context::getModules() as $module) {
                    if ($module->hasComposerDependencies()) {
                        $module->removeSectionsFromComposerJson();
                    }
                }
            } elseif ($this->getProvidedArgument('mode') === 'post_commit') {
                foreach (framework\Context::getModules() as $module) {
                    if ($module->hasComposerDependencies()) {
                        $module->addSectionsToComposerJson();
                    }
                }
            }
        }

        protected function _setup()
        {
            $this->_command_name = 'commit';
            $this->addRequiredArgument('mode', 'pre_commit or post_commit');
            $this->_description = "Cleans or appends module-specific changes from composer.json when running a commit";
        }

    }
