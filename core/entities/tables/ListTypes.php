<?php

    namespace pachno\core\entities\tables;

    use b2db\QueryColumnSort;
    use b2db\Update;
    use pachno\core\entities\Datatype;
    use pachno\core\framework;

    /**
     * List types table
     *
     * @method static ListTypes getTable()
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="listtypes")
     * @Entity(class="\pachno\core\entities\DatatypeBase")
     * @Entities(identifier="itemtype")
     * @SubClasses(status="\pachno\core\entities\Status", category="\pachno\core\entities\Category", priority="\pachno\core\entities\Priority", role="\pachno\core\entities\Role", resolution="\pachno\core\entities\Resolution", reproducability="\pachno\core\entities\Reproducability", severity="\pachno\core\entities\Severity", activitytype="\pachno\core\entities\ActivityType")
     */
    class ListTypes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'listtypes';

        const ID = 'listtypes.id';

        const SCOPE = 'listtypes.scope';

        const NAME = 'listtypes.name';

        const ITEMTYPE = 'listtypes.itemtype';

        const ITEMDATA = 'listtypes.itemdata';

        const APPLIES_TO = 'listtypes.applies_to';

        const APPLIES_TYPE = 'listtypes.applies_type';

        const ORDER = 'listtypes.sort_order';

        protected static $_item_cache = null;

        public function clearListTypeCache()
        {
            self::$_item_cache = null;
        }

        public function populateItemCache()
        {
            $this->_populateItemCache();
        }

        protected function _populateItemCache()
        {
            if (self::$_item_cache === null) {
                self::$_item_cache = [];
                $query = $this->getQuery();
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->addOrderBy(self::ORDER, QueryColumnSort::SORT_ASC);
                $items = $this->select($query);
                foreach ($items as $item) {
                    self::$_item_cache[$item->getItemtype()][$item->getID()] = $item;
                }
            }
        }

        public function getAllByItemType($itemtype)
        {
            $this->_populateItemCache();

            return (array_key_exists($itemtype, self::$_item_cache)) ? self::$_item_cache[$itemtype] : [];
        }

        public function getAllByItemTypeAndItemdata($itemtype, $itemdata)
        {
            $this->_populateItemCache();
            $items = (array_key_exists($itemtype, self::$_item_cache)) ? self::$_item_cache[$itemtype] : [];
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

        public function getStatusListForUpgrade()
        {
            $query = $this->getQuery();
            $query->where(self::ITEMTYPE, Datatype::STATUS);
            $query->join(Scopes::getTable(), Scopes::ID, self::SCOPE);
            $res = $this->rawSelect($query);

            $statuses = [];
            while ($row = $res->getNextRow()) {
                if (!array_key_exists($row[self::SCOPE], $statuses)) $statuses[$row[self::SCOPE]] = ['scopename' => $row[Scopes::NAME], 'statuses' => []];
                $statuses[$row[self::SCOPE]]['statuses'][$row[self::ID]] = $row[self::NAME];
            }

            return $statuses;
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope', [self::SCOPE]);
        }

    }
