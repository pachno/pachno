<?php

    namespace pachno\core\framework;

    /**
     * Class used in the MVC part of the framework
     *
     * @package pachno
     * @subpackage mvc
     */
    class JsonOutput
    {

        protected $content;

        public function __construct($response = [])
        {
            Context::getResponse()->setContentType('application/json');
            Context::getResponse()->setDecoration(Response::DECORATE_NONE);

            if (is_array($response) && array_key_exists('error', $response)) {
                Context::getResponse()->setHttpStatus(Response::HTTP_STATUS_BAD_REQUEST);
            }

            if (is_array($response)) {
                array_walk_recursive($response, static function (&$item) {
                    if (is_object($item)) {
                        Logging::log('Unexpected object in json response', 'main', Logging::LEVEL_FATAL);
                        exit();
                    }
                    $item = iconv('UTF-8', 'UTF-8//IGNORE', $item);
                });
            } else {
                $response = iconv('UTF-8', 'UTF-8//IGNORE', $response);
            }

            $this->content = $response;
        }

        /**
         * @return mixed
         */
        public function getContent()
        {
            return $this->content;
        }

        /**
         * @param mixed $content
         */
        public function setContent($content): void
        {
            $this->content = $content;
        }

        public function __toString(): string
        {
            return json_encode($this->content, JSON_THROW_ON_ERROR);
        }

    }
