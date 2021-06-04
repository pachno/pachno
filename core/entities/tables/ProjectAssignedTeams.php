<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\entities\Role;
    use pachno\core\entities\Team;
    use pachno\core\framework;

    /**
     * Project assigned teams table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Project assigned teams table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="projectassignedteams")
     */
    class ProjectAssignedTeams extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'projectassignedteams';

        public const ID = 'projectassignedteams.id';

        public const SCOPE = 'projectassignedteams.scope';

        public const TEAM_ID = 'projectassignedteams.uid';

        public const PROJECT_ID = 'projectassignedteams.project_id';

        public const ROLE_ID = 'projectassignedteams.role_id';

        public function deleteByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $res = $this->rawDelete($query);

            return $res;
        }

        public function deleteByRoleID($role_id)
        {
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $res = $this->rawDelete($query);

            return $res;
        }

        public function addTeamToProject($project_id, $team_id, $role_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::TEAM_ID, $team_id);
            $query->where(self::ROLE_ID, $role_id);
            if (!$this->count($query)) {
                $insertion = new Insertion();
                $insertion->add(self::PROJECT_ID, $project_id);
                $insertion->add(self::TEAM_ID, $team_id);
                $insertion->add(self::ROLE_ID, $role_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);

                return true;
            }

            return false;
        }

        public function getTeamByProjectIDTeamIDRoleID($project_id, $team_id, $role_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::TEAM_ID, 'tid');
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::TEAM_ID, $team_id);
            $query->where(self::ROLE_ID, $role_id);
            $teams = [];

            if ($res = $this->rawSelect($query, 'none')) {
                while ($row = $res->getNextRow()) {
                    $tid = $row['tid'];
                    if (!array_key_exists($tid, $teams))
                        $teams[$tid] = new Team($tid);
                    // Only one team is needed since only one can be inserted in method "addTeamToProject".
                    break;
                }
            }

            return $teams;
        }

        public function removeTeamFromProject($project_id, $team)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::TEAM_ID, $team);
            $this->rawDelete($query);
        }

        public function getProjectsByTeamID($team)
        {
            $query = $this->getQuery();
            $query->where(self::TEAM_ID, $team);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            $projects = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $pid = $row->get(self::PROJECT_ID);
                    $projects[$pid] = $pid;
                }
            }

            return $projects;
        }

        public function getRolesForProject($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $res = $this->rawSelect($query);

            $roles = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $roles[$row->get(self::TEAM_ID)][] = new Role($row->get(self::ROLE_ID));
                }
            }

            return $roles;
        }

        public function getTeamsByRoleID($role_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::TEAM_ID, 'tid');
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $teams = [];

            if ($res = $this->rawSelect($query, 'none')) {
                while ($row = $res->getNextRow()) {
                    $tid = $row['tid'];
                    if (!array_key_exists($tid, $teams))
                        $teams[$tid] = new Team($tid);
                }
            }

            return $teams;
        }

        public function getTeamsByRoleIDAndProjectID($role_id, $project_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::TEAM_ID, 'tid');
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $teams = [];

            if ($res = $this->rawSelect($query, 'none')) {
                while ($row = $res->getNextRow()) {
                    $tid = $row['tid'];
                    if (!array_key_exists($tid, $teams))
                        $teams[$tid] = new Team($tid);
                }
            }

            return $teams;
        }

        /**
         * Obtains information about all teams assigned to different projects
         * through the same (provided) role.
         *
         * @param role_id Role ID.
         *
         * @return pachno\core\entities\tables\ProjectAssignedTeams\row[] Array of rows with requested information.
         */
        public function getAssignmentsByRoleID($role_id)
        {
            $assignments = [];

            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $assignments[] = $row;
                }
            }

            return $assignments;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::PROJECT_ID, Projects::getTable());
            parent::addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
            parent::addForeignKeyColumn(self::TEAM_ID, Teams::getTable());
        }
    }
