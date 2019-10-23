<?php

    namespace pachno\core\helpers;

    use pachno\core\entities\User;

    /**
     * Common interface for objects providing a list of related users
     *
     * @package pachno
     * @subpackage core
     */
    interface MentionableProvider
    {

        /**
         * Returns an array of users
         *
         * @return array|User
         */
        public function getMentionableUsers();

    }

