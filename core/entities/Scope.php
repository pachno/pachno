<?php

    namespace pachno\core\entities;

    use pachno\core\entities\tables\Files;
    use pachno\core\entities\tables\IssueFields,
        pachno\core\entities\tables\Links,
        pachno\core\entities\tables\Scopes,
        pachno\core\entities\tables\ScopeHostnames,
        pachno\core\entities\tables\Settings,
        pachno\core\entities\tables\Users,
        pachno\core\entities\tables\WorkflowIssuetype,
        pachno\core\entities\common\Identifiable;
    use pachno\core\framework;

    /**
     * The scope class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * The scope class
     *
     * @package pachno
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Scopes")
     */
    class Scope extends Identifiable
    {

        protected static $_scopes = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = '';

        /**
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enabled = false;

        /**
         * @var string
         */
        protected $_shortname = '';

        protected $_administrator = null;

        protected $_hostnames = null;

        protected $_is_secure = false;

        protected $_used_storage = null;

        /**
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_uploads_enabled = true;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_upload_limit = 0;

        /**
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_custom_workflows_enabled = true;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_workflows = 0;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_users = 0;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_projects = 0;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_teams = 0;

        /**
         * @Relates(class="\pachno\core\entities\Project", collection=true, foreign_column="scope")
         */
        protected $_projects = null;

        /**
         * @Relates(class="\pachno\core\entities\Issue", collection=true, foreign_column="scope")
         */
        protected $_issues = null;

        /**
         * Return all available scopes
         *
         * @return \pachno\core\entities\Scope[]
         */
        static function getAll()
        {
            if (self::$_scopes === null)
            {
                self::$_scopes = tables\Scopes::getTable()->selectAll();
            }

            return self::$_scopes;
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

        public function isEnabled()
        {
            return $this->_enabled;
        }

        public function isDefault()
        {
            return in_array('*', $this->getHostnames());
        }

        public function setEnabled($enabled = true)
        {
            $this->_enabled = (bool) $enabled;
        }

        public function getDescription()
        {
            return $this->_description;
        }

        public function setDescription($description)
        {
            $this->_description = $description;
        }

        protected function _populateHostnames()
        {
            if ($this->_hostnames === null)
            {
                if ($this->_id)
                    $this->_hostnames = tables\ScopeHostnames::getTable()->getHostnamesForScope($this->getID());
                else
                    $this->_hostnames = array();
            }
        }

        public function getHostnames()
        {
            $this->_populateHostnames();
            return $this->_hostnames;
        }

        public function addHostname($hostname)
        {
            $hostname = trim($hostname, "/");
            $this->_populateHostnames();
            $this->_hostnames[] = $hostname;
        }

        /**
         * Returns the scope administrator
         *
         * @return \pachno\core\entities\User
         */
        public function getScopeAdmin()
        {
            if (!$this->_administrator instanceof \pachno\core\entities\User && $this->_administrator != 0)
            {
                try
                {
                    $this->_administrator = tables\Users::getTable()->selectById($this->_administrator);
                }
                catch (\Exception $e) { }
            }
            return $this->_administrator;
        }

        protected function _preDelete()
        {
            $tables = array(
                '\pachno\core\entities\tables\IssueCustomFields',
                '\pachno\core\entities\tables\IssueAffectsEdition',
                '\pachno\core\entities\tables\IssueAffectsBuild',
                '\pachno\core\entities\tables\IssueAffectsComponent',
                '\pachno\core\entities\tables\IssueFiles',
                '\pachno\core\entities\tables\IssueRelations',
                '\pachno\core\entities\tables\IssuetypeSchemeLink',
                '\pachno\core\entities\tables\IssuetypeSchemes',
                '\pachno\core\entities\tables\IssueTypes',
                '\pachno\core\entities\tables\ListTypes',
                '\pachno\core\entities\tables\Issues',
                '\pachno\core\entities\tables\Comments',
                '\pachno\core\entities\tables\ProjectAssignedTeams',
                '\pachno\core\entities\tables\ProjectAssignedUsers',
                '\pachno\core\entities\tables\Components',
                '\pachno\core\entities\tables\Editions',
                '\pachno\core\entities\tables\Builds',
                '\pachno\core\entities\tables\Files',
                '\pachno\core\entities\tables\Milestones',
                '\pachno\core\entities\tables\Issues',
                '\pachno\core\entities\tables\Projects',
                '\pachno\core\entities\tables\UserScopes',
                '\pachno\core\entities\tables\Dashboards',
                '\pachno\core\entities\tables\DashboardViews',
                '\pachno\core\entities\tables\ScopeHostnames',
                '\pachno\core\entities\tables\Settings'
            );
            foreach($tables as $table)
            {
                $table::getTable()->deleteFromScope($this->getID());
            }
        }

        protected function _postSave($is_new)
        {
            tables\ScopeHostnames::getTable()->saveScopeHostnames($this->getHostnames(), $this->getID());
            // Load fixtures for this scope if it's a new scope
            if ($is_new)
            {
                if (!$this->isDefault())
                {
                    $prev_scope = framework\Context::getScope();
                    framework\Context::setScope($this);
                }
                $this->loadFixtures();
                if (!$this->isDefault())
                {
                    Module::installModule('publish', $this);
                    framework\Context::setScope($prev_scope);
                    framework\Context::clearPermissionsCache();
                }
            }
        }

        public function _construct(\b2db\Row $row, $foreign_key = null)
        {
            if (framework\Context::isCLI())
            {
                $this->_hostname = php_uname('n');
            }
            else
            {
                $hostprefix = (!array_key_exists('HTTPS', $_SERVER) || $_SERVER['HTTPS'] == '' || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';

                if (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)) {
                    $hostprefix = $_SERVER["HTTP_X_FORWARDED_PROTO"];
                }

                $this->_is_secure = (bool) ($hostprefix == 'https');
                if(isset($_SERVER["HTTP_X_FORWARDED_HOST"]) && $_SERVER["HTTP_X_FORWARDED_HOST"]!="")
                {
                   $this->_hostname = "{$hostprefix}://{$_SERVER["HTTP_X_FORWARDED_HOST"]}";
                }
                else
                {
                    $this->_hostname = "{$hostprefix}://{$_SERVER['SERVER_NAME']}";
                }
                $port = $_SERVER['SERVER_PORT'];
                if ($port != 80)
                {
                    $this->_hostname .= ":{$port}";
                }
            }
        }

        public function isSecure()
        {
            return $this->_is_secure;
        }

        public function getCurrentHostname($clean = false)
        {
            if ($clean)
            {
                // a scheme is needed before php 5.4.7
                // thus, let's add the prefix http://
                if (!stristr($this->_hostname,'http'))
                {
                    $url = parse_url('http://'.$this->_hostname);
                }
                else
                {
                    $url = parse_url($this->_hostname);
                }
                return $url['host'];
            }
            return $this->_hostname;
        }

        public function loadFixtures()
        {
            // Load initial settings
            tables\Settings::getTable()->loadFixtures($this);
            \pachno\core\framework\Settings::loadSettings();

            // Load group, users and permissions fixtures
            Group::loadFixtures($this);

            // Load initial teams
            Team::loadFixtures($this);

            // Set up user states, like "available", "away", etc
            Userstate::loadFixtures($this);

            // Set up data types
            list($bug_report_id, $feature_req_id, $enhancement_id, $task_id, $user_story_id, $idea_id, $epic_id) = Issuetype::loadFixtures($this);
            list($full_range_scheme, $balanced_scheme, $balanced_agile_scheme, $simple_scheme) = IssuetypeScheme::loadFixtures($this, [$bug_report_id, $feature_req_id, $enhancement_id, $task_id, $user_story_id, $idea_id, $epic_id]);
            tables\IssueFields::getTable()->loadFixtures($this, $full_range_scheme, $balanced_scheme, $balanced_agile_scheme, $simple_scheme, $bug_report_id, $feature_req_id, $enhancement_id, $task_id, $user_story_id, $idea_id, $epic_id);
            Datatype::loadFixtures($this);

            // Set up workflows
            list ($multi_team_workflow, $balanced_workflow, $simple_workflow) = Workflow::loadFixtures($this);
            list ($multi_team_workflow_scheme, $balanced_workflow_scheme, $simple_workflow_scheme) = WorkflowScheme::loadFixtures($this);

            tables\WorkflowIssuetype::getTable()->loadFixtures($this, $multi_team_workflow, $multi_team_workflow_scheme);
            tables\WorkflowIssuetype::getTable()->loadFixtures($this, $balanced_workflow, $balanced_workflow_scheme);
            tables\WorkflowIssuetype::getTable()->loadFixtures($this, $simple_workflow, $simple_workflow_scheme);

            // Set up left menu links
            tables\Links::getTable()->loadFixtures($this);

            framework\Context::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $this->getID());
            framework\Context::getModule('publish')->saveSetting('allow_camelcase_links', 1);
            framework\Context::getModule('publish')->saveSetting('require_change_reason', 0);

            framework\Context::getModule('mailing')->saveSetting('smtp_host', '', 0, $this->getID());
            framework\Context::getModule('mailing')->saveSetting('smtp_port', 25, 0, $this->getID());
            framework\Context::getModule('mailing')->saveSetting('smtp_user', '', 0, $this->getID());
            framework\Context::getModule('mailing')->saveSetting('smtp_pwd', '', 0, $this->getID());
            framework\Context::getModule('mailing')->saveSetting('headcharset', framework\Context::getI18n()->getLangCharset(), 0, $this->getID());
            framework\Context::getModule('mailing')->saveSetting('from_name', 'Pachno Automailer', 0, $this->getID());
            framework\Context::getModule('mailing')->saveSetting('from_addr', '', 0, $this->getID());
            framework\Context::getModule('mailing')->saveSetting('ehlo', 1, 0, $this->getID());
        }

        public function isUploadsEnabled()
        {
            return ($this->isDefault() || $this->_uploads_enabled);
        }

        public function setUploadsEnabled($enabled = true)
        {
            $this->_uploads_enabled = $enabled;
        }

        public function isCustomWorkflowsEnabled()
        {
            return ($this->isDefault() || $this->_custom_workflows_enabled);
        }

        public function setCustomWorkflowsEnabled($enabled = true)
        {
            $this->_custom_workflows_enabled = $enabled;
        }

        public function setMaxWorkflowsLimit($limit)
        {
            $this->_max_workflows = $limit;
        }

        public function getMaxWorkflowsLimit()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_workflows;
        }

        public function hasCustomWorkflowsAvailable()
        {
            if ($this->isCustomWorkflowsEnabled())
                return ($this->getMaxWorkflowsLimit()) ? (Workflow::getCustomWorkflowsCount() < $this->getMaxWorkflowsLimit()) : true;
            else
                return false;
        }

        public function getCurrentUploadUsagePercent()
        {
            $multiplier = 100 / $this->getMaxUploadLimit();
            $pct = floor(($this->getCurrentUploadUsage() / 1000000) * $multiplier);
            return ($pct > 100) ? 100 : $pct;
        }

        public function getCurrentUploadUsageMB()
        {
            $usage = $this->getCurrentUploadUsage();
            return round($usage / 1000000, 1);
        }

        public function getCurrentUploadUsage()
        {
            if ($this->_used_storage === null) {
                $this->_used_storage = Files::getTable()->getSizeByScopeID($this->getID());
            }

            return $this->_used_storage;
        }

        public function setMaxUploadLimit($limit)
        {
            $this->_max_upload_limit = $limit;
        }

        public function getMaxUploadLimit()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_upload_limit;
        }

        public function getMaxUsers()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_users;
        }

        public function setMaxUsers($limit)
        {
            $this->_max_users = $limit;
        }

        public function hasUsersAvailable()
        {
            return ($this->getMaxUsers()) ? (\pachno\core\entities\User::getUsersCount() < $this->getMaxUsers()) : true;
        }

        public function getMaxProjects()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_projects;
        }

        public function setMaxProjects($limit)
        {
            $this->_max_projects = $limit;
        }

        public function hasProjectsAvailable()
        {
            return ($this->getMaxProjects()) ? (Project::getProjectsCount() < $this->getMaxProjects()) : true;
        }

        public function getMaxTeams()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_teams;
        }

        public function setMaxTeams($limit)
        {
            $this->_max_teams = $limit;
        }

        public function hasTeamsAvailable()
        {
            return ($this->getMaxTeams()) ? (Team::countAll() < $this->getMaxTeams()) : true;
        }

        public function getNumberOfProjects()
        {
            return (int) $this->_b2dbLazyCount('_projects');
        }

        public function getNumberOfIssues()
        {
            return (int) $this->_b2dbLazyCount('_issues');
        }

    }
