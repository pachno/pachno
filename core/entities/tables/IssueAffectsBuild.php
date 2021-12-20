<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Join;
    use b2db\Update;
    use pachno\core\entities\Issue;
    use pachno\core\framework;

    /**
     * Issue affects build table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Issue affects build table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issueaffectsbuild")
     */
    class IssueAffectsBuild extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'issueaffectsbuild';

        public const ID = 'issueaffectsbuild.id';

        public const SCOPE = 'issueaffectsbuild.scope';

        public const ISSUE = 'issueaffectsbuild.issue';

        public const BUILD = 'issueaffectsbuild.build';

        public const CONFIRMED = 'issueaffectsbuild.confirmed';

        public const STATUS = 'issueaffectsbuild.status';

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
            $query->join(Builds::getTable(), Builds::ID, self::BUILD, [], Join::INNER);
            $res = $this->rawSelect($query, false);

            return $res;
        }

        public function preloadValuesByIssueIDs($issue_ids)
        {
            $this->_preloaded_values = [];
            $build_ids = [];
            $res = $this->getByIssueIDs($issue_ids);
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $issue_id = $row->get(self::ISSUE);
                    $build_id = $row->get(self::BUILD);
                    if (!array_key_exists($issue_id, $this->_preloaded_values)) $this->_preloaded_values[$issue_id] = [];
                    $this->_preloaded_values[$issue_id][] = $row;
                    $build_ids[$build_id] = $build_id;
                }
            }

            return $build_ids;
        }

        public function clearPreloadedValues()
        {
            $this->_preloaded_custom_fields = null;
        }

        public function deleteByBuildID($build_id)
        {
            $query = $this->getQuery();
            $query->where(self::BUILD, $build_id);
            $this->rawDelete($query);
        }

        public function deleteByIssueIDandBuildID($issue_id, $build_id)
        {
            if (!$this->getByIssueIDandBuildID($issue_id, $build_id)) {
                return false;
            } else {
                $query = $this->getQuery();
                $query->where(self::ISSUE, $issue_id);
                $query->where(self::BUILD, $build_id);
                $this->rawDelete($query);

                return true;
            }
        }

        public function getByIssueIDandBuildID($issue_id, $build_id)
        {
            $query = $this->getQuery();
            $query->where(self::BUILD, $build_id);
            $query->where(self::ISSUE, $issue_id);
            $res = $this->rawSelectOne($query);

            return $res;
        }

        public function confirmByIssueIDandBuildID($issue_id, $build_id, $confirmed = true)
        {
            if (!($res = $this->getByIssueIDandBuildID($issue_id, $build_id))) {
                return false;
            } else {
                $update = new Update();
                $update->add(self::CONFIRMED, $confirmed);
                $this->rawUpdateById($update, $res->get(self::ID));

                return true;
            }
        }

        public function setStatusByIssueIDandBuildID($issue_id, $build_id, $status_id)
        {
            if (!($res = $this->getByIssueIDandBuildID($issue_id, $build_id))) {
                return false;
            } else {
                $update = new Update();
                $update->add(self::STATUS, $status_id);
                $this->rawUpdateById($update, $res->get(self::ID));

                return true;
            }
        }

        public function setIssueAffected($issue_id, $build_id)
        {
            if (!$this->getByIssueIDandBuildID($issue_id, $build_id)) {
                $insertion = new Insertion();
                $insertion->add(self::ISSUE, $issue_id);
                $insertion->add(self::BUILD, $build_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $ret = $this->rawInsert($insertion);

                return $ret->getInsertID();
            } else {
                return false;
            }
        }

        public function getCountsForBuild($build_id)
        {
            $query = $this->getQuery();
            $query->where(self::BUILD, $build_id);

            $query2 = clone $query;
            $query2->join(Issues::getTable(), Issues::ID, self::ISSUE);
            $query2->where(Issues::STATE, Issue::STATE_CLOSED);

            return [$this->count($query), $this->count($query2)];
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addBoolean(self::CONFIRMED);
            parent::addForeignKeyColumn(self::BUILD, Builds::getTable(), Builds::ID);
            parent::addForeignKeyColumn(self::ISSUE, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::STATUS, ListTypes::getTable(), ListTypes::ID);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('issue', self::ISSUE);
        }

    }
