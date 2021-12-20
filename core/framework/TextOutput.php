<?php

    namespace pachno\core\framework;

    /**
     * Class used in the MVC part of the framework
     *
     * @package pachno
     * @subpackage mvc
     */
    class TextOutput extends RenderedOutput
    {

        public function __construct(string $response, $content_type = null)
        {
            Context::getResponse()->setContentType($content_type ?? 'text/plain');
            Context::getResponse()->setDecoration(Response::DECORATE_NONE);

            $this->content = $response;
        }

        /**
         * @return string
         */
        public function getContent(): string
        {
            return $this->content;
        }

        /**
         * @param string $content
         */
        public function setContent(string $content): void
        {
            $this->content = $content;
        }

    }
