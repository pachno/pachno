<?php

    namespace pachno\core\helpers;

    use pachno\core\entities\User;

    /**
     * Common content parser interface to be implemented by all custom content parsers
     *
     * @package pachno
     * @subpackage core
     */
    interface ContentParser
    {

        /**
         * Returns an array of mentioned users
         *
         * @return array|User
         */
        public function getMentions();

        /**
         * Whether there are mentioned users in this content
         *
         * @return boolean
         */
        public function hasMentions();

    }

