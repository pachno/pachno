<?php

    namespace pachno\core\entities\tables;

    use pachno\core\framework;

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
     * @Table(name="userstate")
     * @Entity(class="\pachno\core\entities\Userstate")
     */
    class UserStates extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'userstate';

        const ID = 'userstate.id';

        const SCOPE = 'userstate.scope';

        const NAME = 'userstate.name';

        const UNAVAILABLE = 'userstate.is_unavailable';

        const BUSY = 'userstate.is_busy';

        const ONLINE = 'userstate.is_online';

        const MEETING = 'userstate.is_in_meeting';

        const COLOR = 'userstate.itemdata';

        const ABSENT = 'userstate.is_absent';

        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

    }
