<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use b2db\Saveable;
    use pachno\core\entities\Userstate;
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
     * @method static UserStates getTable()
     * @method Userstate[] select(Query $query, $join = 'all')
     * @method Userstate[] selectAll()
     * @method Userstate selectOne(Query $query, $join = 'all')
     * @method Userstate selectById($id, Query $query = null, $join = 'all')
     *
     * @Table(name="userstate")
     * @Entity(class="\pachno\core\entities\Userstate")
     */
    class UserStates extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'userstate';

        public const ID = 'userstate.id';

        public const SCOPE = 'userstate.scope';

        public const NAME = 'userstate.name';

        public const UNAVAILABLE = 'userstate.is_unavailable';

        public const BUSY = 'userstate.is_busy';

        public const ONLINE = 'userstate.is_online';

        public const MEETING = 'userstate.is_in_meeting';

        public const COLOR = 'userstate.itemdata';

        public const ABSENT = 'userstate.is_absent';
    
        /**
         * @return Userstate[]
         */
        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

    }
