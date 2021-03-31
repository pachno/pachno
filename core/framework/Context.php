<?php

    namespace pachno\core\framework;

    use b2db\Core;
    use Exception;
    use pachno\core\entities\Client;
    use pachno\core\entities\CustomDatatype;
    use pachno\core\entities\Datatype;
    use pachno\core\entities\Group;
    use pachno\core\entities\Module;
    use pachno\core\entities\Project;
    use pachno\core\entities\Scope;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\tables\Scopes;
    use pachno\core\entities\User;
    use pachno\core\framework\cli;
    use pachno\core\framework\exceptions\ActionNotAllowedException;
    use pachno\core\framework\exceptions\ActionNotFoundException;
    use pachno\core\framework\exceptions\CSRFFailureException;
    use pachno\core\framework\exceptions\TemplateNotFoundException;
    use pachno\core\framework\routing\Route;
    use pachno\core\helpers\TextParserMarkdown;
    use pachno\core\modules\debugger\Debugger;
    use pachno\core\modules\main\controllers\Common;
    use pachno\core\modules\main\controllers\Main;
    use Ramsey\Uuid\Uuid;
    use RuntimeException;
    use Spyc;
    use const PACHNO_CONFIGURATION_PATH;

    /**
     * The core class of the framework powering pachno
     *
     * @package pachno
     * @subpackage core
     */
    class Context
    {

        const INTERNAL_MODULES = 'internal_modules';

        const EXTERNAL_MODULES = 'external_modules';

        protected static $_debug_mode = true;

        protected static $debug_id;

        protected static $_configuration;

        protected static $_session_initialization_time;

        protected static $_partials_visited = [];

        /**
         * Outdated modules
         *
         * @var array
         */
        protected static $_outdated_modules;

        /**
         * The current user
         *
         * @var User
         */
        protected static $_user;

        /**
         * List of modules
         *
         * @var Module[]
         */
        protected static $_modules = [];

        /**
         * List of internal modules
         *
         * @var CoreModule[]
         */
        protected static $_internal_modules = [];

        /**
         * List of internal module paths
         *
         * @var string[]
         */
        protected static $_internal_module_paths = [];

        /**
         * List of permissions
         *
         * @var array
         */
        protected static $_permissions = [];

        /**
         * List of available permissions
         *
         * @var array
         */
        protected static $_available_permissions;

        /**
         * List of available permission paths
         *
         * @var array
         */
        protected static $_available_permission_paths;

        /**
         * The path to pachno relative from url server root
         *
         * @var string
         */
        protected static $_webroot;

        /**
         * Stripped version of the $_webroot
         *
         * @see $_webroot
         *
         * @var string
         */
        protected static $_stripped_webroot;

        /**
         * Whether we're in installmode or not
         *
         * @var boolean
         */
        protected static $_installmode = false;

        /**
         * Whether we're in upgrademode or not
         *
         * @var boolean
         */
        protected static $_upgrademode = false;

        /**
         * The i18n object
         *
         * @var I18n
         */
        protected static $_i18n;

        /**
         * The request object
         *
         * @var Request
         */
        protected static $_request;

        /**
         * The current action object
         *
         * @var Action
         */
        protected static $_current_controller_object;

        /**
         * @var string
         */
        protected static $_current_controller_method;

        /**
         * @var string
         */
        protected static $_current_controller_module;

        /**
         * The response object
         *
         * @var Response
         */
        protected static $_response;

        /**
         * The current scope object
         *
         * @var Scope
         */
        protected static $_scope;

        /**
         * The currently selected project, if any
         *
         * @var Project
         */
        protected static $_selected_project;

        /**
         * The currently selected client, if any
         *
         * @var Client
         */
        protected static $_selected_client;

        /**
         * Used to determine when the b2 engine started loading
         *
         * @var integer
         */
        protected static $_loadstart;

        /**
         * List of loaded libraries
         *
         * @var string[]
         */
        protected static $_libs = [];

        /**
         * The routing object
         *
         * @var Routing
         */
        protected static $_routing;

        /**
         * The cache object
         *
         * @var Cache
         */
        protected static $_cache;

        /**
         * Messages passed on from the previous request
         *
         * @var array
         */
        protected static $_messages;

        protected static $_redirect_login;

        /**
         * Information about the latest available version. Should be null
         * in case the information has not been fetched (or fetching
         * failed), or an array with keys: maj, min, rev, nicever.
         *
         */
        protected static $_latest_available_version;

        /**
         * Displays a nicely formatted exception message
         *
         * @param Exception $exception
         */
        public static function exceptionHandler($exception)
        {
            if (self::isDebugMode() && !self::isInstallmode())
                self::generateDebugInfo();

            if (self::isCLI()) {
                self::cliError($exception->getMessage(), $exception);
            }

            if (self::getRequest() instanceof Request && self::getRequest()->isAjaxCall()) {
                self::getResponse()->ajaxResponseText(404, $exception->getMessage());
            } else {
                self::getResponse()->cleanBuffer();
                require PACHNO_CORE_PATH . 'templates' . DS . 'error.php';
            }
            die();
        }

        public static function isDebugMode()
        {
            return self::$_debug_mode;
        }

        public static function setDebugMode($value = true)
        {
            self::$_debug_mode = $value;
        }

        /**
         * Returns whether or not we're in install mode
         *
         * @return boolean
         */
        public static function isInstallmode()
        {
            return self::$_installmode;
        }

        protected static function generateDebugInfo()
        {
            $debug_summary = [];
            $load_time = self::getLoadtime();
            $session_time = self::$_session_initialization_time;
            if (Core::isInitialized()) {
                $debug_summary['db']['queries'] = Core::getSQLHits();
                $debug_summary['db']['timing'] = Core::getSQLTiming();
                $debug_summary['db']['objectpopulation'] = Core::getObjectPopulationHits();
                $debug_summary['db']['objecttiming'] = Core::getObjectPopulationTiming();
                $debug_summary['db']['objectcount'] = Core::getObjectPopulationCount();
            }
            $debug_summary['load_time'] = ($load_time >= 1) ? round($load_time, 2) . 's' : round($load_time * 1000, 1) . 'ms';
            $debug_summary['session_initialization_time'] = ($session_time >= 1) ? round($session_time, 2) . 's' : round($session_time * 1000, 1) . 'ms';
            $debug_summary['scope'] = [];
            $scope = self::getScope();
            $debug_summary['scope']['id'] = $scope instanceof Scope ? $scope->getID() : 'unknown';
            $debug_summary['scope']['hostnames'] = ($scope instanceof Scope && Core::isConnected()) ? implode(', ', $scope->getHostnames()) : 'unknown';
            $debug_summary['settings'] = Settings::getAll();
            $debug_summary['memory'] = memory_get_usage();
            $debug_summary['partials'] = self::getVisitedPartials();
            $debug_summary['log'] = Logging::getEntries();
            $debug_summary['routing'] = (self::getRouting()->getCurrentRoute() instanceof Route) ? self::getRouting()->getCurrentRoute()->toJSON() : [];

            if (isset($_SESSION)) {
                if (!array_key_exists('___DEBUGINFO___', $_SESSION)) {
                    $_SESSION['___DEBUGINFO___'] = [];
                }
                $_SESSION['___DEBUGINFO___'][self::getDebugID()] = $debug_summary;
                while (count($_SESSION['___DEBUGINFO___']) > 25) {
                    array_shift($_SESSION['___DEBUGINFO___']);
                }
            }
        }

        /**
         * Get the time from when we started loading
         *
         * @param integer $precision
         *
         * @return integer
         */
        public static function getLoadtime($precision = 5)
        {
            $endtime = explode(' ', microtime());

            return round((($endtime[1] + $endtime[0]) - self::$_loadstart), $precision);
        }

        /**
         * Returns current scope
         *
         * @return Scope
         */
        public static function getScope(): ?Scope
        {
            return self::$_scope;
        }

        /**
         * Find and set the current scope
         *
         * @param Scope $scope Specify a scope to set for this request
         */
        public static function setScope($scope = null)
        {
            Logging::log("Setting current scope");
            if ($scope !== null) {
                Logging::log("Setting scope from function parameter");
                self::$_scope = $scope;
                Settings::forceSettingsReload();
                Logging::log("...done (Setting scope from function parameter)");

                return true;
            }

            $row = null;
            try {
                $hostname = null;
                if (!self::isCLI() && !self::isInstallmode()) {
                    Logging::log("Checking if scope can be set from hostname (" . $_SERVER['HTTP_HOST'] . ")");
                    $hostname = $_SERVER['HTTP_HOST'];
                }

                if (!self::isUpgrademode() && !self::isInstallmode())
                    $scope = Scopes::getTable()->getByHostnameOrDefault($hostname);

                if (!$scope instanceof Scope) {
                    Logging::log("It couldn't", 'main', Logging::LEVEL_WARNING);
                    if (!self::isInstallmode())
                        throw new Exception("Pachno isn't set up to work with this server name.");
                    else
                        return;
                }

                Logging::log("Setting scope {$scope->getID()} from hostname");
                self::$_scope = $scope;
                Settings::forceSettingsReload();
                Settings::loadSettings();
                Logging::log("...done (Setting scope from hostname)");

                return true;
            } catch (Exception $e) {
                if (self::isCLI()) {
                    Logging::log("Couldn't set up default scope.", 'main', Logging::LEVEL_FATAL);
                    throw new Exception("Could not load default scope. Error message was: " . $e->getMessage());
                } elseif (!self::isInstallmode()) {
                    Logging::log("Couldn't find a scope for hostname {$_SERVER['HTTP_HOST']}", 'main', Logging::LEVEL_FATAL);
                    Logging::log($e->getMessage(), 'main', Logging::LEVEL_FATAL);
                    throw new Exception("Could not load scope. This is usually because the scopes table doesn't have a scope for this hostname");
                } else {
                    Logging::log("Couldn't find a scope for hostname {$_SERVER['HTTP_HOST']}, but we're in installmode so continuing anyway");
                }
            }
        }

        protected static function getVisitedPartials()
        {
            return self::$_partials_visited;
        }

        /**
         * Returns the routing object
         *
         * @return Routing
         */
        public static function getRouting()
        {
            if (!self::$_routing) {
                self::$_routing = new Routing();
            }

            return self::$_routing;
        }

        public static function getDebugID()
        {
            return self::$debug_id;
        }

        /**
         * Returns the request object
         *
         * @return Request
         */
        public static function getRequest()
        {
            if (!self::$_request instanceof Request) {
                self::$_request = new Request();
            }

            return self::$_request;
        }

        /**
         * Returns the response object
         *
         * @return Response
         */
        public static function getResponse()
        {
            if (!self::$_response instanceof Response) {
                self::$_response = new Response();
            }

            return self::$_response;
        }

        public static function isCLI()
        {
            return (PHP_SAPI == 'cli');
        }

        protected static function cliError($title, $exception)
        {
            cli\Command::cliError($title, $exception);
        }

        public static function errorHandler($code, $error, $file, $line)
        {
            // Do not run the handler for suppressed errors. Normally this should be
            // only commands where supression is done via the @ operator.
            if (error_reporting() === 0) {
                return false;
            }

            if (self::isDebugMode())
                self::generateDebugInfo();

            if (self::getRequest() instanceof Request && self::getRequest()->isAjaxCall()) {
                self::getResponse()->ajaxResponseText(404, $error);
            }

            $details = compact('code', 'error', 'file', 'line');

            if (self::isCLI()) {
                self::cliError($error, $details);
            } else {
                self::getResponse()->cleanBuffer();
                require PACHNO_CORE_PATH . 'templates' . DS . 'error.php';
            }
            die();
        }

        /**
         * Setup the routing object with CLI parameters
         *
         * @param string $module
         * @param string $action
         */
        public static function setCLIRouting($module, $action)
        {
            $routing = self::getRouting();
            $routing->setCurrentRoute(new Route('cli', $module, $action));
        }

        /**
         * Get the subdirectory part of the url, stripped
         *
         * @return string
         */
        public static function getStrippedWebroot()
        {
            if (self::$_stripped_webroot === null) {
                self::$_stripped_webroot = (self::isCLI()) ? '' : rtrim(self::getWebroot(), '/');
            }

            return self::$_stripped_webroot;
        }

        /**
         * Get the subdirectory part of the url
         *
         * @return string
         */
        public static function getWebroot()
        {
            if (self::$_webroot === null) {
                self::_setWebroot();
            }

            return self::$_webroot;
        }

        /**
         * Set the subdirectory part of the url, from the url
         */
        protected static function _setWebroot()
        {
            self::$_webroot = defined('\pachno\core\entities\_CLI') ? '.' : dirname($_SERVER['PHP_SELF']);
            if (stripos(PHP_OS, 'WIN') !== false) {
                self::$_webroot = str_replace("\\", "/", self::$_webroot); /* Windows adds a \ to the URL which we don't want */
            }
            if (self::$_webroot[strlen(self::$_webroot) - 1] != '/')
                self::$_webroot .= '/';
        }

        public static function isInitialized()
        {
            return (self::$_loadstart !== null);
        }

        public static function getSessionLoadTime()
        {
            return self::$_session_initialization_time;
        }

        public static function clearRoutingCache()
        {
            self::getCache()->delete(Cache::KEY_ROUTES_CACHE, true, true);
            self::getCache()->delete(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, true, true);
            self::getCache()->delete(Cache::KEY_ANNOTATION_LISTENERS_CACHE, true, true);
            self::getCache()->fileDelete(Cache::KEY_ROUTES_CACHE, true, true);
            self::getCache()->fileDelete(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, true, true);
            self::getCache()->fileDelete(Cache::KEY_ANNOTATION_LISTENERS_CACHE, true, true);
        }

        /**
         * Returns the cache object
         *
         * @return Cache
         */
        public static function getCache()
        {
            if (!self::$_cache) {
                self::$_cache = new Cache();
            }

            return self::$_cache;
        }

        public static function clearMenuLinkCache()
        {
            if (!self::getCache()->isEnabled())
                return;
            foreach ([Cache::KEY_MAIN_MENU_LINKS] as $key) {
                self::getCache()->delete($key);
                self::getCache()->fileDelete($key);
            }
        }

        public static function loadEventListeners($event_listeners)
        {
            Logging::log('Loading event listeners');
            foreach ($event_listeners as $listener) {
                [$event_module, $event_identifier, $module, $method] = $listener;
                Event::listen($event_module, $event_identifier, [self::getModule($module), $method]);
            }
            Logging::log('... done (loading event listeners)');
        }

        /**
         * Returns a specified module
         *
         * @param string $module_name
         *
         * @return Module
         */
        public static function getModule($module_name)
        {
            if (!self::isModuleLoaded($module_name) && !isset(self::$_internal_modules[$module_name])) {
                throw new Exception("The module '{$module_name}' is not loaded");
            } else {
                return (isset(self::$_internal_modules[$module_name])) ? self::$_internal_modules[$module_name] : self::$_modules[$module_name];
            }
        }

        /**
         * @return Debugger

         * @throws Exception
         */
        public static function getDebugger()
        {
            return self::getModule('debugger');
        }

        /**
         * Whether or not a module is loaded
         *
         * @param string $module_name
         *
         * @return boolean
         */
        public static function isModuleLoaded($module_name)
        {
            return isset(self::$_modules[$module_name]);
        }

        /**
         * @return interfaces\ModuleInterface[][]
         */
        public static function getAllModules()
        {
            return [
                self::INTERNAL_MODULES => self::$_internal_modules,
                self::EXTERNAL_MODULES => self::getModules()
            ];
        }

        /**
         * Returns an array of modules
         *
         * @return Module[]
         */
        public static function getModules()
        {
            return self::$_modules;
        }

        /**
         * Reinitialize the i18n object, used only when changing the language in the middle of something
         *
         * @param string $language The language code to change to
         */
        public static function reinitializeI18n($language = null)
        {
            if (!$language) {
                self::$_i18n = new I18n(Settings::get('language'));
            } else {
                Logging::log('Changing language to ' . $language);
                self::$_i18n = new I18n($language);
                self::$_i18n->initialize();
            }
        }

        /**
         * Get available themes
         *
         * @return array
         */
        public static function getThemes()
        {
            $theme_path_handle = opendir(PACHNO_PATH . 'themes' . DS);
            $themes = [];
            $parser = new TextParserMarkdown();

            while ($theme = readdir($theme_path_handle)) {
                if ($theme != '.' && $theme != '..' && is_dir(PACHNO_PATH . 'themes' . DS . $theme) && file_exists(PACHNO_PATH . 'themes' . DS . $theme . DS . 'theme.php')) {
                    $themes[$theme] = [
                        'key' => $theme,
                        'name' => ucfirst($theme),
                        'version' => file_get_contents(PACHNO_PATH . 'themes' . DS . $theme . DS . 'VERSION'),
                        'author' => file_get_contents(PACHNO_PATH . 'themes' . DS . $theme . DS . 'AUTHOR'),
                        'description' => $parser->transform(file_get_contents(PACHNO_PATH . 'themes' . DS . $theme . DS . 'README.md'))
                    ];
                }
            }

            return $themes;
        }

        public static function switchUserContext(User $user)
        {
            if (self::getUser() instanceof User && $user->getID() == self::getUser()->getID()) {
                return;
            }

            self::setUser($user);
            Settings::forceSettingsReload();
            self::reloadPermissionsCache();
        }

        /**
         * Returns the user object
         *
         * @return User
         */
        public static function getUser()
        {
            return self::$_user;
        }

        /**
         * Set the current user
         *
         * @param User $user
         */
        public static function setUser(User $user)
        {
            self::$_user = $user;
        }

        public static function reloadPermissionsCache()
        {
            self::$_available_permission_paths = null;
            self::$_available_permissions = null;

            self::_cacheAvailablePermissions();
            self::cacheAllPermissions();
        }

        protected static function _cacheAvailablePermissions()
        {
            if (self::$_available_permissions === null) {
                Logging::log("Loading and caching permissions tree");
                $i18n = self::getI18n();
                self::$_available_permissions = ['user' => [], 'general' => [], 'project' => []];

                self::$_available_permissions['user'][Permissions::PERMISSION_ACCESS_GROUP_ISSUES] = ['description' => $i18n->__('Can see issues reported by users in the same group'), 'mode' => 'permissive'];
                self::$_available_permissions['configuration'][Permissions::PERMISSION_SAVE_CONFIGURATION] = ['description' => $i18n->__('Can access the configuration page and edit all configuration'), 'details' => []];
                self::$_available_permissions['configuration'][Permissions::PERMISSION_SAVE_CONFIGURATION]['details'][Permissions::PERMISSION_ACCESS_CONFIGURATION] = ['description' => $i18n->__('Can access the configuration page and view all configuration'), 'details' => []];
                self::$_available_permissions['pages'][Permissions::PERMISSION_PAGE_ACCESS_DASHBOARD] = ['description' => $i18n->__('Can access the personal dashboard')];
                self::$_available_permissions['pages'][Permissions::PERMISSION_PAGE_ACCESS_PROJECT_LIST] = ['description' => $i18n->__('Can access the project list')];
                self::$_available_permissions['pages'][Permissions::PERMISSION_PAGE_ACCESS_ACCOUNT] = ['description' => $i18n->__('Can access the "Account details" page')];
                self::$_available_permissions['pages'][Permissions::PERMISSION_PAGE_ACCESS_SEARCH] = ['description' => $i18n->__('Can access the global issue search page')];

                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS] = ['description' => $i18n->__('Has normal read access to the project'), 'details' => []];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_DASHBOARD] = ['description' => $i18n->__('Can access the project dashboard and team overview')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_BOARDS] = ['description' => $i18n->__('Can access project boards')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_RELEASES] = ['description' => $i18n->__('Can access project releases and roadmap')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_ISSUES] = ['description' => $i18n->__('Can access issue search and issue pages')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_DOCUMENTATION] = ['description' => $i18n->__('Can access the project documentation')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_CODE] = ['description' => $i18n->__('Can access project code and discussions')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_TIME_LOGGING] = ['description' => $i18n->__('Can see time spent on issues')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_ACCESS]['details'][Permissions::PERMISSION_PROJECT_ACCESS_ALL_ISSUES] = ['description' => $i18n->__('Can see issues reported by other users')];

                self::$_available_permissions['project'][Permissions::PERMISSION_EDIT_DOCUMENTATION] = ['description' => $i18n->__('Can create new documentation pages, edit existing documentation and add comments'), 'details' => []];
                self::$_available_permissions['project'][Permissions::PERMISSION_EDIT_DOCUMENTATION]['details'][Permissions::PERMISSION_EDIT_DOCUMENTATION_OWN] = ['description' => $i18n->__('Can create new documentation pages but not edit documentation created by others')];
                self::$_available_permissions['project'][Permissions::PERMISSION_EDIT_DOCUMENTATION]['details'][Permissions::PERMISSION_EDIT_DOCUMENTATION_POST_COMMENTS] = ['description' => $i18n->__('Can see existing comments, post new, edit own and delete own comments')];

                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS] = ['description' => $i18n->__('Has access to internal project resources'), 'details' => []];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS]['details'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS_EDITIONS] = ['description' => $i18n->__('Has access to internal editions')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS]['details'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS_COMPONENTS] = ['description' => $i18n->__('Has access to internal components')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS]['details'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS_BUILDS] = ['description' => $i18n->__('Has access to internal releases')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS]['details'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS_MILESTONES] = ['description' => $i18n->__('Has access to internal milestones')];
                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS]['details'][Permissions::PERMISSION_PROJECT_INTERNAL_ACCESS_COMMENTS] = ['description' => $i18n->__('Has access to internal comments')];

                self::$_available_permissions['project'][Permissions::PERMISSION_MANAGE_PROJECT] = ['description' => $i18n->__('Has access to manage the project')];
                self::$_available_permissions['project'][Permissions::PERMISSION_MANAGE_PROJECT]['details'][Permissions::PERMISSION_MANAGE_PROJECT_DETAILS] = ['description' => $i18n->__('Can edit project details and settings')];
                self::$_available_permissions['project'][Permissions::PERMISSION_MANAGE_PROJECT]['details']['cancreatepublicboards'] = ['description' => $i18n->__('Can create public boards')];
                self::$_available_permissions['project'][Permissions::PERMISSION_MANAGE_PROJECT]['details']['cancreatepublicsavedsearches'] = ['description' => $i18n->__('Can create public saved searches')];
                self::$_available_permissions['project'][Permissions::PERMISSION_MANAGE_PROJECT]['details'][Permissions::PERMISSION_MANAGE_PROJECT_RELEASES] = ['description' => $i18n->__('Can manage project releases')];
                self::$_available_permissions['project'][Permissions::PERMISSION_MANAGE_PROJECT]['details']['canlockandeditlockedissues'] = ['description' => $i18n->__('Can restrict access to specific issues')];
                self::$_available_permissions['project'][Permissions::PERMISSION_MANAGE_PROJECT]['details'][Permissions::PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION] = ['description' => $i18n->__('Can moderate documentation and comments')];

                self::$_available_permissions['project'][Permissions::PERMISSION_PROJECT_CREATE_ISSUES] = ['description' => $i18n->__('Can create new issues')];
                self::$_available_permissions['project']['canaccessrestrictedissues'] = ['description' => $i18n->__('Can access restricted issues')];

                self::$_available_permissions['issues']['canvoteforissues'] = ['description' => $i18n->__('Can vote for issues')];

                $arr = [
                    Permissions::PERMISSION_OWN_SUFFIX => $i18n->__('For own issues only: edit issue details, triage, close and delete issues'),
                    '' => $i18n->__('For issues reported by anyone: edit issue details, triage, close and delete issues'),
                ];
                foreach ($arr as $suffix => $description) {
                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix] = ['description' => $description, 'details' => []];
                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details'][Permissions::PERMISSION_EDIT_ISSUES_BASIC . $suffix] = ['description' => $i18n->__('Can edit title, description and reproduction steps')];

                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details'][Permissions::PERMISSION_EDIT_ISSUES_TRIAGE . $suffix] = ['description' => $i18n->__('Can triage issues (edit category, priority, severity, reproducability, estimates)')];
                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details'][Permissions::PERMISSION_EDIT_ISSUES_TRANSITION . $suffix] = ['description' => $i18n->__('Can apply workflow actions (edit status, resolution, milestone, percent completed)')];
                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details'][Permissions::PERMISSION_EDIT_ISSUES_TIME_TRACKING . $suffix] = ['description' => $i18n->__('Can log time spent working on issues')];
                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details'][Permissions::PERMISSION_EDIT_ISSUES_ADDITIONAL . $suffix] = ['description' => $i18n->__('Can add/remove extra information (links and attachments)')];
                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details'][Permissions::PERMISSION_EDIT_ISSUES_COMMENTS . $suffix] = ['description' => $i18n->__('Can see existing comments, post new, edit own and delete own comments')];

                    if (!$suffix) {
                        self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES]['details'][Permissions::PERMISSION_EDIT_ISSUES_PEOPLE] = ['description' => $i18n->__('Can edit people involved in the issue (poster, assignee, owner)')];
                        self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES]['details'][Permissions::PERMISSION_EDIT_ISSUES_MODERATE_COMMENTS] = ['description' => $i18n->__('Can moderate comments')];
                        self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES]['details'][Permissions::PERMISSION_EDIT_ISSUES_DELETE] = ['description' => $i18n->__('Can delete issue')];
                    }
                }

                foreach ($arr as $suffix => $description) {
                    self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details'][Permissions::PERMISSION_EDIT_ISSUES_CUSTOM_FIELDS . $suffix] = [
                        'description' => $i18n->__('Can edit any custom fields'),
                        'details' => []
                    ];
                }

                foreach (CustomDatatype::getAll() as $cdf) {
                    foreach ($arr as $suffix => $description) {
                        self::$_available_permissions['issues'][Permissions::PERMISSION_EDIT_ISSUES . $suffix]['details']['caneditissuecustomfields' . $suffix]['details']['caneditissuecustomfields' . $cdf->getKey() . $suffix] = ['description' => $i18n->__('Can change custom field "%field_name"', ['%field_name' => $i18n->__($cdf->getDescription())])];
                    }
                }

                foreach (self::$_available_permissions as $category => $permissions) {
                    self::addPermissionsPath($permissions, $category);
                }

                Logging::log("Done loading and caching permissions tree");
            }
        }

        /**
         * Get the i18n object
         *
         * @return I18n
         */
        public static function getI18n(): I18n
        {
            if (!self::isI18nInitialized()) {
                Logging::log('Cannot access the translation object until the i18n system has been initialized!', 'i18n', Logging::LEVEL_WARNING);
                throw new Exception('Cannot access the translation object until the i18n system has been initialized!');
            }

            return self::$_i18n;
        }

        public static function isI18nInitialized(): bool
        {
            return (self::$_i18n instanceof I18n);
        }

        protected static function addPermissionsPath($permissions, $category, $parent = [])
        {
            foreach ($permissions as $permission => $details) {
                self::$_available_permission_paths[$category][$permission] = array_reverse(array_values($parent));
                if (array_key_exists('details', $details)) {
                    $path = $parent;
                    $path[$permission] = $permission;
                    self::addPermissionsPath($details['details'], $category, $path);
                }
            }
        }

        /**
         * Cache all permissions
         */
        public static function cacheAllPermissions()
        {
            Logging::log('caches permissions');
            self::$_permissions = [];

            if (!self::isInstallmode() && $permissions = self::getCache()->get(Cache::KEY_PERMISSIONS_CACHE)) {
                self::$_permissions = $permissions;
                Logging::log('Using cached permissions');
            } else {
                if (self::isInstallmode() || !$permissions = self::getCache()->fileGet(Cache::KEY_PERMISSIONS_CACHE)) {
                    Logging::log('starting to cache access permissions');
                    if ($res = Permissions::getTable()->getAll()) {
                        while ($row = $res->getNextRow()) {
                            if (!array_key_exists($row->get(Permissions::MODULE), self::$_permissions)) {
                                self::$_permissions[$row->get(Permissions::MODULE)] = [];
                            }
                            if (!array_key_exists($row->get(Permissions::PERMISSION_TYPE), self::$_permissions[$row->get(Permissions::MODULE)])) {
                                self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)] = [];
                            }
                            if (!array_key_exists($row->get(Permissions::TARGET_ID), self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)])) {
                                self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)][$row->get(Permissions::TARGET_ID)] = [];
                            }
                            self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)][$row->get(Permissions::TARGET_ID)][] = ['uid' => $row->get(Permissions::USER_ID), 'gid' => $row->get(Permissions::GROUP_ID), 'tid' => $row->get(Permissions::TEAM_ID), 'allowed' => (bool)$row->get(Permissions::ALLOWED), 'role_id' => $row->get(Permissions::ROLE_ID)];
                        }
                    }
                    Logging::log('done (starting to cache access permissions)');
                    if (!self::isInstallmode())
                        self::getCache()->fileAdd(Cache::KEY_PERMISSIONS_CACHE, self::$_permissions);
                } else {
                    self::$_permissions = $permissions;
                }
                if (!self::isInstallmode())
                    self::getCache()->add(Cache::KEY_PERMISSIONS_CACHE, self::$_permissions);
            }
            Logging::log('...cached');
        }

        public static function finishUpgrading()
        {
            self::$_upgrademode = false;
            self::$_installmode = false;
            self::loadModules();
        }

        /**
         * Loads and initializes all modules
         */
        public static function loadModules()
        {
            Logging::log('Loading modules');

            if (self::isInstallmode())
                return;

            if (self::isUpgrademode()) {
                self::$_modules = Module::getB2DBTable()->getAllNames();

                return;
            }

            Logging::log('getting modules from database');
            self::$_modules = Module::getB2DBTable()->getAll();
            Logging::log('done (setting up module objects)');

            Logging::log('initializing modules');
            if (!empty(self::$_modules)) {
                foreach (self::$_modules as $module) {
                    $module->initialize();
                }
                Logging::log('done (initializing modules)');
            } else {
                Logging::log('no modules found');
            }
            Logging::log('...done (loading modules)');
        }

        /**
         * Returns whether or not we're in upgrade mode
         *
         * @return boolean
         */
        public static function isUpgrademode()
        {
            return self::$_upgrademode;
        }

        /**
         * Adds a module to the module list
         *
         * @param Module $module
         */
        public static function addModule($module, $module_name)
        {
            if (self::$_modules === null) {
                self::$_modules = [];
            }
            self::$_modules[$module_name] = $module;
        }

        /**
         * Unloads a loaded module
         *
         * @param string $module_name The name of the module to unload
         */
        public static function unloadModule($module_name)
        {
            if (isset(self::$_modules[$module_name])) {
                unset(self::$_modules[$module_name]);
                Event::clearListeners($module_name);
            }
        }

        /**
         * Returns an array of modules which need upgrading
         *
         * @return array
         */
        public static function getOutdatedModules()
        {
            if (self::$_outdated_modules == null) {
                self::$_outdated_modules = [];
                foreach (self::getModules() as $module) {
                    if ($module->isOutdated()) {
                        self::$_outdated_modules[] = $module;
                    }
                }
            }

            return self::$_outdated_modules;
        }

        /**
         * Get uninstalled modules
         *
         * @return Module[]
         */
        public static function getUninstalledModules()
        {
            $module_path_handle = opendir(PACHNO_MODULES_PATH);
            $modules = [];
            while ($module_name = readdir($module_path_handle)) {
                if (is_dir(PACHNO_MODULES_PATH . $module_name) && file_exists(PACHNO_MODULES_PATH . $module_name . DS . ucfirst($module_name) . '.php')) {
                    if (self::isModuleLoaded($module_name))
                        continue;

                    try {
                        $module_class = "\\pachno\\modules\\{$module_name}\\" . ucfirst($module_name);
                        if (class_exists($module_class)) {
                            $modules[$module_name] = new $module_class();
                        }
                    } catch (\Exception $e) {}
                }
            }

            return $modules;
        }

        /**
         * Return all permissions available
         *
         * @param string $type
         * @param integer $uid
         * @param integer $tid
         * @param integer $gid
         * @param integer $target_id [optional]
         * @param boolean $all [optional]
         *
         * @return array
         */
        public static function getAllPermissions($type, $uid, $tid, $gid, $target_id = null, $all = false)
        {
            $query = Permissions::getTable()->getQuery();
            $query->where(Permissions::SCOPE, self::getScope()->getID());
            $query->where(Permissions::PERMISSION_TYPE, $type);

            if (($uid + $tid + $gid) == 0 && !$all) {
                $query->where(Permissions::USER_ID, $uid);
                $query->where(Permissions::TEAM_ID, $tid);
                $query->where(Permissions::GROUP_ID, $gid);
            } else {
                switch (true) {
                    case ($uid != 0):
                        $query->where(Permissions::USER_ID, $uid);
                    case ($tid != 0):
                        $query->where(Permissions::TEAM_ID, $tid);
                    case ($gid != 0):
                        $query->where(Permissions::GROUP_ID, $gid);
                }
            }
            if ($target_id !== null) {
                $query->where(Permissions::TARGET_ID, $target_id);
            }

            $permissions = [];

            if ($res = Permissions::getTable()->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $permissions[] = ['p_type' => $row->get(Permissions::PERMISSION_TYPE), 'target_id' => $row->get(Permissions::TARGET_ID), 'allowed' => $row->get(Permissions::ALLOWED), 'uid' => $row->get(Permissions::USER_ID), 'gid' => $row->get(Permissions::GROUP_ID), 'tid' => $row->get(Permissions::TEAM_ID), 'id' => $row->get(Permissions::ID)];
                }
            }

            return $permissions;
        }

        public static function deleteModulePermissions($module_name, $scope)
        {
            if ($scope == self::getScope()->getID()) {
                if (array_key_exists($module_name, self::$_permissions)) {
                    unset(self::$_permissions[$module_name]);
                }
            }
            Permissions::getTable()->deleteModulePermissions($module_name, $scope);
        }

        public static function removeAllPermissionsForCombination($uid, $gid, $tid, $target_id = 0, $role_id = null)
        {
            Permissions::getTable()->deleteAllPermissionsForCombination($uid, $gid, $tid, $target_id, $role_id);
            self::clearPermissionsCache();
        }

        public static function clearPermissionsCache()
        {
            self::getCache()->delete(Cache::KEY_PERMISSIONS_CACHE, true, true);
            self::getCache()->fileDelete(Cache::KEY_PERMISSIONS_CACHE, true, true);
        }

        /**
         * Save a permission setting
         *
         * @param string $permission_type The permission type
         * @param mixed $target_id The target id
         * @param string $module The name of the module for which the permission is valid
         * @param integer $user_id The user id for which the permission is valid, 0 for none
         * @param integer $group_id The group id for which the permission is valid, 0 for none
         * @param integer $team_id The team id for which the permission is valid, 0 for none
         * @param integer $scope [optional] A specified scope if not the default
         */
        public static function setPermission($permission_type, $target_id, $module, $user_id, $group_id, $team_id, $scope = null, $role_id = null)
        {
            if ($scope === null)
                $scope = self::getScope()->getID();

            if ($role_id === null) {
                self::removePermission($permission_type, $target_id, $module, $user_id, $group_id, $team_id, false, $scope, 0);
            }
            Permissions::getTable()->setPermission($user_id, $group_id, $team_id, $module, $permission_type, $target_id, $scope, $role_id);
            self::clearPermissionsCache();

            self::cacheAllPermissions();
        }

        /**
         * Remove a saved permission
         *
         * @param string $permission_type The permission type
         * @param mixed $target_id The target id
         * @param string $module The name of the module for which the permission is valid
         * @param integer $user_id The user id for which the permission is valid, 0 for none
         * @param integer $group_id The group id for which the permission is valid, 0 for none
         * @param integer $team_id The team id for which the permission is valid, 0 for none
         * @param boolean $recache Whether to recache after clearing this permission
         * @param integer $scope A specified scope if not the default
         */
        public static function removePermission($permission_type, $target_id, $module, $user_id, $group_id, $team_id, $recache = true, $scope = null, $role_id = null)
        {
            if ($scope === null)
                $scope = self::getScope()->getID();

            Permissions::getTable()->removeSavedPermission($user_id, $group_id, $team_id, $module, $permission_type, $target_id, $scope, $role_id);
            self::clearPermissionsCache();

            if ($recache)
                self::cacheAllPermissions();
        }

        public static function isPermissionSet($type, $permission_key, $id, $target_id = 0, $module_name = 'core', $without_role = null)
        {
            if (array_key_exists($module_name, self::$_permissions) &&
                array_key_exists($permission_key, self::$_permissions[$module_name]) &&
                array_key_exists($target_id, self::$_permissions[$module_name][$permission_key])) {
                if ($type == 'group') {
                    foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission) {
                        if ($permission['gid'] == $id && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0)))
                            return $permission['allowed'];
                    }
                }
                if ($type == 'user') {
                    foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission) {
                        if ($permission['uid'] == $id && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0)))
                            return $permission['allowed'];
                    }
                }
                if ($type == 'team') {
                    foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission) {
                        if ($permission['tid'] == $id && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0)))
                            return $permission['allowed'];
                    }
                }
                if ($type == 'everyone') {
                    foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission) {
                        if ($permission['uid'] + $permission['gid'] + $permission['tid'] == 0 && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0))) {
                            return $permission['allowed'];
                        }
                    }
                }
            }

            return null;
        }

        public static function getLoadedPermissions()
        {
            return self::$_permissions;
        }

        public static function getPermissionDetails($permission, $permissions_list = null, $module_name = null)
        {
            self::_cacheAvailablePermissions();
            if ($module_name === null) {
                $permissions_list = ($permissions_list === null) ? self::$_available_permissions : $permissions_list;
            } else {
                $permissions_list = ($permissions_list === null) ? self::getModule($module_name)->getAvailablePermissions() : $permissions_list;
            }
            foreach ($permissions_list as $permission_key => $permission_info) {
                if (is_numeric($permission_key))
                    return null;
                if ($permission_key == $permission)
                    return $permission_info;

                if (in_array($permission_key, array_keys(self::$_available_permissions)) || (array_key_exists('details', $permission_info) && is_array($permission_info['details']) && count($permission_info['details']))) {
                    $p_info = (in_array($permission_key, array_keys(self::$_available_permissions))) ? $permission_info : $permission_info['details'];
                    $permissionDetails = self::getPermissionDetails($permission, $p_info, $module_name);
                    if ($permissionDetails)
                        return $permissionDetails;
                }
            }
        }

        public static function permissionCheck($module, $permission, $target_id, $uid, $gid, $team_ids)
        {
            $key = 'config';

            foreach (self::$_available_permission_paths as $permission_key => $permissions) {
//                if ($permission_key == 'config')
//                    continue;

                if (array_key_exists($permission, $permissions)) {
                    $key = $permission_key;
                    break;
                }
            }

//            if ($key != 'config') {
            foreach (self::$_available_permission_paths[$key][$permission] as $parent_permission) {
                $value = self::checkPermission($module, $parent_permission, $target_id, $uid, $gid, $team_ids);
                if ($value !== null) {
                    return $value;
                }
            }
//            }

            return self::checkPermission($module, $permission, $target_id, $uid, $gid, $team_ids);
        }

        /**
         * Checks if users that can be matched against provided user ID, group
         * membership, or team membership should be granted access to specified
         * resource.
         *
         * @param string module_name Name of the module associated with permission type.
         * @param string permission_type Permission type.
         * @param mixed target_id Target (object) ID, if applicable. If not applicable, set to 0. Should be either non-negative integer or string.
         * @param integer uid User ID for matching the users. Set to 0 if it should not be used for matching, or if $uid, $gid and $team_ids are all 0/empty, match any user.
         * @param integer gid Group ID for matching the users. Set to 0 if it should not be used for matching, or if $uid, $gid and $team_ids are all 0/empty, match any user.
         * @param array team_ids List of team IDs for matching the users. Set to empty array if it should not be used for matching, or if $uid, $gid and $team_ids are all 0/empty, match any user.
         *
         * @return mixed If permission matching the specified criteria has been found in database (cache, to be more precise), returns permission value (true or false). If no matching permission has been found, returns null. Receiving null means the caller needs to apply a default rule (allow or deny), which depends on caller implementation.
         * @see User::hasPermission() For description of module name, permission type, target ID.
         *
         */
        public static function checkPermission($module_name, $permission_type, $target_id, $uid, $gid, $team_ids)
        {
            // Default is that no permission was found/matched against user
            // specifier.
            $result = null;

            // Check if there are any permission rules stored for given module and permission type.
            if (array_key_exists($module_name, self::$_permissions) &&
                array_key_exists($permission_type, self::$_permissions[$module_name])) {
                // Permissions relevant to module + permission type are stored in an
                // array, grouped based on whether they are applied against specific
                // target ID or globally.
                $permission_groups = [];

                // Since we could have multiple matches, we need to keep track of
                // what permission has the most weight.
                $permission_candidate_weight = -1;

                // Populate permission groups with permissions specific to provided
                // target IDs and global permissions. Use target_id as index since
                // we need to pass it in for weight calculation.
                if (($target_id != 0 || is_string($target_id)) && array_key_exists($target_id, self::$_permissions[$module_name][$permission_type])) {
                    $permission_groups[$target_id] = self::$_permissions[$module_name][$permission_type][$target_id];
                }

                if (array_key_exists(0, self::$_permissions[$module_name][$permission_type])) {
                    $permission_groups[0] = self::$_permissions[$module_name][$permission_type][0];
                }

                foreach ($permission_groups as $permission_group_target_id => $permission_group) {
                    foreach ($permission_group as $permission) {
                        // Permission is applicable if we can match it against the
                        // user specifier (uid, gid, or one of team IDs), or if the
                        // permission should be applied to all users.
                        if (($uid != 0 && $uid == $permission['uid']) ||
                            (count($team_ids) != 0 && in_array($permission['tid'], $team_ids)) ||
                            ($gid != 0 && $gid == $permission['gid']) ||
                            ($permission['uid'] == 0 && $permission['gid'] == 0 && $permission['tid'] == 0)) {
                            // Calculate the permissions weight, and apply its
                            // result (allow/deny) if it outweighs the previously
                            // matched permission.
                            $permission_weight = self::_getPermissionWeight($permission, $permission_group_target_id);
                            if ($permission_weight > $permission_candidate_weight) {
                                $permission_candidate_weight = $permission_weight;
                                $result = $permission['allowed'];
                            }
                        }
                    }
                }
            }

            // Return the result (true/false/null).
            return $result;
        }

        /**
         * Calculates weight of a specific permission. Permission weight is a
         * non-negative integer value denoting what priority permission should take
         * when being applied if multiple matching permissions are found.
         *
         * In other words, if user requests access to a resource, and there are
         * multiple permissions that would grant or deny access to the user for this
         * resource, permission weight can be used to determine which permission
         * applies.
         *
         * Permission weight algorithm takes into account the following:
         *
         * - How specific is the resource associated with the permission. I.e. if
         *   permission is specified against a specific target ID, it should get
         *   higher priority than the one specified against any (all) target IDs.
         * - How specific is the designator that matches against the user. User can
         *   be matched through user ID (most specific), team ID, group ID, or "any
         *   user" specifier. The weights are set in the same order (so, uid > tid >
         *   gid > any user).
         * - What is the permission rule result - i.e. does it allow or deny
         *   access. Denying access has priority over granting access.
         *
         * The weight of the above three items is also proportional to each other,
         * that is the specificity of target ID brings more weight than specific
         * uid/gid/tid, which in turns weights more than specific rule result
         * (allowed/denied).
         *
         * @param permission array An array defining permission. Must include the following keys: uid (user ID), gid (group ID), tid (team ID), and allowed (true/false).
         * @param target_id mixed Either a non-negative integer or string designating target to which the permission applies. 0 means global target.
         *
         * @return integer A non-negative integer denoting weight of permission.
         */
        protected static function _getPermissionWeight($permission, $target_id)
        {
            // The following array contains values used for figuring out permission
            // weight based on criteria of specificity. Have a look at method
            // description for logic behind it.
            $weight_bases = [
                'specific_target_id' => 1000,
                'specific_uid' => 750,
                'specific_tid' => 500,
                'specific_gid' => 250,
                'allow_false' => 50,
                'allow_true' => 0,
            ];

            // Assume least weight initially.
            $weight = 0;

            // Add weight based on target ID specificity.
            if ($target_id != 0) {
                $weight += $weight_bases['specific_target_id'];
            }

            // Apply weight based on user matching specificity.
            if ($permission['uid'] != 0) {
                $weight += $weight_bases['specific_uid'];
            } elseif ($permission['tid'] != 0) {
                $weight += $weight_bases['specific_tid'];
            } elseif ($permission['gid'] != 0) {
                $weight += $weight_bases['specific_gid'];
            }

            // Add weight based on result specificity.
            if ($permission['allowed'] === false) {
                $weight += $weight_bases['allow_false'];
            } elseif ($permission['allowed'] === true) {
                $weight += $weight_bases['allow_true'];
            }

            return $weight;
        }

        /**
         * Returns all permissions available for a specific identifier
         *
         * @param string $applies_to The identifier
         *
         * @return array
         */
        public static function getAvailablePermissions($applies_to = null)
        {
            self::_cacheAvailablePermissions();
            if ($applies_to === null) {
                $list = self::$_available_permissions;
                $retarr = [];
                foreach ($list as $key => $details) {
                    foreach ($details as $dkey => $dd) {
                        $retarr[$dkey] = $dd;
                    }
                }
                foreach (self::getModules() as $module_key => $module) {
                    $retarr['module_' . $module_key] = [];
                    foreach ($module->getAvailablePermissions() as $mpkey => $mp) {
                        $retarr['module_' . $module_key][$mpkey] = $mp;
                    }
                }

                return $retarr;
            }
            if (array_key_exists($applies_to, self::$_available_permissions)) {
                return self::$_available_permissions[$applies_to];
            } elseif (mb_substr($applies_to, 0, 7) == 'module_') {
                $module_name = mb_substr($applies_to, 7);
                if (self::isModuleLoaded($module_name)) {
                    return self::getModule($module_name)->getAvailablePermissions();
                }
            } else {
                return [];
            }
        }

        /**
         * Set the currently selected client
         *
         * @param Client $client The client, or null if none
         */
        public static function setCurrentClient($client)
        {
            self::$_selected_client = $client;
        }

        /**
         * Return whether current client is set
         *
         * @return boolean
         */
        public static function isClientContext()
        {
            return (bool)(self::getCurrentClient() instanceof Client);
        }

        /**
         * Return the currently selected client if any, or null
         *
         * @return Client
         */
        public static function getCurrentClient()
        {
            return self::$_selected_client;
        }

        /**
         * Return the currently selected project if any, or null
         *
         * @return Project
         */
        public static function getCurrentProject()
        {
            return self::$_selected_project;
        }

        /**
         * Return whether current project is set
         *
         * @return boolean
         */
        public static function isProjectContext()
        {
            return (bool)(self::getCurrentProject() instanceof Project);
        }

        /**
         * Retrieve the message and clear it
         *
         * @return string
         */
        public static function getMessageAndClear($key)
        {
            if ($message = self::getMessage($key)) {
                self::clearMessage($key);

                return $message;
            }

            return null;
        }

        /**
         * Retrieve a message passed on from the previous request
         *
         * @param string $key A message identifier
         *
         * @return string
         */
        public static function getMessage($key)
        {
            return (self::hasMessage($key)) ? self::$_messages[$key] : null;
        }

        /**
         * Whether or not there is a message in the next request
         *
         * @return boolean
         */
        public static function hasMessage($key)
        {
            self::_setupMessages();

            return array_key_exists($key, self::$_messages);
        }

        protected static function _setupMessages()
        {
            if (self::$_messages === null) {
                self::$_messages = [];
                if (array_key_exists('pachno_flash_message', $_SESSION)) {
                    self::$_messages = $_SESSION['pachno_flash_message'];
                    unset($_SESSION['pachno_flash_message']);
                }
            }
        }

        /**
         * Clear the message
         */
        public static function clearMessage($key)
        {
            if (self::hasMessage($key)) {
                unset(self::$_messages[$key]);
            }
        }

        public static function bootstrap()
        {
            // Set up error and exception handling
            set_exception_handler([self::class, 'exceptionHandler']);
            set_error_handler([self::class, 'errorHandler']);
            error_reporting(E_ALL | E_NOTICE | E_STRICT);

            if (PHP_VERSION_ID < 70100)
                die('This software requires PHP 7.1.0 or newer. Please upgrade to a newer version of php to use Pachno.');

            gc_enable();
            date_default_timezone_set('UTC');

            if (!defined('PACHNO_PATH'))
                die('You must define the PACHNO_PATH constant so we can find the files we need');

            defined('DS') || define('DS', DIRECTORY_SEPARATOR);
            defined('PACHNO_CORE_PATH') || define('PACHNO_CORE_PATH', PACHNO_PATH . 'core' . DS);
            defined('PACHNO_VENDOR_PATH') || define('PACHNO_VENDOR_PATH', PACHNO_PATH . 'vendor' . DS);
            defined('PACHNO_CACHE_PATH') || define('PACHNO_CACHE_PATH', PACHNO_PATH . 'cache' . DS);
            defined('PACHNO_CONFIGURATION_PATH') || define('PACHNO_CONFIGURATION_PATH', PACHNO_CORE_PATH . 'config' . DS);
            defined('PACHNO_INTERNAL_MODULES_PATH') || define('PACHNO_INTERNAL_MODULES_PATH', PACHNO_CORE_PATH . 'modules' . DS);
            defined('PACHNO_MODULES_PATH') || define('PACHNO_MODULES_PATH', PACHNO_PATH . 'modules' . DS);
            defined('PACHNO_PUBLIC_FOLDER_NAME') || define('PACHNO_PUBLIC_FOLDER_NAME', '');

            self::initialize();

            if (self::isCLI()) {
                self::setupI18n();

                // Available permissions cannot be cached during
                // installation because the scope is not set-up at that
                // point. Permissions also must be cached at this point,
                // and not together with self::initializeUser since i18n
                // system must be initialised beforehand.
                if (!self::isInstallmode())
                    self::_cacheAvailablePermissions();
            }
        }

        /**
         * Initialize the context
         *
         * @return null
         */
        public static function initialize()
        {
            try {
                self::$debug_id = Uuid::uuid4()->toString();

                // The time the script was loaded
                $starttime = explode(' ', microtime());
                define('NOW', (integer)$starttime[1]);

                // Set the start time
                self::setLoadStart($starttime[1] + $starttime[0]);

                self::checkInstallMode();

                self::getCache()->setPrefix(str_replace('.', '_', Settings::getVersion()));

                if (!self::isReadySetup()) {
                    self::getCache()->disable();
                } else {
                    self::getCache()->checkEnabled();
                    if (self::getCache()->isEnabled()) {
                        Logging::log((self::getCache()->getCacheType() == Cache::TYPE_APC) ? 'Caching enabled: APC, filesystem' : 'Caching enabled: filesystem');
                    } else {
                        Logging::log('No caching available');
                    }
                }

                self::loadConfiguration();

                Logging::log('Initializing Caspar framework');
                Logging::log('PHP_SAPI says "' . PHP_SAPI . '"');
                Logging::log('We are version "' . Settings::getVersion() . '"');
                Logging::log('Debug mode: ' . ((self::$_debug_mode) ? 'yes' : 'no'));

                if (!self::isCLI() && !ini_get('session.auto_start'))
                    self::initializeSession();

                Logging::log('Loading B2DB');

                if (array_key_exists('b2db', self::$_configuration))
                    Core::initialize(self::$_configuration['b2db'], self::getCache());
                else
                    Core::initialize([], self::getCache());

                if (self::isReadySetup() && !Core::isInitialized()) {
                    throw new exceptions\ConfigurationException("Pachno seems installed, but B2DB isn't configured.", exceptions\ConfigurationException::NO_B2DB_CONFIGURATION);
                }

                Logging::log('...done (Initializing B2DB)');

                if (Core::isInitialized() && self::isReadySetup()) {
                    Logging::log('Database connection details found, connecting');
                    Core::doConnect();
                    Logging::log('...done (Database connection details found, connecting)');
                }

                Logging::log('...done');

                Logging::log('Initializing context');

                mb_internal_encoding("UTF-8");
                mb_language('uni');
                mb_http_output("UTF-8");

                Logging::log('Loading scope');
                self::setScope();
                Logging::log('done (loading scope)');

                self::loadInternalModules();
                if (!self::isInstallmode()) {
                    self::setupCoreListeners();
                    self::loadModules();
                }

                self::getRouting()->loadRoutes(!self::isInstallmode());

                Logging::log('...done');
                Logging::log('...done initializing');

                Logging::log('Caspar framework loaded');
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
         * Set that we've started loading
         *
         * @param integer $when
         */
        public static function setLoadStart($when)
        {
            self::$_loadstart = $when;
        }

        public static function checkInstallMode()
        {
            if (!is_readable(PACHNO_PATH . 'installed')) {
                self::$_installmode = true;
            } elseif (is_readable(PACHNO_PATH . 'upgrade')) {
                self::$_installmode = true;
                self::$_upgrademode = true;
                self::getCache()->disable();
            } else {
                $version_info = explode(',', file_get_contents(PACHNO_PATH . 'installed'));
                if (count($version_info) < 2)
                    throw new exceptions\ConfigurationException("Version information not present", exceptions\ConfigurationException::NO_VERSION_INFO);

                $current_version = $version_info[0];
                if ($current_version != Settings::getVersion(false, true))
                    throw new exceptions\ConfigurationException("You are trying to use a newer version of Pachno than the one you installed", exceptions\ConfigurationException::UPGRADE_REQUIRED);

                self::$_installmode = false;
                self::$_upgrademode = false;
            }
            if (self::$_installmode) {
                Logging::log('Installation mode enabled');
            }
            if (self::$_upgrademode) {
                Logging::log('Upgrade mode enabled');
            }
        }

        public static function isReadySetup()
        {
            return (!(self::isInstallmode() || self::isUpgrademode()));
        }

        protected static function loadConfiguration()
        {
            Logging::log('Loading configuration from cache', 'core');
            if (self::isReadySetup()) {
                $configuration = self::getCache()->get(Cache::KEY_CONFIGURATION, false);
                if (!$configuration) {
                    Logging::log('Loading configuration from disk cache', 'core');
                    $configuration = self::getCache()->fileGet(Cache::KEY_CONFIGURATION, false);
                }
            }

            if (!self::isReadySetup() || !$configuration) {
                Logging::log('Loading configuration from files', 'core');
                $config_filename = PACHNO_CONFIGURATION_PATH . "settings.yml";
                $b2db_filename = PACHNO_CONFIGURATION_PATH . "b2db.yml";
                if (!file_exists($config_filename))
                    throw new Exception("The configuration file ({$config_filename} does not exist.");

                $config = Spyc::YAMLLoad($config_filename);
                $b2db_config = Spyc::YAMLLoad($b2db_filename);
                $configuration = array_merge($config, $b2db_config);

                if (self::isReadySetup()) {
                    self::getCache()->fileAdd(Cache::KEY_CONFIGURATION, $configuration, false);
                    self::getCache()->add(Cache::KEY_CONFIGURATION, $configuration, false);
                }
            }
            self::$_configuration = $configuration;

            self::$_debug_mode = self::$_configuration['core']['debug'];

            $log_file = (isset(self::$_configuration['core']['log_file'])) ? self::$_configuration['core']['log_file'] : null;
            if ($log_file) {
                Logging::setLogFilePath($log_file);
                Logging::log('Log file path set. At this point, configuration is loaded & caching enabled, if possible.', 'core');
            }
            Logging::log('Done Loading Configuration', 'core');
        }

        public static function initializeSession()
        {
            Logging::log('Initializing session');

            $starttime = explode(' ', microtime());
            $before = $starttime[1] + $starttime[0];
            session_name(PACHNO_SESSION_NAME);
            session_start();

            $endtime = explode(' ', microtime());
            $after = $endtime[1] + $endtime[0];
            self::$_session_initialization_time = round(($after - $before), 5);

            Logging::log('done (initializing session)');
        }

        /**
         * Loads and initializes internal modules
         */
        public static function loadInternalModules()
        {
            Logging::log('Loading internal modules');

            $modules = self::getCache()->get(Cache::KEY_INTERNAL_MODULES, false);
            if (self::isReadySetup() || !$modules) {
                foreach (scandir(PACHNO_INTERNAL_MODULES_PATH) as $modulename) {
                    if (in_array($modulename, ['.', '..']) || !is_dir(PACHNO_INTERNAL_MODULES_PATH . $modulename))
                        continue;

                    self::$_internal_module_paths[$modulename] = $modulename;
                }

                self::getCache()->add(Cache::KEY_INTERNAL_MODULES, $modules, false);
            } else {
                Logging::log('Loading cached modules');
                self::$_internal_module_paths = $modules;
            }

            foreach (self::$_internal_module_paths as $modulename) {
                $classname = "\\pachno\\core\\modules\\{$modulename}\\" . ucfirst($modulename);
                self::$_internal_modules[$modulename] = new $classname($modulename);
                self::$_internal_modules[$modulename]->initialize();
            }

            Logging::log('...done (loading internal modules)');
        }

        protected static function setupCoreListeners()
        {
            Event::listen('core', 'pachno\core\entities\File::hasAccess', '\pachno\core\entities\Project::listen_pachno_core_entities_File_hasAccess');
            Event::listen('core', 'pachno\core\entities\File::hasAccess', '\pachno\core\entities\Build::listen_pachno_core_entities_File_hasAccess');
            Event::listen('core', 'pachno\core\entities\File::hasAccess', '\pachno\core\framework\Settings::listen_pachno_core_entities_File_hasAccess');
        }

        protected static function setupI18n()
        {
            Logging::log('Initializing i18n');
//        if (true || !self::isCLI())
//        {
            $language = (self::$_user instanceof User) ? self::$_user->getLanguage() : Settings::getLanguage();

            if (self::$_user instanceof User && self::$_user->getLanguage() == 'sys') {
                $language = Settings::getLanguage();
            }

            Logging::log("Initializing i18n with language {$language}");
            self::$_i18n = new I18n($language);
            self::$_i18n->initialize();
//        }
            Logging::log('done (initializing i18n)');
        }

        /**
         * Launches the MVC framework
         */
        public static function go()
        {
            Logging::log('Dispatching');
            try {
                if (($route = self::getRouting()->getRouteFromUrl(self::getRequest()->getParameter('url', null, false))) || self::isInstallmode()) {

                    if (self::isUpgrademode()) {
                        $route = new Route('installation_upgrade', 'installation', 'upgrade');
                    } elseif (self::isInstallmode()) {
                        $route = new Route('installation_intro', 'installation', 'installIntro');
                    }

                    self::getRouting()->setCurrentRoute($route);

                    $controllerObject = $route->getController();
                    $controllerMethod = $route->getModuleActionMethod();
                    $moduleName = $route->getModuleName();
                } else {
                    $controllerObject = new Common();
                    $controllerMethod = 'runNotFound';
                    $moduleName = 'main';
                }

                self::$_current_controller_object = $controllerObject;
                self::$_current_controller_method = $controllerMethod;
                self::$_current_controller_module = $moduleName;

                if (!self::isInstallmode())
                    self::initializeUser();

                self::setupI18n();

                // Available permissions cannot be cached during
                // installation because the scope is not set-up at that
                // point. Permissions also must be cached at this point,
                // and not together with self::initializeUser since i18n
                // system must be initialised beforehand.
                if (!self::isInstallmode())
                    self::_cacheAvailablePermissions();

                if (self::$_redirect_login == 'login') {

                    Logging::log('An error occurred setting up the user object, redirecting to login', 'main', Logging::LEVEL_NOTICE);
                    if (self::getRouting()->getCurrentRoute()->getName() != 'auth_login') {
                        self::setMessage('login_message_err', self::geti18n()->__('Please log in'));
                        self::setMessage('login_referer', self::getRouting()->generate(self::getRouting()->getCurrentRoute()->getName(), self::getRequest()->getParameters()));
                    }
                    self::getResponse()->headerRedirect(self::getRouting()->generate('auth_login_page'), 403);
                }

                if (self::$_redirect_login == 'auth_elevated_login') {
                    Logging::log('Elevated permissions required', 'main', Logging::LEVEL_NOTICE);
                    if (self::getRouting()->getCurrentRoute()->getName() != 'auth_elevated_login') {
                        self::setMessage('elevated_login_message_err', self::geti18n()->__('Please re-enter your password to continue'));
                    }

                    self::$_current_controller_object = new Main();
                    self::$_current_controller_method = 'runElevatedLogin';
                    self::$_current_controller_module = 'main';
                }

                if (self::$_redirect_login == '2fa_login') {
                    Logging::log('2FA verification required', 'main', Logging::LEVEL_NOTICE);

                    self::getResponse()->headerRedirect(self::getRouting()->generate('auth_2fa_code_input'));

                    return true;
                }

                if (self::performAction()) {
                    if (self::isDebugMode()) {
                        self::generateDebugInfo();
                    }

                    if (Core::isInitialized()) {
                        Core::closeDBLink();
                    }

                    return true;
                }

            } catch (TemplateNotFoundException $e) {
                Core::closeDBLink();
                //header("HTTP/1.0 404 Not Found", true, 404);
                throw $e;

            } catch (ActionNotFoundException $e) {
                Core::closeDBLink();
                header("HTTP/1.0 404 Not Found", true, 404);
                throw $e;

            } catch (ActionNotAllowedException $e) {
                self::$_current_controller_object = new Common();
                self::$_current_controller_object['message'] = $e->getMessage();

                self::$_current_controller_method = 'runForbidden';
                self::$_current_controller_module = 'main';

                self::performAction();

            } catch (CSRFFailureException $e) {
                Core::closeDBLink();
                if (self::isDebugMode()) {
                    self::generateDebugInfo();
                }

                self::getResponse()->setHttpStatus(403);
                $message = $e->getMessage();

                if (!self::isCLI() && self::getRequest()->isResponseFormatAccepted('application/json', false)) {
                    self::getResponse()->setContentType('application/json');
                    $message = json_encode(['message' => $message]);
                }

                self::getResponse()->renderHeaders();
                echo $message;

            } catch (Exception $e) {
                Core::closeDBLink();
                //header("HTTP/1.0 404 Not Found", true, 404);
                throw $e;
            }
        }

        protected static function initializeUser()
        {
            Logging::log('Loading user');
            try {
                Logging::log('is this logout?');
                if (self::getRequest()->getParameter('logout')) {
                    Logging::log('yes');
                    self::logout();
                } else {
                    Logging::log('no');
                    Logging::log('sets up user object');
                    $event = Event::createNew('core', 'pre_login');
                    $event->trigger();

                    if ($event->isProcessed())
                        self::loadUser($event->getReturnValue());
                    elseif (!self::isCLI())
                        self::loadUser(null);
                    else
                        self::$_user = new User();

                    Event::createNew('core', 'post_login', self::getUser())->trigger();

                    Logging::log('loaded');
                    Logging::log('caching permissions');
                    self::cacheAllPermissions();
                    Logging::log('done (caching permissions)');
                }
            } catch (exceptions\TwoFactorAuthenticationException $e) {
                Logging::log("Could not authenticate 2FA token: " . $e->getMessage(), 'main', Logging::LEVEL_INFO);
                self::setMessage('elevated_login_message_err', $e->getMessage());
                self::$_redirect_login = '2fa_login';
            } catch (exceptions\ElevatedLoginException $e) {
                Logging::log("Could not reauthenticate elevated permissions: " . $e->getMessage(), 'main', Logging::LEVEL_INFO);
                self::setMessage('elevated_login_message_err', $e->getMessage());
                self::$_redirect_login = 'auth_elevated_login';
            } catch (Exception $e) {
                Logging::log("Something happened while setting up user: " . $e->getMessage(), 'main', Logging::LEVEL_WARNING);

                $is_anonymous_route = self::isCLI() || self::getRouting()->getCurrentRoute()->isAnonymous();

                if (!$is_anonymous_route) {
                    self::setMessage('login_message_err', $e->getMessage());
                    self::$_redirect_login = 'login';
                } else {
                    self::$_user = User::getB2DBTable()->selectById(Settings::getDefaultUserID());
                }
            }
            Logging::log('...done');
        }

        /**
         * Log out the current user (does not work when auth method is set to http)
         */
        public static function logout()
        {
            $authentication_backend = Settings::getAuthenticationBackend();
            $authentication_backend->logout();

            Event::createNew('core', 'pre_logout')->trigger();
            self::getResponse()->deleteCookie('PACHNO');
            session_regenerate_id(true);
            Event::createNew('core', 'post_logout')->trigger();
        }

        /**
         * Load the user object into the user property
         *
         * @param User $user
         *
         * @return User
         */
        public static function loadUser(User $user = null)
        {
            try {
                self::$_user = ($user) ?? User::identify(self::getRequest(), self::getCurrentControllerObject(), true);
                if (self::$_user->isAuthenticated()) {
                    if (!self::getRequest()->hasCookie('original_username')) {
                        self::$_user->updateLastSeen();
                    }
                    if (!self::getScope()->isDefault() && !self::getRequest()->isAjaxCall() && !in_array(self::getRouting()->getCurrentRoute()->getName(), ['auth_add_scope', 'debugger', 'auth_logout']) && !self::$_user->isGuest() && !self::$_user->isConfirmedMemberOfScope(self::getScope())) {
                        self::getResponse()->headerRedirect(self::getRouting()->generate('add_scope'));
                    }
                    self::$_user->save();
                    if (!(self::$_user->getGroup() instanceof Group)) {
                        throw new RuntimeException('This user account belongs to a group that does not exist anymore. Please contact the system administrator.');
                    }
                }
            } catch (exceptions\ElevatedLoginException $e) {
                throw $e;
            } catch (Exception $e) {
                self::$_user = new User();
                throw $e;
            }

            return self::$_user;
        }

        /**
         * Returns the current action object
         *
         * @return Action
         */
        public static function getCurrentControllerObject()
        {
            return self::$_current_controller_object;
        }

        /**
         * Set a message to be retrieved in the next request
         *
         * @param string $key The key
         * @param mixed $message The message
         */
        public static function setMessage($key, $message)
        {
            if (!array_key_exists('pachno_flash_message', $_SESSION)) {
                $_SESSION['pachno_flash_message'] = [];
            }
            $_SESSION['pachno_flash_message'][$key] = $message;
        }

        /**
         * Performs an action.
         *
         * @return bool
         * @throws Exception
         * @throws CSRFFailureException
         */
        public static function performAction()
        {
            // Set content variable
            $content = null;

            // Set the template to be used when rendering the html (or other) output
            $templateBasePath = (self::isInternalModule(self::$_current_controller_module)) ? PACHNO_INTERNAL_MODULES_PATH : PACHNO_MODULES_PATH;
            $templatePath = $templateBasePath . self::$_current_controller_module . DS . 'templates' . DS;

            $controllerClassName = get_class(self::$_current_controller_object);
            $unPrefixedControllerMethod = substr(self::$_current_controller_method, 3);
            $preActionToRunName = 'pre' . $unPrefixedControllerMethod;

            // Set up the response object, responsible for controlling any output
            self::getResponse()->setPage(self::getRouting()->getCurrentRoute()->getName());
            self::getResponse()->setTemplate(mb_strtolower($unPrefixedControllerMethod) . '.' . self::getRequest()->getRequestedFormat() . '.php');
            self::getResponse()->setupResponseContentType(self::getRequest()->getRequestedFormat());
            self::setCurrentProject(null);

            // Run the specified action method set if it exists
            if (method_exists(self::$_current_controller_object, self::$_current_controller_method)) {
                // Turning on output buffering
                ob_start('mb_output_handler');
                ob_implicit_flush(0);

                if (self::getRouting()->getCurrentRoute()->isCsrfProtected() && !self::checkCsrfToken()) {
                    return true;
                }

                if (self::$_debug_mode) {
                    $time = explode(' ', microtime());
                    $pretime = $time[1] + $time[0];
                }
                if ($content === null) {
                    Logging::log('Running main pre-execute action');
                    // Running any overridden preExecute() method defined for that module
                    // or the default empty one provided by \pachno\core\framework\Action
                    if ($pre_action_result = self::$_current_controller_object->preExecute(self::getRequest(), self::$_current_controller_method)) {
                        $content = ob_get_clean();
                        Logging::log('preexecute method returned something, skipping further action');
                        if (self::$_debug_mode)
                            $visited_templatename = "{$controllerClassName}::preExecute()";
                    }
                }

                if ($content === null) {
                    $action_output = null;
                    if (self::getResponse()->getHttpStatus() == 200) {
                        // Checking for and running action-specific preExecute() function if
                        // it exists
                        if (method_exists(self::$_current_controller_object, $preActionToRunName)) {
                            Logging::log('Running custom pre-execute action');
                            self::$_current_controller_object->$preActionToRunName(self::getRequest(), self::$_current_controller_method);
                        }

                        // Running main route action
                        Logging::log('Running route action ' . self::$_current_controller_method . '()');
                        if (self::$_debug_mode) {
                            $time = explode(' ', microtime());
                            $action_pretime = $time[1] + $time[0];
                        }
                        $action_output = self::$_current_controller_object->{self::$_current_controller_method}(self::getRequest());

                        if (self::$_debug_mode) {
                            $time = explode(' ', microtime());
                            $action_posttime = $time[1] + $time[0];
                            self::visitPartial("{$controllerClassName}::" . self::$_current_controller_method . "()", $action_posttime - $action_pretime);
                        } else {
                            //session_write_close();
                        }
                    }
                    if ($action_output && self::getResponse()->getHttpStatus() == 200) {
                        // If the action returns *any* output, we're done, and collect the
                        // output to a variable to be outputted in context later
                        $content = ob_get_clean();
                        Logging::log('...done');
                    } elseif (!$action_output) {
                        // If the action doesn't return any output (which it usually doesn't)
                        // we continue on to rendering the template file for that specific action
                        Logging::log('...done');
                        Logging::log('Displaying template');

                        // Check to see if we have a translated version of the template
                        if (self::$_current_controller_method == 'runNotFound' && self::$_current_controller_module == 'main') {
                            $templateName = $templatePath . self::getResponse()->getTemplate();
                        } elseif (!self::isReadySetup() || ($templateName = self::getI18n()->hasTranslatedTemplate(self::getResponse()->getTemplate())) === false) {
                            // Check to see if any modules provide an alternate template
                            $event = Event::createNew('core', "self::performAction::renderTemplate")->triggerUntilProcessed(['class' => $controllerClassName, 'action' => self::$_current_controller_method]);
                            if ($event->isProcessed()) {
                                $templateName = $event->getReturnValue();
                            }

                            // Check to see if the template has been changed, and whether it's in a
                            // different module, specified by "module/templatename"
                            if (mb_strpos(self::getResponse()->getTemplate(), '/')) {
                                $newPath = explode('/', self::getResponse()->getTemplate());
                                $templateName = (self::isInternalModule($newPath[0])) ? PACHNO_INTERNAL_MODULES_PATH : PACHNO_MODULES_PATH;
                                $templateName .= $newPath[0] . DS . 'templates' . DS . $newPath[1] . '.' . self::getRequest()->getRequestedFormat() . '.php';
                            } else {
                                $templateName = $templatePath . self::getResponse()->getTemplate();
                            }
                        }

                        // Check to see if the template exists and throw an exception otherwise
                        if (!isset($templateName) || !file_exists($templateName)) {
                            Logging::log('The template file for the ' . self::$_current_controller_method . ' action ("' . self::getResponse()->getTemplate() . '") does not exist', 'core', Logging::LEVEL_FATAL);
                            Logging::log('Trying to load file "' . $templateName . '"', 'core', Logging::LEVEL_FATAL);
                            throw new exceptions\TemplateNotFoundException('The template file for the ' . self::$_current_controller_method . ' action ("' . self::getResponse()->getTemplate() . '") does not exist');
                        }

                        self::loadLibrary('common');
                        // Present template for current action
                        ActionComponent::presentTemplate($templateName, self::$_current_controller_object->getParameterHolder());
                        $content = ob_get_clean();
                        Logging::log('...completed');
                    }
                } elseif (self::$_debug_mode) {
                    $time = explode(' ', microtime());
                    $posttime = $time[1] + $time[0];
                    self::visitPartial($visited_templatename, $posttime - $pretime);
                }

                Logging::log('rendering final content');

                // Set core layout path
                self::getResponse()->setLayoutPath(PACHNO_CORE_PATH . 'templates');

                // Trigger event for rendering (so layout path can be overwritten)
                Event::createNew('core', '\pachno\core\framework\Context::renderBegins')->trigger();

                if (Settings::isMaintenanceModeEnabled() && !mb_strstr(self::getRouting()->getCurrentRoute()->getName(), 'configure')) {
                    if (!file_exists(self::getResponse()->getLayoutPath() . DS . 'offline.inc.php')) {
                        throw new exceptions\TemplateNotFoundException('Can not find offline mode template');
                    }
                    ob_start('mb_output_handler');
                    ob_implicit_flush(0);
                    ActionComponent::presentTemplate(self::getResponse()->getLayoutPath() . DS . 'offline.inc.php');
                    $content = ob_get_clean();
                }

                // Render output in correct order
                self::getResponse()->renderHeaders();

                if (self::getResponse()->getDecoration() == Response::DECORATE_DEFAULT && !self::getRequest()->isAjaxCall()) {
                    if (!file_exists(self::getResponse()->getLayoutPath() . DS . 'layout.php')) {
                        throw new exceptions\TemplateNotFoundException('Can not find layout template');
                    }
                    ob_start('mb_output_handler');
                    ob_implicit_flush(0);
                    $layout_properties = self::setupLayoutProperties($content);
                    ActionComponent::presentTemplate(self::getResponse()->getLayoutPath() . DS . 'layout.php', $layout_properties);
                    ob_flush();
                } else {
                    // Render header template if any, and store the output in a variable
                    if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateHeader()) {
                        Logging::log('decorating with header');
                        if (!file_exists(self::getResponse()->getHeaderDecoration())) {
                            throw new exceptions\TemplateNotFoundException('Can not find header decoration: ' . self::getResponse()->getHeaderDecoration());
                        }
                        ActionComponent::presentTemplate(self::getResponse()->getHeaderDecoration());
                    }

                    echo $content;

                    // Trigger event for ending the rendering
                    Event::createNew('core', '\pachno\core\framework\Context::renderEnds')->trigger();

                    Logging::log('...done (rendering content)');

                    // Render footer template if any
                    if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateFooter()) {
                        Logging::log('decorating with footer');
                        if (!file_exists(self::getResponse()->getFooterDecoration())) {
                            throw new exceptions\TemplateNotFoundException('Can not find footer decoration: ' . self::getResponse()->getFooterDecoration());
                        }
                        ActionComponent::presentTemplate(self::getResponse()->getFooterDecoration());
                    }

                    Logging::log('...done');
                }
                Logging::log('done (rendering final content)');

                return true;
            } else {
                Logging::log("Cannot find the method " . self::$_current_controller_method . "() in class {$controllerClassName}.", 'core', Logging::LEVEL_FATAL);
                throw new exceptions\ActionNotFoundException("Cannot find the method " . self::$_current_controller_method . "() in class {$controllerClassName}. Make sure the method exists.");
            }
        }

        public static function isInternalModule($module)
        {
            return isset(self::$_internal_modules[$module]);
        }

        /**
         * Set the currently selected project
         *
         * @param Project $project The project, or null if none
         */
        public static function setCurrentProject($project)
        {
            self::$_selected_project = $project;
        }

        /**
         * @return bool
         * @throws CSRFFailureException
         */
        public static function checkCsrfToken()
        {
            $token = self::getCsrfToken();
            if ($token == self::getRequest()->getParameter('csrf_token'))
                return true;

            $message = self::getI18n()->__('An authentication error occured. Please reload your page and try again');
            throw new exceptions\CSRFFailureException($message);
        }

        public static function getCsrfToken()
        {
            if (!array_key_exists('csrf_token', $_SESSION) || $_SESSION['csrf_token'] == '') {
                $_SESSION['csrf_token'] = Uuid::uuid4()->toString();
            }

            return $_SESSION['csrf_token'];
        }

        public static function visitPartial($template_name, $time)
        {
            if (!self::$_debug_mode)
                return;
            if (!array_key_exists($template_name, self::$_partials_visited)) {
                self::$_partials_visited[$template_name] = ['time' => $time, 'count' => 1];
            } else {
                self::$_partials_visited[$template_name]['count']++;
                self::$_partials_visited[$template_name]['time'] += $time;
            }
        }

        /**
         * Loads a function library
         *
         * @param string $lib_name The name of the library
         */
        public static function loadLibrary($lib_name)
        {
            if (mb_strpos($lib_name, '/') !== false) {
                [$module, $lib_name] = explode('/', $lib_name);
            }

            // Skip the library if it already exists
            if (!array_key_exists($lib_name, self::$_libs)) {
                $lib_file_name = "{$lib_name}.inc.php";

                if (isset($module) && file_exists(PACHNO_MODULES_PATH . $module . DS . 'lib' . DS . $lib_file_name)) {
                    require PACHNO_MODULES_PATH . $module . DS . 'lib' . DS . $lib_file_name;
                    self::$_libs[$lib_name] = PACHNO_MODULES_PATH . $module . DS . 'lib' . DS . $lib_file_name;
                } elseif (file_exists(PACHNO_MODULES_PATH . self::getRouting()->getCurrentRoute()->getModuleName() . DS . 'lib' . DS . $lib_file_name)) {
                    // Include the library from the current module if it exists
                    require PACHNO_MODULES_PATH . self::getRouting()->getCurrentRoute()->getModuleName() . DS . 'lib' . DS . $lib_file_name;
                    self::$_libs[$lib_name] = PACHNO_MODULES_PATH . self::getRouting()->getCurrentRoute()->getModuleName() . DS . 'lib' . DS . $lib_file_name;
                } elseif (file_exists(PACHNO_CORE_PATH . 'lib' . DS . $lib_file_name)) {
                    // Include the library from the global library directory if it exists
                    require PACHNO_CORE_PATH . 'lib' . DS . $lib_file_name;
                    self::$_libs[$lib_name] = PACHNO_CORE_PATH . 'lib' . DS . $lib_file_name;
                } else {
                    // Throw an exception if the library can't be found in any of
                    // the above directories
                    Logging::log("The \"{$lib_name}\" library does not exist in either " . PACHNO_MODULES_PATH . self::getRouting()->getCurrentRoute()->getModuleName() . DS . 'lib' . DS . ' or ' . PACHNO_CORE_PATH . 'lib' . DS, 'core', Logging::LEVEL_FATAL);
                    throw new exceptions\LibraryNotFoundException("The \"{$lib_name}\" library does not exist in either " . PACHNO_MODULES_PATH . self::getRouting()->getCurrentRoute()->getModuleName() . DS . 'lib' . DS . ' or ' . PACHNO_CORE_PATH . 'lib' . DS);
                }
            }
        }

        protected static function setupLayoutProperties($content)
        {
            $basepath = PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . DS;
            $theme = Settings::getThemeName();
            $themepath = PACHNO_PATH . 'themes' . DS . $theme . DS;
            foreach (self::getModules() as $module) {
                $module_path = (self::isInternalModule($module->getName())) ? PACHNO_INTERNAL_MODULES_PATH : PACHNO_MODULES_PATH;
                $module_name = $module->getName();
                if (file_exists($module_path . $module_name . DS . 'public' . DS . 'css' . DS . "{$module_name}.css")) {
                    self::getResponse()->addStylesheet(self::getRouting()->generate('asset_module_css', ['module_name' => $module_name, 'css' => "{$module_name}.css"]));
                }
                if (file_exists($module_path . $module_name . DS . 'public' . DS . 'js' . DS . "{$module_name}.js")) {
                    self::getResponse()->addJavascript(self::getRouting()->generate('asset_module_js', ['module_name' => $module_name, 'js' => "{$module_name}.js"], false));
                    //self::getResponse()->addJavascript("module/{$module_name}/{$module_name}.js");
                }
                if (file_exists($themepath . 'css' . DS . "{$module_name}.css")) {
                    self::getResponse()->addStylesheet(self::getRouting()->generate('asset_css', ['theme_name' => $theme, 'css' => "{$module_name}.css"]));
                }
                if (file_exists($themepath . 'js' . DS . "theme.js")) {
                    //self::getResponse()->addJavascript(self::getRouting()->generate('asset_js', ['theme_name' => $theme, 'js' => "theme.js"], false));
                }
                if (file_exists($basepath . 'js' . DS . "{$module_name}.js")) {
                    //self::getResponse()->addJavascript(self::getRouting()->generate('asset_js_unthemed', ['js' => "{$module_name}.js"]));
                }
            }

            [$localjs, $externaljs] = self::getResponse()->getJavascripts();
            $webroot = self::getWebroot();

            $values = compact('content', 'localjs', 'externaljs', 'webroot');

            return $values;
        }

        public static function getDebugData($debug_id)
        {
            if (!array_key_exists('___DEBUGINFO___', $_SESSION))
                return null;
            if (!array_key_exists($debug_id, $_SESSION['___DEBUGINFO___']))
                return null;

            return $_SESSION['___DEBUGINFO___'][$debug_id];
        }

        public static function getURLhost()
        {
            return self::getScope()->getCurrentHostname();
        }

        public static function getCurrentCLIusername()
        {
            if (extension_loaded('posix')) {
                // Original code
                $processUser = posix_getpwuid(posix_geteuid());

                return $processUser['name'];
            } else {
                // Try to get CLI process owner without the POSIX extension
                $environmentUser = getenv('USERNAME');
                if ($environmentUser === false) {
                    $environmentUser = 'Unknown';
                }

                return $environmentUser;
            }
        }

        /**
         * Whether to serve minified asset files (JS and CSS)
         *
         * @return bool
         *   true, if asset files shall be delivered minified, false otherwise.
         */
        public static function isMinifiedAssets()
        {
            return !empty(self::$_configuration['core']['minified_assets']);
        }

        /**
         * Retrieves information about the latest available version from the official website.
         *
         * @return array
         *
         *   null, if latest available version information could not be
         *   retrieved due to errors, otherwise an array describing the
         *   latest available version with the following keys:
         *
         *   maj
         *     Major version number.
         *
         *   min
         *     Minor version number.
         *
         *   rev
         *     Revision version number.
         *
         *   nicever
         *     Formatted version string suitable for showing to user.
         */
        public static function getLatestAvailableVersionInformation()
        {
            // Use cached information if available.
            if (self::$_latest_available_version !== null) {
                return self::$_latest_available_version;
            }

            // Set-up client and retrieve version information.
            $client = new \GuzzleHttp\Client([
                'base_uri' => 'https://pachno.com/',
                'http_errors' => false]);
            $response = $client->request('GET', '/updatecheck.php');

            // Verify status code.
            if ($response->getStatusCode() == 200) {
                // Decode response.
                $info = json_decode($response->getBody());

                // Cache value if response was decoded and necessary
                // information was read from it.
                if (is_object($info) && isset($info->maj, $info->min, $info->rev, $info->nicever)) {
                    self::$_latest_available_version = $info;
                }
            }

            return self::$_latest_available_version;
        }

        /**
         * Checks if an update is available based on passed-in version
         * information.
         *
         * @param array version Version information. Should contain keys: maj (major version number),
         *                      min (minor version number), rev (revision number),
         *                      nicever (formatted version string that can be shown to user).
         *
         * @return bool
         *   true, if an update is available, false otherwise.
         */
        public static function isUpdateAvailable($version)
        {
            $update_available = false;

            // Check if we are out of date.
            if ($version->maj > Settings::getMajorVer()) {
                $update_available = true;
            } elseif ($version->min > Settings::getMinorVer() && ($version->maj == Settings::getMajorVer())) {
                $update_available = true;
            } elseif ($version->rev > Settings::getRevision() && ($version->maj == Settings::getMajorVer()) && ($version->min == Settings::getMinorVer())) {
                $update_available = true;
            }

            return $update_available;
        }

    }
