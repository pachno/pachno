<?php

    namespace pachno\core\framework\exceptions;

    use Exception;

    /**
     * Exception used in an action
     *
     * @package pachno
     * @subpackage mvc
     */
    class ConfigurationException extends Exception
    {

        const NO_VERSION_INFO = 1;

        const UPGRADE_REQUIRED = 2;

        const UPGRADE_FILE_MISSING = 3;

        const NO_B2DB_CONFIGURATION = 4;

        const PERMISSION_DENIED = 5;

    }

