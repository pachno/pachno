<?php

    namespace pachno\core\framework;

    use Exception;
    use pachno\core\entities\User;
    use pachno\core\framework\exceptions\ActionNotAllowedException;

    /**
     * Action class used in the MVC part of the framework
     *
     * @package pachno
     * @subpackage mvc
     */
    class Action extends Parameterholder
    {

        public const AUTHENTICATION_METHOD_CORE = 'core';

        public const AUTHENTICATION_METHOD_DUMMY = 'dummy';

        public const AUTHENTICATION_METHOD_CLI = 'cli';

        public const AUTHENTICATION_METHOD_RSS_KEY = 'rss_key';

        public const AUTHENTICATION_METHOD_APPLICATION_PASSWORD = 'application_password';

        public const AUTHENTICATION_METHOD_ELEVATED = 'elevated';

        public const AUTHENTICATION_METHOD_BASIC = 'basic';

        /**
         * Retrieves authentication method for running an
         * action. Default method implementation will only detect
         * whether the CLI is being used, and set the authentiction
         * method appropriatelly, otherwise defaulting to standard
         * (core) user authentication.
         *
         * Modules implementing their own controller should override
         * this method if certain actions can be performed by
         * providing application password, require elevated privileges
         * etc.
         *
         * Use the AUTHENTICATION_METHOD constants from above when
         * returning or checking the authentication method.
         *
         * @return string Authentication method.
         */
        public function getAuthenticationMethodForAction($action)
        {
            if (Context::isCLI())
                return self::AUTHENTICATION_METHOD_CLI;

            return self::AUTHENTICATION_METHOD_CORE;
        }

        /**
         * Function that is executed before any actions in an action class
         *
         * @param Request $request The request object
         * @param string $action The action that is being triggered
         */
        public function preExecute(Request $request, $action)
        {

        }

        /**
         * Redirect from one action method to another in the same action
         *
         * @param string $redirect_to The method to redirect to
         */
        public function redirect(string $redirect_to)
        {
            $actionName = 'run' . ucfirst($redirect_to);
            $this->getResponse()->setTemplate(mb_strtolower($redirect_to) . '.' . Context::getRequest()->getRequestedFormat() . '.php');
            if (method_exists($this, $actionName)) {
                return $this->$actionName(Context::getRequest());
            }
            throw new exceptions\ActionNotFoundException("The action \"{$actionName}\" does not exist in " . get_class($this));
        }

        /**
         * Sets the response to 404 and shows an error, with an optional message
         *
         * @param string $message [optional] The message
         */
        public function return404($message = null)
        {
            if (Context::getRequest()->isAjaxCall() || Context::getRequest()->getRequestedFormat() == 'json') {
                $this->getResponse()->ajaxResponseText(Response::HTTP_STATUS_NOT_FOUND, $message);
            }

            $this->message = $message;
            $this->getResponse()->setHttpStatus(Response::HTTP_STATUS_NOT_FOUND);
            $this->getResponse()->setTemplate('main/notfound');

            return false;
        }

        /**
         * Sets the response to 304 (Not modified) and exits
         */
        public function return304()
        {
            $this->getResponse()->setHttpStatus(Response::HTTP_STATUS_NOT_MODIFIED);
            $this->getResponse()->renderHeaders();
            exit();
        }

        /**
         * Sets response code to 400 (bad request), sending optional
         * error message to caller.
         *
         * @param string $message [optional] Error message to return to caller.
         */
        public function return400($message = null)
        {
            if (Context::getRequest()->isAjaxCall() || Context::getRequest()->getRequestedFormat() == 'json') {
                $this->getResponse()->ajaxResponseText(Response::HTTP_STATUS_BAD_REQUEST, $message);
            }

            $this->message = $message;
            $this->getResponse()->setHttpStatus(Response::HTTP_STATUS_BAD_REQUEST);
            $this->getResponse()->setTemplate('main/http400');

            return false;
        }

        /**
         * Forward the user with HTTP status code 403 and an (optional) message
         *
         * @param string $message [optional] The message
         */
        public function forward403($message = null)
        {
            $this->forward403unless(false, $message);
        }

        /**
         * Forward the user with HTTP status code 403 and an (optional) message
         * based on a boolean check
         *
         * @param boolean $condition
         * @param string $message [optional] The message
         */
        public function forward403unless($condition, $message = null)
        {
            if (!$condition) {
                $message = ($message === null) ? Context::getI18n()->__('Please log in to continue') : $message;
                if (Context::getUser()->isGuest()) {
                    Context::setMessage('login_message_err', htmlentities($message));
                    Context::setMessage('login_force_redirect', true);
                    Context::setMessage('login_referer', Context::getRouting()->generate(Context::getRouting()->getCurrentRoute()->getName(), Context::getRequest()->getParameters()));
                    $this->forward(Context::getRouting()->generate('auth_login_page'), Response::HTTP_STATUS_FORBIDDEN);
                } elseif (Context::getRequest()->isAjaxCall()) {
                    $this->getResponse()->setHttpStatus(Response::HTTP_STATUS_FORBIDDEN);
                    throw new Exception($message);
                } else {
                    throw new ActionNotAllowedException($message);
                }
            }
        }

        /**
         * Forward the user to a specified url
         *
         * @param string $url The URL to forward to
         * @param integer $code [optional] HTTP status code
         */
        public function forward($url, $code = Response::HTTP_STATUS_OK)
        {
            if (Context::getRequest()->isAjaxCall() || Context::getRequest()->getRequestedFormat() == 'json') {
                $this->getResponse()->ajaxResponseText($code, Context::getMessageAndClear('forward'));
            }
            Logging::log("Forwarding to url {$url}");

            Logging::log('Triggering header redirect function');
            $this->getResponse()->headerRedirect($url, $code);
        }

        /**
         * Return the response object
         *
         * @return Response
         */
        protected function getResponse()
        {
            return Context::getResponse();
        }

        public function forward403if($condition, $message = null)
        {
            $this->forward403unless(!$condition, $message);
        }

        /**
         * Render a component
         *
         * @param string $template the component name
         * @param array $params component parameters
         *
         * @return boolean
         */
        public function renderComponent($template, $params = [])
        {
            echo ActionComponent::includeComponent($template, $params);

            return true;
        }

        /**
         * Returns the HTML output from a component, but doesn't render it
         *
         * @param string $template the component name
         * @param array $params component parameters
         *
         * @return boolean
         */
        public function getComponentHTML($template, $params = [])
        {
            return self::returnComponentHTML($template, $params);
        }

        /**
         * Returns the HTML output from a component, but doesn't render it
         *
         * @param string $template the component name
         * @param array $params component parameters
         *
         * @return boolean
         */
        public static function returnComponentHTML($template, $params = [])
        {
            $current_content = ob_get_clean();
            (Context::isCLI()) ? ob_start() : ob_start('mb_output_handler');
            echo ActionComponent::includeComponent($template, $params);
            $component_content = ob_get_clean();
            (Context::isCLI()) ? ob_start() : ob_start('mb_output_handler');
            echo $current_content;

            return $component_content;
        }

        /**
         * Return the i18n object
         *
         * @return I18n
         */
        protected function getI18n()
        {
            return Context::getI18n();
        }

        /**
         * Return the current logged in user
         *
         * @return User
         */
        protected function getUser()
        {
            return Context::getUser();
        }

        /**
         * Verify that the specified user has a valid membership in the current scope
         *
         * @param User $user
         *
         * @return bool
         */
        protected function verifyScopeMembership(User $user)
        {
            if (!Context::getScope()->isDefault() && !$user->isGuest() && !$user->isConfirmedMemberOfScope(Context::getScope())) {
                $route = self::getRouting()->generate('add_scope');
                if (Context::getRequest()->isAjaxCall()) {
                    return $this->renderJSON(['forward' => $route]);
                } else {
                    $this->getResponse()->headerRedirect($route);
                }
            }
        }

        /**
         * Return the routing object
         *
         * @return Routing
         */
        protected function getRouting()
        {
            return Context::getRouting();
        }

        /**
         * Renders JSON output, also takes care of setting the correct headers
         *
         * @param mixed $text An array, or text, to serve as json
         *
         * @return JsonOutput
         */
        public function renderJSON($text = []): JsonOutput
        {
            return new JsonOutput($text);
        }

        /**
         * Renders plaintext output, also takes care of setting the correct headers
         *
         * @param string $text An array, or text, to serve as json
         * @param ?string $content_type A content type (default is text/plain unless specified)
         *
         * @return TextOutput
         */
        public function renderText(string $text = '', string $content_type = null): TextOutput
        {
            return new TextOutput($text, $content_type);
        }

    }
