<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\framework\Settings;

    /**
     * User state class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * User state class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\UserStates")
     */
    class Userstate extends IdentifiableScoped
    {

        static $_userstates = null;

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
        protected $_is_online = false;

        /**
         * @Column(type="boolean")
         */
        protected $_is_unavailable = false;

        /**
         * @Column(type="boolean")
         */
        protected $_is_busy = false;

        /**
         * @Column(type="boolean")
         */
        protected $_is_in_meeting = false;

        /**
         * @Column(type="boolean")
         */
        protected $_is_absent = false;

        public static function loadFixtures(Scope $scope)
        {
            $available = new Userstate();
            $available->setIsOnline();
            $available->setName('Available');
            $available->save();

            $offline = new Userstate();
            $offline->setIsUnavailable();
            $offline->setName('Offline');
            $offline->save();

            $busy = new Userstate();
            $busy->setIsUnavailable();
            $busy->setIsOnline();
            $busy->setName('Busy');
            $busy->save();

            $unavailable = new Userstate();
            $unavailable->setIsUnavailable();
            $unavailable->setIsOnline();
            $unavailable->setName('Unavailable');
            $unavailable->save();

            $in_a_meeting = new Userstate();
            $in_a_meeting->setIsUnavailable();
            $in_a_meeting->setIsInMeeting();
            $in_a_meeting->setName('In a meeting');
            $in_a_meeting->save();

            $coding = new Userstate();
            $coding->setIsUnavailable();
            $coding->setIsBusy();
            $coding->setIsOnline();
            $coding->setName('Coding');
            $coding->save();

            $coffee = new Userstate();
            $coffee->setIsUnavailable();
            $coffee->setIsBusy();
            $coffee->setIsOnline();
            $coffee->setName('On coffee break');

            $away = new Userstate();
            $away->setIsUnavailable();
            $away->setIsOnline();
            $away->setIsBusy();
            $away->setIsAbsent();
            $away->setName('Away');
            $away->save();

            $vacation = new Userstate();
            $vacation->setIsUnavailable();
            $vacation->setIsBusy();
            $vacation->setIsAbsent();
            $vacation->setName('On vacation');
            $vacation->save();

            Settings::saveSetting(Settings::SETTING_ONLINESTATE, $available->getID(), 'core', $scope->getID());
            Settings::saveSetting(Settings::SETTING_OFFLINESTATE, $offline->getID(), 'core', $scope->getID());
            Settings::saveSetting(Settings::SETTING_AWAYSTATE, $away->getID(), 'core', $scope->getID());
        }

        public function setIsOnline($val = true)
        {
            $this->_is_online = $val;
        }

        public function setIsUnavailable($val = true)
        {
            $this->_is_unavailable = $val;
        }

        public function setIsInMeeting($val = true)
        {
            $this->_is_in_meeting = $val;
        }

        public function setIsBusy($val = true)
        {
            $this->_is_busy = $val;
        }

        public function setIsAbsent($val = true)
        {
            $this->_is_absent = $val;
        }

        public function toJSON($detailed = true)
        {
            return [
                'name' => $this->getName(),
                'is_absent' => $this->isAbsent(),
                'is_busy' => $this->isBusy(),
                'is_in_meeting' => $this->isInMeeting(),
                'is_online' => $this->isOnline(),
                'is_unavalable' => $this->isUnavailable()
            ];
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

        public function isAbsent()
        {
            return $this->_is_absent;
        }

        public function isBusy()
        {
            return $this->_is_busy;
        }

        public function isInMeeting()
        {
            return $this->_is_in_meeting;
        }

        public function isOnline()
        {
            return $this->_is_online;
        }

        public function isUnavailable()
        {
            return $this->_is_unavailable;
        }

    }
