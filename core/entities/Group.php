<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\common\Permissible;
    use pachno\core\entities\traits\PermissionContainer;
    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;

    /**
     * Group class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Group class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\Groups")
     */
    class Group extends IdentifiableScoped implements Permissible
    {

        protected static $_groups = null;

        protected $_members = null;

        protected $_num_members = null;

        /**
         * @var Permission[]
         * @Relates(class="\pachno\core\entities\Permission", collection=true, foreign_column="gid")
         */
        protected $_permissions = null;

        protected $_permission_keys;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        public static function doesGroupNameExist($group_name)
        {
            return tables\Groups::getTable()->doesGroupNameExist($group_name);
        }

        public static function getAll()
        {
            if (self::$_groups === null) {
                self::$_groups = tables\Groups::getTable()->getAll();
            }

            return self::$_groups;
        }

        public static function loadFixtures(Scope $scope)
        {
            $scope_id = $scope->getID();

            $admin_group = new Group();
            $admin_group->setName('Administrators');
            $admin_group->setScope($scope);
            $admin_group->save();
            Settings::saveSetting('admingroup', $admin_group->getID(), 'core', $scope_id);

            $user_group = new Group();
            $user_group->setName('Regular users');
            $user_group->setScope($scope);
            $user_group->save();
            Settings::saveSetting('defaultgroup', $user_group->getID(), 'core', $scope_id);

            $guest_group = new Group();
            $guest_group->setName('Guests');
            $guest_group->setScope($scope);
            $guest_group->save();

            // Set up initial users, and their permissions
            if ($scope->isDefault()) {
                list($guestuser_id, $adminuser_id) = User::loadFixtures($scope, $admin_group, $user_group, $guest_group);
                tables\UserScopes::getTable()->addUserToScope($guestuser_id, $scope->getID(), $guest_group->getID(), true);
                tables\UserScopes::getTable()->addUserToScope($adminuser_id, $scope->getID(), $admin_group->getID(), true);
            } else {
//                $default_scope_id = Settings::getDefaultScopeID();
//                $default_user_id = (int) Settings::get(Settings::SETTING_DEFAULT_USER_ID, 'core', $default_scope_id);
                tables\UserScopes::getTable()->addUserToScope(2, $scope->getID(), $user_group->getID(), true);
                tables\UserScopes::getTable()->addUserToScope(1, $scope->getID(), $admin_group->getID());
                Settings::saveSetting(Settings::SETTING_DEFAULT_USER_ID, 2, 'core', $scope->getID());
            }

            Permission::loadFixtures($scope, $user_group, $admin_group, $guest_group);
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

        public function isDefaultUserGroup()
        {
            return (bool) (Settings::getDefaultGroup()->getID() == $this->getID());
        }

        /**
         * Return an array of all members in this group
         *
         * @return array
         */
        public function getMembers()
        {
            if ($this->_members === null) {
                $this->_members = tables\UserScopes::getTable()->getUsersByGroupID($this->getID());
            }

            return $this->_members;
        }

        public function getNumberOfMembers()
        {
            if ($this->_members !== null) {
                return count($this->_members);
            } elseif ($this->_num_members === null) {
                $this->_num_members = tables\UserScopes::getTable()->countUsersByGroupID($this->getID());
            }

            return $this->_num_members;
        }

        public function removeMember(User $user)
        {
            if ($this->_members !== null) {
                unset($this->_members[$user->getID()]);
            }
            if ($this->_num_members !== null) {
                $this->_num_members--;
            }
        }

        protected function _postSave(bool $is_new): void
        {
            if ($is_new) {
                if (self::$_groups !== null) {
                    self::$_groups[$this->getID()] = $this;
                }
            }
        }

        protected function _preDelete(): void
        {
            tables\UserScopes::getTable()->clearUserGroups($this->getID());
        }

        public function addPermission($permission_name, $module = 'core', $scope = null, $target_id = 0)
        {
            if ($scope === null) {
                $scope = Context::getScope();
            }

            $permission = new Permission();
            $permission->setGroup($this);
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
                    $this->_permission_keys[$permission->getModuleName() . '_' . $permission->getPermissionName() . '_' . $permission->getTargetId()] = true;
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
            tables\Permissions::getTable()->removeGroupPermission($this->getID(), $permission_name, $module, $target_id);
        }

    }
