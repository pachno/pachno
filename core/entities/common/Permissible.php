<?php

    namespace pachno\core\entities\common;

    interface Permissible {

        public function removePermission($permission_name, $target_id = 0, $module = 'core');

    }