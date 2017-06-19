<?php

class KZ_View_Helper_Breadcrumb extends Zend_View
{
	
	public function breadcrumb()
	{
		// Get Request object
		$objRequest	= Zend_Controller_Front::getInstance()->getRequest();
		
		// Get Breadcrumb
		return $this->getBreadcrumb($objRequest);
		
	}
	
	public function getBreadcrumb($objRequest)
	{
		// Get Params
		$strModule 		= $objRequest->getParam('module');
		$strController 	= $objRequest->getParam('controller'); 
		$strAction		= $objRequest->getParam('action');
		$strLanguage	= $objRequest->getParam('lang');
		
		if($strModule == 'admin') {
			$strNavigation = $this->adminBreadcrumb($objRequest, $strController, $strAction, $strLanguage);
		} else {
			$strNavigation = $this->defaultBreadcrumb($objRequest, $strController, $strAction, $strLanguage);
		}
		return $strNavigation;
		
	}
	
	public function defaultBreadcrumb($objRequest, $strController, $strAction, $strLanguage)
	{
		// Set All Params
		$arrParams				= $objRequest->getParams();
		
		if($strController == 'page' && $strAction == 'index') {
			return '';
		} else {
		
			// Default breadcrumb
			$strBreadcrumbContent 	= 'U bevindt zich hier: <a href="'.ROOT_URL.'" title="Home">Home</a>';
			
			if(in_array($strAction, array('page'))) {
				
				if(isset($arrParams['title']) && ! empty($arrParams['title'])) {

					// Set Models
					$objModelPages	= new KZ_Models_Pages();
					
					$intMainPageID = null;
					if(isset($arrParams['mainpage']) && ! empty($arrParams['mainpage'])) {
						$arrMainPage	= $objModelPages->getPageBySlug($arrParams['mainpage']);
						if(isset($arrMainPage) && is_array($arrMainPage) && count($arrMainPage) > 0) {
							$intMainPageID	= $arrMainPage['page_id'];
						}
					}

					// Get Page by Slug
					$arrPage		= $objModelPages->getPageBySlug($arrParams['title'], $intMainPageID);
					
					if(isset($arrPage) && is_array($arrPage) && count($arrPage) > 0)
					{
						
						if(isset($arrPage['parent_id']) && ! empty($arrPage['parent_id']) && is_numeric($arrPage['parent_id']) && $arrPage['parent_id'] > 0) {
							
							// Get Page by Slug
							$arrParentPage		= $objModelPages->getPageByID($arrPage['parent_id']);
							
							if(isset($arrParentPage) && is_array($arrParentPage) && count($arrParentPage) > 0) {
								
								$strBreadcrumbContent	.= '<a href="/'.$arrParentPage['menu_url'].'/" title="'.$arrParentPage['menu_name'].'">'.$arrParentPage['menu_name'].'</a>';
								$strBreadcrumbContent	.= '<a href="/'.$arrParentPage['menu_url'].'/'.$arrPage['menu_url'].'/" title="'.$arrPage['menu_name'].'">'.$arrPage['menu_name'].'</a>';
								
							} else {
								
								$strBreadcrumbContent	.= '<a href="/'.$arrParentPage['menu_url'].'/'.$arrPage['menu_url'].'/" title="'.$arrPage['menu_name'].'">'.$arrPage['menu_name'].'</a>';
								
							}
							
						} else {
								
							$strBreadcrumbContent	.= '<a href="/'.$arrPage['menu_url'].'/" title="'.$arrPage['menu_name'].'">'.$arrPage['menu_name'].'</a>';
							
						}
						
					}
					
				}
				
			}
			
			if(in_array($strAction, array('news','allnews'))) {
				$strBreadcrumbContent	.= '<a href="/nieuws/" title="Nieuws">Nieuws</a>';
				
				if(isset($arrParams['title']) && ! empty($arrParams['title'])) {
						
					// Set Models
					$objModelNews		= new KZ_Models_News();
					
					// Get News Item
					$arrNews			= $objModelNews->getNewsBySlug($arrParams['title'], 1);
					
					// Check if News was found
					if(isset($arrNews) && is_array($arrNews) && count($arrNews) > 0) {
						$strBreadcrumbContent	.= '<a href="/nieuws/'.$arrNews['nameSlug'].'/" title="'.$arrNews['name'].'">'.$arrNews['name'].'</a>';											
					}

				}
			}
			
			if(in_array($strAction, array('agenda','allagenda'))) {
				$strBreadcrumbContent	.= '<a href="/agenda/" title="Agenda">Agenda</a>';
				
				if(isset($arrParams['title']) && ! empty($arrParams['title'])) {
					
					// Set Models
					$objModelAgenda		= new KZ_Models_Agenda();
					
					// Get Agenda Item
					$arrAgenda			= $objModelAgenda->getAgendaBySlug($arrParams['title'], 1);
					
					// Check if Agenda was found
					if(isset($arrAgenda) && is_array($arrAgenda) && count($arrAgenda) > 0) {
						$strBreadcrumbContent	.= '<a href="/agenda/'.$arrAgenda['nameSlug'].'/" title="'.$arrAgenda['name'].'">'.$arrAgenda['name'].'</a>';											
					}

				}
				
			}

            if(in_array($strController, array('blog'))) {

				// Set Base Breadcrumb
	            $strBreadcrumbContent	.= '<a href="/blog/" title="Blog">Blog</a>';

	            // Set Blog Model
	            $objModelBlog = new KZ_Models_Blog();

	            // Check for blogger
	            if(! empty($arrParams['blogger'])) {
	            	$arrBlogger = $objModelBlog->getBloggerBySlug($arrParams['blogger']);
	            	if(! empty($arrBlogger) && is_array($arrBlogger)) {
						// Add Blogger to Breadcrumb
			            $strBreadcrumbContent	.= '<a href="/blog/'.$arrBlogger['slug'].'/" title="'.$arrBlogger['name'].'">'.$arrBlogger['name'].'</a>';
		            }
	            }

	            // Check for item
	            if(! empty($arrParams['item'])) {
		            $arrBlogItem = $objModelBlog->getBlogItemBySlug($arrParams['item']);
		            if(! empty($arrBlogItem) && is_array($arrBlogItem)) {
			            // Add Blog item to Breadcrumb
			            $strBreadcrumbContent	.= '<a href="/blog/'.$arrBlogItem['slug'].'/" title="'.$arrBlogItem['title'].'">'.$arrBlogItem['title'].'</a>';
		            }
	            }

            }
			
			if(in_array($strAction, array('results'))) {
				$strBreadcrumbContent	.= '<a href="/uitslagen/" title="Uitslagen">Uitslagen</a>';
			}
			
			if(in_array($strAction, array('program'))) {
				$strBreadcrumbContent	.= '<a href="/programma/" title="Programma">Programma</a>';
			}
			
			if(in_array($strAction, array('match'))) {
				
				if(isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'results')) {
					$strBreadcrumbContent	.= '<a href="/uitslagen/" title="Uitslagen">Uitslagen</a>';
				} else {
					$strBreadcrumbContent	.= '<a href="/programma/" title="Programma">Programma</a>';
				}
				
				if(isset($arrParams['match_id']) && ! empty($arrParams['match_id']) && is_numeric($arrParams['match_id'])) {
					
					// Set Models
					$objModelMatches	= new KZ_Models_Matches();
					
					// Get Match
					$arrMatch			= $objModelMatches->getMatchByID($arrParams['match_id']);
					
					// Check if Match was found
					if(isset($arrMatch) && is_array($arrMatch) && count($arrMatch) > 0) {
						$strBreadcrumbContent	.= '<a href="/wedstrijd/'.$arrMatch['match_id'].'/" title="'.$arrMatch['team_home_name'].' - '.$arrMatch['team_away_name'].'">'.$arrMatch['team_home_name'].' - '.$arrMatch['team_away_name'].'</a>';											
					}
					
				}
	
			}
			
			if($strController == 'gastenboek') {
				
				$strBreadcrumbContent	.= '<a href="/gastenboek/" title="Gastenboek">Gastenboek</a>';
				
				if($strAction == 'bericht') {
						$strBreadcrumbContent	.= '<a href="/gastenboek/bericht/" title="Bericht">Bericht</a>';				
				}
			
			}
			
			if($strController == 'team') {
				if(isset($arrParams['team']) && ! empty($arrParams['team'])) {
					$strBreadcrumbContent .= '<a href="/teams/" title="Teams">Teams</a>';
					$strBreadcrumbContent .= '<a href="/team/'.$arrParams['team'].'/" title="KZ/Hiltex '.$arrParams['team'].'">KZ/Hiltex '.$arrParams['team'].'</a>';
				}
			}
			
			if(in_array($strAction, array('social'))) {
				$strBreadcrumbContent	.= '<a href="/social/" title="Social">Social</a>';
			}

			if(in_array($strController, array('profile'))) {
				$strBreadcrumbContent	.= '<a href="/profiel/" title="Profiel">Profiel</a>';
				if($strAction != 'index') {
					$strBreadcrumbContent	.= '<a href="/profiel/'.$strAction.'" title="'.$strAction.'">'.ucfirst($strAction).'</a>';
				}
			}

			return '<div id="breadcrumb">'.stripslashes($strBreadcrumbContent).'</div>';
		}
		
	}
	
	public function adminBreadcrumb($objRequest, $strController, $strAction, $strLanguage)
	{
		
		// Set View
		$objView = new Zend_View();
		$objView->addHelperPath('KZ/View/Helper/', 'KZ_View_Helper');
		
		// Homepage
		$strBreadcrumbContent = '<li><a href="/admin/" title="'.$objView->translate('Back to Homepage').'">'.$objView->translate('Back to Home').'</a></li>';
		
		// Controller
		$strControllerName 		=  (($strController == 'index') ? 'Dashboard' : ucfirst($strController));
		$strBreadcrumbContent 	.= '<li><a href="/admin/'.$strController.'/" title="'.$strControllerName.'">'.$strControllerName.'</a></li>';
		
		return '<ul id="breadcrumbs">
					'.$strBreadcrumbContent.'
				</ul>';
		
	}
	
}