<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Application password class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Application password class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\ApplicationPasswords")
     */
    class ApplicationPassword extends IdentifiableScoped
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Hashed password
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_password = '';

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_last_used_at;

        /**
         * Who the notification is for
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * Creates the token from the one-time-use application password for subsequent authentication.
         *
         * @param string $application_password
         *
         * @return string A SHA-256 Hash of the password
         */
        public static function createToken($application_password)
        {
            return password_hash($application_password, PASSWORD_DEFAULT);
        }

        /**
         * Return the application password name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the application password name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Returns a hash of the user password
         *
         * @return string
         * @see \pachno\core\entities\ApplicationPassword::getHashPassword
         */
        public function getPassword()
        {
            return $this->getHashPassword();
        }

        /**
         * Set a new application password. Generates token & crypts the token.
         *
         * @param string $newpassword
         */
        public function setPassword($newpassword)
        {
            $this->_password = password_hash($newpassword, PASSWORD_DEFAULT);
        }

        /**
         * Returns a hash of the user password
         *
         * @return string
         */
        public function getHashPassword()
        {
            return $this->_password;
        }

        public function getCreatedAt()
        {
            return $this->_created_at;
        }

        public function setCreatedAt($created_at)
        {
            $this->_created_at = $created_at;
        }

        public function getLastUsedAt()
        {
            return $this->_last_used_at;
        }

        public function setLastUsedAt($last_used_at)
        {
            $this->_last_used_at = $last_used_at;
        }

        public function isUsed()
        {
            return (bool)$this->_last_used_at;
        }

        public function verify()
        {
            $password = User::createPassword(30);
            $this->_password = password_hash($password, PASSWORD_DEFAULT);
            $this->useOnce();

            return $password;
        }

        public function useOnce()
        {
            $this->_last_used_at = time();
        }

        public function getUser()
        {
            return $this->_b2dbLazyLoad('_user_id');
        }

        public function setUser($uid)
        {
            $this->_user_id = $uid;
        }

        protected function _preSave(bool $is_new): void
        {
            parent::_preSave($is_new);
            if ($is_new) {
                $this->_created_at = NOW;
            }
        }

    }
