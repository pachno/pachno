<?php

    namespace pachno\core\modules\auth_ldap;

    use Exception;
    use pachno\core\entities\User;
    use pachno\core\framework;
    use pachno\core\framework\CoreModule;
    use pachno\core\framework\Event;
    use pachno\core\framework\interfaces\AuthenticationProvider;

    /**
     * LDAP Authentication
     *
     * @author
     * @version 0.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package auth_ldap
     * @subpackage core
     */

    /**
     * LDAP Authentication
     *
     * @package auth_ldap
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Modules")
     */
    class Auth_ldap extends CoreModule
    {
        // Module information.
        public const VERSION = '2.0';

        protected $_name = 'auth_ldap';

        protected $_longname = 'LDAP Authentication';

        protected $_description = 'Allows authentication against a LDAP or Active Directory server';

        protected $_module_config_title = 'LDAP Authentication';

        protected $_module_config_description = 'Configure server connection settings';

        protected $_has_config_settings = true;

        /**
         * LDAP connection handler. Handler is used for control user LDAP
         * operations. Verification of user login credentials is performed via
         * dedicated connection.
         */
        protected $_connection;

        protected $_backend;

        /**
         * Returns module type.
         *
         * @return int
         */
        public final function getType()
        {
            return framework\interfaces\ModuleInterface::MODULE_AUTH;
        }

        /**
         * Processes configuration settings submitted by user.
         *
         * @param pachno\core\framework\Request $request
         *   Request containing submitted information.
         *
         * @throws Exception
         *   Thrown in case of an error with configuration settings provided via
         *   request
         */
        public function postConfigSettings(framework\Request $request)
        {
            $settings = ['hostname',                 // LDAP URL to connect to.
                'b_dn',                     // Base DN under which to search for entries.
                'dn_attr',                  // Name of the attribute that stores entry DN.

                'u_type',                   // Object class for user entries.
                'u_attr',                   // Username attribute.
                'e_attr',                   // E-mail attribute.
                'f_attr',                   // Full name attribute.
                'b_attr',                   // Buddy name attribute.

                'g_type',                   // Object class for group entries.
                'groups',                   // Comma-separated list of allowed group CN's.
                'g_attr',                   // Group member attribute.

                'control_user',             // DN for logging-in as control user.
                'control_pass',             // Password for logging-in as control user.

                'integrated_auth',          // Whether to use header value passed-on by web server as username.
                'integrated_auth_header'];  // Name of header passed-on by web server to use for username.

            // Verify presence of mandatory settings.
            $mandatory_settings = ['hostname' => framework\Context::geti18n()->__('LDAP connection URI must be specified.'),
                'b_dn' => framework\Context::geti18n()->__('Base DN must be specified.'),
                'dn_attr' => framework\Context::geti18n()->__('Object DN attribute must be specified.'),

                'u_type' => framework\Context::geti18n()->__('User object class must be specified.'),
                'u_attr' => framework\Context::geti18n()->__('User username attribute must be specified.')];

            foreach ($mandatory_settings as $setting => $error_message) {
                if ($request->getParameter($setting, '') === '') {
                    throw new Exception($error_message);
                }
            }

            // Verify that group object class and group members attribute is
            // specified if we need to restrict access based on group
            // membership.
            if ($request->getParameter('groups') !== '' && ($request->getParameter('g_type', '') === '' || $request->getParameter('g_attr', '') === '')) {
                throw new Exception(framework\Context::geti18n()->__('Both group object class and group member attribute must be specified if allowed groups are provided.'));
            }

            // Verify that header name is passed if HTTP integrated
            // authentication is enabled.
            if ($request->getParameter('integrated_auth') != 0 && $request->getParameter('integrated_auth_header', '') === '') {
                throw new Exception(framework\Context::geti18n()->__('HTTP header field must be specified if HTTP Integrated Authentication is enabled.'));
            }

            // Process each setting, and ensure defaults are set if values are
            // not provided for specific options.
            foreach ($settings as $setting) {
                if ($setting == 'integrated_auth') {
                    $this->saveSetting($setting, (int)$request->getParameter($setting, 0));
                } else {
                    $this->saveSetting($setting, $request->getParameter($setting));
                }
            }
        }

        /**
         * @return AuthenticationProvider
         */
        public function getAuthenticationBackend()
        {
            if (!$this->_backend instanceof LdapAuthenticationBackend) {
                $this->_backend = new LdapAuthenticationBackend();
                $this->_backend->setModule($this);
            }

            return $this->_backend;
        }

        /**
         * Handles event for fetching authentication backend.
         *
         * @param Event $event
         *   Event emitted by caller.
         *
         */
        public function listen_configurationAuthenticationMethod(Event $event)
        {
            if (framework\Settings::getAuthenticationBackendIdentifier() == $this->getName()) {
                $event->setReturnValue(framework\Action::AUTHENTICATION_METHOD_CORE);
            }
        }

        /**
         * Performs basic connectivity and settings test. The following is
         * tested:
         *
         * - Ability to connect and bind as control user.
         * - Availability of all allowed groups (if specified).
         * - Availability of users in LDAP directory.
         * - Integrated authentication (if enabled).
         * - Availability of currently logged-in user in LDAP directory.
         *
         * WARNING: Integrated authentication and availability of currently
         * logged-in user in LDAP directory are not tested when this method is
         * called from CLI.
         *
         * @return array
         *   Result of test. The following keys are available:
         *
         *   success
         *     A bool value denoting whether the test was successful or not.
         *
         *   summary
         *     Short summary of operation.
         *
         *   details
         *     Operation details. In case of errors usually includes an LDAP
         *     error message as well.
         */
        public function testConnection()
        {
            // We'll catch all exceptions and return them in a nice format for
            // consumption.
            try {
                // Verify connectivity.
                $this->_connectAndBindControlUser();

                // Verify that configured groups used for allowing access exist.
                $allowed_groups = $this->getSetting('groups');

                if ($allowed_groups != "") {
                    $member_attribute = $this->getSetting('g_attr');
                    $group_class = $this->getSetting('g_type');

                    $invalid_groups = [];
                    $attributes = [$member_attribute];

                    foreach (explode(',', $allowed_groups) as $group) {
                        $filter = $this->_prepareFilter("(&(cn=%group)(objectClass=%objectclass))", ['%group' => $group,
                            '%objectclass' => $group_class]);
                        $entries = $this->_search($filter, $attributes);

                        if ($entries['count'] != 1) {
                            $invalid_groups[] = $group;
                        }
                    }

                    if (count($invalid_groups) != 0) {
                        throw new Exception(framework\Context::geti18n()->__('Failed to validate groups (groups do not exist or multiple entries were found): %groups',
                            ['%groups' => implode(', ', $invalid_groups)]));
                    }
                }

                // Verify that we can locate users within the LDAP directory.
                $ldap_users = $this->getLDAPUserInformation();
                if (count($ldap_users) == 0) {
                    throw new Exception(framework\Context::geti18n()->__('Failed to locate any valid users in LDAP directory.'));
                }

                // Verify that header is present if HTTP integrated
                // authentication option is enabled.
                if (!framework\Context::isCLI() && $this->getSetting('integrated_auth')) {
                    $header_field = $this->getSetting('integrated_auth_header');

                    if (!isset($_SERVER[$header_field])) {
                        throw new Exception(framework\Context::getI18n()->__('HTTP integrated authentication is enabled but the %headerfield header is not being provided to Pachno. Please check your web server configuration.', ['%headerfield' => $header_field]));
                    }
                }

                // Verify that our current user can be located within the LDAP
                // directory. We don't have a user when in CLI.
                if (!framework\Context::isCLI()) {
                    $current_username = framework\Context::getUser()->getUsername();
                    $ldap_users = $this->getLDAPUserInformation($current_username);
                    if (count($ldap_users) != 1) {
                        throw new Exception(framework\Context::geti18n()->__('Failed to locate current user (%username) in LDAP directory. If you enable LDAP authentication, you may find yourself locked-out of the settings. All other checks have passed.',
                            ['%username' => $current_username]));
                    }
                }
            } catch (Exception $e) {
                return ['success' => false,
                    'summary' => framework\Context::getI18n()->__('LDAP connection test failed'),
                    'details' => $e->getMessage()];
            }

            return ['success' => true,
                'summary' => framework\Context::getI18n()->__('Connection test successful'),
                'details' => framework\Context::getI18n()->__('All LDAP connection tests have passed.')];
        }

        /**
         * Connects to LDAP server and binds as control user using the module
         * settings. Once a successful connection has been established, it is
         * cached for future use in property $_connection.
         *
         * In case an error occurs while trying to connect and bind, an
         * exception is thrown with appopriatelly set message.
         *
         *
         * @throws Exception
         *   Thrown in case it was not possible to connect and bind as control
         *   user.
         */
        protected function _connectAndBindControlUser()
        {
            // Only connect and bind if we have not been able to do so before.
            if ($this->_connection === null) {
                // Ignore PHP errors from this function (all PHP ldap_*
                // functions misuse PHP error handling). This function call does
                // not open an actual connection, it only verifies the URL
                // syntax.
                $connection_url = $this->getSetting('hostname');
                $connection = @ldap_connect($connection_url);

                if ($connection === false) {
                    throw new Exception(framework\Context::geti18n()->__('LDAP connection URL has invalid syntax.'));
                } else {
                    // Default LDAP protocol version used is 2, ensure we are
                    // using version 3 instead.
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

                    // Ignore PHP errors from this function (all PHP ldap_*
                    // functions misuse PHP error handling).
                    $bind_result = @ldap_bind($connection, $this->getSetting('control_user'), $this->getSetting('control_pass'));

                    if ($bind_result === false) {
                        throw new Exception(framework\Context::geti18n()->__('Failed to bind as control user. Error was: %error', ['%error' => ldap_error($connection)]));
                    }

                    // At this point we should have a fully-functioning
                    // connection. Store the value.
                    $this->_connection = $connection;
                }
            }
        }

        /**
         * Helper function for preparing filter for use with LDAP search. Takes
         * care of properly escaping values used in searches.
         *
         * The function itself does not perform any kind of syntax checks.
         *
         * Example use:
         *
         * _prepareFilter('(objectClass=%myclass)', ['%myclass' => 'inetOrgPerson'])");
         *
         * @param string $filter
         *   LDAP filter template to use.
         *
         * @param string[] $replacements
         *   Replacements to use in the filter template. Keys should be strings
         *   within filter that should be replaced. There is no specific format
         *   for keys that must be used, it is up to the caller to decide what
         *   to use as syntax for keys.
         *
         *
         * @return string
         *   Prepared filter.
         */
        protected function _prepareFilter($filter, $replacements)
        {
            if (!empty($replacements)) {
                $filter = str_replace(array_keys($replacements),
                    array_map(function ($value) {
                        return ldap_escape($value, null, LDAP_ESCAPE_FILTER);
                    },
                        array_values($replacements)), $filter);
            }

            return $filter;
        }

        /**
         * Runs a search on an LDAP server using the provided filter and
         * optional set of attributes to retrieve.
         *
         * Base DN for performing the search is taken from the module
         * configuration.
         *
         * @param string $filter
         *   LDAP filter to use for limiting the results. It is highly
         *   recommended to prepare filter using the Auth_ldap::_prepareFilter()
         *   method to avoid syntax errors due to restricted characters
         *   appearing within the values used in filter matching.
         *
         * @param string[] $attributes
         *   List of attributes to retrieve from the LDAP server for every
         *   matching entry. Set to null or empty array to retrieve all
         *   attributes
         *
         * @return array[]
         *   List of matching entries. Format is the same as returned by
         *   ldap_get_entries function.
         *
         * @throws Exception
         *   Thrown in case the LDAP search operation itself or retrieving the
         *   search results failed.
         */
        protected function _search($filter, $attributes = null)
        {
            // Get the LDAP connection handle.
            $connection = $this->getConnection();

            // ldap_search accepts only empty array, null is there for user
            // convenience.
            if ($attributes === null) {
                $attributes = [];
            }

            // Ignore PHP errors for ldap_search, we'll handle it by checking
            // return value and grabbing error via ldap_error function. PHP LDAP
            // functions misuse PHP error handling.
            $search = @ldap_search($connection, $this->getSetting('b_dn'), $filter, $attributes);

            if ($search === false) {
                throw new Exception(framework\Context::geti18n()->__('LDAP search failed. Filter: %filter; Error: %error', ['%filter' => $filter,
                    '%error' => ldap_error($connection)]));
            }

            // Ignore PHP errors for ldap_get_entries, we'll handle it by
            // checking return value and grabbing error via ldap_error
            // function. PHP LDAP functions misuse PHP error handling.
            $entries = @ldap_get_entries($connection, $search);

            if ($entries === false || $entries === null) {
                throw new Exception('Failed to get entries for performed LDAP search: ' . ldap_error($connection));
            }

            return $entries;
        }

        /**
         * Returns LDAP connection handle. Initialises the handle (connects and
         * binds) if handle is not initialised.
         *
         *
         * @return resource LDAP connection handle.
         */
        public function getConnection()
        {
            if ($this->_connection === null) {
                $this->_connectAndBindControlUser();
            }

            return $this->_connection;
        }

        /**
         * Retrieves user information from an LDAP directory.
         *
         * @param string $username
         *   Limit the search to username with specified username. If set to
         *   null, retrieve all users.
         *
         * @return array
         *   An array with information about all users. Every user is
         *   represented by one item, which is an array on its own with the
         *   following keys available:
         *
         *   - ldap_username (LDAP user DN)
         *   - username
         *   - realname
         *   - buddyname
         *   - email
         *
         * @throws Exception
         *   Thrown in case the LDAP module is not configured properly for this
         *   operation.
         */
        public function getLDAPUserInformation($username = null)
        {
            // Extract general LDAP configuration.
            $dn_attr = $this->getSetting('dn_attr');

            // Extract configuration for user attribute mapping.
            $user_class = $this->getSetting('u_type');
            $username_attr = $this->getSetting('u_attr');
            $fullname_attr = $this->getSetting('f_attr');
            $buddyname_attr = $this->getSetting('b_attr');
            $email_attr = $this->getSetting('e_attr');

            // Verify we are properly configured before proceeding.
            if (!$user_class || !$username_attr || !$dn_attr) {
                throw new Exception(framework\Context::geti18n()->__('LDAP module has not been configured correctly. Please check your settings and test connection.'));
            }

            // Grab allowed LDAP users by group membership.
            $allowed_ldap_users = $this->_getAllowedLDAPUsersByGroupMembership();

            // Retrieve all users in the LDAP directory based on object class
            // and username (if provided). CN is used as fallback for some user
            // settings later on.
            $attributes = [$fullname_attr, $buddyname_attr, $username_attr, $email_attr, $dn_attr, 'cn'];
            if ($username !== null) {
                $filter = $this->_prepareFilter('(&(objectClass=%user_class)(%username_attr=%username))', ['%user_class' => $user_class,
                    '%username_attr' => $username_attr,
                    '%username' => $username]);
            } else {
                $filter = $this->_prepareFilter('(&(objectClass=%user_class)(%username_attr=*))', ['%user_class' => $user_class,
                    '%username_attr' => $username_attr]);
            }
            $ldap_users = $this->_search($filter, $attributes);

            // We'll store information in this array.
            $users = [];

            // Iterate LDAP users.
            unset($ldap_users['count']);
            foreach ($ldap_users as $ldap_user) {
                if ($allowed_ldap_users === null || in_array(strtolower($ldap_user[$dn_attr][0]), $allowed_ldap_users)) {
                    $user = [];

                    // Grab the full name, falling back to cn if available.
                    if (array_key_exists(strtolower($fullname_attr), $ldap_user)) {
                        $user['realname'] = $ldap_user[strtolower($fullname_attr)][0];
                    } elseif (array_key_exists(strtolower('cn'), $ldap_user)) {
                        $user['realname'] = $ldap_user['cn'][0];
                    } else {
                        $user['realname'] = "";
                    }

                    // Grab the buddy name, falling back to cn if available.
                    if (array_key_exists(strtolower($buddyname_attr), $ldap_user)) {
                        $user['buddyname'] = $ldap_user[strtolower($buddyname_attr)][0];

                    } elseif (array_key_exists(strtolower('cn'), $ldap_user)) {
                        $user['buddyname'] = $ldap_user['cn'][0];
                    } else {
                        $user['buddyname'] = "";
                    }

                    // Grab e-mail if present.
                    if (array_key_exists(strtolower($email_attr), $ldap_user)) {
                        $user['email'] = $ldap_user[strtolower($email_attr)][0];
                    } else {
                        $user['email'] = '';
                    }

                    // Grab username.
                    $user['username'] = $ldap_user[strtolower($username_attr)][0];

                    // Grab DN.
                    $user['ldap_username'] = $ldap_user[$dn_attr][0];

                    $users[] = $user;
                }
            }

            return $users;
        }

        /**
         * Retrieves a list of LDAP users authorised to access Pachno based on
         * group membership.
         *
         *
         * @return string[] | null
         *   List of LDAP user DNs, lower-cased, which are allowed to access
         *   Pachno. If group restrictions have not been configured in Pachno, returns
         *   null.
         *
         * @throws Exception
         *   Thrown in case the LDAP module is not configured properly for this
         *   operation.
         */
        protected function _getAllowedLDAPUsersByGroupMembership()
        {
            // Get list of groups from configuration.
            $allowed_groups = $this->getSetting('groups');

            // Indicates no checks based on groups.
            if ($allowed_groups == "") {
                return null;
            }

            // List of all user DNs that are allowed access.
            $result = [];

            $dn_attr = $this->getSetting('dn_attr');

            // Extract configuration for access control based on group membership.
            $group_class = strtolower($this->getSetting('g_type'));
            $groups_members_attr = strtolower($this->getSetting('g_attr'));

            // Verify we are properly configured before proceeding.
            if (!$group_class || !$groups_members_attr || !$dn_attr) {
                throw new Exception(framework\Context::geti18n()->__('LDAP module has not been configured correctly. Please check your settings and test connection.'));
            }

            // Filter for matching all the different groups. Result should be
            // along the lines of '(cn=group1)(cn=group2)'.
            $group_cn_filter = "";
            foreach (explode(',', $allowed_groups) as $allowed_group) {
                $group_cn_filter = $group_cn_filter . $this->_prepareFilter('(cn=%cn)', ['%cn' => $allowed_group]);
            }

            // Grab all the non-empty groups we are interested in.
            $filter = $this->_prepareFilter("(&(|{$group_cn_filter})(objectClass=%group_class)(%groups_members_attr=*))", ['%group_class' => $group_class,
                '%groups_members_attr' => $groups_members_attr]);
            $attributes = [$groups_members_attr, 'cn', $dn_attr];
            $groups = $this->_search($filter, $attributes);

            unset($groups['count']);

            // Go through all members, adding them to array.
            foreach ($groups as $group) {
                unset($group[$groups_members_attr]['count']);
                foreach ($group[$groups_members_attr] as $member) {
                    $result[] = strtolower($member);
                }
            }

            return array_unique($result);
        }

        /**
         * Imports and updates all valid users from LDAP directory. This method
         * will not remove existing users if they cannot be located in the LDAP
         * directory.
         *
         *
         * @return array
         *   Array with statistics information. The following keys are
         *   available:
         *
         *   imported
         *     Number of imported (new) users.
         *
         *   updated
         *     Number of updated users.
         *
         *   total
         *     Total number of valid LDAP users found.
         */
        public function importAndUpdateUsers()
        {
            // Fetch user information from LDAP server.
            $ldap_users = $this->getLDAPUserInformation();

            // Counters for statistics.
            $import_count = 0;
            $update_count = 0;
            $total_count = count($ldap_users);

            /*
             * For every user that was found, either create a new user object, or update
             * the existing one. This will update the created and updated counts as appropriate.
             */
            foreach ($ldap_users as $ldap_user) {
                list($user, $created) = $this->createOrUpdateUser($ldap_user);

                if ($created === true) {
                    $import_count++;
                } else {
                    $update_count++;
                }
            }

            return ['imported' => $import_count,
                'updated' => $update_count,
                'total' => $total_count];
        }

        /**
         * Creates or updates an existing user based on passed-in LDAP user
         * information.
         *
         * @param array $ldap_user
         *   Array describing the LDAP user. This is normally one of the
         *   elements from _getLDAPUserInformation() method. The following keys
         *   should be present in the array:
         *
         *   - ldap_username (LDAP user DN)
         *   - username
         *   - realname
         *   - buddyname
         *   - email
         *
         *
         * @return array
         *   Returns an array with two elements. First element is instance of
         *   \pachno\core\entities\User, and second is a bool value
         *   denoting if the user was created just now.
         */
        public function createOrUpdateUser($ldap_user)
        {
            $user = User::getByUsername($ldap_user['username']);

            if ($user instanceof User) {
                $user->setRealname($ldap_user['realname']);
                $user->setEmail($ldap_user['email']);
                $user->setRealname($ldap_user['buddyname']);
                $user->save();

                $created = false;
            } else {
                $user = new User();
                $user->setUsername($ldap_user['username']);
                $user->setRealname($ldap_user['realname']);
                $user->setBuddyname($ldap_user['buddyname']);
                $user->setEmail($ldap_user['email']);
                $user->setEnabled();
                $user->setActivated();
                $user->setJoined();
                $user->setPassword((openssl_random_pseudo_bytes(32)));
                $user->save();

                $created = true;
            }

            return [$user, $created];
        }

        /**
         * Removes all users which are not present in the LDAP directory.
         *
         *
         * @return array
         *   Array with statistics information. The following keys are
         *   available:
         *
         *   deleted
         *     Number of imported (new) users.
         *
         *   total_ldap_users
         *     Total number of valid LDAP users found.
         *
         *   total_pachno_users
         *     Total number of Pachno users found.
         */
        public function pruneUsers()
        {
            // Fetch user information from LDAP server.
            $ldap_users = $this->getLDAPUserInformation();

            // Fetch Pachno users.
            $pachno_users = User::getAll();

            // Counters for statistics
            $delete_count = 0;
            $total_ldap_count = count($ldap_users);
            $total_users_count = count($pachno_users);

            // Extract all Pachno usernames for LDAP users.
            $usernames_to_keep = array_map(function ($user) {
                return $user['username'];
            }, $ldap_users);

            $default = framework\Settings::getDefaultUserID();

            foreach ($pachno_users as $pachno_user) {
                if ($pachno_user->getID() != $default && !in_array($pachno_user->getUsername(), $usernames_to_keep)) {
                    $delete_count++;
                    $pachno_user->delete();
                }
            }

            return ['deleted' => $delete_count,
                'total_ldap_users' => $total_ldap_count,
                'total_pachno_users' => $total_users_count];
        }

        /**
         * Initialises the module. No module-specific steps are taken for this
         * module.
         */
        protected function _initialize()
        {
        }

        /**
         * Installs the module. No module-specific steps are taken for this
         * module.
         *
         * @param pachno\core\entities\Scope $scope
         *   Scope within which the module is installed.
         */
        protected function _install($scope)
        {
        }

        /**
         * Uninstalls the module. No module-specific steps are taken for this
         * module.
         */
        protected function _uninstall()
        {
        }

        /**
         * Registers listeners for events.
         */
        protected function _addListeners()
        {
            Event::listen('core', 'pachno\core\modules\configuration\controllers\Main\getAuthenticationMethodForAction', [$this, 'listen_configurationAuthenticationMethod']);
        }

    }
