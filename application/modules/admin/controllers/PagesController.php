<?php
class Admin_PagesController extends KZ_Controller_Action
{

	public function indexAction()
	{
		// Set Models
		$objModelPage				= new KZ_Models_Pages();
		$objModelMenu				= new KZ_Models_Menu();
		$objModelWidgets			= new KZ_Models_Widgets();
		
		// Get All Standalone pages, ordered by rank
		$arrStandalonePages			= $objModelPage->getPages(0, false, 'rank');

		// Get All Main pages, ordered by rank
		$arrMainPages				= $objModelPage->getPages(1, false, 'rank');
		
		// Get All Sub Pages, ordered by rank
		$arrSubPages				= $objModelPage->getPages(2, false, 'rank', 'parent_id');
		
		// Get All Sub Sub Pages, ordered by rank
		$arrSubSubPages				= $objModelPage->getPages(3, false, 'rank', 'parent_id');
		
		// Get Menu Types
		$arrMenuTypes				= $objModelMenu->getMenuTypes('menu_type_id');
		
		// Set Default Variables
		$strName					= '';
		$strMenuName				= '';
		$strMenuUrl					= '';
		$intFullPage                = KZ_Controller_Action::STATE_INACTIVE;
		$strSeoTitle				= '';
		$strSeoKeywords				= '';
		$strSeoDescription			= '';
		$intWidgetLayout			= 0;
		$intStatus					= 0;

		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams			= $this->_getAllParams();

			// Set Post Variables
			$strName				= $arrPostParams['name'];
			$strMenuName			= $arrPostParams['menu_name'];
			$strMenuUrl				= $arrPostParams['menu_url'];
            $intFullPage            = $arrPostParams['fullpage'];
			$strSeoTitle			= $arrPostParams['seo_title'];
			$strSeoKeywords			= $arrPostParams['seo_keywords'];
			$strSeoDescription		= $arrPostParams['seo_description'];
		    $intWidgetLayout		= $arrPostParams['widget_layout'];
			$intStatus				= $arrPostParams['status'];

			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strMenuName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a menu name');
			} elseif(empty($strMenuUrl)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a menu url');
			} else {

				// Set Page as default allowed to be deleted
				$intPageDelete			= 1;
				
				// Pages automaticly addes as Stand-alone.
				// Ordering can be done in overview

                // Resultset of matching pages with the same slug
                $arrMatchingPagesBySlug = $objModelPage->getMatchingPagesBySlug(KZ_Controller_Action_Helper_Slug::slug($strMenuUrl),'page_id');

				// Check if Pages where found
                if(empty($arrMatchingPagesBySlug) && is_array($arrMatchingPagesBySlug)) {
                    $arrPageData = array(
                        'parent_id'			=> 0,
                        'name' 				=> $strName,
                        'menu_name'			=> $strMenuName,
                        'menu_url'			=> ((strstr($strMenuUrl, 'http://')) ? $strMenuUrl : KZ_Controller_Action_Helper_Slug::slug($strMenuUrl)),
                        'menu_type_id'		=> 0,
                        'fullpage'          => $intFullPage,
                        'seo_title'			=> $strSeoTitle,
                        'seo_description'	=> $strSeoDescription,
                        'seo_keywords'		=> $strSeoKeywords,
                        'widget_layout'		=> (($intWidgetLayout == 0) ? NULL : $intWidgetLayout),
                        'menu'				=> 0,
                        'rank'				=> 0,
                        'delete'			=> $intPageDelete,
                        'status'			=> $intStatus,
                        'user_id'			=> KZ_Controller_Session::getActiveUser()
                    );

	                // Add Page
                    $intInsertID		= $objModelPage->addPage($arrPageData);

	                // Check for Succesfull page insert
                    if(isset($intInsertID) && is_numeric($intInsertID)) {
	                    // Return Feedback
                        $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added page')));
                        $this->_redirect('/admin/pages/content/id/'.$intInsertID.'/feedback/'.$strFeedback.'/#tab1');

                    } else {
                        // Return feedback
                        $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the page');
                    }
                } else {
	                // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'There is already a page with this url');
                }
            }
		}

		// Set Default Ordered Pages array
		$arrOrderedPages 		         = array();

		// Loop through Main Pages
		foreach($arrMainPages as $intPageKey => $arrPage) {

			// Set the Menu Type ID
			$intMenuType			= $arrPage['menu_type_id'];

			// Set the Main Page ID
			$intMainPageID			= $arrPage['page_id'];

			// Add page to Main Items
			$arrOrderedPages[$intMenuType][$intMainPageID]		= $arrPage;
		}

		// Order the Pages with Main Menu first
		ksort($arrOrderedPages);

		// Parse Variables to View
		$this->view->pages 				= $arrOrderedPages;
		$this->view->subpages			= $arrSubPages;
		$this->view->subsubpages		= $arrSubSubPages;

		// Set Widget Layout Status
		$intWidgetLayoutStatus			= 1; // Active Widget Layouts only

		// Get All Widget Layouts for status
		$arrWidgetLayouts				= $objModelWidgets->getWidgetLayouts($intWidgetLayoutStatus);

		// Parse Variables to View
		//$this->view->pages 				= $arrOrderedPages;
		$this->view->standalonePages	= $arrStandalonePages;
		$this->view->menuTypes			= $arrMenuTypes;
		$this->view->widgetLayouts		= $arrWidgetLayouts;

		$this->view->name				= $strName;
		$this->view->menu_name			= $strMenuName;
		$this->view->menu_url			= $strMenuUrl;
		$this->view->fullpage           = $intFullPage;
		$this->view->seo_title			= $strSeoTitle;
		$this->view->seo_description	= $strSeoDescription;
		$this->view->seo_keywords		= $strSeoKeywords;
		$this->view->widget_layout		= $intWidgetLayout;
		$this->view->status				= $intStatus;

	}
	
	public function editAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage				= new KZ_Models_Pages();
    	$objModelWidgets			= new KZ_Models_Widgets();
    	
    	// Get Page
    	$arrPage					= $objModelPage->getPageByID($arrParams['id']);
    	
    	// Check if Page wasn't found
    	if(isset($arrPage) && count($arrPage) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find page')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
		// Set Default Variables
		$strName					= $arrPage['name'];
		$strMenuName				= $arrPage['menu_name'];
		$strMenuUrl					= $arrPage['menu_url'];
		$intMenuTypeID				= $arrPage['menu_type_id'];
		$intFullPage                = $arrPage['fullpage'];
		$strSeoTitle				= $arrPage['seo_title'];
		$strSeoKeywords				= $arrPage['seo_keywords'];
		$strSeoDescription			= $arrPage['seo_description'];
		$intWidgetLayout			= $arrPage['widget_layout'];
		$intStatus					= $arrPage['status'];

		// Set Menu ID
		$intMenu					= $arrPage['menu'];
		
		// Get Other Pages with same menu type
		$arrPages					= $objModelPage->getPages($intMenu, false, 'rank');
		
		// Get Total Pages
		$intLastPageCount			= count($arrPages);
		
		// Get Latest Page Rank
		$intLatestPageRank			= (($intLastPageCount == 0) ? 0 : $arrPages[$intLastPageCount - 1]['rank']);


		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams			= $this->_getAllParams();

			// Set Post Variables
			$strName				= $arrPostParams['name'];
			$strMenuName			= $arrPostParams['menu_name'];
			$strMenuUrl				= $arrPostParams['menu_url'];
			$intFullPage            = $arrPostParams['fullpage'];
			$strSeoTitle			= $arrPostParams['seo_title'];
			$strSeoKeywords			= $arrPostParams['seo_keywords'];
			$strSeoDescription		= $arrPostParams['seo_description'];
			$intWidgetLayout		= $arrPostParams['widget_layout'];
			$intStatus				= $arrPostParams['status'];

			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else {

                // Resultset of matching pages with the same slug
                $arrMatchingPagesBySlug = $objModelPage->getMatchingPagesBySlug(KZ_Controller_Action_Helper_Slug::slug($strMenuUrl),'page_id');

                // Remove Current Page From Edit
                if(array_key_exists($arrParams['id'], $arrMatchingPagesBySlug)) {
                    // Remove Current Page From Matching Pages by Slug
                    unset($arrMatchingPagesBySlug[$arrParams['id']]);
                }

                if(empty($arrMatchingPagesBySlug) && is_array($arrMatchingPagesBySlug)){
                    $arrPageData = array(
                        'name' 				=> $strName,
                        'menu_name'			=> $strMenuName,
                        'menu_url'			=> $strMenuUrl,
                        'fullpage'          => $intFullPage,
                        'seo_title'			=> $strSeoTitle,
                        'seo_description'	=> $strSeoDescription,
                        'seo_keywords'		=> $strSeoKeywords,
                        'widget_layout'		=> (($intWidgetLayout == 0) ? NULL : $intWidgetLayout),
                        'status'			=> $intStatus,
                        'user_id'			=> KZ_Controller_Session::getActiveUser()
                    );

                    $intUpdateID		= $objModelPage->updatePage($arrPage['page_id'], $arrPageData);

                    if(isset($intUpdateID) && is_numeric($intUpdateID)) {
                        $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated page')));
                        $this->_redirect('/admin/pages/index/feedback/'.$strFeedback.'/#tab0');
                    } else {
                        // Return feedback
                        $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the page');
                    }
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'A page with this url already exists');
                }
				
			}
		
		}
		
		// Set Widget Layout Status for status
		$intWidgetLayoutStatus			= 1; // Active Widget Layouts only
		
		// Get All Widget Layouts
		$arrWidgetLayouts				= $objModelWidgets->getWidgetLayouts($intWidgetLayoutStatus);
		
		// Parse Variables to View
		$this->view->page 				= $arrPage;
		$this->view->pages				= $arrPages;
		$this->view->latestPageRank		= $intLatestPageRank; 
		$this->view->widgetLayouts		= $arrWidgetLayouts;

		$this->view->name				= $strName;
		$this->view->menu_name			= $strMenuName;
		$this->view->menu_url			= $strMenuUrl;
		$this->view->menu_type_id		= $intMenuTypeID;
		$this->view->fullpage           = $intFullPage;
		$this->view->seo_title			= $strSeoTitle;
		$this->view->seo_description	= $strSeoDescription;
		$this->view->seo_keywords		= $strSeoKeywords;
		$this->view->widget_layout		= $intWidgetLayout;
		$this->view->status				= $intStatus;

	}

	public function deleteAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	/**
    	 * @todo Make an isDeletable flag in database
    	 */
    	if($arrParams['id'] == 1) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to delete this page')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage				= new KZ_Models_Pages();
    	
    	// Get Page
    	$arrPage					= $objModelPage->getPageByID($arrParams['id']);

    	// Check if Page wasn't found
    	if(isset($arrPage) && count($arrPage) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find page')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Get Page Content
    	$arrPageContent				= $objModelPage->getPageContent($arrPage['page_id']);
		
		// Set Default Variables
		$strName					= $arrPage['name'];
		$strMenuName				= $arrPage['menu_name'];
		$strMenuUrl					= $arrPage['menu_url'];
		$strSeoTitle				= $arrPage['seo_title'];
		$strSeoKeywords				= $arrPage['seo_keywords'];
		$strSeoDescription			= $arrPage['seo_description'];
		$intStatus					= $arrPage['status'];
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			$intDeleteID		= $objModelPage->deletePage($arrPage['page_id']);
				
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				
				// Delete the Page Content
				$objModelPage->deletePageContentByPageID($arrPage['page_id']);
				
				// Update the Ordering after page has been deleted
				$objModelPage->updatePageOrderingAfterDelete($arrPage['rank'], $arrPage['menu']);
				
				// If page is main-menu item, update submenu-items to Standalone pages
				$objCurrentDate	= new Zend_Date();
				$arrUpdateData	= array('lastmodified' 	=> $objCurrentDate->toString('yyyy-MM-dd HH:mm:ss'), 
										'parent_id'		=> 0, 
										'menu' 			=> 0,
										'user_id'		=> KZ_Controller_Session::getActiveUser());
				
				$objModelPage->updateSubPagesAfterPageDelete($arrUpdateData, $arrPage['page_id']);
				
				
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted page')));
				$this->_redirect('/admin/pages/index/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the page');
			}
		
		}
		
		// Parse Variables to View
		$this->view->page 				= $arrPage;

		$this->view->name				= $strName;
		$this->view->menu_name			= $strMenuName;
		$this->view->menu_url			= $strMenuUrl;
		$this->view->seo_title			= $strSeoTitle;
		$this->view->seo_description	= $strSeoDescription;
		$this->view->seo_keywords		= $strSeoKeywords;
		$this->view->status				= $intStatus;
    	
    	
	}
	
	
	/**
	 * Save the menu ordering
	 */
	public function savemenuAction()
	{
		// Disable Layout and View
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	
		// Set an empty post array
		$arrPostData		= array();
		$arrMenuOrder		= array();
	
		// Check for AJAX call
		if($this->_request->isXmlHttpRequest()) {
			// Get all Post Data
			$arrPostData		= $this->_getAllParams();
	
			// Get the menu
			$arrMenuOrder		= $arrPostData['rank'];
				
			// Check for empty
			if(is_array($arrMenuOrder) && !empty($arrMenuOrder)) {
	
				// Set the Model
				$objModelPage			= new KZ_Models_Pages();
	
				// Set the Menu type
				$intMenuType			= (isset($arrPostData['menutype']) ? str_replace('nestable', '', $arrPostData['menutype']) : '1');
				$intMenuTypeID			= (($intMenuType == '0') ? '0' : $intMenuType);
				$intMenuID				= (($intMenuType == '0') ? '0' : '1');
	
				// Set the Start Rank number
				$intMainRankStart		= '1';
				$intSubRankStart		= '1';
				$intSubSubRankStart		= '1';
	
				// Create the Rank Update array
				$arrMainRankUpdate		= array();
				$arrSubRankUpdate		= array();
				$arrSubSubRankUpdate	= array();
				foreach($arrMenuOrder as $key => $arrMenuValue) {
					$intPageID				= $arrMenuValue['id'];
					$arrMainRankUpdate[]	= array('pageID' 		=> $intPageID,
													'menu'			=> $intMenuID,
													'menu_type_id'	=> $intMenuTypeID,
													'parent_id'		=> '0',
													'rank'			=> $intMainRankStart);
						
					// Check for sub-menu items
					if(isset($arrMenuValue['children']) && is_array($arrMenuValue['children']) && !empty($arrMenuValue['children'])) {
						foreach($arrMenuValue['children'] as $subkey => $arrSubValue) {
							//$intMenuID				= '2';
							$intSubPageID			= $arrSubValue['id'];
							$arrSubRankUpdate[]		= array('pageID'		=> $intSubPageID,
															'menu'			=> '2',
															'menu_type_id'	=> $intMenuTypeID,
															'parent_id'		=> $intPageID,
															'rank'			=> $intSubRankStart);
								
							// Check for subsub-menu items
							if(isset($arrSubValue['children']) && is_array($arrSubValue['children']) && !empty($arrSubValue['children'])) {
								foreach($arrSubValue['children'] as $subsubkey => $arrSubSubValue) {
									//$intMenuID					= '3';
									$intSubSubPageID			= $arrSubSubValue['id'];
									$arrSubSubRankUpdate[]		= array('pageID'		=> $intSubSubPageID,
																		'menu'			=> '3',
																		'menu_type_id'	=> $intMenuTypeID,
																		'parent_id'		=> $intSubPageID,
																		'rank'			=> $intSubSubRankStart);
									$intSubSubRankStart++;
								}
							}
								
							$intSubRankStart++;
						}
					}
						
					$intMainRankStart++;
						
				}
	
				// Update the Main Pages
				if(!empty($arrMainRankUpdate)) {
					foreach($arrMainRankUpdate as $intMainkey => $arrMainPageValue) {
						// Update the Page
						$objModelPage->updatePageRanks($arrMainPageValue);
					}
				}
	
				// Update the Sub Pages
				if(!empty($arrSubRankUpdate)) {
					foreach($arrSubRankUpdate as $intSubkey => $arrSubPageValue) {
						// Update the Page
						$objModelPage->updatePageRanks($arrSubPageValue);
					}
				}
	
				// Update the SubSub Pages
				if(!empty($arrSubSubRankUpdate)) {
					foreach($arrSubSubRankUpdate as $intSubSubkey => $arrSubSubPageValue) {
						// Update the Page
						$objModelPage->updatePageRanks($arrSubSubPageValue);
					}
				}
			}
				
			// All Done, echo a true for AJAX
			echo true;
			die;
		}
	
		echo 'somethings wrong';
		die;
	}
	
	
	public function contentAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage				= new KZ_Models_Pages();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	$objModelTeams				= new KZ_Models_Teams();
    	
    	// Get Page by ID
    	$arrPage					= $objModelPage->getPageByID($arrParams['id']);
    	
        // Get Categories
        $booReturnTopsportAsCategory = true;
        $arrTeamCategories = $objModelTeams->getDistinctCategories($booReturnTopsportAsCategory);
    	
		// Check if category was found
    	if(! isset($arrPage) || ! is_array($arrPage) || count($arrPage) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find page')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get Page Content By Page ID
    	$arrPageContent			= $objModelPage->getPageContent($arrPage['page_id']);
    	
    	// Get Last Page Rank
    	$intLastPageRank			= $objModelPage->getLastPageRank($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('pages','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= 0;
    	$strName					= '';
    	$intStatus					= 0;
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		$arrContent13Teams			= array();
    	
    	// Check for Post
    	if($this->getRequest()->isPost()) {
    		
    		// Set Post Params
    		$arrPostParams				= $this->_getAllParams();
    		
    		// Set Post Variables
    		$intContentTypeID			= $arrPostParams['content_type_id'];
    		$strName					= $arrPostParams['name'];
    		$intStatus					= $arrPostParams['status'];
    		$intActiveContentType		= $arrPostParams['activeContentType'];
    		
    		if(empty($strName)) {
    			$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
    		} else {
    			
    			// Set Default Data array
    			$arrPageData = array(
    				'page_id'			=> $arrPage['page_id'],
    				'content_type_id'	=> $intContentTypeID,
    				'rank'				=> ($intLastPageRank + 1),
    				'name'				=> $strName,
    				'status'			=> $intStatus,
    				'user_id'			=> KZ_Controller_Session::getActiveUser()
    			);
    			
	    		if(isset($intActiveContentType) && ! empty($intActiveContentType) && is_numeric($intActiveContentType)) {
	    			
	    			// Title and text
	    			if($intActiveContentType == 1) {
	    				
	    				// Set Post Variables
	    				$strContent1Title 	= $arrPostParams['content_1_title'];
	    				$strContent1Text 	= $arrPostParams['content_1_text'];
	    				
	    				if(empty($strContent1Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} elseif(empty($strContent1Text)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a text');
	    				} else {
	    					
	    					// Add Default table class to table element
//	    					if(
//	    					strstr($arrPostParams['content_1_text'], '<table')
//	    					&&	! strstr($arrPostParams['content_1_text'], 'class="default"')
//	    					) {
//
//	    						$strContent1Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_1_text']);
//
//	    					}
	    					
	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_1_title'	=> $strContent1Title,
	    													'content_1_text'	=> $strContent1Text
	    												));
	    					
	    					// Add Page Content
	    					$intInsertID 		= $objModelPage->addPageContent($arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPage['page_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Title, image and text
	    			if($intActiveContentType == 2) {
	    				
	    				// Set Post Variables
	    				$strContent2Title 	= $arrPostParams['content_2_title'];
	    				$strContent2Image 	= $arrPostParams['content_2_image'];
	    				$strContent2Text 	= $arrPostParams['content_2_text'];
	    				
	    				if(empty($strContent2Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} elseif(empty($strContent2Image)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
	    				} elseif(empty($strContent2Text)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a text');
	    				} else {
	    					
	    					// Add Default table class to table element
//	    					if(
//	    					strstr($arrPostParams['content_2_text'], '<table')
//	    					&&	! strstr($arrPostParams['content_2_text'], 'class="default"')
//	    					) {
//
//	    						$strContent2Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_2_text']);
//
//	    					}
	    					
	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_2_title'	=> $strContent2Title,
	    													'content_2_image'	=> str_replace(ROOT_URL.'/upload/', '', $strContent2Image),
	    													'content_2_text'	=> $strContent2Text
	    												));
	    					
	    					// Add Page Content
	    					$intInsertID 		= $objModelPage->addPageContent($arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPage['page_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    					
	    				}
	    				
	    			}
	    			
	    			// Video
	    			if($intActiveContentType == 3) {
	    				
	    				// Set Post Variables
	    				$strContent3Video 	= $arrPostParams['content_3_video'];
	    				
	    				if(empty($strContent3Video)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a video');
	    				} else {

	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_3_video'	=> $strContent3Video
	    												));

	    					// Add Page Content
	    					$intInsertID 		= $objModelPage->addPageContent($arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPage['page_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Teams
	    			if($intActiveContentType == 13) {
	    				
	    				// Set Defaults
	    				$arrActiveTags			= array();
	    				
	    				// Loop through Post for selected Tags
						foreach($arrPostParams as $strPostKey => $strPostValue) {
							// Check if tag was found
							if(strstr($strPostKey, 'team-')) {
								// Add Tag to active Tags array
								$arrActiveTags[] = $strPostValue;
							}
						}
	    				
	    				if(count($arrActiveTags) == 0) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any team');
	    				} else {

	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_13_teams'	=> implode(',', $arrActiveTags)
	    												));

	    					// Add Page Content
	    					$intInsertID 		= $objModelPage->addPageContent($arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPage['page_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    		}
    		}
    		
    		
    	}
    	
    	// Initialize Wysiwyg Editor
		$this->view->editorInit			= KZ_Controller_Editor::setEditor('tinymce'); 
    	
    	// Parse Data to View
    	$this->view->page				= $arrPage;
    	$this->view->pages_content		= $arrPageContent;
    	$this->view->contentTypes 		= $arrContentTypes;
    	$this->view->team_categories    = $arrTeamCategories;
    	
    	$this->view->content_type_id 	= $intContentTypeID;
    	$this->view->name 				= $strName;
    	$this->view->status			 	= $intStatus;
    	
    	$this->view->content_1_title	= $strContent1Title;
    	$this->view->content_1_text		= $strContent1Text;
    	
    	$this->view->content_2_title	= $strContent2Title;
    	$this->view->content_2_image	= $strContent2Image;
    	$this->view->content_2_text		= $strContent2Text;
    	
    	$this->view->content_3_video	= $strContent3Video;
    	
		$this->view->content_13_teams	= $arrContent13Teams;

	}
	
	public function contenteditAction()
	{
		
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage				= new KZ_Models_Pages();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	$objModelTeams				= new KZ_Models_Teams();
    	
    	// Get Page Content by ID
    	$arrPageContent				= $objModelPage->getPageContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('pages','content_type_id');
    	
    	// Get Categories
        $booReturnTopsportAsCategory = true;
    	$arrTeamCategories = $objModelTeams->getDistinctCategories($booReturnTopsportAsCategory);
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrPageContent['content_type_id'];
    	$strName					= $arrPageContent['name'];
    	$intStatus					= $arrPageContent['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		$arrContent13Teams			= array();
		
		// Set Default Content Data
		$arrData					= unserialize($arrPageContent['data']);
		
		if($intContentTypeID == 1) {
			$strContent1Title			= $arrData['content_1_title'];
    		$strContent1Text			= $arrData['content_1_text'];
		}
		
		if($intContentTypeID == 2) {
			$strContent2Title			= $arrData['content_2_title'];
			$strContent2Image			= $arrData['content_2_image'];
    		$strContent2Text			= $arrData['content_2_text'];
		}
		
		if($intContentTypeID == 3) {
			$strContent3Video			= $arrData['content_3_video'];
		}
		
		if($intContentTypeID == 13) {
			$arrContent13Teams			= ((! empty($arrData['content_13_teams'])) ? explode(',', $arrData['content_13_teams']) : array());
		}
    	
    	// Check for Post
    	if($this->getRequest()->isPost()) {
    		
    		// Set Post Params
    		$arrPostParams				= $this->_getAllParams();
    		
    		// Set Post Variables
    		$intContentTypeID			= $arrPostParams['content_type_id'];
    		$strName					= $arrPostParams['name'];
    		$intStatus					= $arrPostParams['status'];
    		$intActiveContentType		= $arrPostParams['activeContentType'];
    		
    		if(empty($strName)) {
    			$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
    		} else {
    			
    			// Set Default Data array
    			$arrPageData = array(
    				'page_id'			=> $arrPageContent['page_id'],
    				'content_type_id'	=> $intContentTypeID,
    				'name'				=> $strName,
    				'status'			=> $intStatus,
    				'user_id'			=> KZ_Controller_Session::getActiveUser()
    			);
    			
	    		if(isset($intActiveContentType) && ! empty($intActiveContentType) && is_numeric($intActiveContentType)) {
	    			
	    			// Title and text
	    			if($intActiveContentType == 1) {
	    				
	    				// Set Post Variables
	    				$strContent1Title 	= $arrPostParams['content_1_title'];
	    				$strContent1Text 	= $arrPostParams['content_1_text'];
	    				
	    				if(empty($strContent1Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} elseif(empty($strContent1Text)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a text');
	    				} else {
	    					
	    					// Add Default table class to table element
//	    					if(
//	    					strstr($arrPostParams['content_1_text'], '<table')
//	    					&&	! strstr($arrPostParams['content_1_text'], 'class="default"')
//	    					) {
//
//	    						$strContent1Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_1_text']);
//
//	    					}
	    					
	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_1_title'	=> $strContent1Title,
	    													'content_1_text'	=> $strContent1Text
	    												));
	    					
	    					// Update Page Content
	    					$intUpdateID 		= $objModelPage->updatePageContent($arrPageContent['page_content_id'], $arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
	    						
	    						// Get Last Revision from Page Content Backup
	    						$intLastRevision		= $objModelPage->getLastPageContentBackupRevision($arrPageContent['page_content_id']);
	    						
	    						// Set Page Content Backup array
	    						$arrPageContentBackup 	= array(
	    							'page_content_id' 	=> $arrPageContent['page_content_id'],
	    							'page_id'			=> $arrPageContent['page_id'],
	    							'revision'			=> ($intLastRevision + 1),
	    							'content_type_id'	=> $arrPageContent['content_type_id'],
	    							'name'				=> $arrPageContent['name'],
	    							'data'				=> $arrPageContent['data'],
	    							'status'			=> $arrPageContent['status'],
	    							'user_id'			=> KZ_Controller_Session::getActiveUser()
	    						);
	    						
	    						// Backup overwritten Page Content
	    						$intInsertID			= $objModelPage->addPageContentBackup($arrPageContentBackup);
	    						
	    						
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPageContent['page_id'].'/feedback/'.$strFeedback.'/');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Title, image and text
	    			if($intActiveContentType == 2) {
	    				
	    				// Set Post Variables
	    				$strContent2Title 	= $arrPostParams['content_2_title'];
	    				$strContent2Image 	= $arrPostParams['content_2_image'];
	    				$strContent2Text 	= $arrPostParams['content_2_text'];
	    				
	    				if(empty($strContent2Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} elseif(empty($strContent2Image)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
	    				} elseif(empty($strContent2Text)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a text');
	    				} else {
	    					
	    					// Add Default table class to table element
//	    					if(
//	    					strstr($arrPostParams['content_2_text'], '<table')
//	    					&&	! strstr($arrPostParams['content_2_text'], 'class="default"')
//	    					) {
//
//	    						$strContent2Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_2_text']);
//
//	    					}
	    					
	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_2_title'	=> $strContent2Title,
	    													'content_2_image'	=> str_replace(ROOT_URL.'/upload/', '', $strContent2Image),
	    													'content_2_text'	=> $strContent2Text
	    												));
	    					
	    					// Update Page Content
	    					$intUpdateID 		= $objModelPage->updatePageContent($arrPageContent['page_content_id'], $arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPageContent['page_id'].'/feedback/'.$strFeedback.'/');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    					
	    				}
	    				
	    			}
	    			
	    			// Video
	    			if($intActiveContentType == 3) {
	    				
	    				// Set Post Variables
	    				$strContent3Video 	= $arrPostParams['content_3_video'];
	    				
	    				if(empty($strContent3Video)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a video');
	    				} else {

	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_3_video'	=> $strContent3Video
	    												));

	    					// Update Page Content
	    					$intUpdateID 		= $objModelPage->updatePageContent($arrPageContent['page_content_id'], $arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPageContent['page_id'].'/feedback/'.$strFeedback.'/');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Teams
	    			if($intActiveContentType == 13) {
	    				
	    				// Set Defaults
	    				$arrActiveTags			= array();
	    				
	    				// Loop through Post for selected Tags
						foreach($arrPostParams as $strPostKey => $strPostValue) {
							// Check if tag was found
							if(strstr($strPostKey, 'team-')) {
								// Add Tag to active Tags array
								$arrActiveTags[] = $strPostValue;
							}
						}
	    				
	    				if(count($arrActiveTags) == 0) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any team');
	    				} else {

	    					// Add content data to array
	    					$arrPageData['data']	= serialize(array(	
	    													'content_13_teams'	=> implode(',', $arrActiveTags)
	    												));

	    					// Update Page Content
	    					$intUpdateID 		= $objModelPage->updatePageContent($arrPageContent['page_content_id'], $arrPageData);

	    					// Check if Page Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/pages/content/id/'.$arrPageContent['page_id'].'/feedback/'.$strFeedback.'/');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    		}
	    		
    		}
    		
    	}
    	
    	// Initialize Wysiwyg Editor
		$this->view->editorInit			= KZ_Controller_Editor::setEditor('tinymce'); 
    	
    	// Parse Data to View
    	$this->view->page_content		= $arrPageContent;
    	$this->view->contentTypes 		= $arrContentTypes;
    	$this->view->team_categories	= $arrTeamCategories;
    	
    	$this->view->content_type_id 	= $intContentTypeID;
    	$this->view->name 				= $strName;
    	$this->view->status			 	= $intStatus;
    	
    	$this->view->content_1_title	= $strContent1Title;
    	$this->view->content_1_text		= $strContent1Text;
    	
    	$this->view->content_2_title	= $strContent2Title;
    	$this->view->content_2_image	= $strContent2Image;
    	$this->view->content_2_text		= $strContent2Text;
    	
    	$this->view->content_3_video	= $strContent3Video;
    	
    	$this->view->content_13_teams	= $arrContent13Teams;
    	
	}
	
	public function contentdeleteAction()
	{

		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage				= new KZ_Models_Pages();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Page Content by ID
    	$arrPageContent				= $objModelPage->getPageContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('pages','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrPageContent['content_type_id'];
    	$strName					= $arrPageContent['name'];
    	$intStatus					= $arrPageContent['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrPageContent['data']);
		
		if($intContentTypeID == 1) {
			$strContent1Title			= $arrData['content_1_title'];
    		$strContent1Text			= $arrData['content_1_text'];
		}
		
		if($intContentTypeID == 2) {
			$strContent2Title			= $arrData['content_2_title'];
			$strContent2Image			= $arrData['content_2_image'];
    		$strContent2Text			= $arrData['content_2_text'];
		}
		
		if($intContentTypeID == 3) {
			$strContent3Video			= $arrData['content_3_video'];
		}
    	
    	// Check for Post
    	if($this->getRequest()->isPost()) {
    		
    		// Delete Page Content
			$intDeleteID 				= $objModelPage->deletePageContent($arrPageContent['page_content_id']);

			// Check if Page Content was succesfully updated
	    	if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted content')));
				$this->_redirect('/admin/pages/content/id/'.$arrPageContent['page_id'].'/feedback/'.$strFeedback.'/');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the content');
			}

	    }
    	
    	// Parse Data to View
    	$this->view->page_content		= $arrPageContent;
    	$this->view->contentTypes 		= $arrContentTypes;
    	
    	$this->view->content_type_id 	= $intContentTypeID;
    	$this->view->name 				= $strName;
    	$this->view->status			 	= $intStatus;
    	
    	$this->view->content_1_title	= $strContent1Title;
    	$this->view->content_1_text		= $strContent1Text;
    	
    	$this->view->content_2_title	= $strContent2Title;
    	$this->view->content_2_image	= $strContent2Image;
    	$this->view->content_2_text		= $strContent2Text;
    	
    	$this->view->content_3_video	= $strContent3Video;
		
	}
	
	public function sortcontentAction()
	{
		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	$arrParams = $this->_getAllParams();
    	
    	// Check for array with rank data
    	if(isset($arrParams['rank']) && ! empty($arrParams['rank']) && is_array($arrParams['rank']))
    	{
    		// Set Models
    		$objModelPage 		= new KZ_Models_Pages();
    		
    		// Loop through rank
    		foreach($arrParams['rank'] as $intRankKey => $strRankData) {
    			
    			$intPageContentID	= str_replace('content_', '', $strRankData);
    			$intUpdateID 		= $objModelPage->updatePagesContentRank($intPageContentID, ($intRankKey + 1));
    			
    		}

    	}

	}

	public function contentbackupAction()
	{

		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage				= new KZ_Models_Pages();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
		// Get Page Content
    	$arrPageContent				= $objModelPage->getPageContentByID($arrParams['id']);
    	
		// Check if Page was found
    	if(! isset($arrPageContent) || ! is_array($arrPageContent) || count($arrPageContent) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find page content')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get Page Content Backups by ID
    	$arrPageContentBackup		= $objModelPage->getPageContentBackups($arrPageContent['page_content_id']);
    	
    	// Get Page
    	$arrPage					= $objModelPage->getPageByID($arrPageContent['page_id']);

    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('pages','content_type_id');
    	
    	// Parse Data to View
    	$this->view->page					= $arrPage;
    	$this->view->page_content_backups	= $arrPageContentBackup;
    	$this->view->contentTypes 			= $arrContentTypes;

	}
	
	public function contentbackupshowAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage				= new KZ_Models_Pages();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Page Content Backup by ID
    	$arrPageContentBackup		= $objModelPage->getPageBackupContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('pages','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrPageContentBackup['content_type_id'];
    	$strName					= $arrPageContentBackup['name'];
    	$intStatus					= $arrPageContentBackup['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrPageContentBackup['data']);
		
		if($intContentTypeID == 1) {
			$strContent1Title			= $arrData['content_1_title'];
    		$strContent1Text			= $arrData['content_1_text'];
		}
		
		if($intContentTypeID == 2) {
			$strContent2Title			= $arrData['content_2_title'];
			$strContent2Image			= $arrData['content_2_image'];
    		$strContent2Text			= $arrData['content_2_text'];
		}
		
		if($intContentTypeID == 3) {
			$strContent3Video			= $arrData['content_3_video'];
		}
    	
    	// Parse Data to View
    	$this->view->page_content_backup	= $arrPageContentBackup;
    	$this->view->contentTypes 			= $arrContentTypes;
    	
    	$this->view->content_type_id 		= $intContentTypeID;
    	$this->view->name 					= $strName;
    	$this->view->status			 		= $intStatus;
    	
    	$this->view->content_1_title		= $strContent1Title;
    	$this->view->content_1_text			= $strContent1Text;
    	
    	$this->view->content_2_title		= $strContent2Title;
    	$this->view->content_2_image		= $strContent2Image;
    	$this->view->content_2_text			= $strContent2Text;

    	$this->view->content_3_video		= $strContent3Video;
	}
	
	public function contentrestoreAction()
	{
		// Get All Params
		$arrParams 		= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelPage			= new KZ_Models_Pages();
    	
    	// Get Page Backup By ID
    	$arrPageContentBackup	= $objModelPage->getPageBackupContentByID($arrParams['id']);
		
    	// Set Page Content Update array
    	$arrPageContentUpdate	= array(
    		'content_type_id'	=> $arrPageContentBackup['content_type_id'],
	    	'name'				=> $arrPageContentBackup['name'],
    		'data'				=> $arrPageContentBackup['data'],
    		'status'			=> $arrPageContentBackup['status'],
    		'user_id'			=> KZ_Controller_Session::getActiveUser()
    	);
    	
		// Update Page Content
		$intUpdateID			= $objModelPage->updatePageContent($arrPageContentBackup['page_content_id'], $arrPageContentUpdate);
    	
		// Check if update was succesfull
		if(is_numeric($intUpdateID)) {
			$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully restored backup')));
			$this->_redirect('/admin/pages/content/id/'.$arrPageContentBackup['page_id'].'/feedback/'.$strFeedback.'/');
		} else {
			// Return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Something went wrong trying to restore the backup')));
			$this->_redirect('/admin/pages/content/id/'.$arrPageContentBackup['page_id'].'/feedback/'.$strFeedback.'/');
		}
    	
	}
	
	public function mailingsAction()
	{

	}

	public function getmenuurlAction()
	{
		// Disable Layout and View
		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// Get All Params
    	$arrParams = $this->_getAllParams();
    	
    	// Check if rel was set and not empty
    	if(isset($arrParams['name']) && ! empty($arrParams['name']))
    	{
    		echo KZ_Controller_Action_Helper_Slug::slug($arrParams['name']);
    		exit;
    	}

    	echo '';
	}

	public function autocompleteAction()
	{
		// Disable Layout and View
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Set Pages Model
		$objModelPages      = new KZ_Models_Pages();

		// Get All Pages
		$arrPages           = $objModelPages->getPages();

		// Set Default Autocomplete array
		$arrAutocomplete    = array();

		// Loop through pages
		if(! empty($arrPages) && is_array($arrPages)) {
			foreach($arrPages as $arrPage) {
				$arrAutocomplete[]  = array('label' => $arrPage['name'], 'value' => $arrPage['page_id']);
			}
		}

		echo json_encode($arrAutocomplete);

	}

}