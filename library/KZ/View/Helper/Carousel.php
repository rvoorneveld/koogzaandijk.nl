<?php
class KZ_View_Helper_Carousel extends Zend_View_Helper_Abstract
{
	
	public function carousel()
	{
		$objRequest				= Zend_Controller_Front::getInstance()->getRequest();
		
		$strControllerName 		= $objRequest->getParam('controller');
		$strActionName 			= $objRequest->getParam('action');
		$strTitle	 			= $objRequest->getParam('title');

		if($strControllerName == 'page' && $strActionName == 'index' && $strTitle == 'home') {
			
			// Set Models
			$objModelNews		= new KZ_Models_News();
	
			// Set Content Type ID - Carousel
			$intContentTypeID 	= 1;
			
			// Set Status - active
			$intStatus			= 1;
			
			// Get Carousel items
			$arrCarouselItems	= $objModelNews->getNews($intContentTypeID, false, $intStatus);
			
			// Check if Carousel items where found
			if(isset($arrCarouselItems) && is_array($arrCarouselItems) && count($arrCarouselItems) > 0) {
				
				// Set Default Carousel Content array
				$arrCarouselContentItems	= array();
				
				foreach($arrCarouselItems as $intCarouselItemKey => $arrCarouselItem) {
					
					// Get Carousel Content
					$arrCarouselContent		= $objModelNews->getNewsContent($arrCarouselItem['news_id'], $intStatus);
					
					$arrCarouselContentItems[$intCarouselItemKey]				= $arrCarouselItem;
					$arrCarouselContentItems[$intCarouselItemKey]['content']	= $arrCarouselContent;
				}
				
				// Return Carousel HTML
				return $this->view->partial('snippets/carousel.phtml', array(
					'items'	=> $arrCarouselContentItems
				));
				
			}
			
		}
		
		return '';
		
	}
	
}