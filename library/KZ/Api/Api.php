<?php

/**
 * This is the default XML handler. It's main task is to parse the incoming xml and send it to a service.
 * Before the parsed xml is send to the service number of checks will be done:
 *
 * 1 - Is the parameter xml filled.
 * 2 - Is the xml valid (can it be parsed with simplexml_load_string()).
 * 3 - Is there an API key.
 * 4 - Is the API key valid.
 * 5 - Is there a service to load.
 * 6 - Does the service exist.
 * 7 - Does the sended API key have access to the requested service.
 * 8 - Does the class exists in the services structure of the requested service.
 *
  *
 * @author      Rick Voorneveld <rick@mediaconcepts.nl>
 * @copyright   Copyright (c) 2012 Media concepts. (http://www.mediaconcepts.nl)
 * @since       2012-01-24
 * @version     1.0
 */
class KZ_Api_Api
{
    private $db;
    private $requestParameters;
    private $apiID;
    private $apiKey;
    private $apiServiceID;
    private $apiServiceName;
    private $apiServiceClass;
    private $apiParams;

    /**
     * Contruct this class
     */
    public function __construct()
    {
         $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    /**
     * Parse xml string that has been send by the user,
     * when the xml is parsed correctly the requested service will be started.
     *
     * @param <string> $xml
     * @return <string>
     */
    public function parse($requestParameters)
    {
        // Set reuqestParameters
        $this->requestParameters = $requestParameters;

        // Load the xml
        $xmlArray = @simplexml_load_string(stripslashes($this->requestParameters['xml']));

        // Checks
        if($xmlArray){

            // Check is the is a API key
            $this->apiKey = $xmlArray->key;
            if($this->apiKey){

                // Check if API key is valid
                $this->apiID = $this->checkKey($xmlArray->key);
                if($this->apiID){

                    // Check there is a service set
                    if($xmlArray->service){

                        // Check if service exists
                        $this->apiServiceName = $xmlArray->service;
                        $this->apiServiceID   = $this->checkService($this->apiServiceName);
                        if($this->apiServiceID){

                            // Check if API key has access to this service
                            if($this->checkServiceAccess()){

                                // Key has access, now check if there are any params
                                if(!empty($xmlArray->params)){
                                    $this->apiParams = $xmlArray->params;
                                }else{
                                    $this->apiParams = false;
                                }

                                // Ok run the service
                                return $this->runService();

                            }else{
                                return $this->createError('107','no access to service');
                            }
                        }else{
                            return $this->createError('106','service does not exist');
                        }
                    }else{
                        return $this->createError('105','no service'); 
                    }
                }else{
                    return $this->createError('104','invalid API key'); 
                }
            }else{
                return $this->createError('103','no API key'); 
            }
        }else{
            return $this->createError('102','invalid xml');
        }
    }

    /**
     * Check if the apiKey is correct
     *
     * @param <string> $key
     * @return <mixed>
     */
    public function checkKey($key)
    {
        $query = $this->db->select()
                          ->from('api',array('api_id'))
                          ->where('`key` = ?',$key);
        $data  = $this->db->fetchRow($query);
        if(empty($data)){
            return false;
        }else{
            return $data['api_id'];
        }
    }

    /**
     * Check is requested service exists
     *
     * @return <mixed>
     */
    public function checkService()
    {
        $query = $this->db->select()
                          ->from('api_service',array('api_service_id'))
                          ->where('`name` = ?',$this->apiServiceName);
        $data  = $this->db->fetchRow($query);
        if(!empty($data)){
            return $data['api_service_id'];
        }else{
            return false;
        }        
    }

    /**
     * Check if API key has access to this service
     *
     * @return <boolean>
     */
    public function checkServiceAccess()
    {
        $query = $this->db->select()
                          ->from('api_service_access',array('api_service_access_id'))
                          ->joinInner('api_service','api_service_access.api_service_id = api_service.api_service_id')
                          ->where('`api_service_access`.`api_id` = ?',$this->apiID)
                          ->where('`api_service_access`.`api_service_id` = ?',$this->apiServiceID);
        $data  = $this->db->fetchRow($query);

        if(!empty($data)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Create custom error and create a xml string to show API user
     *
     * @param <int> $code
     * @param <string> $message
     * @param <mixed> $notice
     * @return <string>
     */
    public function createError($code,$message,$notice=false)
    {
        $data = array('response' => array('type'    => 'error',
                                          'params'  => array('code'    => $code,
                                                             'message' => $message)));
        // Check if notice isset
        if($notice){
           $data['response']['params']['notice'] = $notice;
        }

        return $this->createXml($data);
    }

    /**
     * Create xml string from an array
     *
     * @param <array> $array
     * @return <string>
     */
    public function createXml($array)
    {
    	$log = new KZ_Api_Log();
        $log->log($this->requestParameters,$array);

       	$dom = new KZ_Api_Extend_XmlDomConstruct('1.0','utf-8');
       	$dom->fromMixed($array);
       	$data = $dom->saveXML();
       	return $data;

    }

    /**
     * Run the selected service
     *
     * @return <string>
     */
    private function runService()
    {
        if($this->apiServiceName){
            
            // Create the service object and set apiKey
            $className = 'KZ_Api_Services_'.$this->apiServiceName;
            $service = new $className;
            $service->setApiKey($this->apiKey);

            // Check if there are any params
            if($this->apiParams){
                $service->setParams($this->apiParams);
            }
            
            // Run it
            return $this->createXml($service->run());
            
        }else{
            return $this->createError('108','service class not found','contact API owner');
        }

    }

}