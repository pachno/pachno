<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * @Table(name="savedsearchfilters")
     * @Entity(class="\pachno\core\entities\SearchFilter")
     */
    class SavedSearchFilters extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'savedsearchfilters';

        public const ID = 'savedsearchfilters.id';

        public const SCOPE = 'savedsearchfilters.scope';

        public const VALUE = 'savedsearchfilters.value';

        public const OPERATOR = 'savedsearchfilters.operator';

        public const SEARCH_ID = 'savedsearchfilters.search_id';

        public const FILTER_KEY = 'savedsearchfilters.filter_key';

        public function getFiltersBySavedSearchID($savedsearch_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::SEARCH_ID, $savedsearch_id);

            $retarr = [];

            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    if (!array_key_exists($row->get(self::FILTER_KEY), $retarr)) $retarr[$row->get(self::FILTER_KEY)] = [];
                    $retarr[$row->get(self::FILTER_KEY)][] = ['operator' => $row->get(self::OPERATOR), 'value' => $row->get(self::VALUE)];
                }
            }

            return $retarr;
        }

        public function deleteBySearchID($saved_search_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::SEARCH_ID, $saved_search_id);
            $this->rawDelete($query);
        }

        public function saveFiltersForSavedSearch($saved_search_id, $filters)
        {
            foreach ($filters as $filter => $filter_info) {
                if (array_key_exists('value', $filter_info)) {
                    $this->_saveFilterForSavedSearch($saved_search_id, $filter, $filter_info['value'], $filter_info['operator']);
                } else {
                    foreach ($filter_info as $k => $single_filter) {
                        $this->_saveFilterForSavedSearch($saved_search_id, $filter, $single_filter['value'], $single_filter['operator']);
                    }
                }
            }

        }

        protected function _saveFilterForSavedSearch($saved_search_id, $filter_key, $value, $operator)
        {
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::SEARCH_ID, $saved_search_id);
            $insertion->add(self::FILTER_KEY, $filter_key);
            $insertion->add(self::VALUE, $value);
            $insertion->add(self::OPERATOR, $operator);
            $this->rawInsert($insertion);
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::VALUE, 200);
            parent::addVarchar(self::OPERATOR, 40);
            parent::addVarchar(self::FILTER_KEY, 100);
            parent::addForeignKeyColumn(self::SEARCH_ID, SavedSearches::getTable(), SavedSearches::ID);
        }

    }
