<?php

class KZ_View_Helper_Navigator extends Zend_View_Helper_Abstract
{
	
	public function navigator($intNavigator = false)
	{
		$objRequest	= Zend_Controller_Front::getInstance()->getRequest();
		
		return $this->getNavigation($intNavigator, $objRequest->getParam('module'),$objRequest->getParam('controller'),$objRequest->getParam('action'));
	}
	
	public function getNavigation($intNavigator, $strModuleName, $strControllerName, $strActionName)
	{
		if($strModuleName == 'admin') {
			$strNavigation = $this->adminNavigation($intNavigator, $strModuleName, $strControllerName, $strActionName);
		} elseif($strModuleName == 'homeowner') {
			$strNavigation = 'homeowner navigation';
		} else {
			$strNavigation = $this->defaultNavigation($intNavigator, $strModuleName, $strControllerName, $strActionName);
		}
		return $strNavigation;
	}
	
	public function adminNavigation($intNavigator, $strModuleName, $strControllerName, $strActionName)
	{
		$strNavigationHtml = '';
		
		// Get View
		$objView = new Zend_View();
		$objView->addHelperPath('KZ/View/Helper/', 'KZ_View_Helper');
		
		/**
		 * ADD Admin Navigation Controllers to APPLICATION.INI file for ACL Plugin
		 */
		
		if($strControllerName != 'login') {
			
			// Get the Application Config
			$objConfig					= Zend_Registry::get('Zend_Config');
			
			// Set Models
			$objModelUsers				= new KZ_Models_Users();
			
			// Set Disabled Controllers for menu
			$arrDisabledControllers 	= array('index','error','login','logout');
			
			// Get User Session
			$objNamespace 				= new Zend_Session_Namespace('KZ_Admin');
			
			// Set User Session
			$arrUser					= $objNamespace->user;
			
			// Get User Permission
			$arrUserPermissions			= $objModelUsers->getGroupPermissions($arrUser['user_group_id']);
			
			// Set Default User Controller Permissions
			$arrAllowedControllers 	= array();
			
			// Set Default User Action Permissions
			$arrAllowedActions		= array();
			
			// Check if User Permisson where set
			if(isset($arrUserPermissions) && is_array($arrUserPermissions) && count($arrUserPermissions) > 0) {
				
				// Set Permission for Controllers
				if(strstr($arrUserPermissions['controllers'],',')) {
					$arrAllowedControllers = explode(',', $arrUserPermissions['controllers']);
				} else {
					$arrAllowedControllers = array($arrUserPermissions['controllers']);
				}
				
				// Set Action Persmission
				$arrAllowedActions	= json_decode($arrUserPermissions['actions'], true);
			}

			$strNavigationHtml = '<nav id="main-nav"><ul>';
								
			foreach($objConfig->controllers as $intControllerKey => $strConfigController) {
				
				if(
				
						in_array($strConfigController, $arrDisabledControllers)
					||	! in_array($strConfigController, $arrAllowedControllers)
				
				) {
					continue;
				}

				$strMainItemClass 	= ((strtolower(str_replace(' ', '', $strConfigController)) == (($strControllerName == 'index') ? 'dashboard' : $strControllerName)) ? 'current' : '');
				$strHasSubmenu 		= ((isset($arrAllowedActions[$strConfigController]) && count($arrAllowedActions[$strConfigController]) > 0) ? '' : ' no-submenu');
				
				$strNavigationHtml .= '<li class="'.$strMainItemClass.'">
										<a class="'.strtolower(str_replace(' ', '', $strConfigController)).$strHasSubmenu.'" href="/admin/'.str_replace(' ', '', strtolower($strConfigController)).'" title="'.ucfirst($strConfigController).'">'.ucfirst($objView->translate($strConfigController)).'</a>';
				
				if(isset($arrAllowedActions) && is_array($arrAllowedActions) && in_array($strConfigController, array_keys($arrAllowedActions)) && $strHasSubmenu == '') {
					
					$strNavigationHtml .= '<ul>';
					
					foreach($arrAllowedActions[$strConfigController] as $strSubItem) {
						
						$strSubItemLink = (($strSubItem == 'overview') ? 'index' : $strSubItem);
						
						$strSubItemClass = ((stristr($strActionName, $strSubItem)) ? 'current' : '');
						$strNavigationHtml .= '	<li class="'.$strSubItemClass.'">
													<a href="/admin/'.strtolower(str_replace(' ', '', $strConfigController)).'/'.str_replace(' ', '', $strSubItemLink).'" title="'.ucfirst($strSubItem).'">'.ucfirst($objView->translate($strSubItem)).'</a>
												</li>';
						
					}
					
					$strNavigationHtml .= '</ul>';
					
				}
				
				$strNavigationHtml .= '</li>';
				
			}
			
			$strNavigationHtml .= '</ul>';
		}
		
		return $strNavigationHtml;
	}
	
	public function defaultNavigation($intNavigator, $strModuleName, $strControllerName, $strActionName)
	{
		// Get Request Object
		$objRequest		= Zend_Controller_Front::getInstance()->getRequest();
		
		// Get Main Page Title
		$strMainPageTitle	= $objRequest->getParam('mainpage');
		
		// Get Title Param
		$strTitleSlug 		= ((! is_null($strMainPageTitle) && $strMainPageTitle != '') ? $strMainPageTitle : $objRequest->getParam('title')); 
		
		// Get Sub Title Slug
		$strSubTitleSlug	= $objRequest->getParam('title');
		
		// Get View
		$objView = new Zend_View();
		$objView->addHelperPath('KZ/View/Helper/', 'KZ_View_Helper');
		
		// Set Model
		$objPageModel	= new KZ_Models_Pages();
		
		// Get Main Pages
		$arrMainPages		= $objPageModel->getPages(1, $intNavigator, 'rank');
		
		// Get Sub Pages
		$arrSubPages		= $objPageModel->getPages(2, $intNavigator, 'rank', 'parent_id');
		
		// Get Sub Sub Pages
		$arrSubSubPages		= $objPageModel->getPages(3, $intNavigator, 'rank', 'parent_id');
		
		// Set Defaults
		$strMenuHtml 		= '';
		
		if($intNavigator == 2) {
			// FOOTER
		
			// Loop through Main Pages
			foreach($arrMainPages as $intPageKey => $arrPage) {
				
				if($arrPage['status'] != 1) { continue; }
				
				$strClass 			= (($strTitleSlug == $arrPage['menu_url']) ? ' hover' : '');
				$strMenuHtml 		.= '<dl><dt>'.$arrPage['menu_name'].'</dt>';
				
				// Check if Sub Pages where found
				if(isset($arrSubPages[$arrPage['page_id']]) && is_array($arrSubPages[$arrPage['page_id']])) {
					
					// Loop through Sub Pages
					foreach($arrSubPages[$arrPage['page_id']] as $intSubPageKey => $arrSubPage) {
						
						if($arrSubPage['status'] != 1) { continue; }
						
						$strClass			= (($strSubTitleSlug == $arrSubPage['menu_url']) ? ' class="hover"' : '');
						$strSubPageUrl		= ((strstr($arrSubPage['menu_url'], 'http://')) ? $arrSubPage['menu_url'] : '/'.$arrPage['menu_url'].'/'.$arrSubPage['menu_url']);
						$strSubPageTarget	= ((strstr($arrSubPage['menu_url'], 'http://')) ? ' target="_blank"' : '');
						
						$strMenuHtml .= '<dd><a'.$strClass.' href="'.$strSubPageUrl.'"'.$strSubPageTarget.'>'.$arrSubPage['menu_name'].'</a></dd>';
						
					}
					
				}

				$strMenuHtml .= '</dl>';
				
			}	
			
			return '<footer><span style="display:table-row">'.$strMenuHtml.'</span></footer>';
			
		} else {
			// MAIN	

			// Set Default Ordered Pages array
			$arrOrderedPages = array();
			
			// Loop through Main Pages
			foreach($arrMainPages as $intPageKey => $arrPage) {
				
				if($arrPage['status'] != 1) { continue; }
				
				$strClass 	= (($strTitleSlug == $arrPage['menu_url']) ? ' hover active' : '');
				
				// Set Default Hover class for homepage
				 if(	$arrPage['page_id'] == 1 
				 	&& 	$strControllerName == 'page'
				 	&& 	$strActionName == 'index'
				 	&& 	$strTitleSlug == 'home'
				 ) {
					$strClass = ' hover active';				 	
				 }
				 
				$strPageUrl		= ((strstr($arrPage['menu_url'], 'http://')) ? $arrPage['menu_url'] : '/'.$arrPage['menu_url']);
				$strPageTarget	= ((strstr($arrPage['menu_url'], 'http://')) ? ' target="_blank"' : '');
				
				$strLinkPlus	= ((isset($arrSubPages[$arrPage['page_id']]) && is_array($arrSubPages[$arrPage['page_id']])) ? '<strong>+</strong> ' : '');
				
				$strMenuHtml 	.= '<li class="main"><a class="main'.$strClass.'" href="'.$strPageUrl.'"'.$strPageTarget.' title="'.$arrPage['menu_name'].'"><span>'.$strLinkPlus.$arrPage['menu_name'].'</span></a>';
				
				// Check if Sub Pages where found
				if(isset($arrSubPages[$arrPage['page_id']]) && is_array($arrSubPages[$arrPage['page_id']])) {
					
					$strMenuHtml .= '<ul>';
					
					// Loop through Sub Pages
					foreach($arrSubPages[$arrPage['page_id']] as $intSubPageKey => $arrSubPage) {
						
						if($arrSubPage['status'] != 1) { continue; }
						
						$strSubClass 		= (($strClass == ' hover active' && $strSubTitleSlug == $arrSubPage['menu_url']) ? ' class="subactive"' : '');
						$strSubPageUrl		= ((strstr($arrSubPage['menu_url'], 'http://')) ? $arrSubPage['menu_url'] : '/'.$arrPage['menu_url'].'/'.$arrSubPage['menu_url']);
						$strSubPageTarget	= ((strstr($arrSubPage['menu_url'], 'http://')) ? ' target="_blank"' : '');
						
						$strMenuHtml .= '<li'.$strSubClass.'><a href="'.$strSubPageUrl.'"'.$strSubPageTarget.'>'.$arrSubPage['menu_name'].'</a></li>';
						
					}
					
					$strMenuHtml .= '</ul>';
					
				}
				
				
				$strMenuHtml .= '</li>';
				
				if($intPageKey == 2) {
					
					$strMenuHtml .= '<li class="main logo"><a href="'.ROOT_URL.'"><img src="/assets/default/image/logo_kzhiltex.png"></a></li>';
					
				}
				
			}
			
			// Set Responsive Object
			$objResponsive	= new KZ_Controller_Action_Helper_Responsive();
			
			return '<nav class="zapp"><ul class="main zapp">'.$strMenuHtml.'</ul></nav>';
			
		}

	}

}