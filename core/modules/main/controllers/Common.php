<?php

namespace pachno\core\modules\main\controllers;

use pachno\core\framework,
    pachno\core\entities,
    pachno\core\entities\tables,
    pachno\core\modules\agile;

/**
 * actions for the main module
 */
class Common extends framework\Action
{

    /**
     * About page
     *
     * @param \pachno\core\framework\Request $request
     */
    public function runAbout(framework\Request $request)
    {
        $this->forward403unless($this->getUser()->hasPageAccess('about'));
    }

    /**
     * 404 not found page
     *
     * @Route(name="notfound", url="/404")
     * @param \pachno\core\framework\Request $request
     */
    public function runNotFound(framework\Request $request)
    {
        $this->getResponse()->setHttpStatus(404);
        $message = null;
    }

    /**
     * 403 forbidden page
     *
     * @param \pachno\core\framework\Request $request
     */
    public function runForbidden(framework\Request $request)
    {
        $this->getResponse()->setHttpStatus(403);
        $this->getResponse()->setTemplate('main/forbidden');
    }

}
