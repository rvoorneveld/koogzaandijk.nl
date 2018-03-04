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
    // Set Default Http Host
    $strHttpHost = $objConfig->cron->api->url;

    // Set Cronjobs array
    $arrCronjobs = $objCronjobs->toArray();

    // Set Villatrips API Key
    $strApiKey = $objConfig->cron->api->key;

    // Loop through cronjobs
    foreach ($arrCronjobs as $intCronjobKey => $arrCronjob) {
        switch ($arrCronjob['type']) {
            case 'xml':
            default:
                // Set Post Data
                $strXml = 'xml=<request><key>' . $strApiKey . '</key><service>' . $arrCronjob['service'] . '</service><params>' . $arrCronjob['params'] . '</params></request>';

                // Ininitalize Curl
                $ch = curl_init();

                // Set Curl Url
                curl_setopt($ch, CURLOPT_URL, $strHttpHost);

                // Set Curl Count Post Fields
                curl_setopt($ch, CURLOPT_POST, 1);

                // Set Curl Post Fields
                curl_setopt($ch, CURLOPT_POSTFIELDS, $strXml);

                // Execute the Curl
                curl_exec($ch);

                // Close the Curl
                curl_close($ch);

                break;
        }

    }

}
?>
