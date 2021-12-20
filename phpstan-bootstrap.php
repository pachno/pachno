<?php

    defined('DS') || define('DS', DIRECTORY_SEPARATOR);
    defined('PACHNO_PATH') || define('PACHNO_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
    defined('PACHNO_CORE_PATH') || define('PACHNO_CORE_PATH', PACHNO_PATH . 'core' . DS);
    defined('PACHNO_CONFIG_PATH') || define('PACHNO_CONFIG_PATH', PACHNO_PATH);
    defined('PACHNO_VENDOR_PATH') || define('PACHNO_VENDOR_PATH', PACHNO_PATH . 'vendor' . DS);
    defined('PACHNO_CACHE_PATH') || define('PACHNO_CACHE_PATH', PACHNO_PATH . 'cache' . DS);
    defined('PACHNO_CONFIGURATION_PATH') || define('PACHNO_CONFIGURATION_PATH', PACHNO_CORE_PATH . 'config' . DS);
    defined('PACHNO_INTERNAL_MODULES_PATH') || define('PACHNO_INTERNAL_MODULES_PATH', PACHNO_CORE_PATH . 'modules' . DS);
    defined('PACHNO_MODULES_PATH') || define('PACHNO_MODULES_PATH', PACHNO_PATH . 'modules' . DS);
    defined('PACHNO_PUBLIC_FOLDER_NAME') || define('PACHNO_PUBLIC_FOLDER_NAME', '');
    defined('NOW') || define('NOW', time());
