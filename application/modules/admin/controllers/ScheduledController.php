<?php
class Admin_ScheduledController extends KZ_Controller_Action
{
	
	public function indexAction()
	{
		$this->_redirect('/admin/scheduled/services/');
		exit;	
	}
	
    public function servicesAction()
    {
    
	    // Set Models
    	$objModelServices		= new KZ_Models_Services();
    	
    	// Get Services
    	$objServices			= $objModelServices->getServices();
    	
    	// Check if Services where found
    	if(! is_null($objServices)) {
    		// Parse Channels to View
    		$this->view->services 	= $objServices->toArray();
    	}
    	
    }
    
	public function logAction()
	{
	
		// Set Models
		$objModelApi			= new KZ_Models_Api();
		
		// Get Log
		$objLog					= $objModelApi->getLog();
		
		// Check if Log Data was found
		if(! is_null($objLog))
		{
			
			// Parse Log Data to view
			$this->view->log	= $objLog->toArray();
			
		}
		
	}
	
	public function logitemAction()
	{
		
		// Get Params
    	$arrParams		= $this->_getAllParams();

    	// Check for log id
    	if(! isset($arrParams['id']) || empty($arrParams['id']) || ! is_numeric($arrParams['id'])) {
    		
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/scheduled/log/feedback/'.$strFeedback.'/');
    		
    	}

    	// Set Models
    	$objModelApi	= new KZ_Models_Api();

    	// Get Log item
    	$objLog			= $objModelApi->getLog($arrParams['id']);
    	
   		// Check if log item was found
    	if(is_null($objLog)) {
    		
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find log item')));
    		$this->_redirect('/admin/scheduled/log/feedback/'.$strFeedback.'/');
    		
    	} else {
    		
    		// Parse Log Item to view
    		$this->view->id 		= $objLog->api_log_id;
    		$this->view->time 		= $objLog->time;
    		$this->view->ip			= $objLog->ip;
    		$this->view->type		= $objLog->type;
    		$this->view->parameters	= unserialize($objLog->parameters);
    		$this->view->response	= unserialize($objLog->response); 
    		
    	}
		
	}
	
	public function manuallyAction()
	{
		// Set Config Object
		$objConfig				= Zend_Registry::get('Zend_Config');
		
		// Set Models
		$objModelServices		= new KZ_Models_Services();
		
		// Set Defaults
		$strApiKey				= $objConfig->cron->api->key;
	    $strHttpHost 			= $objConfig->cron->api->url;
	    $strService				= '';
	    $strParams				= '';
	    $intStatus				= 1; // Active Services
	    
	    // Get All Services
	    $objServices			= $objModelServices->getServices($intStatus);

	    // Check for Post
	    if($this->getRequest()->isPost()) {
	    	
	    	// Get Post Params
	    	$arrPostParams		= $this->_getAllParams();
	    	
	    	// Set Service
	    	$strService			= $arrPostParams['service'];
	    	
	    	// Check Params
	    	if(isset($arrPostParams['params']) && is_array($arrPostParams['params'])) {
	    		// Loop through Params
	    		foreach($arrPostParams['params'] as $strParam) {
	    			$arrParam	= explode('_', $strParam);
	    			if(! empty($arrParam) && is_array($arrParam) && count($arrParam) == 2) {
	    				// Add Param to XML
	    				$strParams	.= '<'.$arrParam[0].'>'.$arrParam[1].'</'.$arrParam[0].'>';
	    			}
	    		}
	    	}
			
	    	// Set XML
	    	$strXml					= '<request><key>'.$strApiKey.'</key><service>'.$strService.'</service><params>'.$strParams.'</params></request>';
	    	
	    	// Set the Connection
	    	$ch     = curl_init();
	    	curl_setopt($ch, CURLOPT_URL, $strHttpHost);
	    	curl_setopt($ch, CURLOPT_POST, 1);
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, 'xml='.$strXml);
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    	 
	    	// Get the Result
	    	$strLoadedXml      = curl_exec($ch);
	    	
	    	$this->_helper->layout()->disableLayout();
	    	$this->_helper->viewRenderer->setNoRender(true);
	    	
	    	header ("Content-Type:text/xml");
	    	print $strLoadedXml;
	    	
	    }
	    
	    // Parse Variables to View
	    $this->view->httphost	= $strHttpHost;
	    $this->view->services	= $objServices->toArray();

	}
	
}