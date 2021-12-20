<?php

    namespace pachno\core\framework\exceptions;

    use Exception;

    /**
     * Exception used in routing setup
     *
     * @package pachno
     * @subpackage mvc
     */
    class RoutingException extends Exception
    {

        public const MISSING_OVERRIDE = 1;

    }

