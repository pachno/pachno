<?php

    namespace pachno\core\entities\tables;

    /**
     * Application passwords table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Application passwords table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="application_password")
     * @Entity(class="\pachno\core\entities\ApplicationPassword")
     */
    class ApplicationPasswords extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const SCOPE = 'application_password.scope';

    }
