<?php

    namespace pachno\core\modules\installation\controllers;

    use b2db\AnnotationSet;
    use b2db\Core;
    use Exception;
    use pachno\core\entities\Scope;
    use pachno\core\entities\tables\Users;
    use pachno\core\framework;
    use pachno\core\modules\installation\Upgrade;
    use pachno\core\modules\installation\upgrade_4112\UsersTable;
    use ReflectionClass;
    use ReflectionProperty;
    use Spyc;
    use const PACHNO_CONFIGURATION_PATH;

    class Main extends framework\Action
    {

        /**
         * Sample docblock used to test docblock retrieval
         */
        protected $_sampleproperty;

        /**
         * Check or cache folder exists, otherwise create one with proper rights
         **/
        public static function createCacheFolder()
        {
            $dir = __DIR__ . '/../../../cache';
            if (!file_exists($dir)) {
                mkdir($dir);
            }
        }

        /**
         * Check or symlink to the images in the oxygen theme folder exists, otherwise create it.
         **/
        public static function checkAssetSymlink()
        {
            $rootDir = __DIR__ . '/../../../../';
            $link = $rootDir . 'public/images';
            if (!file_exists($link)) {
                $target = $rootDir . 'themes/oxygen/images';
                symlink($target, $link);
            }
        }

        public function preExecute(framework\Request $request, $action)
        {
            $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);
        }

        /**
         * Runs the installation action
         *
         * @param framework\Request $request The request object
         *
         * @return null
         */
        public function runInstallIntro(framework\Request $request)
        {
            $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);

            if (($step = $request['step']) && $step >= 1 && $step <= 6) {
                if ($step >= 5) {
                    $scope = new Scope(1);
                    framework\Context::setScope($scope);
                }

                return $this->redirect('installStep' . $step);
            }
        }

        /**
         * Runs the action for the first step of the installation
         *
         * @param framework\Request $request The request object
         *
         * @return null
         */
        public function runInstallStep1(framework\Request $request)
        {
            $this->all_well = true;
            $this->base_folder_perm_ok = true;
            $this->cache_folder_perm_ok = true;
            $this->pachno_folder_perm_ok = true;
            $this->b2db_param_file_ok = true;
            $this->b2db_param_folder_ok = true;
            $this->pdo_ok = true;
            $this->mysql_ok = true;
            $this->pgsql_ok = true;
            $this->gd_ok = true;
            $this->mb_ok = true;
            $this->php_ok = true;
            $this->pcre_ok = true;
            $this->dom_ok = true;
            $this->docblock_ok = false;
            $this->php_ver = PHP_VERSION;
            $this->pcre_ver = PCRE_VERSION;

            if (version_compare($this->php_ver, '7.1.0', 'lt')) {
                $this->php_ok = false;
                $this->all_well = false;
            }
            if (version_compare($this->pcre_ver, '7', 'le')) {
                $this->pcre_ok = false;
                $this->all_well = false;
            }
            if (file_exists(PACHNO_CONFIGURATION_PATH . 'b2db.yml') && !is_writable(PACHNO_CONFIGURATION_PATH . 'b2db.yml')) {
                $this->b2db_param_file_ok = false;
                $this->all_well = false;
            } elseif (!file_exists(PACHNO_CONFIGURATION_PATH . 'b2db.yml') && !is_writable(PACHNO_CONFIGURATION_PATH)) {
                $this->b2db_param_folder_ok = false;
                $this->b2db_param_file_ok = false;
                $this->all_well = false;
            }
            if (!is_writable(PACHNO_PATH)) {
                $this->base_folder_perm_ok = false;
                $this->all_well = false;
            }

            if (!is_writable(PACHNO_PATH)) {
                $this->base_folder_perm_ok = false;
                $this->all_well = false;
            }

            if (!is_writable(PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . DS)) {
                $this->pachno_folder_perm_ok = false;
                $this->all_well = false;
            }
            if (!class_exists('PDO')) {
                $this->pdo_ok = false;
                $this->all_well = false;
            }
            if (!class_exists('DOMDocument')) {
                $this->dom_ok = false;
                $this->all_well = false;
            }
            if (!extension_loaded('pdo_mysql')) {
                $this->mysql_ok = false;
            }
            if (!extension_loaded('pdo_pgsql')) {
                $this->pgsql_ok = false;
            }
            if (!extension_loaded('gd')) {
                $this->gd_ok = false;
            }
            if (!extension_loaded('mbstring')) {
                $this->mb_ok = false;
                $this->all_well = false;
            }

            $reflection = new ReflectionProperty(get_class($this), '_sampleproperty');
            $docblock = $reflection->getDocComment();
            if ($docblock) {
                $this->docblock_ok = true;
            } else {
                $this->all_well = false;
            }

            if (!$this->mysql_ok && !$this->pgsql_ok) {
                $this->all_well = false;
            }
        }

        /**
         * Runs the action for the second step of the installation
         * where you enter database information
         *
         * @param framework\Request $request The request object
         *
         * @return null
         */
        public function runInstallStep2(framework\Request $request)
        {
            $this->preloaded = false;
            $this->selected_connection_detail = 'custom';

            if (!$this->error) {
                try {
                    $b2db_filename = PACHNO_CONFIGURATION_PATH . "b2db.yml";
                    if (file_exists($b2db_filename)) {
                        $b2db_config = Spyc::YAMLLoad($b2db_filename);
                        Core::initialize($b2db_config);
                    }
                } catch (Exception $e) {

                }
                if (Core::isInitialized()) {
                    $this->preloaded = true;
                    $this->username = Core::getUsername();
                    $this->password = Core::getPassword();
                    $this->dsn = Core::getDSN();
                    $this->hostname = Core::getHostname();
                    $this->port = Core::getPort();
                    $this->b2db_dbtype = Core::getDriver();
                    $this->db_name = Core::getDatabaseName();
                }
            }
        }

        /**
         * Runs the action for the third step of the installation
         * where it tests the connection, sets up the database and the initial scope
         *
         * @param framework\Request $request The request object
         *
         * @return null
         */
        public function runInstallStep3(framework\Request $request)
        {
            $this->selected_connection_detail = $request['connection_type'];
            try {
                if ($this->username = $request['db_username']) {
                    Core::setUsername($this->username);
                    Core::setTablePrefix($request['db_prefix']);
                    if ($this->password = $request->getRawParameter('db_password'))
                        Core::setPassword($this->password);

                    if ($this->selected_connection_detail == 'dsn') {
                        if (($this->dsn = $request['db_dsn']) != '')
                            Core::setDSN($this->dsn);
                        else
                            throw new Exception('You must provide a valid DSN');
                    } else {
                        if ($this->db_type = $request['db_type']) {
                            Core::setDriver($this->db_type);
                            if ($this->db_hostname = $request['db_hostname'])
                                Core::setHostname($this->db_hostname);
                            else
                                throw new Exception('You must provide a database hostname');

                            if ($this->db_port = $request['db_port'])
                                Core::setPort($this->db_port);

                            if ($this->db_databasename = $request['db_name'])
                                Core::setDatabaseName($this->db_databasename);
                            else
                                throw new Exception('You must provide a database to use');
                        } else {
                            throw new Exception('You must provide a database type');
                        }
                    }

                    try {
                        Core::doConnect();
                    } catch (\b2db\Exception $e) {
                        throw new Exception('There was an error connecting to the database: ' . $e->getMessage());
                    }

                    if (Core::getDatabaseName() == '')
                        throw new Exception('You must provide a database to use');

                    Core::saveConnectionParameters(PACHNO_CONFIGURATION_PATH . "b2db.yml");
                } else {
                    throw new Exception('You must provide a database username');
                }

                // Create v4 tables
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
                sort($tables_created);
                $this->tables_created = $tables_created;
            } catch (Exception $e) {
                $this->error = $e->getMessage();
            }
            $server_type = strtolower(trim($_SERVER['SERVER_SOFTWARE']));
            switch (true) {
                case (stripos($server_type, 'apache') !== false):
                    $this->server_type = 'apache';
                    break;
                case (stripos($server_type, 'nginx') !== false):
                    $this->server_type = 'nginx';
                    break;
                case (stripos($server_type, 'iis') !== false):
                    $this->server_type = 'iis';
                    break;
                default:
                    $this->server_type = 'unknown';
            }
            $dirname = dirname($_SERVER['PHP_SELF']);
            if (mb_stristr(PHP_OS, 'WIN')) {
                $dirname = str_replace("\\", "/", $dirname); /* Windows adds a \ to the URL which we don't want */
            }

            $this->dirname = ($dirname != '/') ? $dirname . '/' : $dirname;
        }

        /**
         * Runs the action for the fourth step of the installation
         * where it loads fixtures and saves settings for url
         *
         * @param framework\Request $request The request object
         *
         * @return null
         */
        public function runInstallStep4(framework\Request $request)
        {
            try {
                framework\Logging::log('Initializing language support');
                framework\Context::reinitializeI18n('en_US');

                framework\Logging::log('Loading fixtures for default scope');
                $scope = new Scope();
                $scope->addHostname('*');
                $scope->setName('The default scope');
                $scope->setEnabled(true);
                framework\Context::setScope($scope);
                $scope->save();

                framework\Settings::saveSetting('language', 'en_US', 'core', 1);

                $this->htaccess_error = false;
                $this->htaccess_ok = (bool)$request['apache_autosetup'];

                if ($request['apache_autosetup']) {
                    if (!is_writable(PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . '/') || (file_exists(PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . '/.htaccess') && !is_writable(PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . '/.htaccess'))) {
                        $this->htaccess_error = 'Permission denied when trying to save the file <span class="command_box">[main folder]/' . PACHNO_PUBLIC_FOLDER_NAME . '/.htaccess</span>';
                    } else {
                        $content = str_replace('###PUT URL SUBDIRECTORY HERE###', $request['url_subdir'], file_get_contents(PACHNO_CORE_PATH . '/templates/htaccess.template'));
                        file_put_contents(PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . '/.htaccess', $content);
                        if (file_get_contents(PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . '/.htaccess') != $content) {
                            $this->htaccess_error = true;
                        }
                    }
                }
            } catch (Exception $e) {
                $this->error = $e->getMessage();
                throw $e;
            }
        }

        /**
         * Runs the action for the fifth step of the installation
         * where it enables modules on demand
         *
         * @param framework\Request $request The request object
         *
         * @return null
         */
        public function runInstallStep5(framework\Request $request)
        {
            try {
                $password = trim($request['password']);
                if ($password !== trim($request['password_repeat']))
                    throw new Exception("Passwords don't match");

                $this->password = $password;
                $user = Users::getTable()->getByUsername('administrator');
                $username = trim(strtolower($request['username']));
                if ($username) $user->setUsername($username);
                $user->setRealname($request['name']);
                $user->setPassword($request['password']);
                $user->setEmail($request['email']);
                $user->save();

                $this->user = $user;
            } catch (Exception $e) {
                $this->error = $e->getMessage();
            }
        }

        /**
         * Runs the action for the sixth step of the installation
         * where it finalizes the installation
         *
         * @param framework\Request $request The request object
         *
         * @return null
         */
        public function runInstallStep6(framework\Request $request)
        {
            $installed_string = framework\Settings::getVersion() . ', installed ' . date('d.m.Y H:i');

            if (file_put_contents(PACHNO_PATH . 'installed', $installed_string) === false) {
                $this->error = "Couldn't write to the main directory. Please create the file " . PACHNO_PATH . "installed manually, with the following content: \n" . $installed_string;
            }
            if (file_exists(PACHNO_PATH . 'upgrade') && !unlink(PACHNO_PATH . 'upgrade')) {
                $this->error = "Couldn't remove the file " . PACHNO_PATH . "upgrade. Please remove this file manually.";
            }
            framework\Context::clearRoutingCache();
        }

        public function runUpgrade(framework\Request $request)
        {
            list ($this->current_version, $this->upgrade_available) = framework\Settings::getUpgradeStatus();

            $this->upgrade_complete = false;
            $this->adminusername = UsersTable::getTable()->getAdminUsername();
            $this->requires_password_reset = !in_array($this->current_version, ['4.2.0', '4.2.1']);
            try {
                if ($this->upgrade_available) {
                    $this->permissions_ok = false;
                    if (is_writable(PACHNO_PATH . 'installed') && is_writable(PACHNO_PATH . 'upgrade')) {
                        $this->permissions_ok = true;
                    }
                }

                if ($this->upgrade_available && $request->isPost()) {
                    $upgrader = new Upgrade();
                    $this->upgrade_complete = $upgrader->upgrade($request);

                    if ($this->upgrade_complete) {
                        $this->current_version = framework\Settings::getVersion(false, false);
                        $this->upgrade_available = false;
                    }
                } elseif ($this->upgrade_complete) {
                    $this->forward(framework\Context::getRouting()->generate('home'));
                }
            } catch (Exception $e) {
                $this->error = $e->getMessage();
            }
        }

    }
