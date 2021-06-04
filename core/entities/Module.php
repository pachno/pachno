<?php

    namespace pachno\core\entities;

    use b2db\Core;
    use b2db\Row;
    use b2db\Update;
    use Exception;
    use GuzzleHttp\Client as GuzzleClient;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\Modules;
    use pachno\core\framework;
    use ZipArchive;

    /**
     * Module class, extended by all pachno modules
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Module class, extended by all pachno modules
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Modules")
     */
    abstract class Module extends IdentifiableScoped implements framework\interfaces\ModuleInterface
    {

        protected static $_permissions = [];

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_classname = '';

        /**
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enabled = false;

        /**
         * @var string
         * @Column(type="string", length=10)
         */
        protected $_version = '';

        protected $_longname = '';

        protected $_shortname = '';

        protected $_showinconfig = false;

        protected $_module_config_title = '';

        protected $_module_config_description = '';

        protected $_description = '';

        protected $_availablepermissions = [];

        protected $_settings = [];

        protected $_routes = [];

        protected $_has_account_settings = false;

        protected $_account_settings_name = null;

        protected $_account_settings_logo = null;

        protected $_has_config_settings = false;

        /**
         * Installs a module
         *
         * @param string $module_name the module key
         *
         * @return boolean Whether the install succeeded or not
         */
        public static function installModule($module_name, $scope = null)
        {
            $scope_id = ($scope) ? $scope->getID() : framework\Context::getScope()->getID();
            if (!framework\Context::getScope() instanceof Scope) throw new Exception('No scope??');

            framework\Logging::log('installing module ' . $module_name);
            $transaction = Core::startTransaction();
            try {
                $module = tables\Modules::getTable()->installModule($module_name, $scope_id);
                $module->install($scope_id);
                $transaction->commitAndEnd();
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
            framework\Logging::log('done (installing module ' . $module_name . ')');

            return $module;
        }

        public static function unloadModule($module_key)
        {
            $module = framework\Context::getModule($module_key);
            $module->disable();
            unset($module);
            framework\Context::unloadModule($module_key);
        }

        public function disable()
        {
            self::disableModule($this->getID());
            $this->_enabled = false;
        }

        public static function disableModule($module_id)
        {
            tables\Modules::getTable()->disableModuleByID($module_id);
        }

        public static function removeModule($module_id)
        {
            tables\Modules::getTable()->removeModuleByID($module_id);
        }

        /**
         * @param $module_key
         *
         * @throws framework\exceptions\ModuleDownloadException
         */
        public static function downloadModule($module_key)
        {
            self::downloadPlugin('addon', $module_key);
        }

        /**
         * @param $plugin_type
         * @param $plugin_key
         *
         * @throws framework\exceptions\ModuleDownloadException
         */
        public static function downloadPlugin($plugin_type, $plugin_key)
        {
//            try {
                $client = new GuzzleClient(['base_uri' => 'https://thebuggenie.com']);
//                $response = $client->get('/' . $plugin_type . 's/' . $plugin_key . '.json');
//
//                if ($response->getStatusCode() === 200) {
//                    $plugin_json = json_decode($response->getBody());
//                }
//            } catch (Exception $e) {
//                throw $e;
//            }

//            if (isset($plugin_json) && $plugin_json !== false) {
                $filename = PACHNO_CACHE_PATH . $plugin_type . '_' . $plugin_key . '.zip';
                $response = $client->get('/' . $plugin_type . 's/' . $plugin_key . '/download');
                if ($response->getStatusCode() != 200) {
                    throw new framework\exceptions\ModuleDownloadException("", framework\exceptions\ModuleDownloadException::JSON_NOT_FOUND);
                }
                file_put_contents($filename, $response->getBody());
                $module_zip = new ZipArchive();
                $module_zip->open($filename);
                switch ($plugin_type) {
                    case 'addon':
                        $target_folder = PACHNO_MODULES_PATH;
                        break;
                    case 'theme':
                        $target_folder = PACHNO_PATH . 'themes';
                        break;
                }
                if (!is_writable($target_folder)) {
                    throw new framework\exceptions\ModuleDownloadException("", framework\exceptions\ModuleDownloadException::READONLY_TARGET);
                }
                $module_zip->extractTo(realpath($target_folder));
                $module_zip->close();
//            } else {
//                throw new framework\exceptions\ModuleDownloadException("", framework\exceptions\ModuleDownloadException::FILE_NOT_FOUND);
//            }
        }

        /**
         * @param $module_key
         *
         * @throws framework\exceptions\ModuleDownloadException
         */
        public static function downloadTheme($theme_key)
        {
            self::downloadPlugin('theme', $theme_key);
        }

        /**
         * Class constructor
         */
        final public function _construct(Row $row, string $foreign_key = null): void
        {
            if ($this->_version != $row->get(tables\Modules::VERSION)) {
                throw new Exception('This module must be upgraded to the latest version');
            }
        }

        final public function install($scope)
        {
            try {
                framework\Context::clearRoutingCache();
                framework\Context::clearPermissionsCache();
                $this->_install($scope);
                $b2db_classpath = PACHNO_MODULES_PATH . $this->_name . DS . 'entities' . DS . 'tables';

                if ($scope == framework\Settings::getDefaultScopeID() && is_dir($b2db_classpath)) {
                    $b2db_classpath_handle = opendir($b2db_classpath);
                    while ($table_class_file = readdir($b2db_classpath_handle)) {
                        if (($tablename = mb_substr($table_class_file, 0, mb_strpos($table_class_file, '.'))) != '') {
                            Core::getTable("\\pachno\\modules\\" . $this->_name . "\\entities\\tables\\" . $tablename)->create();
                        }
                    }
                }
                $this->_loadFixtures($scope);
            } catch (Exception $e) {
                throw $e;
            }
        }

        protected function _install($scope)
        {
        }

        protected function _loadFixtures($scope)
        {
        }

        public function log($message, $level = 1)
        {
            framework\Logging::log($message, $this->getName(), $level);
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        public function enable()
        {
            $update = new Update();
            $update->add(tables\Modules::ENABLED, 1);
            tables\Modules::getTable()->rawUpdateById($update, $this->getID());
            $this->_enabled = true;
        }

        final public function upgrade()
        {
            framework\Context::clearRoutingCache();
            framework\Context::clearPermissionsCache();
            $this->_upgrade();
            $this->save();
            Modules::getTable()->setModuleVersion($this->_name, static::VERSION);
        }

        protected function _upgrade()
        {
        }

        final public function uninstall($scope = null)
        {
            $this->_uninstall();
            $this->delete();
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            framework\Settings::deleteModuleSettings($this->getName(), $scope);
            framework\Context::deleteModulePermissions($this->getName(), $scope);
            framework\Context::clearRoutingCache();
            framework\Context::clearPermissionsCache();
        }

        protected function _uninstall()
        {
        }

        public function __toString(): string
        {
            return $this->_name;
        }

        public function __call($func, $args)
        {
            throw new Exception('Trying to call function ' . $func . '() in module ' . $this->_name . ', but the function does not exist');
        }

        public function getLongName()
        {
            return $this->_longname;
        }

        public function setLongName($name)
        {
            $this->_longname = $name;
        }

        public function addAvailablePermission($permission_name, $description, $target = 0)
        {
            $this->_availablepermissions[$permission_name] = ['description' => $description, 'target_id' => $target];
        }

        public function getAvailablePermissions()
        {
            return $this->_availablepermissions;
        }

        public function getAvailableCommandLineCommands()
        {
            return [];
        }

        public function getConfigTitle()
        {
            return $this->_module_config_title;
        }

        public function getConfigDescription()
        {
            return $this->_module_config_description;
        }

        public function getVersion()
        {
            return $this->_version;
        }

        public function getType()
        {
            return framework\interfaces\ModuleInterface::MODULE_NORMAL;
        }

        /**
         * Shortcut for the global settings function
         *
         * @param string $name the name of the setting
         * @param integer $uid the uid for the user to check
         *
         * @return mixed
         */
        public function getSetting($name, $uid = 0)
        {
            return framework\Settings::get($name, $this->getName(), framework\Context::getScope()->getID(), $uid);
        }

        public function saveSetting($name, $value, $uid = 0, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            framework\Settings::saveSetting($name, $value, $this->getName(), $scope, $uid);
        }

        public function deleteSetting($name, $uid = 0, $scope = null)
        {
            framework\Settings::deleteSetting($name, $this->getName(), $scope, $uid);
        }

        public function addRoute($key, $url, $function, $params = [], $options = [], $module_name = null)
        {
            $module_name = ($module_name !== null) ? $module_name : $this->getName();
            $this->_routes[] = [$key, $url, $module_name, $function, $params, $options];
        }

        final public function initialize()
        {
            $this->_initialize();
            if ($this->isEnabled()) {
                $this->_addListeners();
                $this->_addAvailablePermissions();
            }
        }

        abstract protected function _initialize();

        /**
         * Returns whether the module is enabled
         *
         * @return boolean
         */
        public function isEnabled()
        {
            /* Outdated modules can not be used */
            if ($this->isOutdated()) {
                return false;
            }

            return $this->_enabled;
        }

        /**
         * Returns whether the module is out of date
         *
         * @return boolean
         */
        public function isOutdated()
        {
            if ($this->_version != static::VERSION) {
                return true;
            }

            return false;
        }

        protected function _addListeners()
        {
        }

        protected function _addAvailablePermissions()
        {
        }

        public function getDescription()
        {
            return $this->_description;
        }

        public function setDescription($description)
        {
            $this->_description = $description;
        }

        public function loadHelpTitle($topic)
        {
            return $topic;
        }

        public function setHasAccountSettings($val = true)
        {
            $this->_has_account_settings = (bool)$val;
        }

        public function hasAccountSettings()
        {
            return $this->_has_account_settings;
        }

        public function getAccountSettingsName()
        {
            return framework\Context::geti18n()->__($this->_account_settings_name);
        }

        public function setAccountSettingsName($name)
        {
            $this->_account_settings_name = $name;
        }

        public function getAccountSettingsLogo()
        {
            return $this->_account_settings_logo;
        }

        public function setAccountSettingsLogo($logo)
        {
            $this->_account_settings_logo = $logo;
        }

        public function hasConfigSettings()
        {
            /* If the module is outdated, we may not access its settings */
            if ($this->isOutdated()) {
                return false;
            }

            return $this->_has_config_settings;
        }

        public function hasProjectAwareRoute()
        {
            return false;
        }

        public function hasFontAwesomeIcon()
        {
            return true;
        }

        public function getFontAwesomeIcon()
        {
            return 'puzzle-piece';
        }

        public function getFontAwesomeStyle()
        {
            return 'fas';
        }

        public function getFontAwesomeColor()
        {
            return 'mediumseagreen';
        }

        public function getTabKey()
        {
            return $this->getName();
        }

        public function postConfigSettings(framework\Request $request)
        {

        }

        public function postAccountSettings(framework\Request $request)
        {

        }

    }
