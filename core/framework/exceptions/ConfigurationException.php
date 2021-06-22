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

        public const NO_VERSION_INFO = 1;

        public const UPGRADE_REQUIRED = 2;

        public const UPGRADE_FILE_MISSING = 3;

        public const NO_B2DB_CONFIGURATION = 4;

        public const PERMISSION_DENIED = 5;

        public const UPGRADE_NON_RESET_COMPOSER_JSON = 6;

    }

