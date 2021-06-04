<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\Settings;
    use pachno\core\framework\Context;

    /**
     * Setting entity class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Setting entity class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Settings")
     */
    class Setting extends IdentifiableScoped
    {

        /**
         * The name of the setting
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_updated_at;

        /**
         * @var User
         * @Column(type="integer", length=10, name="uid")
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * The setting module
         *
         * @var string
         * @Column(type="string", length=45, name="module")
         */
        protected $_module_key;

        /**
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_value;

        /**
         * @return string
         */
        public function getName(): string
        {
            return $this->_name;
        }

        /**
         * @param string $name
         */
        public function setName(string $name)
        {
            $this->_name = $name;
        }

        /**
         * @return mixed
         */
        public function getUpdatedAt()
        {
            return $this->_updated_at;
        }

        /**
         * @param mixed $updated
         */
        public function setUpdatedAt($updated)
        {
            $this->_updated_at = $updated;
        }

        /**
         * @return int
         */
        public function getUserId(): int
        {
            $user = $this->getUser();

            return ($user instanceof User) ? $user->getID() : (int)$user;
        }

        /**
         * @param int $user_id
         */
        public function setUserId(int $user_id)
        {
            $this->_user_id = $user_id;
        }

        /**
         * @return User
         */
        public function getUser(): ?User
        {
            return $this->_b2dbLazyload('_user_id');
        }

        /**
         * @return string
         */
        public function getModuleKey(): string
        {
            return $this->_module_key;
        }

        /**
         * @param string $module_key
         */
        public function setModuleKey(string $module_key)
        {
            $this->_module_key = $module_key;
        }

        public function getModule(): ?Module
        {
            return ($this->_module_key !== 'core') ? Context::getModule($this->_module_key) : null;
        }

        /**
         * @return string
         */
        public function getValue()
        {
            return (is_numeric($this->_value)) ? (int)$this->_value : $this->_value;
        }

        /**
         * @param string $value
         */
        public function setValue(string $value)
        {
            $this->_value = $value;
        }

        /**
         * @return int
         */
        public function getScopeId()
        {
            return ($this->getScope() instanceof Scope) ? $this->getScope()->getID() : $this->_scope;
        }

        protected function _preSave(bool $is_new = false): void
        {
            $this->_updated_at = time();

            Settings::getTable()->preventDuplicate($this);
        }

    }
