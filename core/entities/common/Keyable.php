<?php

    namespace pachno\core\entities\common;

    /**
     * Generic keyable class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Generic keyable class
     *
     * @package pachno
     * @subpackage main
     */
    abstract class Keyable extends IdentifiableScoped
    {

        /**
         * The key for this item
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_key = null;

        public static function getOrCreateByKeyish($scope, $key, $name)
        {
            $item = static::getByKeyish($key);

            if (!$item instanceof self) {
                $item = new static();
                $item->setKey($key);
                $item->setName($name);
                $item->setScope($scope);
                $item->save();
            }

            return $item;
        }

        protected function _preSave(bool $is_new): void
        {
            parent::_preSave($is_new);
            $this->_generateKey();
        }

        public static function getByKeyish($key)
        {
            foreach (static::getAll() as $item) {
                if ($item->getKey() == str_replace([' ', '/', "'"], ['', '', ''], mb_strtolower($key))) {
                    return $item;
                }
            }

            return null;
        }

        public function toJSON($detailed = true)
        {
            return [
                'id' => $this->getID(),
                'key' => $this->getKey(),
            ];
        }

        public function getKey()
        {
            $this->_generateKey();

            return $this->_key;
        }

        public function setKey($key)
        {
            $this->_key = $key;
        }

        protected function _generateKey()
        {
            if ($this->_key === null)
                $this->_key = preg_replace("/[^\pL0-9]/iu", '', mb_strtolower($this->getName()));
        }

    }
