<?php

    namespace pachno\core\modules\auth_ldap\cli;

    use Exception;
    use pachno\core\framework;
    use pachno\core\framework\cli\Command;

    /**
     * Implementation of CLI command for importing LDAP users into Pachno.
     *
     * @author Branko Majic <branko@majic.rs>
     * @version 4.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage auth_ldap
     */


    /**
     * CLI command for performing import of LDAP users into Pachno.
     *
     * @package pachno
     * @subpackage auth_ldap
     */
    class Import extends Command
    {
        public const ERROR = 1;

        /**
         * Executes the command.
         *
         */
        public function do_execute()
        {
            $i18n = framework\Context::getI18n();

            try {
                $statistics = framework\Context::getModule('auth_ldap')->importAndUpdateUsers();
            } catch (Exception $e) {
                $this->cliEcho($i18n->__("Import failed") . ": " . $e->getMessage(), 'red');
                $this->cliEcho("\n");
                exit(self::ERROR);
            }

            $this->cliEcho($i18n->__('Import successful! Imported %imported users and updated %updated users out of total %total valid users found in LDAP',
                ['%imported' => $statistics['imported'],
                    '%updated' => $statistics['updated'],
                    '%total' => $statistics['total']]));
            $this->cliEcho("\n");
        }

        /**
         * Sets-up the command name and description.
         */
        protected function _setup()
        {
            $this->_command_name = 'import';
            $this->_description = 'Import new and update existing users based on user information from LDAP directory';
        }
    }