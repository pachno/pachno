<?php

    namespace pachno\core\modules\profile;

    use pachno\core\framework\CoreModule;
    use pachno\core\framework;
    use pachno\core\entities\tables;

    class Profile extends CoreModule
    {

        /**
         * @Listener(module="core", identifier="get_backdrop_partial")
         * @param framework\Event $event
         */
        public function listenGetBackdropPartial(framework\Event $event)
        {
            $request = $event->getParameter('request');

            switch ($request['key']) {
                case 'usercard':
                    $event->setReturnValue('profile/usercard');
                    if ($user_id = $request->getParameter('user_id')) {
                        $user = tables\Users::getTable()->selectById($user_id);
                        $event->addToReturnList($user, 'user');
                    }
                    $event->setProcessed(true);
                    break;
            }
        }


    }
