<?php

    namespace pachno\core\entities\tables;

    use b2db\Table;

    /**
     * Userstate table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Userstate table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="usersession")
     * @Entity(class="\pachno\core\entities\UserSession")
     */
    class UserSessions extends Table
    {

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'usersession';

    }
