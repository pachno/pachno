<?php

    namespace pachno\core\modules\search;

    use pachno\core\framework;

    class Search extends framework\CoreModule
    {

        /**
         * Header "Publish" page names
         *
         * @Listener(module="core", identifier="project/templates/projectheader::pagename")
         *
         * @param framework\Event $event
         */
        public function dashboardProjectHeaderPagename(framework\Event $event)
        {
            switch (framework\Context::getRouting()->getCurrentRoute()->getModuleName()) {
                case 'search':
                    $event->setReturnValue(framework\Context::getI18n()->__('Issues'));
                    $event->setProcessed(true);
                    break;
            }
        }

    }
