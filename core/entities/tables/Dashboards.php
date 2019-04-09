<?php

    namespace pachno\core\entities\tables;

    use pachno\core\entities\tables\ScopedTable;

    /**
     * User dashboards table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * User dashboards table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="dashboards")
     * @Entity(class="\pachno\core\entities\Dashboard")
     */
    class Dashboards extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const SCOPE = 'dashboards.scope';

    }
