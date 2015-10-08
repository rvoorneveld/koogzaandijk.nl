<?php

ini_set('display_errors',1);
ini_set('error_reporting','E_ALL|E_STRICT');

class KZ_Api_Services {

    public $db;
    public $userID;
    public $params;
    public $apiKey;

    public function __construct()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    public function createError($code,$message,$notice=false)
    {
        $data = array('response' => array('type'    => 'error',
                                          'params'  => array('code'    => $code,
                                                             'message' => $message)));
        if($notice){
           $data['response']['params']['notice'] = $notice;
        }
        return $data;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getServiceVersion($service)
    {
        $serviceClass = 'KZ_Api_Services_'.$service;
        $serviceVersion = new $serviceClass;
        return $serviceVersion->version;
    }

    protected function _loadXml($booLoadCached, $arrConfig) {
    	
    	$strLoadedXml	= false;
    	
    	if($booLoadCached === true) {
   		// Load XML from Cached file
    		// Set Service XML Location
	    	$strServiceXmlLocation	= $_SERVER['DOCUMENT_ROOT'].'/cronjobs/xml/'.strtolower(str_replace('Get', '', $this->service)).'.xml';

	    	// Check if file exists
	    	if(file_exists($strServiceXmlLocation)) {
	    		$strLoadedXml = file_get_contents($strServiceXmlLocation);
	    	}
    	}
    	
    	// Load From Onsweb plugin when not loading from cache or something has gone wrong with loaded cache file
    	if($booLoadCached === false || $strLoadedXml === false) {
    		 
    		// Set Location Url
    		$strLocation	= $this->location.'/'.$this->code.'/'.$arrConfig['file'].'/';
    		 
    		// Set Params as string
    		$strParams		= '';
    		foreach($arrConfig as $strKey => $strValue) {
    			$strParams .= $strKey.'='.$strValue.'&';
    		}
    		 
    		// Remove last & character
    		rtrim($strParams, '&');
    		 
    		// Set the Connection
    		$ch     = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $strLocation);
    		curl_setopt($ch, CURLOPT_POST, count($arrConfig));
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $strParams);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	
    		// Get the Result
    		$strLoadedXml      = curl_exec($ch);
    		 
    	}
    	
    	return $strLoadedXml;
    	
    }
    
}

?>