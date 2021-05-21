<?php

    namespace pachno\core\entities\common;

    interface Permissible {

        public function removePermission($permission_name, $target_id = 0, $module = 'core');

        public function getPermissions(): array;

        public function hasPermission($permission_name, $target_id = 0, $module = 'core'): bool;

        public function addPermission($permission_name, $module = 'core', $scope = null, $target_id = 0);

    }