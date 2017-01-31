<?php

// Define path to application directory
defined($strKey = 'APPLICATION_PATH') || define($strKey,realpath(dirname(__FILE__).'/../application'));

// Define path to application directory
defined($strKey = 'CACHE_PATH') || define($strKey,realpath(dirname(__FILE__).'/../cache'));

// Define application environment
defined($strKey = 'APPLICATION_ENV') || define($strKey,getenv($strKey) ?: 'production');

// Define root url
defined($strKey = 'ROOT_URL') || define($strKey,'http://'.$_SERVER['HTTP_HOST']);

defined($strKey = 'SERVER_URL') || define($strKey, realpath($_SERVER['DOCUMENT_ROOT']));
    
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();
