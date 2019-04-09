<?php

    define('DS', DIRECTORY_SEPARATOR);
    define('PACHNO_PATH', realpath(dirname(__FILE__) . DS . '..' . DS) . DS);
    define('PACHNO_CORE_PATH', PACHNO_PATH . 'core' . DS);
    define('PACHNO_VENDOR_PATH', PACHNO_PATH . 'vendor' . DS);

    gc_enable();
    date_default_timezone_set('UTC');

    define('PACHNO_CACHE_PATH', PACHNO_PATH . 'cache' . DS);
    define('PACHNO_CONFIGURATION_PATH', PACHNO_CORE_PATH . 'config' . DS);
    define('PACHNO_INTERNAL_MODULES_PATH', PACHNO_CORE_PATH . 'modules' . DS);
    define('PACHNO_MODULES_PATH', PACHNO_PATH . 'modules' . DS);

    require_once PACHNO_PATH . 'tests' . DS . 'b2dbmock.php';
