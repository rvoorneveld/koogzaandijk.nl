<?php
class Admin_BlogController extends KZ_Controller_Action
{
	public $objModelBlog;

	public function init()
	{
		$this->objModelBlog = new KZ_Models_Blog();
	}

	public function indexAction()
	{
		// Parse Variables to View
		$this->view->blog = $this->objModelBlog->getBlog();

	}
	
	public function editAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda				= new KZ_Models_Agenda();
    	$objModelNews				= new KZ_Models_News();
    	$objModelWidgets			= new KZ_Models_Widgets();
    	
    	// Get Agenda
    	$arrAgenda					= $objModelAgenda->getAgendaByID($arrParams['id']);
    	
    	// Get News
    	$arrNews					= $objModelNews->getNews();

    	// Check if Agenda wasn't found
    	if(isset($arrAgenda) && count($arrAgenda) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find agenda')));
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
		// Set Default Variables
		$strName					= $arrAgenda['name'];
		$intNewsID					= $arrAgenda['news_id'];
		$strLocation				= $arrAgenda['location'];
		$strDateStart				= $this->view->date()->format($arrAgenda['date_start'], 'dd-MM-yyyy');
		$strDateEnd					= $this->view->date()->format($arrAgenda['date_end'], 'dd-MM-yyyy');
		$strTimeStart				= $arrAgenda['time_start'];
		$strTimeEnd					= $arrAgenda['time_end'];
		$strSeoTitle				= $arrAgenda['seo_title'];
		$strSeoKeywords				= $arrAgenda['seo_keywords'];
		$strSeoDescription			= $arrAgenda['seo_description'];
		$intWidgetLayout			= $arrAgenda['widget_layout'];
		$intStatus					= $arrAgenda['status'];
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams			= $this->_getAllParams();
			
			// Set Post Variables
			$strName				= $arrPostParams['name'];
			$intNewsID				= $arrPostParams['news_id'];
			$strLocation			= $arrPostParams['location'];
			$strDateStart			= $arrPostParams['date_start'];
			$strDateEnd				= $arrPostParams['date_end'];
			$strTimeStart			= $arrPostParams['time_start'];
			$strTimeEnd				= $arrPostParams['time_end'];
			$strSeoTitle			= $arrPostParams['seo_title'];
			$strSeoKeywords			= $arrPostParams['seo_keywords'];
			$strSeoDescription		= $arrPostParams['seo_description'];
			$intWidgetLayout		= $arrPostParams['widget_layout'];
			$intStatus				= $arrPostParams['status'];
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strDateStart)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select in a start date');
			} elseif(empty($strDateEnd)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select in an end date');
			} else {

                // Resultset of matching agenda items with the same slug
                $arrMatchingAgendaBySlug = $objModelAgenda->getMatchingAgendaBySlug(KZ_Controller_Action_Helper_Slug::slug($strName),'agenda_id');

                // Remove Current Agenda items From Edit
                if(array_key_exists($arrParams['id'], $arrMatchingAgendaBySlug)) {
                    // Remove Current Agenda item From Matching Agenda by Slug
                    unset($arrMatchingAgendaBySlug[$arrParams['id']]);
                }

                // Check if Agenda items where found
                if(empty($arrMatchingAgendaBySlug) && is_array($arrMatchingAgendaBySlug)) {

                    $arrAgendaData = array(
                        'name' 				=> $strName,
                        'news_id'			=> $intNewsID,
                        'nameSlug'			=> KZ_Controller_Action_Helper_Slug::slug($strName),
                        'location'			=> $strLocation,
                        'date_start'		=> $this->view->date()->format($strDateStart, 'yyyy-MM-dd'),
                        'date_end'			=> $this->view->date()->format($strDateEnd, 'yyyy-MM-dd'),
                        'time_start'		=> $strTimeStart,
                        'time_end'			=> $strTimeEnd,
                        'seo_title'			=> $strSeoTitle,
                        'seo_description'	=> $strSeoDescription,
                        'seo_keywords'		=> $strSeoKeywords,
                        'widget_layout'		=> (($intWidgetLayout == 0) ? NULL : $intWidgetLayout),
                        'status'			=> $intStatus,
                        'user_id'			=> KZ_Controller_Session::getActiveUser()
                    );

                    $intUpdateID		= $objModelAgenda->updateAgenda($arrAgenda['agenda_id'], $arrAgendaData);

                    if(isset($intUpdateID) && is_numeric($intUpdateID)) {
                        $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated agenda')));
                        $this->_redirect('/admin/agenda/index/feedback/'.$strFeedback.'/#tab0');
                    } else {
                        // Return feedback
                        $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the agenda');
                    }
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'An agenda item with this name already exists');
                }
				
			}
		
		}
		
		// Set Widget Layout Status for status
		$intWidgetLayoutStatus			= 1; // Active Widget Layouts only
		
		// Get All Widget Layouts
		$arrWidgetLayouts				= $objModelWidgets->getWidgetLayouts($intWidgetLayoutStatus);
		
		// Parse Variables to View
		$this->view->agenda 			= $arrAgenda;
		$this->view->news				= $arrNews;
		$this->view->widgetLayouts		= $arrWidgetLayouts;

		$this->view->name				= $strName;
		$this->view->news_id			= $intNewsID;
		$this->view->location			= $strLocation;
		$this->view->date_start			= $strDateStart;
		$this->view->date_end			= $strDateEnd;
		$this->view->time_start			= $strTimeStart;
		$this->view->time_end			= $strTimeEnd;
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
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda				= new KZ_Models_Agenda();
    	
    	// Get Agenda
    	$arrAgenda					= $objModelAgenda->getAgendaByID($arrParams['id']);

    	// Check if Agenda wasn't found
    	if(isset($arrAgenda) && count($arrAgenda) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find agenda')));
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Get Agenda Content
    	$arrAgendaContent				= $objModelAgenda->getAgendaContent($arrAgenda['agenda_id']);
		
		// Set Default Variables
		$strName					= $arrAgenda['name'];
		$intNewsID					= $arrAgenda['news_id'];
		$strNewsName				= '';
		$strLocation				= $arrAgenda['location'];
		$strDateStart				= $arrAgenda['date_start'];
		$strDateEnd					= $arrAgenda['date_end'];
		$strTimeStart				= $arrAgenda['time_start'];
		$strTimeEnd					= $arrAgenda['time_end'];
		$strSeoTitle				= $arrAgenda['seo_title'];
		$strSeoKeywords				= $arrAgenda['seo_keywords'];
		$strSeoDescription			= $arrAgenda['seo_description'];
		$intStatus					= $arrAgenda['status'];
		
		// Check if News was attached
		if($intNewsID > 0) {
			
			// Set news Model
			$objModelNews	= new KZ_Models_News();
			
			// Get News
			$arrNews		= $objModelNews->getNewsByID($intNewsID);
			
			// Check if News was found
			if(isset($arrNews) && is_array($arrNews) && count($arrNews) > 0) {
				// Set News Name
				$strNewsName	= $arrNews['name'];
			}
			
		}
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			$intDeleteID		= $objModelAgenda->deleteAgenda($arrAgenda['agenda_id']);
				
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				
				$intDeleteID	= $objModelAgenda->deleteAgendaContentByAgendaID($arrAgenda['agenda_id']);
				
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted agenda')));
				$this->_redirect('/admin/agenda/index/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the agenda');
			}
		
		}
		
		// Parse Variables to View
		$this->view->agenda 			= $arrAgenda;

		$this->view->name				= $strName;
		$this->view->news_name			= $strNewsName;
		$this->view->news_id			= $intNewsID;
		$this->view->location			= $strLocation;
		$this->view->date_start			= $this->view->date()->format($strDateStart, 'dd-MM-yyyy');
		$this->view->date_end			= $this->view->date()->format($strDateEnd, 'dd-MM-yyyy');
		$this->view->time_start			= $strTimeStart;
		$this->view->time_end			= $strTimeEnd;
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
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda				= new KZ_Models_Agenda();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Agenda by ID
    	$arrAgenda					= $objModelAgenda->getAgendaByID($arrParams['id']);
    	
    	// Check if News was attached
    	if($arrAgenda['news_id'] > 0) {
    		$this->view->note = array('type' => 'attention', 'message' => 'There is news attached to this agenda item. This content will not be displayed on the website');
    	}
    	
		// Check if category was found
    	if(! isset($arrAgenda) || ! is_array($arrAgenda) || count($arrAgenda) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find agenda')));
    		$this->_redirect('/admin/agenda/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get Agenda Content By Agenda ID
    	$arrAgendaContent			= $objModelAgenda->getAgendaContent($arrAgenda['agenda_id']);
    	
    	// Get Last Agenda Rank
    	$intLastAgendaRank			= $objModelAgenda->getLastAgendaRank($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('agenda','content_type_id');
    	
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
    			$arrAgendaData = array(
    				'agenda_id'			=> $arrAgenda['agenda_id'],
    				'content_type_id'	=> $intContentTypeID,
    				'rank'				=> ($intLastAgendaRank + 1),
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
	    					
	    					// Add content data to array
	    					$arrAgendaData['data']	= serialize(array(	
	    													'content_1_title'	=> $strContent1Title,
	    													'content_1_text'	=> $strContent1Text
	    												));
	    					
	    					// Add Agenda Content
	    					$intInsertID 		= $objModelAgenda->addAgendaContent($arrAgendaData);

	    					// Check if Agenda Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/agenda/content/id/'.$arrAgenda['agenda_id'].'/feedback/'.$strFeedback.'/#tab0');
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
	    					
	    					// Add content data to array
	    					$arrAgendaData['data']	= serialize(array(	
	    													'content_2_title'	=> $strContent2Title,
	    													'content_2_image'	=> str_replace(ROOT_URL.'/upload/', '', $strContent2Image),
	    													'content_2_text'	=> $strContent2Text
	    												));
	    					
	    					// Add Agenda Content
	    					$intInsertID 		= $objModelAgenda->addAgendaContent($arrAgendaData);

	    					// Check if Agenda Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/agenda/content/id/'.$arrAgenda['agenda_id'].'/feedback/'.$strFeedback.'/#tab0');
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
	    					$arrAgendaData['data']	= serialize(array(	
	    													'content_3_video'	=> $strContent3Video
	    												));

	    					// Add Agenda Content
	    					$intInsertID 		= $objModelAgenda->addAgendaContent($arrAgendaData);

	    					// Check if Agenda Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/agenda/content/id/'.$arrAgenda['agenda_id'].'/feedback/'.$strFeedback.'/#tab0');
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
    	$this->view->agenda				= $arrAgenda;
    	$this->view->agenda_content		= $arrAgendaContent;
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
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda				= new KZ_Models_Agenda();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Agenda Content by ID
    	$arrAgendaContent			= $objModelAgenda->getAgendaContentByID($arrParams['id']);
    	
    	// Get Agenda
    	$arrAgenda					= $objModelAgenda->getAgendaByID($arrAgendaContent['agenda_id']);
    	
    	// Check if News was attached
		if($arrAgenda['news_id'] > 0) {
    		$this->view->note = array('type' => 'attention', 'message' => 'There is news attached to this agenda item. This content will not be displayed on the website');
    	}
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('agenda','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrAgendaContent['content_type_id'];
    	$strName					= $arrAgendaContent['name'];
    	$intStatus					= $arrAgendaContent['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrAgendaContent['data']);
		
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
    			$arrAgendaData = array(
    				'agenda_id'			=> $arrAgendaContent['agenda_id'],
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
	    					
	    					// Add content data to array
	    					$arrAgendaData['data']	= serialize(array(	
	    													'content_1_title'	=> $strContent1Title,
	    													'content_1_text'	=> $strContent1Text
	    												));
	    					
	    					// Update Agenda Content
	    					$intUpdateID 		= $objModelAgenda->updateAgendaContent($arrAgendaContent['agenda_content_id'], $arrAgendaData);

	    					// Check if Agenda Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
	    						
	    						// Get Last Revision from Agenda Content Backup
	    						$intLastRevision		= $objModelAgenda->getLastAgendaContentBackupRevision($arrAgendaContent['agenda_content_id']);
	    						
	    						// Set Agenda Content Backup array
	    						$arrAgendaContentBackup 	= array(
	    							'agenda_content_id' 	=> $arrAgendaContent['agenda_content_id'],
	    							'agenda_id'			=> $arrAgendaContent['agenda_id'],
	    							'revision'			=> ($intLastRevision + 1),
	    							'content_type_id'	=> $arrAgendaContent['content_type_id'],
	    							'name'				=> $arrAgendaContent['name'],
	    							'data'				=> $arrAgendaContent['data'],
	    							'status'			=> $arrAgendaContent['status'],
	    							'user_id'			=> KZ_Controller_Session::getActiveUser()
	    						);
	    						
	    						// Backup overwritten Agenda Content
	    						$intInsertID			= $objModelAgenda->addAgendaContentBackup($arrAgendaContentBackup);
	    						
	    						
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/agenda/content/id/'.$arrAgendaContent['agenda_id'].'/feedback/'.$strFeedback.'/');
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
	    					
	    					// Add content data to array
	    					$arrAgendaData['data']	= serialize(array(	
	    													'content_2_title'	=> $strContent2Title,
	    													'content_2_image'	=> str_replace(ROOT_URL.'/upload/', '', $strContent2Image),
	    													'content_2_text'	=> $strContent2Text
	    												));
	    					
	    					// Update Agenda Content
	    					$intUpdateID 		= $objModelAgenda->updateAgendaContent($arrAgendaContent['agenda_content_id'], $arrAgendaData);

	    					// Check if Agenda Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/agenda/content/id/'.$arrAgendaContent['agenda_id'].'/feedback/'.$strFeedback.'/');
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
	    					$arrAgendaData['data']	= serialize(array(	
	    													'content_3_video'	=> $strContent3Video
	    												));

	    					// Update Agenda Content
	    					$intUpdateID 		= $objModelAgenda->updateAgendaContent($arrAgendaContent['agenda_content_id'], $arrAgendaData);

	    					// Check if Agenda Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/agenda/content/id/'.$arrAgendaContent['agenda_id'].'/feedback/'.$strFeedback.'/');
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
    	$this->view->agenda_content		= $arrAgendaContent;
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
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda				= new KZ_Models_Agenda();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Agenda Content by ID
    	$arrAgendaContent				= $objModelAgenda->getAgendaContentByID($arrParams['id']);
    	
		// Get Agenda
    	$arrAgenda					= $objModelAgenda->getAgendaByID($arrAgendaContent['agenda_id']);
    	
    	// Check if News was attached
		if($arrAgenda['news_id'] > 0) {
    		$this->view->note = array('type' => 'attention', 'message' => 'There is news attached to this agenda item. This content will not be displayed on the website');
    	}
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('agenda','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrAgendaContent['content_type_id'];
    	$strName					= $arrAgendaContent['name'];
    	$intStatus					= $arrAgendaContent['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrAgendaContent['data']);
		
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
    		
    		// Delete Agenda Content
			$intDeleteID 				= $objModelAgenda->deleteAgendaContent($arrAgendaContent['agenda_content_id']);

			// Check if Agenda Content was succesfully updated
	    	if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted content')));
				$this->_redirect('/admin/agenda/content/id/'.$arrAgendaContent['agenda_id'].'/feedback/'.$strFeedback.'/');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the content');
			}

	    }
    	
    	// Parse Data to View
    	$this->view->agenda_content		= $arrAgendaContent;
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
    		$objModelAgenda 		= new KZ_Models_Agenda();
    		
    		// Loop through rank
    		foreach($arrParams['rank'] as $intRankKey => $strRankData) {
    			
    			$intAgendaContentID	= str_replace('content_', '', $strRankData);
    			$intUpdateID 		= $objModelAgenda->updateAgendaContentRank($intAgendaContentID, ($intRankKey + 1));
    			
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
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda				= new KZ_Models_Agenda();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
		// Get Agenda Content
    	$arrAgendaContent				= $objModelAgenda->getAgendaContentByID($arrParams['id']);
    	
		// Check if Agenda was found
    	if(! isset($arrAgendaContent) || ! is_array($arrAgendaContent) || count($arrAgendaContent) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find agenda content')));
    		$this->_redirect('/admin/agenda/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get Agenda Content Backups by ID
    	$arrAgendaContentBackup		= $objModelAgenda->getAgendaContentBackups($arrAgendaContent['agenda_content_id']);
    	
    	// Get Agenda
    	$arrAgenda					= $objModelAgenda->getAgendaByID($arrAgendaContent['agenda_id']);

    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('agenda','content_type_id');
    	
    	// Parse Data to View
    	$this->view->agenda					= $arrAgenda;
    	$this->view->agenda_content_backups	= $arrAgendaContentBackup;
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
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda				= new KZ_Models_Agenda();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Agenda Content Backup by ID
    	$arrAgendaContentBackup		= $objModelAgenda->getAgendaBackupContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('agenda','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrAgendaContentBackup['content_type_id'];
    	$strName					= $arrAgendaContentBackup['name'];
    	$intStatus					= $arrAgendaContentBackup['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		$strContent3Video 			= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrAgendaContentBackup['data']);
		
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
    	$this->view->agenda_content_backup	= $arrAgendaContentBackup;
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
    		$this->_redirect('/admin/agenda/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelAgenda			= new KZ_Models_Agenda();
    	
    	// Get Agenda Backup By ID
    	$arrAgendaContentBackup	= $objModelAgenda->getAgendaBackupContentByID($arrParams['id']);
		
    	// Set Agenda Content Update array
    	$arrAgendaContentUpdate	= array(
    		'content_type_id'	=> $arrAgendaContentBackup['content_type_id'],
	    	'name'				=> $arrAgendaContentBackup['name'],
    		'data'				=> $arrAgendaContentBackup['data'],
    		'status'			=> $arrAgendaContentBackup['status'],
    		'user_id'			=> KZ_Controller_Session::getActiveUser()
    	);
    	
		// Update Agenda Content
		$intUpdateID			= $objModelAgenda->updateAgendaContent($arrAgendaContentBackup['agenda_content_id'], $arrAgendaContentUpdate);
    	
		// Check if update was succesfull
		if(is_numeric($intUpdateID)) {
			$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully restored backup')));
			$this->_redirect('/admin/agenda/content/id/'.$arrAgendaContentBackup['agenda_id'].'/feedback/'.$strFeedback.'/');
		} else {
			// Return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Something went wrong trying to restore the backup')));
			$this->_redirect('/admin/agenda/content/id/'.$arrAgendaContentBackup['agenda_id'].'/feedback/'.$strFeedback.'/');
		}
    	
	}
	
	public function mailingsAction()
	{

	}

	/**
     * Function for generating the Agenda table
     * Used for the AJAX call for the Datatable
     */
    public function generateagendatableAction()
    {
    	// Disable Layout and View
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// Set Models
    	$objModelAgenda			= new KZ_Models_Agenda();
    	$objTranslate			= new KZ_View_Helper_Translate();
    	
	   	// Set the Columns
    	$arrColumns				= array($objTranslate->translate('ID'),
						    			$objTranslate->translate('Name'),
						    			$objTranslate->translate('Date'),
						    			$objTranslate->translate('Status'),
						    			$objTranslate->translate('Created'),
						    			$objTranslate->translate('Lastmodified'),
						    			$objTranslate->translate('Modified by'),
						    			$objTranslate->translate('Options'));

    	// Set the DB Table Columns
    	$arrTableColums			= array('agenda_id',
						    			'name',
    									'date_start',
						    			'status',
						    			'created',
						    			'lastmodified',
    									'user_id');

    	// Set the Search
    	$strSearchData			= null;
    	if($_POST['sSearch'] != "") {
    		$strSearchString		= $_POST['sSearch'];
    	
    		if(is_numeric($strSearchString)) {
    			$strSearchData		= "agenda_id LIKE '%".$strSearchString."%'";
    		} else {
    			$strSearchData		= "(name LIKE '%".$strSearchString."%' OR nameSlug LIKE '%".$strSearchString."%' OR date_start LIKE '%".$strSearchString."%' OR date_end LIKE '%".$strSearchString."%')";
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
    	$intTotalAgenda				= $objModelAgenda->getAgendaForTable(true);
    	
    	// Select all Agenda items
    	$objAgenda 					= $objModelAgenda->getAgendaForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
    	$arrAgenda					= $objAgenda->toArray();

    	// Create the JSON Data
    	$output 					= array("sEcho" 				=> intval($_POST['sEcho']),
							    			"iTotalRecords" 		=> $intTotalAgenda,
							    			"iTotalDisplayRecords" 	=> $intTotalAgenda,
							    			"aaData"				=> array()
    	);
    	
    	
    	/**
    	 * 
    	 * <?php foreach($this->agenda as $arrAgenda) {
					
					// Format Created Date
					$strCreatedDate 	= KZ_View_Helper_Date::format($arrAgenda['created'], 'dd-MM-yyyy HH:mm:ss');
					
					// Format Modified Date
					$strModifiedDate 	= KZ_View_Helper_Date::format($arrAgenda['lastmodified'], 'dd-MM-yyyy HH:mm:ss');
					
			?>
			
					<tr>
						<td><?php echo $arrAgenda['agenda_id']; ?></td>
						<td><a href="/admin/agenda/edit/id/<?php echo $arrAgenda['agenda_id']; ?>"><strong><?php echo $arrAgenda['name']; ?></strong></a></td>
						<td><?php echo KZ_View_Helper_Date::format($arrAgenda['date_start'], 'dd/MM/yyyy').' - '.KZ_View_Helper_Date::format($arrAgenda['date_end'], 'dd/MM/yyyy'); ?></td>
						<td><span class="tag <?php echo (($arrAgenda['status'] == 1) ? 'green' : 'red'); ?>"><?php echo (($arrAgenda['status'] == 1) ? 'active' : 'inactive'); ?></span></td>
						<td><?php echo $strCreatedDate;?></td>
						<td><?php echo $strModifiedDate;?></td>
						<td><?php echo ((isset($this->users[$arrAgenda['user_id']]['name']) && ! empty($this->users[$arrAgenda['user_id']]['name'])) ? $this->users[$arrAgenda['user_id']]['name'] : '-');?></td>
						<td>
							<ul class="actions">
								<li><a rel="tooltip" href="/preview/index/id/<?php echo $arrAgenda['agenda_id']; ?>/" target="_blank" class="view" original-title="<?php echo $this->translate('Show preview'); ?>"><?php echo $this->translate('Show preview'); ?></a></li>
								<li><a rel="tooltip" href="/admin/agenda/content/id/<?php echo $arrAgenda['agenda_id']; ?>/" class="content" original-title="<?php echo $this->translate('Edit content'); ?>"><?php echo $this->translate('Edit agenda content'); ?></a></li>
								<li><a rel="tooltip" href="/admin/agenda/edit/id/<?php echo $arrAgenda['agenda_id']; ?>/" class="edit" original-title="<?php echo $this->translate('Edit agenda'); ?>"><?php echo $this->translate('Edit agenda'); ?></a></li>
								<li><a rel="tooltip" href="/admin/agenda/delete/id/<?php echo $arrAgenda['agenda_id']; ?>/" class="delete" original-title="<?php echo $this->translate('Delete agenda'); ?>"><?php echo $this->translate('Delete agenda'); ?></a></li>
							</ul>
						</td>
					</tr>
			
			<?php } ?>
    	 */
    	
    	if(!empty($arrAgenda)) {
    		foreach($arrAgenda as $key => $arrAgendaValues) {

    			$row = array();
    			for($i = 0; $i < count($arrColumns); $i++) {
    				if($arrColumns[$i] != ' ') {
    					
    					if(isset($arrTableColums[$i])) {
    						if($arrTableColums[$i] == 'date_start') {
    							if($arrAgendaValues['date_start'] != $arrAgendaValues['date_end']) {
    								$strRowData		= $this->view->date()->format($arrAgendaValues['date_start'], 'dd-MM-YYYY').' / '.$this->view->date()->format($arrAgendaValues['date_end'], 'dd-MM-YYYY');
    							} else {
    								$strRowData		= $this->view->date()->format($arrAgendaValues['date_start'], 'dd-MM-YYYY');
    							}
    						}elseif($arrTableColums[$i] == 'status') {
    							$strRowData		= '<span class="tag '.(($arrAgendaValues['status'] == 1) ? 'green' : 'red').'">'.(($arrAgendaValues['status'] == 1) ? 'active' : 'inactive').'</span>';
    						} elseif(in_array($arrTableColums[$i], array('created','lastmodified'))) {
    							$strRowData		= $this->view->date()->format($arrAgendaValues[$arrTableColums[$i]], 'dd-MM-YYYY HH:mm:ss');
    						} elseif($arrTableColums[$i] == 'user_id') {
    							$strRowData		= ((isset($this->view->users[$arrAgendaValues['user_id']]['name']) && ! empty($this->view->users[$arrAgendaValues['user_id']]['name'])) ? $this->view->users[$arrAgendaValues['user_id']]['name'] : '-');
    						} else {
    							$strRowData		= stripslashes($arrAgendaValues[$arrTableColums[$i]]);
    						}
    					} else {
    						
    						$strOptionsHtml = '<ul class="actions">
													<li><a rel="tooltip" href="/preview/index/id/'.$arrAgendaValues['agenda_id'].'/" target="_blank" class="view" original-title="'.$objTranslate->translate('Show preview').'">'.$objTranslate->translate('Show preview').'</a></li>
													<li><a rel="tooltip" href="/admin/agenda/content/id/'.$arrAgendaValues['agenda_id'].'/" class="content" original-title="'.$objTranslate->translate('Edit content').'">'.$objTranslate->translate('Edit agenda content').'</a></li>
													<li><a rel="tooltip" href="/admin/agenda/edit/id/'.$arrAgendaValues['agenda_id'].'/" class="edit" original-title="'.$objTranslate->translate('Edit agenda').'">'.$objTranslate->translate('Edit agenda').'</a></li>
													<li><a rel="tooltip" href="/admin/agenda/delete/id/'.$arrAgendaValues['agenda_id'].'/" class="delete" original-title="'.$objTranslate->translate('Delete agenda').'">'.$objTranslate->translate('Delete agenda').'</a></li>
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
