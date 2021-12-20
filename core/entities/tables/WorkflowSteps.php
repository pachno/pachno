<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use b2db\Join;
    use b2db\Update;
    use pachno\core\entities\Scope;
    use pachno\core\entities\Status;
    use pachno\core\entities\WorkflowTransitionAction;
    use pachno\core\framework;

    /**
     * Workflow steps table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Workflow steps table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="workflow_steps")
     * @Entity(class="\pachno\core\entities\WorkflowStep")
     */
    class WorkflowSteps extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'workflow_steps';

        public const ID = 'workflow_steps.id';

        public const SCOPE = 'workflow_steps.scope';

        public const NAME = 'workflow_steps.name';

        public const STATUS_ID = 'workflow_steps.status_id';

        public const WORKFLOW_ID = 'workflow_steps.workflow_id';

        public const CLOSED = 'workflow_steps.closed';

        public const DESCRIPTION = 'workflow_steps.description';

        public const EDITABLE = 'workflow_steps.editable';

        public function loadFixtures(Scope $scope)
        {
            $steps = [];
            $steps[] = ['name' => 'New', 'description' => 'A new issue, not yet handled', 'status_id' => Status::getByKeyish('new')->getID(), 'editable' => true, 'is_closed' => false];
            $steps[] = ['name' => 'Investigating', 'description' => 'An issue that is being investigated, looked into or is by other means between new and unconfirmed state', 'status_id' => Status::getByKeyish('investigating')->getID(), 'editable' => true, 'is_closed' => false];
            $steps[] = ['name' => 'Confirmed', 'description' => 'An issue that has been confirmed', 'status_id' => Status::getByKeyish('confirmed')->getID(), 'editable' => false, 'is_closed' => false];
            $steps[] = ['name' => 'In progress', 'description' => 'An issue that is being adressed', 'status_id' => Status::getByKeyish('beingworkedon')->getID(), 'editable' => false, 'is_closed' => false];
            $steps[] = ['name' => 'Ready for testing', 'description' => 'An issue that has been marked fixed and is ready for testing', 'status_id' => Status::getByKeyish('readyfortesting/qa')->getID(), 'editable' => false, 'is_closed' => false];
            $steps[] = ['name' => 'Testing', 'description' => 'An issue where the proposed or implemented solution is currently being tested or approved', 'status_id' => Status::getByKeyish('testing/qa')->getID(), 'editable' => false, 'is_closed' => false];
            $steps[] = ['name' => 'Rejected', 'description' => 'A closed issue that has been rejected', 'status_id' => Status::getByKeyish('notabug')->getID(), 'editable' => false, 'is_closed' => true];
            $steps[] = ['name' => 'Closed', 'description' => 'A closed issue', 'status_id' => null, 'editable' => false, 'is_closed' => true];

            foreach ($steps as $step) {
                $insertion = new Insertion();
                $insertion->add(self::WORKFLOW_ID, 1);
                $insertion->add(self::SCOPE, $scope->getID());
                $insertion->add(self::NAME, $step['name']);
                $insertion->add(self::DESCRIPTION, $step['description']);
                $insertion->add(self::STATUS_ID, $step['status_id']);
                $insertion->add(self::CLOSED, $step['is_closed']);
                $insertion->add(self::EDITABLE, $step['editable']);
                $this->rawInsert($insertion);
            }
        }

        public function countByWorkflowID($workflow_id)
        {
            $query = $this->getQuery();
            $query->where(self::WORKFLOW_ID, $workflow_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query, false);

            return $row;
        }

        public function countByStatusID($status_id)
        {
            $query = $this->getQuery();
            $query->where(self::STATUS_ID, $status_id);

            return $this->count($query);
        }

        public function getAllByWorkflowSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->join(Workflows::getTable(), Workflows::ID, self::WORKFLOW_ID, [], Join::INNER);
            $query->join(WorkflowIssuetype::getTable(), WorkflowIssuetype::WORKFLOW_ID, self::WORKFLOW_ID, [], Join::INNER);
            $query->where(WorkflowIssuetype::WORKFLOW_SCHEME_ID, $scheme_id);
            $res = $this->rawSelect($query);

            $steps = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $step_id = $row->get(self::ID);
                    $steps[$step_id] = $step_id;
                }
            }

            return $steps;
        }

        public function updateStepStatus($current_status_id, $new_status_id)
        {
            $query = $this->getQuery();
            $query->where(self::STATUS_ID, $current_status_id);

            $update = new Update();
            $update->update(self::STATUS_ID, $new_status_id);

            $this->rawUpdate($update, $query);
        }

    }