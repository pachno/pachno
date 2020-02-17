<?php

    namespace pachno\core\framework;

    use Exception;
    use pachno\core\framework\interfaces\ModuleInterface;

    /**
     * Core module class
     *
     * @package pachno
     * @subpackage mvc
     */
    abstract class CoreModule implements interfaces\ModuleInterface
    {

        protected $name;

        protected $_longname;

        protected $_description;

        protected $_availablepermissions = [];

        protected $_has_config_settings = false;

        protected $_module_config_title = '';

        protected $_module_config_description = '';

        public function __construct($name)
        {
            $this->name = $name;
        }

        public function getLongName()
        {
            return $this->_longname;
        }

        public function getDescription()
        {
            return $this->_description;
        }

        public function getType()
        {
            return ModuleInterface::MODULE_CORE;
        }

        public function hasConfigSettings()
        {
            return $this->_has_config_settings;
        }

        public function getConfigTitle()
        {
            return $this->_module_config_title;
        }

        public function getConfigDescription()
        {
            return $this->_module_config_description;
        }

        public function addAvailablePermission($permission_name, $description, $target = 0)
        {
            $this->_availablepermissions[$permission_name] = ['description' => $description, 'target_id' => $target];
        }

        public function getAvailablePermissions()
        {
            return $this->_availablepermissions;
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

        public function isEnabled()
        {
            return true;
        }

        /**
         * Save a setting
         *
         * @param string $name The settings key / name of the setting to store
         * @param mixed $value The value to store
         * @param int $scope A scope id (or 0 to apply to all scopes)
         * @param int $uid A user id to save settings for
         *
         * @throws Exception
         */
        public function saveSetting($name, $value, $uid = 0, $scope = null)
        {
            $scope = ($scope === null) ? Context::getScope()->getID() : $scope;
            Settings::saveSetting($name, $value, $this->getName(), $scope, $uid);
        }

        public function getName()
        {
            return $this->name;
        }

        public function getSetting($name, $uid = 0)
        {
            return Settings::get($name, $this->getName(), Context::getScope()->getID(), $uid);
        }

        public function deleteSetting($name, $uid = 0, $scope = null)
        {
            Settings::deleteSetting($name, $this->getName(), $scope, $uid);
        }

        public function hasAccountSettings()
        {
            return false;
        }

        protected function _addListeners() {}

        public function initialize()
        {
            $this->_addListeners();
        }

        public function getAccountSettingsLogo()
        {
        }

        public function getAccountSettingsName()
        {
        }

        public final function install($scope)
        {
            $this->_install($scope);
            $this->_loadFixtures($scope);
        }

        protected function _install($scope)
        {
        }

        protected function _loadFixtures($scope)
        {
        }

    }
