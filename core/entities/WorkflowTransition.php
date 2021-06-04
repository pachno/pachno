<?php

    namespace pachno\core\entities;

    use Exception;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use pachno\core\framework\Request;

    /**
     * Workflow transition class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\WorkflowTransitions")
     */
    class WorkflowTransition extends IdentifiableScoped
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The workflow description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = null;

        protected $_incoming_steps = null;

        protected $_actions = null;

        protected $_num_incoming_steps = null;

        /**
         * The outgoing step from this transition
         *
         * @var WorkflowStep
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowStep")
         */
        protected $_outgoing_step_id = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_template = null;

        /**
         * The originating request
         *
         * @var Request
         */
        protected $_request = null;

        protected $_pre_validation_rules = null;

        protected $_post_validation_rules = null;

        protected $_validation_errors = [];

        /**
         * The associated workflow object
         *
         * @var Workflow
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Workflow")
         */
        protected $_workflow_id = null;

        public static function loadMultiTeamWorkflowFixtures(Scope $scope, Workflow $workflow, $steps)
        {
            $rejected_resolutions = [
                Resolution::getByKeyish('notanissue')->getID(),
                Resolution::getByKeyish('wontfix')->getID(),
                Resolution::getByKeyish('cantfix')->getID(),
                Resolution::getByKeyish('cantreproduce')->getID(),
                Resolution::getByKeyish('duplicate')->getID()
            ];
            $resolved_resolutions = [
                Resolution::getByKeyish('resolved')->getID(),
                Resolution::getByKeyish('wontfix')->getID(),
                Resolution::getByKeyish('postponed')->getID(),
                Resolution::getByKeyish('duplicate')->getID()
            ];
            $closed_statuses = [
                Status::getByKeyish('closed')->getID(),
                Status::getByKeyish('postponed')->getID(),
                Status::getByKeyish('done')->getID(),
                Status::getByKeyish('fixed')->getID()
            ];
            $transitions = [];
            $transitions['investigateissue'] = [
                'name' => 'Investigate issue',
                'description' => 'Assign the issue to yourself and start investigating it',
                'outgoing_step' => 'investigating',
                'template' => null,
                'pre_validations' => [
                    WorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES => 5]
                ,
                'actions' => [
                    WorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0
                ]
            ];
            $transitions['requestmoreinformation'] = [
                'name' => 'Request more information',
                'description' => 'Move issue back to new state for more details',
                'outgoing_step' => 'new',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0
                ]
            ];
            $transitions['confirmissue'] = [
                'name' => 'Confirm issue',
                'description' => 'Confirm that the issue is valid',
                'outgoing_step' => 'confirmed',
                'template' => null,
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 10
                ]
            ];
            $transitions['rejectissue'] = [
                'name' => 'Reject issue',
                'description' => 'Reject the issue as invalid',
                'outgoing_step' => 'rejected',
                'template' => 'main/updateissueproperties',
                'post_validations' => [
                    WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID => join(',', $rejected_resolutions)]
                ,
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_RESOLUTION => 0,
                    WorkflowTransitionAction::ACTION_SET_DUPLICATE => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 100,
                    WorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0
                ]
            ];
            $transitions['acceptissue'] = [
                'name' => 'Accept issue',
                'description' => 'Accept the issue and assign it to yourself',
                'outgoing_step' => 'inprogress',
                'template' => null,
                'pre_validations' => [
                    WorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES => 5]
                ,
                'actions' => [
                    WorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0,
                    WorkflowTransitionAction::ACTION_USER_START_WORKING => 0
                ]
            ];
            $transitions['reopenissue'] = [
                'name' => 'Reopen issue',
                'description' => 'Reopen the issue',
                'outgoing_step' => 'new',
                'template' => null,
                'actions' => [
                    WorkflowTransitionAction::ACTION_CLEAR_RESOLUTION => 0,
                    WorkflowTransitionAction::ACTION_CLEAR_DUPLICATE => 0,
                    WorkflowTransitionAction::ACTION_CLEAR_PERCENT => 0
                ]
            ];
            $transitions['assignissue'] = [
                'name' => 'Assign issue',
                'description' => 'Accept the issue and assign it to someone',
                'outgoing_step' => 'inprogress',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_ASSIGN_ISSUE => 0,
                    WorkflowTransitionAction::ACTION_USER_START_WORKING => 0
                ]
            ];
            $transitions['markreadyfortesting'] = [
                'name' => 'Mark ready for testing',
                'description' => 'Mark the issue as ready to be tested',
                'outgoing_step' => 'readyfortesting',
                'template' => null,
                'actions' => [
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0,
                    WorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0
                ]
            ];
            $transitions['resolveissue'] = [
                'name' => 'Resolve issue',
                'description' => 'Resolve the issue',
                'outgoing_step' => 'closed',
                'template' => 'main/updateissueproperties',
                'post_validations' => [
                    WorkflowTransitionValidationRule::RULE_STATUS_VALID => join(',', $closed_statuses),
                    WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID => join(',', $resolved_resolutions)]
                ,
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_STATUS => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 100,
                    WorkflowTransitionAction::ACTION_SET_RESOLUTION => 0,
                    WorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0
                ]
            ];
            $transitions['testissuesolution'] = [
                'name' => 'Test issue solution',
                'description' => 'Check whether the solution is valid',
                'outgoing_step' => 'testing',
                'template' => null,
                'actions' => [
                    WorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0,
                    WorkflowTransitionAction::ACTION_USER_START_WORKING => 0
                ]
            ];
            $transitions['acceptissuesolution'] = [
                'name' => 'Accept issue solution',
                'description' => 'Mark the issue as resolved',
                'outgoing_step' => 'closed',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_RESOLUTION => 0,
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0,
                    WorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0
                ]
            ];
            $transitions['rejectissuesolution'] = [
                'name' => 'Reject issue solution',
                'description' => 'Reject the proposed solution and mark the issue as in progress',
                'outgoing_step' => 'inprogress',
                'template' => null,
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_RESOLUTION => 0,
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0,
                    WorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0
                ]
            ];

            $transitions = self::loadFixtures($scope, $workflow, $transitions, $steps);

            return $transitions;
        }

        protected static function loadFixtures(Scope $scope, Workflow $workflow, $transitions, $steps)
        {
            foreach ($transitions as $key => $transition) {
                if (!isset($steps[$transition['outgoing_step']])) {
                    throw new Exception('Outgoing step ' . $transition['outgoing_step'] . ' does not exist for workflow ' . $workflow->getName());
                }

                $transition_object = new WorkflowTransition();
                $transition_object->setName($transition['name']);
                $transition_object->setDescription($transition['description']);
                $transition_object->setOutgoingStep($steps[$transition['outgoing_step']]['step']);
                $transition_object->setTemplate($transition['template']);
                $transition_object->setWorkflow($workflow);
                $transition_object->save();
                $transitions[$key] = $transition_object;

                if (array_key_exists('pre_validations', $transition) && is_array($transition['pre_validations'])) {
                    foreach ($transition['pre_validations'] as $type => $validation) {
                        $rule = new WorkflowTransitionValidationRule();
                        $rule->setTransition($transition_object);
                        $rule->setPre();
                        $rule->setRule($type);
                        $rule->setRuleValue($validation);
                        $rule->setWorkflow($workflow);
                        $rule->save();
                    }
                }
                if (array_key_exists('post_validations', $transition) && is_array($transition['post_validations'])) {
                    foreach ($transition['post_validations'] as $type => $validation) {
                        $rule = new WorkflowTransitionValidationRule();
                        $rule->setTransition($transition_object);
                        $rule->setPost();
                        $rule->setRule($type);
                        $rule->setRuleValue($validation);
                        $rule->setWorkflow($workflow);
                        $rule->save();
                    }
                }
                if (array_key_exists('actions', $transition) && is_array($transition['actions'])) {
                    foreach ($transition['actions'] as $type => $action) {
                        $action_object = new WorkflowTransitionAction();
                        $action_object->setActionType($type);
                        $action_object->setTransition($transition_object);
                        $action_object->setWorkflow($workflow);
                        if (!is_null($action)) $action_object->setTargetValue($action);
                        $action_object->save();
                    }
                }
            }

            foreach ($steps as $step) {
                foreach ($step['transitions'] as $transition) {
                    $step['step']->addOutgoingTransition($transitions[$transition]);
                }
            }

            return $transitions;
        }

        /**
         * Set the outgoing step
         *
         * @param WorkflowStep $step A workflow step
         */
        public function setOutgoingStep(WorkflowStep $step)
        {
            $this->_outgoing_step_id = $step;
        }

        public function setWorkflow(Workflow $workflow)
        {
            $this->_workflow_id = $workflow;
        }

        public static function loadSimpleWorkflowFixtures(Scope $scope, Workflow $workflow, $steps)
        {
            $transitions = [];
            $transitions['startprogress'] = [
                'name' => 'Start progress',
                'description' => 'Assign the issue to yourself and start working on it',
                'outgoing_step' => 'inprogress',
                'template' => null,
                'actions' => [
                    WorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 33
                ]
            ];
            $transitions['resolveissue'] = [
                'name' => 'Resolve issue',
                'description' => 'Mark issue as resolved',
                'outgoing_step' => 'resolved',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 90
                ]
            ];
            $transitions['closeissue'] = [
                'name' => 'Close issue',
                'description' => 'Close the issue',
                'outgoing_step' => 'closed',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 100,
                    WorkflowTransitionAction::ACTION_SET_DUPLICATE => 0
                ]
            ];
            $transitions['reopenissue'] = [
                'name' => 'Reopen issue',
                'description' => 'Reopen the issue',
                'outgoing_step' => 'reopened',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_RESOLUTION => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 0
                ]
            ];

            $transitions = self::loadFixtures($scope, $workflow, $transitions, $steps);

            return $transitions;
        }

        public static function loadBalancedWorkflowFixtures(Scope $scope, Workflow $workflow, $steps)
        {
            $transitions = [];
            $transitions['startprogress'] = [
                'name' => 'Start progress',
                'description' => 'Assign the issue to yourself and start working on it',
                'outgoing_step' => 'inprogress',
                'template' => null,
                'actions' => [
                    WorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 33
                ]
            ];
            $transitions['readyfortesting'] = [
                'name' => 'Mark ready for testing',
                'description' => 'Mark issue as ready to be tested',
                'outgoing_step' => 'readyfortesting',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 80
                ]
            ];
            $transitions['confirmissue'] = [
                'name' => 'Confirm issue',
                'description' => 'Confirm that the issue is valid',
                'outgoing_step' => 'confirmed',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 10
                ]
            ];
            $transitions['resolveissue'] = [
                'name' => 'Resolve issue',
                'description' => 'Mark issue as resolved',
                'outgoing_step' => 'resolved',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 90
                ]
            ];
            $transitions['closeissue'] = [
                'name' => 'Close issue',
                'description' => 'Close the issue',
                'outgoing_step' => 'closed',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 100,
                    WorkflowTransitionAction::ACTION_SET_DUPLICATE => 0
                ]
            ];
            $transitions['reopenissue'] = [
                'name' => 'Reopen issue',
                'description' => 'Reopen the issue',
                'outgoing_step' => 'reopened',
                'template' => 'main/updateissueproperties',
                'actions' => [
                    WorkflowTransitionAction::ACTION_SET_RESOLUTION => 0,
                    WorkflowTransitionAction::ACTION_SET_PERCENT => 0
                ]
            ];

            $transitions = self::loadFixtures($scope, $workflow, $transitions, $steps);

            return $transitions;
        }

        public function getTemplateName()
        {
            $templates = self::getTemplates();

            return $templates[$this->getTemplate()];
        }

        public static function getTemplates()
        {
            $templates = ['' => 'No template used - transition happens instantly', 'main/updateissueproperties' => 'Set issue properties or add comment'];
            $event = Event::createNew('core', 'workflow_templates', null, [], $templates)->trigger();

            return $event->getReturnList();
        }

        public function getTemplate()
        {
            return $this->_template;
        }

        /**
         * Set the template to be used
         *
         * @param string $template
         */
        public function setTemplate($template)
        {
            if (array_key_exists($template, $this->getTemplates()) || !$template) {
                $this->_template = $template;
            }
        }

        public function hasTemplate()
        {
            return (bool)($this->getTemplate() != '');
        }

        public function getIncomingSteps()
        {
            $this->_populateIncomingSteps();

            return $this->_incoming_steps;
        }

        protected function _populateIncomingSteps()
        {
            if ($this->_incoming_steps === null) {
                $this->_incoming_steps = tables\WorkflowStepTransitions::getTable()->getByTransitionID($this->getID());
            }
        }

        public function getNumberOfIncomingSteps()
        {
            if ($this->_num_incoming_steps === null && $this->_incoming_steps !== null) {
                $this->_num_incoming_steps = count($this->_incoming_steps);
            } elseif ($this->_num_incoming_steps === null) {
                $this->_num_incoming_steps = tables\WorkflowStepTransitions::getTable()->countByTransitionID($this->getID());
            }

            return $this->_num_incoming_steps;
        }

        /**
         * Set the outgoing step id
         *
         * @param WorkflowStep $step_id A workflow step id
         */
        public function setOutgoingStepID($step_id)
        {
            $this->_outgoing_step_id = $step_id;
        }

        public function deleteTransition($direction, $step_id)
        {
            if ($direction == 'incoming') {
                $this->delete();
            } else {
                tables\WorkflowStepTransitions::getTable()->deleteByTransitionAndStepID($this->getID(), $step_id);

                if (tables\WorkflowStepTransitions::getTable()->countByTransitionID($this->getID()) == 0) {
                    $this->delete();
                }
            }
        }

        public function hasPreValidationRules()
        {
            return (bool)count($this->getPreValidationRules());
        }

        public function getPreValidationRules()
        {
            $this->_populateValidationRules();

            return $this->_pre_validation_rules;
        }

        protected function _populateValidationRules()
        {
            if ($this->_pre_validation_rules === null) {
                $rules = tables\WorkflowTransitionValidationRules::getTable()->getByTransitionID($this->getID());
                $this->_pre_validation_rules = $rules['pre'];
                $this->_post_validation_rules = $rules['post'];
            }
        }

        public function hasPreValidationRule($rule)
        {
            $rules = $this->getPreValidationRules();

            return (array_key_exists($rule, $rules));
        }

        public function getPreValidationRule($rule)
        {
            $rules = $this->getPreValidationRules();

            return (array_key_exists($rule, $rules)) ? $rules[$rule] : null;
        }

        public function hasPostValidationRules()
        {
            return (bool)count($this->getPostValidationRules());
        }

        /**
         *
         * @return WorkflowTransitionValidationRule[]
         */
        public function getPostValidationRules()
        {
            $this->_populateValidationRules();

            return $this->_post_validation_rules;
        }

        public function hasPostValidationRule($rule)
        {
            $rules = $this->getPostValidationRules();

            return (array_key_exists($rule, $rules));
        }

        public function getPostValidationRule($rule)
        {
            $rules = $this->getPostValidationRules();

            return (array_key_exists($rule, $rules)) ? $rules[$rule] : null;
        }

        public function isAvailableForIssue(Issue $issue)
        {
            foreach ($this->getPreValidationRules() as $validation_rule) {
                if ($validation_rule instanceof WorkflowTransitionValidationRule) {
                    if (!$validation_rule->isValid($issue)) return false;
                }
            }

            return true;
        }

        public function getProperties()
        {
            return ($this->getOutgoingStep()->isClosed()) ? ['resolution', 'status'] : [];
        }

        /**
         * Return the outgoing step
         *
         * @return WorkflowStep
         */
        public function getOutgoingStep()
        {
            return $this->_b2dbLazyLoad('_outgoing_step_id');
        }

        public function hasActions()
        {
            return (bool)count($this->getActions());
        }

        /**
         * @return WorkflowTransitionAction[]
         */
        public function getActions()
        {
            $this->_populateActions();

            return $this->_actions;
        }

        protected function _populateActions()
        {
            if ($this->_actions === null) {
                $this->_actions = WorkflowTransitionAction::getByTransitionID($this->getID());
            }
        }

        public function hasAction($action_type)
        {
            $actions = $this->getActions();

            return array_key_exists($action_type, $actions);
        }

        public function getAction($action_type)
        {
            $actions = $this->getActions();

            return (array_key_exists($action_type, $actions)) ? $actions[$action_type] : null;
        }

        /**
         * Transition an issue to the outgoing step, based on request data if available
         *
         * @param Issue $issue
         */
        public function transitionIssueToOutgoingStepWithoutRequest(Issue $issue)
        {
            // Pass new Request object so that functions like getParameter can be called.
            $request = new Request;

            if (!$this->validateFromRequest($request)) {
                return $this->getValidationErrors();
            }

            $this->getOutgoingStep()->applyToIssue($issue);
            if (!empty($this->_validation_errors)) return false;

            foreach ($this->getActions() as $action) {
                $action->perform($issue, $request);
            }

            foreach ($this->getPostValidationRules() as $rule) {
                if (!$rule->isValid($request)) {
                    $this->_validation_errors[$rule->getRule()] = true;
                }
            }

            if (count($this->getValidationErrors())) {
                return $this->getValidationErrors();
            }

            $issue->save();

            return true;
        }

        public function validateFromRequest(Request $request)
        {
            $this->_request = $request;
            foreach ($this->getPreValidationRules() as $rule) {
                if (!$rule->isValid($request)) {
                    $this->_validation_errors[$rule->getRule()] = true;
                }
            }
            foreach ($this->getActions() as $action) {
                if (!$action->isValid($request)) {
                    $this->_validation_errors[$action->getActionType()] = true;
                }
            }

            return empty($this->_validation_errors) ? true : $this->_validation_errors;
        }

        public function getValidationErrors()
        {
            return array_keys($this->_validation_errors);
        }

        /**
         * Transition an issue to the outgoing step, based on request data if available
         *
         * @param Issue $issue
         * @param Request $request
         */
        public function transitionIssueToOutgoingStepFromRequest(Issue $issue, $request = null)
        {
            $request = ($request !== null) ? $request : $this->_request;
            $this->getOutgoingStep()->applyToIssue($issue);
            if (!empty($this->_validation_errors)) {
                return $this->_validation_errors;
            }

            foreach ($this->getActions() as $action) {
                $action->perform($issue, $request);
            }

            foreach ($this->getPostValidationRules() as $rule) {
                if (!$rule->isValid($request)) {
                    $this->_validation_errors[$rule->getRule()] = true;
                }
            }

            if (!empty($this->_validation_errors)) {
                return $this->_validation_errors;
            }

            if ($request->hasParameter('comment_body') && trim($request['comment_body'] != '')) {
                $comment = new Comment();
                $comment->setContent($request->getParameter('comment_body', null, false));
                $comment->setPostedBy(framework\Context::getUser()->getID());
                $comment->setTargetID($issue->getID());
                $comment->setTargetType(Comment::TYPE_ISSUE);
                $comment->setModuleName('core');
                $comment->setIsPublic(true);
                $comment->setSystemComment(false);
                $comment->save();
                $issue->setSaveComment($comment);
            }

            $issue->save();

            return true;
        }

        public function copy(Workflow $new_workflow)
        {
            $new_transition = clone $this;
            $new_transition->setWorkflow($new_workflow);
            $new_transition->save();

            foreach ($this->getPreValidationRules() as $rule) {
                $new_rule = clone $rule;
                $new_rule->setTransition($new_transition);
                $new_rule->setWorkflow($new_workflow);
                $new_rule->save();
            }

            foreach ($this->getPostValidationRules() as $rule) {
                $new_rule = clone $rule;
                $new_rule->setTransition($new_transition);
                $new_rule->setWorkflow($new_workflow);
                $new_rule->save();
            }

            foreach ($this->getActions() as $action) {
                $new_action = clone $action;
                $new_action->setTransition($new_transition);
                $new_action->setWorkflow($new_workflow);
                $new_action->save();
            }

            return $new_transition;
        }

        public function isInitialTransition()
        {
            return ($this->getWorkflow()->getInitialTransition()->getID() == $this->getID());
        }

        /**
         * Return the workflow
         *
         * @return Workflow
         */
        public function getWorkflow()
        {
            return $this->_b2dbLazyLoad('_workflow_id');
        }

        public function toJSON($detailed = true)
        {
            $json = parent::toJSON($detailed);
            $json['name'] = $this->getName();
            $json['description'] = $this->getDescription();
            $json['template'] = $this->getTemplate();
            $json['url'] = framework\Context::getRouting()->generate('transition_issue', ['project_key' => '%project_key%', 'issue_id' => '%issue_id%', 'transition_id' => $this->getID()]);
            $json['backdrop_url'] = framework\Context::getRouting()->generate('get_partial_for_backdrop', ['key' => 'workflow_transition', 'transition_id' => $this->getID()]);
            $json['status_ids'] = [];
            if ($this->getOutgoingStep()->getLinkedStatus() instanceof Status) {
                $json['status_ids'][] = $this->getOutgoingStep()->getLinkedStatus()->getID();
            }

            $json['actions'] = [];

            foreach ($this->getActions() as $action) {
                $json['actions'][] = $action->toJSON();
            }

            $json['post_validations'] = [];
            foreach ($this->getPostValidationRules() as $rule) {
                if ($rule->getRule() == WorkflowTransitionValidationRule::RULE_STATUS_VALID) {
                    $values = explode(',', $rule->getRuleValue());
                    foreach ($values as $value) {
                        $json['status_ids'][] = $value;
                    }
                }
                $json['post_validations'][] = $rule->toJSON();
            }

            return $json;
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Returns the workflows description
         *
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * Set the workflows description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        protected function _preDelete(): void
        {
            tables\WorkflowStepTransitions::getTable()->deleteByTransitionID($this->getID());
        }

    }
