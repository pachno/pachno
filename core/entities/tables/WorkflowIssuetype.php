<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
    use pachno\core\entities\Scope;
    use pachno\core\entities\Workflow;
    use pachno\core\entities\WorkflowScheme;
    use pachno\core\framework;

    /**
     * Link table between workflow and issue type
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Link table between workflow and issue type
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="workflow_issuetype")
     */
    class WorkflowIssuetype extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'workflow_issuetype';

        public const ID = 'workflow_issuetype.id';

        public const SCOPE = 'workflow_issuetype.scope';

        public const WORKFLOW_SCHEME_ID = 'workflow_issuetype.workflow_scheme_id';

        public const WORKFLOW_ID = 'workflow_issuetype.workflow_id';

        public const ISSUETYPE_ID = 'workflow_issuetype.issutype_id';

        public function loadFixtures(Scope $scope, Workflow $workflow, WorkflowScheme $workflowScheme)
        {
            foreach (IssueTypes::getTable()->getAllIDsByScopeID($scope->getID()) as $issuetype_id) {
                $insertion = new Insertion();
                $insertion->add(self::SCOPE, $scope->getID());
                $insertion->add(self::WORKFLOW_ID, $workflow->getID());
                $insertion->add(self::WORKFLOW_SCHEME_ID, $workflowScheme->getID());
                $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
                $this->rawInsert($insertion);
            }
        }

        public function setWorkflowIDforIssuetypeIDwithSchemeID($workflow_id, $issuetype_id, $scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::WORKFLOW_SCHEME_ID, $scheme_id);
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            if ($res = $this->rawSelect($query)) {
                if ($workflow_id) {
                    $update = new Update();
                    $update->add(self::WORKFLOW_ID, $workflow_id);
                    $this->rawUpdate($update, $query);
                } else {
                    $this->rawDelete($query);
                }
            } elseif ($workflow_id) {
                $insertion = new Insertion();
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $insertion->add(self::WORKFLOW_ID, $workflow_id);
                $insertion->add(self::WORKFLOW_SCHEME_ID, $scheme_id);
                $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
                $this->rawInsert($insertion);
            }
        }

        public function countSchemesByWorkflowID($workflow_id)
        {
            $query = $this->getQuery();
            $query->setIsDistinct();
            $query->addSelectionColumn(self::WORKFLOW_SCHEME_ID);
            $query->where(self::WORKFLOW_ID, $workflow_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function countByWorkflowSchemeID($workflow_scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::WORKFLOW_SCHEME_ID, $workflow_scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function deleteByWorkflowSchemeID($workflow_scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::WORKFLOW_SCHEME_ID, $workflow_scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->rawDelete($query);
        }

        public function getByWorkflowSchemeID($workflow_scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::WORKFLOW_SCHEME_ID, $workflow_scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $return_array = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $return_array[$row->get(self::ISSUETYPE_ID)] = new Workflow($row->get(self::WORKFLOW_ID), $row);
                }
            }

            return $return_array;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::WORKFLOW_ID, Workflows::getTable());
            parent::addForeignKeyColumn(self::WORKFLOW_SCHEME_ID, WorkflowSchemes::getTable());
            parent::addForeignKeyColumn(self::ISSUETYPE_ID, IssueTypes::getTable());
        }

    }
