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
        
        public const FIELD_SHORTNAME = 'shortname';
        public const FIELD_DESCRIPTION = 'description';
        public const FIELD_REPRODUCTION_STEPS = 'reproduction_steps';
        public const FIELD_STATUS = 'status';
        public const FIELD_CATEGORY = 'category';
        public const FIELD_RESOLUTION = 'resolution';
        public const FIELD_PRIORITY = 'priority';
        public const FIELD_REPRODUCABILITY = 'reproducability';
        public const FIELD_SEVERITY = 'severity';
        public const FIELD_PERCENT_COMPLETE = 'percent_complete';
        public const FIELD_OWNED_BY = 'owned_by';
        public const FIELD_ASSIGNEE = 'assignee';
        public const FIELD_EDITION = 'edition';
        public const FIELD_BUILD = 'build';
        public const FIELD_COMPONENT = 'component';
        public const FIELD_ESTIMATED_TIME = 'estimated_time';
        public const FIELD_SPENT_TIME = 'spent_time';
        public const FIELD_MILESTONE = 'milestone';
        public const FIELD_USER_PAIN = 'user_pain';
        public const FIELD_VOTES = 'votes';

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
                self::FIELD_SHORTNAME => self::BUILTIN,
                self::FIELD_DESCRIPTION => self::BUILTIN,
                self::FIELD_REPRODUCTION_STEPS => self::BUILTIN,
                self::FIELD_STATUS => self::BUILTIN,
                self::FIELD_CATEGORY => self::BUILTIN,
                self::FIELD_RESOLUTION => self::BUILTIN,
                self::FIELD_PRIORITY => self::BUILTIN,
                self::FIELD_REPRODUCABILITY => self::BUILTIN,
                self::FIELD_SEVERITY => self::BUILTIN,
                self::FIELD_PERCENT_COMPLETE => self::BUILTIN,
                self::FIELD_OWNED_BY => self::BUILTIN,
                self::FIELD_ASSIGNEE => self::BUILTIN,
                self::FIELD_EDITION => self::BUILTIN,
                self::FIELD_BUILD => self::BUILTIN,
                self::FIELD_COMPONENT => self::BUILTIN,
                self::FIELD_ESTIMATED_TIME => self::BUILTIN,
                self::FIELD_SPENT_TIME => self::BUILTIN,
                self::FIELD_MILESTONE => self::BUILTIN,
                self::FIELD_USER_PAIN => self::BUILTIN,
                self::FIELD_VOTES => self::BUILTIN
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
        public function __toString(): string
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
