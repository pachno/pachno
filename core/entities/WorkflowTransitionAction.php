<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\common\Timeable;
    use pachno\core\entities\tables\ListTypes;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\entities\tables\Teams;
    use pachno\core\entities\tables\Users;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use pachno\core\framework\Request;

    /**
     * Workflow transition action class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Workflow transition action class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\WorkflowTransitionActions")
     */
    class WorkflowTransitionAction extends IdentifiableScoped
    {

        public const ACTION_ASSIGN_ISSUE_SELF = 'assign_self';

        public const ACTION_ASSIGN_ISSUE = 'assign_user';

        public const ACTION_CLEAR_ASSIGNEE = 'clear_assignee';

        public const ACTION_SET_DUPLICATE = 'set_duplicate';

        public const ACTION_CLEAR_DUPLICATE = 'clear_duplicate';

        public const ACTION_SET_RESOLUTION = 'set_resolution';

        public const ACTION_CLEAR_RESOLUTION = 'clear_resolution';

        public const ACTION_SET_STATUS = 'set_status';

        public const ACTION_SET_MILESTONE = 'set_milestone';

        public const ACTION_CLEAR_MILESTONE = 'clear_milestone';

        public const ACTION_SET_PRIORITY = 'set_priority';

        public const ACTION_CLEAR_PRIORITY = 'clear_priority';

        public const ACTION_SET_CATEGORY = 'set_category';

        public const ACTION_CLEAR_CATEGORY = 'clear_category';

        public const ACTION_SET_SEVERITY = 'set_severity';

        public const ACTION_CLEAR_SEVERITY = 'clear_severity';

        public const ACTION_SET_PERCENT = 'set_percent';

        public const ACTION_CLEAR_PERCENT = 'clear_percent';

        public const ACTION_SET_REPRODUCABILITY = 'set_reproducability';

        public const ACTION_CLEAR_REPRODUCABILITY = 'clear_reproducability';

        public const ACTION_USER_START_WORKING = 'user_start_working';

        public const ACTION_USER_STOP_WORKING = 'user_stop_working';

        public const CUSTOMFIELD_CLEAR_PREFIX = 'customfield_clear_';

        public const CUSTOMFIELD_SET_PREFIX = 'customfield_set_';

        protected static $_available_actions = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_action_type;

        /**
         * @Column(type="string", length=200)
         */
        protected $_target_value = null;

        /**
         * The connected transition
         *
         * @var WorkflowTransition
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowTransition")
         */
        protected $_transition_id = null;

        /**
         * The associated workflow object
         *
         * @var Workflow
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Workflow")
         */
        protected $_workflow_id = null;

        public static function getAvailableTransitionActions($key)
        {
            self::_populateAvailableActions();

            return self::$_available_actions[$key];
        }

        protected static function _populateAvailableActions()
        {
            if (self::$_available_actions === null) {
                $initial_list = ['special' => [], 'set' => [], 'clear' => []];
                $i18n = framework\Context::getI18n();
                $initial_list['special'][self::ACTION_ASSIGN_ISSUE] = $i18n->__('Assign the issue to a user');
                $initial_list['special'][self::ACTION_ASSIGN_ISSUE_SELF] = $i18n->__('Assign the issue to the current user');
                $initial_list['special'][self::ACTION_CLEAR_DUPLICATE] = $i18n->__('Mark as not duplicate');
                $initial_list['special'][self::ACTION_SET_DUPLICATE] = $i18n->__('Possibly mark as duplicate');
                $initial_list['special'][self::ACTION_USER_START_WORKING] = $i18n->__('Start logging time');
                $initial_list['special'][self::ACTION_USER_STOP_WORKING] = $i18n->__('Stop logging time and optionally add time spent');
                $initial_list['clear'][self::ACTION_CLEAR_ASSIGNEE] = $i18n->__('Clear issue assignee');
                $initial_list['clear'][self::ACTION_CLEAR_PRIORITY] = $i18n->__('Clear issue priority');
                $initial_list['clear'][self::ACTION_CLEAR_SEVERITY] = $i18n->__('Clear issue severity');
                $initial_list['clear'][self::ACTION_CLEAR_CATEGORY] = $i18n->__('Clear issue category');
                $initial_list['clear'][self::ACTION_CLEAR_PERCENT] = $i18n->__('Clear issue percent');
                $initial_list['clear'][self::ACTION_CLEAR_REPRODUCABILITY] = $i18n->__('Clear issue reproducability');
                $initial_list['clear'][self::ACTION_CLEAR_RESOLUTION] = $i18n->__('Clear issue resolution');
                $initial_list['clear'][self::ACTION_CLEAR_MILESTONE] = $i18n->__('Clear issue milestone');
                $initial_list['set'][self::ACTION_SET_PRIORITY] = $i18n->__('Set issue priority');
                $initial_list['set'][self::ACTION_SET_SEVERITY] = $i18n->__('Set issue severity');
                $initial_list['set'][self::ACTION_SET_CATEGORY] = $i18n->__('Set issue category');
                $initial_list['set'][self::ACTION_SET_PERCENT] = $i18n->__('Set issue percent');
                $initial_list['set'][self::ACTION_SET_REPRODUCABILITY] = $i18n->__('Set issue reproducability');
                $initial_list['set'][self::ACTION_SET_RESOLUTION] = $i18n->__('Set issue resolution');
                $initial_list['set'][self::ACTION_SET_STATUS] = $i18n->__('Set issue status');
                $initial_list['set'][self::ACTION_SET_MILESTONE] = $i18n->__('Set issue milestone');
                foreach (CustomDatatype::getAll() as $key => $details) {
                    $initial_list['clear'][self::CUSTOMFIELD_CLEAR_PREFIX . $key] = $i18n->__('Clear issue field %key', ['%key' => $key]);
                    $initial_list['set'][self::CUSTOMFIELD_SET_PREFIX . $key] = $i18n->__('Set issue field %key', ['%key' => $key]);
                }

                $event = new Event('core', 'WorkflowTransitionAction::getAvailableTransitionActions', null, [], $initial_list);
                $event->trigger();

                self::$_available_actions = $event->getReturnList();
            }
        }

        public static function getByTransitionID($transition_id)
        {
            $actions = [];
            if ($actions_array = tables\WorkflowTransitionActions::getTable()->getByTransitionID($transition_id)) {
                foreach ($actions_array as $action) {
                    $actions[$action->getActionType()] = $action;
                }
            }

            return $actions;
        }

        /**
         * Return the workflow
         *
         * @return Workflow
         */
        public function getWorkflow()
        {
            return $this->_b2dbLazyLoad('_workflow_id');
        }

        public function setWorkflow(Workflow $workflow)
        {
            $this->_workflow_id = $workflow;
        }

        public function setTransition(WorkflowTransition $transition)
        {
            $this->_transition_id = $transition;
        }

        public function getTransition()
        {
            return $this->_b2dbLazyLoad('_transition_id');
        }

        public function hasTargetValue()
        {
            return (bool)$this->_target_value;
        }

        public function getDescription()
        {
            switch ($this->_action_type) {
                case self::ACTION_ASSIGN_ISSUE_SELF:
                    return __('Assign the issue to the current user');
                case self::ACTION_SET_MILESTONE:
                    return __('Set milestone to milestone provided by user');
                case self::ACTION_CLEAR_ASSIGNEE:
                    return __('Clear issue assignee');
                case self::ACTION_CLEAR_PRIORITY:
                    return __('Clear issue priority');
                case self::ACTION_CLEAR_SEVERITY:
                    return __('Clear issue severity');
                case self::ACTION_CLEAR_CATEGORY:
                    return __('Clear issue category');
                case self::ACTION_CLEAR_PERCENT:
                    return __('Clear issue percent completed');
                case self::ACTION_CLEAR_REPRODUCABILITY:
                    return __('Clear issue reproducability');
                case self::ACTION_CLEAR_RESOLUTION:
                    return __('Clear issue resolution');
                case self::ACTION_CLEAR_MILESTONE:
                    return __('Clear issue milestone');
                case self::ACTION_USER_START_WORKING:
                    return __('Start logging time');
                case self::ACTION_USER_STOP_WORKING:
                    return __('Stop logging time and optionally add time spent');
                case self::ACTION_SET_DUPLICATE:
                    return __('Mark issue as duplicate of another, existing issue');
                case self::ACTION_CLEAR_DUPLICATE:
                    return __('Mark issue as unique (no longer a duplicate) issue');
                case $this->isCustomClearAction():
                    return __('Clear issue field %key', ['%key' => $this->getCustomActionType()]);

            }
        }

        public function isCustomClearAction($only_prefix = false)
        {
            return $this->isCustomAction($only_prefix, self::CUSTOMFIELD_CLEAR_PREFIX);
        }

        public function isCustomAction($only_prefix = false, $prefixes = [])
        {
            $prefixes = count((array)$prefixes)
                ? (array)$prefixes
                : [self::CUSTOMFIELD_CLEAR_PREFIX, self::CUSTOMFIELD_SET_PREFIX];

            foreach ($prefixes as $prefix) {
                if (substr(
                        $this->_action_type,
                        0,
                        strlen($prefix)
                    ) == $prefix) {
                    return $only_prefix ? $prefix : true;
                }
            }

            return $only_prefix ? null : false;
        }

        public function getCustomActionType()
        {
            $prefix = $this->isCustomAction(true);

            if (is_null($prefix)) return null;

            return substr($this->_action_type, strlen($prefix));
        }

        public function hasEdit()
        {
            switch ($this->_action_type) {
                case self::ACTION_ASSIGN_ISSUE_SELF:
                case self::ACTION_CLEAR_ASSIGNEE:
                case self::ACTION_CLEAR_PRIORITY:
                case self::ACTION_CLEAR_SEVERITY:
                case self::ACTION_CLEAR_CATEGORY:
                case self::ACTION_CLEAR_PERCENT:
                case self::ACTION_CLEAR_REPRODUCABILITY:
                case self::ACTION_CLEAR_RESOLUTION:
                case self::ACTION_CLEAR_MILESTONE:
                case self::ACTION_CLEAR_DUPLICATE:
                case self::ACTION_USER_START_WORKING:
                case self::ACTION_USER_STOP_WORKING:
                case self::ACTION_SET_MILESTONE:
                case self::ACTION_SET_DUPLICATE:
                case self::CUSTOMFIELD_CLEAR_PREFIX . $this->getCustomActionType():
                    return false;
                default:
                    return true;
            }
        }

        public function perform(Issue $issue, $request = null)
        {
            switch ($this->_action_type) {
                case self::ACTION_ASSIGN_ISSUE_SELF:
                    $issue->setAssignee(framework\Context::getUser());
                    break;
                case self::ACTION_SET_STATUS:
                    if ($this->getTargetValue()) {
                        $issue->setStatus(ListTypes::getTable()->selectById((int) $this->getTargetValue()));
                    } else {
                        $issue->setStatus($request['status_id']);
                    }
                    break;
                case self::ACTION_CLEAR_MILESTONE:
                    $issue->setMilestone(null);
                    break;
                case self::ACTION_SET_MILESTONE:
                    if ($this->getTargetValue()) {
                        $issue->setMilestone(Milestones::getTable()->selectById((int) $this->getTargetValue()));
                    } else {
                        $issue->setMilestone($request['milestone_id']);
                    }
                    break;
                case self::ACTION_CLEAR_PRIORITY:
                    $issue->setPriority(null);
                    break;
                case self::ACTION_SET_PRIORITY:
                    if ($this->getTargetValue()) {
                        $issue->setPriority(ListTypes::getTable()->selectById((int) $this->getTargetValue()));
                    } else {
                        $issue->setPriority($request['priority_id']);
                    }
                    break;
                case self::ACTION_CLEAR_SEVERITY:
                    $issue->setSeverity(null);
                    break;
                case self::ACTION_SET_SEVERITY:
                    if ($this->getTargetValue()) {
                        $issue->setSeverity(ListTypes::getTable()->selectById((int) $this->getTargetValue()));
                    } else {
                        $issue->setSeverity($request['severity_id']);
                    }
                    break;
                case self::ACTION_CLEAR_CATEGORY:
                    $issue->setCategory(null);
                    break;
                case self::ACTION_SET_CATEGORY:
                    if ($this->getTargetValue()) {
                        $issue->setCategory(ListTypes::getTable()->selectById((int) $this->getTargetValue()));
                    } else {
                        $issue->setCategory($request['category_id']);
                    }
                    break;
                case self::ACTION_CLEAR_PERCENT:
                    $issue->setPercentCompleted(0);
                    break;
                case self::ACTION_SET_PERCENT:
                    if ($this->getTargetValue()) {
                        $issue->setPercentCompleted((int) $this->getTargetValue());
                    } else {
                        $issue->setPercentCompleted((int) $request['percent_complete_id']);
                    }
                    break;
                case self::ACTION_CLEAR_DUPLICATE:
                    $issue->setDuplicateOf(null);
                    break;
                case self::ACTION_SET_DUPLICATE:
                    $issue->setDuplicateOf($request['duplicate_issue_id']);
                    break;
                case self::ACTION_CLEAR_RESOLUTION:
                    $issue->setResolution(null);
                    break;
                case self::ACTION_SET_RESOLUTION:
                    if ($this->getTargetValue()) {
                        $issue->setResolution(ListTypes::getTable()->selectById((int) $this->getTargetValue()));
                    } else {
                        $issue->setResolution($request['resolution_id']);
                    }
                    break;
                case self::ACTION_CLEAR_REPRODUCABILITY:
                    $issue->setReproducability(null);
                    break;
                case self::ACTION_SET_REPRODUCABILITY:
                    if ($this->getTargetValue()) {
                        $issue->setReproducability(ListTypes::getTable()->selectById((int) $this->getTargetValue()));
                    } else {
                        $issue->setReproducability($request['reproducability_id']);
                    }
                    break;
                case self::ACTION_CLEAR_ASSIGNEE:
                    $issue->clearAssignee();
                    break;
                case self::ACTION_ASSIGN_ISSUE:
                    if ($this->getTargetValue()) {
                        $target_details = explode('_', $this->_target_value);
                        if ($target_details[0] == 'user') {
                            $assignee = Users::getTable()->selectById((int) $target_details[1]);
                        } else {
                            $assignee = Teams::getTable()->selectById((int) $target_details[1]);
                        }
                        $issue->setAssignee($assignee);
                    } else {
                        $assignee = null;
                        switch ($request['assignee_type']) {
                            case 'user':
                                $assignee = Users::getTable()->selectById((int) $request['assignee_id']);
                                break;
                            case 'team':
                                $assignee = Teams::getTable()->selectById((int) $request['assignee_id']);
                                break;
                        }
                        if ((bool)$request->getParameter('assignee_teamup', false) && $assignee instanceof User && $assignee->getID() != framework\Context::getUser()->getID()) {
                            $team = new Team();
                            $team->setName($assignee->getBuddyname() . ' & ' . framework\Context::getUser()->getBuddyname());
                            $team->setOndemand(true);
                            $team->save();
                            $team->addMember($assignee);
                            $team->addMember(framework\Context::getUser());
                            $assignee = $team;
                        }
                        $issue->setAssignee($assignee);
                    }
                    break;
                case self::ACTION_USER_START_WORKING:
                    $issue->clearUserWorkingOnIssue();
                    $assignee = $issue->getAssignee();
                    if ($assignee instanceof Team && $assignee->isOndemand()) {
                        $members = $assignee->getMembers();
                        $issue->startWorkingOnIssue(array_shift($members));
                    } elseif ($assignee instanceof User) {
                        $issue->startWorkingOnIssue($assignee);
                    }
                    break;
                case self::ACTION_USER_STOP_WORKING:
                    if ($request->getParameter('did', 'nothing') == 'nothing') {
                        $issue->clearUserWorkingOnIssue();
                    } elseif ($request->getParameter('did', 'nothing') == 'this') {
                        $times = [];
                        if ($request['timespent_manual']) {
                            $times = Issue::convertFancyStringToTime($request['timespent_manual'], $issue);
                        } elseif ($request['timespent_specified_type']) {
                            $times = Timeable::getZeroedUnitsWithPoints();
                            $times[$request['timespent_specified_type']] = $request['timespent_specified_value'];
                        }
                        if (array_sum($times) > 0) {
                            $times['hours'] *= 100;
                            $spenttime = new IssueSpentTime();
                            $spenttime->setIssue($issue);
                            $spenttime->setUser(framework\Context::getUser());
                            $spenttime->setSpentPoints($times['points']);
                            $spenttime->setSpentMinutes($times['minutes']);
                            $spenttime->setSpentHours($times['hours']);
                            $spenttime->setSpentDays($times['days']);
                            $spenttime->setSpentWeeks($times['weeks']);
                            $spenttime->setSpentMonths($times['months']);
                            $spenttime->setActivityType($request['timespent_activitytype']);
                            $spenttime->setComment($request['timespent_comment']);
                            $spenttime->save();
                        }
                        $issue->clearUserWorkingOnIssue();
                    } else {
                        $issue->stopWorkingOnIssue(framework\Context::getUser(), $request['timespent_activitytype'], $request['timespent_comment']);
                    }
                    break;
                default:
                    if (strpos($this->_action_type, self::CUSTOMFIELD_CLEAR_PREFIX) === 0) {
                        $customkey = substr($this->_action_type, strlen(self::CUSTOMFIELD_CLEAR_PREFIX));
                        $issue->setCustomField($customkey, null);
                    } elseif (strpos($this->_action_type, self::CUSTOMFIELD_SET_PREFIX) === 0) {
                        $customkey = substr($this->_action_type, strlen(self::CUSTOMFIELD_SET_PREFIX));

                        if ($this->getTargetValue()) {
                            $issue->setCustomField($customkey, $this->getTargetValue());
                        } else {
                            $issue->setCustomField($customkey, $request[$customkey . '_id']);
                        }
                    } else {
                        $event = new Event('core', 'WorkflowTransitionAction::perform', $issue, ['request' => $request]);
                        $event->triggerUntilProcessed();
                    }
            }
        }

        public function getTargetValue()
        {
            return $this->_target_value;
        }

        public function setTargetValue($target_value)
        {
            $this->_target_value = $target_value;
        }

        public function hasValidTarget()
        {
            if (!$this->_target_value) return true;

            switch ($this->_action_type) {
                case self::ACTION_ASSIGN_ISSUE:
                    $target_details = explode('_', $this->_target_value);

                    return (bool)($target_details[0] == 'user') ? User::doesIDExist($target_details[1]) : Team::doesIDExist($target_details[1]);
                    break;
                case self::ACTION_SET_PERCENT:
                    return (bool)($this->_target_value > -1);
                    break;
                case self::ACTION_SET_MILESTONE:
                    return (bool)Milestone::doesIDExist($this->_target_value);
                    break;
                case self::ACTION_SET_PRIORITY:
                    return (bool)Priority::has($this->_target_value);
                    break;
                case self::ACTION_SET_SEVERITY:
                    return (bool)Severity::has($this->_target_value);
                    break;
                case self::ACTION_SET_CATEGORY:
                    return (bool)Category::has($this->_target_value);
                    break;
                case self::ACTION_SET_STATUS:
                    return (bool)Status::has($this->_target_value);
                    break;
                case self::ACTION_SET_REPRODUCABILITY:
                    return (bool)Reproducability::has($this->_target_value);
                    break;
                case self::ACTION_SET_RESOLUTION:
                    return (bool)Resolution::has($this->_target_value);
                    break;
                default:
                    return true;
            }
        }

        public function isCustomSetAction($only_prefix = false)
        {
            return $this->isCustomAction($only_prefix, self::CUSTOMFIELD_SET_PREFIX);
        }

        public function isValid(Request $request)
        {
            if ($this->_target_value) return true;

            switch ($this->_action_type) {
                case self::ACTION_ASSIGN_ISSUE:
                    return (bool)$request['assignee_type'] && $request['assignee_id'];
                    break;
                case self::ACTION_SET_MILESTONE:
                    return (bool)$request->hasParameter('milestone_id');
                    break;
                case self::ACTION_SET_PRIORITY:
                    return (bool)$request->hasParameter('priority_id');
                    break;
                case self::ACTION_SET_SEVERITY:
                    return (bool)$request->hasParameter('severity_id');
                    break;
                case self::ACTION_SET_CATEGORY:
                    return (bool)$request->hasParameter('category_id');
                    break;
                case self::ACTION_SET_STATUS:
                    return (bool)$request->hasParameter('status_id');
                    break;
                case self::ACTION_SET_REPRODUCABILITY:
                    return (bool)$request->hasParameter('reproducability_id');
                    break;
                case self::ACTION_SET_RESOLUTION:
                    return (bool)$request->hasParameter('resolution_id');
                    break;
                default:
                    return true;
            }
        }

        /**
         * @return Datatype[]
         */
        public function getOptions()
        {
            switch ($this->getActionType()) {
                case self::ACTION_SET_STATUS:
                    return Status::getAll();

                case self::ACTION_SET_PRIORITY:
                    return Priority::getAll();

                case self::ACTION_SET_SEVERITY:
                    return Severity::getAll();

                case self::ACTION_SET_CATEGORY:
                    return Category::getAll();

                case self::ACTION_SET_PERCENT:
                    return range(1, 100);

                case self::ACTION_SET_RESOLUTION:
                    return Resolution::getAll();

                case self::ACTION_SET_REPRODUCABILITY:
                    return Reproducability::getAll();

                default:
                    if ($this->isCustomAction()) {
                        if ($this->getCustomField()->getType() != DatatypeBase::CALCULATED_FIELD) {
                            return $this->getCustomField()->getOptions();
                        }
                    }
            }
        }

        public function getActionType()
        {
            return $this->_action_type;
        }

        public function setActionType($action_type)
        {
            $this->_action_type = $action_type;
        }

        /**
         * @return CustomDatatype
         */
        public function getCustomField()
        {
            return CustomDatatype::getByKey($this->getCustomActionType());
        }

        /**
         * @return bool
         */
        public function hasOptions()
        {
            switch ($this->getActionType()) {
                case self::ACTION_SET_STATUS:
                case self::ACTION_SET_PRIORITY:
                case self::ACTION_SET_SEVERITY:
                case self::ACTION_SET_CATEGORY:
                case self::ACTION_SET_PERCENT:
                case self::ACTION_SET_RESOLUTION:
                case self::ACTION_SET_REPRODUCABILITY:
                    return true;
                default:
                    return ($this->isCustomAction() && $this->getCustomField()->getType() != DatatypeBase::CALCULATED_FIELD);
            }
        }

        public function toJSON($detailed = true)
        {
            $json = parent::toJSON($detailed);
            $json['type'] = $this->getActionType();
            $json['value'] = $this->getTargetValue();

            return $json;
        }

    }
