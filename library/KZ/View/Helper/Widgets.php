<?php

class KZ_View_Helper_Widgets extends Zend_View_Helper_Abstract
{
	
	public function widgets()
	{
		// Get Request object
		$objRequest			= Zend_Controller_Front::getInstance()->getRequest();
		
		// Set Models
		$objModelWidgets	= new KZ_Models_Widgets();
		$objModelPages		= new KZ_Models_Pages();
		$objModelNews		= new KZ_Models_News();
		$objModelAgenda		= new KZ_Models_Agenda();
		
		// Get All Params
		$arrParams			= $objRequest->getParams();
		
		// Set action
		$strController		= $arrParams['controller'];
		
		// Set action
		$strAction			= $arrParams['action'];

		if($strController == 'page') {
			
			// Set Allowed Widget Actions array
			$arrAllowedWidgetActions	= array('page','index','news','agenda','social');
		
			if(in_array($strAction, $arrAllowedWidgetActions) && isset($arrParams['title']) && $arrParams['title'] != '') {
			
				// Set Default Widget Layout
				$intWidgetLayoutID 	= 1;
				
				// Set Title Slug
				$strSlug			= (($arrParams['title'] == 'home') ? '' : $arrParams['title']);
				
				switch($strAction) {
					case 'index':
					case 'page':
					case 'social':
						$arrPage	= $objModelPages->getPageBySlug($strSlug);
						if(isset($arrPage)&& is_array($arrPage) && isset($arrPage['widget_layout']) && is_numeric($arrPage['widget_layout'])) {
							$intWidgetLayoutID	= $arrPage['widget_layout'];
						}
						break;
					case 'news':
						$arrNews	= $objModelNews->getNewsBySlug($strSlug);
						if(isset($arrNews)&& is_array($arrNews) && isset($arrNews['widget_layout']) && is_numeric($arrNews['widget_layout'])) {
							$intWidgetLayoutID	= $arrNews['widget_layout'];
						}
						break;
					case 'agenda':
						$arrAgenda	= $objModelAgenda->getAgendaBySlug($strSlug);
						if(isset($arrAgenda)&& is_array($arrAgenda) && isset($arrAgenda['widget_layout']) && is_numeric($arrAgenda['widget_layout'])) {
							$intWidgetLayoutID	= $arrAgenda['widget_layout'];
						}
						break;
				}
				
				if(isset($intWidgetLayoutID) && is_numeric($intWidgetLayoutID)) {
				
					// Get Widget layout
					$arrWidgetLayout	= $objModelWidgets->getWidgetLayoutByID($intWidgetLayoutID);
					
					// Set Defaults
					$strWidgetContent	= '';
					$strContentBottom 	= '';
					//$arrOpenInfoboxes	= array(1,4,7,10,13,16,19,22);
					$arrOpenInfoboxes	= array(1,3,5,7,9,11,13,15,17,19,21,23);
					$intStatus			= 1;
							
					// Check if Widget layout was found
					if(isset($arrWidgetLayout) && is_array($arrWidgetLayout) && count($arrWidgetLayout) > 0) {
						
						// Set structure widgets array
						$arrStructure		= explode(',', $arrWidgetLayout['structure']);
						
						// Set Total Widgets
						$intTotalWidgets	= count($arrStructure);
						
						$intCount 			= 1;

						// Loop through structure
						foreach($arrStructure as $intWidgetID) {
							
							// Get Widget
							$arrWidget			= $objModelWidgets->getWidgetByID($intWidgetID);
							
							// Check if Widget was found
							if(isset($arrWidget) && is_array($arrWidget) && count($arrWidget) > 0) {
								
								// Get Widget Content
								$arrWidgetContent	= $objModelWidgets->getWidgetContent($intWidgetID, $intStatus);
								
								if(isset($arrWidgetContent) && is_array($arrWidgetContent) && count($arrWidgetContent) > 0) {
									
										// Set Widget Row top
										if(in_array($intCount, $arrOpenInfoboxes)) {
											echo '<div class="table infobox">';
										}
										
										echo $this->view->partial('/widgets/widget.phtml', array(
											'widget' 			=> $arrWidget,
											'widget_content' 	=> $arrWidgetContent,
											'count'				=> $intCount,
											'total'				=> $intTotalWidgets,
											'openInfoBoxes'		=> $arrOpenInfoboxes
										));
										
										$strContentBottom .= $this->view->partial('/widgets/widget_bottom.phtml', array(
											'widget' 			=> $arrWidget,
											'widget_content' 	=> $arrWidgetContent,
											'count'				=> $intCount,
											'total'				=> $intTotalWidgets,
											'openInfoBoxes'		=> $arrOpenInfoboxes
										));
										
										// Set Widget Row bottom
										if(($intCount % 2) == 0 || $intCount == $intTotalWidgets) {
											
											echo $strContentBottom.'</div>';
											
											// set Defaults
											$strContentBottom	= '';
			
										}
										
										
										$intCount++;
									
								} else {
									$intTotalWidgets--;
								}
								
							} else {
								$intTotalWidgets--;
							}
						
						}
						
					}
					
				}

			}
		
		}
		
	}
	
}