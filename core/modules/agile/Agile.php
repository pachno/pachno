<?php

    namespace pachno\core\modules\agile;

    use pachno\core\entities\Project;
    use pachno\core\framework;
    use pachno\core\framework\CoreModule;

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

        protected function _loadFixtures($scope)
        {
        }

        protected function _uninstall()
        {
            if (framework\Context::getScope()->isDefault())
            {
            }
        }

        /**
         * User dashboard project list buttons listener
         *
         * @Listener(module="core", identifier="main\Components::DashboardViewUserProjects::links")
         *
         * @param \pachno\core\framework\Event $event
         */
        public function userDashboardProjectButtonLinks(framework\Event $event)
        {
            $routing = framework\Context::getRouting();
            $i18n = framework\Context::getI18n();
            $event->addToReturnList(array('url' => $routing->generate('agile_index', array('project_key' => '%project_key%')), 'text' => $i18n->__('Planning')));
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="project/templates/projectheader")
         *
         * @param \pachno\core\framework\Event $event
         */
        public function projectHeaderLinks(framework\Event $event)
        {
            $board = \pachno\core\entities\AgileBoard::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('board_id'));
            if ($board instanceof \pachno\core\entities\AgileBoard)
            {
                framework\ActionComponent::includeComponent('agile/projectheaderstriplinks', array('project' => $event->getSubject(), 'board' => $board));
            }
        }

        /**
         * Listen to milestone save event and return correct agile component
         *
         * @Listener(module="project", identifier="runMilestone::post")
         *
         * @param \pachno\core\framework\Event $event
         */
        public function milestoneSave(framework\Event $event)
        {
            $board = \pachno\core\entities\AgileBoard::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('board_id'));
            if ($board instanceof \pachno\core\entities\AgileBoard)
            {
                $component = framework\Action::returnComponentHTML('agile/milestonebox', array('milestone' => $event->getSubject(), 'board' => $board, 'include_counts' => true));
                $event->setReturnValue($component);
                $event->setProcessed();
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="templates/headermainmenu::projectmenulinks")
         *
         * @param \pachno\core\framework\Event $event
         */
        public function headerMenuProjectLinks(framework\Event $event)
        {
            if ($event->getSubject() instanceof Project)
            {
                $boards = \pachno\core\entities\AgileBoard::getB2DBTable()->getAvailableProjectBoards(framework\Context::getUser()->getID(), $event->getSubject()->getID());
                framework\ActionComponent::includeComponent('agile/headermenuprojectlinks', array('project' => $event->getSubject(), 'boards' => $boards));
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="project_overview_item_links")
         *
         * @param \pachno\core\framework\Event $event
         */
        public function dashboardProjectLinks(framework\Event $event)
        {
            $boards = \pachno\core\entities\AgileBoard::getB2DBTable()->getAvailableProjectBoards(framework\Context::getUser()->getID(), $event->getSubject()->getID());
            framework\ActionComponent::includeComponent('agile/projectlinks', array('project' => $event->getSubject(), 'boards' => $boards));
        }

        /**
         * @Listener(module='core', identifier='get_backdrop_partial')
         * @param \pachno\core\framework\Event $event
         */
        public function listen_get_backdrop_partial(framework\Event $event)
        {
            $request = framework\Context::getRequest();
            $options = array();

            switch ($event->getSubject())
            {
                case 'agileboard':
                    $template_name = 'agile/editagileboard';
                    $board = ($request['board_id']) ? \pachno\core\entities\tables\AgileBoards::getTable()->selectById($request['board_id']) : new \pachno\core\entities\AgileBoard();
                    if (!$board->getID())
                    {
                        $board->setAutogeneratedSearch(\pachno\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES);
                        $board->setTaskIssuetype(framework\Settings::get('issuetype_task'));
                        $board->setEpicIssuetype(framework\Settings::get('issuetype_epic'));
                        $board->setIsPrivate($request->getParameter('is_private', true));
                        $board->setProject($request['project_id']);
                    }
                    $options['board'] = $board;
                    break;
                case 'milestone_finish':
                    $template_name = 'agile/milestonefinish';
                    $options['project'] = \pachno\core\entities\tables\Projects::getTable()->selectById($request['project_id']);
                    $options['board'] = \pachno\core\entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
                    $options['milestone'] = \pachno\core\entities\tables\Milestones::getTable()->selectById($request['milestone_id']);
                    if (!$options['milestone']->hasReachedDate()) $options['milestone']->setReachedDate(time());
                    break;
                case 'agilemilestone':
                    $template_name = 'agile/milestone';
                    $options['project'] = \pachno\core\entities\tables\Projects::getTable()->selectById($request['project_id']);
                    $options['board'] = \pachno\core\entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
                    if ($request->hasParameter('milestone_id'))
                        $options['milestone'] = \pachno\core\entities\tables\Milestones::getTable()->selectById($request['milestone_id']);
                    break;
                default:
                    return;
            }
            
            foreach ($options as $key => $value)
            {
                $event->addToReturnList($value, $key);
            }
            $event->setReturnValue($template_name);
            $event->setProcessed();
        }

    }

