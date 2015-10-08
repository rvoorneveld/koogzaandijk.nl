<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define path to application directory
defined('CACHE_PATH')
|| define('CACHE_PATH', realpath(dirname(__FILE__) . '/../cache'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define root url
defined('ROOT_URL')
    || define('ROOT_URL', 'http://' . $_SERVER['HTTP_HOST']);

defined('SERVER_URL')
	|| define('SERVER_URL', realpath($_SERVER['DOCUMENT_ROOT']));
    
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