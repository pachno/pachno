<?php

    namespace pachno\core\modules\auth_ldap\cli;

    use pachno\core\framework;
    use pachno\core\framework\cli\Command;

    /**
     * Implementation of CLI command for testing LDAP module connection and
     * configuration.
     *
     * @author Branko Majic <branko@majic.rs>
     * @version 4.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage auth_ldap
     */


    /**
     * CLI command for testing LDAP module connection and configuration.
     *
     * @package pachno
     * @subpackage auth_ldap
     */
    class Test extends Command
    {
        const ERROR = 1;

        /**
         * Executes the command.
         *
         */
        public function do_execute()
        {
            $i18n = framework\Context::getI18n();

            $result = framework\Context::getModule('auth_ldap')->testConnection();

            if ($result['success'] === false) {
                $this->cliEcho($result['summary'] . ': ' . $result['details'], 'red');
                $this->cliEcho("\n");
                exit(self::ERROR);
            }

            $this->cliEcho($result['summary']);
            $this->cliEcho("\n");
        }

        /**
         * Sets-up the command name and description.
         */
        protected function _setup()
        {
            $this->_command_name = 'test';
            $this->_description = 'Tests LDAP configuration and connectivity. WARNING: HTTP Integrated Authentication and availability of currently logged-in user cannot be tested in CLI!';
        }
    }