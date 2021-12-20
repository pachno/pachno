<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\Update;
    use pachno\core\entities\WorkflowTransitionAction;
    use pachno\core\entities\WorkflowTransitionValidationRule;
    use pachno\core\framework;

    /**
     * Workflow transition validation rules table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Workflow transition validation rules table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static WorkflowTransitionValidationRules getTable() Return an instance of this table
     * @method WorkflowTransitionValidationRule selectById($id, Query $query = null, $join = 'all')
     * @method WorkflowTransitionValidationRule[] select(Query $query, $join = 'all')
     *
     * @Table(name="workflow_transition_validation_rules")
     * @Entity(class="\pachno\core\entities\WorkflowTransitionValidationRule")
     */
    class WorkflowTransitionValidationRules extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'workflow_transition_validation_rules';

        public const ID = 'workflow_transition_validation_rules.id';

        public const SCOPE = 'workflow_transition_validation_rules.scope';

        public const RULE = 'workflow_transition_validation_rules.rule';

        public const TRANSITION_ID = 'workflow_transition_validation_rules.transition_id';

        public const WORKFLOW_ID = 'workflow_transition_validation_rules.workflow_id';

        public const RULE_VALUE = 'workflow_transition_validation_rules.rule_value';

        public const PRE_OR_POST = 'workflow_transition_validation_rules.pre_or_post';

        public function getByTransitionID($transition_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TRANSITION_ID, $transition_id);

            $actions = ['pre' => [], 'post' => []];
            if ($res = $this->select($query, false)) {
                foreach ($res as $rule) {
                    $actions[$rule->isPreOrPost()][$rule->getRule()] = $rule;
                }
            }

            return $actions;
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('scope_transitionid', [self::SCOPE, self::TRANSITION_ID]);
        }

        public function updateValidationRule($rule_type, $current_item_id, $new_item_id)
        {
            $query = $this->getQuery();
            $query->where(self::RULE, $rule_type);
            $query->where(self::RULE_VALUE, $current_item_id);

            $update = new Update();
            $update->add(self::RULE_VALUE, $new_item_id);

            $this->rawUpdate($update, $query);

            $query = $this->getQuery();
            $query->where(self::RULE, $rule_type);
            $query->where(self::RULE_VALUE, $current_item_id, Criterion::LIKE);
            foreach ($this->select($query) as $rule) {
                $valid_items = explode(',', $rule->getRuleValue());
                foreach ($valid_items as $key => $value) {
                    if ($value == $current_item_id) {
                        $valid_items[$key] = $new_item_id;
                        $updateQuery = $this->getQuery();
                        $updateQuery->where(self::ID, $rule->getID());

                        $update = new Update();
                        $update->update(self::RULE_VALUE, implode(',', $valid_items));

                        $this->rawUpdate($update, $updateQuery);
                        break;
                    }
                }
            }
        }
    }