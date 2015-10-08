<?php
class PreviewController extends KZ_Controller_Action
{
	
	public function indexAction()
	{
		// Get config
		$this->objConfig 			= Zend_Registry::get('Zend_Config');
		
		// Get Allowed Preview Locations
		$arrAllowedPreviewLocations	= $this->objConfig->locations->preview->allowed;
		
		// Get All Params
		$arrParams 	= $this->_getAllParams();
		
		if(! isset($_SERVER['HTTP_REFERER']) || ! in_array(str_replace(ROOT_URL, '', $_SERVER['HTTP_REFERER']), $arrAllowedPreviewLocations->toArray())) {
			print 'Error: Unable to see preview. Code 001';
    		exit;
		}
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		print 'Error: Unable to see preview. Code 002';
    		exit;
    	}

    	// Set Models
    	$objModelNews				= new KZ_Models_News();
		
    	// Get News
    	$arrNews					= $objModelNews->getNewsByID($arrParams['id']);
    	
    	// Get News Content
    	$arrNewsContent				= $objModelNews->getNewsContent($arrNews['news_id']);
    	
    	Zend_Debug::dump($arrNews);
    	
    	foreach($arrNewsContent as $intNewsContentKey => $arrContent) {
    		
    		Zend_Debug::dump($arrContent);
    		
    	}
    	
	}
	
}