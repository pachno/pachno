<?php

    namespace pachno\core\entities;

    use b2db\QueryColumnSort;
    use pachno\core\framework\Context;
    use pachno\core\entities\common\Identifiable;
    use pachno\core\entities\tables\Issues;

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
         * @var Identifiable[]
         */
        protected $_identifiables;

        /**
         * @var AgileBoard
         */
        protected $_board;

        /**
         * Milestone
         * @var Milestone
         */
        protected $_milestone;

        /**
         * Cached search object
         * @var SavedSearch
         */
        protected $_search_object;

        protected $_name;

        protected $_identifier;

        protected $_identifier_type;
        
        protected $_identifier_grouping;

        /**
         * @return Identifiable[]
         */
        public function getIdentifiables()
        {
            return $this->_identifiables;
        }

        public function setIdentifiables($identifiables)
        {
            $this->_identifiables = (is_array($identifiables)) ? $identifiables : [$identifiables];
        }

        public function getName()
        {
            if ($this->_name === null) {
                $names = [];
                foreach ($this->_identifiables as $identifiable) {
                    $names[] = ($identifiable instanceof Identifiable) ? $identifiable->getName() : Context::getI18n()->__('Unknown / not set');
                }
                $this->_name = join(', ', $names);
            }

            return $this->_name;
        }

        public function hasIdentifiables()
        {
            foreach ($this->_identifiables as $identifiable) {
                if ($identifiable instanceof Identifiable) return true;
            }

            return false;
        }

        public function hasIssue(Issue $issue)
        {
            switch ($this->getIdentifierType()) {
                case AgileBoard::SWIMLANES_ISSUES:
                    $parent_issue_count = count($issue->getParentIssues());

                    if (!$this->getIdentifierIssue() instanceof Issue) {
                        return $parent_issue_count === 0;
                    }

                    if (!$parent_issue_count) {
                        return false;
                    }

                    foreach ($issue->getParentIssues() as $parent_issue) {
                        if ($parent_issue->getID() === $this->getIdentifierIssue()->getID()) {
                            return true;
                        }
                    }

                    return false;
                    break;
                case AgileBoard::SWIMLANES_GROUPING:
                case AgileBoard::SWIMLANES_EXPEDITE:
                    if (!$this->hasIdentifiables()) {
                        if ($this->getIdentifierGrouping() == 'priority') {
                            return !$issue->getPriority() instanceof Priority;
                        } elseif ($this->getIdentifierGrouping() == 'category') {
                            return !$issue->getCategory() instanceof Category;
                        } elseif ($this->getIdentifierGrouping() == 'severity') {
                            return !$issue->getSeverity() instanceof Severity;
                        }
                    }

                    $issue_identifiable = null;
                    if ($this->getIdentifierGrouping() == 'priority') {
                        $issue_identifiable = $issue->getPriority();
                    } elseif ($this->getIdentifierGrouping() == 'category') {
                        $issue_identifiable = $issue->getCategory();
                    } elseif ($this->getIdentifierGrouping() == 'severity') {
                        $issue_identifiable = $issue->getSeverity();
                    }

                    if (!$issue_identifiable instanceof Datatype) {
                        return false;
                    }

                    foreach ($this->getIdentifiables() as $identifiable) {
                        if ($issue_identifiable->getID() === $identifiable->getId()) {
                            return true;
                        }
                    }

                    return false;
                    break;
            }
        }

        /**
         * @param int $column_id
         * @return Issue[]
         */
        public function getIssues($column_id = null)
        {
            if (!$this->getBoard()->usesSwimlanes() || in_array($this->getBoard()->getSwimlaneType(), [AgileBoard::SWIMLANES_EXPEDITE, AgileBoard::SWIMLANES_GROUPING])) {
                $this->_setupSearchObject($column_id);

                return $this->_search_object->getIssues();
            } else {
                if ($this->getIdentifierIssue() instanceof Issue) {
                    return $this->getIdentifierIssue()->getChildIssues();
                } else {
                    $this->_setupSearchObject($column_id);

                    return $this->_search_object->getIssues();
                }
            }
        }

        public function getBoard()
        {
            return $this->_board;
        }

        public function setBoard(AgileBoard $board)
        {
            $this->_board = $board;
        }

        protected function _setupSearchObject($column_id = null)
        {
            if ($this->_search_object === null) {
                $this->_search_object = new SavedSearch();
                $this->_search_object->setFilter('project_id', SearchFilter::createFilter('project_id', ['o' => '=', 'v' => $this->getBoard()->getProject()->getID()]));
                $this->_search_object->setFilter('milestone', SearchFilter::createFilter('milestone', ['o' => '=', 'v' => ($this->getMilestone() instanceof Milestone) ? $this->getMilestone()->getID() : 0]));
                if (!$this->getMilestone() instanceof Milestone) {
                    $this->_search_object->setFilter('state', SearchFilter::createFilter('state', ['o' => '=', 'v' => Issue::STATE_OPEN]));
                }

                $this->_search_object->setFilter('state', SearchFilter::createFilter('state', ['o' => '=', 'v' => [Issue::STATE_CLOSED, Issue::STATE_OPEN]]));
                $this->_search_object->setFilter('deleted', SearchFilter::createFilter('deleted', ['o' => '=', 'v' => false]));
//                $this->_search_object->setFilter('archived', SearchFilter::createFilter('archived', ['o' => '=', 'v' => false]));
                $this->_search_object->setFilter('issuetype', SearchFilter::createFilter('issuetype', ['o' => '!=', 'v' => $this->getBoard()->getEpicIssuetypeID()]));
                if ($column_id === null) {
                    $this->_search_object->setFilter('status', SearchFilter::createFilter('status', ['o' => '=', 'v' => $this->getBoard()->getStatusIds()]));
                } else {
                    foreach ($this->getBoard()->getColumns() as $boardColumn) {
                        if ($boardColumn->getID() == $column_id) {
                            $this->_search_object->setFilter('status', SearchFilter::createFilter('status', ['o' => '=', 'v' => $boardColumn->getStatusIds()]));
                            break;
                        }
                    }
                }
                if ($this->getBoard()->usesSwimlanes() && $this->getBoard()->getSwimlaneType() == AgileBoard::SWIMLANES_ISSUES) {
                    $values = [];
                    foreach ($this->getBoard()->getMilestoneSwimlanes($this->getMilestone()) as $swimlane) {
                        if ($swimlane->getIdentifier() == $this->getIdentifier()) continue;
                        $values[] = $swimlane->getIdentifierIssue()->getID();
                        foreach ($swimlane->getIssues($column_id) as $issue) $values[] = $issue->getID();
                    }
                    $this->_search_object->setFilter('id', SearchFilter::createFilter('id', ['o' => '!=', 'v' => $values]));
                } else {
                    if ($this->getBoard()->usesSwimlanes()) {
                        $values = [];
                        foreach ($this->_identifiables as $identifiable) $values[] = ($identifiable instanceof Identifiable) ? $identifiable->getID() : $identifiable;
                        $this->_search_object->setFilter($this->getBoard()->getSwimlaneIdentifier(), SearchFilter::createFilter($this->getBoard()->getSwimlaneIdentifier(), ['o' => '=', 'v' => $values]));
                    }
                }
                $this->_search_object->setIssuesPerPage(500);
                $this->_search_object->setOffset(0);
                $this->_search_object->setSortFields([Issues::MILESTONE_ORDER => QueryColumnSort::SORT_ASC]);
                $this->_search_object->setGroupby(null);
            }
        }

        /**
         * @return Milestone
         */
        public function getMilestone()
        {
            return $this->_milestone;
        }

        public function setMilestone(Milestone $milestone = null)
        {
            $this->_milestone = $milestone;
        }

        public function getIdentifierType()
        {
            return $this->_identifier_type;
        }

        public function setIdentifierType($identifier_type)
        {
            $this->_identifier_type = $identifier_type;
        }

        public function getIdentifierGrouping()
        {
            return $this->_identifier_grouping;
        }

        public function setIdentifierGrouping($identifier_grouping)
        {
            $this->_identifier_grouping = $identifier_grouping;
        }

        public function getIdentifier()
        {
            if ($this->_identifier === null) {
                $identifiers = [];
                foreach ($this->_identifiables as $identifiable) {
                    $identifiers[] = ($identifiable instanceof Identifiable) ? $identifiable->getId() : $identifiable;
                }
                $this->_identifier = 'swimlane_' . implode('_', $identifiers);
            }

            return $this->_identifier;
        }

        /**
         * @return Issue
         */
        public function getIdentifierIssue()
        {
            return reset($this->_identifiables);
        }

        public function toJSON($column_id)
        {
            $json = [
                'name' => $this->getName(),
                'has_identifiables' => $this->hasIdentifiables(),
                'identifier' => $this->getIdentifier(),
                'identifier_type' => $this->_identifier_type,
                'identifier_grouping' => $this->_identifier_grouping,
                'identifiables' => array_map(fn($identifiable) => ($identifiable instanceof Identifiable) ? $identifiable->toJSON(false) : 0, $this->getIdentifiables()),
                'identifier_issue' => ($this->getIdentifierIssue() instanceof Issue) ? $this->getIdentifierIssue()->toJSON(false) : null,
                'issues' => []
            ];
            if (is_array($json['identifiables'])) {
                $json['identifiables'] = array_values($json['identifiables']);
            }

            foreach ($this->getIssues($column_id) as $issue) {
                $json['issues'][] = $issue->toJSON(false);
            }

            return $json;
        }

    }
