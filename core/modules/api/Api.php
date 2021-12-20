<?php

    namespace pachno\core\modules\api;

    use pachno\core\framework;

    /**
     * The Bug Genie API module
     *
     * @author
     * @version 0.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno/modules/api
     * @subpackage core
     */

    /**
     * The Bug Genie API module
     *
     * @package pachno/modules/api
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Modules")
     */
    class Api extends framework\CoreModule
    {

        protected $_name = 'api';
        protected $_longname = 'External API module';
        protected $_description = 'Enables integration with third party tools and plugins';
        protected $_module_config_title = 'API';
        protected $_module_config_description = 'Set up the API module from this section';
        protected $_has_account_settings = false;

        /**
         * Return an instance of this module
         *
         * @return Api
         */
        public static function getModule(): Api
        {
            return framework\Context::getModule('api');
        }

        protected function _initialize()
        {
        }

        protected function _addListeners()
        {
        }

        protected function _install($scope)
        {
        }

        protected function _loadFixtures($scope)
        {
        }

        protected function _uninstall()
        {
        }

    }
