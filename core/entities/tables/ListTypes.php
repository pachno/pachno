<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Update;
    use pachno\core\entities\Datatype;
    use pachno\core\entities\DatatypeBase;
    use pachno\core\framework;

    /**
     * List types table
     *
     * @method static ListTypes getTable()
     * @method DatatypeBase[] select(Query $query, $join = 'all')
     * @method DatatypeBase selectOne(Query $query, $join = 'all')
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="listtypes")
     * @Entity(class="\pachno\core\entities\DatatypeBase")
     * @Entities(identifier="itemtype")
     * @SubClasses(status="\pachno\core\entities\Status", category="\pachno\core\entities\Category", priority="\pachno\core\entities\Priority", role="\pachno\core\entities\Role", resolution="\pachno\core\entities\Resolution", reproducability="\pachno\core\entities\Reproducability", severity="\pachno\core\entities\Severity", activitytype="\pachno\core\entities\ActivityType", tag="\pachno\core\entities\Tag")
     */
    class ListTypes extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'listtypes';

        public const ID = 'listtypes.id';

        public const SCOPE = 'listtypes.scope';

        public const NAME = 'listtypes.name';

        public const ITEMTYPE = 'listtypes.itemtype';

        public const ITEMDATA = 'listtypes.itemdata';

        public const APPLIES_TO = 'listtypes.applies_to';

        public const APPLIES_TYPE = 'listtypes.applies_type';

        public const ORDER = 'listtypes.sort_order';

        protected static $_item_cache = null;

        public function clearListTypeCache($scope_id = null)
        {
            if ($scope_id === null) {
                $scope_id = framework\Context::getScope()->getID();
            }

            unset(self::$_item_cache[$scope_id]);
        }

        public function populateItemCache($scope_id = null)
        {
            $this->_populateItemCache($scope_id);
        }

        public function removeFromItemCache(DatatypeBase $item)
        {
            if (isset(self::$_item_cache[$item->getScope()->getID()][$item->getItemtype()][$item->getID()])) {
                unset(self::$_item_cache[$item->getScope()->getID()][$item->getItemtype()][$item->getID()]);
            }
        }

        public function updateItemCache(DatatypeBase $item)
        {
            if (isset(self::$_item_cache[$item->getScope()->getID()])) {
                if (!isset(self::$_item_cache[$item->getScope()->getID()][$item->getItemtype()])) {
                    self::$_item_cache[$item->getScope()->getID()][$item->getItemtype()] = [];
                }
                self::$_item_cache[$item->getScope()->getID()][$item->getItemtype()][$item->getID()] = $item;
            }
        }

        protected function _populateItemCache($scope_id = null)
        {
            if ($scope_id === null) {
                $scope_id = framework\Context::getScope()->getID();
            }

            if (!isset(self::$_item_cache[$scope_id])) {
                self::$_item_cache[$scope_id] = [];
                $query = $this->getQuery();
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->addOrderBy(self::ORDER, QueryColumnSort::SORT_ASC);
                $items = $this->select($query);
                foreach ($items as $item) {
                    $this->updateItemCache($item);
                }
            }
        }

        /**
         * @param $itemtype
         * @param int $scope_id
         *
         * @return DatatypeBase[]
         */
        public function getAllByItemType($itemtype, $scope_id = null)
        {
            if ($scope_id === null) {
                $scope_id = framework\Context::getScope()->getID();
            }

            $this->_populateItemCache($scope_id);

            return (array_key_exists($itemtype, self::$_item_cache[$scope_id])) ? self::$_item_cache[$scope_id][$itemtype] : [];
        }

        /**
         * @param $key
         * @param $itemtype
         * @param null $exclude_id
         * @param bool $ignore_empty
         *
         * @return DatatypeBase
         */
        public function getByKeyAndItemType($key, $itemtype, $exclude_id = null, $ignore_empty = false)
        {
            $query = $this->getQuery();
            $query->where('listtypes.key', $key);
            $query->where(self::ITEMTYPE, $itemtype);
            if ($exclude_id !== null) {
                $query->where(self::ID, $exclude_id, Criterion::NOT_EQUALS);
            }
            if ($ignore_empty) {
                $query->where(self::ITEMDATA, '', Criterion::NOT_EQUALS);
                $query->where(self::ITEMDATA, '#', Criterion::NOT_EQUALS);
            }

            return $this->selectOne($query);
        }

        public function getAllByItemTypeAndItemdata($itemtype, $itemdata, $scope_id = null)
        {
            if ($scope_id === null) {
                $scope_id = framework\Context::getScope()->getID();
            }
            $this->_populateItemCache($scope_id);
            $items = (array_key_exists($itemtype, self::$_item_cache[$scope_id])) ? self::$_item_cache[$scope_id][$itemtype] : [];
            foreach ($items as $id => $item) {
                if ($item->getItemdata() != $itemdata) unset($items[$id]);
            }

            return $items;
        }

        public function deleteByTypeAndId($type, $id)
        {
            $query = $this->getQuery();
            $query->where(self::ITEMTYPE, $type);
            $query->where(self::ID, $id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawDelete($query);
        }

        public function saveOptionOrder($options, $type)
        {
            foreach ($options as $key => $option_id) {
                $update = new Update();
                $update->add(self::ORDER, $key + 1);
                $this->rawUpdateById($update, $option_id);
            }
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('scope', [self::SCOPE]);
        }

    }
