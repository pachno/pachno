<?php

    namespace pachno\core\entities;

    use b2db\Saveable;
    use Exception;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\common\Permissible;
    use pachno\core\framework;
    use pachno\core\framework\Context;

    /**
     * Client class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Client class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\Clients")
     */
    class Client extends IdentifiableScoped implements Permissible
    {

        /**
         * @var Client[]
         */
        protected static $_clients = null;

        protected $_members = null;

        protected $_num_members = null;

        /**
         * The name of the client
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Email of client
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_email = null;

        /**
         * Telephone number of client
         *
         * @var integer
         * @Column(type="string", length=200)
         */
        protected $_telephone = null;

        /**
         * URL for client website
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_website = null;

        /**
         * Fax number of client
         *
         * @var integer
         * @Column(type="string", length=200)
         */
        protected $_fax = null;

        /**
         * Client external contact user
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_external_contact_user_id = null;

        /**
         * Client internal contact user
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_internal_contact_user_id = null;

        /**
         * List of client's dashboards
         *
         * @var array|Dashboard
         * @Relates(class="\pachno\core\entities\Dashboard", collection=true, foreign_column="client_id", orderby="name")
         */
        protected $_dashboards = null;

        /**
         * @var Permission[]
         * @Relates(class="\pachno\core\entities\Permission", collection=true, foreign_column="client_id")
         */
        protected $_permissions = null;

        protected $_permission_keys;

        public static function doesClientNameExist($client_name): bool
        {
            return tables\Clients::getTable()->doesClientNameExist($client_name);
        }

        /**
         * @return Client[]
         */
        public static function getAll(): array
        {
            if (self::$_clients === null) {
                self::$_clients = tables\Clients::getTable()->getAll();
            }

            return self::$_clients;
        }

        public static function loadFixtures(Scope $scope)
        {

        }

        public function __toString(): string
        {
            return "" . $this->_name;
        }

        /**
         * Get the client's website
         *
         * @return string
         */
        public function getWebsite()
        {
            return $this->_website;
        }

        /**
         * Set the client's website
         *
         * @param string
         */
        public function setWebsite($website)
        {
            $this->_website = $website;
        }

        /**
         * Get the client's email address
         *
         * @return string
         */
        public function getEmail()
        {
            return $this->_email;
        }

        /**
         * Set the client's email address
         *
         * @param string
         */
        public function setEmail($email)
        {
            $this->_email = $email;
        }

        /**
         * Get the client's telephone number
         *
         * @return integer
         */
        public function getTelephone()
        {
            return $this->_telephone;
        }

        /**
         * Set the client's telephone number
         *
         * @param integer
         */
        public function setTelephone($telephone)
        {
            $this->_telephone = $telephone;
        }

        /**
         * Get the client's fax number
         *
         * @return integer
         */
        public function getFax()
        {
            return $this->_fax;
        }

        /**
         * Set the client's fax number
         *
         * @param integer
         */
        public function setFax($fax)
        {
            $this->_fax = $fax;
        }

        /**
         * Adds a user to the client
         *
         * @param User $user
         */
        public function addMember(User $user)
        {
            if (!$user->getID()) throw new Exception('Cannot add user object to client until the object is saved');

            tables\ClientMembers::getTable()->addUserToClient($user->getID(), $this->getID());

            if (is_array($this->_members))
                $this->_members[$user->getID()] = $user->getID();
        }

        public function getMembers()
        {
            if ($this->_members === null) {
                $this->_members = [];
                foreach (tables\ClientMembers::getTable()->getUIDsForClientID($this->getID()) as $uid) {
                    $this->_members[$uid] = User::getB2DBTable()->selectById($uid);
                }
            }

            return $this->_members;
        }

        public function removeMember(User $user)
        {
            if ($this->_members !== null) {
                unset($this->_members[$user->getID()]);
            }
            if ($this->_num_members !== null) {
                $this->_num_members--;
            }
            tables\ClientMembers::getTable()->removeUserFromClient($user->getID(), [$this->getID()]);
        }

        public function getNumberOfMembers()
        {
            if ($this->_members !== null) {
                return count($this->_members);
            } elseif ($this->_num_members === null) {
                $this->_num_members = tables\ClientMembers::getTable()->getNumberOfMembersByClientID($this->getID());
            }

            return $this->_num_members;
        }

        public function hasAccess()
        {
            return (bool) framework\Context::getUser()->isMemberOfClient($this);
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
         * Returns an array of client dashboards
         *
         * @return Dashboard[]
         */
        public function getDashboards()
        {
            $this->_b2dbLazyLoad('_dashboards');

            return $this->_dashboards;
        }

        /**
         * @return Project[][]
         */
        public function getProjects()
        {
            $projects = Project::getAllByClientID($this->getID());

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

        protected function _preDelete(): void
        {
            tables\ClientMembers::getTable()->removeUsersFromClient($this->getID());
        }

        public function getExternalContact(): ?User
        {
            return $this->_b2dbLazyLoad('_external_contact_user_id');
        }

        /**
         * @param User|null $user
         */
        public function setExternalContact(User $user = null)
        {
            $this->_external_contact_user_id = $user;
        }

        public function getInternalContact(): ?User
        {
            return $this->_b2dbLazyLoad('_internal_contact_user_id');
        }

        public function setInternalContact(User $user = null)
        {
            $this->_internal_contact_user_id = $user;
        }

        public function addPermission($permission_name, $module = 'core', $scope = null, $target_id = 0)
        {
            if ($scope === null) {
                $scope = Context::getScope();
            }

            $permission = new Permission();
            $permission->setClient($this);
            $permission->setModuleName($module);
            $permission->setTargetId($target_id);
            $permission->setScope($scope);
            $permission->setPermissionName($permission_name);
            $permission->save();
        }

        protected function _populatePermissions()
        {
            if ($this->_permissions === null) {
                $this->_permissions = $this->_b2dbLazyload('_permissions');
                $this->_permission_keys = [];

                foreach ($this->_permissions as $permission) {
                    $this->_permission_keys[$permission->getModuleName() . '_' . $permission->getPermissionName() . '_' . $permission->getTargetId()] = ['module' => $permission->getModuleName(), 'permission' => $permission->getPermissionName(), 'target_id' => $permission->getTargetId()];;
                }
            }
        }

        public function hasPermission($permission_name, $target_id = 0, $module = 'core'): bool
        {
            $permissions = $this->getPermissions();

            return array_key_exists($module . '_' . $permission_name . '_' . $target_id, $permissions);
        }

        /**
         * Returns all permissions assigned to this role
         *
         * @return Permission[]
         */
        public function getPermissions(): array
        {
            $this->_populatePermissions();

            return $this->_permission_keys;
        }

        /**
         * Removes permission from the role.
         *
         * @param string $permission_name
         * @param string $module
         */
        public function removePermission($permission_name, $target_id = 0, $module = 'core')
        {
            tables\Permissions::getTable()->removeClientPermission($this->getID(), $permission_name, $module, $target_id);
        }

    }
