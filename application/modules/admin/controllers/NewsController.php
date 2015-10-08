<?php
class Admin_NewsController extends KZ_Controller_Action
{

	public function indexAction()
	{
		// Get Models
		$objModelNews				= new KZ_Models_News();
		$objModelCategories			= new KZ_Models_Categories();
		$objModelTags				= new KZ_Models_Tags();
		$objModelPages				= new KZ_Models_Pages();
		$objModelUsers				= new KZ_Models_Users();
		$objModelWidgets			= new KZ_Models_Widgets();
		$objModelMenu				= new KZ_Models_Menu();
		
		// Set Date Object
		$objDate					= new Zend_Date();
		
		// Get All News items
		$arrNews					= $objModelNews->getNews();
		
		// Get All News Types
		$arrNewsTypes				= $objModelNews->getNewsTypes('news_type_id');
		
		// Get All Categories
		$arrCategories				= $objModelCategories->getCategories();
		
		// Get All Tags
		$arrTags					= $objModelTags->getTags('tag_id');
		
		// Get All Standalone pages, ordered by rank
		$arrStandalonePages			= $objModelPages->getPages(0, false, 'rank');
		
		// Get All Main pages, ordered by rank
		$arrMainPages				= $objModelPages->getPages(1, false, 'rank');
		
		// Get All Sub Pages, ordered by rank
		$arrSubPages				= $objModelPages->getPages(2, false, 'rank', 'parent_id');
		
		// Get All Sub Sub Pages, ordered by rank
		$arrSubSubPages				= $objModelPages->getPages(3, false, 'rank', 'parent_id');
		
		// Get Menu Types
		$arrMenuTypes				= $objModelMenu->getMenuTypes('menu_type_id');
		
		// Set Default Ordered Pages array
		$arrOrderedPages = array();
		
		// Loop through Main Pages
		foreach($arrMainPages as $intPageKey => $arrPage) {
			// Only add active pages
			if($arrPage['status'] == 1) {
				$arrOrderedPages[$arrPage['menu_type_id']][$arrPage['page_id']] = $arrPage;
			}
		}
		
		// Loop through Sub Pages
		foreach($arrSubPages as $intMainPageID => $arrSubPages) {
			
			foreach($arrSubPages as $intSubPageKey => $arrSubPage) {
				// Only add active pages
				if($arrSubPage['status'] == 1) {
					$arrOrderedPages[$arrSubPage['menu_type_id']][$intMainPageID]['submenu'][$arrSubPage['page_id']] = $arrSubPage;
				}
			}
		}
		
		// Set Default Variables
		$intNewsTypeID				= 0;
		$intCategoryID				= 0;
		$strName					= '';
		$strImage					= '';
		$strDate					= $objDate->toString('dd-MM-YYYY');
		$intTime					= $objDate->toString('h');
		$strSeoTitle				= '';
		$strSeoKeywords				= '';
		$strSeoDescription			= '';
		$intWidgetLayout			= 0;
		$intPageID					= 0;
		$intStatus					= 0;
		$arrActiveTags				= array();
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams			= $this->_getAllParams();
			
			// Set Post Variables
			$intNewsTypeID			= $arrPostParams['news_type_id'];
			$intCategoryID			= $arrPostParams['category_id'];
			$strName				= $arrPostParams['name'];
			$strImage				= $arrPostParams['image'];
			$strDate				= $arrPostParams['date'];
			$intTime				= $arrPostParams['time'];
			$strSeoTitle			= $arrPostParams['seo_title'];
			$strSeoKeywords			= $arrPostParams['seo_keywords'];
			$strSeoDescription		= $arrPostParams['seo_description'];
			$intWidgetLayout		= $arrPostParams['widget_layout'];
			$intPageID				= $arrPostParams['page_id'];
			$intStatus				= $arrPostParams['status'];
			
			// Loop through Post for selected Tags
			foreach($arrPostParams as $strPostKey => $strPostValue) {
				// Check if tag was found
				if(strstr($strPostKey, 'tag-')) {
					// Add Tag to active Tags array
					$arrActiveTags[] = $strPostValue;
				}
			}
			
			if(empty($intNewsTypeID)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a type');
			} elseif(empty($intCategoryID)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a category');
			} elseif(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strImage)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
			} else {

                // Resultset of matching news items with the same slug
                $arrMatchingNewsBySlug = $objModelNews->getMatchingNewsBySlug(KZ_Controller_Action_Helper_Slug::slug($strName),'news_id');

                // Check if News items where found
                if(empty($arrMatchingNewsBySlug) && is_array($arrMatchingNewsBySlug)) {

                    $arrNewsData = array(
                        'news_type_id'		=> $intNewsTypeID,
                        'category_id'		=> $intCategoryID,
                        'tags'				=> implode(',', $arrActiveTags),
                        'name' 				=> $strName,
                        'nameSlug'			=> KZ_Controller_Action_Helper_Slug::slug($strName),
                        'image' 			=> str_replace(ROOT_URL.'/upload/', '', $strImage),
                        'date'				=> KZ_View_Helper_Date::format($strDate, 'YYYY-MM-dd'),
                        'time'				=> $intTime,
                        'seo_title'			=> $strSeoTitle,
                        'seo_description'	=> $strSeoDescription,
                        'seo_keywords'		=> $strSeoKeywords,
                        'widget_layout'		=> (($intWidgetLayout == 0) ? NULL : $intWidgetLayout),
                        'page_id'			=> (($intPageID == 0) ? NULL : $intPageID),
                        'status'			=> $intStatus,
                        'user_id'			=> KZ_Controller_Session::getActiveUser()
                    );

                    $intInsertID		= $objModelNews->addNews($arrNewsData);

                    if(isset($intInsertID) && is_numeric($intInsertID)) {
                        $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added news')));
                        $this->_redirect('/admin/news/content/id/'.$intInsertID.'/feedback/'.$strFeedback.'/#tab1');
                    } else {
                        // Return feedback
                        $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the news');
                    }
                }
                else
                {
                    $this->view->feedback = array('type' => 'error', 'message' => 'A newsitem with this URL already exists.');
                }

			}

		}

		// Set Widget Layout Status
		$intWidgetLayoutStatus			= 1; // Active Widget Layouts only

		// Get All Widget Layouts for status
		$arrWidgetLayouts				= $objModelWidgets->getWidgetLayouts($intWidgetLayoutStatus);

		// Parse Variables to View
		$this->view->news 				= $arrNews;
		$this->view->newsTypes			= $arrNewsTypes;
		$this->view->categories 		= $arrCategories;
		$this->view->tags				= $arrTags;
		$this->view->widgetLayouts		= $arrWidgetLayouts;

		$this->view->pages				= $arrOrderedPages;
		$this->view->standalonePages	= $arrStandalonePages;
		$this->view->menu_types			= $arrMenuTypes;

		$this->view->news_type_id		= $intNewsTypeID;
		$this->view->category_id		= $intCategoryID;
		$this->view->active_tags		= $arrActiveTags;
		$this->view->name				= $strName;
		$this->view->image				= $strImage;
		$this->view->date				= $strDate;
		$this->view->time				= $intTime;
		$this->view->seo_title			= $strSeoTitle;
		$this->view->seo_description	= $strSeoDescription;
		$this->view->seo_keywords		= $strSeoKeywords;
		$this->view->widget_layout		= $intWidgetLayout;
		$this->view->page_id			= $intPageID;
		$this->view->status				= $intStatus;

		$this->view->editorInit			= KZ_Controller_Editor::setEditor('tinymce');

	}

	public function editAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();

		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}

    	// Set Models
    	$objModelNews				= new KZ_Models_News();
    	$objModelCategories 		= new KZ_Models_Categories();
    	$objModelTags				= new KZ_Models_Tags();
    	$objModelWidgets			= new KZ_Models_Widgets();
    	$objModelPage				= new KZ_Models_Pages();
    	$objModelMenu				= new KZ_Models_Menu();

    	// Get News
    	$arrNews					= $objModelNews->getNewsByID($arrParams['id']);

    	// Check if News wasn't found
    	if(isset($arrNews) && count($arrNews) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find news')));
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}

    	// Get All News Types
		$arrNewsTypes				= $objModelNews->getNewsTypes('news_type_id');

		// Get All Categories
		$arrCategories				= $objModelCategories->getCategories();

		// Get All Tags
		$arrTags					= $objModelTags->getTags('tag_id');

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

		// Set Default Ordered Pages array
		$arrOrderedPages = array();

		// Loop through Main Pages
		foreach($arrMainPages as $intPageKey => $arrPage) {
			// Only add active pages
			if($arrPage['status'] == 1) {
				$arrOrderedPages[$arrPage['menu_type_id']][$arrPage['page_id']] = $arrPage;
			}
		}

		// Loop through Sub Pages
		foreach($arrSubPages as $intMainPageID => $arrSubPages) {

			foreach($arrSubPages as $intSubPageKey => $arrSubPage) {
				// Only add active pages
				if($arrSubPage['status'] == 1) {
					$arrOrderedPages[$arrSubPage['menu_type_id']][$intMainPageID]['submenu'][$arrSubPage['page_id']] = $arrSubPage;
				}
			}
		}

		// Set Default Variables
		$intNewsTypeID				= $arrNews['news_type_id'];
		$intCategoryID				= $arrNews['category_id'];
		$strName					= $arrNews['name'];
		$strImage					= $arrNews['image'];
		$strDate					= $arrNews['date'];
		$intTime					= $arrNews['time'];
		$strSeoTitle				= $arrNews['seo_title'];
		$strSeoKeywords				= $arrNews['seo_keywords'];
		$strSeoDescription			= $arrNews['seo_description'];
		$intWidgetLayout			= $arrNews['widget_layout'];
		$intPageID					= $arrNews['page_id'];
		$intStatus					= $arrNews['status'];

		$arrActiveTags				= ((! empty($arrNews['tags'])) ? explode(',', $arrNews['tags']) : array());

		// Check if Post was set
		if($this->getRequest()->isPost()) {

			$arrActiveTags 			= array();

			// Get All Params
			$arrPostParams			= $this->_getAllParams();

			// Set Post Variables
			$intNewsTypeID			= $arrPostParams['news_type_id'];
			$intCategoryID			= $arrPostParams['category_id'];
			$strName				= $arrPostParams['name'];
			$strImage				= $arrPostParams['image'];
			$strDate				= $arrPostParams['date'];
			$intTime				= $arrPostParams['time'];
			$strSeoTitle			= $arrPostParams['seo_title'];
			$strSeoKeywords			= $arrPostParams['seo_keywords'];
			$strSeoDescription		= $arrPostParams['seo_description'];
			$intWidgetLayout		= $arrPostParams['widget_layout'];
			$intPageID				= $arrPostParams['page_id'];
			$intStatus				= $arrPostParams['status'];

			// Loop through Post for selected Tags
			foreach($arrPostParams as $strPostKey => $strPostValue) {
				// Check if tag was found
				if(strstr($strPostKey, 'tag-')) {
					// Add Tag to active Tags array
					$arrActiveTags[] = $strPostValue;
				}
			}

			if(empty($intNewsTypeID)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a type');
			} elseif(empty($intCategoryID)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a category');
			} elseif(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strImage)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
			} else {

                // Resultset of matching news items with the same slug
                $arrMatchingNewsBySlug = $objModelNews->getMatchingNewsBySlug(KZ_Controller_Action_Helper_Slug::slug($strName),'news_id');

                // Remove Current News items From Edit
                if(array_key_exists($arrParams['id'], $arrMatchingNewsBySlug)) {
                    // Remove Current News item From Matching News by Slug
                    unset($arrMatchingNewsBySlug[$arrParams['id']]);
                }

                // Check if News items where found
                if(empty($arrMatchingNewsBySlug) && is_array($arrMatchingNewsBySlug)) {

                    $arrNewsData = array(
                        'news_type_id'		=> $intNewsTypeID,
                        'category_id'		=> $intCategoryID,
                        'tags'				=> implode(',', $arrActiveTags),
                        'name' 				=> $strName,
                        'nameSlug'			=> KZ_Controller_Action_Helper_Slug::slug($strName),
                        'image' 			=> str_replace(ROOT_URL.'/upload/', '', $strImage),
                        'date'				=> KZ_View_Helper_Date::format($strDate, 'YYYY-MM-dd'),
                        'time'				=> $intTime,
                        'seo_title'			=> $strSeoTitle,
                        'seo_description'	=> $strSeoDescription,
                        'seo_keywords'		=> $strSeoKeywords,
                        'widget_layout'		=> (($intWidgetLayout == 0) ? NULL : $intWidgetLayout),
                        'page_id'			=> (($intPageID == 0) ? NULL : $intPageID),
                        'status'			=> $intStatus,
                        'user_id'			=> KZ_Controller_Session::getActiveUser()
                    );

                    $intUpdateID		= $objModelNews->updateNews($arrNews['news_id'], $arrNewsData);

                    if(isset($intUpdateID) && is_numeric($intUpdateID)) {
                        $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated news')));
                        $this->_redirect('/admin/news/index/feedback/'.$strFeedback.'/#tab0');
                    } else {
                        // Return feedback
                        $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the news');
                    }
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'A newsitem with this url already exists');
                }

			}
		
		}
		
		// Set Widget Layout Status for status
		$intWidgetLayoutStatus			= 1; // Active Widget Layouts only
		
		// Get All Widget Layouts
		$arrWidgetLayouts				= $objModelWidgets->getWidgetLayouts($intWidgetLayoutStatus);
		
		// Parse Variables to View
		$this->view->news 				= $arrNews;
		$this->view->newsTypes			= $arrNewsTypes;
		$this->view->categories 		= $arrCategories;
		$this->view->tags				= $arrTags;
		$this->view->widgetLayouts		= $arrWidgetLayouts;
		
		$this->view->pages				= $arrOrderedPages;
		$this->view->standalonePages	= $arrStandalonePages;
		$this->view->menu_types			= $arrMenuTypes;

		$this->view->news_type_id		= $intNewsTypeID;
		$this->view->category_id		= $intCategoryID;
		$this->view->active_tags		= $arrActiveTags;
		$this->view->name				= $strName;
		$this->view->image				= $strImage;
		$this->view->date				= KZ_View_Helper_Date::format($strDate, 'dd-MM-YYYY');
		$this->view->time				= $intTime;
		$this->view->seo_title			= $strSeoTitle;
		$this->view->seo_description	= $strSeoDescription;
		$this->view->seo_keywords		= $strSeoKeywords;
		$this->view->widget_layout		= $intWidgetLayout;
		$this->view->page_id			= $intPageID;
		$this->view->status				= $intStatus;
		
		$this->view->editorInit			= KZ_Controller_Editor::setEditor('tinymce');
    	
	}
	
	public function deleteAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelNews				= new KZ_Models_News();
    	$objModelCategories 		= new KZ_Models_Categories();
    	$objModelTags				= new KZ_Models_Tags();
    	
    	// Get News
    	$arrNews					= $objModelNews->getNewsByID($arrParams['id']);

    	// Check if News wasn't found
    	if(isset($arrNews) && count($arrNews) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find news')));
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Get News Content
    	$arrNewsContent				= $objModelNews->getNewsContent($arrNews['news_id']);
    	
    	// Get All News Types
		$arrNewsTypes				= $objModelNews->getNewsTypes('news_type_id');
		
		// Get All Categories
		$arrCategories				= $objModelCategories->getCategories('category_id');
		
		// Get All Tags
		$arrTags					= $objModelTags->getTags('tag_id');
		
		// Set Default Variables
		$intNewsTypeID				= $arrNews['news_type_id'];
		$intCategoryID				= $arrNews['category_id'];
		$strName					= $arrNews['name'];
		$strImage					= $arrNews['image'];
		$strDate					= $arrNews['date'];
		$intTime					= $arrNews['time'];
		$strSeoTitle				= $arrNews['seo_title'];
		$strSeoKeywords				= $arrNews['seo_keywords'];
		$strSeoDescription			= $arrNews['seo_description'];
		$intStatus					= $arrNews['status'];
		
		$arrActiveTags				= ((! empty($arrNews['tags'])) ? explode(',', $arrNews['tags']) : array());
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			$intDeleteID		= $objModelNews->deleteNews($arrNews['news_id']);
				
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				
				$intDeleteID	= $objModelNews->deleteNewsContentByNewsID($arrNews['news_id']);
				
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted news')));
				$this->_redirect('/admin/news/index/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the news');
			}
		
		}
		
		// Parse Variables to View
		$this->view->news 				= $arrNews;
		$this->view->newsContent		= $arrNewsContent;
		$this->view->newsTypes			= $arrNewsTypes;
		$this->view->categories 		= $arrCategories;
		$this->view->tags				= $arrTags;
		
		$this->view->news_type_id		= $intNewsTypeID;
		$this->view->category_id		= $intCategoryID;
		$this->view->active_tags		= $arrActiveTags;
		$this->view->name				= $strName;
		$this->view->image				= $strImage;
		$this->view->date				= KZ_View_Helper_Date::format($strDate, 'dd-MM-YYYY');
		$this->view->time				= $intTime;
		$this->view->seo_title			= $strSeoTitle;
		$this->view->seo_description	= $strSeoDescription;
		$this->view->seo_keywords		= $strSeoKeywords;
		$this->view->status				= $intStatus;
    	
    	
	}
	
	public function contentAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelNews				= new KZ_Models_News();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get News by ID
    	$arrNews					= $objModelNews->getNewsByID($arrParams['id']);
    	
		// Check if category was found
    	if(! isset($arrNews) || ! is_array($arrNews) || count($arrNews) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find news')));
    		$this->_redirect('/admin/news/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get News Content By News ID
    	$arrNewsContent				= $objModelNews->getNewsContent($arrNews['news_id']);
    	
    	// Get Last News Rank
    	$intLastNewsRank			= $objModelNews->getLastNewsRank($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('news','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= 0;
    	$strName					= '';
    	$intStatus					= 0;
    	
    	$strContent1Title			= ((count($arrNewsContent) > 0) ? '' : $arrNews['name']);
    	$strContent1Text			= '';
    	
		$strContent2Title 			= ((count($arrNewsContent) > 0) ? '' : $arrNews['name']);
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
		
		$strContent3Video			= '';
    	
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
    			$arrNewsData = array(
    				'news_id'			=> $arrNews['news_id'],
    				'content_type_id'	=> $intContentTypeID,
    				'rank'				=> ($intLastNewsRank + 1),
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
	    					if(
	    					strstr($arrPostParams['content_1_text'], '<table')
	    					&&	! strstr($arrPostParams['content_1_text'], 'class="default"')
	    					) {
	    					
	    						$strContent1Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_1_text']);
	    					
	    					}
	    					
	    					// Add content data to array
	    					$arrNewsData['data']	= serialize(array(	
	    													'content_1_title'	=> $strContent1Title,
	    													'content_1_text'	=> $strContent1Text
	    												));
	    					
	    					// Add News Content
	    					$intInsertID 		= $objModelNews->addNewsContent($arrNewsData);

	    					// Check if News Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/news/content/id/'.$arrNews['news_id'].'/feedback/'.$strFeedback.'/#tab0');
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
	    					if(
	    					strstr($arrPostParams['content_2_text'], '<table')
	    					&&	! strstr($arrPostParams['content_2_text'], 'class="default"')
	    					) {
	    					
	    						$strContent2Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_2_text']);
	    					
	    					}
	    					
	    					// Add content data to array
	    					$arrNewsData['data']	= serialize(array(	
	    													'content_2_title'	=> $strContent2Title,
	    													'content_2_image'	=> str_replace(ROOT_URL.'/upload/', '', $strContent2Image),
	    													'content_2_text'	=> $strContent2Text
	    												));
	    					
	    					// Add News Content
	    					$intInsertID 		= $objModelNews->addNewsContent($arrNewsData);

	    					// Check if News Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/news/content/id/'.$arrNews['news_id'].'/feedback/'.$strFeedback.'/#tab0');
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
	    					$arrNewsData['data']	= serialize(array(	
	    													'content_3_video'	=> $strContent3Video
	    												));

	    					// Add News Content
	    					$intInsertID 		= $objModelNews->addNewsContent($arrNewsData);

	    					// Check if News Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/news/content/id/'.$arrNews['news_id'].'/feedback/'.$strFeedback.'/#tab0');
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
    	$this->view->news				= $arrNews;
    	$this->view->news_content		= $arrNewsContent;
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
	
	public function contenteditAction()
	{
		
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelNews				= new KZ_Models_News();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get News Content by ID
    	$arrNewsContent				= $objModelNews->getNewsContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('news','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrNewsContent['content_type_id'];
    	$strName					= $arrNewsContent['name'];
    	$intStatus					= $arrNewsContent['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrNewsContent['data']);
		
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
    			$arrNewsData = array(
    				'news_id'			=> $arrNewsContent['news_id'],
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
	    				
	    				// Add Default table class to table element
	    				if(
	    				strstr($arrPostParams['content_1_text'], '<table')
	    				&&	! strstr($arrPostParams['content_1_text'], 'class="default"')
	    				) {
	    				
	    					$strContent1Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_1_text']);
	    				
	    				}
	    				
	    				if(empty($strContent1Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} elseif(empty($strContent1Text)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a text');
	    				} else {
	    					
	    					// Add content data to array
	    					$arrNewsData['data']	= serialize(array(	
	    													'content_1_title'	=> $strContent1Title,
	    													'content_1_text'	=> $strContent1Text
	    												));
	    					
	    					// Update News Content
	    					$intUpdateID 		= $objModelNews->updateNewsContent($arrNewsContent['news_content_id'], $arrNewsData);

	    					// Check if News Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
	    						
	    						// Get Last Revision from News Content Backup
	    						$intLastRevision		= $objModelNews->getLastNewsContentBackupRevision($arrNewsContent['news_content_id']);
	    						
	    						// Set News Content Backup array
	    						$arrNewsContentBackup 	= array(
	    							'news_content_id' 	=> $arrNewsContent['news_content_id'],
	    							'news_id'			=> $arrNewsContent['news_id'],
	    							'revision'			=> ($intLastRevision + 1),
	    							'content_type_id'	=> $arrNewsContent['content_type_id'],
	    							'name'				=> $arrNewsContent['name'],
	    							'data'				=> $arrNewsContent['data'],
	    							'status'			=> $arrNewsContent['status'],
	    							'user_id'			=> KZ_Controller_Session::getActiveUser()
	    						);
	    						
	    						// Backup overwritten News Content
	    						$intInsertID			= $objModelNews->addNewsContentBackup($arrNewsContentBackup);
	    						
	    						
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/news/content/id/'.$arrNewsContent['news_id'].'/feedback/'.$strFeedback.'/');
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
	    					if(
	    					strstr($arrPostParams['content_2_text'], '<table')
	    					&&	! strstr($arrPostParams['content_2_text'], 'class="default"')
	    					) {
	    						 
	    						$strContent2Text	= str_replace('<table', '<table class="default"', $arrPostParams['content_2_text']);
	    						 
	    					}
	    					
	    					// Add content data to array
	    					$arrNewsData['data']	= serialize(array(	
	    													'content_2_title'	=> $strContent2Title,
	    													'content_2_image'	=> str_replace(ROOT_URL.'/upload/', '', $strContent2Image),
	    													'content_2_text'	=> $strContent2Text
	    												));
	    					
	    					// Update News Content
	    					$intUpdateID 		= $objModelNews->updateNewsContent($arrNewsContent['news_content_id'], $arrNewsData);

	    					// Check if News Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/news/content/id/'.$arrNewsContent['news_id'].'/feedback/'.$strFeedback.'/');
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
	    					$arrNewsData['data']	= serialize(array(	
	    													'content_3_video'	=> $strContent3Video
	    												));

	    					// Update News Content
	    					$intUpdateID 		= $objModelNews->updateNewsContent($arrNewsContent['news_content_id'], $arrNewsData);

	    					// Check if News Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/news/content/id/'.$arrNewsContent['news_id'].'/feedback/'.$strFeedback.'/');
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
    	$this->view->news_content		= $arrNewsContent;
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
	
	public function contentdeleteAction()
	{

		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelNews				= new KZ_Models_News();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get News Content by ID
    	$arrNewsContent				= $objModelNews->getNewsContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('news','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrNewsContent['content_type_id'];
    	$strName					= $arrNewsContent['name'];
    	$intStatus					= $arrNewsContent['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrNewsContent['data']);
		
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
    		
    		// Delete News Content
			$intDeleteID 				= $objModelNews->deleteNewsContent($arrNewsContent['news_content_id']);

			// Check if News Content was succesfully updated
	    	if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted content')));
				$this->_redirect('/admin/news/content/id/'.$arrNewsContent['news_id'].'/feedback/'.$strFeedback.'/');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the content');
			}

	    }
    	
    	// Parse Data to View
    	$this->view->news_content		= $arrNewsContent;
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
    		$objModelNews 		= new KZ_Models_News();
    		
    		// Loop through rank
    		foreach($arrParams['rank'] as $intRankKey => $strRankData) {
    			
    			$intNewsContentID	= str_replace('content_', '', $strRankData);
    			$intUpdateID 		= $objModelNews->updateNewsContentRank($intNewsContentID, ($intRankKey + 1));
    			
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
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelNews				= new KZ_Models_News();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
		// Get News Content
    	$arrNewsContent				= $objModelNews->getNewsContentByID($arrParams['id']);
    	
		// Check if News was found
    	if(! isset($arrNewsContent) || ! is_array($arrNewsContent) || count($arrNewsContent) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find news content')));
    		$this->_redirect('/admin/news/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get News Content Backups by ID
    	$arrNewsContentBackup		= $objModelNews->getNewsContentBackups($arrNewsContent['news_content_id']);
    	
    	// Get News
    	$arrNews					= $objModelNews->getNewsByID($arrNewsContent['news_id']);

    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('news','content_type_id');
    	
    	// Parse Data to View
    	$this->view->news					= $arrNews;
    	$this->view->news_content_backups	= $arrNewsContentBackup;
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
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelNews				= new KZ_Models_News();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get News Content Backup by ID
    	$arrNewsContentBackup		= $objModelNews->getNewsBackupContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('news','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrNewsContentBackup['content_type_id'];
    	$strName					= $arrNewsContentBackup['name'];
    	$intStatus					= $arrNewsContentBackup['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrNewsContentBackup['data']);
		
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
    	$this->view->news_content_backup	= $arrNewsContentBackup;
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
    		$this->_redirect('/admin/news/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelNews			= new KZ_Models_News();
    	
    	// Get News Backup By ID
    	$arrNewsContentBackup	= $objModelNews->getNewsBackupContentByID($arrParams['id']);
		
    	// Set News Content Update array
    	$arrNewsContentUpdate	= array(
    		'content_type_id'	=> $arrNewsContentBackup['content_type_id'],
	    	'name'				=> $arrNewsContentBackup['name'],
    		'data'				=> $arrNewsContentBackup['data'],
    		'status'			=> $arrNewsContentBackup['status'],
    		'user_id'			=> KZ_Controller_Session::getActiveUser()
    	);
    	
		// Update News Content
		$intUpdateID			= $objModelNews->updateNewsContent($arrNewsContentBackup['news_content_id'], $arrNewsContentUpdate);
    	
		// Check if update was succesfull
		if(is_numeric($intUpdateID)) {
			$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully restored backup')));
			$this->_redirect('/admin/news/content/id/'.$arrNewsContentBackup['news_id'].'/feedback/'.$strFeedback.'/');
		} else {
			// Return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Something went wrong trying to restore the backup')));
			$this->_redirect('/admin/news/content/id/'.$arrNewsContentBackup['news_id'].'/feedback/'.$strFeedback.'/');
		}
    	
	}

	/**
     * Function for generating the News table
     * Used for the AJAX call for the Datatable
     */
    public function generatenewstableAction()
    {
    	// Disable Layout and View
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// Set Models
    	$objModelNews			= new KZ_Models_News();
    	$objModelTags			= new KZ_Models_Tags();
    	$objTranslate			= new KZ_View_Helper_Translate();
    	
    	// Get All News Types
		$arrNewsTypes			= $objModelNews->getNewsTypes('news_type_id');

		// Get All Tags
		$arrAllTags				= $objModelTags->getTags('tag_id');
		
    	// Set the Columns
    	$arrColumns				= array($objTranslate->translate('ID'),
						    			$objTranslate->translate('Name'),
						    			$objTranslate->translate('Type'),
						    			$objTranslate->translate('Category'),
						    			$objTranslate->translate('Tags'),
						    			$objTranslate->translate('Date'),
						    			$objTranslate->translate('Status'),
						    			$objTranslate->translate('Created'),
						    			$objTranslate->translate('Lastmodified'),
						    			$objTranslate->translate('Modified by'),
						    			$objTranslate->translate('Options'));

    	// Set the DB Table Columns
    	$arrTableColums			= array('news_id',
						    			'name',
    									'news_type_id',
						    			'category',
						    			'tags',
    									'date',
						    			'status',
						    			'created',
						    			'lastmodified',
    									'user_id');

    	// Set the Search
    	$strSearchData			= null;
    	if($_POST['sSearch'] != "") {
    		$strSearchString		= $_POST['sSearch'];
    	
    		if(is_numeric($strSearchString)) {
    			$strSearchData		= "news_id LIKE '%".$strSearchString."%'";
    		} else {
    			$strSearchData		= "(news.name LIKE '%".$strSearchString."%' OR news.nameSlug LIKE '%".$strSearchString."%')";
    		}
    	}
    	
    	// Set the Limit
    	$intResultsOnPage		= $this->_getParam('iDisplayLength');
    	$intStartNumber			= $this->_getParam('iDisplayStart');
    	$arrLimitData			= array('count' 	=> $intResultsOnPage,
    									'offset'	=> $intStartNumber);
    	
    	// Ordering
    	$arrOrderData					= array();
    	if(isset($_POST['iSortCol_0'])) {
    		$trsOrdering = "ORDER BY  ";
    		for($intI = 0; $intI < intval($_POST['iSortingCols']); $intI++ ) {
    			if($_POST['bSortable_'.intval($_POST['iSortCol_'.$intI])] == "true" ) {
    				$strSortColumns		= $arrTableColums[intval($_POST['iSortCol_'.$intI])];
    				$strSortDirection	= $_POST['sSortDir_'.$intI];
    				$arrOrderData[]		= $strSortColumns.' '.strtoupper($strSortDirection);
    			}
    		}
    	}

    	// Get the Totals
    	$intTotalNews				= $objModelNews->getNewsForTable(true);
    	
    	// Select all News
    	$objNews 					= $objModelNews->getNewsForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
    	$arrNews					= $objNews->toArray();

    	// Create the JSON Data
    	$output 					= array("sEcho" 				=> intval($_POST['sEcho']),
							    			"iTotalRecords" 		=> $intTotalNews,
							    			"iTotalDisplayRecords" 	=> $intTotalNews,
							    			"aaData"				=> array()
    	);
    	
    	if(!empty($arrNews)) {
    		foreach($arrNews as $key => $arrNewsValues) {

    			$row = array();
    			for($i = 0; $i < count($arrColumns); $i++) {
    				if($arrColumns[$i] != ' ') {
    					
    					if(isset($arrTableColums[$i])) {
    						if($arrTableColums[$i] == 'news_type_id') {
    							$strRowData		= ((isset($arrNewsTypes[$arrNewsValues['news_type_id']]) && is_array($arrNewsTypes[$arrNewsValues['news_type_id']])) ? $arrNewsTypes[$arrNewsValues['news_type_id']]['name'] : '-');
    						} elseif($arrTableColums[$i] == 'category') {
    							$strRowData		= $arrNewsValues['category'].'<br /><span class="tag" style="background-color: '.$arrNewsValues['category_color'].'"></span>';
    						} elseif($arrTableColums[$i] == 'tags') {
	    						
    							// Set Tags
								$strTags			= '';
							
								if(isset($arrNewsValues['tags']) && ! empty($arrNewsValues['tags'])) {
									// Check if tags where found
									if(! empty($arrNewsValues['tags'])) {
										// Check if multiple tags where found
										if(strstr($arrNewsValues['tags'], ',')) {
											
											// Set Tags array
											$arrTags = explode(',', $arrNewsValues['tags']);
											
											foreach($arrTags as $intTagKey => $intTagID) {
												$strSeperator = (($intTagKey == 0) ? '' : ', ');
												$strTags .= $strSeperator.$arrAllTags[$intTagID]['name'];	
											}
											
										} else {
			
											// Set Single Tag Name
											$strTags	= $arrAllTags[$arrNewsValues['tags']]['name'];
											
										}
										
									}
									
								}
								
								$strRowData = $strTags;
							} elseif($arrTableColums[$i] == 'date') {
								$strRowData		= KZ_View_Helper_Date::format($arrNewsValues['date'], 'dd-MM-YYYY').'<br />'.(($arrNewsValues['time'] <= 9) ? '0'.$arrNewsValues['time'] : $arrNewsValues['time']).':00';
    						} elseif($arrTableColums[$i] == 'status') {
    							$strRowData		= '<span class="tag '.(($arrNewsValues['status'] == 1) ? 'green' : 'red').'">'.(($arrNewsValues['status'] == 1) ? 'active' : 'inactive').'</span>';
    						} elseif(in_array($arrTableColums[$i], array('created','lastmodified'))) {
    							$strRowData		= KZ_View_Helper_Date::format($arrNewsValues[$arrTableColums[$i]], 'dd-MM-YYYY HH:mm:ss');
    						} elseif($arrTableColums[$i] == 'user_id') {
    							$strRowData		= ((isset($this->view->users[$arrNewsValues['user_id']]['name']) && ! empty($this->view->users[$arrNewsValues['user_id']]['name'])) ? $this->view->users[$arrNewsValues['user_id']]['name'] : '-');
    						} else {
    							$strRowData		= stripslashes($arrNewsValues[$arrTableColums[$i]]);
    						}
    					} else {
    						
    						$strOptionsHtml = '<ul class="actions">
													<li><a rel="tooltip" href="/preview/index/id/'.$arrNewsValues['news_id'].'/" target="_blank" class="view" original-title="'.$objTranslate->translate('Show preview').'">'.$objTranslate->translate('Show preview').'</a></li>
													<li><a rel="tooltip" href="/admin/news/content/id/'.$arrNewsValues['news_id'].'/" class="content" original-title="'.$objTranslate->translate('Edit content').'">'.$objTranslate->translate('Edit news content').'</a></li>
													<li><a rel="tooltip" href="/admin/news/edit/id/'.$arrNewsValues['news_id'].'/" class="edit" original-title="'.$objTranslate->translate('Edit news').'">'.$objTranslate->translate('Edit news').'</a></li>
													<li><a rel="tooltip" href="/admin/news/delete/id/'.$arrNewsValues['news_id'].'/" class="delete" original-title="'.$objTranslate->translate('Delete news').'">'.$objTranslate->translate('Delete news').'</a></li>
												</ul>';

    						$strRowData		= $strOptionsHtml;
    					}
    					$row[] = $strRowData;
    				}
    			}
    	
    			$output['aaData'][] = $row;
    		}
    	}
    	
    	// Send the Output
    	echo json_encode($output);
    	
    }
	
}