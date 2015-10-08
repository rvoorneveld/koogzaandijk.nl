<?php

class ErrorController extends KZ_Controller_Action
{
	
	public function errorAction()
    {

    	// Get errors
    	$errors 		= $this->_getParam('error_handler');
    	
    	// Get Module
    	$strModuleName	= $errors->request->module;

	    if(APPLICATION_ENV != 'production' || $strModuleName == 'admin') {
	    	
	    		$this->_helper->layout->disableLayout();
	    		
		        switch ($errors->type) {
		            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
		            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
		            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
		        
		                // 404 error -- controller or action not found
		                $this->getResponse()->setHttpResponseCode(404);
		                $this->view->message = 'Page not found';
		                break;
		            default:
		                // application error
		                $this->getResponse()->setHttpResponseCode(500);
		                $this->view->message = 'Application error';
		                break;
		        }
		        
		        // Log exception, if logger available
		        if ($log = $this->getLog()) {
		        	$log->crit($this->view->message.', '.$errors->exception);
		        }
		
		        
		        // conditionally display exceptions
		        if ($this->getInvokeArg('displayExceptions') == true) {
		            $this->view->exception = $errors->exception;
		        }
		        
		        $this->view->request   = $errors->request;
	    	
    	} else {
    		
    		// Set View Render Script
    		$this->_helper->viewRenderer->setRender('errorstyled');
    		
    		// Get Config
			$objConfig				= Zend_Registry::get('Zend_Config');
    		
    		// Set Max Related items
			$intMaxItems			= (int)$objConfig->news->maxRelated * 2;
			
			// Set Models
			$objModelPages			= new KZ_Models_Pages();
			$objModelNews			= new KZ_Models_News();
			
			// Get Page by slug
			$arrPage				= $objModelPages->getPageBySlug('error');
			
			// Get Latest News
			$arrLatestNews			= $objModelNews->getLatestNews($intMaxItems);
			
			// Set Status - active
			$intStatus				= 1;
			
			$arrPageContent			= $objModelPages->getPageContent($arrPage['page_id'], $intStatus);
			
			$this->view->page				= $arrPage;
			$this->view->content 			= $arrPageContent;
			$this->view->latest				= $arrLatestNews;
    		
    	}
		
		
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
       
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }

        $log = $bootstrap->getPluginResource('Log')->getLog();
        		
        return $log;
    }

}