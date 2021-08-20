<?php

    namespace pachno\core\framework;

    use b2db\AnnotationSet;
    use DirectoryIterator;
    use Exception;
    use pachno\core\framework\exceptions\InvalidRouteException;
    use pachno\core\framework\routing\Route;
    use ReflectionClass;
    use Spyc;
    use const PACHNO_CONFIGURATION_PATH;
    use const PACHNO_INTERNAL_MODULES_PATH;
    use const PACHNO_MODULES_PATH;

    /**
     * Routing class
     *
     * @package pachno
     * @subpackage mvc
     */
    class Routing
    {

        /**
         * @var Route[]
         */
        protected $routes = [];

        protected $component_override_map = [];

        protected $annotation_listeners = [];

        protected $has_cached_routes;

        /**
         * @var Route
         */
        protected $current_route;

        public function __construct(Route $current_route = null)
        {
            if ($current_route instanceof Route) {
                $this->current_route = $current_route;
            }
        }

        public function hasComponentOverride($component)
        {
            return array_key_exists($component, $this->component_override_map);
        }

        public function getComponentOverride($component)
        {
            return $this->component_override_map[$component];
        }

        /**
         * Load routes into the routing object
         *
         * @param bool $skip_cache Whether to skip the cache when reading routes
         */
        public function loadRoutes($skip_cache = false)
        {
            if ($skip_cache || !$this->hasCachedRoutes()) {
                $this->loadRoutesFromFiles();
                if (!$skip_cache) {
                    $this->cache();
                }
            } else {
                $this->loadCachedRoutes();
            }
        }

        public function hasCachedRoutes()
        {
            if ($this->has_cached_routes === null) {
                if (Context::isInstallmode()) {
                    $this->has_cached_routes = false;
                } else {
                    $this->has_cached_routes = Context::getCache()->has(Cache::KEY_ROUTES_CACHE);
                    if ($this->has_cached_routes) {
                        Logging::log('Routes are cached', 'routing');
                    } else {
                        Logging::log('Routes are not cached', 'routing');
                    }
                }
            }

            return $this->has_cached_routes;
        }

        /**
         * Loads routes by looking through module controllers and configuration files
         */
        protected function loadRoutesFromFiles()
        {
            Logging::log('Loading routes from routing files', 'routing');

            foreach (Context::getAllModules() as $modules) {
                foreach ($modules as $module_name => $module) {
                    $this->loadModuleRoutes($module_name);
                }
            }
            $this->loadYamlRoutes(PACHNO_CONFIGURATION_PATH . 'routes.yml');
            Context::loadEventListeners($this->getAnnotationListeners());

            Logging::log('...done (loading routes from routing file)', 'routing');
        }

        /**
         * Loads all routes within a module
         *
         * @param string $module_name
         */
        protected function loadModuleRoutes($module_name)
        {
            $module_path_prefix = (Context::isInternalModule($module_name)) ? PACHNO_INTERNAL_MODULES_PATH : PACHNO_MODULES_PATH;
            $module_routes_filename = $module_path_prefix . $module_name . DS . 'configuration' . DS . 'routes.yml';
            if (file_exists($module_routes_filename)) {
                $this->loadYamlRoutes($module_routes_filename, $module_name);
            }

            $this->loadAnnotationRoutes($module_name);
            $this->loadAnnotationListeners($module_name);
        }

        /**
         * Load all routes defined in a yaml file
         *
         * @param string $yaml_filename Full path to yaml file
         * @param string $module_name
         */
        protected function loadYamlRoutes($yaml_filename, $module_name = null)
        {
            $routes = Spyc::YAMLLoad($yaml_filename);

            foreach ($routes as $route => $details) {
                $details['module'] = ($details['module']) ?? $module_name;
                $this->addYamlRoute($route, $details);
            }
        }

        protected function addYamlRoute($key, $details)
        {
            $name = $key;
            $module = $details['module'];
            $action = $details['action'];
            if (array_key_exists('overrides', $details)) {
                $this->overrideRoute($name, $module, $details['overrides']);
            } else {
                $params = (array_key_exists('parameters', $details)) ? $details['parameters'] : [];

                $route = new Route($key, $module, $action, $details['route'], $params);

                if (array_key_exists('csrf_enabled', $details)) {
                    $route->setIsCsrfProtected($details['csrf_enabled']);
                }
                if (array_key_exists('anonymous_route', $details)) {
                    $route->setIsAnonymous($details['anonymous_route']);
                }
                if (array_key_exists('authentication_method', $details)) {
                    $route->setAuthenticationMethod($details['authentication_method']);
                }
                if (array_key_exists('methods', $details)) {
                    $route->setAllowedMethods($details['methods']);
                }

                $this->addRoute($route);
            }
        }

        /**
         * Override an existing route
         *
         * @param string $name
         * @param string $module
         * @param string $action
         */
        public function overrideRoute($name, $module, $action)
        {
            $this->routes[$name]->setModuleName($module);
            $this->routes[$name]->setModuleAction($action);
            $this->routes[$name]->setIsOverridden(true);
        }

        /**
         * Add a route to the route list
         *
         * @param Route $route
         */
        public function addRoute(Route $route)
        {
            $name = $route->getName();

            if ($this->hasRoute($name)) {
                if ($this->routes[$name]->isOverridden()) {
                    Logging::log("Skipping overridden route {$name}", 'routing');

                    return;
                }
            }

            $this->routes[$name] = $route;
        }

        public function hasRoute($route)
        {
            return array_key_exists($route, $this->routes);
        }

        protected function loadAnnotationRoutes($module_name)
        {
            $is_internal = Context::isInternalModule($module_name);
            $namespace = ($is_internal) ? '\\pachno\\core\\modules\\' : '\\pachno\\modules\\';
            $controller_path = ($is_internal) ? PACHNO_INTERNAL_MODULES_PATH . $module_name . '/controllers' : PACHNO_MODULES_PATH . $module_name . '/controllers';

            if (file_exists($controller_path)) {
                // Point the annotated routes to the right module controllers
                foreach (new DirectoryIterator($controller_path) as $controller) {
                    if (!$controller->isDot()) {
                        $this->loadModuleAnnotationRoutes($namespace . $module_name . '\\controllers\\' . $controller->getBasename('.php'), $module_name);
                    }
                }
            }

            if (!$is_internal) {
                $this->loadModuleComponentOverrideMappings($namespace . $module_name . '\\Components', $module_name);
            }
        }

        protected function loadModuleAnnotationRoutes($classname, $module_name)
        {
            if (!class_exists($classname)) {
                return;
            }

            $internal = Context::isInternalModule($module_name);
            $reflection = new ReflectionClass($classname);
            $docblock = $reflection->getDocComment();
            $annotation_set = new AnnotationSet($docblock);
            $paths = explode('/', str_replace('\\', '/', $classname));
            $controller = array_pop($paths);

            $route_url_prefix = '';
            $route_name_prefix = '';
            $default_route_name_prefix = ($internal) ? '' : $module_name . '_';
            if ($annotation_set->hasAnnotation('Routes')) {
                $routes = $annotation_set->getAnnotation('Routes');
                if ($routes->hasProperty('url_prefix')) {
                    $route_url_prefix = $routes->getProperty('url_prefix');
                }
                if ($routes->hasProperty('name_prefix')) {
                    $route_name_prefix = $routes->getProperty('name_prefix', $default_route_name_prefix);
                }
            } else {
                $route_name_prefix = $default_route_name_prefix;
            }

            foreach ($reflection->getMethods() as $method) {
                $annotation_set = new AnnotationSet($method->getDocComment());
                if ($annotation_set->hasAnnotation('Route')) {
                    $route = Route::fromAnnotation($module_name, $controller, $route_name_prefix, $route_url_prefix, $method, $annotation_set);

                    if ($annotation_set->hasAnnotation('Overrides')) {
                        $name = $annotation_set->getAnnotation('Overrides')->getProperty('name');
                        $this->overrideRoute($name, $module_name, $route->getModuleAction());
                    } else {
                        if ($this->hasRoute($route->getName())) {
                            throw new exceptions\RoutingException("Trying to override route '{$route->getName()}' in {$module_name}/{$route->getModuleAction()}. A route that overrides another route must have an @Override annotation");
                        }

                        $this->addRoute($route);
                    }
                }
            }
        }

        /**
         * Load all component overrides defined in a module class
         *
         * @param string $classname
         * @param string $module_name
         */
        protected function loadModuleComponentOverrideMappings($classname, $module_name)
        {
            if (!class_exists($classname)) {
                return;
            }

            $reflection = new ReflectionClass($classname);
            foreach ($reflection->getMethods() as $method) {
                $annotation_set = new AnnotationSet($method->getDocComment());
                if ($annotation_set->hasAnnotation('Overrides')) {
                    $overridden_component = $annotation_set->getAnnotation('Overrides')->getProperty('name');
                    $component = ['module' => $module_name, 'method' => substr($method->name, 9)];
                    $this->component_override_map[$overridden_component] = $component;
                }
            }
        }

        /**
         * Load all listeners in a module defined through annotations
         *
         * @param string $module_name
         */
        protected function loadAnnotationListeners($module_name)
        {
            $is_internal = Context::isInternalModule($module_name);
            $namespace = ($is_internal) ? '\\pachno\\core\\modules\\' : '\\pachno\\modules\\';
            $classname = $namespace . $module_name . '\\' . ucfirst($module_name);

            if (!class_exists($classname)) {
                return;
            }

            $reflection = new ReflectionClass($classname);
            foreach ($reflection->getMethods() as $method) {
                $annotation_set = new AnnotationSet($method->getDocComment());
                if ($annotation_set->hasAnnotation('Listener')) {
                    $listener_annotation = $annotation_set->getAnnotation('Listener');
                    $event_module = $listener_annotation->getProperty('module');
                    $event_identifier = $listener_annotation->getProperty('identifier');
                    $this->annotation_listeners[] = [$event_module, $event_identifier, $module_name, $method->name];
                }
            }
        }

        /**
         * Get all registered annotation module listeners
         *
         * @return array
         */
        public function getAnnotationListeners()
        {
            return $this->annotation_listeners;
        }

        /**
         * Set component override map manually (used by cache functions)
         *
         * @param array $annotation_listeners
         */
        public function setAnnotationListeners($annotation_listeners)
        {
            $this->annotation_listeners = $annotation_listeners;
        }

        public function cache()
        {
            Context::getCache()->fileAdd(Cache::KEY_ROUTES_CACHE, $this->getRoutes());
            Context::getCache()->add(Cache::KEY_ROUTES_CACHE, $this->getRoutes());
            Context::getCache()->fileAdd(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, $this->getComponentOverrideMap());
            Context::getCache()->add(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, $this->getComponentOverrideMap());
            Context::getCache()->fileAdd(Cache::KEY_ANNOTATION_LISTENERS_CACHE, $this->getAnnotationListeners());
            Context::getCache()->add(Cache::KEY_ANNOTATION_LISTENERS_CACHE, $this->getAnnotationListeners());
        }

        /**
         * Get all the routes
         *
         * @return Route[]
         */
        public function getRoutes()
        {
            return $this->routes;
        }

        /**
         * Set all routes manually (used by cache functions)
         *
         * @param array $routes
         */
        public function setRoutes($routes)
        {
            $this->routes = $routes;
        }

        /**
         * Get all component override mappings
         *
         * @return array
         */
        public function getComponentOverrideMap()
        {
            return $this->component_override_map;
        }

        /**
         * Set component override map manually (used by cache functions)
         *
         * @param array $component_override_map
         */
        public function setComponentOverrideMap($component_override_map)
        {
            $this->component_override_map = $component_override_map;
        }

        /**
         * Loads routes by reading them from the cache
         */
        protected function loadCachedRoutes()
        {
            Logging::log('Loading routes from cache', 'routing');
            $cache = Context::getCache();
            $routes = $cache->get(Cache::KEY_ROUTES_CACHE);
            $component_override_map = $cache->get(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE);
            $annotation_listeners = $cache->get(Cache::KEY_ANNOTATION_LISTENERS_CACHE);

            if ($routes === null) {
                Logging::log('Loading routes from disk cache', 'routing');
                $routes = $cache->fileGet(Cache::KEY_ROUTES_CACHE);
            }
            if ($component_override_map === null) {
                Logging::log('Loading component override mappings from disk cache', 'routing');
                $component_override_map = $cache->fileGet(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE);
            }
            if ($annotation_listeners === null) {
                Logging::log('Loading event listeners from disk cache', 'routing');
                $annotation_listeners = $cache->fileGet(Cache::KEY_ANNOTATION_LISTENERS_CACHE);
            }

            if ($routes === null || $component_override_map === null || $annotation_listeners === null) {
                throw new exceptions\CacheException('There is an issue with the cache. Clear the cache and try again.');
            }

            Logging::log('Setting routes from cache', 'routing');
            $this->setRoutes($routes);
            Logging::log('Setting component override mappings from cache', 'routing');
            $this->setComponentOverrideMap($component_override_map);
            Logging::log('Setting annotation listeners from cache', 'routing');
            Context::loadEventListeners($annotation_listeners);
            Logging::log('...done', 'routing');
        }

        /**
         * Get route details from a given url
         *
         * @param string $url The url to retrieve details from
         *
         * @return ?Route
         */
        public function getRouteFromUrl(string $url): ?Route
        {
            Logging::log("URL is '" . htmlentities($url, ENT_COMPAT, 'utf-8') . "'", 'routing');
            // an URL should start with a '/', mod_rewrite doesn't respect that, but no-mod_rewrite version does.
            if (mb_strlen($url) == 0 || '/' != $url[0]) {
                $url = '/' . $url;
            }
            if (mb_strlen($url) > 1 && mb_substr($url, -1) == '/') {
                $url = mb_substr($url, 0, -1);
            }
            Logging::log("URL is now '" . htmlentities($url, ENT_COMPAT, 'utf-8') . "'", 'routing');

            // we remove the query string
            if ($pos = mb_strpos($url, '?')) {
                $url = mb_substr($url, 0, $pos);
            }

            $route = null;

            // we remove multiple /
            $url = preg_replace('#/+#', '/', $url);
            Logging::log("URL is now '" . htmlentities($url, ENT_COMPAT, 'utf-8') . "'", 'routing');
            foreach ($this->routes as $route_name => $route) {
                if ($route->match($url)) {
                    break;
                }
            }

            // no route found
            if (!$route instanceof Route) {
                Logging::log('no matching route found', 'routing');

                return null;
            }

            return $route;
        }

        public function getCurrentRouteAuthenticationMethod(Action $action)
        {
            if ($this->getCurrentRoute()->getAuthenticationMethod() != '') {
                return constant('\pachno\core\framework\Action::' . $this->getCurrentRoute()->getAuthenticationMethod());
            }

            return $action->getAuthenticationMethodForAction($this->getCurrentRoute()->getModuleAction());
        }

        /**
         * @return Route
         */
        public function getCurrentRoute()
        {
            return $this->current_route;
        }

        /**
         * Set the current route
         *
         * @param Route $route
         */
        public function setCurrentRoute(Route $route)
        {
            Logging::log('match route [' . $route->getName() . '] "' . $route->getUrl() . '"', 'routing');
            $this->current_route = $route;

            foreach ($route->getMatchedParameters() as $key => $val) {
                Context::getRequest()->setParameter($key, $val);
            }
        }

        /**
         * Generate a url based on a route
         *
         * @param string $name The route key
         * @param array $params key=>value pairs of route parameters
         * @param boolean $relative Whether to generate an url relative to web root or an absolute
         *
         * @return string
         */
        public function generate($name, $params = [], $relative = true, $querydiv = '/', $divider = '/', $equals = '/')
        {
            if (!isset($this->routes[$name])) {
                Logging::log("The route '$name' does not exist", 'routing', Logging::LEVEL_FATAL);
                throw new InvalidRouteException("The route '$name' does not exist");
            }

            $route = $this->routes[$name];

            $defaults = [
                'action' => $route->getModuleAction(),
                'module' => $route->getModuleName()
            ];

            $params = self::arrayDeepMerge($defaults, $params);
            if ($route->isCsrfProtected()) {
                $params['csrf_token'] = Context::getCsrfToken();
                $defaults['csrf_token'] = Context::getCsrfToken();
            }

            // all params must be given
            foreach ($route->getParameterNames() as $tmp) {
                if (!isset($params[$tmp]) && !isset($defaults[$tmp])) {
                    if ($tmp == 'csrf_token') {
                        throw new Exception(sprintf('Route named "%s" have a mandatory "%s" parameter, but is not marked as csrf protected.', $name, $tmp));
                    } else {
                        throw new Exception(sprintf('Route named "%s" have a mandatory "%s" parameter', $name, $tmp));
                    }
                }
            }

            // in PHP 5.5, preg_replace with /e modifier is deprecated; preg_replace_callback is recommended
            $callback = function ($matches) use ($params) {
                return (array_key_exists($matches[1], $params)) ? urlencode($params[$matches[1]]) : $matches[0];
            };

            $real_url = preg_replace_callback('/\:([^\/]+)/', $callback, $route->getUrl());

            // we add all other params if *
            if (mb_strpos($real_url, '*')) {
                $tmp = [];
                foreach ($params as $key => $value) {
                    if (isset($defaults[$key])) continue;

                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if (is_array($v)) {
                                foreach ($v as $vk => $vv) {
                                    if (is_array($vv)) {
                                        foreach ($vv as $vvk => $vvv) {
                                            $tmp[] = "{$key}[{$k}][{$vk}][{$vvk}]" . $equals . urlencode($vvv);
                                        }
                                    } else {
                                        $tmp[] = "{$key}[{$k}][{$vk}]" . $equals . urlencode($vv);
                                    }
                                }
                            } else {
                                $tmp[] = "{$key}[{$k}]" . $equals . urlencode($v);
                            }
                        }
                    } else {
                        $tmp[] = urlencode($key) . $equals . urlencode($value);
                    }
                }
                $tmp = implode($divider, $tmp);
                if (mb_strlen($tmp) > 0) {
                    $tmp = $querydiv . $tmp;
                }
                $real_url = preg_replace('/\/\*(\/|$)/', "$tmp$1", $real_url);
            }

            // strip off last divider character
            if (mb_strlen($real_url) > 1) {
                $real_url = rtrim($real_url, $divider);
            }
            if (!$relative) {
                return Context::getURLhost() . Context::getStrippedWebroot() . $real_url;
            }

            return Context::getStrippedWebroot() . $real_url;
        }


        // code from php at moechofe dot com (array_merge comment on php.net)
        /*
         * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
         *
         * Like array_merge
         *
         *    arrayDeepMerge() merges the elements of one or more arrays together so
         * that the values of one are appended to the end of the previous one. It
         * returns the resulting array.
         *    If the input arrays have the same string keys, then the later value for
         * that key will overwrite the previous one. If, however, the arrays contain
         * numeric keys, the later value will not overwrite the original value, but
         * will be appended.
         *    If only one array is given and the array is numerically indexed, the keys
         * get reindexed in a continuous way.
         *
         * Different from array_merge
         *    If string keys have arrays for values, these arrays will merge recursively.
         */
        public static function arrayDeepMerge()
        {
            switch (func_num_args()) {
                case 0:
                    return false;
                case 1:
                    return func_get_arg(0);
                case 2:
                    $args = func_get_args();
                    $args[2] = [];
                    if (is_array($args[0]) && is_array($args[1])) {
                        foreach (array_unique(array_merge(array_keys($args[0]), array_keys($args[1]))) as $key) {
                            $isKey0 = array_key_exists($key, $args[0]);
                            $isKey1 = array_key_exists($key, $args[1]);
                            if ($isKey0 && $isKey1 && is_array($args[0][$key]) && is_array($args[1][$key])) {
                                $args[2][$key] = self::arrayDeepMerge($args[0][$key], $args[1][$key]);
                            } elseif ($isKey0 && $isKey1) {
                                $args[2][$key] = $args[1][$key];
                            } elseif (!$isKey1) {
                                $args[2][$key] = $args[0][$key];
                            } elseif (!$isKey0) {
                                $args[2][$key] = $args[1][$key];
                            }
                        }

                        return $args[2];
                    } else {
                        return $args[1];
                    }
                default :
                    $args = func_get_args();
                    $args[1] = self::arrayDeepMerge($args[0], $args[1]);
                    array_shift($args);

                    return call_user_func_array(['self', 'arrayDeepMerge'], $args);
                    break;
            }
        }

    }
