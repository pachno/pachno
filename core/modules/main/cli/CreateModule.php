<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\framework\cli\Command;
    use pachno\core\framework\Context;

    /**
     * CLI command class, main -> create_module
     *
     * @package pachno
     * @subpackage core
     */
    class CreateModule extends Command
    {

        public function do_execute()
        {
            if (Context::isInstallmode()) {
                $this->cliEcho("Create module\n", 'white', 'bold');
                $this->cliEcho("Pachno is not installed\n", 'red');
            } else {
                $module_key = mb_strtolower($this->getProvidedArgument('module_name'));
                $module_name = ucfirst($module_key);
                $module_description = "Autogenerated module {$module_name}";
                $this->cliEcho("Initializing empty module ");
                $this->cliEcho("{$module_key}\n", 'white', 'bold');
                $this->cliEcho("Checking that the module doesn't exist ... ");
                if (file_exists(PACHNO_MODULES_PATH . $module_key)) {
                    $this->cliEcho("fail\n", 'red');
                    $this->cliEcho("A module with this name already exists\n", 'red');

                    return false;
                } else {
                    $this->cliEcho("OK\n", 'green', 'bold');
                }

                $this->cliEcho("Checking for conflicting classnames ... ");
                if (class_exists($module_name)) {
                    $this->cliEcho("fail\n", 'red');
                    $this->cliEcho("A class with this name already exists\n", 'red');

                    return false;
                } else {
                    $this->cliEcho("OK\n", 'green', 'bold');
                }

                $this->cliEcho("Checking that the module path is writable ... ");
                if (!is_writable(PACHNO_MODULES_PATH)) {
                    $this->cliEcho("fail\n", 'red');
                    $this->cliEcho("Module path isn't writable\n\n", 'red');
                    $this->cliEcho("Please make sure that the following path is writable: \n");
                    $this->cliEcho(PACHNO_MODULES_PATH, 'cyan');

                    return false;
                } else {
                    $this->cliEcho("OK\n", 'green', 'bold');
                }

                $this->cliEcho("\nCreating module directory structure ... \n", 'white', 'bold');
                $this_module_path = PACHNO_MODULES_PATH . $module_key . DS;
                mkdir(PACHNO_MODULES_PATH . $module_key);
                $this->cliEcho('modules' . DS . "{$module_key}\n");
                mkdir($this_module_path . 'components');
                $this->cliEcho('modules' . DS . $module_key . DS . "components\n");
                mkdir($this_module_path . 'controllers');
                $this->cliEcho('modules' . DS . $module_key . DS . "controllers\n");
                mkdir($this_module_path . 'configuration');
                $this->cliEcho('modules' . DS . $module_key . DS . "configuration\n");
                mkdir($this_module_path . 'entities');
                $this->cliEcho('modules' . DS . $module_key . DS . "entities\n");
                mkdir($this_module_path . 'cli');
                $this->cliEcho('modules' . DS . $module_key . DS . "cli\n");
                mkdir($this_module_path . 'templates');
                $this->cliEcho('modules' . DS . $module_key . DS . "templates\n");
                $this->cliEcho("... ", 'white', 'bold');
                $this->cliEcho("OK\n", 'green', 'bold');

                $this->cliEcho("\nCreating module files ... \n", 'white', 'bold');
                $module_class_template = file_get_contents(PACHNO_INTERNAL_MODULES_PATH . "main" . DS . "fixtures" . DS . "emptymoduleclass");
                $module_class_content = str_replace(['module_key', 'module_name', 'module_description'], [$module_key, $module_name, $module_description], $module_class_template);
                file_put_contents($this_module_path . $module_name . ".php", $module_class_content);
                $this->cliEcho("modules" . DS . $module_key . DS . $module_name . ".php\n");

                $module_actions_class_template = file_get_contents(PACHNO_INTERNAL_MODULES_PATH . "main" . DS . "fixtures" . DS . "emptymoduleactionsclass");
                $module_actions_class_content = str_replace(['module_key', 'module_name', 'module_description'], [$module_key, $module_name, $module_description], $module_actions_class_template);
                file_put_contents($this_module_path . DS . "controllers" . DS . "Main.php", $module_actions_class_content);
                $this->cliEcho("modules" . DS . $module_key . DS . "Main.php\n");

                $module_actioncomponents_class_template = file_get_contents(PACHNO_INTERNAL_MODULES_PATH . "main" . DS . "fixtures" . DS . "emptymoduleactioncomponentsclass");
                $module_actioncomponents_class_content = str_replace(['module_key', 'module_name', 'module_description'], [$module_key, $module_name, $module_description], $module_actioncomponents_class_template);
                file_put_contents($this_module_path . "Components.php", $module_actioncomponents_class_content);
                $this->cliEcho("modules" . DS . $module_key . DS . "Components.php\n");

                file_put_contents($this_module_path . "templates" . DS . "index.html.php", "{$module_name} frontpage");
                $this->cliEcho("modules" . DS . $module_key . DS . "templates" . DS . "index.html.php\n");

                $this->cliEcho("... ", 'white', 'bold');
                $this->cliEcho("OK\n\n", 'green', 'bold');

                $this->cliEcho("The module was created successfully!\n", 'green');
            }
        }

        protected function _setup()
        {
            $this->_command_name = 'create_module';
            $this->_description = "Create an empty module ready to start developing";
            $this->addRequiredArgument('module_name', "The module to create, typically 'MyModule' or similar - no spaces!");
        }

    }
