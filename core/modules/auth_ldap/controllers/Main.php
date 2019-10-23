<?php

    namespace pachno\core\modules\auth_ldap\controllers;

    use Exception;
    use pachno\core\framework;
    use pachno\core\framework\Request;

    /**
     * actions for the ldap_authentication module
     */
    class Main extends framework\Action
    {

        /**
         * Test the LDAP connection
         *
         * @param Request $request
         */
        public function runTestConnection(Request $request)
        {
            $result = framework\Context::getModule('auth_ldap')->testConnection();

            if ($result['success'] === true) {
                framework\Context::setMessage('module_message', $result['summary']);
                $this->forward(framework\Context::getRouting()->generate('configure_module', ['config_module' => 'auth_ldap']));
            } else {
                framework\Context::setMessage('module_error', $result['summary']);
                framework\Context::setMessage('module_error_details', $result['details']);
                $this->forward(framework\Context::getRouting()->generate('configure_module', ['config_module' => 'auth_ldap']));
            }
        }

        /**
         * Prune users from users table who aren't in LDAP
         *
         * @param Request $request
         */
        public function runPruneUsers(Request $request)
        {
            try {
                $statistics = framework\Context::getModule('auth_ldap')->pruneUsers();
            } catch (Exception $e) {
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Pruning failed'));
                framework\Context::setMessage('module_error_details', $e->getMessage());
                $this->forward(framework\Context::getRouting()->generate('configure_module', ['config_module' => 'auth_ldap']));
            }

            framework\Context::setMessage('module_message', framework\Context::getI18n()->__('Pruning successful! %deleted users deleted',
                ['%deleted' => $statistics['deleted']]));
            $this->forward(framework\Context::getRouting()->generate('configure_module', ['config_module' => 'auth_ldap']));
        }

        /**
         * Import all valid users
         *
         * @param Request $request
         */
        public function runImportUsers(Request $request)
        {
            try {
                $statistics = framework\Context::getModule('auth_ldap')->importAndUpdateUsers();
            } catch (Exception $e) {
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Import failed'));
                framework\Context::setMessage('module_error_details', $e->getMessage());
                $this->forward(framework\Context::getRouting()->generate('configure_module', ['config_module' => 'auth_ldap']));
            }

            framework\Context::setMessage(
                'module_message',
                framework\Context::getI18n()->__('Import successful! Imported %imported users and updated %updated users out of total %total valid users found in LDAP',
                    ['%imported' => $statistics['imported'],
                        '%updated' => $statistics['updated'],
                        '%total' => $statistics['total']]));

            $this->forward(framework\Context::getRouting()->generate('configure_module', ['config_module' => 'auth_ldap']));
        }

    }
