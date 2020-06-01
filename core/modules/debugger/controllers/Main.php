<?php

    namespace pachno\core\modules\debugger\controllers;

    use pachno\core\framework;
    use pachno\core\framework\Response;

    /**
     * actions for the debugger module
     */
    class Main extends framework\Action
    {

        public function runIndex(framework\Request $request)
        {
            $this->getResponse()->setDecoration(Response::DECORATE_NONE);
            return $this->renderJSON([
                'content' => $this->getComponentHTML('debugger', [
                    'pachno_summary' => framework\Context::getDebugData($request['debug_id'])
                ])
            ]);
        }

    }
