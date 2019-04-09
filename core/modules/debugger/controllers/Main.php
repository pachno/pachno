<?php

namespace pachno\core\modules\debugger\controllers;

use pachno\core\framework;

/**
 * actions for the debugger module
 */
class Main extends framework\Action
{

    public function runIndex(framework\Request $request)
    {
        $this->getResponse()->setDecoration(\pachno\core\framework\Response::DECORATE_NONE);
        $this->pachno_summary = framework\Context::getDebugData($request['debug_id']);
    }

}
