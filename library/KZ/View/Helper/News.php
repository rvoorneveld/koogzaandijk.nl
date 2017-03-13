<?php
class KZ_View_Helper_News extends Zend_View_Helper_Abstract
{
	
	public function news($booIsMobile = false)
	{
		$objConfig			= Zend_Registry::get('Zend_Config');
		
		$objRequest			= Zend_Controller_Front::getInstance()->getRequest();
		
		$strControllerName 	= $objRequest->getParam('controller');
		$strActionName 		= $objRequest->getParam('action');
		$strTitle			= $objRequest->getParam('title');
		
		if($strControllerName == 'page' && $strActionName == 'index') {
		
			// Set Models
			$objModelNews		= new KZ_Models_News();
			$objModelAgenda		= new KZ_Models_Agenda();
			$objModelCategories	= new KZ_Models_Categories();
			$objModelSettings	= new KZ_Models_Settings();
			$objModelPage		= new KZ_Models_Pages();

			// Get Title Slug
			$strTitleSlug		= $objRequest->getParam('title');
			
			// Set Defaults
			$intCategoryID		= false;
			$arrCategory		= false;
			
			if($strTitleSlug != 'home') {
				
				// Get Category By Category Name
				$arrCategory	= $objModelCategories->getCategoryBySlug($strTitleSlug);
				$intCategoryID	= ((isset($arrCategory) && is_array($arrCategory) && count($arrCategory) > 0) ? $arrCategory['category_id'] : false);

			}
			
			// Set Content Type ID - Carousel (1) and Top (2)
			$intContentTypeID 	= array(1,2);
			
			// Set Status - active
			$intStatus			= 1;
			
			// Set Limit
			$intLimit			= (($booIsMobile === true) ? $objConfig->news->maxRelatedMobile : $objConfig->news->maxRelated);
			
			// Get News items
			$arrNewsItems		= $objModelNews->getNews($intContentTypeID, $intCategoryID, $intStatus, false, $intLimit);
			
			// Set Current Date
			$objDate			= new Zend_Date();
			$strDate			= $objDate->toString('yyyy-MM-dd');
			
			// Get Agenda items
			$arrAgendaItems		= $objModelAgenda->getAgenda($strDate, $intStatus, 8);
			
			// Get News Categories
			$arrCategories		= $objModelCategories->getCategories('category_id');
			
			// Get Topstory
			$arrTopstory		= $objModelSettings->getSettingsByKey('topstory');
			
			// Check if Topstory was found
			if(isset($arrTopstory) && is_array($arrTopstory) && count($arrTopstory) > 0) {
				
				$arrTopstory	= $objModelPage->getPageContent($arrTopstory['value']);
				
			}
			
			// Get News Categories
			$intStatus				= 1;
			$arrNewsCategories 		= $objModelNews->getNewsCategories($intStatus);
			
			return $this->view->partial('snippets/news.phtml', array(
				'news'				=> $arrNewsItems,
				'agenda'			=> $arrAgendaItems,
				'category'			=> $arrCategory,
				'categories'		=> $arrCategories,
				/*'topstory'		=> $arrTopstory,*/
				'news_categories'	=> array_slice($arrNewsCategories,0,5),
				'title'				=> $strTitle
			));

		}
		
	}
	
}