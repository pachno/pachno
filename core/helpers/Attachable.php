<?php

    namespace pachno\core\helpers;

    /**
     * Common interface for objects that can have files attached
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Common interface for objects that can have files attached
     *
     * @package pachno
     * @subpackage core
     */
    interface Attachable
    {

        /**
         * Attaches a file
         *
         * @param \pachno\core\entities\File $file
         */
        public function attachFile(\pachno\core\entities\File $file, $file_comment = '', $description = '');
        
        /**
         * Detaches a file
         *
         * @param \pachno\core\entities\File $file
         */
        public function detachFile(\pachno\core\entities\File $file);

    }

