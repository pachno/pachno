<?php

    namespace pachno\core\framework;

    /**
     * Class used in the MVC part of the framework
     *
     * @package pachno
     * @subpackage mvc
     */
    abstract class RenderedOutput
    {

        protected $content;

        public function __toString(): string
        {
            return $this->content;
        }

    }
