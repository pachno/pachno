<?php

    namespace pachno\core\modules\main\cli;

    use b2db\AnnotationSet;
    use b2db\Core;
    use Exception;
    use pachno\core\entities\Module;
    use pachno\core\entities\Scope;
    use pachno\core\framework\cli\Command;
    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;
    use ReflectionClass;
    use Spyc;
    use const PACHNO_CONFIGURATION_PATH;

    /**
     * CLI command class, main -> install
     *
     * @package pachno
     * @subpackage core
     */
    class Install extends Command
    {

        protected $connect_mode = false;

        public function do_execute()
        {
            if ($this->getProvidedArgument('only_connect') == 'yes') {
                $this->connect_mode = true;
                $this->cliEcho("\n-----------------------------\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                $this->cliEcho('| Running in "connect mode" |', Command::COLOR_WHITE, Command::STYLE_BOLD);
                $this->cliEcho("\n-----------------------------\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
            }

            if (file_exists(PACHNO_PATH . 'installed')) {
                $this->cliEcho("Pachno seems to already be installed.\n", 'red', Command::STYLE_BOLD);
                $this->cliEcho('Please remove the file ');
                $this->cliEcho(PACHNO_PATH . 'installed', Command::COLOR_WHITE, Command::STYLE_BOLD);
                $this->cliEcho(' and try again.');
                $this->cliEcho("\n");

                return;
            }
            $this->cliEcho("\nWelcome to the \"Pachno\" installation wizard!\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
            $this->cliEcho("This wizard will take you through the installation of Pachno.\nRemember that you can also install Pachno from your web-browser.\n");
            $this->cliEcho("Simply point your web-browser to the Pachno subdirectory on your web server,\nand the installation will start.\n\n");
            $this->cliEcho("Press ENTER to continue with the installation ...");
            $this->pressEnterToContinue();

            $this->cliEcho("\n");
            $this->cliEcho("How to support future development\n", Command::COLOR_GREEN, Command::STYLE_BOLD);
            $this->cliEcho("Even though this software has been provided to you free of charge,\ndeveloping it would not have been possible without support from our users.\n");
            $this->cliEcho("By making a donation, or buying a support contract you can help us continue development.\n\n");
            $this->cliEcho("If this software is valuable to you - please consider supporting it.\n\n");
            $this->cliEcho("More information about supporting Pachno's development can be found here:\n");
            $this->cliEcho("https://pachno.com/support\n\n", 'blue', 'underline');
            $this->cliEcho("Press ENTER to continue ...");

            $this->pressEnterToContinue();
            $this->cliEcho("\n");

            try {
                $this->cliEcho("License information\n", Command::COLOR_GREEN, Command::STYLE_BOLD);
                $this->cliEcho("This software is Open Source Initiative approved Open Source Software.\nOpen Source Initiative Approved is a trademark of the Open Source Initiative.\n\n");
                $this->cliEcho("True to the the Open Source Definition, Pachno is released\nunder the MPL 2.0. You can read the full license here:\n");
                $this->cliEcho("http://opensource.org/licenses/MPL-2.0\n\n", 'blue', 'underline');

                if ($this->getProvidedArgument('accept_license') != 'yes') {
                    $this->cliEcho("Before you can continue the installation, you need to confirm that you \nagree to be bound by the terms in this license.\n\n");
                    $this->cliEcho("Do you agree to be bound by the terms in the MPL 2.0 license?\n(type \"yes\" to agree, anything else aborts the installation): ");
                    if (!$this->askToAccept()) throw new Exception($this->cliEcho('You need to accept the license to continue', 'red', Command::STYLE_BOLD));
                } else {
                    $this->cliEcho('You have accepted the license', 'yellow', Command::STYLE_BOLD);
                    $this->cliEcho("\n\n");
                }

                $not_well = [];
                if (!is_writable(PACHNO_CONFIGURATION_PATH)) {
                    $not_well[] = 'b2db_perm';
                }
                if (!is_writable(PACHNO_PATH)) {
                    $not_well[] = 'root';
                }

                if (count($not_well) > 0) {
                    $this->cliEcho("\n");
                    foreach ($not_well as $afail) {
                        switch ($afail) {
                            case 'b2db_perm':
                                $this->cliEcho("Could not write to the B2DB directory\n", 'red', Command::STYLE_BOLD);
                                $this->cliEcho('The folder ');
                                $this->cliEcho(PACHNO_CONFIGURATION_PATH, Command::COLOR_WHITE, Command::STYLE_BOLD);
                                $this->cliEcho(' folder needs to be writable');
                                break;
                            case 'root':
                                $this->cliEcho("Could not write to the main directory\n", 'red', Command::STYLE_BOLD);
                                $this->cliEcho('The top level folder must be writable during installation');
                                break;
                        }
                    }

                    throw new Exception("\n\nYou need to correct the above errors before the installation can continue.");
                } else {
                    $this->cliEcho("Step 1 - database information\n");
                    if (file_exists($this->_b2db_config_file)) {
                        $this->cliEcho("You seem to already have completed this step successfully.\n");
                        if ($this->getProvidedArgument('use_existing_db_info') == 'yes') {
                            $this->cliEcho("\n");
                            $this->cliEcho("Using existing database information\n", 'yellow', Command::STYLE_BOLD);
                            $use_existing_db_info = true;
                        } else {
                            $this->cliEcho("Do you want to use the stored settings?\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho("\nType \"no\" to enter new settings, press ENTER to use existing: ", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $use_existing_db_info = $this->askToDecline();
                        }
                        $this->cliEcho("\n");
                    } else {
                        $use_existing_db_info = false;
                    }
                    if (!$use_existing_db_info) {
                        $this->cliEcho("Pachno uses a database to store information. To be able to connect\nto your database, Pachno needs some information, such as\ndatabase type, username, password, etc.\n\n");
                        $this->cliEcho("Please select what kind of database you are installing Pachno on:\n");
                        $db_types = [];
                        foreach (Core::getDrivers() as $db_type => $db_desc) {
                            $db_types[] = $db_type;
                            $this->cliEcho(count($db_types) . ': ' . $db_desc . "\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        }
                        do {
                            $this->cliEcho('Enter the corresponding number for the database (1-' . count($db_types) . '): ');
                            $db_selection = $this->getInput();
                            if (!isset($db_types[((int)$db_selection - 1)])) throw new Exception($db_selection . ' is not a valid database type selection');
                            $db_type = $db_types[((int)$db_selection - 1)];
                            $this->cliEcho("Selected database type: ");
                            $this->cliEcho($db_type . "\n\n");
                            $this->cliEcho("Please enter the database hostname: \n");
                            $this->cliEcho('Database hostname [localhost]: ', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $db_hostname = $this->getInput();
                            $db_hostname = ($db_hostname == '') ? 'localhost' : $db_hostname;
                            $this->cliEcho("\nPlease enter the username Pachno will use to connect to the database: \n");
                            $this->cliEcho('Database username: ', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $db_username = $this->getInput();
                            $this->cliEcho("Database password (press ENTER if blank): ", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $db_password = $this->getInput();
                            $this->cliEcho("\nPlease enter the database Pachno will use.\nIf it does not exist, Pachno will create it for you.\n(the default database name is ");
                            $this->cliEcho("pachno_db", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho(" - press ENTER to use that):\n");
                            $this->cliEcho('Database name: ', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $db_name = $this->getInput('pachno_db');
                            $this->cliEcho("\n");
                            $this->cliEcho("The following settings will be used:\n");
                            $this->cliEcho("Database type: \t\t", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho($db_type . "\n");
                            $this->cliEcho("Database hostname: \t", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho($db_hostname . "\n");
                            $this->cliEcho("Database username: \t", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho($db_username . "\n");
                            $this->cliEcho("Database password: \t", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho($db_password . "\n");
                            $this->cliEcho("Database name: \t\t", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho($db_name . "\n");

                            $this->cliEcho("\nIf these settings are ok, press ENTER, or anything else to retry: ");

                            $e_ok = $this->askToDecline();
                        } while (!$e_ok);
                        try {
                            Core::setHostname($db_hostname);
                            Core::setUsername($db_username);
                            Core::setPassword($db_password);
                            Core::setDriver($db_type);
                            Core::setTablePrefix('pachno_');

                            Core::doConnect();
                            if ($this->connect_mode) {
                                $this->cliEcho("\nConnecting to existing database.\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            } else {
                                Core::createDatabase($db_name);
                            }
                            Core::setDatabaseName($db_name);
                            Core::doConnect();
                        } catch (Exception $e) {
                            throw new Exception("Could not connect to the database:\n" . $e->getMessage());
                        }
                        Core::setDatabaseName($db_name);
                        $this->cliEcho("\nSuccessfully connected to the database.\n", Command::COLOR_GREEN);
                        $this->cliEcho("Press ENTER to continue ... ");
                        $this->pressEnterToContinue();
                        $this->cliEcho("\n");
                        $this->cliEcho("Saving database connection information ... ", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        $this->cliEcho("\n");
                        Core::saveConnectionParameters($this->_b2db_config_file);
                        $this->cliEcho("Successfully saved database connection information.\n", Command::COLOR_GREEN);
                        $this->cliEcho("\n");
                    } else {
                        $b2db_config = Spyc::YAMLLoad($this->_b2db_config_file);

                        if (!array_key_exists("b2db", $b2db_config)) {
                            throw new Exception("Could not find database configuration in file " . $this->_b2db_config_file);
                        }

                        try {
                            Core::initialize($b2db_config["b2db"], Context::getCache());
                            Core::doConnect();
                        } catch (Exception $e) {
                            throw new Exception("Could not connect to the database:\n" .
                                $e->getMessage() . "\nPlease check your configuration file " .
                                $this->_b2db_config_file);
                        }

                        $this->cliEcho("Successfully connected to the database.\n", Command::COLOR_GREEN);
                    }
                    $this->cliEcho("\nPachno needs some server settings to function properly...\n\n");

                    do {
                        $this->cliEcho("URL rewriting\n", 'cyan', Command::STYLE_BOLD);
                        $this->cliEcho("Pachno uses a technique called \"url rewriting\" - which allows for pretty\nURLs such as ") . $this->cliEcho('/issue/1', Command::COLOR_WHITE, Command::STYLE_BOLD) . $this->cliEcho(' instead of ') . $this->cliEcho("viewissue.php?issue_id=1\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        $this->cliEcho("Make sure you have read the URL_REWRITE document located in the root\nfolder, or at https://pachno.com before you continue\n");

                        if (!$this->hasProvidedArgument('url_subdir')) {
                            $this->cliEcho("Press ENTER to continue ... ");
                            $this->pressEnterToContinue();
                        }
                        $this->cliEcho("\n");

                        $this->cliEcho("Pachno subdir\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        $this->cliEcho("This is the sub-path of the Web server where Pachno will be located.\n");
                        if ($this->hasProvidedArgument('url_subdir')) {
                            $this->cliEcho('Pachno subdir: ', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $url_subdir = $this->getProvidedArgument('url_subdir');
                            $this->cliEcho($url_subdir, 'yellow', Command::STYLE_BOLD);
                            $this->cliEcho("\n");
                        } else {
                            $this->cliEcho('Start and end this with a forward slash', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho(". (ex: \"/public/\")\nIf Pachno is running at the root directory, just type \"/\" (without the quotes)\n\n");
                            $this->cliEcho('Pachno subdir: ', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $url_subdir = $this->getInput();
                        }
                        $this->cliEcho("\n");

                        $this->cliEcho("Pachno will now be accessible at\n");
                        $this->cliEcho("http://example.com" . $url_subdir, Command::COLOR_WHITE, Command::STYLE_BOLD);
                        if ($this->hasProvidedArgument('url_subdir')) {
                            $this->cliEcho("\n");
                            $this->cliEcho("Using existing values", 'yellow', Command::STYLE_BOLD);
                            $this->cliEcho("\n");
                            $e_ok = true;
                        } else {
                            $this->cliEcho("\nPress ENTER if ok, or \"no\" to try again: ");
                            $e_ok = $this->askToDecline();
                        }
                        $this->cliEcho("\n");
                    } while (!$e_ok);

                    if ($this->getProvidedArgument('setup_htaccess') != 'yes') {
                        $this->cliEcho("Setup can autoconfigure your .htaccess file (located in the public/ subfolder), so you don't have to.\n");
                        $this->cliEcho('Would you like setup to auto-generate those files for you?');
                        $this->cliEcho("\nPress ENTER if ok, or \"no\" to not set up the .htaccess file: ");
                        $htaccess_ok = $this->askToDecline();
                    } else {
                        $this->cliEcho('Autoconfiguring .htaccess', 'yellow', Command::STYLE_BOLD);
                        $this->cliEcho("\n");
                        $htaccess_ok = true;
                    }
                    $this->cliEcho("\n");

                    if ($htaccess_ok) {
                        if (!is_writable(PACHNO_PATH . 'public/') || (file_exists(PACHNO_PATH . 'public/.htaccess') && !is_writable(PACHNO_PATH . 'public/.htaccess'))) {
                            $this->cliEcho("Permission denied when trying to save the [main folder]/public/.htaccess\n", 'red', Command::STYLE_BOLD);
                            $this->cliEcho("You will have to set up the .htaccess file yourself. See the README file for more information.\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho('Please note: ', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho("Pachno will not function properly until the .htaccess file is properly set up!\n");
                        } else {
                            $content = str_replace('###PUT URL SUBDIRECTORY HERE###', $url_subdir, file_get_contents(PACHNO_CORE_PATH . 'templates/htaccess.template'));
                            file_put_contents(PACHNO_PATH . 'public/.htaccess', $content);
                            if (file_get_contents(PACHNO_PATH . 'public/.htaccess') != $content) {
                                $this->cliEcho("Permission denied when trying to save the [main folder]/public/.htaccess\n", 'red', Command::STYLE_BOLD);
                                $this->cliEcho("You will have to set up the .htaccess file yourself. See the README file for more information.\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                                $this->cliEcho('Please note: ', Command::COLOR_WHITE, Command::STYLE_BOLD);
                                $this->cliEcho("Pachno will not function properly until the .htaccess file is properly set up!\n");
                            } else {
                                $this->cliEcho("The .htaccess file was successfully set up...\n", Command::COLOR_GREEN, Command::STYLE_BOLD);
                            }
                        }
                    } else {
                        $this->cliEcho("Skipping .htaccess auto-setup.");
                    }

                    if ($this->getProvidedArgument('setup_htaccess') != 'yes') {
                        $this->cliEcho("Press ENTER to continue ... ");
                        $this->pressEnterToContinue();
                        $this->cliEcho("\n");
                    }
                    $this->cliEcho("\n");
                    if ($this->connect_mode) {
                        $this->cliEcho("Skipping table creation ...\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                    } else {
                        $this->cliEcho("Creating tables ...\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        $b2db_entities_path = PACHNO_CORE_PATH . 'entities' . DS . 'tables' . DS;
                        $tables_created = [];
                        foreach (scandir($b2db_entities_path) as $tablefile) {
                            if (in_array($tablefile, ['.', '..']))
                                continue;

                            if (($tablename = mb_substr($tablefile, 0, mb_strpos($tablefile, '.'))) != '') {
                                $tablename = "\\pachno\\core\\entities\\tables\\{$tablename}";
                                $reflection = new ReflectionClass($tablename);
                                $docblock = $reflection->getDocComment();
                                $annotationset = new AnnotationSet($docblock);
                                if ($annotationset->hasAnnotation('Table')) {
                                    Core::getTable($tablename)->create();
                                    Core::getTable($tablename)->createIndexes();
                                    $tables_created[] = $tablename;
                                }
                            }
                        }

                        $this->cliEcho("\n");
                        $this->cliEcho("All tables successfully created...\n\n", Command::COLOR_GREEN, Command::STYLE_BOLD);
                        $this->cliEcho("Setting up initial scope... \n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        Context::reinitializeI18n('en_US');
                        $scope = new Scope();
                        $scope->setName('The default scope');
                        $scope->addHostname('*');
                        $scope->setEnabled();
                        Context::setScope($scope);
                        $scope->save();
                        Settings::saveSetting('language', 'en_US');
                        $this->cliEcho("Initial scope setup successfully... \n\n", Command::COLOR_GREEN, Command::STYLE_BOLD);

                    }

                    try {
                        if ($this->connect_mode) {
                            $this->cliEcho("Skipping module installation ...\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        } else {
                            $this->cliEcho("Setting up modules... \n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            foreach (['publish', 'mailing', 'vcs_integration'] as $module) {
                                $this->cliEcho("Installing {$module}... \n");
                                Module::installModule($module);
                            }

                            $this->cliEcho("\n");
                            $this->cliEcho("All modules installed successfully...\n", Command::COLOR_GREEN, Command::STYLE_BOLD);
                            $this->cliEcho("\n");
                        }

                        $this->cliEcho("Finishing installation... \n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                        $installed_string = Settings::getVersion() . ', installed ' . date('d.m.Y H:i');

                        if ((file_exists(PACHNO_PATH . 'installed') && !is_writable(PACHNO_PATH . 'installed')) ||
                            (!file_exists(PACHNO_PATH . 'installed') && !is_writable(PACHNO_PATH))) {
                            $this->cliEcho("\n");
                            $this->cliEcho("Could not create the 'installed' file.\n", 'red', Command::STYLE_BOLD);
                            $this->cliEcho("Please create the file ");
                            $this->cliEcho(PACHNO_PATH . "installed\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho("with the following line inside:\n");
                            $this->cliEcho($installed_string, 'blue', Command::STYLE_BOLD);
                            $this->cliEcho("\n");
                            $this->cliEcho("This can be done by running the following command when installation has finished:\n");
                            $this->cliEcho('echo "' . $installed_string . '" > ' . PACHNO_PATH . 'installed', Command::COLOR_WHITE, Command::STYLE_BOLD);
                            $this->cliEcho("\n");
                            $this->cliEcho("Press ENTER to continue ... ");
                            $this->pressEnterToContinue();
                            $this->cliEcho("\n");
                            $this->cliEcho("\n");
                        } else {
                            file_put_contents(PACHNO_PATH . 'installed', $installed_string);
                        }
                        $this->cliEcho("The installation was completed successfully!\n", Command::COLOR_GREEN, Command::STYLE_BOLD);
                        $this->cliEcho("\nTo use Pachno, access http://example.com" . $url_subdir . "index.php with a web-browser.\n");
                        $this->cliEcho("The default username is ") . $this->cliEcho('Administrator') . $this->cliEcho(' and the password is ') . $this->cliEcho('admin');
                        $this->cliEcho("\n\nFor support, please visit ") . $this->cliEcho('https://pachno.com/', 'blue', 'underline');
                        $this->cliEcho("\n");
                    } catch (Exception $e) {
                        throw new Exception("Could not install the $module module:\n" . $e->getMessage());
                    }

                }
            } catch (Exception $e) {
                $this->cliEcho("\n\nThe installation was interrupted\n", 'red');
                $this->cliEcho($e->getMessage() . "\n");
            }

            $this->cliEcho("\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'install';
            $this->_description = "Run the installation routine";
            $this->_b2db_config_file = PACHNO_CONFIGURATION_PATH . "b2db.yml";
            $this->addOptionalArgument('accept_license', 'Set to "yes" to auto-accept license');
            $this->addOptionalArgument('url_subdir', 'Specify URL subdirectory');
            $this->addOptionalArgument('use_existing_db_info', 'Set to "yes" to use existing db information if available');
            $this->addOptionalArgument('enable_all_modules', 'Set to "yes" to install all modules');
            $this->addOptionalArgument('setup_htaccess', 'Set to "yes" to autoconfigure .htaccess file');
            $this->addOptionalArgument('only_connect', 'Set to "yes" to connect to existing database and only generate configuration files');
        }

    }
