<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * Buddies table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Buddies table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="buddies")
     */
    class Buddies extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'buddies';

        const ID = 'buddies.id';

        const SCOPE = 'buddies.scope';

        const USER_ID = 'buddies.uid';

        const BUDDY_USER_ID = 'buddies.bid';

        public function addFriend($user_id, $friend_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::USER_ID, $user_id);
            $insertion->add(self::BUDDY_USER_ID, $friend_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawInsert($insertion);
        }

        public function getFriendsByUserID($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $friends = [];
            if ($res = $this->rawSelect($query, false)) {
                while ($row = $res->getNextRow()) {
                    $friends[] = $row->get(self::BUDDY_USER_ID);
                }
            }

            return $friends;
        }

        public function removeFriendByUserID($user_id, $friend_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::BUDDY_USER_ID, $friend_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
            parent::addForeignKeyColumn(self::BUDDY_USER_ID, Users::getTable(), Users::ID);
        }

    }
