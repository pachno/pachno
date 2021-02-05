<?php

    namespace pachno\core\entities;

    use pachno\core\framework;
    use pachno\core\framework\Event;

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
    abstract class Datatype extends DatatypeBase
    {
        /**
         * Item type status
         *
         */
        const STATUS = 'status';

        /**
         * Item type priority
         *
         */
        const PRIORITY = 'priority';

        /**
         * Item type reproducability
         *
         */
        const REPRODUCABILITY = 'reproducability';

        /**
         * Item type resolution
         *
         */
        const RESOLUTION = 'resolution';

        /**
         * Item type severity
         *
         */
        const SEVERITY = 'severity';

        /**
         * Item type issue type
         *
         */
        const ISSUETYPE = 'issuetype';

        /**
         * Item type category
         *
         */
        const CATEGORY = 'category';

        /**
         * Item type project role
         *
         */
        const ROLE = 'role';

        /**
         * Item type tag
         *
         */
        const TAG = 'tag';

        /**
         * Item type activity type
         *
         */
        const ACTIVITYTYPE = 'activitytype';

        public static function loadFixtures(Scope $scope)
        {
            Category::loadFixtures($scope);
            Priority::loadFixtures($scope);
            Reproducability::loadFixtures($scope);
            Resolution::loadFixtures($scope);
            Severity::loadFixtures($scope);
            Status::loadFixtures($scope);
            Role::loadFixtures($scope);
            ActivityType::loadFixtures($scope);
            foreach (self::getTypes() as $type => $class) {
                framework\Context::setPermission('set_datatype_' . $type, 0, 'core', 0, 0, 0, true, $scope->getID());
            }
        }

        public static function getTypes()
        {
            $types = [];
            $types[self::STATUS] = '\pachno\core\entities\Status';
            $types[self::PRIORITY] = '\pachno\core\entities\Priority';
            $types[self::CATEGORY] = '\pachno\core\entities\Category';
            $types[self::SEVERITY] = '\pachno\core\entities\Severity';
            $types[self::REPRODUCABILITY] = '\pachno\core\entities\Reproducability';
            $types[self::RESOLUTION] = '\pachno\core\entities\Resolution';
            $types[self::ACTIVITYTYPE] = '\pachno\core\entities\ActivityType';

            $types = Event::createNew('core', 'Datatype::getTypes', null, [], $types)->trigger()->getReturnList();

            return $types;
        }

        public static function has($item_id)
        {
            $items = static::getAll();

            return array_key_exists($item_id, $items);
        }

        /**
         * Returns all severities available
         *
         * @return static[]
         */
        public static function getAll()
        {
            return tables\ListTypes::getTable()->getAllByItemType(static::ITEMTYPE);
        }

        protected function _postSave($is_new)
        {
            parent::_postSave($is_new);
            tables\ListTypes::getTable()->updateItemCache($this);
        }

        public function isBuiltin()
        {
            return true;
        }

        public function canBeDeleted()
        {
            return true;
        }

        public function getFontAwesomeIcon()
        {
            switch ($this->_itemtype) {
                case self::PRIORITY:
                    switch ($this->_itemdata) {
                        case Priority::CRITICAL:
                            return 'exclamation';
                        case Priority::HIGH:
                            return 'angle-up';
                        case Priority::NORMAL:
                            return 'minus';
                        case Priority::LOW:
                            return 'angle-down';
                        case Priority::TRIVIAL:
                            return 'angle-double-down';
                    }
                case self::RESOLUTION:
                    return 'clipboard-check';
                case self::REPRODUCABILITY:
                    return 'list-ol';
                case self::SEVERITY:
                    return 'chart-line';
            }
        }

        public function getFontAwesomeIconStyle()
        {
            switch ($this->_itemtype) {
                case self::PRIORITY:
                    return 'fas';
                default:
                    return 'fas';
            }
        }

        public function toJSON($detailed = true)
        {
            $json = parent::toJSON($detailed);
            $json['icon'] = [
                'name' => $this->getFontAwesomeIcon(),
                'style' => $this->getFontAwesomeIconStyle()
            ];

            return $json;
        }

    }
