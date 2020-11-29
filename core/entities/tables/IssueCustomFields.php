<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Table;
    use b2db\Update;
    use pachno\core\entities\CustomDatatype;
    use pachno\core\entities\CustomDatatypeOption;
    use pachno\core\framework;

    /**
     * Issue <-> custom fields relations table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Issue <-> custom fields relations table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issuecustomfields")
     */
    class IssueCustomFields extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'issuecustomfields';

        const ID = 'issuecustomfields.id';

        const SCOPE = 'issuecustomfields.scope';

        const ISSUE_ID = 'issuecustomfields.issue_id';

        const CUSTOMFIELDOPTION_ID = 'issuecustomfields.customfieldoption_id';

        const CUSTOMFIELDS_ID = 'issuecustomfields.customfields_id';

        const OPTION_VALUE = 'issuecustomfields.option_value';

        protected $_preloaded_custom_fields = null;

        public function getAllValuesByIssueID($issue_id)
        {
            if (is_array($this->_preloaded_custom_fields)) {
                if (array_key_exists($issue_id, $this->_preloaded_custom_fields)) {
                    $values = $this->_preloaded_custom_fields[$issue_id];
                    unset($this->_preloaded_custom_fields[$issue_id]);

                    return $values;
                } else {
                    return [];
                }
            } else {
                $res = $this->getAllValuesByIssueIDs([$issue_id]);
                $rows = [];
                if ($res) {
                    while ($row = $res->getNextRow()) {
                        $rows[] = $row;
                    }
                }

                return $rows;
            }
        }

        public function getAllValuesByIssueIDs($issue_ids)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_ids, Criterion::IN);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawSelect($query, false);

            return $res;
        }

        public function preloadValuesByIssueIDs($issue_ids)
        {
            $this->_preloaded_custom_fields = [];
            $res = $this->getAllValuesByIssueIDs($issue_ids);
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $issue_id = $row->get(self::ISSUE_ID);
                    if (!array_key_exists($issue_id, $this->_preloaded_custom_fields)) $this->_preloaded_custom_fields[$issue_id] = [];
                    $this->_preloaded_custom_fields[$issue_id][] = $row;
                }
            }
        }

        public function clearPreloadedValues()
        {
            $this->_preloaded_custom_fields = null;
        }

        public function saveIssueCustomFieldValue($value, $customdatatype_id, $issue_id)
        {
            if ($row = $this->getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id)) {
                if ($value === null) {
                    $this->rawDeleteById($row->get(self::ID));
                } else {
                    $update = new Update();
                    $update->add(self::OPTION_VALUE, $value);
                    $this->rawUpdateById($update, $row->get(self::ID));
                }
            } elseif ($value !== null) {
                $insertion = new Insertion();
                $insertion->add(self::ISSUE_ID, $issue_id);
                $insertion->add(self::OPTION_VALUE, $value);
                $insertion->add(self::CUSTOMFIELDS_ID, $customdatatype_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);
            }
        }

        public function getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_id);
            $query->where(self::CUSTOMFIELDS_ID, $customdatatype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $row = $this->rawSelectOne($query);

            return $row;
        }

        public function saveIssueCustomFieldOption($option_id, $customdatatype_id, $issue_id)
        {
            if ($row = $this->getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id)) {
                if ($option_id === null) {
                    $this->rawDeleteById($row->get(self::ID));
                } else {
                    $update = new Update();
                    $update->add(self::CUSTOMFIELDOPTION_ID, $option_id);
                    $this->rawUpdateById($update, $row->get(self::ID));
                }
            } elseif ($option_id !== null) {
                $insertion = new Insertion();
                $insertion->add(self::ISSUE_ID, $issue_id);
                $insertion->add(self::CUSTOMFIELDOPTION_ID, $option_id);
                $insertion->add(self::CUSTOMFIELDS_ID, $customdatatype_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);
            }
        }

        public function doDeleteByFieldId($id)
        {
            $query = $this->getQuery();
            $query->where(self::CUSTOMFIELDS_ID, $id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawSelect($query);

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $this->rawDeleteById($row->get(self::ID));
                }
            }
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::CUSTOMFIELDS_ID, CustomFields::getTable(), CustomFields::ID);
            parent::addForeignKeyColumn(self::CUSTOMFIELDOPTION_ID, CustomFieldOptions::getTable(), CustomFieldOptions::ID);
            parent::addText(self::OPTION_VALUE, false);
        }

        protected function setupIndexes()
        {
            $this->addIndex('issueid_scope', [self::ISSUE_ID, self::SCOPE]);
        }

    }
