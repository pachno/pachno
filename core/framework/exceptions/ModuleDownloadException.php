<?php

    namespace pachno\core\framework\exceptions;

    use Exception;

    /**
     * Exception used when trying to download a module
     *
     * @package pachno
     * @subpackage core
     */
    class ModuleDownloadException extends Exception
    {

        public const JSON_NOT_FOUND = 1;

        public const FILE_NOT_FOUND = 2;

        public const MISSING_LICENSE = 3;

        public const READONLY_TARGET = 4;

    }

