<?php

    namespace pachno\core\framework;

    /**
     * Parameter holder class used in the MVC part of the framework for \pachno\core\entities\Action and \pachno\core\entities\ActionComponent
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage mvc
     */

    use pachno\core\entities\tables\Users;
    use pachno\core\entities\User;
    use pachno\core\entities\UserSession;
    use pachno\core\framework\exceptions\ElevatedLoginException;

    /**
     * Parameter holder class used in the MVC part of the framework for \pachno\core\entities\Action and \pachno\core\entities\ActionComponent
     *
     * @package pachno
     * @subpackage mvc
     */
    class AuthenticationBackend implements interfaces\AuthenticationProvider
    {
        function getAuthenticationMethod()
        {
            return interfaces\AuthenticationProvider::AUTHENTICATION_TYPE_TOKEN;
        }

        function autoVerifyLogin($username, $password, $is_elevated = false)
        {
        }

        /**
         * Verify username and token against valid tokens for that user
         *
         * @param string $username
         * @param string $token
         * @param bool $is_elevated
         *
         * @return User|null
         * @throws ElevatedLoginException
         */
        function autoVerifyToken($username, $token, $is_elevated = false)
        {
            $user = Users::getTable()->getByUsername($username);

            if (!$user instanceof User)
            {
                Context::logout();
                return;
            }

            if (!$user->verifyUserSession($token, $is_elevated))
            {
                if ($is_elevated)
                {
                    Context::setUser($user);
                    Context::getRouting()->setCurrentRouteName('elevated_login_page');
                    throw new ElevatedLoginException('reenter');
                }

                $user = null;
            }

            return $user;
        }

        function doLogin($username, $token)
        {
        }

        function logout()
        {
            Context::getResponse()->deleteCookie('username');
            Context::getResponse()->deleteCookie('session_token');
            Context::getResponse()->deleteCookie('elevated_session_token');
        }

        /**
         * @param Request $request
         *
         * @return null|User
         */
        function doExplicitLogin(Request $request)
        {
            $username = $request['username'];
            $password = $request['password'];

            $user = Users::getTable()->getByUsername($username);

            if (!$user instanceof User)
            {
                Context::logout();
                return;
            }

            if (!$user->hasPassword($password))
            {
                $user = null;
            }

            return $user;
        }

        /**
         * @param User $user
         * @param UserSession $token
         * @param bool $session_only
         * @return mixed|void
         */
        function persistTokenSession(User $user, UserSession $token, $session_only)
        {
            if ($session_only)
            {
                Context::getResponse()->setSessionCookie('username', $user->getUsername());
                Context::getResponse()->setSessionCookie('session_token', $token->getToken());
            }
            else
            {
                Context::getResponse()->setCookie('username', $user->getUsername());
                Context::getResponse()->setCookie('session_token', $token->getToken());
            }
        }

        function persistPasswordSession(User $user, $password, $session_only)
        {
            if ($session_only)
            {
                Context::getResponse()->setSessionCookie('username', $user->getUsername());
                Context::getResponse()->setSessionCookie('password', $password);
            }
            else
            {
                Context::getResponse()->setCookie('username', $user->getUsername());
                Context::getResponse()->setCookie('password', $password);
            }
        }

    }
