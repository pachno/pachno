<?php

    namespace pachno\core\modules\main\controllers;

    use pachno\core\framework;
    use pachno\core\framework\Request;

    /**
     * actions for the main module
     */
    class Common extends framework\Action
    {

        /**
         * About page
         *
         * @param Request $request
         */
        public function runAbout(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPageAccess('about'));
        }

        /**
         * 404 not found page
         *
         * @Route(name="notfound", url="/404")
         * @param Request $request
         */
        public function runNotFound(Request $request)
        {
            $this->getResponse()->setHttpStatus(404);
            $message = null;
        }

        /**
         * 403 forbidden page
         *
         * @param Request $request
         */
        public function runForbidden(Request $request)
        {
            $this->getResponse()->setHttpStatus(403);
            $this->getResponse()->setTemplate('main/forbidden');
        }

    }
