<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\framework;
    use pachno\core\framework\Settings;
    use pachno\core\helpers\TextParser;

    /**
     * Saved search class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Saved search class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\SavedSearches")
     */
    class SavedSearch extends IdentifiableScoped
    {

        public const PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES = 1;

        public const PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS = 12;

        public const PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES = 2;

        public const PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS = 13;

        public const PREDEFINED_SEARCH_PROJECT_WISHLIST = 10;

        public const PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO = 6;

        public const PREDEFINED_SEARCH_PROJECT_MOST_VOTED = 7;

        public const PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH = 8;

        public const PREDEFINED_SEARCH_PROJECT_REPORTED_LAST_NUMBEROF_TIMEUNITS = 9;

        public const PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES = 3;

        public const PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES = 4;

        public const PREDEFINED_SEARCH_MY_REPORTED_ISSUES = 5;

        public const PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES = 11;

        /**
         * The name of the saved search
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The description of the saved search
         *
         * @var string
         * @Column(type="string", length=255)
         */
        protected $_description = null;

        /**
         * Whether the saved search is public or not
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_is_public = true;

        /**
         * The template used by the saved search
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_templatename = 'results_normal';

        /**
         * Template parameter used by the saved search
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_templateparameter;

        /**
         * Number of issues per page
         *
         * @var integer
         * @Column(type="integer", length=10, default=50)
         */
        protected $_issues_per_page = 50;

        /**
         * Quickfound issue
         *
         * @var Issue[]
         */
        protected $_quickfound_issues;

        /**
         * Search offset
         *
         * @var integer
         */
        protected $_offset = 0;

        /**
         * Custom search title
         *
         * @var string
         */
        protected $_searchtitle;

        /**
         * Sort fields
         *
         * @var string
         * @Column(type="string", length=400)
         */
        protected $_sortfields = "";

        /**
         * Columns
         *
         * @var string
         * @Column(type="string", length=600)
         */
        protected $_columns = "";

        /**
         * The grouping used by the saved search
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_groupby = 'issuetype';

        /**
         * The group order used by the saved search
         *
         * @var string
         * @Column(type="string", length=5)
         */
        protected $_grouporder = 'asc';

        /**
         * The project this saved search applies to
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_applies_to_project;

        /**
         * Who saved the search
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_uid;

        /**
         * An array of \pachno\core\entities\SearchFilters
         *
         * @var SearchFilter[]
         * @Relates(class="\pachno\core\entities\SearchFilter", collection=true, foreign_column="search_id")
         */
        protected $_filters;

        /**
         * An array of \pachno\core\entities\Issues
         *
         * @var Issue[]
         */
        protected $_issues;

        /**
         * The total number of issues found
         *
         * @var integer
         */
        protected $_total_number_of_issues;

        public static function getPredefinedSearchObject($predefined_search)
        {
            $search = new SavedSearch();
            $search->setPredefinedVariables($predefined_search);

            return $search;
        }

        public function setPredefinedVariables($type)
        {
            $i18n = framework\Context::getI18n();
            switch ($type) {
                case self::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES:
                    $this->_searchtitle = (framework\Context::isProjectContext()) ? $i18n->__('Open issues for %project_name', ['%project_name' => framework\Context::getCurrentProject()->getName()]) : $i18n->__('All open issues');
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS:
                    $this->_searchtitle = $i18n->__('Open issues for %project_name (including subprojects)', ['%project_name' => framework\Context::getCurrentProject()->getName()]);
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES:
                    $this->_searchtitle = (framework\Context::isProjectContext()) ? $i18n->__('Closed issues for %project_name', ['%project_name' => framework\Context::getCurrentProject()->getName()]) : $i18n->__('All closed issues');
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS:
                    $this->_searchtitle = $i18n->__('Closed issues for %project_name (including subprojects)', ['%project_name' => framework\Context::getCurrentProject()->getName()]);
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_PROJECT_WISHLIST:
                    $this->_searchtitle = $i18n->__('%project_name wishlist', ['%project_name' => framework\Context::getCurrentProject()->getName()]);
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO:
                    $this->_searchtitle = $i18n->__('Milestone todo-list for %project_name', ['%project_name' => framework\Context::getCurrentProject()->getName()]);
                    $this->_templatename = 'results_todo';
                    $this->_groupby = 'milestone';
                    break;
                case self::PREDEFINED_SEARCH_PROJECT_MOST_VOTED:
                    $this->_searchtitle = (framework\Context::isProjectContext()) ? $i18n->__('Most voted issues for %project_name', ['%project_name' => framework\Context::getCurrentProject()->getName()]) : $i18n->__('Most voted issues');
                    $this->_templatename = 'results_votes';
                    $this->_groupby = 'votes';
                    $this->_grouporder = 'desc';
                    $this->_issues_per_page = 100;
                    break;
                case self::PREDEFINED_SEARCH_MY_REPORTED_ISSUES:
                    $this->_searchtitle = $i18n->__('Issues reported by me');
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES:
                    $this->_searchtitle = $i18n->__('Open issues assigned to me');
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES:
                    $this->_searchtitle = $i18n->__('Open issues assigned to my teams');
                    $this->_groupby = 'issuetype';
                    break;
                case self::PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES:
                    $this->_searchtitle = $i18n->__('Open issues owned by me');
                    $this->_groupby = 'issuetype';
                    break;
            }
            $this->_filters = SearchFilter::getPredefinedFilters($type, $this);
        }

        /**
         * @param framework\Request $request
         *
         * @return SavedSearch
         */
        public static function getFromRequest(framework\Request $request)
        {
            $search = null;
            $search_id = ($request['saved_search_id']) ? $request['saved_search_id'] : $request['saved_search'];
            if ($search_id) {
                $search = tables\SavedSearches::getTable()->selectById($search_id);
            } else {
                $search = new SavedSearch();
                $search->setValuesFromRequest($request);
            }

            return $search;
        }

        public function setValuesFromRequest(framework\Request $request)
        {
            if ($request->hasParameter('predefined_search')) {
                $this->setPredefinedVariables($request['predefined_search']);
            } else {
                $this->_templatename = ($request->hasParameter('template') && self::isTemplateValid($request['template'])) ? $request['template'] : 'results_normal';
                $this->_templateparameter = $request['template_parameter'];

                $this->_issues_per_page = (in_array($request->getRequestedFormat(), ['csv', 'xlsx', 'ods'])) ? 0 : $request->getParameter('issues_per_page', 50);
                $this->_offset = $request->getParameter('offset', 0);

                if ($request['quicksearch']) {
                    $this->setSortFields([tables\Issues::LAST_UPDATED => 'desc']);

                    if ($request['term']) {
                        $request->setParameter('fs', ['text' => ['v' => $request['term'], 'o' => '=']]);
                    }
                }

                $this->_filters = SearchFilter::getFromRequest($request, $this);
                $this->_applies_to_project = framework\Context::getCurrentProject();
                $this->_columns = $request->getParameter('columns');
                $this->_sortfields = $request->getParameter('sortfields');

                $this->_groupby = $request['groupby'];
                $this->_grouporder = $request->getParameter('grouporder', 'asc');

                if (in_array($this->_templatename, ['results_userpain_singlepainthreshold', 'results_userpain_totalpainthreshold'])) {
                    $this->_searchtitle = framework\Context::getI18n()->__('Showing "bug report" issues sorted by user pain, threshold set at %threshold', ['%threshold' => $this->_templateparameter]);
                    $this->_issues_per_page = 0;
                    $this->_groupby = 'user_pain';
                    $this->_grouporder = 'desc';
                    $this->_filters['issuetype'] = SearchFilter::createFilter('issuetype', ['operator' => '=', 'value' => join(',', tables\IssueTypes::getTable()->getBugReportTypeIDs())]);
                } elseif ($this->_templatename == 'results_votes') {
                    $this->_searchtitle = framework\Context::getI18n()->__('Showing issues ordered by number of votes');
                    $this->_issues_per_page = $request->getParameter('issues_per_page', 100);
                    $this->_groupby = 'votes';
                    $this->_grouporder = 'desc';
                }
            }
            $this->_setupGenericFilters();
        }

        public static function isTemplateValid($template_name)
        {
            return array_key_exists($template_name, self::getTemplates(false));
        }

        public static function getTemplates($display_only = true)
        {
            $i18n = framework\Context::getI18n();
            $templates = [];
            $templates['results_normal'] = ['name' => 'results_normal', 'title' => $i18n->__('Standard'), 'icon' => 'list', 'description' => $i18n->__('Standard search results'), 'grouping' => true, 'parameter' => false];
            $templates['results_todo'] = ['name' => 'results_todo', 'title' => $i18n->__('Todo-list'), 'icon' => 'clipboard-check', 'description' => $i18n->__('Todo-list with progress indicator'), 'grouping' => false, 'parameter' => false];
            $templates['results_votes'] = ['name' => 'results_votes', 'title' => $i18n->__('Voting results'), 'icon' => 'vote-yea', 'description' => $i18n->__('Most voted-for issues'), 'grouping' => false, 'parameter' => false];
            $templates['results_userpain_singlepainthreshold'] = ['name' => 'results_userpain_singlepainthreshold', 'title' => $i18n->__('User pain with threshold'), 'icon' => 'meh', 'description' => $i18n->__('User pain indicator with custom single bug pain threshold'), 'grouping' => false, 'parameter' => true, 'parameter_header' => $i18n->__('User pain threshold'), 'parameter_text' => $i18n->__('Pain threshold (0 - 100)')];
            if (!$display_only) {
                $templates['results_rss'] = $i18n->__('RSS feed');
            }

            return $templates;
        }

        public static function getTemplate($template)
        {
            return self::getTemplates(true)[$template];
        }

        /**
         * @return Project
         */
        public function getProject()
        {
            return $this->getAppliesToProject();
        }

        /**
         * @return Project
         */
        public function getAppliesToProject()
        {
            return $this->_b2dbLazyLoad('_applies_to_project');
        }

        /**
         * @param Project $applies_to_project
         */
        public function setAppliesToProject($applies_to_project)
        {
            $this->_applies_to_project = $applies_to_project;
        }

        /**
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        public function isPublic()
        {
            return $this->getIsPublic();
        }

        /**
         * @return boolean
         */
        public function getIsPublic()
        {
            return $this->_is_public;
        }

        /**
         * @param boolean $is_public
         */
        public function setIsPublic($is_public)
        {
            $this->_is_public = $is_public;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        public function setUser($user)
        {
            $this->setUid($user);
        }

        /**
         * @param User $uid
         */
        public function setUid($uid)
        {
            $this->_uid = $uid;
        }

        public function getUserID()
        {
            $user = $this->getUser();

            return ($user instanceof User) ? $user->getID() : 0;
        }

        /**
         * @return User
         */
        public function getUser()
        {
            return $this->_b2dbLazyLoad('_uid');
        }

        public function getFilter($key)
        {
            return ($this->hasFilter($key)) ? $this->_filters[$key] : null;
        }

        public function hasFilter($key)
        {
            return isset($this->_filters[$key]);
        }

        public function setFilter($key, $value)
        {
            $this->_filters[$key] = $value;
        }

        public function getTitle()
        {
            return (isset($this->_searchtitle)) ? $this->_searchtitle : $this->_name;
        }

        public function addSortField($field, $sort_order)
        {
            $this->_initializeSortFields();
            $this->_sortfields[$field] = $sort_order;
        }

        /**
         * @return array
         */
        public function getSortDirection($field)
        {
            $this->_initializeSortFields();

            return (isset($this->_sortfields[$field])) ? $this->_sortfields[$field] : null;
        }

        public function addColumns($column)
        {
            $this->_initializeColumns();
            $this->_columns[] = $column;
        }

        /**
         * @param bool $include_deleted
         *
         * @return Issue[]
         */
        public function getIssues($include_deleted = false)
        {
            if ($this->hasQuickfoundIssues()) {
                return $this->_quickfound_issues;
            }

            if ($this->_issues === null) {
                $this->_performSearch($include_deleted);
            }

            return $this->_issues;
        }

        public function hasQuickfoundIssues()
        {
            if ($this->_quickfound_issues === null) {
                $this->_quickfound_issues = [];
                if ($this->getSearchterm()) {
                    preg_replace_callback(TextParser::getIssueRegex(), [$this, 'extractIssues'], $this->getSearchterm());
                }
            }
            if (!count($this->_quickfound_issues)) {
                $issue = Issue::getIssueFromLink($this->getSearchterm());
                if ($issue instanceof Issue) {
                    $this->_quickfound_issues[] = $issue;
                }
            }

            return (bool)count($this->_quickfound_issues);
        }

        public function getSearchterm()
        {
            $filters = $this->getFilters();

            return (array_key_exists('text', $filters)) ? $filters['text']->getValue() : null;
        }

        protected function _performSearch($include_deleted = false)
        {
            list ($this->_issues, $this->_total_number_of_issues) = Issue::findIssues($this->getFilters(), $this->getIssuesPerPage(), $this->getOffset(), $this->getGroupby(), $this->getGrouporder(), $this->getSortFields(), $include_deleted);
        }

        /**
         * @return int
         */
        public function getIssuesPerPage()
        {
            return $this->_issues_per_page;
        }

        /**
         * @param int $issues_per_page
         */
        public function setIssuesPerPage($issues_per_page)
        {
            $this->_issues_per_page = $issues_per_page;
        }

        /**
         * @return int
         */
        public function getOffset()
        {
            return $this->_offset;
        }

        /**
         * @param int $offset
         */
        public function setOffset($offset)
        {
            $this->_offset = $offset;
        }

        /**
         * @return string
         */
        public function getGroupby()
        {
            return $this->_groupby;
        }

        /**
         * @param string $groupby
         */
        public function setGroupby($groupby)
        {
            $this->_groupby = $groupby;
        }

        /**
         * @return string
         */
        public function getGrouporder()
        {
            return $this->_grouporder;
        }

        /**
         * @param string $grouporder
         */
        public function setGrouporder($grouporder)
        {
            $this->_grouporder = $grouporder;
        }

        public function getNumberOfIssues()
        {
            if ($this->_issues === null) {
                $this->_performSearch();
            }

            return count($this->_issues);
        }

        public function extractIssues($matches)
        {
            $issue = Issue::getIssueFromLink($matches["issues"]);
            if ($issue instanceof Issue) {
                if (!framework\Context::isProjectContext() || (framework\Context::isProjectContext() && $issue->getProjectID() == framework\Context::getCurrentProject()->getID())) {
                    $this->_quickfound_issues[] = $issue;
                }
            }
        }

        public function getQuickfoundIssues()
        {
            return $this->_quickfound_issues;
        }

        public function hasPagination()
        {
            return ($this->getIssuesPerPage() > 0);
        }

        public function needsPagination()
        {
            return ($this->getTotalNumberOfIssues() > $this->getIssuesPerPage());
        }

        public function getTotalNumberOfIssues()
        {
            if ($this->hasQuickfoundIssues()) {
                return count($this->_quickfound_issues);
            }

            if ($this->_total_number_of_issues === null) {
                $this->_performSearch();
            }

            return $this->_total_number_of_issues;
        }

        public function getCurrentPage()
        {
            return ceil($this->getOffset() / $this->getIssuesPerPage()) + 1;
        }

        public function getNumberOfPages()
        {
            return ceil($this->getTotalNumberOfIssues() / $this->getIssuesPerPage());
        }

        public function getParametersAsString()
        {
            return join('&', $this->getParameters());
        }

        public function getParameters()
        {
            $parameters = [];
            foreach ($this->getFilters() as $key => $filter) {
                if (is_array($filter)) {
                    foreach ($filter as $subkey => $subfilter) {
                        if (is_array($subfilter)) {
                            foreach ($subfilter as $subsubkey => $subsubfilter) {
                                $parameters[] = "fs[{$key}][{$subkey}][{$subsubkey}]=" . urlencode($subsubfilter['value']);
                            }
                        } else {
                            $parameters[] = "fs[{$key}][{$subkey}]=" . urlencode($subfilter['value']);
                        }
                    }
                } else {
                    $parameters[] = "fs[{$key}][o]=" . urlencode($filter['operator']);
                    $parameters[] = "fs[{$key}][v]=" . urlencode($filter['value']);
                }
            }
            $parameters[] = 'template=' . $this->getTemplateName();
            $parameters[] = 'template_parameter=' . $this->getTemplateParameter();
            $parameters[] = 'searchterm=' . $this->getSearchterm();
            $parameters[] = 'groupby=' . $this->getGroupby();
            $parameters[] = 'grouporder=' . $this->getGrouporder();
            $parameters[] = 'issues_per_page=' . $this->getIssuesPerPage();

            return $parameters;
        }

        /**
         * @return string
         */
        public function getTemplateParameter()
        {
            return $this->_templateparameter;
        }

        /**
         * @param string $template_parameter
         */
        public function setTemplateParameter($template_parameter)
        {
            $this->_templateparameter = $template_parameter;
        }

        protected function _preSave(bool $is_new): void
        {
            parent::_preSave($is_new);
            $this->_sortfields = $this->getSortFieldsAsString();
            $this->_columns = join(',', $this->getColumns());
        }

        public function getSortFieldsAsString()
        {
            $strings = [];
            foreach ($this->getSortFields() as $field => $sort) {
                $strings[] = "{$field}={$sort}";
            }

            return join(',', $strings);
        }

        /**
         * @return array
         */
        public function getSortFields()
        {
            $this->_initializeSortFields();

            return $this->_sortfields;
        }

        /**
         * @param array $fields
         */
        public function setSortFields($fields)
        {
            $this->_sortfields = $fields;
        }

        protected function _initializeSortFields()
        {
            if (!is_array($this->_sortfields)) {
                if (!strlen($this->_sortfields)) {
                    $this->_sortfields = [tables\Issues::LAST_UPDATED => 'desc'];
                } else {
                    $fields = explode(',', $this->_sortfields);
                    $this->_sortfields = [];
                    foreach ($fields as $field) {
                        $sort_details = explode('=', $field);
                        $this->_sortfields[$sort_details[0]] = $sort_details[1];
                    }
                }
            }
        }

        /**
         * @return array
         */
        public function getColumns()
        {
            $this->_initializeColumns();

            return array_values($this->_columns);
        }

        /**
         * @param string $columns Comma-separated list of columns to display
         */
        public function setColumns($columns)
        {
            $this->_columns = $columns;
        }

        protected function _initializeColumns()
        {
            if (!is_array($this->_columns)) {
                if (!strlen($this->_columns)) {
                    if ($columns = Settings::get('search_scs_' . $this->getTemplateName(), 'core', null, framework\Context::getUser()->getID()))
                        $this->_columns = explode(',', $columns);
                    else
                        $this->_columns = self::getDefaultVisibleColumns();
                } else {
                    $this->_columns = explode(',', $this->_columns);
                }
            }
        }

        /**
         * @return string
         */
        public function getTemplateName()
        {
            return $this->_templatename;
        }

        /**
         * @param string $template_name
         */
        public function setTemplateName($template_name)
        {
            $this->_templatename = self::isTemplateValid($template_name) ? $template_name : 'results_normal';
        }

        public static function getDefaultColumns()
        {
            return ['title', 'issuetype', 'assigned_to', 'status', 'resolution', 'category', 'severity', 'percent_complete', 'reproducability', 'priority', 'components', 'milestone', 'estimated_time', 'spent_time', 'last_updated', 'comments'];
        }

        public static function getAvailableColumns()
        {
            $default_columns = self::getDefaultColumns();
            $custom_columns = array_map(function ($customfield) { return $customfield->getKey(); }, CustomDatatype::getAll());

            return array_merge($default_columns, $custom_columns);
        }

        public static function getDefaultVisibleColumns()
        {
            return ['title', 'assigned_to', 'status', 'resolution', 'last_updated', 'comments'];
        }

        protected function _postSave(bool $is_new): void
        {
            foreach ($this->getFilters() as $filter) {
                $filter->setSearchId($this);
                $filter->save();
            }
        }

        /**
         * Return current filters
         *
         * @return array
         */
        public function getFilters()
        {
            if ($this->_filters === null) {
                $filters = [];
                $this->_b2dbLazyLoad('_filters');
                foreach ($this->_filters as $filter) {
                    $filters[$filter->getFilterKey()] = $filter;
                }
                $this->_filters = $filters;
                $this->_setupGenericFilters();
            }

            return $this->_filters;
        }

        protected function _setupGenericFilters()
        {
            if (!isset($this->_filters['issuetype'])) $this->_filters['issuetype'] = SearchFilter::createFilter('issuetype', [], $this);
            if (!isset($this->_filters['status'])) $this->_filters['status'] = SearchFilter::createFilter('status', [], $this);
            if (!isset($this->_filters['category'])) $this->_filters['category'] = SearchFilter::createFilter('category', [], $this);
            if (!framework\Context::isProjectContext() && !isset($this->_filters['project_id'])) $this->_filters['project_id'] = SearchFilter::createFilter('project_id', [], $this);
        }

    }
