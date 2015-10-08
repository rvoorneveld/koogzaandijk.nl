<?php

/**
 * This class log's every request to the XML API.
 *
 * @package     Ippies XML API
 * @author      Rick Voorneveld <rick@mediaconcepts.nl>
 * @copyright   Copyright (c) 2012 Media concepts. (http://www.mediaconcepts.nl)
 * @since       2012-01-24
 * @version     1.1
 * 
 */
class KZ_Api_Log
{
    public function log($parameters,$xmlResponse)
    {
        // Set database object
        $db = Zend_Db_Table::getDefaultAdapter();

        // All parameters to serialized string
        $parametersString 	= serialize($parameters);

        // Load the xml and create a serialized string
        $xmlResponseString 	= serialize($xmlResponse);
        
        // Set Log Response Type
        $strLogType 		= ((isset($xmlResponse) && is_array($xmlResponse) && ! empty($xmlResponse['response']['type'])) ? $xmlResponse['response']['type'] : 'error');
        
        // Insert into database
        $db->insert('api_log',array('ip'         => $_SERVER['REMOTE_ADDR'],
        							'type'		 => $strLogType,
                                    'parameters' => $parametersString,
                                    'response'   => $xmlResponseString));
    }
}