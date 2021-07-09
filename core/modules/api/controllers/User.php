<?php

    namespace pachno\core\modules\api\controllers;

    use pachno\core\framework,
        pachno\core\entities,
        pachno\core\entities\tables;

    /**
     * User actions for the api module
     *
     * @property entities\User $user
     *
     * @Routes(name_prefix="api_", url_prefix="/api/v1")
     */
    class User extends ApiController
    {

        /**
         * @param string $action
         * @return string
         */
        public function getAuthenticationMethodForAction($action)
        {
            if ($action === 'authenticate') {
                return framework\Action::AUTHENTICATION_METHOD_DUMMY;
            }

            return parent::getAuthenticationMethodForAction($action);
        }

        /**
         * Authenticate an application using a one-time application password.
         * Creates a token to be used for subsequent requests.
         *
         * @Route(name="authenticate", url="/authenticate")
         * @AnonymousRoute
         * @param framework\Request $request
         */
        public function runAuthenticate(framework\Request $request): framework\JsonOutput
        {
            framework\Logging::log('Authenticating new application password.', 'api', framework\Logging::LEVEL_INFO);

            $username = trim($request['username']);
            $password = trim($request['password']);
            if ($username) {
                $user = tables\Users::getTable()->getByUsername($username);
                if ($password && $user instanceof entities\User) {
                    foreach ($user->getApplicationPasswords() as $app_password) {
                        // Only return the token for new application passwords!
                        if (!$app_password->isUsed()) {
                            if (password_verify($password, $app_password->getHashPassword())) {
                                $token = $app_password->verify();
                                $app_password->save();
                                return $this->renderJSON(['token' => $token, 'name' => $app_password->getName(), 'created_at' => $app_password->getCreatedAt()]);
                            }
                        }
                    }
                }
                framework\Logging::log('No password matched.', 'api', framework\Logging::LEVEL_INFO);
            }

            $this->getResponse()->setHttpStatus(400);
            return $this->renderJSON(['error' => "Incorrect username ({$username}) or application password ({$password})"]);
        }

        /**
         * @Route(name="me", url="/me")
         * @param framework\Request $request
         */
        public function runMe(framework\Request $request): framework\JsonOutput
        {
            return $this->renderJSON(['user' => $this->getUser()->toJSON()]);
        }

    }
