<?php

    namespace pachno\core\framework\interfaces;

    use pachno\core\entities\User;
    use pachno\core\entities\UserSession;
    use pachno\core\framework\Request;

    /**
     * An authentication module
     *
     * @package pachno
     * @subpackage core
     */
    interface AuthenticationProvider
    {

        public const AUTHENTICATION_TYPE_PASSWORD = 'authenticate_password';

        public const AUTHENTICATION_TYPE_TOKEN = 'authenticate_token';

        function getAuthenticationMethod();

        /**
         * @param $username
         * @param $password
         * @param bool $is_elevated
         *
         * @return User|null
         */
        function autoVerifyLogin($username, $password, $is_elevated = false);

        /**
         * @param $username
         * @param $token
         * @param bool $is_elevated
         *
         * @return User|null
         */
        function autoVerifyToken($username, $token, $is_elevated = false);

        /**
         * @param $username
         * @param $password
         *
         * @return User|null
         */
        function doLogin($username, $password);

        function logout();

        /**
         * @param Request $request
         *
         * @return User|null
         */
        function doExplicitLogin(Request $request);

        /**
         * @param User $user
         * @param UserSession $token
         * @param bool $session_only
         *
         * @return mixed
         */
        function persistTokenSession(User $user, UserSession $token, $session_only);

        /**
         * @param User $user
         * @param string $password
         * @param bool $session_only
         *
         * @return mixed
         */
        function persistPasswordSession(User $user, $password, $session_only);

    }
