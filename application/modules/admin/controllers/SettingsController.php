<?php
class Admin_SettingsController extends KZ_Controller_Action
{
	
	public $arrColors;
	public $arrPages;
	public $arrNews;
	
	public function init() {
	
		// Set Models
		$objModelColors		= new KZ_Models_Colors();
		$objModelPage		= new KZ_Models_Pages();
		$objModelNews		= new KZ_Models_News();
		$objModelAgenda		= new KZ_Models_Agenda();
		
		// Get Colors That Are not only widget colors
		$strWidgetOnly 		= 'n';
		
		// Get All Colors
		$this->arrColors 	= $objModelColors->getColors('code', $strWidgetOnly);
	
		$this->view->colors	= $this->arrColors;
		
		// Get All Standalone pages, ordered by rank
		$arrStandalonePages			= $objModelPage->getPages(0, false, 'rank');
				
		// Get All Main pages, ordered by rank
		$arrMainPages				= $objModelPage->getPages(1, false, 'rank');
		
		// Get All Sub Pages, ordered by rank
		$arrSubPages				= $objModelPage->getPages(2, false, 'rank', 'parent_id');
		
		// Get All Sub Sub Pages, ordered by rank
		$arrSubSubPages				= $objModelPage->getPages(3, false, 'rank', 'parent_id');
	
		// Set Default Ordered Pages array
		$arrOrderedPages = array();
		
		// Loop through Main Pages
		foreach($arrMainPages as $intPageKey => $arrPage) {
			
			// Skip inactive Main Pages
			if($arrPage['status'] == 0) {
				continue;
			}
			
			if(isset($arrSubPages[$arrPage['page_id']]) && is_array($arrSubPages[$arrPage['page_id']])) {
				$arrPage['hasSubPages'] = true;
			}
			
			$strMenuType 	= (($arrPage['menu_type_id'] == 1) ? 'Main' : 'Sub');
				
			// Add Page To Ordered Pages
			$arrOrderedPages[$strMenuType][] 		= $arrPage;
				
			// Check if Sub Pages where found
			if(isset($arrSubPages[$arrPage['page_id']]) && is_array($arrSubPages[$arrPage['page_id']])) {
		
				// Loop through Sub Pages
				foreach($arrSubPages[$arrPage['page_id']] as $intSubPageKey => $arrSubPage) {
					
					// Skip inactive Sub Pages
					if($arrSubPage['status'] == 0) {
						continue;
					}
						
					if(isset($arrSubSubPages[$arrSubPage['page_id']]) && is_array($arrSubSubPages[$arrSubPage['page_id']])) {
						$arrSubPage['hasSubSubPages'] = true;
					}
						
					// Add Sub Page To Ordered Pages
					$arrOrderedPages[$strMenuType][]	= $arrSubPage;
						
					// Check if Sub Sub Pages where found
					if(isset($arrSubSubPages[$arrSubPage['page_id']]) && is_array($arrSubSubPages[$arrSubPage['page_id']])) {
		
						// Loop through Sub Sub Pages
						foreach($arrSubSubPages[$arrSubPage['page_id']] as $intSubSubPageKey => $arrSubSubPage) {
							
							// Skip inactive Sub Sub Pages
							if($arrSubSubPage['status'] == 0) {
								continue;
							}
							
							// Add Sub Sub Page To Ordered Pages
							$arrOrderedPages[$strMenuType][]	= $arrSubSubPage;
		
						}
		
					}
						
				}
		
			}
				
		}
		
		$arrOrderedPages['Standalone']	= $arrStandalonePages;
		
		$this->view->pages = $arrOrderedPages;
		
		// Set Date Object
		$objDate			= new Zend_Date();
		$intYear			= $objDate->toString(Zend_Date::YEAR);
		$strDate			= $objDate->toString('YYYY-MM-dd');
		
		// Get News
		$arrNews			= $objModelNews->getNews(false, false, 1, $intYear);
		$this->view->news	= $arrNews;
		
		// Get Agenda
		$arrAgenda			= $objModelAgenda->getAgenda($strDate, 1);
		$this->view->agenda	= $arrAgenda;
		
		
	}
	
	public function categoriesAction()
	{
		// Set Models
		$objModelCategories		= new KZ_Models_Categories();
		
		// Get All Categories
		$arrCategories			= $objModelCategories->getCategories();
		
		// Set Default Variables
		$strName	= '';
		$strColor	= '';
		$intStatus	= 0;

		// Check for Post
		if($this->getRequest()->isPost()) {
			
			// Set Post Params
			$arrPostParams	= $this->_getAllParams();
			
			// Set Post Variables
			$strName	= $arrPostParams['name'];
			$strColor	= $arrPostParams['color'];
			$intStatus	= $arrPostParams['status'];
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strColor) || strlen($strColor) != 7) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a color');
			} else {
				
				// Set Update array
				$arrAddCategory	= array(
					'name'		=> $strName,
					'color'		=> $strColor,
					'status'	=> $intStatus
				);
				
				// Add new Category
				$intInsertID	= $objModelCategories->addCategory($arrAddCategory);
				
				if(isset($intInsertID) && is_numeric($intInsertID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added category')));
					$this->_redirect('/admin/settings/categories/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the category');
				}
				
			}

		}
		
		// Parse Variables to view
		$this->view->categories 	= $arrCategories;
		$this->view->name			= $strName;
		$this->view->color			= $strColor;
		$this->view->status 		= $intStatus;
		
	}
	
	public function categorieseditAction()
	{
		
		// Get All Params
		$arrParams	= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/settings/categories/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelCategories	= new KZ_Models_Categories();
    	
    	// Get Category by ID
    	$arrCategory		= $objModelCategories->getCategory($arrParams['id']);
    	
		// Check if category was found
    	if(! isset($arrCategory) || ! is_array($arrCategory) || count($arrCategory) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find category')));
    		$this->_redirect('/admin/settings/categories/feedback/'.$strFeedback.'/#overview/');
    	}
    	
    	// Set Default Variables
		$strName				= $arrCategory['name'];
		$strColor				= $arrCategory['color'];
		$intStatus				= $arrCategory['status'];
		
		if($this->getRequest()->isPost()) {
			
			// Set Post Params
			$arrPostParams	= $this->_getAllParams();
			
			// Set Post Variables
			$strName	= $arrPostParams['name'];
			$strColor	= $arrPostParams['color'];
			$intStatus	= $arrPostParams['status'];
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strColor) || strlen($strColor) != 7) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a color');
			} else {
				
				// Set Update array
				$arrUpdateCategory	= array(
					'name'		=> $strName,
					'color'		=> $strColor,
					'status'	=> $intStatus
				);
				
				// Update Category
				$intUpdateID	= $objModelCategories->updateCategory($arrParams['id'], $arrUpdateCategory);
				
				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated category')));
					$this->_redirect('/admin/settings/categories/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the category');
				}
				
			}
			
		}
    	
    	// Parse Variables to view
    	$this->view->name		= $strName;
    	$this->view->color		= $strColor;
    	$this->view->status		= $intStatus;
		
	}
	
	public function categoriesdeleteAction()
	{
		
		// Get All Params
		$arrParams	= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/settings/categories/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelCategories	= new KZ_Models_Categories();
    	
    	// Get Category by ID
    	$arrCategory		= $objModelCategories->getCategory($arrParams['id']);
    	
		// Check if category was found
    	if(! isset($arrCategory) || ! is_array($arrCategory) || count($arrCategory) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find category')));
    		$this->_redirect('/admin/settings/categories/feedback/'.$strFeedback.'/#tab0/');
    	}
		
		if($this->getRequest()->isPost()) {
			
			// Delete Category
			$intDeleteID	= $objModelCategories->deleteCategory($arrParams['id']);
			
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted category')));
				$this->_redirect('/admin/settings/categories/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the category');
			}
				
		}
    	
    	// Parse Variables to view
    	$this->view->name			= $arrCategory['name'];
    	$this->view->color			= $arrCategory['color'];
    	$this->view->status			= $arrCategory['status'];
    	$this->view->created		= $arrCategory['created'];
    	$this->view->lastmodified	= $arrCategory['lastmodified'];
		
	}
	
	public function tagsAction()
	{
		// Set Models
		$objModelTags			= new KZ_Models_Tags();
		
		// Get All Tags
		$arrTags				= $objModelTags->getTags();
		
		// Set Default Variables
		$strName	= '';
		$intStatus	= 0;

		// Check for Post
		if($this->getRequest()->isPost()) {
			
			// Set Post Params
			$arrPostParams	= $this->_getAllParams();
			
			// Set Post Variables
			$strName	= $arrPostParams['name'];
			$intStatus	= $arrPostParams['status'];
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else {
				
				// Set Update array
				$arrAddTag	= array(
					'name'		=> $strName,
					'status'	=> $intStatus
				);
				
				// Add new Tag
				$intInsertID	= $objModelTags->addTag($arrAddTag);
				
				if(isset($intInsertID) && is_numeric($intInsertID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added tag')));
					$this->_redirect('/admin/settings/tags/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the tag');
				}
				
			}

		}
		
		// Parse Variables to view
		$this->view->tags 		= $arrTags;
		$this->view->name		= $strName;
		$this->view->status 	= $intStatus;
		
	}
	
	public function tagseditAction()
	{
		
		// Get All Params
		$arrParams	= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/settings/tags/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelTags		= new KZ_Models_Tags();
    	
    	// Get Tag by ID
    	$arrTag				= $objModelTags->getTag($arrParams['id']);
    	
		// Check if category was found
    	if(! isset($arrTag) || ! is_array($arrTag) || count($arrTag) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find tag')));
    		$this->_redirect('/admin/settings/tags/feedback/'.$strFeedback.'/#overview/');
    	}
    	
    	// Set Default Variables
		$strName				= $arrTag['name'];
		$intStatus				= $arrTag['status'];
		
		if($this->getRequest()->isPost()) {
			
			// Set Post Params
			$arrPostParams	= $this->_getAllParams();
			
			// Set Post Variables
			$strName	= $arrPostParams['name'];
			$intStatus	= $arrPostParams['status'];
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else {
				
				// Set Update array
				$arrUpdateTag	= array(
					'name'		=> $strName,
					'status'	=> $intStatus
				);
				
				// Update Tag
				$intUpdateID	= $objModelTags->updateTag($arrParams['id'], $arrUpdateTag);
				
				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated tag')));
					$this->_redirect('/admin/settings/tags/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the tag');
				}
				
			}
			
		}
    	
    	// Parse Variables to view
    	$this->view->name		= $strName;
    	$this->view->status		= $intStatus;
		
	}
	
	public function tagsdeleteAction()
	{
		
		// Get All Params
		$arrParams	= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/settings/tags/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelTags		= new KZ_Models_Tags();
    	
    	// Get Tag by ID
    	$arrTag				= $objModelTags->getTag($arrParams['id']);
    	
		// Check if category was found
    	if(! isset($arrTag) || ! is_array($arrTag) || count($arrTag) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find tag')));
    		$this->_redirect('/admin/settings/tags/feedback/'.$strFeedback.'/#tab0/');
    	}
		
		if($this->getRequest()->isPost()) {
			
			// Delete Tag
			$intDeleteID	= $objModelTags->deleteTag($arrParams['id']);
			
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted tag')));
				$this->_redirect('/admin/settings/tags/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the tag');
			}
				
		}
    	
    	// Parse Variables to view
    	$this->view->name			= $arrTag['name'];
    	$this->view->status			= $arrTag['status'];
    	$this->view->created		= $arrTag['created'];
    	$this->view->lastmodified	= $arrTag['lastmodified'];
		
	}
	
	public function topstoryAction()
	{
		// Set Models
		$objModelPage				= new KZ_Models_Pages();
		$objModelMenu				= new KZ_Models_Menu();
		$objModelUsers				= new KZ_Models_Users();
		$objModelSettings			= new KZ_Models_Settings();
		
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
		
		// Get Current Setting by Key
		$arrSettings				= $objModelSettings->getSettingsByKey('topstory');
		
		// Set Default SettingsID
		$intTopstoryID				= 0;
		
		if(isset($arrSettings) && is_array($arrSettings) && count($arrSettings) > 0) {
			$intTopstoryID			= $arrSettings['value'];
		}
				
		// Set Default Ordered Pages array
		$arrOrderedPages = array();
		
		// Loop through Main Pages
		foreach($arrMainPages as $intPageKey => $arrPage) {
			
			if(isset($arrSubPages[$arrPage['page_id']]) && is_array($arrSubPages[$arrPage['page_id']])) {
				$arrPage['hasSubPages'] = true;
			}
			
			// Add Page To Ordered Pages
			$arrOrderedPages[$arrPage['menu_type_id']][] 		= $arrPage;
			
			// Check if Sub Pages where found
			if(isset($arrSubPages[$arrPage['page_id']]) && is_array($arrSubPages[$arrPage['page_id']])) {
				
				// Loop through Sub Pages
				foreach($arrSubPages[$arrPage['page_id']] as $intSubPageKey => $arrSubPage) {
					
					if(isset($arrSubSubPages[$arrSubPage['page_id']]) && is_array($arrSubSubPages[$arrSubPage['page_id']])) {
						$arrSubPage['hasSubSubPages'] = true;
					}
					
					// Add Sub Page To Ordered Pages
					$arrOrderedPages[$arrPage['menu_type_id']][]	= $arrSubPage;
					
					// Check if Sub Sub Pages where found
					if(isset($arrSubSubPages[$arrSubPage['page_id']]) && is_array($arrSubSubPages[$arrSubPage['page_id']])) {

						// Loop through Sub Sub Pages
						foreach($arrSubSubPages[$arrSubPage['page_id']] as $intSubSubPageKey => $arrSubSubPage) {

							// Add Sub Sub Page To Ordered Pages
							$arrOrderedPages[$arrPage['menu_type_id']][]	= $arrSubSubPage;

						}
						
					}
					
				}
				
			}
			
		}
		
		// Parse Variables to View
		$this->view->pages 				= $arrOrderedPages;
		$this->view->standalonePages	= $arrStandalonePages;
		$this->view->menuTypes			= $arrMenuTypes;
		$this->view->topstoryID			= $intTopstoryID;

	}
	
	public function updatetopstoryAction()
	{
		// Disable layout and view 
		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// Get All Params
    	$arrParams		= $this->_getAllParams();
    	
    	// Check if category ID was set
    	if(isset($arrParams['id']) && ! empty($arrParams['id']) && is_numeric($arrParams['id'])) {
    		
    		// Set Models
    		$objModelSettings	= new KZ_Models_Settings();
    		
    		// Update Settings
    		$intUpdateID		= $objModelSettings->updateSettingsByKey('topstory', array(
    			'value' => (int)$arrParams['id']
    		));
    		
    		echo $intUpdateID;
    		exit;

    	}
    	
    	echo Zend_Debug::dump($arrParams);
    	exit;
    	
	}
	
	public function newscategoriesAction()
	{
		// Set Models
		$objModelNews			= new KZ_Models_News();
		
		// Get News Categories
		$arrNewsCategories		= $objModelNews->getNewsCategories();
		
		// Set Defaults
		$arrDefaults = array(
			'title'					=> '',
			'foreign_table'			=> '',
			'foreign_key_page'		=> '',
			'foreign_key_news'		=> '',
			'foreign_key_agenda'	=> '',
			'foreign_key_other'		=> '',
			'link'					=> '',
			'link_target'			=> '_self',
			'color_bg'				=> '',
			'color_text'			=> '',
			'status'				=> ''
		);
		
		if($this->getRequest()->isPost()) {
			
			// Get All Params
			$arrPostParams		= $this->_getAllParams();
			
			// Set Selected foreign key
			if($arrPostParams['foreign_table'] == 'other') {
				$arrPostParams['foreign_key'] 	= 0;
			} else {
				$arrPostParams['foreign_key']	= $arrPostParams['foreign_key_'.$arrPostParams['foreign_table']];
			}
			
			if(empty($arrPostParams['title'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else {
				
				// Unset Data from Update Array
				unset($arrPostParams['controller']);
				unset($arrPostParams['action']);
				unset($arrPostParams['module']);
				unset($arrPostParams['formAction']);
				unset($arrPostParams['feedback']);
				
				// Unset unnessecary foreign keys
				unset($arrPostParams['foreign_key_page']);
				unset($arrPostParams['foreign_key_news']);
				unset($arrPostParams['foreign_key_agenda']);
				unset($arrPostParams['foreign_key_other']);
				
				$intInsertID	= $objModelNews->addNewsCategory($arrPostParams);
				
				if(isset($intInsertID) && is_numeric($intInsertID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added news category')));
					$this->_redirect('/admin/settings/newscategories/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the news category');
				}
				
			}
			
			// Overwrite Defaults by Post Variables
			$arrDefaults		= $arrPostParams;
		}
		
		// Parse variables to view
		$this->view->defaults			= $arrDefaults;
		$this->view->news_categories	= $arrNewsCategories;
	}
	
	public function newscategorieseditAction()
	{
		// Get Params
		$arrParams		= $this->_getAllParams();
		
		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/settings/newscategories/feedback/'.$strSerializedFeedback.'/#tab0/');
		}
		 
		// Set Models
		$objModelNews		= new KZ_Models_News();
		 
		// Get News Category by ID
		$arrNewsCategory	= $objModelNews->getNewsCategory($arrParams['id']);
		 
		// Check if News Category was found
		if(! isset($arrNewsCategory) || ! is_array($arrNewsCategory) || count($arrNewsCategory) == 0) {
			// return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find news category')));
			$this->_redirect('/admin/settings/newscategories/feedback/'.$strFeedback.'/#tab0/');
		}
		
		// Set Defaults
		$arrDefaults 	= $arrNewsCategory;
		
		if($this->getRequest()->isPost()) {
				
			// Get All Params
			$arrPostParams		= $this->_getAllParams();
				
			// Set Selected foreign key
			if($arrPostParams['foreign_table'] == 'other') {
				$arrPostParams['foreign_key'] 	= 0;
			} else {
				$arrPostParams['foreign_key']	= $arrPostParams['foreign_key_'.$arrPostParams['foreign_table']];
			}
				
			if(empty($arrPostParams['title'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else {
		
				// Unset Data from Update Array
				unset($arrPostParams['controller']);
				unset($arrPostParams['action']);
				unset($arrPostParams['module']);
				unset($arrPostParams['formAction']);
				unset($arrPostParams['id']);
				unset($arrPostParams['feedback']);
		
				// Unset unnessecary foreign keys
				unset($arrPostParams['foreign_key_page']);
				unset($arrPostParams['foreign_key_news']);
				unset($arrPostParams['foreign_key_agenda']);
				unset($arrPostParams['foreign_key_other']);
		
				$intUpdateID	= $objModelNews->updateNewsCategory($arrParams['id'],$arrPostParams);
		
				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated news category')));
					$this->_redirect('/admin/settings/newscategories/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
					$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the news category');
				}
		
			}
				
			// Overwrite Defaults by Post Variables
			$arrDefaults		= $arrPostParams;
		}
		
		// Parse variables to view
		$this->view->defaults			= $arrDefaults;
	}
	
	public function newscategoriesdeleteAction()
	{
		// Get Params
		$arrParams		= $this->_getAllParams();
	
		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/settings/newscategories/feedback/'.$strSerializedFeedback.'/#tab0/');
		}
			
		// Set Models
		$objModelNews		= new KZ_Models_News();
			
		// Get News Category by ID
		$arrNewsCategory	= $objModelNews->getNewsCategory($arrParams['id']);
			
		// Check if News Category was found
		if(! isset($arrNewsCategory) || ! is_array($arrNewsCategory) || count($arrNewsCategory) == 0) {
			// return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find news category')));
			$this->_redirect('/admin/settings/newscategories/feedback/'.$strFeedback.'/#tab0/');
		}
	
		// Set Defaults
		$arrDefaults 	= $arrNewsCategory;
	
		if($this->getRequest()->isPost()) {
	
			$intDeleteID	= $objModelNews->deleteNewsCategory($arrParams['id']);

			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted news category')));
				$this->_redirect('/admin/settings/newscategories/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the news category');
			}

		}
	
		// Parse variables to view
		$this->view->defaults			= $arrDefaults;
	}
	
}