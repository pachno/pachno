<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use b2db\Update;
    use pachno\core\entities\WorkflowTransition;
    use pachno\core\framework;

    /**
     * Workflow transitions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Workflow transitions table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method WorkflowTransition selectById($id, Query $query = null, $join = 'all')
     *
     * @Table(name="workflow_transitions")
     * @Entity(class="\pachno\core\entities\WorkflowTransition")
     */
    class WorkflowTransitions extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'workflow_transitions';

        public const ID = 'workflow_transitions.id';

        public const SCOPE = 'workflow_transitions.scope';

        public const WORKFLOW_ID = 'workflow_transitions.workflow_id';

        public const NAME = 'workflow_transitions.name';

        public const DESCRIPTION = 'workflow_transitions.description';

        public const OUTGOING_STEP_ID = 'workflow_transitions.outgoing_step_id';

        public const TEMPLATE = 'workflow_transitions.template';

        public function countByStepID($step_id)
        {
            return $this->_countByTypeID('step', $step_id);
        }

        protected function _countByTypeID($type, $id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);

            return $this->count($query);
        }

        public function getByStepID($step_id)
        {
            return $this->_getByTypeID('step', $step_id);
        }

        protected function _getByTypeID($type, $id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);

            $return_array = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $return_array[$row->get(self::ID)] = new WorkflowTransition($row->get(self::ID), $row);
                }
            }

            return $return_array;
        }

        public function countByWorkflowID($workflow_id)
        {
            return $this->_countByTypeID('workflow', $workflow_id);
        }

        public function getByWorkflowID($workflow_id)
        {
            return $this->_getByTypeID('workflow', $workflow_id);
        }

        public function reMapByWorkflowID($workflow_id, $mapper_array)
        {
            foreach ($mapper_array as $old_step_id => $new_step_id) {
                $query = $this->getQuery();
                $update = new Update();

                $update->add(self::OUTGOING_STEP_ID, $new_step_id);

                $query->where(self::OUTGOING_STEP_ID, $old_step_id);
                $query->where(self::WORKFLOW_ID, $workflow_id);

                $this->rawUpdate($update, $query);
            }
        }

        protected function _deleteByTypeID($type, $id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);

            return $this->rawDelete($query);
        }

    }