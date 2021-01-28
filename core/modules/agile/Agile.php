<?php

    namespace pachno\core\modules\agile;

    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\Project;
    use pachno\core\entities\SavedSearch;
    use pachno\core\entities\tables\AgileBoards;
    use pachno\core\entities\tables\Milestones;
    use pachno\core\entities\tables\Projects;
    use pachno\core\framework;
    use pachno\core\framework\CoreModule;
    use pachno\core\framework\Event;

    /**
     * Agile module
     *
     * @author
     * @version 0.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package agile
     * @subpackage core
     */

    /**
     * Agile module
     *
     * @package agile
     * @subpackage core
     *
     * @Table(name="\pachno\core\entities\tables\Modules")
     */
    class Agile extends CoreModule
    {

        const VERSION = '1.0';

        protected $_name = 'agile';

        protected $_longname = 'Agile';

        protected $_module_config_title = 'Agile';

        protected $_module_config_description = 'Agile - planning and whiteboard for agile teams';

        protected $_description = 'Agile - planning and whiteboard for agile teams';

        /**
         * User dashboard project list buttons listener
         *
         * @Listener(module="core", identifier="main\Components::DashboardViewUserProjects::links")
         *
         * @param Event $event
         */
        public function userDashboardProjectButtonLinks(Event $event)
        {
            $routing = framework\Context::getRouting();
            $i18n = framework\Context::getI18n();
            $event->addToReturnList(['url' => $routing->generate('agile_index', ['project_key' => '%project_key%']), 'text' => $i18n->__('Planning')]);
        }

        /**
         * Header "Agile" menu and board list
         *
         * @param Event $event
         */
        public function projectHeaderLinks(Event $event)
        {
            $board = AgileBoard::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('board_id'));
            if ($board instanceof AgileBoard) {
                framework\ActionComponent::includeComponent('agile/projectheaderstriplinks', ['project' => $event->getSubject(), 'board' => $board]);
            }
        }

        /**
         * Listen to milestone save event and return correct agile component
         *
         * @Listener(module="project", identifier="runMilestone::post")
         *
         * @param Event $event
         */
        public function milestoneSave(Event $event)
        {
            $board = AgileBoard::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('board_id'));
            if ($board instanceof AgileBoard) {
                $component = framework\Action::returnComponentHTML('agile/milestonelistitem', ['milestone' => $event->getSubject(), 'board' => $board, 'selected_milestone' => $event->getSubject()]);
                $event->setReturnValue($component);
                $event->setProcessed();
            }
        }

        /**
         * Header selected tab listener
         *
         * @Listener(module="core", identifier="header_menu::selectedTab")
         *
         * @param Event $event
         */
        public function headerMenuSelectedTab(Event $event)
        {
            if (framework\Context::getRouting()->getCurrentRoute()->getModuleName() == 'agile') {
                $event->setReturnValue('projects');
                $event->setProcessed();
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="templates/header::projectmenulinks")
         *
         * @param Event $event
         */
        public function headerMenuProjectLinks(Event $event)
        {
            if ($event->getSubject() instanceof Project) {
                $boards = AgileBoard::getB2DBTable()->getAvailableProjectBoards(framework\Context::getUser()->getID(), $event->getSubject()->getID());
                framework\ActionComponent::includeComponent('agile/headermenuprojectlinks', ['project' => $event->getSubject(), 'boards' => $boards]);
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="project_header_buttons")
         *
         * @param Event $event
         */
        public function dashboardProjectButtons(Event $event)
        {
            if (framework\Context::getRouting()->getCurrentRoute()->getName() == 'agile_index') {
                framework\ActionComponent::includeComponent('agile/projectheaderbutton', ['project' => $event->getSubject()]);
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="project/templates/projectheader::pagename")
         *
         * @param Event $event
         */
        public function dashboardProjectHeaderPagename(Event $event)
        {
            switch (framework\Context::getRouting()->getCurrentRoute()->getName()) {
                case 'agile_index':
                    $event->setReturnValue(framework\Context::getI18n()->__('Project boards'));
                    $event->setProcessed(true);
                    break;
                case 'agile_board':
                case 'agile_whiteboard':
                    $event->setReturnValue(framework\Context::getI18n()->__('Project board'));
                    $event->setProcessed(true);
                    break;
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="project/templates/projectheader/after-spacer")
         *
         * @param Event $event
         */
        public function dashboardProjectHeaderAgileTabs(Event $event)
        {
            switch (framework\Context::getRouting()->getCurrentRoute()->getName()) {
                case 'agile_board':
                case 'agile_whiteboard':
                    $board = AgileBoards::getTable()->selectById(framework\Context::getRequest()->getParameter('board_id'));
                    framework\ActionComponent::includeComponent('agile/headermenuagiletabs', ['project' => $event->getSubject(), 'board' => $board]);
                    break;
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="project_overview_item_links")
         *
         * @param Event $event
         */
        public function dashboardProjectLinks(Event $event)
        {
            $boards = AgileBoard::getB2DBTable()->getAvailableProjectBoards(framework\Context::getUser()->getID(), $event->getSubject()->getID());
            framework\ActionComponent::includeComponent('agile/projectlinks', ['project' => $event->getSubject(), 'boards' => $boards]);
        }

        /**
         * @Listener(module='core', identifier='get_backdrop_partial')
         * @param Event $event
         */
        public function listen_get_backdrop_partial(Event $event)
        {
            $request = framework\Context::getRequest();
            $options = [];

            switch ($event->getSubject()) {
                case 'agileboard':
                    $template_name = 'agile/editagileboard';
                    $board = ($request['board_id']) ? AgileBoards::getTable()->selectById($request['board_id']) : new AgileBoard();
                    if (!$board->getID()) {
                        $board->setAutogeneratedSearch(SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES);
                        $board->setTaskIssuetype(framework\Settings::get('issuetype_task'));
                        $board->setEpicIssuetype(framework\Settings::get('issuetype_epic'));
                        $board->setIsPrivate($request->getParameter('is_private', true));
                        $board->setProject($request['project_id']);
                    }
                    $options['board'] = $board;
                    break;
                case 'milestone_finish':
                    $template_name = 'agile/milestonefinish';
                    $options['project'] = Projects::getTable()->selectById($request['project_id']);
                    $options['board'] = AgileBoards::getTable()->selectById($request['board_id']);
                    $options['milestone'] = Milestones::getTable()->selectById($request['milestone_id']);
                    if (!$options['milestone']->hasReachedDate()) $options['milestone']->setReachedDate(time());
                    break;
                case 'agilemilestone':
                    $template_name = 'agile/milestone';
                    $options['project'] = Projects::getTable()->selectById($request['project_id']);
                    $options['board'] = AgileBoards::getTable()->selectById($request['board_id']);
                    if ($request->hasParameter('milestone_id'))
                        $options['milestone'] = Milestones::getTable()->selectById($request['milestone_id']);
                    break;
                default:
                    return;
            }

            foreach ($options as $key => $value) {
                $event->addToReturnList($value, $key);
            }
            $event->setReturnValue($template_name);
            $event->setProcessed();
        }

        protected function _initialize()
        {
        }

        protected function _addAvailablePermissions()
        {
        }

        protected function _addListeners()
        {
        }

        protected function _install($scope)
        {
        }

        protected function _uninstall()
        {
            if (framework\Context::getScope()->isDefault()) {
            }
        }

    }

