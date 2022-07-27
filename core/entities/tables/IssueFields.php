<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\entities\DatatypeBase;
    use pachno\core\entities\IssuetypeScheme;
    use pachno\core\entities\Scope;
    use pachno\core\framework;

    /**
     * Issue fields table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Issue fields table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issuefields")
     */
    class IssueFields extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'issuefields';

        public const ID = 'issuefields.id';

        public const SCOPE = 'issuefields.scope';

        public const ADDITIONAL = 'issuefields.is_additional';

        public const ISSUETYPE_ID = 'issuefields.issuetype_id';

        public const ISSUETYPE_SCHEME_ID = 'issuefields.issuetype_scheme_id';

        public const FIELD_KEY = 'issuefields.field_key';

        public const REPORTABLE = 'issuefields.is_reportable';

        public const REQUIRED = 'issuefields.required';

        public static function getFieldName($key)
        {
            switch ($key) {
                case DatatypeBase::FIELD_SHORTNAME:
                    return framework\Context::getI18n()->__('Identifier / shortname');
                case DatatypeBase::FIELD_DESCRIPTION:
                    return framework\Context::getI18n()->__('Description');
                case DatatypeBase::FIELD_REPRODUCTION_STEPS:
                    return framework\Context::getI18n()->__('Reproduction steps');
                case DatatypeBase::FIELD_USER_PAIN:
                    return framework\Context::getI18n()->__('Triaging: User pain');
                case DatatypeBase::FIELD_PERCENT_COMPLETE:
                    return framework\Context::getI18n()->__('Percent completed');
                case DatatypeBase::FIELD_BUILD:
                    return framework\Context::getI18n()->__('Affected release(s)');
                case DatatypeBase::FIELD_COMPONENT:
                    return framework\Context::getI18n()->__('Affected component(s)');
                case DatatypeBase::FIELD_EDITION:
                    return framework\Context::getI18n()->__('Affected edition(s)');
                case DatatypeBase::FIELD_ESTIMATED_TIME:
                    return framework\Context::getI18n()->__('Estimate');
                case DatatypeBase::FIELD_SPENT_TIME:
                    return framework\Context::getI18n()->__('Time spent');
                case DatatypeBase::FIELD_MILESTONE:
                    return framework\Context::getI18n()->__('Milestone');
                case DatatypeBase::FIELD_VOTES:
                    return framework\Context::getI18n()->__('Votes');
                case DatatypeBase::FIELD_OWNED_BY:
                    return framework\Context::getI18n()->__('Issue owner');
                default:
                    return framework\Context::getI18n()->__(ucfirst($key));
            }
        }

        public static function getFieldDescription($key)
        {
            switch ($key) {
                case DatatypeBase::FIELD_SHORTNAME:
                    return framework\Context::getI18n()->__('A short, recognizable name');
                case DatatypeBase::FIELD_DESCRIPTION:
                    return framework\Context::getI18n()->__('Textarea with issue description');
                case DatatypeBase::FIELD_REPRODUCTION_STEPS:
                    return framework\Context::getI18n()->__('Steps to reproduce the issue');
                case DatatypeBase::FIELD_USER_PAIN:
                    return framework\Context::getI18n()->__('Triaging: User pain');
                case DatatypeBase::FIELD_PERCENT_COMPLETE:
                    return framework\Context::getI18n()->__('Percent completed');
                case DatatypeBase::FIELD_BUILD:
                    return framework\Context::getI18n()->__('Affected release(s)');
                case DatatypeBase::FIELD_COMPONENT:
                    return framework\Context::getI18n()->__('Affected component(s)');
                case DatatypeBase::FIELD_EDITION:
                    return framework\Context::getI18n()->__('Affected edition(s)');
                case DatatypeBase::FIELD_ESTIMATED_TIME:
                    return framework\Context::getI18n()->__('Estimated time to complete');
                case DatatypeBase::FIELD_SPENT_TIME:
                    return framework\Context::getI18n()->__('Time spent working on the issue');
                case DatatypeBase::FIELD_MILESTONE:
                    return framework\Context::getI18n()->__('Targetted for milestone');
                case DatatypeBase::FIELD_VOTES:
                    return framework\Context::getI18n()->__('Up- and downvotes');
                case DatatypeBase::FIELD_OWNED_BY:
                    return framework\Context::getI18n()->__('Issue owner');
                case DatatypeBase::FIELD_ASSIGNEE:
                    return framework\Context::getI18n()->__('Issue assignee (team/user)');
                default:
                    return framework\Context::getI18n()->__(ucfirst($key));
            }
        }

        public static function getFieldFontAwesomeImageStyle($key)
        {
            switch ($key) {
                default:
                    return 'fas';
            }
        }

        public static function getFieldFontAwesomeImage($key)
        {
            switch ($key) {
                case DatatypeBase::FIELD_DESCRIPTION:
                case DatatypeBase::FIELD_REPRODUCTION_STEPS:
                    return 'align-left';
                case DatatypeBase::FIELD_USER_PAIN:
                    return 'chart-line';
                case DatatypeBase::FIELD_PERCENT_COMPLETE:
                    return 'percentage';
                case DatatypeBase::FIELD_BUILD:
                    return 'compact-disc';
                case DatatypeBase::FIELD_COMPONENT:
                    return 'boxes';
                case DatatypeBase::FIELD_EDITION:
                    return 'box';
                case DatatypeBase::FIELD_ESTIMATED_TIME:
                case DatatypeBase::FIELD_SPENT_TIME:
                    return 'clock';
                case DatatypeBase::FIELD_MILESTONE:
                    return 'list-alt';
                case DatatypeBase::FIELD_VOTES:
                    return 'vote-yea';
                case DatatypeBase::FIELD_OWNED_BY:
                    return 'user';
                default:
                    return 'tag';
            }
        }

        public function getSchemeVisibleFieldsArrayByIssuetypeID($scheme_id, $issuetype_id)
        {
            $res = $this->getBySchemeIDandIssuetypeID($scheme_id, $issuetype_id);
            $fields = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $fields[$row->get(IssueFields::FIELD_KEY)] = [
                        'label' => $row->get(CustomFields::FIELD_DESCRIPTION),
                        'required' => (bool)$row->get(IssueFields::REQUIRED),
                        'reportable' => (bool)$row->get(IssueFields::REPORTABLE),
                        'additional' => (bool)$row->get(IssueFields::ADDITIONAL),
                        'type' => $row->get(CustomFields::FIELD_TYPE) ? $row->get(CustomFields::FIELD_TYPE) : 'builtin',
                    ];
                }
            }

            return $fields;
        }

        public function getBySchemeIDandIssuetypeID($scheme_id, $issuetype_id)
        {
            $query = $this->getQuery();
            $query->join(CustomFields::getTable(), CustomFields::FIELD_KEY, self::FIELD_KEY);
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query, false);

            return $res;
        }

        public function deleteBySchemeIDandIssuetypeID($scheme_id, $issuetype_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function copyBySchemeIDs($from_scheme_id, $to_scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ISSUETYPE_SCHEME_ID, $from_scheme_id);
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $insertion = new Insertion();
                    $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                    $insertion->add(self::ISSUETYPE_SCHEME_ID, $to_scheme_id);
                    $insertion->add(self::FIELD_KEY, $row->get(self::FIELD_KEY));
                    $insertion->add(self::ADDITIONAL, $row->get(self::ADDITIONAL));
                    $insertion->add(self::ISSUETYPE_ID, $row->get(self::ISSUETYPE_ID));
                    $insertion->add(self::REPORTABLE, $row->get(self::REPORTABLE));
                    $insertion->add(self::REQUIRED, $row->get(self::REQUIRED));
                    $this->rawInsert($insertion);
                }
            }
        }

        public function addFieldAndDetailsBySchemeIDandIssuetypeID($scheme_id, $issuetype_id, $key, $details)
        {
            $insertion = new Insertion();
            $insertion->add(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
            $insertion->add(self::FIELD_KEY, $key);
            if (array_key_exists('reportable', $details)) {
                $insertion->add(self::REPORTABLE, true);
            }
            if (array_key_exists('required', $details)) {
                $insertion->add(self::REQUIRED, true);
            }

            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawInsert($insertion);
        }

        public function deleteByIssuetypeSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function deleteByIssueFieldKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::FIELD_KEY, $key);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function loadFixtures(Scope $scope, IssuetypeScheme $full_range_scheme, IssuetypeScheme $balanced_scheme, IssuetypeScheme $balanced_agile_scheme, IssuetypeScheme $simple_scheme, $issue_type_bug_report_id, $issue_type_feature_request_id, $issue_type_enhancement_id, $issue_type_task_id, $issue_type_user_story_id, $issue_type_idea_id, $issue_type_epic_id)
        {
            $scope = $scope->getID();
            $schemes = [
                $full_range_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'edition' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_enhancement_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_idea_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_user_story_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => true, 'required' => false, 'additional' => true],
                    ],
                    $issue_type_epic_id => [
                        'shortname' => ['reportable' => true, 'required' => true, 'additional' => false],
                    ],
                ],
                $balanced_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'edition' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_idea_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                ],
                $balanced_agile_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'edition' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_idea_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_user_story_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => true, 'required' => false, 'additional' => true],
                    ],
                    $issue_type_epic_id => [
                        'shortname' => ['reportable' => true, 'required' => true, 'additional' => false],
                    ],
                ],
                $simple_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'edition' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                ],
            ];

            foreach ($schemes as $scheme_id => $issuetypes) {
                foreach ($issuetypes as $issuetype_id => $fields) {
                    foreach ($fields as $field => $settings) {
                        $insertion = new Insertion();
                        $insertion->add(self::ISSUETYPE_SCHEME_ID, $scheme_id);
                        $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
                        $insertion->add(self::FIELD_KEY, $field);
                        $insertion->add(self::REPORTABLE, $settings['reportable']);
                        $insertion->add(self::REQUIRED, $settings['required']);
                        $insertion->add(self::ADDITIONAL, $settings['additional']);
                        $insertion->add(self::SCOPE, $scope);
                        $this->rawInsert($insertion);
                    }
                }
            }

        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::FIELD_KEY, 100);
            parent::addBoolean(self::REQUIRED);
            parent::addBoolean(self::REPORTABLE);
            parent::addBoolean(self::ADDITIONAL);
            parent::addForeignKeyColumn(self::ISSUETYPE_ID, IssueTypes::getTable(), IssueTypes::ID);
            parent::addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, IssuetypeSchemes::getTable(), IssuetypeSchemes::ID);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('scope_issuetypescheme_issuetype', [self::SCOPE, self::ISSUETYPE_SCHEME_ID, self::ISSUETYPE_ID]);
        }

    }
