<?php

    namespace pachno\core\framework\interfaces;

    interface ModuleInterface
    {

        public const MODULE_CORE = 0;
        public const MODULE_NORMAL = 1;
        public const MODULE_AUTH = 2;

        public function saveSetting($name, $value, $uid = 0, $scope = null);

        public function getSetting($name, $uid = 0);

        public function deleteSetting($name, $uid = 0, $scope = null);

        public function hasAccountSettings();

        public function initialize();

    }
