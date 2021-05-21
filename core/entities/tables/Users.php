<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Join;
    use b2db\Query;
    use b2db\Table;
    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * Users table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Users getTable()
     * @method User[] selectAll()
     * @method User|null selectOne(Query $query, $join = 'all')
     * @method User|null selectById($id, Query $query = null, $join = 'all')
     *
     * @Table(name="users")
     * @Entity(class="\pachno\core\entities\User")
     */
    class Users extends Table
    {

        const B2DB_TABLE_VERSION = 3;

        const B2DBNAME = 'users';

        const ID = 'users.id';

        const USERNAME = 'users.username';

        const PASSWORD = 'users.password';

        const BUDDYNAME = 'users.buddyname';

        const REALNAME = 'users.realname';

        const EMAIL = 'users.email';

        const USERSTATE = 'users.userstate';

        const CUSTOMSTATE = 'users.customstate';

        const HOMEPAGE = 'users.homepage';

        const LANGUAGE = 'users.language';

        const LASTSEEN = 'users.lastseen';

        const QUOTA = 'users.quota';

        const ACTIVATED = 'users.activated';

        const ENABLED = 'users.enabled';

        const DELETED = 'users.deleted';

        const AVATAR = 'users.avatar';

        const USE_GRAVATAR = 'users.use_gravatar';

        const PRIVATE_EMAIL = 'users.private_email';

        const JOINED = 'users.joined';

        const GROUP_ID = 'users.group_id';

        const OPENID_LOCKED = 'users.openid_locked';

        protected $_username_lookup_cache = [];

        /**
         * @return User
         */
        public function getAll()
        {
            return $this->selectAll();
        }

        /**
         * @param $username
         *
         * @return User
         */
        public function getByUsername($username): ?User
        {
            if (trim($username) == '') {
                return null;
            }

            if (!array_key_exists($username, $this->_username_lookup_cache)) {
                $query = $this->getQuery();
                $query->where(self::USERNAME, strtolower($username), Criterion::EQUALS, '', '', Query::DB_LOWER);
                $query->where(self::DELETED, false);

                $user = $this->selectOne($query);
                $this->_username_lookup_cache[$username] = $user;
            }

            return $this->_username_lookup_cache[$username];
        }

        /**
         * @param $realname
         *
         * @return User
         */
        public function getByRealname($realname)
        {
            $query = $this->getQuery();
            $query->where(self::REALNAME, strtolower($realname), Criterion::EQUALS, '', '', Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return $this->selectOne($query);
        }

        /**
         * @param $buddyname
         *
         * @return User
         */
        public function getByBuddyname($buddyname)
        {
            $query = $this->getQuery();
            $query->where(self::BUDDYNAME, strtolower($buddyname), Criterion::EQUALS, '', '', Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return $this->selectOne($query);
        }

        /**
         * @param $email
         *
         * @return User
         */
        public function getByEmail($email)
        {
            $query = $this->getQuery();
            $query->where(self::EMAIL, strtolower($email), Criterion::EQUALS, '', '', Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return $this->selectOne($query);
        }

        public function isEmailAvailable($email)
        {
            $query = $this->getQuery();
            $query->where(self::EMAIL, strtolower($email), Criterion::EQUALS, '', '', Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return !(bool)$this->count($query);
        }

        public function isUsernameAvailable($username)
        {
            $query = $this->getQuery();
            $query->where(self::USERNAME, strtolower($username), Criterion::EQUALS, '', '', Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return !(bool)$this->count($query);
        }

        /**
         * @param $userids
         *
         * @return User[]
         */
        public function getByUserIDs($userids)
        {
            if (empty($userids)) return [];

            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ID, $userids, Criterion::IN);

            return $this->select($query);
        }

        /**
         * @param $userid
         *
         * @return User
         */
        public function getByUserID($userid)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);

            return $this->selectById($userid, $query);
        }

        public function doesIDExist($userid)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ID, $userid);

            return $this->count($query);
        }

        /**
         * @param string $details
         * @param int $limit
         *
         * @param bool $return_empty
         *
         * @return User[]
         * @throws \b2db\Exception
         */
        public function getByDetails($details, $limit = null, $return_empty = false)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            if (mb_stristr($details, "@")) {
                $query->where(self::EMAIL, "%$details%", Criterion::LIKE);
            } else {
                $query->where(self::USERNAME, "%$details%", Criterion::LIKE);
            }

            if ($limit) {
                $query->setLimit($limit);
            }
            $res = $this->select($query);
            if (!$res && !$return_empty) {
                $query = $this->getQuery();
                $query->where(self::DELETED, false);
                $query->where(self::USERNAME, "%$details%", Criterion::LIKE);
                $query->or(self::BUDDYNAME, "%$details%", Criterion::LIKE);
                $query->or(self::REALNAME, "%$details%", Criterion::LIKE);
                $query->or(self::EMAIL, "%$details%", Criterion::LIKE);
                if ($limit) {
                    $query->setLimit($limit);
                }
                $res = $this->select($query);
            }

            $users = [];
            if ($res) {
                foreach ($res as $key => $user) {
                    if ($user->isScopeConfirmed()) {
                        $users[$key] = $user;
                    }
                }
            }

            return $users;
        }

        /**
         * @param string $details
         * @param int $limit
         * @param bool $allow_keywords
         *
         * @return User[]
         */
        public function findInConfig($details, $limit = 50, $allow_keywords = true)
        {
            $query = $this->getQuery();

            switch ($details) {
                case 'unactivated':
                    if ($allow_keywords) {
                        $query->where(self::ACTIVATED, false);
                        $limit = 500;
                        break;
                    }
                case 'newusers':
                    if ($allow_keywords) {
                        $query->where(self::JOINED, NOW - 1814400, Criterion::GREATER_THAN_EQUAL);
                        $limit = 500;
                        break;
                    }
                case '0-9':
                    if ($allow_keywords) {
                        $criteria = new Criteria();
                        $criteria->where(self::USERNAME, ['0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'], Criterion::IN);
                        $criteria->or(self::BUDDYNAME, ['0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'], Criterion::IN);
                        $criteria->or(self::REALNAME, ['0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'], Criterion::IN);
                        $query->where($criteria);
                        $limit = 500;
                        break;
                    }
                case 'all':
                    if ($allow_keywords) {
                        $limit = 500;
                        break;
                    }
                default:
                    if (mb_strlen($details) == 1) $limit = 500;
                    $details = (mb_strlen($details) == 1) ? mb_strtolower("$details%") : mb_strtolower("%$details%");
                    $criteria = new Criteria();
                    $criteria->where(self::USERNAME, $details, Criterion::LIKE);
                    $criteria->or(self::BUDDYNAME, $details, Criterion::LIKE);
                    $criteria->or(self::REALNAME, $details, Criterion::LIKE);
                    $criteria->or(self::EMAIL, $details, Criterion::LIKE);
                    $query->where($criteria);
                    break;
            }
            $query->join(UserScopes::getTable(), UserScopes::USER_ID, self::ID, [], Join::INNER);
            $query->where(UserScopes::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::DELETED, false);
            $query->where(self::USERNAME, 'guest', Criterion::NOT_EQUALS);

            $users = [];
            $res = null;

            if ($details != '' && $res = $this->rawSelect($query)) {
                while (($row = $res->getNextRow()) && count($users) < $limit) {
                    $user_id = (int)$row->get(self::ID);
                    $details = UserScopes::getTable()->getUserDetailsByScope($user_id, framework\Context::getScope()->getID());
                    if (!$details) continue;
                    $users[$user_id] = User::getB2DBTable()->selectById($user_id);
                    $users[$user_id]->setScopeConfirmed($details['confirmed']);
                }
            }

            return $users;
        }

        public function getAllUserIDs()
        {
            $query = $this->getQuery();

            $query->addSelectionColumn(self::ID, 'uid');
            $res = $this->rawSelect($query);

            $uids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $uid = $row->get('uid');
                    $uids[$uid] = $uid;
                }
            }

            return $uids;
        }

        public function preloadUsers($user_ids)
        {
            if (!empty($user_ids)) {
                $query = $this->getQuery();
                $query->where(self::ID, $user_ids, Criterion::IN);
                $users = $this->select($query);
                unset($users);
            }

            return;
        }

        protected function setupIndexes()
        {
            $this->addIndex('userstate', self::USERSTATE);
            $this->addIndex('username_password', [self::USERNAME, self::PASSWORD]);
            $this->addIndex('username_deleted', [self::USERNAME, self::DELETED]);
        }

        protected function getUserMigrationDetails()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn('users.id');
            $query->addSelectionColumn('users.scope');
            $query->addSelectionColumn('users.group_id');
            $res = $this->rawSelect($query);

            $users = [];
            while ($row = $res->getNextRow()) {
                $users[$row->get('users.id')] = ['scope_id' => $row->get('users.scope'), 'group_id' => $row->get('users.group_id')];
            }

            return $users;
        }

    }
