<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use b2db\Saveable;
    use b2db\Update;
    use pachno\core\entities\WorkflowTransitionAction;
    use pachno\core\framework;

    /**
     * Workflow transition actions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Workflow transition actions table
     *
     * @method static WorkflowTransitionActions getTable()
     * @method WorkflowTransitionAction selectById($id, Query $query = null, $join = 'all')
     * @method WorkflowTransitionAction selectOne(Query $query, $join = 'all')
     * @method WorkflowTransitionAction[] select(Query $query, $join = 'all')
     *
     * @Table(name="workflow_transition_actions")
     * @Entity(class="\pachno\core\entities\WorkflowTransitionAction")
     */
    class WorkflowTransitionActions extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'workflow_transition_actions';

        public const ID = 'workflow_transition_actions.id';

        public const SCOPE = 'workflow_transition_actions.scope';

        public const ACTION_TYPE = 'workflow_transition_actions.action_type';

        public const TRANSITION_ID = 'workflow_transition_actions.transition_id';

        public const WORKFLOW_ID = 'workflow_transition_actions.workflow_id';

        public const TARGET_VALUE = 'workflow_transition_actions.target_value';

        public function getByTransitionID($transition_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TRANSITION_ID, $transition_id);

            return $this->select($query);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('scope_transitionid', [self::SCOPE, self::TRANSITION_ID]);
        }

        public function updateTransitionAction($action_type, $current_status_id, $new_status_id)
        {
            $query = $this->getQuery();
            $query->where(self::ACTION_TYPE, $action_type);
            $query->where(self::TARGET_VALUE, $current_status_id);

            $update = new Update();
            $update->add(self::TARGET_VALUE, $new_status_id);

            $this->rawUpdate($update, $query);
        }
    }