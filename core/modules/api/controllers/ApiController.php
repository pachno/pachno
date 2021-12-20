<?php

    namespace pachno\core\modules\api\controllers;

    use pachno\core\framework,
        pachno\core\entities;

    /**
     * Base controller class for actions in the api module
     */
    class ApiController extends framework\Action
    {

        /**
         * @param string $action
         *
         * @return string
         */
        public function getAuthenticationMethodForAction($action)
        {
            if ($action == 'authenticate') {
                return framework\Action::AUTHENTICATION_METHOD_DUMMY;
            }

            return framework\Action::AUTHENTICATION_METHOD_APPLICATION_PASSWORD;
        }

        /**
         * The currently selected project in actions where there is one
         *
         * @access protected
         * @property entities\Project $selected_project
         */
        public function preExecute(framework\Request $request, $action)
        {
            try {
                // Default to JSON if nothing is specified.
                $newFormat = $request->getParameter('format', 'json');
                $this->getResponse()->setTemplate(mb_strtolower($action) . '.' . $newFormat . '.php');
                $this->getResponse()->setupResponseContentType($newFormat);

                if ($this->getRouting()->getCurrentRoute()->getName() != 'api_authenticate' && framework\Context::getUser()->isGuest()) {
                    $this->getResponse()->setHttpStatus(401);
                    return $this->renderJSON(array('error' => "Invalid credentials"));
                }
            } catch (\Exception $e) {
                $this->getResponse()->setHttpStatus(500);
                return $this->renderJSON(array('error' => 'An exception occurred: ' . $e));
            }
        }

    }
