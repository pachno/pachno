<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * Team members table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Team members table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="teammembers")
     */
    class TeamMembers extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'teammembers';

        const ID = 'teammembers.id';

        const SCOPE = 'teammembers.scope';

        const USER_ID = 'teammembers.uid';

        const TEAM_ID = 'teammembers.tid';

        public function getUIDsForTeamID($team_id)
        {
            $query = $this->getQuery();
            $query->where(self::TEAM_ID, $team_id);

            $uids = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $uids[$row->get(self::USER_ID)] = $row->get(self::USER_ID);
                }
            }

            return $uids;
        }

        public function clearTeamsByUserID($user_id)
        {
            $team_ids = [];

            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->join(Teams::getTable(), Teams::ID, self::TEAM_ID);
            $query->where(Teams::ONDEMAND, false);

            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $team_ids[$row->get(self::TEAM_ID)] = true;
                }
            }

            if (!empty($team_ids)) {
                $query = $this->getQuery();
                $query->where(self::USER_ID, $user_id);
                $query->where(self::TEAM_ID, array_keys($team_ids), Criterion::IN);
                $res = $this->rawDelete($query);
            }
        }

        public function getNumberOfMembersByTeamID($team_id)
        {
            $query = $this->getQuery();
            $query->where(self::TEAM_ID, $team_id);
            $count = $this->count($query);

            return $count;
        }

        public function cloneTeamMemberships($cloned_team_id, $new_team_id)
        {
            $query = $this->getQuery();
            $query->where(self::TEAM_ID, $cloned_team_id);
            $memberships_to_add = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $memberships_to_add[] = $row->get(self::USER_ID);
                }
            }

            foreach ($memberships_to_add as $uid) {
                $insertion = new Insertion();
                $insertion->add(self::USER_ID, $uid);
                $insertion->add(self::TEAM_ID, $new_team_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);
            }
        }

        public function addUserToTeam($user_id, $team_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::TEAM_ID, $team_id);
            $insertion->add(self::USER_ID, $user_id);
            $this->rawInsert($insertion);
        }

        public function removeUserFromTeam($user_id, $team_ids)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if (is_array($team_ids)) {
                $query->where(self::TEAM_ID, $team_ids, Criterion::IN);
            } else {
                $query->where(self::TEAM_ID, $team_ids);
            }
            $query->where(self::USER_ID, $user_id);
            $this->rawDelete($query);
        }

        public function removeUsersFromTeam($team_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TEAM_ID, $team_id);
            $this->rawDelete($query);
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable());
            parent::addForeignKeyColumn(self::TEAM_ID, Teams::getTable());
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope_uid', [self::USER_ID, self::SCOPE]);
        }

    }
