<?php

    namespace pachno\core\modules\installation\entities\upgrade_1_0_0;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\Project;
    use pachno\core\entities\User;

    /**
     * Dashboard class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\modules\installation\entities\upgrade_1_0_0\tables\LivelinkImports")
     */
    class LivelinkImport extends IdentifiableScoped
    {

        public const STATUS_CREATED = 0;
        public const STATUS_IMPORTING = 1;
        public const STATUS_IMPORTED = 2;
        public const STATUS_IMPORTED_ERROR = 3;

        /**
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_completed_at;

        /**
         * @Column(type="serializable", length=1000)
         */
        protected $_data;

    }
