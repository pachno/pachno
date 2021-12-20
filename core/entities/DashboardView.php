<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\DashboardViews;
    use pachno\core\framework as framework;

    /**
     * Dashboard class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Dashboard class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\DashboardViews")
     */
    class DashboardView extends IdentifiableScoped
    {

        public const VIEW_PREDEFINED_SEARCH = 1;

        public const VIEW_SAVED_SEARCH = 2;

        public const VIEW_LOGGED_ACTIONS = 3;

        public const VIEW_RECENT_COMMENTS = 4;

        public const VIEW_TIMERS = 5;

        public const VIEW_PROJECTS = 6;

        public const VIEW_MILESTONES = 7;

        public const VIEW_PROJECT_INFO = 101;

        public const VIEW_PROJECT_TEAM = 102;

        public const VIEW_PROJECT_CLIENT = 103;

        public const VIEW_PROJECT_SUBPROJECTS = 104;

        public const VIEW_PROJECT_STATISTICS_LAST15 = 105;

        public const VIEW_PROJECT_STATISTICS_PRIORITY = 106;

        public const VIEW_PROJECT_STATISTICS_STATUS = 111;

        public const VIEW_PROJECT_STATISTICS_WORKFLOW_STEP = 115;

        public const VIEW_PROJECT_STATISTICS_RESOLUTION = 112;

        public const VIEW_PROJECT_STATISTICS_STATE = 113;

        public const VIEW_PROJECT_STATISTICS_CATEGORY = 114;

        public const VIEW_PROJECT_STATISTICS_SEVERITY = 116;

        public const VIEW_PROJECT_RECENT_ISSUES = 107;

        public const VIEW_PROJECT_RECENT_ACTIVITIES = 108;

        public const VIEW_PROJECT_UPCOMING = 109;

        public const VIEW_PROJECT_DOWNLOADS = 110;

        public const TYPE_USER = 1;

        public const TYPE_PROJECT = 2;

        public const TYPE_TEAM = 3;

        public const TYPE_CLIENT = 4;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_column = 1;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_view;

        /**
         * @var Dashboard
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Dashboard")
         */
        protected $_dashboard_id;

        public static function getUserViews($user_id)
        {
            return DashboardViews::getTable()->getViews($user_id, self::TYPE_USER);
        }

        public static function getProjectViews($project_id)
        {
            return DashboardViews::getTable()->getViews($project_id, self::TYPE_PROJECT);
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

        public function setType($type)
        {
            $this->_name = $type;
        }

        public function setDetail($detail)
        {
            $this->_view = $detail;
        }

        public function getProjectID()
        {
            return $this->getProject()->getID();
        }

        /**
         * @return Project
         */
        public function getProject()
        {
            return $this->getDashboard()->getProject();
        }

        /**
         * @return Dashboard
         */
        public function getDashboard()
        {
            return $this->_b2dbLazyLoad('_dashboard_id');
        }

        public function isSearchView()
        {
            return (in_array($this->getType(), [
                self::VIEW_PREDEFINED_SEARCH,
                self::VIEW_SAVED_SEARCH
            ]));
        }

        public function getType()
        {
            return $this->_name;
        }

        public function hasRSS()
        {
            return (in_array($this->getType(), [
                self::VIEW_PREDEFINED_SEARCH,
                self::VIEW_SAVED_SEARCH,
                self::VIEW_PROJECT_RECENT_ACTIVITIES
            ]));
        }

        public function hasJS()
        {
            return (in_array($this->getType(), [
                self::VIEW_PROJECT_STATISTICS_LAST15,
            ]));
        }

        public function getJS()
        {
            return ['excanvas', 'jquery.flot', 'jquery.flot.resize', 'jquery.flot.time'];
        }

        public function getRSSUrl()
        {
            switch ($this->getType()) {
                case self::VIEW_PREDEFINED_SEARCH:
                case self::VIEW_SAVED_SEARCH:
                    return framework\Context::getRouting()->generate('search', $this->getSearchParameters(true));
                    break;
                case self::VIEW_PROJECT_RECENT_ACTIVITIES:
                    return framework\Context::getRouting()->generate('project_timeline', ['project_key' => $this->getProject()->getKey(), 'format' => 'rss']);
                    break;
            }
        }

        public function getSearchParameters($rss = false)
        {
            $parameters = ($rss) ? ['format' => 'rss'] : [];
            switch ($this->getType()) {
                case self::VIEW_PREDEFINED_SEARCH :
                    $parameters['predefined_search'] = $this->getDetail();
                    break;
                case self::VIEW_SAVED_SEARCH :
                    $parameters['saved_search'] = $this->getDetail();
                    break;
            }

            return $parameters;
        }

        public function getDetail()
        {
            return $this->_view;
        }

        public function shouldBePreloaded()
        {
            return in_array($this->getType(), [
                self::VIEW_PROJECT_DOWNLOADS,
                self::VIEW_PROJECT_INFO,
                self::VIEW_PROJECT_UPCOMING
            ]);
        }

        public function isTransparent()
        {
            return in_array($this->getType(), [self::VIEW_PROJECTS]);
        }

        /**
         * Return whether or not this dashboard view has a title header
         *
         * @return bool
         */
        public function hasTitle(): bool
        {
            foreach (self::getAvailableViews($this->getTargetType()) as $type => $views) {
                if (array_key_exists($this->getType(), $views) && array_key_exists($this->getDetail(), $views[$this->getType()])) {
                    return $views[$this->getType()][$this->getDetail()]['has_title'] ?? true;
                    break;
                }
            }

            return false;
        }

        public static function getAvailableViews($target_type)
        {
            $i18n = framework\Context::getI18n();
            $searches = ['info' => [], 'searches' => []];
            switch ($target_type) {
                case self::TYPE_USER:
                    $searches['info'][self::VIEW_LOGGED_ACTIONS] = [0 => ['title' => $i18n->__("What you've done recently"), 'description' => $i18n->__('A widget that shows your most recent actions, such as issue edits, wiki edits and more')]];
                    $searches['info'][self::VIEW_RECENT_COMMENTS] = [0 => ['title' => $i18n->__('Recent comments'), 'description' => $i18n->__('Shows a list of your most recent comments')]];
                    $searches['searches'][self::VIEW_PREDEFINED_SEARCH] = [SavedSearch::PREDEFINED_SEARCH_MY_REPORTED_ISSUES => ['title' => $i18n->__('Issues reported by me'), 'description' => $i18n->__('Shows a list of all issues you have reported, across all projects')],
                        SavedSearch::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES => ['title' => $i18n->__('Open issues assigned to me'), 'description' => $i18n->__('Shows a list of all issues assigned to you')],
                        SavedSearch::PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES => ['title' => $i18n->__('Open issues owned by me'), 'description' => $i18n->__('Shows a list of all issues owned by you')],
                        SavedSearch::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES => ['title' => $i18n->__('Open issues assigned to my teams'), 'description' => $i18n->__('Shows all issues assigned to any of your teams')]];
                    $searches['info'][self::VIEW_PROJECTS] = [0 => ['title' => $i18n->__("Your projects"), 'description' => $i18n->__('A widget that shows projects you are involved in')]];
                    $searches['info'][self::VIEW_MILESTONES] = [0 => ['title' => $i18n->__("Upcoming milestones / sprints"), 'description' => $i18n->__('A widget that shows all upcoming milestones or sprints for any projects you are involved in')]];
                    $searches['info'][self::VIEW_TIMERS] = [0 => ['title' => $i18n->__("Active timers"), 'description' => $i18n->__('A widget that shows all ongoing issue timers')]];
                    break;
                case self::TYPE_PROJECT:
                    $searches['statistics'] = [];
                    $issuetype_icons = [];
                    framework\Context::loadLibrary('ui');
                    foreach (Issuetype::getAll() as $id => $issuetype) {
                        $issuetype_icons[$id] = [
                            'title' => $i18n->__('Recent %issuetype issues', ['%issuetype' => '<span class="issuetype-icon issuetype-' . $issuetype->getIcon() . '">' . fa_image_tag($issuetype->getFontAwesomeIcon()) . $issuetype->getName() . '</span>']),
                            'description' => $i18n->__('Show recent issues of type %issuetype', ['%issuetype' => $issuetype->getName()])
                        ];
                    }

                    $searches['info'][self::VIEW_PROJECT_INFO] = [0 => ['title' => $i18n->__('About this project'), 'has_title' => false, 'description' => $i18n->__('Basic project information widget, showing project name, important people and links')]];
                    $searches['info'][self::VIEW_PROJECT_TEAM] = [0 => ['title' => $i18n->__('Project team'), 'description' => $i18n->__('A widget with information about project developers and the project team and their respective project roles')]];
                    $searches['info'][self::VIEW_PROJECT_CLIENT] = [0 => ['title' => $i18n->__('Project client'), 'description' => $i18n->__('Shows information about the associated project client (if any)')]];
                    $searches['info'][self::VIEW_PROJECT_SUBPROJECTS] = [0 => ['title' => $i18n->__('Subprojects'), 'description' => $i18n->__('Lists all subprojects of this project, with quick links to report an issue, open the project wiki and more')]];
                    $searches['info'][self::VIEW_PROJECT_RECENT_ACTIVITIES] = [0 => ['title' => $i18n->__('Recent activities'), 'description' => $i18n->__('Displays project timeline')]];
                    $searches['info'][self::VIEW_PROJECT_UPCOMING] = [0 => ['title' => $i18n->__('Upcoming milestones and deadlines'), 'description' => $i18n->__('A widget showing a list of upcoming milestones and deadlines for the next three weeks')]];
                    $searches['info'][self::VIEW_PROJECT_DOWNLOADS] = [0 => ['title' => $i18n->__('Latest downloads'), 'description' => $i18n->__('Lists recent downloads released in the release center')]];
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_LAST15] = [0 => ['title' => $i18n->__('Graph of closed vs open issues'), 'description' => $i18n->__('Shows a line graph comparing closed vs open issues for the past 15 days')]];
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_PRIORITY] = [0 => ['title' => $i18n->__('Statistics by priority'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by priority')]];
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_SEVERITY] = [0 => ['title' => $i18n->__('Statistics by severity'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by severity')]];
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_CATEGORY] = [0 => ['title' => $i18n->__('Statistics by category'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by category')]];
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_STATUS] = [0 => ['title' => $i18n->__('Statistics by status'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by status')]];
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_RESOLUTION] = [0 => ['title' => $i18n->__('Statistics by resolution'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by resolution')]];
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP] = [0 => ['title' => $i18n->__('Statistics by workflow step'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by current workflow step')]];
                    $searches['searches'][self::VIEW_PROJECT_RECENT_ISSUES] = $issuetype_icons;
                    break;
            }

            return $searches;
        }

        public function getTargetType()
        {
            if ($this->getDashboard()->getUser() instanceof User)
                return self::TYPE_USER;
            if ($this->getDashboard()->getProject() instanceof Project)
                return self::TYPE_PROJECT;
            if ($this->getDashboard()->getTeam() instanceof Team)
                return self::TYPE_TEAM;
            if ($this->getDashboard()->getClient() instanceof Client)
                return self::TYPE_CLIENT;
        }

        public function getTitle()
        {
            $title = framework\Context::getI18n()->__('Unknown dashboard item');

            if ($this->getType() == self::VIEW_SAVED_SEARCH) {
                $search = tables\SavedSearches::getTable()->selectById($this->getDetail());

                if ($search instanceof SavedSearch) $title = $search->getName();
            } else {
                foreach (self::getAvailableViews($this->getTargetType()) as $type => $views) {
                    if (array_key_exists($this->getType(), $views) && array_key_exists($this->getDetail(), $views[$this->getType()])) {
                        $title = $views[$this->getType()][$this->getDetail()]['title'];
                        break;
                    }
                }
            }

            return $title;
        }

        public function hasHeaderButton()
        {
            switch ($this->getType()) {
                case self::VIEW_PROJECTS:
                case self::VIEW_PREDEFINED_SEARCH:
                case self::VIEW_SAVED_SEARCH:
                case self::VIEW_PROJECT_TEAM:
                case self::VIEW_PROJECT_UPCOMING:
                case self::VIEW_PROJECT_RECENT_ACTIVITIES:
                case self::VIEW_PROJECT_DOWNLOADS:
                case self::VIEW_PROJECT_RECENT_ISSUES:
                    return true;
                default:
                    return false;
            }
        }

        public function getHeaderButton()
        {
            $user = framework\Context::getUser();
            $project = framework\Context::getCurrentProject();

            switch ($this->getType()) {
                case self::VIEW_PROJECTS:
                    if ($user->canCreateProjects() || $user->canSaveConfiguration()) {
                        return '<button class="button secondary highlight" onclick="Pachno.UI.Backdrop.show(\'' . make_url('get_partial_for_backdrop', ['key' => 'project_config']) . '\');">' . fa_image_tag('plus-square') . '<span>' . __('Create a project') . '</span></button>';
                    }
                    break;
                case self::VIEW_PROJECT_DOWNLOADS:
                    if ($user->canEditProjectDetails($project) && $project->isBuildsEnabled()) {
                        return '<a href="' . make_url('project_releases', ['project_key' => $project->getKey()]) . '" class="button secondary">' . fa_image_tag('cloud-download-alt', ['class' => 'icon']) . '<span>' . __('Manage releases') . '</span></a>';
                    }
                    break;
                case self::VIEW_PROJECT_TEAM:
                    if ($user->canEditProjectDetails($project)) {
                        return '<a href="' . make_url('project_settings', ['project_key' => $project->getKey()]) . '" class="button secondary">' . fa_image_tag('users', ['class' => 'icon']) . '<span>' . __('Set up project team') . '</span></a>';
                    }
                    break;
                case self::VIEW_PROJECT_UPCOMING:
                    return '<a href="' . make_url('project_roadmap', ['project_key' => $project->getKey()]) . '" class="button secondary">' . fa_image_tag('tasks', ['class' => 'icon']) . '<span>' . __('Open project roadmap') . '</span></a>';
                case self::VIEW_PROJECT_RECENT_ACTIVITIES:
                    return '<a href="' . make_url('project_timeline', ['project_key' => $project->getKey()]) . '" class="button secondary">' . fa_image_tag('stream', ['class' => 'icon']) . '<span>' . __('Show complete timeline') . '</span></a>';
                case self::VIEW_PROJECT_RECENT_ISSUES:
                    return '<a href="' . make_url('project_issues', ['project_key' => $project->getKey()]) . '" class="button secondary">' . fa_image_tag('search', ['class' => 'icon']) . '<span>' . __('Open project issues search') . '</span></a>';
            }

            return '';
        }

        public function setDashboard($dashboard)
        {
            $this->_dashboard_id = $dashboard;
        }

        public function getTemplate()
        {
            switch ($this->getType()) {
                case self::VIEW_PREDEFINED_SEARCH:
                case self::VIEW_SAVED_SEARCH:
                    return 'search/results_view';
                case self::VIEW_PROJECT_INFO:
                    return 'project/dashboardviewprojectinfo';
                case self::VIEW_PROJECT_TEAM:
                    return 'project/dashboardviewprojectteam';
                case self::VIEW_PROJECT_CLIENT:
                    return 'project/dashboardviewprojectclient';
                case self::VIEW_PROJECT_SUBPROJECTS:
                    return 'project/dashboardviewprojectsubprojects';
                case self::VIEW_PROJECT_STATISTICS_LAST15:
                    return 'project/dashboardviewprojectstatisticslast15';
                case self::VIEW_PROJECT_RECENT_ISSUES:
                    return 'project/dashboardviewprojectrecentissues';
                case self::VIEW_PROJECT_RECENT_ACTIVITIES:
                    return 'project/dashboardviewprojectrecentactivities';
                case self::VIEW_PROJECT_STATISTICS_CATEGORY:
                case self::VIEW_PROJECT_STATISTICS_PRIORITY:
                case self::VIEW_PROJECT_STATISTICS_SEVERITY:
                case self::VIEW_PROJECT_STATISTICS_RESOLUTION:
                case self::VIEW_PROJECT_STATISTICS_STATE:
                case self::VIEW_PROJECT_STATISTICS_STATUS:
                case self::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP:
                    return 'project/dashboardviewprojectstatistics';
                case self::VIEW_PROJECT_UPCOMING:
                    return 'project/dashboardviewprojectupcoming';
                case self::VIEW_PROJECT_DOWNLOADS:
                    return 'project/dashboardviewprojectdownloads';
                case self::VIEW_RECENT_COMMENTS:
                    return 'main/dashboardviewrecentcomments';
                case self::VIEW_LOGGED_ACTIONS:
                    return 'main/dashboardviewloggedactions';
                case self::VIEW_MILESTONES:
                    return 'main/dashboardviewusermilestones';
                case self::VIEW_PROJECTS:
                    return 'main/dashboardviewuserprojects';
                case self::VIEW_TIMERS:
                    return 'main/dashboardviewtimers';
            }
        }

        public function getColumn()
        {
            return $this->_column;
        }

        public function setColumn($column)
        {
            $this->_column = $column;
        }

        public function getSortOrder()
        {
            return $this->_sort_order;
        }

        public function setSortOrder($sort_order)
        {
            $this->_sort_order = $sort_order;
        }

    }
