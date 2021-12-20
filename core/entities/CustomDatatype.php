<?php

    namespace pachno\core\entities;

    use b2db\Row;
    use Exception;
    use pachno\core\entities\tables\Builds;
    use pachno\core\entities\tables\Components;
    use pachno\core\entities\tables\Editions;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\framework;

    /**
     * @Table(name="\pachno\core\entities\tables\CustomFields")
     */
    class CustomDatatype extends DatatypeBase
    {

        protected static $_types = null;

        /**
         * This custom types options (if any)
         *
         * @var array
         * @Relates(class="\pachno\core\entities\CustomDatatypeOption", collection=true, foreign_column="customfield_id", orderby="sort_order")
         */
        protected $_options = null;

        /**
         * The custom types description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = null;

        /**
         * The custom types instructions
         *
         * @var string
         * @Column(type="text")
         */
        protected $_instructions = null;

        public static function getByFieldType($type)
        {
            $return_fields = [];
            foreach (self::getAll() as $field) {
                if ($field->getType() == $type) $return_fields[$field->getID()] = $field;
            }

            return $return_fields;
        }

        /**
         * Returns all custom types available
         *
         * @return CustomDatatype[]
         */
        public static function getAll()
        {
            if (self::$_types === null) {
                self::$_types = tables\CustomFields::getTable()->getAll();
            }

            return self::$_types;
        }

        public static function getByFieldTypes($types)
        {
            $return_fields = [];
            foreach (self::getAll() as $field) {
                if (in_array($field->getType(), $types)) $return_fields[$field->getID()] = $field;
            }

            return $return_fields;
        }

        public static function getAllExceptTypes($types)
        {
            $return_fields = [];
            foreach (self::getAll() as $field) {
                if (!in_array($field->getType(), $types)) $return_fields[$field->getID()] = $field;
            }

            return $return_fields;
        }

        public static function doesKeyExist($key)
        {
            return array_key_exists($key, self::getAll());
        }

        /**
         * Get a custom type by its key
         *
         * @param string $key
         *
         * @return CustomDatatype
         */
        public static function getByKey($key)
        {
            foreach (self::getAll() as $field) {
                if ($field->getKey() == $key) return $field;
            }

            return null;
        }

        public static function getInternalChoiceFieldsAsArray()
        {
            return [DatatypeBase::RELEASES_CHOICE, DatatypeBase::COMPONENTS_CHOICE, DatatypeBase::MILESTONE_CHOICE,
                DatatypeBase::EDITIONS_CHOICE, DatatypeBase::STATUS_CHOICE, DatatypeBase::USER_CHOICE, DatatypeBase::TEAM_CHOICE, DatatypeBase::CLIENT_CHOICE];
        }

        /**
         * Constructor
         *
         * @param B2DBrow $row [optional] A B2DBrow to use
         */
        public function _construct(Row $row, string $foreign_key = null): void
        {
            $this->_description = $this->_description ?: $this->_name;
        }

        public function createNewOption($name, $value, $itemdata = null)
        {
            if ($this->getType() == DatatypeBase::CALCULATED_FIELD) {
                // Only allow one option/formula for the calculated field
                $opts = $this->getOptions();
                foreach ($opts as $option) {
                    $option->delete();
                }
            }

            $option = new CustomDatatypeOption();
            $option->setName($name);
            $option->setKey($this->getKey());
            $option->setValue($value);
            $option->setItemdata($itemdata);
            $option->setCustomdatatype($this->_id);
            $option->save();

            // In order to set permissions correctly the item type has to be the same
            // as the option id not the item field. set the opton id with the newly generated
            // option ID and save again
            $option->setItemtype($option->getID());
            $option->save();
            $this->_options = null;

            return $option;
        }

        public function getType()
        {
            return $this->_itemtype;
        }

        public function getOptions()
        {
            $this->_populateOptions();

            return $this->_options;
        }

        protected function _populateOptions()
        {
            if ($this->_options === null) {
                if ($this->hasCustomOptions()) {
                    $this->_b2dbLazyLoad('_options');
                } else {
                    switch ($this->getType()) {
                        case DatatypeBase::RELEASES_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getBuilds() : Builds::getTable()->selectAll();
                            break;
                        case DatatypeBase::COMPONENTS_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getComponents() : Components::getTable()->selectAll();
                            break;
                        case DatatypeBase::EDITIONS_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getEditions() : Editions::getTable()->selectAll();
                            break;
                        case DatatypeBase::MILESTONE_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getMilestonesForIssues() : Milestones::getTable()->selectAll();
                            break;
                        case DatatypeBase::STATUS_CHOICE:
                            $this->_options = Status::getAll();
                            break;
                    }
                }
            }
        }

        public function hasCustomOptions()
        {
            return (bool)in_array($this->getType(), self::getCustomChoiceFieldsAsArray());
        }

        public static function getCustomChoiceFieldsAsArray()
        {
            return [DatatypeBase::DROPDOWN_CHOICE_TEXT,
                DatatypeBase::RADIO_CHOICE,
                DatatypeBase::CALCULATED_FIELD
            ];
        }

        public function setType($type)
        {
            $this->_itemtype = $type;
        }

        /**
         * Return the description for this custom type
         *
         * @return string
         */
        public function getTypeDescription()
        {
            $types = self::getFieldTypes();

            return $types[$this->_itemtype]['title'] ?? 'INVALID TYPE';
        }

        public static function getFieldTypes()
        {
            $i18n = framework\Context::getI18n();
            $types = [];
            $types[DatatypeBase::DROPDOWN_CHOICE_TEXT] = ['title' => __('Dropdown list of choices (multi-select)'), 'description' => $i18n->__('A dropdown list where the user can select one or more choices'), 'icon' => 'check-square'];
            $types[DatatypeBase::RADIO_CHOICE] = ['title' => __('Dropdown list of choices'), 'description' => $i18n->__('A dropdown list where the user can select one of the available choices'), 'icon' => 'check-circle'];
            $types[DatatypeBase::INPUT_TEXT] = ['title' => __('Simple text input'), 'description' => $i18n->__('A text input where the user can input a single line of text'), 'icon' => 'align-left'];
            $types[DatatypeBase::INPUT_TEXTAREA_MAIN] = ['title' => __('Block of text input (main)'), 'description' => $i18n->__('A text input in the issue main view where the user can input a block of text'), 'icon' => 'align-left'];
            $types[DatatypeBase::INPUT_TEXTAREA_SMALL] = ['title' => __('Block of text input (details)'), 'description' => $i18n->__('A text input in the issue details list sidebar where the user can input a block of text'), 'icon' => 'align-left'];
            $types[DatatypeBase::RELEASES_CHOICE] = ['title' => __('List of releases'), 'description' => $i18n->__('A list of the available releases'), 'icon' => 'compact-disc'];
            $types[DatatypeBase::COMPONENTS_CHOICE] = ['title' => __('List of components'), 'description' => $i18n->__('A list of the available components'), 'icon' => 'boxes'];
            $types[DatatypeBase::EDITIONS_CHOICE] = ['title' => __('List of editions'), 'description' => $i18n->__('A list of the available editions'), 'icon' => 'box'];
            $types[DatatypeBase::MILESTONE_CHOICE] = ['title' => __('List of milestones'), 'description' => $i18n->__('A list of the available milestones'), 'icon' => 'list-alt'];
            $types[DatatypeBase::STATUS_CHOICE] = ['title' => __('Status selector'), 'description' => $i18n->__('Let the user choose from a list of the available statuses'), 'icon' => 'chart-pie'];
            $types[DatatypeBase::DATE_PICKER] = ['title' => __('Date picker'), 'description' => $i18n->__('A date picker'), 'icon' => 'calendar-check'];
            $types[DatatypeBase::DATETIME_PICKER] = ['title' => __('Date and time picker'), 'description' => $i18n->__('A date and time picker'), 'icon' => 'clock'];
            $types[DatatypeBase::USER_CHOICE] = ['title' => __('User selector'), 'description' => $i18n->__('Find and pick a user'), 'icon' => 'user'];
            $types[DatatypeBase::TEAM_CHOICE] = ['title' => __('Team selector'), 'description' => $i18n->__('Find and pick a team'), 'icon' => 'users'];
            $types[DatatypeBase::CLIENT_CHOICE] = ['title' => __('Client selector'), 'description' => $i18n->__('Find and pick a client'), 'icon' => 'users'];
            $types[DatatypeBase::CALCULATED_FIELD] = ['title' => __('Formula'), 'description' => $i18n->__('A field calculated from different values'), 'icon' => 'square-root-alt'];

            return $types;

        }

        public function hasPredefinedOptions()
        {
            return (bool)in_array($this->getType(), self::getChoiceFieldsAsArray());
        }

        public static function getChoiceFieldsAsArray()
        {
            return [DatatypeBase::DROPDOWN_CHOICE_TEXT, DatatypeBase::RADIO_CHOICE, DatatypeBase::RELEASES_CHOICE, DatatypeBase::COMPONENTS_CHOICE,
                DatatypeBase::EDITIONS_CHOICE, DatatypeBase::STATUS_CHOICE, DatatypeBase::USER_CHOICE, DatatypeBase::TEAM_CHOICE, DatatypeBase::CLIENT_CHOICE, DatatypeBase::MILESTONE_CHOICE];
        }

        /**
         * Get the custom types description
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * Set the custom types description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        /**
         * Get the custom types instructions
         */
        public function getInstructions()
        {
            return $this->_instructions;
        }

        /**
         * Set the custom types instructions
         *
         * @param string $instructions
         */
        public function setInstructions($instructions)
        {
            $this->_instructions = $instructions;
        }

        /**
         * Whether or not this custom type has any instructions
         *
         * @return boolean
         */
        public function hasInstructions()
        {
            return (bool)$this->_instructions;
        }

        /**
         * Set the custom type name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Whether or not this custom data type is visible for this issue type
         *
         * @param integer $issuetype_id
         *
         * @return bool
         */
        public function isVisibleForIssuetype($issuetype_id)
        {
            return true;
        }

        /**
         * Whether or not this custom data type is searchable from the Issues filter
         *
         * @return bool
         */
        public function isSearchable()
        {
            switch ($this->getType()) {
                case DatatypeBase::CALCULATED_FIELD:
                    return false;
            }

            return true;
        }

        /**
         * Whether or not this custom data type is editable from the Issues detail page
         *
         * @return bool
         */
        public function isEditable()
        {
            switch ($this->getType()) {
                case DatatypeBase::CALCULATED_FIELD:
                    return false;
            }

            return true;
        }

        public function isBuiltin()
        {
            return false;
        }

        public function getFontAwesomeIcon()
        {
            switch ($this->_itemtype) {
                default:
                    return 'question-mark';
            }
        }

        public function getFontAwesomeIconStyle()
        {
            switch ($this->_itemtype) {
                default:
                    return 'fas';
            }
        }

        protected function _preSave(bool $is_new): void
        {
            parent::_preSave($is_new);
            if ($is_new) {
                if (!$this->_description) {
                    $this->_description = $this->_name;
                }
                if (!$this->_itemdata) {
                    $this->_itemdata = $this->_name;
                }
                $this->_generateKey();
                if (!$this->_key) {
                    throw new Exception(framework\Context::getI18n()->__('This field is not valid'));
                }

                if (array_key_exists($this->_key, self::getAll())) {
                    throw new Exception(framework\Context::getI18n()->__('This field key already exists'));
                }
            }
        }

        protected function _preDelete(): void
        {
            tables\CustomFieldOptions::getTable()->deleteCustomFieldOptions($this->getID());
            tables\IssueFields::getTable()->deleteByIssueFieldKey($this->getKey());
        }

        /**
         * Return this custom types key
         *
         * @return string
         */
        public function getKey()
        {
            return $this->_key;
        }

    }

