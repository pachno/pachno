<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\Dashboard;

    /**
     * Client class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Clients")
     */
    class Client extends IdentifiableScoped
    {

        /**
         * The name of the client
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Email of client
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_email;

        /**
         * Telephone number of client
         *
         * @var integer
         * @Column(type="string", length=200)
         */
        protected $_telephone;

        /**
         * URL for client website
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_website;

        /**
         * Fax number of client
         *
         * @var integer
         * @Column(type="string", length=200)
         */
        protected $_fax;

        /**
         * List of client's dashboards
         *
         * @var array|Dashboard
         * @Relates(class="\pachno\core\entities\Dashboard", collection=true, foreign_column="client_id", orderby="name")
         */
        protected $_dashboards;

        /**
         * @var Permission[]
         * @Relates(class="\pachno\core\entities\Permission", collection=true, foreign_column="client_id")
         */
        protected $_permissions;

        protected $_permission_keys;

    }
