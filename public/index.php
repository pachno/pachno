<?php

    // Set the path to Pachno top folder
    $path = realpath(getcwd());
    defined('PACHNO_SESSION_NAME') || define('PACHNO_SESSION_NAME', 'PACHNO');

    // Default behaviour: define the path to pachno as one folder up from this
    defined('PACHNO_PATH') || define('PACHNO_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

    // Default behaviour: define the public folder name to "public" (actually autodetect name of current folder)
    defined('PACHNO_PUBLIC_FOLDER_NAME') || define('PACHNO_PUBLIC_FOLDER_NAME', substr($path, strrpos($path, DIRECTORY_SEPARATOR) + 1));

    // Root installation: https://projects.pach.no/pachno/docs/HowTo:RootDirectoryInstallation
    // ----
    // Don't look one directory up to find the path to Pachno
    // defined('PACHNO_PATH') || define('PACHNO_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
    // Don't autodetect the subfolder, but use "" instead, since there is none
    // defined('PACHNO_PUBLIC_FOLDER_NAME') || define('PACHNO_PUBLIC_FOLDER_NAME', '');
    // ----

    // Include the "engine" script, which initializes and sets up stuff
    if (!file_exists(PACHNO_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        include PACHNO_PATH . 'core' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'composer.error.php';
        die();
    }
    require PACHNO_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    \pachno\core\framework\Context::bootstrap();
    \pachno\core\framework\Context::go();
