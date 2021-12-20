<?php

    namespace pachno\core\framework;

    use Exception;

    /**
     * The Pachno event class
     *
     * @package pachno
     * @subpackage core
     */
    final class Event
    {

        /**
         * List of registered event listeners
         *
         * @var array
         */
        protected static $_registeredlisteners = [];

        protected $_module = null;

        protected $_identifier = null;

        protected $_return_value = null;

        protected $_subject = null;

        protected $_processed = false;

        protected $_return_list = null;

        protected $_parameters = [];

        /**
         * Create a new event
         *
         * @param string $module
         * @param string $identifier
         * @param mixed $subject
         * @param array $parameters
         * @param array $initial_list
         */
        public function __construct($module, $identifier, $subject = null, $parameters = [], $initial_list = [], $return_value = null)
        {
            $this->_module = $module;
            $this->_identifier = $identifier;
            $this->_subject = $subject;
            $this->_parameters = $parameters;
            $this->_return_list = $initial_list;
            $this->_return_value = $return_value;
        }

        /**
         * Register a listener for a spesified trigger
         *
         * @param string $module The module for which the trigger is active
         * @param string $identifier The trigger identifier
         * @param mixed $callback_function Which function to call
         */
        public static function listen($module, $identifier, $callback_function)
        {
            self::$_registeredlisteners[$module][$identifier][] = $callback_function;
        }

        /**
         * Remove all listeners from a module+identifier
         *
         * @param string $module The module for which the trigger is active
         * @param string $identifier The trigger identifier
         */
        public static function clearListeners($module, $identifier = null)
        {
            if ($identifier !== null) {
                self::$_registeredlisteners[$module][$identifier] = [];
            } elseif (isset(self::$_registeredlisteners[$module])) {
                unset(self::$_registeredlisteners[$module]);
            }
        }

        /**
         * Whether or not there are any listeners to a specific trigger
         *
         * @param string $module The module for which the trigger is active
         * @param string $identifier The trigger identifier
         *
         * @return boolean
         */
        public static function isAnyoneListening($module, $identifier)
        {
            if (isset(self::$_registeredlisteners[$module]) && isset(self::$_registeredlisteners[$module][$identifier]) && !empty(self::$_registeredlisteners[$module][$identifier])) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Create a new event and return it
         *
         * @param string $module
         * @param string $identifier
         * @param mixed $subject
         * @param array $parameters
         * @param array $initial_list
         * @param mixed $return_value
         *
         * @return Event
         */
        public static function createNew($module, $identifier, $subject = null, $parameters = [], $initial_list = [], $return_value = null)
        {
            $event = new Event($module, $identifier, $subject, $parameters, $initial_list, $return_value);

            return $event;
        }

        /**
         * Return the event subject
         *
         * @return mixed
         */
        public function getSubject()
        {
            return $this->_subject;
        }

        /**
         * Return the event parameters
         *
         * @return array
         */
        public function getParameters()
        {
            return $this->_parameters;
        }

        /**
         * Return a specific event parameter
         *
         * @param string $key
         *
         * @return mixed The parameter
         */
        public function getParameter($key)
        {
            return (array_key_exists($key, $this->_parameters)) ? $this->_parameters[$key] : null;
        }

        /**
         * Invoke a trigger
         *
         * @param array $parameters [optional] Parameters to pass to the registered listeners
         * @param mixed $return_value [optional] Initial return value to pass to this event trigger
         *
         * @return Event
         */
        public function trigger($parameters = null, $return_value = null)
        {
            if ($parameters !== null) {
                $this->_parameters = $parameters;
            }
            if ($return_value !== null) {
                $this->_return_value = $return_value;
            }
            self::_trigger($this, false);

            return $this;
        }

        /**
         * Invoke a trigger
         *
         * @param Event $event The event that is triggered
         * @param boolean $return_when_processed (optional) whether to return when processed or continue
         *
         * @return mixed
         */
        protected static function _trigger(Event $event, $return_when_processed = false)
        {
            $module = $event->getModule();
            $identifier = $event->getIdentifier();

            Logging::log("Triggering $module - $identifier");
            if (isset(self::$_registeredlisteners[$module][$identifier])) {
                foreach (self::$_registeredlisteners[$module][$identifier] as $trigger) {
                    try {
                        $cb_string = (is_array($trigger)) ? get_class($trigger[0]) . '::' . $trigger[1] : $trigger;
                        if (is_object($cb_string)) {
                            Logging::log('Running anonymous callback function');
                        } else {
                            Logging::log('Running callback function ' . $cb_string);
                        }
                        $result = call_user_func($trigger, $event);
                        if ($return_when_processed && $event->isProcessed()) {
                            return true;
                        }
                        if (is_object($cb_string)) {
                            Logging::log('done (Running anonymous callback function)');
                        } else {
                            Logging::log('done (Running callback function ' . $cb_string . ')');
                        }
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }
            Logging::log("done (Triggering $module - $identifier)");
        }

        /**
         * Return the event module
         *
         * @return string
         */
        public function getModule()
        {
            return $this->_module;
        }

        /**
         * Return the event identifier
         *
         * @return string
         */
        public function getIdentifier()
        {
            return $this->_identifier;
        }

        /**
         * Return whether or not the event has been processed
         *
         * @return boolean
         */
        public function isProcessed()
        {
            return $this->_processed;
        }

        /**
         * Mark the event as processed / unprocessed
         *
         * @param boolean $val
         */
        public function setProcessed($val = true)
        {
            $this->_processed = $val;
        }

        /**
         * Invoke a trigger and return as soon as it is processed
         *
         * @param array $parameters [optional] Parameters to pass to the registered listeners
         *
         * @return Event
         */
        public function triggerUntilProcessed($parameters = null)
        {
            if ($parameters !== null) {
                $this->_parameters = $parameters;
            }
            self::_trigger($this, true);

            return $this;
        }

        /**
         * Get the event return value
         *
         * @return mixed $val
         */
        public function getReturnValue()
        {
            return $this->_return_value;
        }

        /**
         * Set the event return value
         *
         * @param mixed $val
         */
        public function setReturnValue($val)
        {
            $this->_return_value = $val;
        }

        /**
         * Add an element to the return list
         *
         * @param mixed $val The value to add to the list
         * @param mixed $key [optional] Specify the key
         */
        public function addToReturnList($val, $key = null)
        {
            ($key !== null) ? $this->_return_list[$key] = $val : $this->_return_list[] = $val;
        }

        /**
         * Get the return list
         *
         * @return array
         */
        public function getReturnList()
        {
            return $this->_return_list;
        }

        /**
         * Return a specific event return value
         *
         * @param mixed $key
         *
         * @return mixed The value
         */
        public function getReturnListValue($key)
        {
            return (is_array($this->_return_list) && array_key_exists($key, $this->_return_list)) ? $this->_return_list[$key] : null;
        }

        /**
         * Check if a specific event return value is set
         *
         * @param mixed $key
         *
         * @return mixed Whether the value is set
         */
        public function hasReturnListValue($key)
        {
            return (bool)(is_array($this->_return_list) && array_key_exists($key, $this->_return_list));
        }

    }