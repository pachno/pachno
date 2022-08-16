<?php
    
    namespace pachno\core\modules\installation\entities\upgrade_1_0_5;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Workflow transition class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\modules\installation\entities\upgrade_1_0_5\tables\WorkflowStepTransitions")
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
        
    }
