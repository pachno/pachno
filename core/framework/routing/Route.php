<?php

    namespace pachno\core\framework\routing;

    use b2db\Annotation;
    use b2db\AnnotationSet;
    use pachno\core\framework\Action;
    use pachno\core\framework\Context;
    use pachno\core\framework\exceptions\RoutingException;
    use pachno\core\framework\Logging;
    use pachno\core\framework\exceptions\InvalidRouteException;

    /**
     * Route class for individual routes
     */
    class Route
    {

        protected $name;

        protected $url;

        protected $regex;

        protected $module_name;

        protected $module_action;

        protected $is_overridden = false;

        protected $is_anonymous = false;

        protected $authentication_method;

        protected $is_csrf_protected = false;

        protected $parameters = [];

        protected $names = [];

        protected $matched_parameters = [];

        protected $allowed_methods = ['*'];

        protected $generated_method;

        /**
         * Create a route object from an @Route annotation
         *
         * @param string $module_name
         * @param string $controller
         * @param string $route_name_prefix
         * @param string $route_url_prefix
         * @param \ReflectionMethod $method
         * @param AnnotationSet $annotationSet
         *
         * @return self
         */
        public static function fromAnnotation($module_name, $controller, $route_name_prefix, $route_url_prefix, \ReflectionMethod $method, AnnotationSet $annotationSet)
        {
            if (strpos($method->name, 'run') !== 0) {
                throw new InvalidRouteException('A @Route annotation can only be used on methods prefixed with "run"');
            }

            $route_annotation = $annotationSet->getAnnotation('Route');
            $actionName = substr($method->name, 3);
            $action = $controller . '::' . $actionName;
            $name = $route_name_prefix . (($route_annotation->hasProperty('name')) ? $route_annotation->getProperty('name') : strtolower($actionName));
            $url = rtrim($route_url_prefix . $route_annotation->getProperty('url'), '/');
            $parameters = ($annotationSet->hasAnnotation('Parameters')) ? $annotationSet->getAnnotation('Parameters')->getProperties() : [];

            $route = new self($name, $module_name, $action, $url, $parameters);
            $route->setIsCsrfProtected($annotationSet->hasAnnotation('CsrfProtected'));
            $route->setIsAnonymous($annotationSet->hasAnnotation('AnonymousRoute'));
            $route->setAllowedMethods($route_annotation->getProperty('methods', []));
            if ($annotationSet->hasAnnotation('AuthenticationMethod')) {
                $route->setAuthenticationMethod($annotationSet->getAnnotation('AuthenticationMethod'));
            }

            return $route;
        }

        public function __construct($name, $module_name, $module_action, $url = null, $parameters = [], $allowed_methods = [])
        {
            $this->name = $name;
            $this->url = $url;
            $this->module_name = $module_name;
            $this->module_action = $module_action;

            if (!empty($parameters)) {
                $this->parameters = $parameters;
            }

            if ((is_array($allowed_methods) && !empty($allowed_methods)) || (!is_array($allowed_methods) && !empty($allowed_methods))) {
                $this->setAllowedMethods($allowed_methods);
            }

            if ($this->url !== null) {
                $this->generateRegex();
            }
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name): void
        {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getUrl()
        {
            return $this->url;
        }

        /**
         * @param mixed $url
         */
        public function setUrl($url): void
        {
            $this->url = $url;
        }

        protected function generateRegex()
        {
            if ($this->url == '' || $this->url == '/') {
                $this->regex = '/^[\/]*$/';
            } else {
                $elements = [];
                foreach (explode('/', $this->url) as $element) {
                    if (trim($element)) {
                        $elements[] = $element;
                    }
                }

                if (!isset($elements[0])) {
                    throw new RoutingException('Invalid route ' . $this->name . ' with url regex "' . $this->url . '"');
                }

                // specific suffix for this route?
                // or /$ directory
                $suffix = '';
                $route = $this->url;
                if (preg_match('/^(.+)(\.\w*)$/i', $elements[count($elements) - 1], $matches)) {
                    $suffix = ($matches[2][0] == '.') ? $matches[2] : '';
                    $elements[count($elements) - 1] = $matches[1];
                } elseif ($route{mb_strlen($route) - 1} == '/') {
                    $suffix = '/';
                }

                $regexp_suffix = preg_quote($suffix);
                $parsed = [];
                $names = [];

                foreach ($elements as $element) {
                    if (preg_match('/^:(.+)$/', $element, $r)) {
                        $element = $r[1];

                        $parsed[] = '(?:\/([^\/]+))?';
                        $names[] = $element;
                        $names_hash[$element] = 1;
                    } elseif (preg_match('/^\*$/', $element, $r)) {
                        $parsed[] = '(?:\/(.*))?';
                    } else {
                        $parsed[] = '/' . $element;
                    }
                }
                $regexp = '#^' . implode('', $parsed) . $regexp_suffix . '$#';

                $this->regex = $regexp;
                $this->names = $names;
            }
        }

        /**
         * @return mixed
         */
        public function getRegex()
        {
            return $this->regex;
        }

        /**
         * @param mixed $regex
         */
        public function setRegex($regex): void
        {
            $this->regex = $regex;
        }

        /**
         * @return bool
         */
        public function isCsrfProtected(): bool
        {
            return $this->is_csrf_protected;
        }

        /**
         * @param bool $is_csrf_protected
         */
        public function setIsCsrfProtected(bool $is_csrf_protected): void
        {
            $this->is_csrf_protected = $is_csrf_protected;
        }

        /**
         * @return bool
         */
        public function isAnonymous(): bool
        {
            return $this->is_anonymous;
        }

        /**
         * @param bool $is_anonymous
         */
        public function setIsAnonymous(bool $is_anonymous): void
        {
            $this->is_anonymous = $is_anonymous;
        }

        /**
         * @return mixed
         */
        public function getModuleName()
        {
            return $this->module_name;
        }

        /**
         * @param mixed $module_name
         */
        public function setModuleName($module_name): void
        {
            $this->module_name = $module_name;
        }

        /**
         * @return mixed
         */
        public function getAuthenticationMethod()
        {
            return $this->authentication_method;
        }

        /**
         * @param mixed $authentication_method
         */
        public function setAuthenticationMethod($authentication_method): void
        {
            $this->authentication_method = $authentication_method;
        }

        /**
         * @return mixed
         */
        public function getAllowedMethods()
        {
            return $this->allowed_methods;
        }

        /**
         * @param mixed $allowed_methods
         */
        public function setAllowedMethods($allowed_methods): void
        {
            $methods = (!is_array($allowed_methods)) ? array_filter(explode(',', $allowed_methods), function ($element) {
                return strtolower(trim($element));
            }) : $allowed_methods;

            $this->allowed_methods = $methods;
        }

        /**
         * @return mixed
         */
        public function getModuleAction()
        {
            return $this->module_action;
        }

        /**
         * @param mixed $module_action
         */
        public function setModuleAction($module_action): void
        {
            $this->module_action = $module_action;
        }

        /**
         * @return bool
         */
        public function isOverridden(): bool
        {
            return $this->is_overridden;
        }

        /**
         * @param bool $is_overridden
         */
        public function setIsOverridden(bool $is_overridden): void
        {
            $this->is_overridden = $is_overridden;
        }

        /**
         * @return array
         */
        public function getParameters(): array
        {
            return $this->parameters;
        }

        /**
         * @param array $parameters
         */
        public function setParameters(array $parameters): void
        {
            $this->parameters = $parameters;
        }

        public function setParameter(string $key, $value): void
        {
            if (!is_array($this->parameters)) {
                $this->parameters = [];
            }

            $this->parameters[$key] = $value;
        }

        /**
         * @return string[]
         */
        public function getParameterNames()
        {
            return $this->names;
        }

        protected function isMethodAllowed($method)
        {
            if (empty($this->allowed_methods)) {
                return true;
            }

            if (!is_array($this->allowed_methods)) {
                var_dump($this->allowed_methods);
                die();
            }
            if (in_array('*', $this->allowed_methods)) {
                return true;
            }

            return in_array($method, $this->allowed_methods);
        }

        /**
         * @return string[]|integer[]
         */
        public function getMatchedParameters()
        {
            return $this->matched_parameters;
        }

        public function match($url)
        {
            if (!$this->isMethodAllowed(Context::getRequest()->getMethod())) {
                return false;
            }

            if (!preg_match($this->regex, $url, $r)) {
                return false;
            }

            // remove the first element, which is the url
            array_shift($r);
            $out = [];

            foreach ($this->names as $name) {
                $out[$name] = null;
            }

            // parameters
            $parameters = $this->getParameters();
            $parameters['module'] = $this->module_name;
            $parameters['action'] = $this->module_action;

            foreach ($parameters as $name => $value) {
                if (preg_match('#[a-z_\-]#i', $name)) {
                    $out[$name] = $value;
                } else {
                    $out[$value] = true;
                }
            }

            $pos = 0;
            foreach ($r as $found) {
                if (isset($this->names[$pos])) {
                    $out[$this->names[$pos]] = $found;
                } else {
                    $pass = explode('/', $found);
                    $found = '';
                    for ($i = 0, $max = count($pass); $i < $max; $i += 2) {
                        if (!isset($pass[$i + 1])) continue;

                        $found .= $pass[$i] . '=' . $pass[$i + 1] . '&';
                    }

                    parse_str($found, $pass);

                    foreach ($pass as $key => $value) {
                        if (!isset($names_hash[$key])) {
                            $out[$key] = $value;
                        }
                    }
                }
                $pos++;
            }

            // we must have found all :var stuffs in url? except if default values exists
            foreach ($this->names as $name) {
                if ($out[$name] == null) {
                    return false;
                }
            }

            $this->matched_parameters = $out;
            return true;
        }

        public function toJSON()
        {
            return [
                'name' => $this->getName(),
                'module' => $this->getModuleName(),
                'action' => $this->getModuleAction()
            ];
        }

        public function getNamespacedAction()
        {
            return (stripos($this->getModuleAction(), '::') !== false) ? explode('::', $this->getModuleAction()) : ['Main', $this->getModuleAction()];
        }

        /**
         * @return Action
         */
        public function getController()
        {
            if (!Context::isInternalModule($this->getModuleName())) {
                if (is_dir(PACHNO_MODULES_PATH . $this->getModuleName())) {
                    if (!file_exists(PACHNO_MODULES_PATH . $this->getModuleName() . DS . 'controllers' . DS . 'Main.php')) {
                        throw new \pachno\core\framework\exceptions\ActionNotFoundException(
                            'The `' . $this->getModuleName() . '` module is missing a `/controllers/Main.php` controller, containing the module its initial actions.'
                        );
                    }
                } else {
                    throw new \Exception('Cannot load the ' . $this->getModuleName() . ' module');
                }
                $controllerClassNamespace = "\\pachno\\modules\\".$this->getModuleName().'\\controllers\\';
            } else {
                $controllerClassNamespace = "\\pachno\\core\\modules\\".$this->getModuleName().'\\controllers\\';
            }

            /**
             * Set up the action object by identifying the Controller from the action. The following actions can
             * be resolved by the Framework:
             *
             *  actionName          => /controllers/Main.php::runActionName()
             *  ::actionName        => /controllers/Main.php::runActionName()
             *  Other::actionName   => /controllers/Other.php::runActionName()
             *
             **/

            // If a separate controller is defined within the action name
            list ($controller, $action) = $this->getNamespacedAction();
            $controller = ($controller) ?? 'Main';

            $controllerClass = $controllerClassNamespace . $controller;
            $actionMethod = $action;

            if (class_exists($controllerClass) && is_callable($controllerClass, 'run'.ucfirst($actionMethod))) {
                $controllerObject = new $controllerClass();
            } else {
                throw new \Exception('The `' . $action . '` controller action is not callable');
            }

            $this->generated_method = 'run'.$actionMethod;
            return $controllerObject;
        }

        public function getModuleActionMethod()
        {
            return $this->generated_method;
        }

    }
