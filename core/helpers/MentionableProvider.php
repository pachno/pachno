<?php

    namespace pachno\core\helpers;

    /**
     * Common interface for objects providing a list of related users
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

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
         * @return array|\pachno\core\entities\User
         */
        public function getMentionableUsers();
        
    }

