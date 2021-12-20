<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Join;
    use b2db\Update;
    use pachno\core\framework;

    /**
     * Issue affects component table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Issue affects component table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issueaffectscomponent")
     */
    class IssueAffectsComponent extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'issueaffectscomponent';

        public const ID = 'issueaffectscomponent.id';

        public const SCOPE = 'issueaffectscomponent.scope';

        public const ISSUE = 'issueaffectscomponent.issue';

        public const COMPONENT = 'issueaffectscomponent.component';

        public const CONFIRMED = 'issueaffectscomponent.confirmed';

        public const STATUS = 'issueaffectscomponent.status';

        protected $_preloaded_values = null;

        public function getByIssueID($issue_id)
        {
            if (is_array($this->_preloaded_values)) {
                if (array_key_exists($issue_id, $this->_preloaded_values)) {
                    $values = $this->_preloaded_values[$issue_id];
                    unset($this->_preloaded_values[$issue_id]);

                    return $values;
                } else {
                    return [];
                }
            } else {
                $res = $this->getByIssueIDs([$issue_id]);
                $rows = [];
                if ($res) {
                    while ($row = $res->getNextRow()) {
                        $rows[] = $row;
                    }
                }

                return $rows;
            }
        }

        public function getByIssueIDs($issue_ids)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE, $issue_ids, Criterion::IN);
            $query->join(Issues::getTable(), Issues::ID, self::ISSUE, [], Join::INNER);
            $query->join(Components::getTable(), Components::ID, self::COMPONENT, [], Join::INNER);
            $res = $this->rawSelect($query, false);

            return $res;
        }

        public function preloadValuesByIssueIDs($issue_ids)
        {
            $this->_preloaded_values = [];
            $component_ids = [];
            $res = $this->getByIssueIDs($issue_ids);
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $issue_id = $row->get(self::ISSUE);
                    $component_id = $row->get(self::COMPONENT);
                    if (!array_key_exists($issue_id, $this->_preloaded_values)) $this->_preloaded_values[$issue_id] = [];
                    $this->_preloaded_values[$issue_id][] = $row;
                    $component_ids[$component_id] = $component_id;
                }
            }

            return $component_ids;
        }

        public function clearPreloadedValues()
        {
            $this->_preloaded_custom_fields = null;
        }

        public function deleteByIssueIDandComponentID($issue_id, $component_id)
        {
            if (!($res = $this->getByIssueIDandComponentID($issue_id, $component_id))) {
                return false;
            } else {
                $query = $this->getQuery();
                $query->where(self::ISSUE, $issue_id);
                $query->where(self::COMPONENT, $component_id);
                $this->rawDelete($query);

                return true;
            }
        }

        public function getByIssueIDandComponentID($issue_id, $component_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMPONENT, $component_id);
            $query->where(self::ISSUE, $issue_id);
            $res = $this->rawSelectOne($query);

            return $res;
        }

        public function confirmByIssueIDandComponentID($issue_id, $component_id, $confirmed = true)
        {
            if (!($res = $this->getByIssueIDandComponentID($issue_id, $component_id))) {
                return false;
            } else {
                $update = new Update();
                $update->add(self::CONFIRMED, $confirmed);
                $this->rawUpdateById($update, $res->get(self::ID));

                return true;
            }
        }

        public function setStatusByIssueIDandComponentID($issue_id, $component_id, $status_id)
        {
            if (!($res = $this->getByIssueIDandComponentID($issue_id, $component_id))) {
                return false;
            } else {
                $update = new Update();
                $update->add(self::STATUS, $status_id);
                $this->rawUpdateById($update, $res->get(self::ID));

                return true;
            }
        }

        public function setIssueAffected($issue_id, $component_id)
        {
            if (!$this->getByIssueIDandComponentID($issue_id, $component_id)) {
                $insertion = new Insertion();
                $insertion->add(self::ISSUE, $issue_id);
                $insertion->add(self::COMPONENT, $component_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $ret = $this->rawInsert($insertion);

                return $ret->getInsertID();
            } else {
                return false;
            }
        }

        public function deleteByComponentID($component_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMPONENT, $component_id);
            $res = $this->rawDelete($query);

            return $res;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addBoolean(self::CONFIRMED);
            parent::addForeignKeyColumn(self::COMPONENT, Components::getTable(), Components::ID);
            parent::addForeignKeyColumn(self::ISSUE, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::STATUS, ListTypes::getTable(), ListTypes::ID);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('issue', self::ISSUE);
        }
    }
