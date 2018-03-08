<?php

// Set Ini Settings
ini_set('max_execution_time', 600);        // 10 Minutes execution time
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');

// Define path to application directory
defined($strKey = 'APPLICATION_PATH') || define($strKey, realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined($strKey = 'APPLICATION_ENV') || define($strKey, (true === isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'production'));

defined($strKey = 'VENDOR_PATH') || define($strKey, APPLICATION_PATH . '/../vendor', true);

require_once VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, [
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
]));

// Load Zend Framework and KZ/Hiltex Libraries
Zend_Loader_Autoloader::getInstance()->registerNamespace('KZ_');

// Get Config
$objConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

// Setup Database
$objDatabase = Zend_Db::factory($objConfig->database);
Zend_Db_Table::setDefaultAdapter($objDatabase);

// Set Default Timezone
date_default_timezone_set('Europe/Amsterdam');

// Set Zend Date object
$objDate = new Zend_Date();

// Get Current Hour (single digit)
$intHour = $objDate->get(Zend_Date::HOUR_SHORT);

// Get Current Day of the Month (single digit)
$intDayofMonth = $objDate->get(Zend_Date::DAY_SHORT);

// Get Current Month (single digit)
$intMonth = $objDate->get(Zend_Date::MONTH_SHORT);

// Get Current Day of the Week (single digit)
$intDayofWeek = $objDate->get(Zend_Date::WEEKDAY_DIGIT);

// Set status to active (get Active Cronjobs only)
$intStatus = 1;

$arrDates = [
    'hour' => $intHour,
    'dayofmonth' => $intDayofMonth,
    'month' => $intMonth,
    'dayofweek' => $intDayofWeek,
];

// Load Models
$objModelCronjobs = new KZ_Models_Cronjobs();

// Get Cronjobs
$objCronjobs = $objModelCronjobs->getCronjobs($arrDates, $intStatus);

// Check if Cronjobs where found
if (null !== $objCronjobs && false === empty($objCronjobs->toArray())) {

    // Set Cron Api Settings
    $objCronApi = $objConfig->cron->api;
    $strHttpHost = $objCronApi->url;
    $strApiKey = $objCronApi->key;

    // Set Cronjobs array
    $arrCronjobs = $objCronjobs->toArray();

    // Loop through cronjobs
    foreach ($arrCronjobs as $intCronjobKey => $arrCronjob) {
        if ('xml' === $arrCronjob['type']) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $strHttpHost);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "xml=<request><key>{$strApiKey}</key><service>{$arrCronjob['service']}</service><params>{$arrCronjob['params']}</params></request>");
            curl_exec($ch);
            curl_close($ch);
        }
    }
}
