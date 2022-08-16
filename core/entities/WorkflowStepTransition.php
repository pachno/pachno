<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Workflow transition class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\WorkflowStepTransitions")
     */
    class WorkflowStepTransition extends IdentifiableScoped
    {
        
        /**
         * The outgoing step from this transition
         *
         * @var WorkflowStep
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowStep")
         */
        protected $_from_step_id = null;
    
        /**
         * The connected transition
         *
         * @var WorkflowTransition
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowTransition")
         */
        protected $_transition_id = null;

        /**
         * The associated workflow object
         *
         * @var Workflow
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Workflow")
         */
        protected $_workflow_id = null;
    
        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order;
    
        public function getSortOrder()
        {
            return $this->_sort_order;
        }
    
        public function setSortOrder($sort_order)
        {
            $this->_sort_order = $sort_order;
        }
    
    }
