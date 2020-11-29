<?php

    namespace pachno\core\modules\auth;

    use pachno\core\framework\CoreModule;
    use pachno\core\framework;
    use pachno\core\entities\tables;

    class Auth extends CoreModule
    {

        /**
         * @Listener(module="core", identifier="get_backdrop_partial")
         * @param framework\Event $event
         */
        public function listenGetBackdropPartial(framework\Event $event)
        {
            $request = $event->getParameter('request');

            switch ($request['key']) {
                case 'login':
                    $event->setReturnValue('auth/loginpopup');
                    $event->addToReturnList($this->getComponentHTML('auth/login', ['section' => $request->getParameter('section', 'login')]), 'content');
                    $event->addToReturnList(false, 'mandatory');
                    $event->setProcessed(true);
                    break;
                case 'enable_2fa':
                    $event->setReturnValue('auth/enable2fa');
                    $event->setProcessed(true);
                    $template_name = 'main/enable2fa';
                    break;
            }
        }


    }
