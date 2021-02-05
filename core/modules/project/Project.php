<?php

    namespace pachno\core\modules\project;

    use pachno\core\entities;
    use pachno\core\framework;
    use pachno\core\framework\CoreModule;
    use pachno\core\framework\Event;

    class Project extends CoreModule
    {

        /**
         * Listen to milestone save event and return correct agile component
         *
         * @Listener(module="core", identifier="header_menu_strip")
         *
         * @param Event $event
         */
        public function listenerMainMenustrip(Event $event)
        {
            $route = $event->getSubject();

            if (!$route instanceof framework\routing\Route)
                return;

            switch ($route->getName()) {
                case 'projects_list':
                    $component = framework\Action::returnComponentHTML('project/mainmenustrip');
                    $event->setReturnValue($component);
                    $event->setProcessed();
                    break;
                default:
                    if (framework\Context::getCurrentProject() instanceof entities\Project && in_array($route->getModuleName(), ['project', 'search', 'agile'])) {
                        $project = framework\Context::getCurrentProject();
                        $name_event = framework\Event::createNew('core', 'project/templates/projectheader/namelabel', $project)->trigger();
                        $name = ($name_event->isProcessed()) ? $name_event->getReturnValue() : framework\Context::getI18n()->__('Project dashboard');
                        $component = framework\Action::returnComponentHTML('project/projectheader', ['subpage' => $name]);
                        $event->setReturnValue($component);
                        $event->setProcessed();
                    }
            }
        }

        public function getPageName()
        {
            $i18n = framework\Context::getI18n();

            switch (framework\Context::getRouting()->getCurrentRoute()->getName()) {
                case 'project_releases':
                    return $i18n->__('Project releases');
                case 'project_dashboard':
                default:
                    return $i18n->__('Project dashboard');
            }
        }

    }
