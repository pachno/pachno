<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\Identifiable;

    /**
     * User session class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\UserSessions")
     */
    class UserSession extends Identifiable
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The session token
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_token;

        /**
         * @var int
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * @var int
         * @Column(type="integer", length=10)
         */
        protected $_last_used_at;

        /**
         * @var bool
         * @Column(type="boolean", default=false)
         */
        protected $_is_elevated = false;

        /**
         * @var int
         * @Column(type="integer", length=10)
         */
        protected $_expires_at;

        /**
         * Who the session is for
         *
         * @var \pachno\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

    }
