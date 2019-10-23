<?php

    namespace pachno\core\framework;

    use ArrayAccess;

    /**
     * Parameter holder class used in the MVC part of the framework for \pachno\core\entities\Action and \pachno\core\entities\ActionComponent
     *
     * @package pachno
     * @subpackage mvc
     */
    class Parameterholder implements ArrayAccess
    {

        protected $_property_list = [];

        public function getParameterHolder()
        {
            return $this->_property_list;
        }

        public function offsetUnset($key)
        {
            if (array_key_exists($key, $this->_property_list)) {
                unset($this->_property_list[$key]);
            }
        }

        public function offsetSet($key, $value)
        {
            $this->__set($key, $value);
        }

        public function offsetGet($key)
        {
            return $this->__get($key);
        }

        public function __get($property)
        {
            return ($this->hasParameter($property)) ? $this->_property_list[$property] : null;
        }

        public function __set($key, $value)
        {
            $this->_property_list[$key] = $value;
        }

        public function hasParameter($key)
        {
            return $this->__isset($key);
        }

        public function __isset($key)
        {
            return (array_key_exists($key, $this->_property_list)) ? true : false;
        }

        public function offsetExists($key)
        {
            return $this->__isset($key);
        }

    }
