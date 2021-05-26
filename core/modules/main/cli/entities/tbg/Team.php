<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use Exception;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\Dashboard;

    /**
     * Team class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Teams")
     */
    class Team extends IdentifiableScoped
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="boolean")
         */
        protected $_ondemand = false;

        /**
         * List of team's dashboards
         *
         * @var array|Dashboard
         * @Relates(class="\pachno\core\entities\Dashboard", collection=true, foreign_column="team_id", orderby="name")
         */
        protected $_dashboards = null;

        /**
         * @var Permission[]
         * @Relates(class="\pachno\core\entities\Permission", collection=true, foreign_column="tid")
         */
        protected $_permissions = null;

    }
