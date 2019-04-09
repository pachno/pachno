<?php

    namespace pachno\core\helpers;

    /**
     * Interface for items implementing diffable content
     *
     * @package pachno
     * @subpackage core
     */
    interface Diffable
    {

        /**
         * @return int
         */
        public function getLinesAdded();

        /**
         * @return int
         */
        public function getLinesRemoved();

    }

