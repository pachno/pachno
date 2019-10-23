<?php

    namespace pachno\core\entities\common;

    use pachno\core\entities\Team;
    use pachno\core\entities\User;

    /**
     * Item class for objects with both QA responsible and Leader properties
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Item class for objects with both QA responsible and Leader properties
     *
     * @package pachno
     * @subpackage core
     */
    abstract class QaLeadable extends Releaseable
    {

        /**
         * The lead type for the project, \pachno\core\entities\common\Identifiable::TYPE_USER or \pachno\core\entities\common\Identifiable::TYPE_TEAM
         *
         * @var Team
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Team")
         */
        protected $_leader_team;

        /**
         * The lead for the project
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_leader_user;

        /**
         * The QA responsible for the project, \pachno\core\entities\common\Identifiable::TYPE_USER or \pachno\core\entities\common\Identifiable::TYPE_TEAM
         *
         * @var Team
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Team")
         */
        protected $_qa_responsible_team;

        /**
         * The QA responsible for the project
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_qa_responsible_user;

        public function getLeaderID()
        {
            $leader = $this->getLeader();

            return ($leader instanceof Identifiable) ? $leader->getID() : null;
        }

        public function getLeader()
        {
            $this->_b2dbLazyLoad('_leader_team');
            $this->_b2dbLazyLoad('_leader_user');

            if ($this->_leader_team instanceof Team) {
                return $this->_leader_team;
            } elseif ($this->_leader_user instanceof User) {
                return $this->_leader_user;
            } else {
                return null;
            }
        }

        public function setLeader(Identifiable $leader)
        {
            if ($leader instanceof Team) {
                $this->_leader_user = null;
                $this->_leader_team = $leader;
            } else {
                $this->_leader_team = null;
                $this->_leader_user = $leader;
            }
        }

        public function clearLeader()
        {
            $this->_leader_team = null;
            $this->_leader_user = null;
        }

        public function getQaResponsibleID()
        {
            $qa_responsible = $this->getQaResponsible();

            return ($qa_responsible instanceof Identifiable) ? $qa_responsible->getID() : null;
        }

        public function getQaResponsible()
        {
            if (!empty($this->_qa_responsible_team)) {
                $this->_b2dbLazyLoad('_qa_responsible_team');
            } elseif (!empty($this->_qa_responsible_user)) {
                $this->_b2dbLazyLoad('_qa_responsible_user');
            }

            if ($this->_qa_responsible_team instanceof Team) {
                return $this->_qa_responsible_team;
            } elseif ($this->_qa_responsible_user instanceof User) {
                return $this->_qa_responsible_user;
            } else {
                return null;
            }
        }

        public function setQaResponsible(Identifiable $qa_responsible)
        {
            if ($qa_responsible instanceof Team) {
                $this->_qa_responsible_user = null;
                $this->_qa_responsible_team = $qa_responsible;
            } else {
                $this->_qa_responsible_team = null;
                $this->_qa_responsible_user = $qa_responsible;
            }
        }

        public function clearQaResponsible()
        {
            $this->_qa_responsible_team = null;
            $this->_qa_responsible_user = null;
        }

        public function toJSON($detailed = true)
        {
            $jsonArray = [
                'id' => $this->getID(),
                'leader' => $this->hasLeader() ? $this->getLeader()->toJSON() : null,
                'qa_responsible' => $this->hasQaResponsible() ? $this->getQaResponsible()->toJSON() : null,
                'owner' => $this->hasOwner() ? $this->getOwner()->toJSON() : null
            ];
            if ($detailed) {
                $jsonArray['released'] = $this->isReleased();
                $jsonArray['release_date'] = $this->getReleaseDate();
            }

            return $jsonArray;
        }

        public function hasLeader()
        {
            return (bool)($this->getLeader() instanceof Identifiable);
        }

        public function hasQaResponsible()
        {
            return (bool)($this->getQaResponsible() instanceof Identifiable);
        }

    }
