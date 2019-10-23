<?php

    namespace pachno\core\entities\tables;

    use b2db\Table;
    use b2db\Update;
    use pachno\core\entities\CustomDatatype;
    use pachno\core\framework;

    /**
     * Custom field options table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Custom field options table
     *
     * @package pachno
     * @subpackage tables
     *
     * @static CustomFieldOptions getTable() Returns an instance of this table
     *
     * @Table(name="customfieldoptions")
     * @Entity(class="\pachno\core\entities\CustomDatatypeOption")
     */
    class CustomFieldOptions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'customfieldoptions';

        const ID = 'customfieldoptions.id';

        const NAME = 'customfieldoptions.name';

        const ITEMDATA = 'customfieldoptions.itemdata';

        const OPTION_VALUE = 'customfieldoptions.value';

        const SORT_ORDER = 'customfieldoptions.sort_order';

        const CUSTOMFIELD_ID = 'customfieldoptions.customfield_id';

        const SCOPE = 'customfieldoptions.scope';

        public function getByValueAndCustomfieldID($value, $customfield_id)
        {
            $query = $this->getQuery();
            $query->where(self::OPTION_VALUE, $value);
            $query->where(self::CUSTOMFIELD_ID, $customfield_id);

            $row = $this->selectOne($query);

            return $row;
        }

        public function deleteCustomFieldOptions($customfield_id)
        {
            $query = $this->getQuery();
            $query->where(self::CUSTOMFIELD_ID, $customfield_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        public function saveOptionOrder($options, $customfield_id)
        {
            foreach ($options as $key => $option_id) {
                $query = $this->getQuery();
                $update = new Update();
                $update->add(self::SORT_ORDER, $key + 1);
                $query->where(self::ID, $option_id);
                $query->where(self::CUSTOMFIELD_ID, $customfield_id);
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawUpdate($update, $query);
            }
        }

        protected function migrateData(Table $old_table)
        {
            switch ($old_table->getVersion()) {
                case 1:
                    if ($res = $old_table->rawSelectAll()) {
                        $customdatatypes_table = CustomDatatype::getB2DBTable();
                        $query = $customdatatypes_table->getQuery();
                        $query->indexBy(CustomFields::FIELD_KEY);
                        $customfields = $customdatatypes_table->select($query);
                        while ($row = $res->getNextRow()) {
                            $key = $row->get('customfieldoptions.customfield_key');
                            $customfield = (array_key_exists($key, $customfields)) ? $customfields[$key] : null;
                            if ($customfield instanceof CustomDatatype) {
                                $update = new Update();
                                $update->add(self::CUSTOMFIELD_ID, $customfield->getID());
                                $this->rawUpdateById($update, $row->get(self::ID));
                            } else {
                                $this->rawDeleteById($row->get(self::ID));
                            }
                        }
                    }
                    break;
            }
        }

    }
