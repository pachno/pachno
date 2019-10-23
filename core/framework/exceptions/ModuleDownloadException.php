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

        const JSON_NOT_FOUND = 1;

        const FILE_NOT_FOUND = 2;

        const MISSING_LICENSE = 3;

        const READONLY_TARGET = 4;

    }

