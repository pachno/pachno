<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * Project assigned users table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Project assigned users table
     *
     * @method static ProjectAssignedUsers getTable()
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="projectassignedusers")
     */
    class ProjectAssignedUsers extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'projectassignedusers';

        public const ID = 'projectassignedusers.id';

        public const SCOPE = 'projectassignedusers.scope';

        public const USER_ID = 'projectassignedusers.uid';

        public const PROJECT_ID = 'projectassignedusers.project_id';

        public const ROLE_ID = 'projectassignedusers.role_id';

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

        public function deleteById($id)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $id);
            $res = $this->rawDelete($query);

            return $res;
        }

        public function addUserToProject($project_id, $user_id, $role_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::USER_ID, $user_id);
            $query->where(self::ROLE_ID, $role_id);
            if (!$this->count($query)) {
                $insertion = new Insertion();
                $insertion->add(self::PROJECT_ID, $project_id);
                $insertion->add(self::USER_ID, $user_id);
                $insertion->add(self::ROLE_ID, $role_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);

                return true;
            }

            return false;
        }

        public function getUserByProjectIDUserIDRoleID($project_id, $user_id, $role_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::USER_ID, 'uid');
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::USER_ID, $user_id);;
            $query->where(self::ROLE_ID, $role_id);
            $users = [];

            if ($res = $this->rawSelect($query, 'none')) {
                while ($row = $res->getNextRow()) {
                    $uid = $row['uid'];
                    if (!array_key_exists($uid, $users))
                        $users[$uid] = Users::getTable()->selectById($uid);
                    // Only one user is needed since only one can be inserted in method "addUserToProject".
                    break;
                }
            }

            return $users;
        }

        public function getProjectsByUserID($user_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::PROJECT_ID, 'pid');
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $projects = [];

            if ($res = $this->rawSelect($query, 'none')) {
                while ($row = $res->getNextRow()) {
                    $pid = $row['pid'];
                    if (!array_key_exists($pid, $projects))
                        $projects[$pid] = Projects::getTable()->selectById($pid);
                }
            }

            return $projects;
        }

        public function removeUserFromProject($project_id, $user)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $query->where(self::USER_ID, $user);
            $this->rawDelete($query);
        }

        public function getRolesForProject($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $res = $this->rawSelect($query);

            $roles = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $roles[$row->get(self::USER_ID)][] = ListTypes::getTable()->selectById($row->get(self::ROLE_ID));
                }
            }

            return $roles;
        }

        public function getUsersByRoleID($role_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::USER_ID, 'uid');
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $users = [];

            if ($res = $this->rawSelect($query, 'none')) {
                while ($row = $res->getNextRow()) {
                    $uid = $row['uid'];
                    if (!array_key_exists($uid, $users))
                        $users[$uid] = Users::getTable()->selectById($uid);
                }
            }

            return $users;
        }

        /**
         * Obtains information about all users assigned to different projects
         * through the same (provided) role.
         *
         * @param role_id Role ID.
         *
         * @return pachno\core\entities\tables\ProjectAssignedUsers\row[] Array of rows with requested information.
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
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable());
            parent::addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
        }
    }
