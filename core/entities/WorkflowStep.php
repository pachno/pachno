<?php

    namespace pachno\core\entities;

    use Exception;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\ListTypes;
    use pachno\core\entities\tables\WorkflowStepTransitions;

    /**
     * Workflow step class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Workflow step class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\WorkflowSteps")
     */
    class WorkflowStep extends IdentifiableScoped
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

        /**
         * @Column(type="boolean")
         */
        protected $_editable = null;

        /**
         * @Column(type="boolean")
         */
        protected $_closed = null;

        /**
         * @var ?Status|int
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Status")
         */
        protected $_status_id = null;

        /**
         * @var int
         * @Column(type="integer", length=10)
         */
        protected $_sort_order = 0;

        protected $_incoming_transitions = null;

        protected $_num_incoming_transitions = null;

        protected $_outgoing_transitions = null;

        protected $_num_outgoing_transitions = null;

        /**
         * The associated workflow object
         *
         * @var Workflow
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Workflow")
         */
        protected $_workflow_id = null;

        public static function loadMultiTeamWorkflowFixtures(Scope $scope, Workflow $workflow)
        {
            $steps = [];
            $steps['new'] = [
                'name' => 'New',
                'description' => 'A new issue, not yet handled',
                'status_id' => Status::getByKeyish('new')->getID(),
                'transitions' => ['investigateissue', 'confirmissue', 'rejectissue', 'acceptissue', 'resolveissue'],
                'editable' => true,
                'is_closed' => false
            ];
            $steps['investigating'] = [
                'name' => 'Investigating',
                'description' => 'An issue that is being investigated, looked into or is by other means between new and unconfirmed state',
                'status_id' => Status::getByKeyish('investigating')->getID(),
                'transitions' => ['requestmoreinformation', 'confirmissue', 'rejectissue', 'acceptissue'],
                'editable' => true,
                'is_closed' => false
            ];
            $steps['confirmed'] = [
                'name' => 'Confirmed',
                'description' => 'An issue that has been confirmed',
                'status_id' => Status::getByKeyish('confirmed')->getID(),
                'transitions' => ['acceptissue', 'assignissue', 'resolveissue'],
                'editable' => false,
                'is_closed' => false
            ];
            $steps['inprogress'] = [
                'name' => 'In progress',
                'description' => 'An issue that is being adressed',
                'status_id' => Status::getByKeyish('beingworkedon')->getID(),
                'transitions' => ['rejectissue', 'markreadyfortesting', 'resolveissue'],
                'editable' => false,
                'is_closed' => false
            ];
            $steps['readyfortesting'] = [
                'name' => 'Ready for testing',
                'description' => 'An issue that has been marked fixed and is ready for testing',
                'status_id' => Status::getByKeyish('readyfortesting/qa')->getID(),
                'transitions' => ['resolveissue', 'testissuesolution'],
                'editable' => false,
                'is_closed' => false
            ];
            $steps['testing'] = [
                'name' => 'Testing',
                'description' => 'An issue where the proposed or implemented solution is currently being tested or approved',
                'status_id' => Status::getByKeyish('testing/qa')->getID(),
                'transitions' => ['acceptissuesolution', 'rejectissuesolution'],
                'editable' => false,
                'is_closed' => false
            ];
            $steps['rejected'] = [
                'name' => 'Rejected',
                'description' => 'A closed issue that has been rejected',
                'status_id' => Status::getByKeyish('notabug')->getID(),
                'transitions' => ['reopenissue'],
                'editable' => false,
                'is_closed' => true
            ];
            $steps['closed'] = [
                'name' => 'Closed',
                'description' => 'A closed issue',
                'status_id' => null,
                'transitions' => ['reopenissue'],
                'editable' => false,
                'is_closed' => true
            ];

            $steps = self::loadWorkflowStepsAndTransitions($scope, $workflow, $steps);
            WorkflowTransition::loadMultiTeamWorkflowFixtures($scope, $workflow, $steps);
        }

        public static function loadWorkflowStepsAndTransitions(Scope $scope, Workflow $workflow, $steps)
        {
            foreach ($steps as $key => $step) {
                $step_object = new WorkflowStep();
                $step_object->setWorkflow($workflow);
                $step_object->setName($step['name']);
                $step_object->setDescription($step['description']);
                $step_object->setLinkedStatusID($step['status_id']);
                $step_object->setIsClosed($step['is_closed']);
                $step_object->setIsEditable($step['editable']);
                $step_object->setScope($scope);
                $step_object->save();
                $steps[$key]['step'] = $step_object;
            }

            $transition = new WorkflowTransition();
            $step = $steps['new']['step'];
            $transition->setOutgoingStep($step);
            $transition->setName('Issue created');
            $transition->setWorkflow($workflow);
            $transition->setDescription('This is the initial transition for issues using this workflow');
            $transition->setScope($scope);
            $transition->save();
            $workflow->setInitialTransition($transition);
            $workflow->save();

            return $steps;
        }

        public function setWorkflow(Workflow $workflow)
        {
            $this->_workflow_id = $workflow;
        }

        public function setWorkflowId($workflow_id)
        {
            $this->_workflow_id = $workflow_id;
        }

        public function setLinkedStatusID($status_id)
        {
            $this->_status_id = $status_id;
        }

        public function setIsClosed($is_closed = true)
        {
            $this->_closed = $is_closed;
        }

        public function setIsEditable($is_editable = true)
        {
            $this->_editable = $is_editable;
        }

        public static function loadBalancedWorkflowFixtures(Scope $scope, Workflow $workflow)
        {
            $steps = [];
            $steps['new'] = [
                'name' => 'New',
                'description' => 'A new issue, not yet handled',
                'status_id' => Status::getOrCreateByKeyish($scope, 'new', 'New')->getID(),
                'transitions' => ['resolveissue', 'closeissue', 'confirmissue'],
                'editable' => true,
                'is_closed' => false
            ];
            $steps['confirmed'] = [
                'name' => 'Confirmed',
                'description' => 'A new issue, not yet handled',
                'status_id' => Status::getOrCreateByKeyish($scope, 'confirmed', 'Confirmed')->getID(),
                'transitions' => ['resolveissue', 'closeissue', 'startprogress'],
                'editable' => true,
                'is_closed' => false
            ];
            $steps['inprogress'] = [
                'name' => 'In progress',
                'description' => 'An issue that is being worked on',
                'status_id' => Status::getOrCreateByKeyish($scope, 'inprogress', 'In progress')->getID(),
                'transitions' => ['closeissue', 'resolveissue', 'readyfortesting'],
                'editable' => true,
                'is_closed' => false
            ];
            $steps['readyfortesting'] = [
                'name' => 'Ready for testing',
                'description' => 'An issue that is ready to be tested',
                'status_id' => Status::getOrCreateByKeyish($scope, 'readytotest', 'Ready to test')->getID(),
                'transitions' => ['resolveissue', 'closeissue', 'reopenissue'],
                'editable' => false,
                'is_closed' => false
            ];
            $steps['resolved'] = [
                'name' => 'Resolved',
                'description' => 'An issue that has been resolved',
                'status_id' => Status::getOrCreateByKeyish($scope, 'resolved', 'Resolved')->getID(),
                'transitions' => ['reopenissue', 'closeissue'],
                'editable' => false,
                'is_closed' => false
            ];
            $steps['closed'] = [
                'name' => 'Closed',
                'description' => 'An issue that has been closed',
                'status_id' => Status::getOrCreateByKeyish($scope, 'closed', 'Closed')->getID(),
                'transitions' => ['reopenissue'],
                'editable' => false,
                'is_closed' => true
            ];
            $steps['reopened'] = [
                'name' => 'Reopened',
                'description' => 'An issue that was previously resolved or closed',
                'status_id' => Status::getOrCreateByKeyish($scope, 'reopened', 'Reopened')->getID(),
                'transitions' => ['resolveissue', 'closeissue', 'startprogress'],
                'editable' => true,
                'is_closed' => false
            ];

            $steps = self::loadWorkflowStepsAndTransitions($scope, $workflow, $steps);
            WorkflowTransition::loadBalancedWorkflowFixtures($scope, $workflow, $steps);
        }

        public static function loadSimpleWorkflowFixtures(Scope $scope, Workflow $workflow)
        {
            $steps = [];
            $steps['new'] = [
                'name' => 'Open',
                'description' => 'A new issue, not yet handled',
                'status_id' => Status::getOrCreateByKeyish($scope, 'open', 'Open')->getID(),
                'transitions' => ['resolveissue', 'closeissue', 'startprogress'],
                'editable' => true,
                'is_closed' => false
            ];
            $steps['inprogress'] = [
                'name' => 'In progress',
                'description' => 'An issue that is being worked on',
                'status_id' => Status::getOrCreateByKeyish($scope, 'inprogress', 'In progress')->getID(),
                'transitions' => ['closeissue', 'resolveissue'],
                'editable' => true,
                'is_closed' => false
            ];
            $steps['resolved'] = [
                'name' => 'Resolved',
                'description' => 'An issue that has been resolved',
                'status_id' => Status::getOrCreateByKeyish($scope, 'resolved', 'Resolved')->getID(),
                'transitions' => ['reopenissue', 'closeissue'],
                'editable' => false,
                'is_closed' => false
            ];
            $steps['closed'] = [
                'name' => 'Closed',
                'description' => 'An issue that has been closed',
                'status_id' => Status::getOrCreateByKeyish($scope, 'closed', 'Closed')->getID(),
                'transitions' => ['reopenissue'],
                'editable' => false,
                'is_closed' => true
            ];
            $steps['reopened'] = [
                'name' => 'Reopened',
                'description' => 'An issue that was previously resolved or closed',
                'status_id' => Status::getOrCreateByKeyish($scope, 'reopened', 'Reopened')->getID(),
                'transitions' => ['resolveissue', 'closeissue', 'startprogress'],
                'editable' => true,
                'is_closed' => false
            ];

            $steps = self::loadWorkflowStepsAndTransitions($scope, $workflow, $steps);
            WorkflowTransition::loadSimpleWorkflowFixtures($scope, $workflow, $steps);
        }

        public static function getAllByWorkflowSchemeID($scheme_id)
        {
            $ids = tables\WorkflowSteps::getTable()->getAllByWorkflowSchemeID($scheme_id);
            $steps = [];
            foreach ($ids as $step_id) {
                $steps[$step_id] = new WorkflowStep((int)$step_id);
            }

            return $steps;
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

        public function isEditable()
        {
            return (bool)$this->_editable;
        }

        public function getNumberOfOutgoingTransitions()
        {
            if ($this->_num_outgoing_transitions === null && $this->_outgoing_transitions !== null) {
                $this->_num_outgoing_transitions = count($this->_outgoing_transitions);
            } elseif ($this->_num_outgoing_transitions === null) {
                $this->_num_outgoing_transitions = tables\WorkflowStepTransitions::getTable()->countByStepID($this->getID());
            }

            return $this->_num_outgoing_transitions;
        }

        public function hasOutgoingTransition(WorkflowTransition $transition): bool
        {
            $transitions = $this->getOutgoingTransitions();

            return array_key_exists($transition->getID(), $transitions);
        }

        /**
         * Get all outgoing transitions from this step
         *
         * @return WorkflowTransition[]
         */
        public function getOutgoingTransitions()
        {
            $this->_populateOutgoingTransitions();

            return $this->_outgoing_transitions;
        }

        protected function _populateOutgoingTransitions()
        {
            if ($this->_outgoing_transitions === null) {
                $this->_outgoing_transitions = tables\WorkflowStepTransitions::getTable()->getByStepID($this->getID());
            }
        }

        public function addOutgoingTransition(WorkflowTransition $transition)
        {
            tables\WorkflowStepTransitions::getTable()->addNew($this->getID(), $transition->getID(), $this->getWorkflow()->getID());
            if ($this->_outgoing_transitions !== null) {
                $this->_outgoing_transitions[$transition->getID()] = $transition;
            }
            if ($this->_num_outgoing_transitions !== null) {
                $this->_num_outgoing_transitions++;
            }
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

        public function deleteOutgoingTransitions()
        {
            tables\WorkflowStepTransitions::getTable()->deleteByStepID($this->getID());
        }

        /**
         * Get all incoming transitions from this step
         *
         * @return array An array of \pachno\core\entities\WorkflowTransition objects
         */
        public function getIncomingTransitions()
        {
            $this->_populateIncomingTransitions();

            return $this->_incoming_transitions;
        }

        protected function _populateIncomingTransitions()
        {
            if ($this->_incoming_transitions === null) {
                $this->_incoming_transitions = tables\WorkflowTransitions::getTable()->getByStepID($this->getID());
            }
        }

        public function hasIncomingTransitions()
        {
            return (bool)($this->getNumberOfIncomingTransitions() > 0);
        }

        public function getNumberOfIncomingTransitions()
        {
            if ($this->_num_incoming_transitions === null && $this->_incoming_transitions !== null) {
                $this->_num_incoming_transitions = count($this->_incoming_transitions);
            } elseif ($this->_num_incoming_transitions === null) {
                $this->_num_incoming_transitions = tables\WorkflowTransitions::getTable()->countByStepID($this->getID());
            }

            return $this->_num_incoming_transitions;
        }

        public function getAvailableTransitionsForIssue(Issue $issue)
        {
            $return_array = [];
            foreach ($this->getOutgoingTransitions() as $transition) {
                if ($transition->isAvailableForIssue($issue))
                    $return_array[$transition->getID()] = $transition;
            }

            return $return_array;
        }

        public function applyToIssue(Issue $issue)
        {
            $issue->setWorkflowStep($this);
            if ($this->hasLinkedStatus()) {
                $issue->setStatus($this->getLinkedStatusID());
            }
            if ($this->isClosed()) {
                $issue->close();
            } else {
                $issue->open();
            }
        }

        /**
         * Whether or not this step is linked to a specific status
         *
         * @return boolean
         */
        public function hasLinkedStatus()
        {
            return ($this->getLinkedStatus() instanceof Status);
        }

        /**
         * Return this steps linked status if any
         *
         * @return ?Status
         */
        public function getLinkedStatus()
        {
            return $this->_b2dbLazyLoad('_status_id');
        }

        public function getLinkedStatusID()
        {
            return ($this->hasLinkedStatus()) ? $this->getLinkedStatus()->getID() : null;
        }

        public function isClosed()
        {
            return (bool)$this->_closed;
        }

        public function copy(Workflow $new_workflow)
        {
            $new_step = clone $this;
            $new_step->setWorkflow($new_workflow);
            $new_step->save();

            return $new_step;
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
    
        public function getSortOrder()
        {
            return $this->_sort_order;
        }
    
        public function setSortOrder($sort_order)
        {
            $this->_sort_order = $sort_order;
        }
    
        /**
         * @param int[] $transition_orders
         */
        public function setTransitionOrder($transition_orders): void
        {
            $order = 0;
            foreach ($transition_orders as $id) {
                $step_transition = WorkflowStepTransitions::getTable()->selectById($id);
                if (!$step_transition instanceof WorkflowStepTransition) {
                    continue;
                }

                $step_transition->setSortOrder($order);
                $step_transition->save();
                $order += 1;
            }
        }

    }
