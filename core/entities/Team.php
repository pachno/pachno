<?php

    namespace pachno\core\entities;

    use Exception;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\Teams;
    use pachno\core\framework;

    /**
     * Team class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Team class
     *
     * @package pachno
     * @subpackage main
     *
     * @method static tables\Teams getB2DBTable()
     *
     * @Table(name="\pachno\core\entities\tables\Teams")
     */
    class Team extends IdentifiableScoped
    {

        protected static $_teams = null;

        protected static $_num_teams = null;

        protected $_members = null;

        protected $_num_members = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="boolean")
         */
        protected $_ondemand = false;

        /**
         * List of team's dashboards
         *
         * @var array|Dashboard
         * @Relates(class="\pachno\core\entities\Dashboard", collection=true, foreign_column="team_id", orderby="name")
         */
        protected $_dashboards = null;

        protected $_associated_projects = null;

        public static function doesTeamNameExist($team_name)
        {
            return tables\Teams::getTable()->doesTeamNameExist($team_name);
        }

        public static function getAll()
        {
            if (self::$_teams === null) {
                self::$_teams = Teams::getTable()->getAll();
            }

            return self::$_teams;
        }

        public static function doesIDExist($id)
        {
            return (bool)static::getB2DBTable()->doesIDExist($id);
        }

        public static function loadFixtures(Scope $scope)
        {
            $staff_members = new Team();
            $staff_members->setName('Staff members');
            $staff_members->save();

            $developers = new Team();
            $developers->setName('Developers');
            $developers->save();

            $team_leaders = new Team();
            $team_leaders->setName('Team leaders');
            $team_leaders->save();

            $testers = new Team();
            $testers->setName('Testers');
            $testers->save();

            $translators = new Team();
            $translators->setName('Translators');
            $translators->save();
        }

        public static function countAll()
        {
            if (self::$_num_teams === null) {
                if (self::$_teams !== null)
                    self::$_num_teams = count(self::$_teams);
                else
                    self::$_num_teams = tables\Teams::getTable()->countTeams();
            }

            return self::$_num_teams;
        }

        public function __toString()
        {
            return "" . $this->_name;
        }

        /**
         * Adds a user to the team
         *
         * @param User $user
         */
        public function addMember(User $user)
        {
            if (!$user->getID()) throw new Exception('Cannot add user object to team until the object is saved');

            tables\TeamMembers::getTable()->addUserToTeam($user->getID(), $this->getID());

            if (is_array($this->_members))
                $this->_members[$user->getID()] = $user->getID();
        }

        public function removeMember(User $user)
        {
            if ($this->_members !== null) {
                unset($this->_members[$user->getID()]);
            }
            if ($this->_num_members !== null) {
                $this->_num_members--;
            }
            tables\TeamMembers::getTable()->removeUserFromTeam($user->getID(), $this->getID());
        }

        /**
         * @return Project[][]
         */
        public function getProjects()
        {
            /** @var Project[] $projects */
            $projects = [];

            foreach (Project::getAllByOwner($this) as $project) {
                $projects[$project->getID()] = $project;
            }
            foreach (Project::getAllByLeader($this) as $project) {
                $projects[$project->getID()] = $project;
            }
            foreach (Project::getAllByQaResponsible($this) as $project) {
                $projects[$project->getID()] = $project;
            }
            foreach ($this->getAssociatedProjects() as $project_id => $project) {
                $projects[$project_id] = $project;
            }

            $active_projects = [];
            $archived_projects = [];

            foreach ($projects as $project_id => $project) {
                if ($project->isArchived()) {
                    $archived_projects[$project_id] = $project;
                } else {
                    $active_projects[$project_id] = $project;
                }
            }

            return [$active_projects, $archived_projects];
        }

        /**
         * Get all the projects a team is associated with
         *
         * @return array
         */
        public function getAssociatedProjects()
        {
            if ($this->_associated_projects === null) {
                $this->_associated_projects = [];

                $project_ids = tables\ProjectAssignedTeams::getTable()->getProjectsByTeamID($this->getID());
                foreach ($project_ids as $project_id) {
                    $this->_associated_projects[$project_id] = Project::getB2DBTable()->selectById($project_id);
                }
            }

            return $this->_associated_projects;
        }

        public function isOndemand()
        {
            return $this->_ondemand;
        }

        public function setOndemand($val = true)
        {
            $this->_ondemand = $val;
        }

        public function hasAccess()
        {
            return (bool) framework\Context::getUser()->isMemberOfTeam($this);
        }

        /**
         * Alias for getName
         *
         * @return string
         */
        public function getNameWithUsername()
        {
            return $this->getName();
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Returns an array of team dashboards
         *
         * @return Dashboard[]
         */
        public function getDashboards()
        {
            $this->_b2dbLazyLoad('_dashboards');

            return $this->_dashboards;
        }

        public function toJSON($detailed = true)
        {
            $returnJSON = [
                'id' => $this->getID(),
                'name' => $this->getName(),
                'type' => 'team' // This is for distinguishing of assignees & similar "ambiguous" values in JSON.
            ];

            if ($detailed) {
                $returnJSON['member_count'] = $this->getNumberOfMembers();
                $returnJSON['members'] = [];
                foreach ($this->getMembers() as $member) {
                    $returnJSON['members'][] = $member->toJSON();
                }
            }

            return $returnJSON;
        }

        public function getNumberOfMembers()
        {
            if ($this->_members !== null) {
                return count($this->_members);
            } elseif ($this->_num_members === null) {
                $this->_num_members = tables\TeamMembers::getTable()->getNumberOfMembersByTeamID($this->getID());
            }

            return $this->_num_members;
        }

        public function getMembers()
        {
            if ($this->_members === null) {
                $this->_members = [];
                foreach (tables\TeamMembers::getTable()->getUIDsForTeamID($this->getID()) as $uid) {
                    $this->_members[$uid] = User::getB2DBTable()->selectById($uid);
                }
            }

            return $this->_members;
        }

        protected function _preDelete()
        {
            tables\TeamMembers::getTable()->removeUsersFromTeam($this->getID());
        }

    }
