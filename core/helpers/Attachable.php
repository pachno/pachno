<?php

    namespace pachno\core\helpers;

    use pachno\core\entities\File;

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
         * @param File $file
         */
        public function attachFile(File $file, $file_comment = '', $description = '');

        /**
         * Detaches a file
         *
         * @param File $file
         */
        public function detachFile(File $file);

    }

