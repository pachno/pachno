<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Workflow class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Workflow class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Workflows")
     */
    class Workflow extends IdentifiableScoped
    {

        protected static $_workflows = null;

        protected static $_num_workflows = null;

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

        /**
         * Whether the workflow is active or not
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_is_active = true;

        /**
         * This workflow's steps
         *
         * @var array|WorkflowStep
         * @Relates(class="\pachno\core\entities\WorkflowStep", collection=true, foreign_column="workflow_id")
         */
        protected $_steps = null;

        protected $_num_steps = null;

        /**
         * The initial transition for incoming issues in this workflow
         *
         * @var WorkflowTransition
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowTransition")
         */
        protected $_initial_transition_id = null;

        /**
         * This workflow's transitions
         *
         * @var array|WorkflowTransition
         * @Relates(class="\pachno\core\entities\WorkflowTransition", collection=true, foreign_column="workflow_id")
         */
        protected $_transitions = null;

        /**
         * This workflow's schemes
         *
         * @var array|WorkflowTransition
         * @Relates(class="\pachno\core\entities\WorkflowScheme", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\WorkflowIssuetype")
         */
        protected $_schemes = null;

        protected $_num_schemes = null;

        protected $_is_copied = false;

        public static function loadFixtures(Scope $scope)
        {
            $multi_team_workflow = new Workflow();
            $multi_team_workflow->setName("Multi-team workflow");
            $multi_team_workflow->setDescription("This is a workflow well suited for projects with multiple teams. It uses an issue lifecycle involving triaging, testing and QA, and works well for large projects.");
            $multi_team_workflow->setScope($scope->getID());
            $multi_team_workflow->save();

            $balanced_workflow = new Workflow();
            $balanced_workflow->setName("Balanced workflow");
            $balanced_workflow->setDescription("This is a workflow used to handle medium-sized projects or small-team projects.");
            $balanced_workflow->setScope($scope->getID());
            $balanced_workflow->save();

            $simple_workflow = new Workflow();
            $simple_workflow->setName("Simple workflow");
            $simple_workflow->setDescription("This is a simple workflow that can be used on projects with few people, or even just one person.");
            $simple_workflow->setScope($scope->getID());
            $simple_workflow->save();

            WorkflowStep::loadMultiTeamWorkflowFixtures($scope, $multi_team_workflow);
            WorkflowStep::loadBalancedWorkflowFixtures($scope, $balanced_workflow);
            WorkflowStep::loadSimpleWorkflowFixtures($scope, $simple_workflow);

            return [$multi_team_workflow, $balanced_workflow, $simple_workflow];
        }

        public static function getCustomWorkflowsCount()
        {
            return self::getWorkflowsCount() - 1;
        }

        public static function getWorkflowsCount()
        {
            if (self::$_num_workflows === null) {
                if (self::$_workflows !== null)
                    self::$_num_workflows = count(self::$_workflows);
                else
                    self::$_num_workflows = tables\Workflows::getTable()->countWorkflows();
            }

            return self::$_num_workflows;
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

        /**
         * Whether this is the builtin workflow that cannot be edited or removed
         *
         * @return boolean
         */
        public function isActive()
        {
            return (bool)$this->_is_active;
        }

        /**
         * Get the first step in this workflow
         *
         * @return WorkflowStep
         */
        public function getFirstStep()
        {
            return $this->getInitialTransition()->getOutgoingStep();
        }

        /**
         * Return the workflow's initial transition
         *
         * @return WorkflowTransition
         */
        public function getInitialTransition()
        {
            return $this->_b2dbLazyLoad('_initial_transition_id');
        }

        public function getNumberOfSteps()
        {
            if ($this->_num_steps === null && $this->_steps !== null) {
                $this->_num_steps = count($this->_steps);
            } elseif ($this->_num_steps === null) {
                $this->_num_steps = $this->_b2dbLazyCount('_steps');
            }

            return (int)$this->_num_steps;
        }

        public function isInUse()
        {
            return (bool)$this->getNumberOfSchemes();
        }

        public function getNumberOfSchemes()
        {
            if ($this->_num_schemes === null && $this->_schemes !== null) {
                $this->_num_schemes = count($this->_schemes);
            } elseif ($this->_num_schemes === null) {
                $this->_num_schemes = $this->_b2dbLazyCount('_schemes');
            }

            return $this->_num_schemes;
        }

        /**
         * @return bool
         */
        public function isIsCopied(): bool
        {
            return $this->_is_copied;
        }

        /**
         * @param bool $is_copied
         */
        public function setIsCopied(bool $is_copied): void
        {
            $this->_is_copied = $is_copied;
        }

        public function copy($new_name)
        {
            $new_workflow = new Workflow();
            $new_workflow->setName($new_name);
            $new_workflow->setIsCopied(true);
            $new_workflow->save();
            $step_mapper = [];
            $transition_mapper = [];
            foreach ($this->getSteps() as $key => $step) {
                $this->_steps[$key] = $step->copy($new_workflow);
                $step_mapper[$key] = $this->_steps[$key]->getID();
            }
            foreach ($this->getTransitions() as $key => $transition) {
                $old_id = $transition->getID();
                $this->_transitions[$key] = $transition->copy($new_workflow);
                $transition_mapper[$old_id] = $this->_transitions[$key]->getID();
            }
            tables\WorkflowStepTransitions::getTable()->copyByWorkflowIDs($this->getID(), $new_workflow->getID());
            tables\WorkflowStepTransitions::getTable()->reMapStepIDsByWorkflowID($new_workflow->getID(), $step_mapper);
            tables\WorkflowTransitions::getTable()->reMapByWorkflowID($new_workflow->getID(), $step_mapper);
            tables\WorkflowStepTransitions::getTable()->reMapTransitionIDsByWorkflowID($new_workflow->getID(), $transition_mapper);

            $new_initial_transition = $this->getInitialTransition()->copy($new_workflow);
            $new_initial_transition->setOutgoingStepID($step_mapper[$this->getInitialTransition()->getOutgoingStep()->getID()]);
            $new_initial_transition->save();
            $new_workflow->setInitialTransition($new_initial_transition);
            $new_workflow->save();

            return $new_workflow;
        }

        /**
         * Get all steps in this workflow
         *
         * @return WorkflowStep[] An array of \pachno\core\entities\WorkflowStep objects
         */
        public function getSteps()
        {
            $this->_populateSteps();

            return $this->_steps;
        }

        protected function _populateSteps()
        {
            if ($this->_steps === null) {
                $this->_b2dbLazyLoad('_steps');
            }
        }

        /**
         * Get all transitions in this workflow
         *
         * @return WorkflowTransition[]
         */
        public function getTransitions()
        {
            $this->_populateTransitions();

            return $this->_transitions;
        }

        protected function _populateTransitions()
        {
            if ($this->_transitions === null) {
                $this->_b2dbLazyLoad('_transitions');
                if (array_key_exists($this->getInitialTransition()->getID(), $this->_transitions)) unset($this->_transitions[$this->getInitialTransition()->getID()]);
            }
        }

        public function moveIssueToMatchingWorkflowStep(Issue $issue)
        {
            if (!$issue->isPropertyChanged('status') && !$issue->isPropertyChanged('resolution')) {
                return false;
            }

            foreach ($this->getSteps() as $step) {
                if ($step->hasLinkedStatus() && $issue->getStatus() instanceof Status && $step->getLinkedStatusID() == $issue->getStatus()->getID()) {
                    $step->applyToIssue($issue);

                    return true;
                }
            }
            foreach ($this->getSteps() as $step) {
                if (!$step->hasLinkedStatus()) {
                    foreach ($step->getIncomingTransitions() as $transition) {
                        if ($transition->hasPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID)) {
                            $rule = $transition->getPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID);
                            if ($rule->isValid($issue)) {
                                $step->applyToIssue($issue);

                                return true;
                            }
                        }
                    }
                }
            }

            return false;
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

        protected function _postSave($is_new = false)
        {
            if ($is_new && !$this->_is_copied) {
                $step = new WorkflowStep();
                $step->setName('New');
                $step->setWorkflow($this);
                $step->save();
                $transition = new WorkflowTransition();
                $transition->setOutgoingStep($step);
                $transition->setName('Issue created');
                $transition->setWorkflow($this);
                $transition->setDescription('This is the initial transition for issues using this workflow');
                $transition->save();

                $this->setInitialTransition($transition);
                $this->save();

                $this->_is_copied = false;
            }
        }

        public function setInitialTransition(WorkflowTransition $transition)
        {
            $this->_initial_transition_id = $transition;
        }

    }
