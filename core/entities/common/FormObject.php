<?php

    namespace pachno\core\entities\common;

    use pachno\core\framework\Request;

    interface FormObject {

        public function updateFromRequest(Request $request);

        public function saveFromRequest(Request $request);

    }