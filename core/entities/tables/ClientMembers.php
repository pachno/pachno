<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * Client members table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Client members table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="clientmembers")
     */
    class ClientMembers extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'clientmembers';

        public const ID = 'clientmembers.id';

        public const SCOPE = 'clientmembers.scope';

        public const USER_ID = 'clientmembers.uid';

        public const CLIENT_ID = 'clientmembers.cid';

        public function getUIDsForClientID($client_id)
        {
            $query = $this->getQuery();
            $query->where(self::CLIENT_ID, $client_id);

            $uids = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $uids[$row->get(self::USER_ID)] = $row->get(self::USER_ID);
                }
            }

            return $uids;
        }

        public function clearClientsByUserID($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $res = $this->rawDelete($query);
        }

        public function getNumberOfMembersByClientID($client_id)
        {
            $query = $this->getQuery();
            $query->where(self::CLIENT_ID, $client_id);
            $count = $this->count($query);

            return $count;
        }

        public function cloneClientMemberships($cloned_client_id, $new_client_id)
        {
            $query = $this->getQuery();
            $query->where(self::CLIENT_ID, $cloned_client_id);
            $memberships_to_add = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $memberships_to_add[] = $row->get(self::USER_ID);
                }
            }

            foreach ($memberships_to_add as $uid) {
                $insertion = new Insertion();
                $insertion->add(self::USER_ID, $uid);
                $insertion->add(self::CLIENT_ID, $new_client_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);
            }
        }

        public function getClientIDsForUserID($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);

            return $this->rawSelect($query);
        }

        public function addUserToClient($user_id, $client_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::CLIENT_ID, $client_id);
            $insertion->add(self::USER_ID, $user_id);
            $this->rawInsert($insertion);
        }

        public function removeUserFromClient($user_id, $client_id)
        {
            if (empty($client_id)) {
                return;
            }
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::CLIENT_ID, $client_id, Criterion::IN);
            $query->where(self::USER_ID, $user_id);
            $this->rawDelete($query);
        }

        public function removeUsersFromClient($client_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::CLIENT_ID, $client_id);
            $this->rawDelete($query);
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable());
            parent::addForeignKeyColumn(self::CLIENT_ID, Clients::getTable());
        }

    }
