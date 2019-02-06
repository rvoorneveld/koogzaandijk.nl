<?php

// Define path to application directory
defined($strKey = 'APPLICATION_PATH') || define($strKey,realpath(dirname(__FILE__).'/../application'));

// Define path to application directory
defined($strKey = 'CACHE_PATH') || define($strKey,realpath(dirname(__FILE__).'/../cache'));

// Define application environment
defined($strKey = 'APPLICATION_ENV') || define($strKey,getenv($strKey) ?: 'production');

// Define root url
defined($strKey = 'ROOT_URL') || define($strKey, 'https://'.$_SERVER['HTTP_HOST']);

defined($strKey = 'SERVER_URL') || define($strKey, realpath($_SERVER['DOCUMENT_ROOT']));

defined($strKey = 'VENDOR_PATH') || define($strKey,APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor');

require_once VENDOR_PATH.DIRECTORY_SEPARATOR.'autoload.php';

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// Create application, bootstrap, and run
(new Zend_Application(APPLICATION_ENV,APPLICATION_PATH.'/configs/application.ini'))
    ->bootstrap()
    ->run();
