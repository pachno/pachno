<?php
    
    namespace pachno\core\modules\installation\entities\upgrade_1_0_5;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\Status;
    use pachno\core\entities\Workflow;

    /**
     * Workflow step class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\modules\installation\entities\upgrade_1_0_5\tables\WorkflowSteps")
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
         * @var ?bool
         * @Column(type="boolean")
         */
        protected $_editable = null;

        /**
         * @var ?bool
         * @Column(type="boolean")
         */
        protected $_closed = null;

        /**
         * @var ?Status
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Status")
         */
        protected $_status_id = null;

        /**
         * The associated workflow object
         *
         * @var Workflow
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Workflow")
         */
        protected $_workflow_id = null;

    }
