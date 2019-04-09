<?php

    namespace pachno\core\helpers;

    /**
     * Content parser common interface
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

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
         * @return array|\pachno\core\entities\User
         */
        public function getMentions();
        
        /**
         * Whether there are mentioned users in this content
         * 
         * @return boolean
         */
        public function hasMentions();
        
    }

