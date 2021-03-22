<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\Keyable;
    use pachno\core\framework\Settings;

    /**
     * Generic datatype class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Generic datatype class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    abstract class DatatypeBase extends Keyable
    {
        public const DATETIME_PICKER = 22;
        public const INPUT_TEXTAREA_MAIN = 3;
        public const MILESTONE_CHOICE = 20;
        public const USER_CHOICE = 14;
        public const STATUS_CHOICE = 13;
        public const INPUT_TEXTAREA_SMALL = 4;
        public const CALCULATED_FIELD = 18;
        public const INPUT_TEXT = 2;
        public const CLIENT_CHOICE = 21;
        public const TEAM_CHOICE = 15;
        public const BUILTIN = 0;
        public const DROPDOWN_CHOICE_TEXT = 1;
        public const COMPONENTS_CHOICE = 10;
        public const EDITIONS_CHOICE = 12;
        public const DATE_PICKER = 19;
        public const RADIO_CHOICE = 5;
        public const RELEASES_CHOICE = 8;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Item type
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_itemtype = null;

        /**
         * Extra data for that data type (if any)
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_itemdata = null;

        /**
         * Sort order of this item
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order = null;

        public static function getAvailableFields($builtin_only = false)
        {
            $types = [
                'shortname' => self::BUILTIN,
                'description' => self::BUILTIN,
                'reproduction_steps' => self::BUILTIN,
                'status' => self::BUILTIN,
                'category' => self::BUILTIN,
                'resolution' => self::BUILTIN,
                'priority' => self::BUILTIN,
                'reproducability' => self::BUILTIN,
                'severity' => self::BUILTIN,
                'percent_complete' => self::BUILTIN,
                'owned_by' => self::BUILTIN,
                'assignee' => self::BUILTIN,
                'edition' => self::BUILTIN,
                'build' => self::BUILTIN,
                'component' => self::BUILTIN,
                'estimated_time' => self::BUILTIN,
                'spent_time' => self::BUILTIN,
                'milestone' => self::BUILTIN,
                'user_pain' => self::BUILTIN,
                'votes' => self::BUILTIN
            ];

            if ($builtin_only) return $types;

            foreach (CustomDatatype::getAll() as $customDatatype) {
                $types[$customDatatype->getKey()] = $customDatatype->getType();
            }

            return $types;
        }

        /**
         * Invoked when trying to print the object
         *
         * @return string
         */
        public function __toString()
        {
            return $this->_name;
        }

        public function getItemtype()
        {
            return $this->_itemtype;
        }

        public function setItemtype($itemtype)
        {
            $this->_itemtype = $itemtype;
        }

        public function canUserSet(User $user)
        {
            $retval = $user->hasPermission($this->getPermissionsKey(), $this->getID(), 'core');
            $retval = ($retval === null) ? $user->hasPermission($this->getPermissionsKey(), 0, 'core') : $retval;

            return ($retval !== null) ? $retval : false;
        }

        public function getPermissionsKey()
        {
            return 'set_datatype_' . $this->_itemtype;
        }

        public function setOrder($order)
        {
            $this->_sort_order = $order;
        }

        public function toJSON($detailed = true)
        {
            return [
                'id' => $this->getID(),
                'name' => $this->getName(),
                'key' => $this->getKey(),
                'itemdata' => $this->getItemdata(),
                'itemtype' => $this->_itemtype,
                'builtin' => $this->isBuiltin(),
                'sort_order' => $this->getOrder()
            ];
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
            $this->_generateKey();
        }

        /**
         * Returns the itemdata associated with the datatype (if any)
         *
         * @return string
         */
        public function getItemdata()
        {
            return $this->_itemdata;
        }

        /**
         * Set the itemdata
         *
         * @param string $itemdata
         */
        public function setItemdata($itemdata)
        {
            $this->_itemdata = $itemdata;
        }

        abstract function isBuiltin();

        public function getOrder()
        {
            return (int)$this->_sort_order;
        }

        abstract function getFontAwesomeIcon();

        abstract function getFontAwesomeIconStyle();

    }
