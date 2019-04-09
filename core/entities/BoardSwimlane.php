<?php

    namespace pachno\core\entities;

    use pachno\core\entities\AgileBoard;
    use pachno\core\framework;

    /**
     * Agile board swimlane class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage agile
     */

    /**
     * Agile board swimlane class
     *
     * @package pachno
     * @subpackage agile
     */
    class BoardSwimlane
    {

        /**
         * The identifiable objects for this swimlane
         *
         * @var array|\pachno\core\entities\common\Identifiable
         */
        protected $_identifiables;

        /**
         * @var \pachno\core\entities\AgileBoard
         */
        protected $_board;

        /**
         * Milestone
         * @var \pachno\core\entities\Milestone
         */
        protected $_milestone;

        /**
         * Cached search object
         * @var \pachno\core\entities\SavedSearch
         */
        protected $_search_object;

        protected $_name;

        protected $_identifier;

        public function getIdentifiables()
        {
            return $this->_identifiables;
        }

        public function getBoard()
        {
            return $this->_board;
        }

        public function setIdentifiables($identifiables)
        {
            $this->_identifiables = (is_array($identifiables)) ? $identifiables : array($identifiables);
        }

        public function setBoard(AgileBoard $board)
        {
            $this->_board = $board;
        }

        public function setMilestone(\pachno\core\entities\Milestone $milestone)
        {
            $this->_milestone = $milestone;
        }

        /**
         * @return \pachno\core\entities\Milestone
         */
        public function getMilestone()
        {
            return $this->_milestone;
        }

        public function getName()
        {
            if ($this->_name === null)
            {
                $names = array();
                foreach ($this->_identifiables as $identifiable)
                {
                    $names[] = ($identifiable instanceof \pachno\core\entities\common\Identifiable) ? $identifiable->getName() : \framework\Context::getI18n()->__('Unknown / not set');
                }
                $this->_name = join(', ', $names);
            }

            return $this->_name;
        }

        public function getIdentifier()
        {
            if ($this->_identifier === null)
            {
                $identifiers = array();
                foreach ($this->_identifiables as $identifiable)
                {
                    $identifiers[] = ($identifiable instanceof \pachno\core\entities\common\Identifiable) ? $identifiable->getId() : $identifiable;
                }
                $this->_identifier = 'swimlane_' . join('_', $identifiers);
            }

            return $this->_identifier;
        }

        public function hasIdentifiables()
        {
            foreach ($this->_identifiables as $identifiable)
            {
                if ($identifiable instanceof \pachno\core\entities\common\Identifiable) return true;
            }

            return false;
        }

        protected function _setupSearchObject()
        {
            if ($this->_search_object === null)
            {
                $this->_search_object = new \pachno\core\entities\SavedSearch();
                $this->_search_object->setFilter('project_id', \pachno\core\entities\SearchFilter::createFilter('project_id', array('o' => '=', 'v' => $this->getBoard()->getProject()->getID())));
                $this->_search_object->setFilter('milestone', \pachno\core\entities\SearchFilter::createFilter('milestone', array('o' => '=', 'v' => $this->getMilestone()->getID())));
                $this->_search_object->setFilter('state', \pachno\core\entities\SearchFilter::createFilter('state', array('o' => '=', 'v' => array(\pachno\core\entities\Issue::STATE_CLOSED, \pachno\core\entities\Issue::STATE_OPEN))));
                $this->_search_object->setFilter('issuetype', \pachno\core\entities\SearchFilter::createFilter('issuetype', array('o' => '!=', 'v' => $this->getBoard()->getEpicIssuetypeID())));
                if ($this->getBoard()->usesSwimlanes() && $this->getBoard()->getSwimlaneType() == AgileBoard::SWIMLANES_ISSUES)
                {
                    $values = array();
                    foreach ($this->getBoard()->getMilestoneSwimlanes($this->getMilestone()) as $swimlane)
                    {
                        if ($swimlane->getIdentifier() == $this->getIdentifier()) continue;
                        $values[] = $swimlane->getIdentifierIssue()->getID();
                        foreach ($swimlane->getIssues() as $issue) $values[] = $issue->getID();
                    }
                    $this->_search_object->setFilter('id', \pachno\core\entities\SearchFilter::createFilter('id', array('o' => '!=', 'v' => $values)));
                }
                else
                {
                    if ($this->getBoard()->usesSwimlanes())
                    {
                        $values = array();
                        foreach ($this->_identifiables as $identifiable) $values[] = ($identifiable instanceof \pachno\core\entities\common\Identifiable) ? $identifiable->getID() : $identifiable;
                        $this->_search_object->setFilter($this->getBoard()->getSwimlaneIdentifier(), \pachno\core\entities\SearchFilter::createFilter($this->getBoard()->getSwimlaneIdentifier(), array('o' => '=', 'v' => $values)));
                    }
                }
                $this->_search_object->setIssuesPerPage(0);
                $this->_search_object->setOffset(0);
                $this->_search_object->setSortFields(array(\pachno\core\entities\tables\Issues::MILESTONE_ORDER => \b2db\QueryColumnSort::SORT_ASC));
                $this->_search_object->setGroupby(null);
            }
        }

        public function getIssues()
        {
            if (!$this->getBoard()->usesSwimlanes() || in_array($this->getBoard()->getSwimlaneType(), array(AgileBoard::SWIMLANES_EXPEDITE, AgileBoard::SWIMLANES_GROUPING)))
            {
                $this->_setupSearchObject();
                return $this->_search_object->getIssues();
            }
            else
            {
                if ($this->getIdentifierIssue() instanceof \pachno\core\entities\Issue)
                {
                    return $this->getIdentifierIssue()->getChildIssues();
                }
                else
                {
                    $this->_setupSearchObject();
                    return $this->_search_object->getIssues();
                }
            }
        }

        /**
         * @return \pachno\core\entities\Issue
         */
        public function getIdentifierIssue()
        {
            return reset($this->_identifiables);
        }

    }
