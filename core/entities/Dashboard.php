<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\DashboardViews;
    use pachno\core\framework;

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
     * @Table(name="\pachno\core\entities\tables\Dashboards")
     */
    class Dashboard extends IdentifiableScoped
    {

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
         * Whether the dashboard is the default
         *
         * @var boolean
         * @Column(type="boolean", default=0)
         */
        protected $_is_default = false;

        /**
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_user_id;

        /**
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * @var Team
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Team")
         */
        protected $_team_id;

        /**
         * @var Client
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Client")
         */
        protected $_client_id;

        /**
         * Dashboard views
         *
         * @var DashboardView[]
         */
        protected $_dashboard_views = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200, default="main/dashboardlayoutstandard")
         */
        protected $_layout = 'main/dashboardlayoutstandard';

        public function setUser($user)
        {
            $this->_user_id = $user;
        }

        public function setTeam($team)
        {
            $this->_team_id = $team;
        }

        public function setClient($client)
        {
            $this->_client_id = $client;
        }

        public function setProject($project)
        {
            $this->_project_id = $project;
        }

        public function countViews()
        {
            $this->_populateViews();
            return count($this->_dashboard_views);
        }

        protected function _populateViews()
        {
            if (!is_array($this->_dashboard_views)) {
                $this->_dashboard_views = DashboardViews::getTable()->getByDashboardIdScoped($this->getID());
            }
        }

        /**
         * @return DashboardView[]
         */
        public function getViews()
        {
            $this->_populateViews();

            return $this->_dashboard_views;
        }

        public function getName()
        {
            return $this->_name;
        }

        public function setName($name)
        {
            $this->_name = $name;
        }

        public function getIsDefault()
        {
            return $this->_is_default;
        }

        public function setIsDefault($is_default)
        {
            $this->_is_default = $is_default;
        }

        public function getLayout()
        {
            return $this->_layout;
        }

        public function setLayout($layout)
        {
            $this->_layout = $layout;
        }

        public function canEdit()
        {
            if ($this->getProject() instanceof Project) {
                return framework\Context::getUser()->canEditProjectDetails($this->getProject());
            } elseif ($this->getUser() instanceof User) {
                return $this->getUser()->getID() == framework\Context::getUser()->getID();
            }
        }

        protected function _postSave(bool $is_new): void
        {
            if ($is_new) {
                switch ($this->getType()) {
                    case self::TYPE_USER:
                        $dv_issues = new DashboardView();
                        $dv_issues->setDashboard($this);
                        $dv_issues->setColumn(1);
                        $dv_issues->setType(DashboardView::VIEW_PROJECTS);
                        $dv_issues->setDetail(0);
                        $dv_issues->save();

                        $dv_logged = new DashboardView();
                        $dv_logged->setDashboard($this);
                        $dv_logged->setColumn(2);
                        $dv_logged->setType(DashboardView::VIEW_LOGGED_ACTIONS);
                        $dv_logged->setDetail(0);
                        $dv_logged->save();
                        break;
                    case self::TYPE_PROJECT:
//                        $dv_project_info = new DashboardView();
//                        $dv_project_info->setDashboard($this);
//                        $dv_project_info->setColumn(1);
//                        $dv_project_info->setType(DashboardView::VIEW_PROJECT_INFO);
//                        $dv_project_info->setDetail(0);
//                        $dv_project_info->save();
//
//                        $dv_project_team = new DashboardView();
//                        $dv_project_team->setDashboard($this);
//                        $dv_project_team->setColumn(1);
//                        $dv_project_team->setType(DashboardView::VIEW_PROJECT_TEAM);
//                        $dv_project_team->setDetail(0);
//                        $dv_project_team->save();
//
//                        $dv_project_statistics = new DashboardView();
//                        $dv_project_statistics->setDashboard($this);
//                        $dv_project_statistics->setColumn(2);
//                        $dv_project_statistics->setType(DashboardView::VIEW_PROJECT_STATISTICS_LAST15);
//                        $dv_project_statistics->setDetail(0);
//                        $dv_project_statistics->save();
//
//                        $dv_project_activities = new DashboardView();
//                        $dv_project_activities->setDashboard($this);
//                        $dv_project_activities->setColumn(2);
//                        $dv_project_activities->setType(DashboardView::VIEW_PROJECT_RECENT_ACTIVITIES);
//                        $dv_project_activities->setDetail(0);
//                        $dv_project_activities->save();
                        break;
                }
            }
        }

        public function getType()
        {
            if ($this->getProject() instanceof Project)
                return self::TYPE_PROJECT;
            if ($this->getUser() instanceof User)
                return self::TYPE_USER;
            if ($this->getClient() instanceof Client)
                return self::TYPE_CLIENT;
            if ($this->getTeam() instanceof Team)
                return self::TYPE_TEAM;
        }

        /**
         * Returns the associated project
         *
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project_id');
        }

        /**
         * Returns the associated user
         *
         * @return User
         */
        public function getUser()
        {
            return $this->_b2dbLazyLoad('_user_id');
        }

        /**
         * Returns the associated client
         *
         * @return Client
         */
        public function getClient()
        {
            return $this->_b2dbLazyLoad('_client_id');
        }

        /**
         * Returns the associated team
         *
         * @return Team
         */
        public function getTeam()
        {
            return $this->_b2dbLazyLoad('_team_id');
        }

    }
