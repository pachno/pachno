<?php

    namespace pachno\core\entities;

    use Exception;
    use InvalidArgumentException;
    use pachno\core\entities\common\Identifiable;
    use pachno\core\entities\common\QaLeadable;
    use pachno\core\entities\tables\Projects;
    use pachno\core\framework;
    use pachno\core\framework\Settings;
    use pachno\core\helpers\MentionableProvider;

    /**
     * Project class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Project class
     *
     * @package pachno
     * @subpackage main
     *
     * @method static tables\Projects getB2DBTable()
     *
     * @Table(name="\pachno\core\entities\tables\Projects")
     */
    class Project extends QaLeadable implements MentionableProvider
    {
        /**
         * New issues lock type project and category access
         *
         * @static integer
         */
        public const ISSUES_LOCK_TYPE_PUBLIC_CATEGORY = 0;

        /**
         * New issues lock type project access
         *
         * @static integer
         */
        public const ISSUES_LOCK_TYPE_PUBLIC = 1;

        /**
         * New issues lock type restricted access to poster
         *
         * @static integer
         */
        public const ISSUES_LOCK_TYPE_RESTRICTED = 2;

        public const SUMMARY_TYPE_MILESTONES = 'milestones';

        public const SUMMARY_TYPE_ISSUELIST = 'issuelist';

        public const SUMMARY_TYPE_ISSUETYPES = 'issuetypes';

        /**
         * Project list cache
         *
         * @var Project[]
         */
        protected static $_projects = null;

        protected static $_num_projects = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The project prefix
         *
         * @var string
         * @Column(type="string", length=25)
         */
        protected $_prefix = '';

        /**
         * Whether or not the project uses prefix
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_use_prefix = false;

        /**
         * Whether the item is locked or not
         *
         * @var boolean
         * @access protected
         * @Column(type="boolean")
         */
        protected $_locked = null;

        /**
         * New issues lock type
         *
         * @var integer
         * @access protected
         * @Column(type="integer", length=10)
         */
        protected $_issues_lock_type = null;

        /**
         * Whether or not the project uses builds
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_builds = true;

        /**
         * Edition builds
         *
         * @var array|Build
         */
        protected $_builds = null;

        /**
         * Whether or not the project uses editions
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_editions = null;

        /**
         * Whether or not the project uses components
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_components = null;

        /**
         * Project key
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_key = null;

        /**
         * @var string
         */
        protected $_normalized_key = null;

        /**
         * List of editions for this project
         *
         * @var array|Edition
         * @Relates(class="\pachno\core\entities\Edition", collection=true, foreign_column="project")
         */
        protected $_editions = null;

        /**
         * The projects homepage
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_homepage = '';

        /**
         * List of milestones for this project
         *
         * @var array|Milestone
         * @Relates(class="\pachno\core\entities\Milestone", collection=true, foreign_column="project", orderby="sort_order")
         */
        protected $_milestones = null;

        /**
         * List of components for this project
         *
         * @var array|Component
         * @Relates(class="\pachno\core\entities\Component", collection=true, foreign_column="project", orderby="name")
         */
        protected $_components = null;

        /**
         * Count of issues registered for this project
         *
         * @var integer[][]
         */
        protected $_issuecounts = null;

        /**
         * The large project icon, if set
         *
         * @var File
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\File")
         */
        protected $_large_icon = null;

        /**
         * The project icon name
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_icon_name = '/unthemed/mono/project-icon-generic.png';

        /**
         * The projects documentation URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_doc_url = '';

        /**
         * The projects wiki URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_wiki_url = '';

        /**
         * The project description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description = '';

        /**
         * @Relates(class="\pachno\core\entities\User", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\ProjectAssignedUsers")
         */
        protected $_assigned_users;

        protected $_user_roles = null;

        /**
         * @Relates(class="\pachno\core\entities\Team", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\ProjectAssignedTeams")
         */
        protected $_assigned_teams;

        protected $_team_roles = null;

        /**
         * List of issue fields per issue type
         *
         * @var array
         */
        protected $_fieldsarrays = [];

        /**
         * Whether a user can change details about an issue without working on the issue
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_allow_freelancing = false;

        /**
         * Is project deleted
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_deleted = 0;

        /**
         * Set to true if the project is set to be deleted, but not saved yet
         *
         * @var boolean
         */
        protected $_dodelete = false;

        /**
         * Recent log items
         *
         * @var array
         */
        protected $_recentlogitems = null;

        /**
         * Recent important log items
         *
         * @var array
         */
        protected $_recentimportantlogitems = null;

        /**
         * Recent issues reported
         *
         * @var array
         */
        protected $_recentissues = [];

        /**
         * Priority count
         *
         * @var array
         */
        protected $_prioritycount = null;

        /**
         * Severity count
         *
         * @var array
         */
        protected $_severitycount = null;

        /**
         * Workflow step count
         *
         * @var array
         */
        protected $_workflowstepcount = null;

        /**
         * Status count
         *
         * @var array
         */
        protected $_statuscount = null;

        /**
         * Category count
         *
         * @var array
         */
        protected $_categorycount = null;

        /**
         * Resolution count
         *
         * @var array
         */
        protected $_resolutioncount = null;

        /**
         * State count
         *
         * @var array
         */
        protected $_statecount = null;

        /**
         * The selected workflow scheme
         *
         * @var WorkflowScheme
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\WorkflowScheme")
         */
        protected $_workflow_scheme_id = 1;

        /**
         * The selected workflow scheme
         *
         * @var IssuetypeScheme
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\IssuetypeScheme")
         */
        protected $_issuetype_scheme_id = 1;

        /**
         * Assigned client
         *
         * @var Client
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Client")
         */
        protected $_client = null;

        /**
         * Parent project
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_parent = null;

        /**
         * Child projects
         *
         * @var Array
         */
        protected $_children = null;

        /**
         * Recent activities
         *
         * @var LogItem[][]
         */
        protected $_recent_activities = null;

        /**
         * Whether to show a "Download" link and corresponding section
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_has_downloads = true;

        /**
         * Whether a project is archived (read-only mode)
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_archived = false;

        /**
         * List of project's dashboards
         *
         * @var array|Dashboard
         * @Relates(class="\pachno\core\entities\Dashboard", collection=true, foreign_column="project_id", orderby="name")
         */
        protected $_dashboards = null;

        protected $time_units_indexes = [1 => 'months', 2 => 'weeks', 3 => 'days', 4 => 'hours', 5 => 'minutes'];

        public static function getValidSubprojects(Project $project)
        {
            $valid_subproject_targets = [];
            foreach (self::getAll() as $aproject) {
                if ($aproject->getId() == $project->getId()) continue;
                $valid_subproject_targets[$aproject->getKey()] = $aproject;
            }

            // if this project has no children, life is made easy
            if ($project->hasChildren()) {
                foreach ($project->getChildren() as $child) {
                    unset($valid_subproject_targets[$child->getKey()]);
                }
            }

            return $valid_subproject_targets;
        }

        /**
         * Retrieve all projects
         *
         * @return Project[]
         */
        public static function getAll()
        {
            self::_populateProjects();

            return self::$_projects;
        }

        /**
         * Populates the projects array
         */
        protected static function _populateProjects()
        {
            if (self::$_projects === null) {
                self::$_projects = [];

                $projects = Projects::getTable()->getAll();
                foreach ($projects as $index => $project) {
                    if (!$project instanceof Project) {
                        continue;
                    }
                    if ($project->hasAccess()) {
                        self::$_projects[$project->getKey()] = $project;
                    }
                }
            }
        }

        public static function getIcons()
        {
            return [
                '/unthemed/mono/project-icon-generic.png',
                '/unthemed/mono/project-icon-code.png',
                '/unthemed/mono/project-icon-code-2.png',
                '/unthemed/mono/project-icon-packages.png',
                '/unthemed/mono/project-icon-cd.png',
                '/unthemed/mono/project-icon-servicedesk.png',
                '/unthemed/mono/project-icon-phone-1.png',
                '/unthemed/mono/project-icon-page.png',
                '/unthemed/mono/project-icon-help.png',
                '/unthemed/mono/project-icon-db-build.png',
                '/unthemed/mono/project-icon-globe-map.png',
                '/unthemed/mono/project-icon-checklist.png',
                '/unthemed/project-icon-picture.png',
                '/unthemed/project-icon-book.png',
                '/unthemed/project-icon-archive.png',
                '/unthemed/project-icon-globe-hand.png',
                '/unthemed/project-icon-phone-1.png',
                '/unthemed/project-icon-phone-2.png',
                '/unthemed/project-icon-cloud-heart.png',
                '/unthemed/project-icon-webpage.png',
                '/unthemed/project-icon-shopping.png',
                '/unthemed/project-icon-diamonds.png',
                '/unthemed/project-icon-gaming.png',
                '/unthemed/project-icon-person-computer.png',
            ];
        }

        /**
         * Whether or not the current or target user can access the project
         *
         * @param null $target_user
         *
         * @return boolean
         */
        public function hasAccess($target_user = null)
        {
            if (framework\Context::isCLI()) return true;

            $user = ($target_user === null) ? framework\Context::getUser() : $target_user;

            if ($this->getOwner() instanceof User && $this->getOwner()->getID() == $user->getID()) return true;
            if ($this->getLeader() instanceof User && $this->getLeader()->getID() == $user->getID()) return true;

            return $user->hasPermission('canseeproject', $this->getID());
        }

        /**
         * Return project key
         *
         * @return string
         */
        public function getKey()
        {
            return $this->_key;
        }

        public function setKey($key)
        {
            $this->_key = $key;
        }

        public function hasChildren()
        {
            return (bool)count($this->getChildren());
        }

        // Archived projects do not count

        /**
         * Get children based on archived state
         *
         * @param bool $archived [optional] Show archived projects
         */
        public function getChildren($archived = false)
        {
            $this->_populateChildren();
            $f_projects = [];

            foreach ($this->_children as $project) {
                if ($archived) {
                    if ($project->isArchived() && $project->hasAccess()) $f_projects[] = $project;
                } else {
                    if (!$project->isArchived() && $project->hasAccess()) $f_projects[] = $project;
                }
            }

            return $f_projects;
        }

        protected function _populateChildren()
        {
            if ($this->_children === null) {
                $this->_children = self::getB2DBTable()->getByParentID($this->getID());
            }

            return $this->_children;
        }

        /**
         * Retrieve all projects by id
         *
         * @return Project[]
         */
        public static function getAllByIDs($ids)
        {
            if (empty($ids)) return [];

            self::_populateProjects();
            $projects = [];
            foreach (self::$_projects as $project) {
                if (in_array($project->getID(), $ids)) $projects[$project->getID()] = $project;
                if (count($projects) == count($ids)) break;
            }

            return $projects;
        }

        /**
         * Retrieve all projects by parent ID
         *
         * @return Project[]
         */
        public static function getAllByParentID($id)
        {
            self::_populateProjects();
            $final = [];
            foreach (self::$_projects as $project) {
                if (($project->getParent() instanceof Project) && $project->getParent()->getID() == $id) {
                    $final[] = $project;
                }
            }

            return $final;
        }

        /**
         * @return Project
         */
        public function getParent()
        {
            return $this->_b2dbLazyLoad('_parent');
        }

        public function setParent(Project $project)
        {
            $this->_parent = $project;
        }

        /**
         * Retrieve all projects with no parent. If the parent is archived, the project will not be shown
         *
         * @param bool $archived [optional] Show archived projects instead
         *
         * @return Project[]
         */
        public static function getAllRootProjects($archived = null)
        {
            self::_populateProjects();

            $final = [];
            foreach (self::$_projects as $project) {
                if ($project->hasParent())
                    continue;

                if ($archived === null) {
                    $final[] = $project;
                } elseif ($archived === true && $project->isArchived()) {
                    $final[] = $project;
                } elseif ($archived === false && !$project->isArchived()) {
                    $final[] = $project;
                }
            }

            return $final;
        }

        public function hasParent()
        {
            return ($this->getParent() instanceof Project);
        }

        /**
         * Returns whether or not the project has been archived
         *
         * @return boolean
         */
        public function isArchived()
        {
            return $this->_archived;
        }

        /**
         * Set the archived state
         *
         * @var boolean $archived
         */
        public function setArchived($archived)
        {
            $this->_archived = $archived;
        }

        public static function getProjectsCount()
        {
            if (self::$_num_projects === null) {
                if (self::$_projects !== null)
                    self::$_num_projects = count(self::$_projects);
                else
                    self::$_num_projects = self::getB2DBTable()->countProjects();
            }

            return self::$_num_projects;
        }

        /**
         * Retrieve all projects by client ID
         *
         * @return Project[]
         */
        public static function getAllByClientID($id)
        {
            self::_populateProjects();
            $final = [];
            foreach (self::$_projects as $project) {
                if (($project->getClient() instanceof Client) && $project->getClient()->getID() == $id) {
                    $final[] = $project;
                }
            }

            return $final;
        }

        /**
         * Return the client assigned to the project, or null if there is none
         *
         * @return Client
         */
        public function getClient()
        {
            return $this->_b2dbLazyLoad('_client');
        }

        /**
         * Set the client
         */
        public function setClient($client)
        {
            $this->_client = $client;
        }

        /**
         * Retrieve all projects by leader
         *
         * @param User or \pachno\core\entities\Team
         *
         * @return Project[]
         */
        public static function getAllByLeader($leader)
        {
            self::_populateProjects();
            $final = [];
            $class = get_class($leader);

            if (!($leader instanceof User) && !($leader instanceof Team)) return false;

            foreach (self::$_projects as $project) {
                if ($project->getLeader() instanceof $class && $project->getLeader()->getID() == $leader->getID()) {
                    $final[] = $project;
                }
            }

            return $final;
        }

        /**
         * Retrieve all projects by owner
         *
         * @param User or \pachno\core\entities\Team
         *
         * @return Project[]
         */
        public static function getAllByOwner($owner)
        {
            self::_populateProjects();
            $final = [];
            $class = get_class($owner);

            if (!($owner instanceof User) && !($owner instanceof Team)) return false;

            foreach (self::$_projects as $project) {
                if ($project->getOwner() instanceof $class && $project->getOwner()->getID() == $owner->getID()) {
                    $final[] = $project;
                }
            }

            return $final;
        }

        /**
         * Retrieve all projects by qa
         *
         * @param User or \pachno\core\entities\Team
         *
         * @return Project[]
         */
        public static function getAllByQaResponsible($qa)
        {
            self::_populateProjects();
            $final = [];
            $class = get_class($qa);

            if (!($qa instanceof User) && !($qa instanceof Team)) return false;

            foreach (self::$_projects as $project) {
                if ($project->getQaResponsible() instanceof $class && $project->getQaResponsible()->getID() == $qa->getID()) {
                    $final[] = $project;
                }
            }

            return $final;
        }

        public static function getIncludingAllSubprojectsAsArray(Project $project)
        {
            $projects = [];
            self::getSubprojectsArray($project, $projects);

            return $projects;
        }

        public static function getSubprojectsArray(Project $project, &$projects)
        {
            $projects[$project->getID()] = $project;
            foreach ($project->getChildProjects() as $subproject) {
                self::getSubprojectsArray($subproject, $projects);
            }
        }

        /**
         * @param bool $archived [optional] Show archived projects
         *
         * @return Project[]
         *
         */
        public function getChildProjects($archived = false)
        {
            return $this->getChildren($archived);
        }

        /**
         * Returns the project for a specified prefix
         *
         * @return Project
         */
        static function getByPrefix($prefix)
        {
            return self::getB2DBTable()->getByPrefix($prefix);
        }

        public static function listen_pachno_core_entities_File_hasAccess(framework\Event $event)
        {
            $file = $event->getSubject();
            $projects = self::getB2DBTable()->getByFileID($file->getID());
            foreach ($projects as $project) {
                if ($project->hasAccess()) {
                    $event->setReturnValue(true);
                    $event->setProcessed();
                    break;
                }
            }
        }

        /**
         * Returns whether or not the project has a prefix set (regardless of whether it uses prefix or not)
         *
         * @return boolean
         */
        public function hasPrefix()
        {
            return ($this->_prefix != '') ? true : false;
        }

        /**
         * Returns whether or not this project has a homepage set
         *
         * @return boolean
         */
        public function hasHomepage()
        {
            return ($this->_homepage != '') ? true : false;
        }

        /**
         * Whether or not this project has any editions
         *
         * @return bool
         */
        public function hasEditions()
        {
            return (bool)count($this->getEditions());
        }

        /**
         * Returns an array of all the projects editions
         *
         * @return Edition[]
         */
        public function getEditions()
        {
            $this->_populateEditions();

            return $this->_editions;
        }

        /**
         * Populates editions inside the project
         *
         * @return void
         */
        protected function _populateEditions()
        {
            if ($this->_editions === null) {
                $this->_b2dbLazyLoad('_editions');
                foreach ($this->_editions as $key => $component) {
                    if (!$component->hasAccess()) unset($this->_editions[$key]);
                }
            }
        }

        /**
         * Returns whether or not this project has any description set
         *
         * @return boolean
         */
        public function hasDescription()
        {
            return ($this->_description != '') ? true : false;
        }

        /**
         * Returns whether or not this project has a homepage set
         *
         * @return boolean
         */
        public function hasDocumentationURL()
        {
            return ($this->_doc_url != '') ? true : false;
        }

        /**
         * Set the projects documentation url
         *
         * @param string $doc_url
         */
        public function setDocumentationURL($doc_url)
        {
            $this->_doc_url = $doc_url;
        }

        /**
         * Returns whether or not this project has a wiki set
         *
         * @return boolean
         */
        public function hasWikiURL()
        {
            return ($this->_wiki_url != '') ? true : false;
        }

        /**
         * Set whether or not the project uses prefix
         *
         * @param boolean $use_prefix
         */
        public function setUsePrefix($use_prefix)
        {
            $this->_use_prefix = (bool)$use_prefix;
        }

        public function countEditions()
        {
            if ($this->_editions !== null) {
                return count($this->_editions);
            }

            return $this->_b2dbLazyCount('_editions');
        }

        /**
         * Adds an edition to the project
         *
         * @param string $e_name
         *
         * @return Edition
         */
        public function addEdition($e_name)
        {
            $this->_editions = null;
            $edition = new Edition();
            $edition->setName($e_name);
            $edition->setProject($this);
            $edition->save();

            return $edition;
        }

        public function countComponents()
        {
            if ($this->_components !== null) {
                return count($this->_components);
            }

            return $this->_b2dbLazyCount('_components');
        }

        /**
         * Adds a new component to the project
         *
         * @param string $c_name
         *
         * @return Component
         */
        public function addComponent($c_name)
        {
            $this->_components = null;
            $component = new Component();
            $component->setName($c_name);
            $component->setProject($this);
            $component->save();

            return $component;
        }

        /**
         * Returns an array with all milestones visible for the roadmap
         *
         * @return Milestone[]
         */
        public function getMilestonesForRoadmap()
        {
            $milestones = [];
            foreach ($this->getMilestones() as $milestone) {
                if (!$milestone->isVisibleRoadmap()) continue;

                $milestones[$milestone->getID()] = $milestone;
            }

            return $milestones;
        }

        /**
         * Returns an array with all the milestones
         *
         * @return Milestone[]
         */
        public function getMilestones()
        {
            $this->_populateMilestones();

            return $this->_milestones;
        }

        /**
         * Populates the milestones array
         *
         * @return void
         */
        protected function _populateMilestones()
        {
            if ($this->_milestones === null) {
                $this->_b2dbLazyLoad('_milestones');
            }
        }

        /**
         * Returns an array with all milestones visible for issues
         *
         * @return Milestone[]
         */
        public function getMilestonesForIssues()
        {
            $milestones = [];
            foreach ($this->getOpenMilestones() as $milestone) {
                if (!$milestone->hasAccess() || !$milestone->isVisibleIssues()) continue;

                $milestones[$milestone->getID()] = $milestone;
            }

            return $milestones;
        }

        /**
         * Returns the current milestone if any
         *
         * @return Milestone|null
         */
        public function getCurrentMilestone(): ?Milestone
        {
            foreach ($this->getOpenMilestones() as $milestone) {
                if ($milestone->isCurrent()) return $milestone;
            }

            return null;
        }

        /**
         * Returns an array with all open milestones
         *
         * @return Milestone[]
         */
        public function getOpenMilestones()
        {
            $milestones = $this->getMilestones();
            foreach ($milestones as $key => $milestone) {
                if ($milestone->isClosed()) unset($milestones[$key]);
            }

            return $milestones;
        }

        /**
         * Returns an array with all milestones visible for issues or the roadmap
         *
         * @return Milestone[]
         */
        public function getAvailableMilestones()
        {
            $milestones = [];
            foreach ($this->getMilestones() as $milestone) {
                if (!$milestone->isVisibleIssues() && !$milestone->isVisibleRoadmap()) continue;

                $milestones[$milestone->getID()] = $milestone;
            }

            return $milestones;
        }

        /**
         * Returns a list of upcoming milestones
         *
         * @param integer $days [optional] Number of days, default 21
         *
         * @return Milestone[]
         */
        public function getUpcomingMilestones($days = 21)
        {
            $return_array = [];
            if ($milestones = $this->getMilestones()) {
                $curr_day = NOW;
                foreach ($milestones as $milestone) {
                    if (($milestone->getScheduledDate() >= $curr_day || $milestone->isOverdue()) && (($milestone->getScheduledDate() <= ($curr_day + (86400 * $days))) || ($milestone->getType() == Milestone::TYPE_SCRUMSPRINT && $milestone->isCurrent()))) {
                        $return_array[$milestone->getID()] = $milestone;
                    }
                }
            }

            return $return_array;
        }

        /**
         * Returns a list of milestones starting soon
         *
         * @param integer $days [optional] Number of days, default 21
         *
         * @return Mielstone[]
         */
        public function getStartingMilestones($days = 21)
        {
            $return_array = [];
            if ($milestones = $this->getMilestones()) {
                $curr_day = NOW;
                foreach ($milestones as $milestone) {
                    if (($milestone->getStartingDate() > $curr_day) && ($milestone->getStartingDate() < ($curr_day + (86400 * $days)))) {
                        $return_array[$milestone->getID()] = $milestone;
                    }
                }
            }

            return $return_array;
        }

        public function removeAssignee(Identifiable $assignee)
        {
            $user_id = 0;
            $team_id = 0;
            if ($assignee instanceof User) {
                $user_id = $assignee->getID();
                tables\ProjectAssignedUsers::getTable()->removeUserFromProject($this->getID(), $assignee->getID());
                foreach ($this->getAssignedUsers() as $user) {
                    if ($user->getID() == $user_id) return;
                }
            } else {
                $team_id = $assignee->getID();
                tables\ProjectAssignedTeams::getTable()->removeTeamFromProject($this->getID(), $assignee->getID());
                foreach ($this->getAssignedTeams() as $team) {
                    if ($team->getID() == $team_id) return;
                }
            }
            framework\Context::removeAllPermissionsForCombination($user_id, 0, $team_id, $this->getID());
        }

        public function getAssignedUsers()
        {
            $this->_populateAssignedUsers();

            return $this->_assigned_users;
        }

        protected function _populateAssignedUsers()
        {
            if ($this->_assigned_users === null) {
                $this->_b2dbLazyLoad('_assigned_users');
            }
        }

        public function getAssignedTeams()
        {
            $this->_populateAssignedTeams();

            return $this->_assigned_teams;
        }

        protected function _populateAssignedTeams()
        {
            if ($this->_assigned_teams === null) {
                $this->_b2dbLazyLoad('_assigned_teams');
            }
        }

        /**
         * Adds an assignee with a given role
         *
         * @param Identifiable $assignee The user or team to add
         * @param Role $role The role to add
         *
         * @return null
         */
        public function addAssignee($assignee, $role = null)
        {
            $user_id = 0;
            $team_id = 0;
            if ($assignee instanceof User) {
                $user_id = $assignee->getID();
                if (tables\ProjectAssignedUsers::getTable()->addUserToProject($this->getID(), $user_id, $role->getID()) && is_array($this->_assigned_users)) {
                    $this->_assigned_users = array_merge($this->_assigned_users, tables\ProjectAssignedUsers::getTable()->getUserByProjectIDUserIDRoleID($this->getID(), $user_id, $role->getID()));
                }
            } elseif ($assignee instanceof Team) {
                $team_id = $assignee->getID();
                if (tables\ProjectAssignedTeams::getTable()->addTeamToProject($this->getID(), $team_id, $role->getID()) && is_array($this->_assigned_users)) {
                    $this->_assigned_teams = array_merge($this->_assigned_teams, tables\ProjectAssignedTeams::getTable()->getTeamByProjectIDTeamIDRoleID($this->getID(), $team_id, $role->getID()));
                }
            }
            if ($role instanceof Role) {
                $role_id = $role->getID();
                foreach ($role->getPermissions() as $role_permission) {
                    // Obtain expanded target ID since some role permissions
                    // may contain templated project key ID as target_id
                    // (i.e. wiki article permissions).
                    $target_id = $role_permission->getExpandedTargetIDForProject($this);
                    tables\Permissions::getTable()->removeSavedPermission($user_id, 0, $team_id, $role_permission->getModule(), $role_permission->getPermission(), $target_id, framework\Context::getScope()->getID(), $role_id);
                    framework\Context::setPermission($role_permission->getPermission(), $target_id, $role_permission->getModule(), $user_id, 0, $team_id, true, null, $role_id);
                }
            }
        }

        public function getNonEditionBuilds()
        {
            return array_filter($this->getBuilds(), function ($build) {
                return $build->isEditionBuild() == false;
            });
        }

        /**
         * Returns an array with all builds
         *
         * @return Build[]
         */
        public function getBuilds()
        {
            $this->_populateBuilds();

            return $this->_builds;
        }

        /**
         * Populates builds inside the project
         *
         * @return void
         */
        protected function _populateBuilds()
        {
            if ($this->_builds === null) {
                $this->_builds = [];
                foreach (tables\Builds::getTable()->getByProjectID($this->getID()) as $build) {
                    if ($build->hasAccess()) {
                        $this->_builds[$build->getID()] = $build;
                    }
                }
            }
        }

        public function getUnreleasedBuilds()
        {
            $builds = $this->getBuilds();
            foreach ($builds as $id => $build) {
                if ($build->isReleased() || $build->isArchived()) unset($builds[$id]);
            }

            return $builds;
        }

        public function getReleasedBuilds()
        {
            $builds = $this->getBuilds();
            foreach ($builds as $id => $build) {
                if (!$build->isReleased()) unset($builds[$id]);
            }

            return $builds;
        }

        public function getLast15Counts()
        {
            $this->_populateIssueCounts();

            return $this->_issuecounts['last15'];
        }

        protected function _populateIssueCounts()
        {
            if (!is_array($this->_issuecounts)) {
                $this->_issuecounts = [];
            }
            if (!array_key_exists('all', $this->_issuecounts)) {
                $this->_issuecounts['all'] = [];
            }
            if (empty($this->_issuecounts['all'])) {
                list ($this->_issuecounts['all']['closed'], $this->_issuecounts['all']['open']) = Issue::getIssueCountsByProjectID($this->getID());
            }
            if (empty($this->_issuecounts['last15'])) {
                list ($closed, $open) = tables\LogItems::getTable()->getLast15IssueCountsByProjectID($this->getID());
                $this->_issuecounts['last15']['open'] = $open;
                $this->_issuecounts['last15']['closed'] = $closed;
            }
        }

        /**
         * Returns the number of open issues for this project with a specific issue type
         *
         * @param integer $issue_type ID of the issue type
         *
         * @return integer
         */
        public function countOpenIssuesByType($issue_type)
        {
            $this->_populateIssueCountsByIssueType($issue_type);

            return $this->_issuecounts['issuetype'][$issue_type]['open'];
        }

        protected function _populateIssueCountsByIssueType($issuetype_id)
        {
            if ($this->_issuecounts === null) {
                $this->_issuecounts = [];
            }
            if (!array_key_exists('issuetype', $this->_issuecounts)) {
                $this->_issuecounts['issuetype'] = [];
            }
            if (!array_key_exists($issuetype_id, $this->_issuecounts['issuetype'])) {
                list ($this->_issuecounts['issuetype'][$issuetype_id]['closed'], $this->_issuecounts['issuetype'][$issuetype_id]['open']) = Issue::getIssueCountsByProjectIDandIssuetype($this->getID(), $issuetype_id);
            }
        }

        /**
         * Returns the number of open issues for this project with a specific milestone
         *
         * @param integer $milestone ID of the milestone
         *
         * @return integer
         */
        public function countOpenIssuesByMilestone($milestone)
        {
            $this->_populateIssueCountsByMilestone($milestone);

            return $this->_issuecounts['milestone'][$milestone]['open'];
        }

        /**
         * @param array $allowed_status_ids
         */
        protected function _populateIssueCountsByMilestone($milestone_id, $allowed_status_ids = [])
        {
            if ($this->_issuecounts === null) {
                $this->_issuecounts = [];
            }
            if (!array_key_exists('milestone', $this->_issuecounts)) {
                $this->_issuecounts['milestone'] = [];
            }
            if (!array_key_exists($milestone_id, $this->_issuecounts['milestone'])) {
                list ($this->_issuecounts['milestone'][$milestone_id]['closed'], $this->_issuecounts['milestone'][$milestone_id]['open']) = Issue::getIssueCountsByProjectIDandMilestone($this->getID(), $milestone_id, $allowed_status_ids);
            }
        }

        /**
         * Returns the percentage of closed issues for this project
         *
         * @return integer
         */
        public function getClosedPercentageForAllIssues()
        {
            return $this->_getPercentage($this->countAllClosedIssues(), $this->countAllIssues());
        }

        /**
         * Returns the percentage of a given number related to another given number
         *
         * @param integer $num_1 percentage number
         * @param integer $num_max total number
         *
         * @return integer The percentage
         */
        protected function _getPercentage($num_1, $num_max)
        {
            $pct = 0;

            if ($num_max > 0 && $num_1 > 0) {
                $multiplier = 100 / $num_max;
                $pct = $num_1 * $multiplier;
            }

            return (int)$pct;
        }

        /**
         * Returns the number of closed issues for this project
         *
         * @return integer
         */
        public function countAllClosedIssues()
        {
            $this->_populateIssueCounts();

            return $this->_issuecounts['all']['closed'];
        }

        /**
         * Returns the number of issues for this project
         *
         * @return integer
         */
        public function countAllIssues()
        {
            $this->_populateIssueCounts();

            return $this->_issuecounts['all']['closed'] + $this->_issuecounts['all']['open'];
        }

        /**
         * Returns the percentage of closed issues for this project with a specific issue type
         *
         * @param integer $issue_type ID of the issue type
         *
         * @return integer
         */
        public function getClosedPercentageByType($issue_type)
        {
            return $this->_getPercentage($this->countClosedIssuesByType($issue_type), $this->countIssuesByType($issue_type));
        }

        /**
         * Returns the number of closed issues for this project with a specific issue type
         *
         * @param integer $issue_type ID of the issue type
         *
         * @return integer
         */
        public function countClosedIssuesByType($issue_type)
        {
            $this->_populateIssueCountsByIssueType($issue_type);

            return $this->_issuecounts['issuetype'][$issue_type]['closed'];
        }

        /**
         * Returns the number of issues for this project with a specific issue type
         *
         * @param integer $issuetype ID of the issue type
         *
         * @return integer
         */
        public function countIssuesByType($issuetype)
        {
            $this->_populateIssueCountsByIssueType($issuetype);

            return $this->_issuecounts['issuetype'][$issuetype]['closed'] + $this->_issuecounts['issuetype'][$issuetype]['open'];
        }

        /**
         * Returns the percentage of closed issues for this project with a specific milestone
         *
         * @param integer $milestone ID of the milestone
         * @param array $allowed_status_ids
         *
         * @return integer
         */
        public function getClosedPercentageByMilestone($milestone, $allowed_status_ids = [])
        {
            return $this->_getPercentage($this->countClosedIssuesByMilestone($milestone, $allowed_status_ids), $this->countIssuesByMilestone($milestone, $allowed_status_ids));
        }

        /**
         * Returns the number of closed issues for this project with a specific milestone
         *
         * @param integer $milestone ID of the milestone
         * @param array $allowed_status_ids
         *
         * @return integer
         */
        public function countClosedIssuesByMilestone($milestone, $allowed_status_ids = [])
        {
            $this->_populateIssueCountsByMilestone($milestone, $allowed_status_ids);

            return $this->_issuecounts['milestone'][$milestone]['closed'];
        }

        /**
         * Returns the number of issues for this project with a specific milestone
         *
         * @param integer $milestone ID of the milestone
         * @param array $allowed_status_ids
         *
         * @return integer
         */
        public function countIssuesByMilestone($milestone, $allowed_status_ids = [])
        {
            $this->_populateIssueCountsByMilestone($milestone, $allowed_status_ids);
            if (!$milestone) {
                return $this->_issuecounts['milestone'][$milestone]['open'];
            } else {
                return $this->_issuecounts['milestone'][$milestone]['closed'] + $this->_issuecounts['milestone'][$milestone]['open'];
            }
        }

        /**
         * @param array $allowed_status_ids
         */
        public function getTotalPercentageByMilestone($milestone, $allowed_status_ids = [])
        {
            if ($this->countIssuesByMilestone($milestone, $allowed_status_ids) == 0) return 0;

            return tables\Issues::getTable()->getTotalPercentCompleteByProjectIDAndMilestoneID($this->getID(), $milestone, $allowed_status_ids) / $this->countIssuesByMilestone($milestone, $allowed_status_ids);
        }

        /**
         * Return an array specifying visibility, requirement and choices for fields in reporting wizard
         *
         * @param integer $issue_type
         * @param boolean $prefix_values
         *
         * @return array
         */
        public function getReportableFieldsArray($issue_type, $prefix_values = false)
        {
            return $this->_getFieldsArray($issue_type, true, $prefix_values);
        }

        /**
         * Return an array specifying visibility, requirement and choices for fields in issues
         *
         * @param integer|Issuetype $issue_type
         * @param boolean $reportable [optional] Whether to only include fields that can be reported
         * @param boolean $prefix_values [optional] Whether to prefix keys for values of fields that can be reported
         *
         * @return array
         */
        protected function _getFieldsArray($issue_type, $reportable = true, $prefix_values = false)
        {
            $issue_type = (is_object($issue_type)) ? $issue_type->getID() : $issue_type;
            if (!isset($this->_fieldsarrays[$issue_type][(int)$reportable])) {
                $retval = [];
                $res = tables\IssueFields::getTable()->getBySchemeIDandIssuetypeID($this->getIssuetypeScheme()->getID(), $issue_type);
                if ($res) {
                    $builtin_types = array_keys(DatatypeBase::getAvailableFields(true));
                    while ($row = $res->getNextRow()) {
                        if (!$reportable || (bool)$row->get(tables\IssueFields::REPORTABLE) == true) {
                            if ($reportable) {
                                if (in_array($row->get(tables\IssueFields::FIELD_KEY), $builtin_types) && (!$this->fieldPermissionCheck($row->get(tables\IssueFields::FIELD_KEY)) && !($row->get(tables\IssueFields::REQUIRED) && $reportable))) continue;
                                elseif (!in_array($row->get(tables\IssueFields::FIELD_KEY), $builtin_types) && (!$this->fieldPermissionCheck($row->get(tables\IssueFields::FIELD_KEY), true) && !($row->get(tables\IssueFields::REQUIRED) && $reportable))) continue;
                            }
                            $field_key = $row->get(tables\IssueFields::FIELD_KEY);
                            $retval[$field_key] = ['required' => (bool)$row->get(tables\IssueFields::REQUIRED), 'additional' => (bool)$row->get(tables\IssueFields::ADDITIONAL)];
                            if (!in_array($field_key, $builtin_types)) {
                                $retval[$field_key]['custom'] = true;
                                $custom_type = CustomDatatype::getByKey($field_key);
                                if ($custom_type instanceof CustomDatatype) {
                                    $retval[$field_key]['custom_type'] = $custom_type->getType();
                                } else {
                                    unset($retval[$field_key]);
                                }
                            }
                        }
                    }
                    if (array_key_exists('user_pain', $retval)) {
                        $retval['pain_bug_type'] = ['required' => $retval['user_pain']['required']];
                        $retval['pain_likelihood'] = ['required' => $retval['user_pain']['required']];
                        $retval['pain_effect'] = ['required' => $retval['user_pain']['required']];
                    }

                    if ($reportable) {
                        // 'v' is just a dummy prefix.
                        $key_prefix = $prefix_values ? 'v' : '';

                        foreach ($retval as $key => $return_details) {
                            if ($key == 'edition' || array_key_exists('custom', $return_details) && $return_details['custom'] && $return_details['custom_type'] == DatatypeBase::EDITIONS_CHOICE) {
                                $retval[$key]['values'] = [];
                                $retval[$key]['values'][''] = framework\Context::getI18n()->__('None');
                                foreach ($this->getEditions() as $edition) {
                                    $retval[$key]['values'][$key_prefix . $edition->getID()] = $edition->getName();
                                }
                                if (!$this->isEditionsEnabled() || empty($retval[$key]['values'])) {
                                    if (!$retval[$key]['required']) {
                                        unset($retval[$key]);
                                    } else {
                                        unset($retval[$key]['values']);
                                    }
                                }
                                if (array_key_exists($key, $retval) && array_key_exists('values', $retval[$key])) {
                                    asort($retval[$key]['values'], SORT_STRING);
                                }
                            } elseif ($key == 'status' || array_key_exists('custom', $return_details) && $return_details['custom'] && $return_details['custom_type'] == DatatypeBase::STATUS_CHOICE) {
                                $retval[$key]['values'] = [];
                                foreach (Status::getAll() as $status) {
                                    $retval[$key]['values'][$key_prefix . $status->getID()] = $status->getName();
                                }
                                if (empty($retval[$key]['values'])) {
                                    if (!$retval[$key]['required']) {
                                        unset($retval[$key]);
                                    } else {
                                        unset($retval[$key]['values']);
                                    }
                                }
                                if (array_key_exists($key, $retval) && array_key_exists('values', $retval[$key])) {
                                    asort($retval[$key]['values'], SORT_STRING);
                                }
                            } elseif ($key == 'component' || array_key_exists('custom', $return_details) && $return_details['custom'] && $return_details['custom_type'] == DatatypeBase::COMPONENTS_CHOICE) {
                                $retval[$key]['values'] = [];
                                $retval[$key]['values'][''] = framework\Context::getI18n()->__('None');
                                foreach ($this->getComponents() as $component) {
                                    $retval[$key]['values'][$key_prefix . $component->getID()] = $component->getName();
                                }
                                if (!$this->isComponentsEnabled() || empty($retval[$key]['values'])) {
                                    if (!$retval[$key]['required']) {
                                        unset($retval[$key]);
                                    } else {
                                        unset($retval[$key]['values']);
                                    }
                                }
                                if (array_key_exists($key, $retval) && array_key_exists('values', $retval[$key])) {
                                    asort($retval[$key]['values'], SORT_STRING);
                                }
                            } elseif ($key == 'build' || array_key_exists('custom', $return_details) && $return_details['custom'] && $return_details['custom_type'] == DatatypeBase::RELEASES_CHOICE) {
                                $retval[$key]['values'] = [];
                                $retval[$key]['values'][''] = framework\Context::getI18n()->__('None');
                                foreach ($this->getActiveBuilds() as $build) {
                                    $retval[$key]['values'][$key_prefix . $build->getID()] = $build->getName() . ' (' . $build->getVersion() . ')';
                                }
                                arsort($retval[$key]['values']);
                                if (!$this->isBuildsEnabled() || empty($retval[$key]['values'])) {
                                    if (!$retval[$key]['required']) {
                                        unset($retval[$key]);
                                    } else {
                                        unset($retval[$key]['values']);
                                    }
                                }
                            } elseif ($key == 'milestone') {
                                $retval[$key]['values'] = [];
                                $retval[$key]['values'][''] = framework\Context::getI18n()->__('None');
                                foreach ($this->getOpenMilestones() as $milestone) {
                                    if (!$milestone->hasAccess()) continue;
                                    $retval[$key]['values'][$key_prefix . $milestone->getID()] = $milestone->getName();
                                }
                                if (empty($retval[$key]['values'])) {
                                    if (!$retval[$key]['required']) {
                                        unset($retval[$key]);
                                    } else {
                                        unset($retval[$key]['values']);
                                    }
                                }
                            }
                        }
                    }
                }
                $this->_fieldsarrays[$issue_type][(int)$reportable] = $retval;
            }

            return $this->_fieldsarrays[$issue_type][(int)$reportable];
        }

        public function fieldPermissionCheck($field, $custom = false)
        {
            if ($custom) {
                return (bool)($this->permissionCheck('caneditissuecustomfields' . $field) || $this->permissionCheck('caneditissuecustomfields'));
            } elseif (in_array($field, ['title', 'shortname', 'description', 'reproduction_steps'])) {
                return (bool)($this->permissionCheck('caneditissue' . $field) || $this->permissionCheck('caneditissuebasic') || $this->permissionCheck('cancreateissues') || $this->permissionCheck('cancreateandeditissues'));
            } elseif (in_array($field, ['builds', 'editions', 'components', 'links', 'files'])) {
                return (bool)($this->permissionCheck('canadd' . $field) || $this->permissionCheck('canaddextrainformationtoissues'));
            } elseif (in_array($field, ['user_pain', 'pain_bug_type', 'pain_likelihood', 'pain_effect'])) {
                return (bool)($this->permissionCheck('caneditissueuserpain') || $this->permissionCheck('caneditissue'));
            } else {
                return (bool)($this->permissionCheck('caneditissue' . $field) || $this->permissionCheck('caneditissue'));
            }
        }

        /**
         * Perform a permission check based on a key, and whether or not to
         * check if the permission is explicitly set
         *
         * @param string $key The permission key to check for
         * @param boolean $explicit (optional) Whether to make sure the permission is explicitly set
         *
         * @return boolean
         */
        public function permissionCheck($key, $explicit = false)
        {
            $retval = framework\Context::getUser()->hasPermission($key, $this->getID(), 'core');
            if ($explicit) {
                $retval = ($retval !== null) ? $retval : framework\Context::getUser()->hasPermission($key, 0, 'core');
            } else {
                $retval = ($retval !== null) ? $retval : framework\Context::getUser()->hasPermission($key);
            }

            return $retval;
        }

        /**
         * Is editions enabled
         *
         * @return boolean
         */
        public function isEditionsEnabled()
        {
            return (bool)$this->_enable_editions;
        }

        /**
         * Returns an array with all components
         *
         * @return Component[]
         */
        public function getComponents()
        {
            $this->_populateComponents();

            return $this->_components;
        }

        /**
         * Populates components inside the project
         *
         * @return void
         */
        protected function _populateComponents()
        {
            if ($this->_components === null) {
                $this->_b2dbLazyLoad('_components');
                foreach ($this->_components as $key => $component) {
                    if (!$component->hasAccess()) unset($this->_components[$key]);
                }
            }
        }

        /**
         * Is components enabled
         *
         * @return boolean
         */
        public function isComponentsEnabled()
        {
            return (bool)$this->_enable_components;
        }

        public function getActiveBuilds()
        {
            $builds = $this->getBuilds();
            foreach ($builds as $id => $build) {
                if ($build->isLocked()) unset($builds[$id]);
            }

            return $builds;
        }

        /**
         * Is builds enabled
         *
         * @return boolean
         */
        public function isBuildsEnabled()
        {
            return (bool)$this->_enable_builds;
        }

        /**
         * Return an array specifying visibility, requirement and choices for fields in the "View issue" page
         *
         * @param integer|Issuetype $issue_type
         *
         * @return array
         */
        public function getVisibleFieldsArray($issue_type)
        {
            return $this->_getFieldsArray($issue_type, false);
        }

        public function getPriorityCount()
        {
            $this->_populatePriorityCount();

            return $this->_prioritycount;
        }

        protected function _populatePriorityCount()
        {
            if ($this->_prioritycount === null) {
                $this->_prioritycount = [];
                $this->_prioritycount[0] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                foreach (Priority::getAll() as $priority_id => $priority) {
                    $this->_prioritycount[$priority_id] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                }
                foreach (tables\Issues::getTable()->getPriorityCountByProjectID($this->getID()) as $priority_id => $priority_count) {
                    $this->_prioritycount[$priority_id] = $priority_count;
                }
            }
        }

        public function getSeverityCount()
        {
            $this->_populateSeverityCount();

            return $this->_severitycount;
        }

        protected function _populateSeverityCount()
        {
            if ($this->_severitycount === null) {
                $this->_severitycount = [];
                $this->_severitycount[0] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                foreach (Severity::getAll() as $severity_id => $severity) {
                    $this->_severitycount[$severity_id] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                }
                foreach (tables\Issues::getTable()->getSeverityCountByProjectID($this->getID()) as $severity_id => $severity_count) {
                    $this->_severitycount[$severity_id] = $severity_count;
                }
            }
        }

        public function getWorkflowCount()
        {
            $this->_populateWorkflowCount();

            return $this->_workflowstepcount;
        }

        protected function _populateWorkflowCount()
        {
            if ($this->_workflowstepcount === null) {
                $this->_workflowstepcount = [];
                $this->_workflowstepcount[0] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                foreach (WorkflowStep::getAllByWorkflowSchemeID($this->getWorkflowScheme()->getID()) as $workflow_step_id => $workflow_step) {
                    $this->_workflowstepcount[$workflow_step_id] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                }
                foreach (tables\Issues::getTable()->getWorkflowStepCountByProjectID($this->getID()) as $workflow_step_id => $workflow_count) {
                    $this->_workflowstepcount[$workflow_step_id] = $workflow_count;
                }
            }
        }

        /**
         * Return the projects' associated workflow scheme
         *
         * @return WorkflowScheme
         */
        public function getWorkflowScheme()
        {
            if (!$this->_workflow_scheme_id instanceof WorkflowScheme)
                $this->_b2dbLazyLoad('_workflow_scheme_id');

            return $this->_workflow_scheme_id;
        }

        public function getStatusCount()
        {
            $this->_populateStatusCount();

            return $this->_statuscount;
        }

        protected function _populateStatusCount()
        {
            if ($this->_statuscount === null) {
                $this->_statuscount = [];
                $this->_statuscount[0] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                foreach (Status::getAll() as $status_id => $status) {
                    $this->_statuscount[$status_id] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                }
                foreach (tables\Issues::getTable()->getStatusCountByProjectID($this->getID()) as $status_id => $status_count) {
                    $this->_statuscount[$status_id] = $status_count;
                }
            }
        }

        public function getResolutionCount()
        {
            $this->_populateResolutionCount();

            return $this->_resolutioncount;
        }

        protected function _populateResolutionCount()
        {
            if ($this->_resolutioncount === null) {
                $this->_resolutioncount = [];
                $this->_resolutioncount[0] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                foreach (Resolution::getAll() as $resolution_id => $resolution) {
                    $this->_resolutioncount[$resolution_id] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                }
                foreach (tables\Issues::getTable()->getResolutionCountByProjectID($this->getID()) as $resolution_id => $resolution_count) {
                    $this->_resolutioncount[$resolution_id] = $resolution_count;
                }
            }
        }

        public function getCategoryCount()
        {
            $this->_populateCategoryCount();

            return $this->_categorycount;
        }

        protected function _populateCategoryCount()
        {
            if ($this->_categorycount === null) {
                $this->_categorycount = [];
                $this->_categorycount[0] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                foreach (Category::getAll() as $category_id => $category) {
                    $this->_categorycount[$category_id] = ['open' => 0, 'closed' => 0, 'percentage' => 0];
                }
                foreach (tables\Issues::getTable()->getCategoryCountByProjectID($this->getID()) as $category_id => $category_count) {
                    $this->_categorycount[$category_id] = $category_count;
                }
            }
        }

        public function getStateCount()
        {
            $this->_populateStateCount();

            return $this->_statecount;
        }

        protected function _populateStateCount()
        {
            if ($this->_statecount === null) {
                $this->_statecount = [];
                foreach (tables\Issues::getTable()->getStateCountByProjectID($this->getID()) as $state_id => $state_count) {
                    $this->_statecount[$state_id] = $state_count;
                }
            }
        }

        /**
         * Return this projects 10 most recent issues
         *
         * @return Issue[] A list of \pachno\core\entities\Issues
         */
        public function getRecentIssues($issuetype)
        {
            $issuetype_id = (is_object($issuetype)) ? $issuetype->getID() : $issuetype;
            $this->_populateRecentIssues($issuetype_id);

            return $this->_recentissues[$issuetype_id];
        }

        protected function _populateRecentIssues($issuetype)
        {
            $issuetype_id = (is_object($issuetype)) ? $issuetype->getID() : $issuetype;

            if (!array_key_exists($issuetype_id, $this->_recentissues)) {
                $this->_recentissues[$issuetype_id] = [];
                if ($res = tables\Issues::getTable()->getRecentByProjectIDandIssueType($this->getID(), $issuetype_id)) {
                    while ($row = $res->getNextRow()) {
                        try {
                            $issue = new Issue($row->get(tables\Issues::ID), $row);
                            if ($issue->hasAccess()) $this->_recentissues[$issuetype_id][$issue->getID()] = $issue;
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }

        /**
         * Return a list of recent activity for the project
         *
         * @param integer $limit Limit number of activities
         * @param bool $important
         * @param null $offset
         *
         * @return array
         */
        public function getRecentActivities($limit = null, $important = false, $offset = null)
        {
            $this->_populateRecentActivities($limit, $important, $offset);
            if ($limit !== null) {
                $recent_activities = array_slice($this->_recent_activities, 0, $limit, true);
            } else {
                $recent_activities = $this->_recent_activities;
            }

            return $recent_activities;
        }

        protected function _populateRecentActivities($limit = null, $important = true, $offset = null)
        {
            if ($this->_recent_activities === null) {
                $this->_recent_activities = [];
                foreach ($this->getRecentLogItems($limit, $important, $offset) as $log_item) {
                    if (!array_key_exists($log_item->getTime(), $this->_recent_activities)) {
                        $this->_recent_activities[$log_item->getTime()] = [];
                    }
                    $this->_recent_activities[$log_item->getTime()][] = $log_item;
                }
            }
        }

        /**
         * Return this projects most recent log items
         *
         * @param null $limit
         * @param bool $important
         * @param null $offset
         *
         * @return LogItem[]
         */
        public function getRecentLogItems($limit = null, $important = true, $offset = null)
        {
            $this->_populateLogItems($limit, $important, $offset);

            return ($important) ? $this->_recentimportantlogitems : $this->_recentlogitems;
        }

        protected function _populateLogItems($limit = null, $important = true, $offset = null)
        {
            $varname = ($important) ? '_recentimportantlogitems' : '_recentlogitems';
            if ($this->$varname === null) {
                $this->$varname = [];
                if ($important) {
                    $this->$varname = tables\LogItems::getTable()->getImportantByProjectID($this->getID(), $limit, $offset);
                } else {
                    $this->$varname = tables\LogItems::getTable()->getByProjectID($this->getID(), $limit, $offset);
                }
            }
        }

        public function clearRecentActivities()
        {
            $this->_recent_activities = null;
            $this->_recentissues = null;
            $this->_recentlogitems = null;
        }

        public function setWorkflowScheme(WorkflowScheme $scheme)
        {
            $this->_workflow_scheme_id = $scheme;
        }

        public function setIssuetypeScheme(IssuetypeScheme $scheme)
        {
            $this->_issuetype_scheme_id = $scheme;
        }

        /**
         * Return array of visible fields used by the Project
         *
         * @param bool $includeTextareas
         * @param array $excludeFields
         *
         * @return array
         */
        public function getIssueFields($includeTextareas = true, $excludeFields = [])
        {
            $fields = $this->getIssuetypeScheme()->getVisibleFields();

            foreach ($fields as $key => $field) {
                switch ($key) {
                    case 'user_pain':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Triaging: User pain');
                        break;
                    case 'percent_complete':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Percent completed');
                        break;
                    case 'build':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Release');
                        break;
                    case 'component':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Components');
                        break;
                    case 'edition':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Edition');
                        break;
                    case 'estimated_time':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Estimated time to complete');
                        break;
                    case 'spent_time':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Time spent working on the issue');
                        break;
                    case 'votes':
                        $fields[$key]['label'] = framework\Context::getI18n()->__('Votes');
                        break;
                    default:
                        if (!isset($fields[$key]['label'])) {
                            $fields[$key]['label'] = ucfirst($key);
                        }
                        break;
                }
            }

            if (!$includeTextareas) {
                unset($fields['description'], $fields['reproduction_steps']);
                foreach ($fields as $key => $field) {
                    if (in_array($field['type'], [DatatypeBase::INPUT_TEXTAREA_MAIN, DatatypeBase::INPUT_TEXTAREA_SMALL])) {
                        unset($fields[$key]);
                    }
                }
            }

            foreach ($excludeFields as $field) {
                unset($fields[$field]);
            }

            return $fields;
        }

        public function canSeeAllEditions()
        {
            return (bool)$this->_dualPermissionsCheck('canseeprojecthierarchy', 'canseeallprojecteditions');
        }

        protected function _dualPermissionsCheck($permission_1, $permission_2, $fallback = null)
        {
            $retval = $this->permissionCheck($permission_1);
            $retval = ($retval === null) ? $this->permissionCheck($permission_2) : $retval;

            if ($retval !== null) {
                return $retval;
            } else {
                return ($fallback !== null) ? $fallback : framework\Settings::isPermissive();
            }
        }

        public function canSeeAllComponents()
        {
            return (bool)$this->_dualPermissionsCheck('canseeprojecthierarchy', 'canseeallprojectcomponents');
        }

        public function canSeeAllBuilds()
        {
            return (bool)$this->_dualPermissionsCheck('canseeprojecthierarchy', 'canseeallprojectbuilds');
        }

        public function canSeeAllMilestones()
        {
            return (bool)$this->_dualPermissionsCheck('canseeprojecthierarchy', 'canseeallprojectmilestones');
        }

        public function canSeeTimeSpent()
        {
            return (bool)$this->permissionCheck('canseetimespent');
        }

        public function canVoteOnIssues()
        {
            return (bool)$this->permissionCheck('canvoteforissues');
        }

        public function getParentID()
        {
            return ($this->getParent() instanceof Project) ? $this->getParent()->getID() : 0;
        }

        public function clearParent()
        {
            $this->_parent = null;
        }

        /**
         * Whether or not this project has downloads enabled
         *
         * @return boolean
         */
        public function hasDownloads()
        {
            return (bool)$this->_has_downloads;
        }

        /**
         * Set whether this project has downloads enabled
         *
         * @param boolean $value
         */
        public function setDownloadsEnabled($value = true)
        {
            $this->_has_downloads = $value;
        }

        /**
         * Move issues from one step to another for a given issue type and conversions
         *
         * @param Issuetype $issuetype
         * @param array $conversions
         *
         * $conversions should be an array containing arrays:
         * array (
         *         array(oldstep, newstep)
         *         ...
         * )
         */
        public function convertIssueStepPerIssuetype(Issuetype $issuetype, array $conversions)
        {
            tables\Issues::getTable()->convertIssueStepByIssuetype($this, $issuetype, $conversions);
        }

        /**
         * Returns whether or not this item is locked
         *
         * @return boolean
         * @access public
         */
        public function isLocked()
        {
            return $this->_locked;
        }

        /**
         * Specify whether or not this item is locked
         *
         * @param boolean $locked [optional]
         */
        public function setLocked($locked = true)
        {
            $this->_locked = (bool)$locked;
        }

        /**
         * Returns new issues lock type
         *
         * @access public
         * @return integer
         */
        public function getIssuesLockType()
        {
            return $this->_issues_lock_type;
        }

        /**
         * Set new issues lock type
         *
         * @param boolean $lock_type [optional]
         */
        public function setIssuesLockType($lock_type)
        {
            $this->_issues_lock_type = $lock_type;
        }

        /**
         * @param User $user
         *
         * @return Role[]
         */
        public function getRolesForUser($user)
        {
            $this->_populateUserRoles();

            return (array_key_exists($user->getID(), $this->_user_roles)) ? $this->_user_roles[$user->getID()] : [];
        }

        protected function _populateUserRoles()
        {
            if ($this->_user_roles === null) {
                $this->_user_roles = tables\ProjectAssignedUsers::getTable()->getRolesForProject($this->getID());
            }
        }

        /**
         * @param Team $team
         *
         * @return Role[]
         */
        public function getRolesForTeam($team)
        {
            $this->_populateTeamRoles();

            return (array_key_exists($team->getID(), $this->_team_roles)) ? $this->_team_roles[$team->getID()] : [];
        }

        protected function _populateTeamRoles()
        {
            if ($this->_team_roles === null) {
                $this->_team_roles = tables\ProjectAssignedTeams::getTable()->getRolesForProject($this->getID());
            }
        }

        /**
         * @param User $user
         *
         * @return array
         */
        public function getPlanningColumns(User $user)
        {
            $columns = framework\Settings::get('planning_columns_' . $this->getID(), 'project', framework\Context::getScope()->getID(), $user->getID());
            $columns = explode(',', $columns);
            if (empty($columns) || (isset($columns[0]) && empty($columns[0]))) {
                // Default values
                $columns = [
                    'priority',
                    'estimated_time',
                    'spent_time',
                ];
            }
            // Set array keys to equal array values
            $columns = array_combine($columns, $columns);

            return $columns;
        }

        public function getMentionableUsers()
        {
            $users = [];
            foreach ($this->getAssignedUsers() as $user) {
                $users[$user->getID()] = $user;
            }
            foreach ($this->getAssignedTeams() as $team) {
                foreach ($team->getMembers() as $user) {
                    $users[$user->getID()] = $user;
                }
            }

            return $users;
        }

        public function preloadValues()
        {
            static $preloaded = false;

            if (!$preloaded) {
                $milestones = tables\Milestones::getTable()->getByProjectID($this->getID());
                unset($milestones);
                $issuetypes = IssueType::getAll();
                unset($issuetypes);
                tables\ListTypes::getTable()->populateItemCache();
            }

            $preloaded = true;
        }

        public function toJSON($detailed = true)
        {
            $jsonArray = [
                'id' => $this->getID(),
                'key' => $this->getKey(),
                'name' => $this->getName(),
                'href' => framework\Context::getRouting()->generate('project_dashboard', ['project_key' => $this->getKey()]),
                'deleted' => $this->isDeleted(),
                'archived' => $this->isArchived(),
                'icon' => $this->getIconName(),
                'description' => $this->getDescription(),
                'url_documentation' => $this->getDocumentationURL(),
                'url_homepage' => $this->getHomepage(),
                'url_wiki' => $this->getWikiURL(),
                'prefix_used' => $this->doesUsePrefix(),
                'prefix' => $this->getPrefix(),
                'parent_id' => $this->hasParent() ? $this->getParent()->getID() : null,
                'leader' => $this->hasLeader() ? $this->getLeader()->toJSON(false) : null,
                'owner' => $this->hasOwner() ? $this->getOwner()->toJSON(false) : null,
                'qa_responsible' => $this->hasQaResponsible() ? $this->getQaResponsible()->toJSON(false) : null,
                'client' => $this->hasClient() ? $this->getClient()->toJSON(false) : null,
                'released' => $this->isReleased(),
                'release_date' => $this->getReleaseDate(),
                'settings' => [
                    'workflow_scheme' => $this->hasWorkflowScheme() ? $this->getWorkflowScheme()->toJSON() : null,
                    'issuetype_scheme' => $this->getIssuetypeScheme()->toJSON(),
                    'builds_enabled' => $this->isBuildsEnabled(),
                    'editions_enabled' => $this->isEditionsEnabled(),
                    'components_enabled' => $this->isComponentsEnabled(),
                    'allow_freelancing' => $this->useStrictWorkflowMode(),
                ]
            ];

            if ($detailed) {
                $jsonArray['issues_count'] = $this->countAllIssues();
                $jsonArray['issues_count_open'] = $this->countAllOpenIssues();
                $jsonArray['issues_count_closed'] = $this->countAllClosedIssues();
            }

            return $jsonArray;
        }

        /**
         * Returns whether or not the project has been deleted
         *
         * @return boolean
         */
        public function isDeleted()
        {
            return $this->_deleted;
        }

        /**
         * Mark the project as deleted
         *
         * @return boolean
         */
        public function setDeleted()
        {
            $this->_deleted = true;
            $this->_dodelete = true;
            $this->_key = '';

            return true;
        }

        public function getIconName()
        {
            return ($this->hasIcon()) ? framework\Context::getRouting()->generate('showfile', ['id' => $this->getIcon()->getID()]) : $this->_icon_name;
        }

        public function hasIcon()
        {
            return ($this->getIcon() instanceof File);
        }

        /**
         * @return File
         */
        public function getIcon()
        {
            return $this->_b2dbLazyLoad('_large_icon');
        }

        public function setIcon(File $icon = null)
        {
            $this->_large_icon = $icon;
        }

        public function setIconName($icon_name)
        {
            $this->_icon_name = $icon_name;
        }

        /**
         * Returns the description
         *
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * Set the project description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        /**
         * Returns the documentation url
         *
         * @return string
         */
        public function getDocumentationURL()
        {
            return $this->_doc_url;
        }

        /**
         * Returns homepage
         *
         * @return string
         */
        public function getHomepage()
        {
            return $this->_homepage;
        }

        /**
         * Set the project homepage
         *
         * @param string $homepage
         */
        public function setHomepage($homepage)
        {
            $this->_homepage = $homepage;
        }

        /**
         * Returns the wiki url
         *
         * @return string
         */
        public function getWikiURL()
        {
            return $this->_wiki_url;
        }

        /**
         * Set the projects wiki url
         *
         * @param string $wiki_url
         */
        public function setWikiURL($wiki_url)
        {
            $this->_wiki_url = $wiki_url;
        }

        public function doesUsePrefix()
        {
            return $this->usePrefix();
        }

        /**
         * Returns whether or not the project uses prefix
         *
         * @return boolean
         */
        public function usePrefix()
        {
            return $this->_use_prefix;
        }

        /**
         * Returns the prefix for this project
         *
         * @return string
         */
        public function getPrefix()
        {
            return $this->_prefix;
        }

        /**
         * Set the project prefix
         *
         * @param string $prefix
         *
         * @return boolean
         */
        public function setPrefix($prefix)
        {
            if (preg_match('/[^a-zA-Z0-9]+/', $prefix) > 0) {
                return false;
            } else {
                $this->_prefix = $prefix;

                return true;
            }
        }

        /**
         * Return whether or not this project has a client associated
         *
         * @return boolean
         */
        public function hasClient()
        {
            return (bool)($this->getClient() instanceof Client);
        }

        public function hasWorkflowScheme()
        {
            return (bool)($this->getWorkflowScheme() instanceof WorkflowScheme);
        }

        /**
         * Return whether a user can change details about an issue without working on the issue
         *
         * @return boolean
         */
        public function useStrictWorkflowMode()
        {
            return (bool)!$this->_allow_freelancing;
        }

        /**
         * Returns the number of open issues for this project
         *
         * @return integer
         */
        public function countAllOpenIssues()
        {
            $this->_populateIssueCounts();

            return $this->_issuecounts['all']['open'];
        }

        /**
         * Get reportable time units
         *
         * @return array
         */
        public function getTimeUnits()
        {
            return $this->time_units_indexes;
        }

        public function applyTemplate($template)
        {
            $dashboard_views = [];
            switch ($template) {
                case 'team':
                    $this->setWorkflowSchemeID(Settings::get(Settings::SETTING_MULTI_TEAM_WORKFLOW_SCHEME));
                    $this->setIssuetypeSchemeID(Settings::get(Settings::SETTING_FULL_RANGE_ISSUETYPE_SCHEME));
                    $this->setBuildsEnabled(true);
                    $this->setEditionsEnabled(true);
                    $this->setComponentsEnabled(true);

                    $dashboard_views[DashboardView::VIEW_PROJECT_INFO] = ['column' => 1, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_TEAM] = ['column' => 1, 'order' => 2];
                    $dashboard_views[DashboardView::VIEW_PROJECT_DOWNLOADS] = ['column' => 1, 'order' => 3];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ACTIVITIES] = ['column' => 1, 'order' => 4];
                    $dashboard_views[DashboardView::VIEW_PROJECT_UPCOMING] = ['column' => 2, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 2, 'order' => 2, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_BUG_REPORT)];
                    break;
                case 'open-source':
                    $this->setWorkflowSchemeID(Settings::get(Settings::SETTING_BALANCED_WORKFLOW_SCHEME));
                    $this->setIssuetypeSchemeID(Settings::get(Settings::SETTING_BALANCED_ISSUETYPE_SCHEME));
                    $this->setBuildsEnabled(true);
                    $this->setEditionsEnabled(false);
                    $this->setComponentsEnabled(true);

                    $dashboard_views[DashboardView::VIEW_PROJECT_INFO] = ['column' => 1, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_TEAM] = ['column' => 1, 'order' => 2];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ACTIVITIES] = ['column' => 1, 'order' => 3];
                    $dashboard_views[DashboardView::VIEW_PROJECT_DOWNLOADS] = ['column' => 2, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 2, 'order' => 2, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_BUG_REPORT)];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 2, 'order' => 3, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_FEATURE_REQUEST)];
                    break;
                case 'classic':
                    $this->setWorkflowSchemeID(Settings::get(Settings::SETTING_BALANCED_WORKFLOW_SCHEME));
                    $this->setIssuetypeSchemeID(Settings::get(Settings::SETTING_BALANCED_ISSUETYPE_SCHEME));
                    $this->setBuildsEnabled(true);
                    $this->setEditionsEnabled(false);
                    $this->setComponentsEnabled(true);

                    $dashboard_views[DashboardView::VIEW_PROJECT_INFO] = ['column' => 1, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_TEAM] = ['column' => 1, 'order' => 2];
                    $dashboard_views[DashboardView::VIEW_PROJECT_DOWNLOADS] = ['column' => 1, 'order' => 3];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ACTIVITIES] = ['column' => 1, 'order' => 4];
                    $dashboard_views[DashboardView::VIEW_PROJECT_UPCOMING] = ['column' => 2, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 2, 'order' => 2, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_BUG_REPORT)];
                    break;
                case 'agile':
                    $this->setWorkflowSchemeID(Settings::get(Settings::SETTING_BALANCED_WORKFLOW_SCHEME));
                    $this->setIssuetypeSchemeID(Settings::get(Settings::SETTING_BALANCED_AGILE_ISSUETYPE_SCHEME));
                    $this->setBuildsEnabled(true);
                    $this->setEditionsEnabled(false);
                    $this->setComponentsEnabled(true);

                    $dashboard_views[DashboardView::VIEW_PROJECT_INFO] = ['column' => 1, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_TEAM] = ['column' => 1, 'order' => 2];
                    $dashboard_views[DashboardView::VIEW_PROJECT_DOWNLOADS] = ['column' => 1, 'order' => 3];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ACTIVITIES] = ['column' => 1, 'order' => 4];
                    $dashboard_views[DashboardView::VIEW_PROJECT_UPCOMING] = ['column' => 2, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 2, 'order' => 2, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_BUG_REPORT)];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 2, 'order' => 2, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_FEATURE_REQUEST)];
                    break;
                case 'service-desk':
                    $this->setWorkflowSchemeID(Settings::get(Settings::SETTING_SIMPLE_WORKFLOW_SCHEME));
                    $this->setIssuetypeSchemeID(Settings::get(Settings::SETTING_BALANCED_ISSUETYPE_SCHEME));
                    $this->setBuildsEnabled(true);
                    $this->setEditionsEnabled(false);
                    $this->setComponentsEnabled(true);

                    $dashboard_views[DashboardView::VIEW_PROJECT_INFO] = ['column' => 1, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_TEAM] = ['column' => 1, 'order' => 2];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 1, 'order' => 3, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_BUG_REPORT)];
                    $dashboard_views[DashboardView::VIEW_PROJECT_STATISTICS_PRIORITY] = ['column' => 2, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_STATISTICS_STATUS] = ['column' => 2, 'order' => 2];
                    break;
                case 'personal':
                    $this->setWorkflowSchemeID(Settings::get(Settings::SETTING_SIMPLE_WORKFLOW_SCHEME));
                    $this->setIssuetypeSchemeID(Settings::get(Settings::SETTING_SIMPLE_ISSUETYPE_SCHEME));
                    $this->setBuildsEnabled(false);
                    $this->setEditionsEnabled(false);
                    $this->setComponentsEnabled(true);
                    $this->setStrictWorkflowMode(false);

                    $dashboard_views[DashboardView::VIEW_PROJECT_INFO] = ['column' => 1, 'order' => 1];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ACTIVITIES] = ['column' => 1, 'order' => 2];
                    $dashboard_views[DashboardView::VIEW_PROJECT_RECENT_ISSUES] = ['column' => 2, 'order' => 1, 'subtype' => Settings::get(Settings::SETTING_ISSUETYPE_TASK)];
                    break;
            }

            $dashboard = $this->getDefaultDashboard();

            foreach ($dashboard_views as $view_type => $details) {
                $view = new DashboardView();
                $view->setDashboard($dashboard);
                $view->setType($view_type);
                if (isset($details['subtype'])) {
                    $view->setDetail($details['subtype']);
                }
                $view->setColumn($details['column']);
                $view->setSortOrder($details['order']);
                $view->save();
            }
        }

        public function setWorkflowSchemeID($scheme_id)
        {
            $this->_workflow_scheme_id = $scheme_id;
        }

        public function setIssuetypeSchemeID($scheme_id)
        {
            $this->_issuetype_scheme_id = $scheme_id;
        }

        /**
         * Set if the project uses builds
         *
         * @param boolean $builds_enabled
         */
        public function setBuildsEnabled($builds_enabled)
        {
            $this->_enable_builds = (bool)$builds_enabled;
        }

        /**
         * Set if the project uses editions
         *
         * @param boolean $editions_enabled
         */
        public function setEditionsEnabled($editions_enabled)
        {
            $this->_enable_editions = (bool)$editions_enabled;
        }

        /**
         * Set if the project uses components
         *
         * @param boolean $components_enabled
         */
        public function setComponentsEnabled($components_enabled)
        {
            $this->_enable_components = (bool)$components_enabled;
        }

        /**
         * Set whether a user can change details about an issue without working on the issue
         *
         * @param boolean $val
         */
        public function setStrictWorkflowMode($val)
        {
            $this->_allow_freelancing = !$val;
        }

        public function getDefaultDashboard()
        {
            foreach ($this->getDashboards() as $dashboard) {
                if ($dashboard->getIsDefault()) return $dashboard;
            }

            $dashboard = new Dashboard();
            $dashboard->setProject($this);
            $dashboard->setIsDefault(true);
            $dashboard->save();
            $this->_dashboards[] = $dashboard;

            return $dashboard;
        }

        /**
         * Returns an array of project dashboards
         *
         * @return Dashboard[]
         */
        public function getDashboards()
        {
            $this->_b2dbLazyLoad('_dashboards');

            return $this->_dashboards;
        }

        /**
         * Get current available statuses
         *
         * @return Status[]
         */
        public function getAvailableStatuses()
        {
            $statuses = [];

            $available_statuses = Status::getAll();
            $workflow_scheme = $this->getWorkflowScheme();
            $issue_types = $this->getIssuetypeScheme()->getIssuetypes();

            foreach ($issue_types as $issue_type) {
                $workflow = $workflow_scheme->getWorkflowForIssuetype($issue_type);
                foreach ($workflow->getSteps() as $step) {
                    if (array_key_exists($step->getLinkedStatusID(), $available_statuses)) {
                        $statuses[$step->getLinkedStatusID()] = $available_statuses[$step->getLinkedStatusID()];
                    }
                }
            }

            return $statuses;
        }

        /**
         * Pre save check for conflicting keys
         *
         * @param boolean $is_new
         */
        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            $key = $this->getKey();
            $project = self::getByKey($key);
            if ($project instanceof Project && $project->getID() != $this->getID()) {
                throw new InvalidArgumentException("A project with this key ({$key}, {$this->getID()}) already exists ({$project->getID()})");
            }
        }

        /**
         * Retrieve a project by its key
         *
         * @param string $key
         *
         * @return Project
         */
        public static function getByKey($key): ?Project
        {
            if ($key) {
                $key = mb_strtolower($key);
                self::_populateProjects();

                return (array_key_exists($key, self::$_projects)) ? self::$_projects[$key] : null;
            }

            return null;
        }

        protected function _postSave($is_new)
        {
            if ($is_new) {
                self::$_num_projects = null;
                self::$_projects = null;

                $dashboard = new Dashboard();
                $dashboard->setProject($this);
                $dashboard->save();

                framework\Context::setPermission("canseeproject", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("canseeprojecthierarchy", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("canmanageproject", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("page_project_allpages_access", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("canvoteforissues", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("canseetimespent", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("canlockandeditlockedissues", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("cancreateandeditissues", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("caneditissue", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("caneditissuecustomfields", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("canaddextrainformationtoissues", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);
                framework\Context::setPermission("canpostseeandeditallcomments", $this->getID(), "core", framework\Context::getUser()->getID(), 0, 0, true);

                framework\Event::createNew('core', 'pachno\core\entities\Project::_postSave', $this)->trigger();
            }
            if ($this->_dodelete) {
                tables\Issues::getTable()->markIssuesDeletedByProjectID($this->getID());
                $this->_dodelete = false;
            }
        }

        /**
         * Return the projects' associated issuetype scheme
         *
         * @return IssuetypeScheme
         */
        public function getIssuetypeScheme()
        {
            if (!$this->_issuetype_scheme_id instanceof IssuetypeScheme)
                $this->_b2dbLazyLoad('_issuetype_scheme_id');

            return $this->_issuetype_scheme_id;
        }

        protected function _generateKey()
        {
            if ($this->_key === null)
                $this->_key = preg_replace("/[^0-9a-zA-Z]/i", '', mb_strtolower($this->getName()));
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
         * Set the project name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
            $this->_key = mb_strtolower($this->getStrippedProjectName());
            if ($this->_key == '') $this->_key = 'project' . $this->getID();
        }

        public function getStrippedProjectName()
        {
            return preg_replace("/[^0-9a-zA-Z]/i", '', $this->getName());
        }

    }
