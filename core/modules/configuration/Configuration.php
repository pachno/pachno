<?php

    namespace pachno\core\modules\configuration;

    use pachno\core\framework;

    class Configuration extends framework\CoreModule
    {

        /**
         * Listen to header menu strip
         *
         * @Listener(module="core", identifier="header_menu_strip")
         *
         * @param framework\Event $event
         */
        public function listenerMainMenustrip(framework\Event $event)
        {
            $route = $event->getSubject();

            if (!$route instanceof framework\routing\Route)
                return;

            if ($route->getModuleName() == 'configuration') {
                $component = framework\Action::returnComponentHTML('configuration/mainmenustrip');
                $event->setReturnValue($component);
                $event->setProcessed();
            }
        }

    }
