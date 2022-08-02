<?php

    namespace pachno\core\entities;

    use Exception;
    use pachno\core\entities\common\Identifiable;
    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\Builds;
    use pachno\core\entities\tables\Clients;
    use pachno\core\entities\tables\Components;
    use pachno\core\entities\tables\Editions;
    use pachno\core\entities\tables\Issues;
    use pachno\core\entities\tables\ListTypes;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\entities\tables\Teams;
    use pachno\core\framework;
    use pachno\core\framework\Event;

    /**
     * Workflow transition validation rule class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Workflow transition validation rule class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\WorkflowTransitionValidationRules")
     */
    class WorkflowTransitionValidationRule extends IdentifiableScoped
    {

        public const RULE_MAX_ASSIGNED_ISSUES = 'max_assigned_issues';
        public const RULE_STATUS_VALID = 'valid_status';
        public const RULE_RESOLUTION_VALID = 'valid_resolution';
        public const RULE_REPRODUCABILITY_VALID = 'valid_reproducability';
        public const RULE_PRIORITY_VALID = 'valid_priority';
        public const RULE_TEAM_MEMBERSHIP_VALID = 'valid_team';
        public const RULE_ISSUE_IN_MILESTONE_VALID = 'valid_in_milestone';
        public const CUSTOMFIELD_VALIDATE_PREFIX = 'customfield_validate_';
        
        public const PRE_TRANSITION_VALIDATION = 'pre';
        public const POST_TRANSITION_VALIDATION = 'post';

        /**
         * @Column(type="string", length=100, name="rule")
         */
        protected $_name = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_rule_value = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_pre_or_post;

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

        public static function getAvailablePreValidationRules()
        {
            $initial_list = [];
            $i18n = framework\Context::getI18n();
            $initial_list[self::RULE_MAX_ASSIGNED_ISSUES] = $i18n->__('Max number of assigned issues');
            $initial_list[self::RULE_TEAM_MEMBERSHIP_VALID] = $i18n->__('User must be member of a certain team');
            $initial_list[self::RULE_ISSUE_IN_MILESTONE_VALID] = $i18n->__('Issue must be in milestone');

            $event = new Event('core', 'WorkflowTransitionValidationRule::getAvailablePreValidationRules', null, [], $initial_list);
            $event->trigger();

            return $event->getReturnList();
        }

        public static function getAvailablePostValidationRules()
        {
            $initial_list = [];
            $i18n = framework\Context::getI18n();
            $initial_list[self::RULE_PRIORITY_VALID] = $i18n->__('Validate specified priority');
            $initial_list[self::RULE_REPRODUCABILITY_VALID] = $i18n->__('Validate specified reproducability');
            $initial_list[self::RULE_RESOLUTION_VALID] = $i18n->__('Validate specified resolution');
            $initial_list[self::RULE_STATUS_VALID] = $i18n->__('Validate specified status');
            foreach (CustomDatatype::getAll() as $key => $details) {
                $initial_list[self::CUSTOMFIELD_VALIDATE_PREFIX . $key] = $i18n->__('Validate specified %key', ['%key' => $key]);
            }
            $initial_list[self::RULE_TEAM_MEMBERSHIP_VALID] = $i18n->__('Validate team membership of assignee');

            $event = new Event('core', 'WorkflowTransitionValidationRule::getAvailablePostValidationRules', null, [], $initial_list);
            $event->trigger();

            return $event->getReturnList();
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

        /**
         * @return WorkflowTransition
         */
        public function getTransition()
        {
            return $this->_b2dbLazyLoad('_transition_id');
        }

        public function setPost()
        {
            $this->_pre_or_post = 'post';
        }

        public function setPre()
        {
            $this->_pre_or_post = 'pre';
        }

        public function isPreOrPost()
        {
            return $this->_pre_or_post;
        }

        public function isPre()
        {
            return (bool)($this->_pre_or_post == 'pre');
        }

        public function setRule($rule)
        {
            $this->_name = $rule;
        }

        public function getName()
        {
            return $this->_name;
        }

        public function getRuleOptions()
        {
            if ($this->getRule() == WorkflowTransitionValidationRule::RULE_STATUS_VALID) {
                $options = Status::getAll();
            } elseif ($this->getRule() == WorkflowTransitionValidationRule::RULE_PRIORITY_VALID) {
                $options = Priority::getAll();
            } elseif ($this->getRule() == WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID) {
                $options = Resolution::getAll();
            } elseif ($this->getRule() == WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID) {
                $options = Reproducability::getAll();
            } elseif ($this->getRule() == WorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID) {
                $options = Team::getAll();
            } elseif ($this->getRule() == WorkflowTransitionValidationRule::RULE_ISSUE_IN_MILESTONE_VALID) {
                $options = Milestones::getTable()->selectAll();
            } elseif ($this->isCustom()) {
                $options = $this->getCustomField()->getOptions();
            }

            return $options;
        }

        public function getRule()
        {
            return $this->_name;
        }

        public function isCustom()
        {
            return (bool)(strpos($this->_name, self::CUSTOMFIELD_VALIDATE_PREFIX) !== false);
        }

        /**
         * Returns the custom field object used in the validation rule
         *
         * @return CustomDatatype
         */
        public function getCustomField()
        {
            return CustomDatatype::getByKey($this->getCustomFieldname());
        }

        /**
         * Returns the identifier key for the customfield used in the validation rule
         *
         * @return string
         */
        public function getCustomFieldname()
        {
            return substr($this->_name, strlen(self::CUSTOMFIELD_VALIDATE_PREFIX));
        }

        public function isValueValid($value)
        {
            $is_core = in_array($this->_name, [self::RULE_STATUS_VALID, self::RULE_RESOLUTION_VALID, self::RULE_REPRODUCABILITY_VALID, self::RULE_PRIORITY_VALID, self::RULE_TEAM_MEMBERSHIP_VALID, self::RULE_ISSUE_IN_MILESTONE_VALID]);
            $is_custom = $this->isCustom();

            if ($is_core || $is_custom) {
                $value = (is_object($value)) ? $value->getID() : $value;

                return ($this->getRuleValue()) ? in_array($value, explode(',', $this->getRuleValue())) : (bool)$value;
            } else {
                $event = new Event('core', 'WorkflowTransitionValidationRule::isValueValid', $this);
                $event->setReturnValue(false);
                $event->triggerUntilProcessed(['value' => $value]);

                return $event->getReturnValue();
            }
        }

        public function getRuleValue()
        {
            return $this->_rule_value;
        }

        public function setRuleValue($rule_value)
        {
            $this->_rule_value = $rule_value;
        }

        public function isValid($input)
        {
            switch ($this->_name) {
                case self::RULE_MAX_ASSIGNED_ISSUES:
                    $num_issues = (int)$this->getRuleValue();

                    return ($num_issues) ? (bool)(count(framework\Context::getUser()->getUserAssignedIssues()) < $num_issues) : true;
                    break;
                case self::RULE_TEAM_MEMBERSHIP_VALID:
                    $valid_items = explode(',', $this->getRuleValue());
                    $teams = Team::getAll();
                    if ($this->isPost()) {
                        if ($input instanceof Issue) {
                            $assignee = $input->getAssignee();
                        }
                    }
                    if (!isset($assignee)) {
                        $assignee = framework\Context::getUser();
                    }
                    if ($assignee instanceof User) {
                        if (count($valid_items) == 1 && reset($valid_items) == '') return true;

                        foreach ($valid_items as $team_id) {
                            if ($assignee->isMemberOfTeam($teams[$team_id]))
                                return true;
                        }
                    } elseif ($assignee instanceof Team) {
                        foreach ($valid_items as $team_id) {
                            if ($assignee->getID() == $team_id)
                                return true;
                        }
                    }

                    return false;
                case self::RULE_ISSUE_IN_MILESTONE_VALID:
                    $valid_items = explode(',', $this->getRuleValue());
                    if ($input instanceof Issue) {
                        $issue = $input;
                    } elseif ($input->hasParameter('issue_id')) {
                        $issue = Issues::getTable()->selectByID((int)$input->getParameter('issue_id'));
                    }
                    if (isset($issue) && $issue instanceof Issue) {
                        if (!$issue->getMilestone() instanceof Milestone) return false;

                        if (count($valid_items) == 1 && reset($valid_items) == '') return true;

                        return in_array($issue->getMilestone()->getID(), $valid_items);
                    }

                    return false;
                case self::RULE_STATUS_VALID:
                case self::RULE_PRIORITY_VALID:
                case self::RULE_RESOLUTION_VALID:
                case self::RULE_REPRODUCABILITY_VALID:
                    $valid_items = explode(',', $this->getRuleValue());
                    $valid = false;
                    if ($this->_name == self::RULE_STATUS_VALID) {
                        $fieldname = 'Status';
                        $fieldname_small = 'status';
                    } elseif ($this->_name == self::RULE_RESOLUTION_VALID) {
                        $fieldname = 'Resolution';
                        $fieldname_small = 'resolution';
                    } elseif ($this->_name == self::RULE_REPRODUCABILITY_VALID) {
                        $fieldname = 'Reproducability';
                        $fieldname_small = 'reproducability';
                    } elseif ($this->_name == self::RULE_PRIORITY_VALID) {
                        $fieldname = 'Priority';
                        $fieldname_small = 'priority';
                    } else {
                        throw new framework\exceptions\ConfigurationException(framework\Context::getI18n()->__('Invalid workflow validation rule: %rule_name', ['%rule_name' => $this->_name]));
                    }

                    if (!$this->getRuleValue()) {
                        if ($input instanceof Issue) {
                            $getter = "get{$fieldname}";

                            if (is_object($input->$getter())) {
                                $valid = true;
                            }
                        } elseif ($input instanceof framework\Request) {
                            if ($input->getParameter("{$fieldname_small}_id") && Status::has($input->getParameter("{$fieldname_small}_id"))) {
                                $valid = true;
                            }
                        }
                    } else {
                        foreach ($valid_items as $item) {
                            if ($input instanceof Issue) {
                                $type = "\\pachno\\core\\entities\\{$fieldname}";
                                $getter = "get{$fieldname}";

                                if (is_object($input->$getter()) && $type::getB2DBTable()->selectByID((int)$item)->getID() == $input->$getter()->getID()) {
                                    $valid = true;
                                    break;
                                }
                            } elseif ($input instanceof framework\Request) {
                                if ($input->getParameter("{$fieldname_small}_id") == $item) {
                                    $valid = true;
                                    break;
                                }
                            }
                        }
                    }

                    return $valid;
                    break;
                default:
                    if ($this->isCustom()) {
                        switch ($this->getCustomType()) {
                            case DatatypeBase::RADIO_CHOICE:
                            case DatatypeBase::DROPDOWN_CHOICE_TEXT:
                            case DatatypeBase::TEAM_CHOICE:
                            case DatatypeBase::STATUS_CHOICE:
                            case DatatypeBase::MILESTONE_CHOICE:
                            case DatatypeBase::CLIENT_CHOICE:
                            case DatatypeBase::COMPONENTS_CHOICE:
                            case DatatypeBase::EDITIONS_CHOICE:
                            case DatatypeBase::RELEASES_CHOICE:
                                $valid_items = explode(',', $this->getRuleValue());
                                if ($input instanceof Issue) {
                                    $value = $input->getCustomField($this->getCustomFieldname());
                                } elseif ($input instanceof framework\Request) {
                                    $value = $input->getParameter($this->getCustomFieldname() . "_id");
                                }

                                $valid = false;
                                if (!$this->getRuleValue()) {
                                    foreach ($this->getCustomField()->getOptions() as $item) {
                                        if ($item->getID() == $value) {
                                            $valid = true;
                                            break;
                                        }
                                    }
                                } else {
                                    foreach ($valid_items as $item) {
                                        if ($value instanceof Identifiable && $value->getID() == $item) {
                                            $valid = true;
                                            break;
                                        } elseif (is_numeric($value) && $value == $item) {
                                            $valid = true;
                                            break;
                                        }
                                    }
                                }

                                return $valid;
                                break;
                        }
                    } else {
                        $event = new Event('core', 'WorkflowTransitionValidationRule::isValid', $this);
                        $event->setReturnValue(false);
                        $event->triggerUntilProcessed(['input' => $input]);

                        return $event->getReturnValue();
                    }
            }
        }

        public function isPost()
        {
            return (bool)($this->_pre_or_post == 'post');
        }

        /**
         * Returns the custom type for the custom field object used in the validation rule
         *
         * @return string
         */
        public function getCustomType()
        {
            return ($this->isCustom()) ? $this->getCustomField()->getType() : '';
        }

        public function toJSON($detailed = true)
        {
            return [
                'name' => $this->getRule(),
                'values' => $this->getRuleValueAsJoinedString()
            ];
        }

        public function getRuleValueAsJoinedString()
        {
            $is_core = in_array($this->_name, [self::RULE_STATUS_VALID, self::RULE_RESOLUTION_VALID, self::RULE_REPRODUCABILITY_VALID, self::RULE_PRIORITY_VALID, self::RULE_TEAM_MEMBERSHIP_VALID, self::RULE_ISSUE_IN_MILESTONE_VALID]);
            $is_custom = $this->isCustom();
            $customtype = $this->getCustomType();

            if ($this->_name == self::RULE_STATUS_VALID) {
                $fieldname = '\pachno\core\entities\Status';
            } elseif ($this->_name == self::RULE_RESOLUTION_VALID) {
                $fieldname = '\pachno\core\entities\Resolution';
            } elseif ($this->_name == self::RULE_REPRODUCABILITY_VALID) {
                $fieldname = '\pachno\core\entities\Reproducability';
            } elseif ($this->_name == self::RULE_PRIORITY_VALID) {
                $fieldname = '\pachno\core\entities\Priority';
            } elseif ($this->_name == self::RULE_TEAM_MEMBERSHIP_VALID) {
                $fieldname = '\pachno\core\entities\Team';
            } elseif ($this->_name == self::RULE_ISSUE_IN_MILESTONE_VALID) {
                $fieldname = '\pachno\core\entities\Milestone';
            }

            if ($is_core || $is_custom) {
                $values = explode(',', $this->getRuleValue());
                if ($is_custom) {
                    $custom_field_key = substr($this->_name, strlen(self::CUSTOMFIELD_VALIDATE_PREFIX) - 1);
                    $custom_field = tables\CustomFields::getTable()->getByKey($custom_field_key);
                }
                $return_values = [];
                foreach ($values as $value) {
                    try {
                        if ($is_core) {
                            $field = $fieldname::getB2DBTable()->selectByID((int)$value);
                        } elseif ($is_custom) {
                            switch ($customtype) {
                                case DatatypeBase::RADIO_CHOICE:
                                case DatatypeBase::DROPDOWN_CHOICE_TEXT:
                                    $field = tables\CustomFieldOptions::getTable()->selectById((int)$value);
                                    break;
                                case DatatypeBase::TEAM_CHOICE:
                                    $field = Teams::getTable()->selectById((int)$value);
                                    break;
                                case DatatypeBase::STATUS_CHOICE:
                                    $field = ListTypes::getTable()->selectById((int)$value);
                                    break;
                                case DatatypeBase::MILESTONE_CHOICE:
                                    $field = Milestones::getTable()->selectById((int)$value);
                                    break;
                                case DatatypeBase::CLIENT_CHOICE:
                                    $field = Clients::getTable()->selectById((int)$value);
                                    break;
                                case DatatypeBase::COMPONENTS_CHOICE:
                                    $field = Components::getTable()->selectById((int)$value);
                                    break;
                                case DatatypeBase::EDITIONS_CHOICE:
                                    $field = Editions::getTable()->selectById((int)$value);
                                    break;
                                case DatatypeBase::RELEASES_CHOICE:
                                    $field = Builds::getTable()->selectById((int)$value);
                                    break;
                            }
                        }
                        if ($field instanceof Identifiable) {
                            if ($field instanceof Milestone || $field instanceof Component || $field instanceof Edition || $field instanceof Build) {
                                $return_values[] = $field->getProject()->getName() . ' - ' . $field->getName();
                            } elseif ($field instanceof Status) {
                                $return_values[] = '<span class="status-badge" style="background-color: ' . $field->getColor() . '; color: ' . $field->getTextColor() . ';">' . $field->getName() . '</span>';
                            } else {
                                $return_values[] = $field->getName();
                            }
                        }
                    } catch (Exception $e) {
                    }
                }

                return join(' / ', $return_values);
            } else {
                $event = new Event('core', 'WorkflowTransitionValidationRule::getRuleValueAsJoinedString', $this);
                $event->triggerUntilProcessed();

                return $event->getReturnValue();
            }
        }

    }
