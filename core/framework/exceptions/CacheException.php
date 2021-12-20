<?php

    namespace pachno\core\framework\exceptions;

    use Exception;

    /**
     * Exception used in an action
     *
     * @package pachno
     * @subpackage mvc
     */
    class CacheException extends Exception
    {

        public const NO_FOLDER = 1;

        public const NOT_WRITABLE = 2;

    }

