<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Insertion;
    use pachno\core\entities\Issue;
    use pachno\core\framework;

    /**
     * Issue relations table
     *
     * @method static IssueRelations getTable() Retrieves an instance of this table
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issuerelations")
     */
    class IssueRelations extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'issuerelations';

        public const ID = 'issuerelations.id';

        public const SCOPE = 'issuerelations.scope';

        public const PARENT_ID = 'issuerelations.parent_id';

        public const CHILD_ID = 'issuerelations.child_id';

        public const MUSTFIX = 'issuerelations.mustfix';

        protected $_relations_cache = [];

        public function clearRelationCache()
        {
            $this->_relations_cache = [];
        }

        public function getRelatedIssues($issue_id)
        {
            if (!array_key_exists($issue_id, $this->_relations_cache)) {
                $this->preloadIssueRelations([$issue_id]);
            }

            return $this->_relations_cache[$issue_id];
        }

        public function preloadIssueRelations($issue_ids)
        {
            foreach ($issue_ids as $key => $issue_id) {
                if (!array_key_exists($issue_id, $this->_relations_cache)) {
                    $this->_relations_cache[$issue_id] = [
                        'children' => [],
                        'parents' => []
                    ];
                } else {
                    unset($issue_ids[$key]);
                }
            }

            if (count($issue_ids)) {
                $query = $this->getQuery();
                $query->where(Issues::DELETED, 0);

                $criteria = new Criteria();
                $criteria->where(self::PARENT_ID, $issue_ids, Criterion::IN);
                $criteria->or(self::CHILD_ID, $issue_ids, Criterion::IN);
                $query->and($criteria);

                $res = $this->rawSelect($query);

                $issues_table = Issues::getTable();
                if ($res) {
                    while ($row = $res->getNextRow()) {
                        $child_id = $row->get(self::CHILD_ID);
                        $parent_id = $row->get(self::PARENT_ID);
                        if (in_array($parent_id, $issue_ids)) {
                            $child_issue = $issues_table->selectById($child_id);
                            if ($child_issue instanceof Issue && $child_issue->hasAccess()) {
                                $this->_relations_cache[$parent_id]['children'][$child_id] = $child_issue;
                            }
                        }
                        if (in_array($child_id, $issue_ids)) {
                            $parent_issue = $issues_table->selectById($parent_id);
                            if ($parent_issue instanceof Issue && $parent_issue->hasAccess()) {
                                $this->_relations_cache[$child_id]['parents'][$parent_id] = $parent_issue;
                            }
                        }
                    }
                }
            }
        }

        public function getIssueRelation($this_issue_id, $related_issue_id)
        {
            $query = $this->getQuery();
            $query->where(Issues::DELETED, 0);

            $criteria = new Criteria();
            $criteria->where(self::PARENT_ID, $this_issue_id);
            $criteria->or(self::CHILD_ID, $this_issue_id);
            $query->and($criteria);

            $criteria = new Criteria();
            $criteria->where(self::PARENT_ID, $related_issue_id);
            $criteria->or(self::CHILD_ID, $related_issue_id);
            $query->and($criteria);

            $res = $this->rawSelectOne($query);

            return $res;
        }

        public function addParentIssue($issue_id, $parent_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::CHILD_ID, $issue_id);
            $insertion->add(self::PARENT_ID, $parent_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawInsert($insertion);

            unset($this->_relations_cache[$issue_id], $this->_relations_cache[$parent_id]);

            return $res;
        }

        public function removeParentIssue($issue_id, $parent_id)
        {
            $query = $this->getQuery();
            $query->where(self::CHILD_ID, $issue_id);
            $query->where(self::PARENT_ID, $parent_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);

            unset($this->_relations_cache[$issue_id], $this->_relations_cache[$parent_id]);

            return $res;
        }

        public function addChildIssue($issue_id, $child_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::PARENT_ID, $issue_id);
            $insertion->add(self::CHILD_ID, $child_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawInsert($insertion);

            unset($this->_relations_cache[$issue_id], $this->_relations_cache[$child_id]);

            return $res;
        }

        public function removeChildIssue($issue_id, $child_id)
        {
            $query = $this->getQuery();
            $query->where(self::PARENT_ID, $issue_id);
            $query->where(self::CHILD_ID, $child_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);

            unset($this->_relations_cache[$issue_id], $this->_relations_cache[$parent_id]);

            return $res;
        }

        public function countChildIssues($issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::PARENT_ID, $issue_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function removeIssueRelations($issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $criteria = new Criteria();
            $criteria->where(self::PARENT_ID, $issue_id);
            $criteria->or(self::CHILD_ID, $issue_id);

            $query->and($criteria);

            $res = $this->rawDelete($query);

            return $res;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addBoolean(self::MUSTFIX);
            parent::addForeignKeyColumn(self::PARENT_ID, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::CHILD_ID, Issues::getTable(), Issues::ID);
        }

    }
