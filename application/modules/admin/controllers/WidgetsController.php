<?php
class Admin_WidgetsController extends KZ_Controller_Action
{
	
	public $arrColors;

	public function init() {
		
		// Set Models
		$objModelColors		= new KZ_Models_Colors(); 
		$this->arrColors 	= $objModelColors->getColors('code');
		
		$this->view->colors	= $this->arrColors;
		
	}
	
	public function indexAction()
	{
		// Get Models
		$objModelWidget				= new KZ_Models_Widgets();
		
		// Get All Widget items
		$arrWidgets					= $objModelWidget->getWidgets();
		
		// Set User Model
		$objModelUsers				= new KZ_Models_Users();
		
		// Set Default Variables
		$strName					= '';
		$strSize					= 'small';
		$strColorBackground			= '';
		$strColorText				= '';
		$intStatus					= 0;
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams			= $this->_getAllParams();
			
			// Set Post Variables
			$strName				= $arrPostParams['name'];
			$strSize				= $arrPostParams['size'];
			$strColorBackground		= $arrPostParams['color_background'];
			$strColorText			= $arrPostParams['color_text'];
			$intStatus				= $arrPostParams['status'];
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else { 
				
				$arrWidgetData = array(
					'name' 				=> $strName,
					'size'				=> $strSize,
					'color_background'	=> $strColorBackground,
					'color_text'		=> $strColorText,
					'status'			=> $intStatus,
					'user_id'			=> KZ_Controller_Session::getActiveUser()
				);
				
				$intInsertID		= $objModelWidget->addWidget($arrWidgetData);
				
				if(isset($intInsertID) && is_numeric($intInsertID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added widget')));
					$this->_redirect('/admin/widgets/content/id/'.$intInsertID.'/feedback/'.$strFeedback.'/#tab1');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the widget');
				}
				
			}
		
		}
		
		// Parse Variables to View
		$this->view->widgets 			= $arrWidgets;

		$this->view->name				= $strName;
		$this->view->size				= $strSize;
		$this->view->color_background	= $strColorBackground;
		$this->view->color_text			= $strColorText;
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
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget				= new KZ_Models_Widgets();
    	
    	// Get Widget
    	$arrWidget					= $objModelWidget->getWidgetByID($arrParams['id']);

    	// Check if Widget wasn't found
    	if(isset($arrWidget) && count($arrWidget) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find widget')));
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
		// Set Default Variables
		$strName					= $arrWidget['name'];
		$strSize					= $arrWidget['size'];
		$strColorBackground			= $arrWidget['color_background'];
		$strColorText				= $arrWidget['color_text'];
		$intStatus					= $arrWidget['status'];
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams			= $this->_getAllParams();
			
			// Set Post Variables
			$strName				= $arrPostParams['name'];
			$strSize				= $arrPostParams['size'];
			$strColorBackground		= $arrPostParams['color_background'];
			$strColorText			= $arrPostParams['color_text'];
			$intStatus				= $arrPostParams['status'];
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else { 
				
				$arrWidgetData = array(
					'name' 				=> $strName,
					'size'				=> $strSize,
					'color_background'	=> $strColorBackground,
					'color_text'		=> $strColorText,
					'status'			=> $intStatus,
					'user_id'			=> KZ_Controller_Session::getActiveUser()
				);
				
				$intUpdateID		= $objModelWidget->updateWidget($arrWidget['widget_id'], $arrWidgetData);
				
				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated widget')));
					$this->_redirect('/admin/widgets/index/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the widget');
				}
				
			}
		
		}
		
		// Parse Variables to View
		$this->view->widget 			= $arrWidget;

		$this->view->name				= $strName;
		$this->view->size				= $strSize;
		$this->view->color_background	= $strColorBackground;
		$this->view->color_text			= $strColorText;
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
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget				= new KZ_Models_Widgets();
    	
    	// Get Widget
    	$arrWidget					= $objModelWidget->getWidgetByID($arrParams['id']);

    	// Check if Widget wasn't found
    	if(isset($arrWidget) && count($arrWidget) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find widget')));
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Get Widget Content
    	$arrWidgetContent				= $objModelWidget->getWidgetContent($arrWidget['widget_id']);
		
		// Set Default Variables
		$strName					= $arrWidget['name'];
		$strSize					= $arrWidget['size'];
		$strColorBackground			= $arrWidget['color_background'];
		$strColorText				= $arrWidget['color_text'];
		$intStatus					= $arrWidget['status'];
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			$intDeleteID		= $objModelWidget->deleteWidget($arrWidget['widget_id']);
				
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				
				$intDeleteID	= $objModelWidget->deleteWidgetContentByWidgetID($arrWidget['widget_id']);
				
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted widget')));
				$this->_redirect('/admin/widgets/index/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the widget');
			}
		
		}
		
		// Parse Variables to View
		$this->view->widget 			= $arrWidget;

		$this->view->name				= $strName;
		$this->view->size				= $strSize;
		$this->view->color_background	= $strColorBackground;
		$this->view->color_text			= $strColorText;
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
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget				= new KZ_Models_Widgets();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	$objModelMatches			= new KZ_Models_Matches();
    	
    	// Get Widget by ID
    	$arrWidget					= $objModelWidget->getWidgetByID($arrParams['id']);
    	
		// Check if category was found
    	if(! isset($arrWidget) || ! is_array($arrWidget) || count($arrWidget) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find widget')));
    		$this->_redirect('/admin/widgets/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get Widget Content By Widget ID
    	$arrWidgetContent			= $objModelWidget->getWidgetContent($arrWidget['widget_id']);
    	
    	// Get Last Widget Rank
    	$intLastWidgetRank			= $objModelWidget->getLastWidgetRank($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('widgets','content_type_id');
    	
    	// Set Default Team array
    	$arrDistinctTeams			= array();
    	
    	// Get All teams
		$arrTeams					= $objModelMatches->getTeams();
		
		// Loop through Teams
		if(isset($arrTeams) && is_array($arrTeams) && count($arrTeams) > 0) {
			foreach($arrTeams as $intTeamKey => $arrTeams) {
				
				if(strstr($arrTeams['team_home_name'], 'KZ/Hiltex')) {
					
					$strHomeTeam		= str_replace('/Hiltex ', '', $arrTeams['team_home_name']);
					
					if(! in_array($strHomeTeam, $arrDistinctTeams)) {
						$arrDistinctTeams[]	= $strHomeTeam;
					}
				}
				
				if(strstr($arrTeams['team_away_name'], 'KZ/Hiltex')) {
					
					$strAwayTeam		= str_replace('/Hiltex ', '', $arrTeams['team_away_name']);
					
					if(! in_array($strAwayTeam, $arrDistinctTeams)) {
						$arrDistinctTeams[]	= $strAwayTeam;
					}
					
				}
				
			}	
		}
		
		// Sort Array by values
		asort($arrDistinctTeams);
		
    	// Set Default Variables
    	$intContentTypeID 			= 0;
    	$strName					= '';
    	$intStatus					= 0;
    	
		$strContent3Url 			= '';
		$strContent4Username		= '';
		$strContent5Title 			= '';
		
		$strContent6Title			= '';
		$strContent6Location		= 'http://';
		$strContent6Target			= '_self';
		
		$strContent7Text			= '';
		
		$strContent8Home 			= '';
	    $strContent8Away 			= '';
	    $strContent8Details			= '';
	    
	    $arrContent9Teams			= array();

	    $arrContent10Teams			= array();
	    
	    $strContent12Image			= '';
	    $strContent12Location		= '';
		$strContent12Target			= '';
		
		$strContent14Youtube		= '';
    	
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
    			$arrWidgetData = array(
    				'widget_id'			=> $arrWidget['widget_id'],
    				'content_type_id'	=> $intContentTypeID,
    				'rank'				=> ($intLastWidgetRank + 1),
    				'name'				=> $strName,
    				'status'			=> $intStatus,
    				'user_id'			=> KZ_Controller_Session::getActiveUser()
    			);
    			
	    		if(isset($intActiveContentType) && ! empty($intActiveContentType) && is_numeric($intActiveContentType)) {
	    			
	    			// Facebook
	    			if($intActiveContentType == 3) {
	    				
	    				// Set Post Variables
	    				$strContent3Url 	= $arrPostParams['content_3_url'];
	    				
	    				if(empty($strContent3Url)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in an url');
	    				} else {
	    					
	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_3_url'	=> $strContent3Url
	    												));
	    					
	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Twitter
	    			if($intActiveContentType == 4) {
	    				
	    				// Set Post Variables
	    				$strContent4Username		= $arrPostParams['content_4_username'];

	    				if(empty($strContent4Username)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in an username');
	    				} else {
	    					
	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_4_username'	=> $strContent4Username
	    												));
	    					
	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    					
	    				}
	    				
	    			}
	    			
	    			// Title
	    			if($intActiveContentType == 5) {
	    				
	    				// Set Post Variables
	    				$strContent5Title 		= $arrPostParams['content_5_title'];
	    				
	    				if(empty($strContent5Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_5_title'	=> $strContent5Title
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Link (bottom)
	    			if($intActiveContentType == 6) {
						
	    				// Set Post Variables
	    				$strContent6Title 		= $arrPostParams['content_6_title'];
	    				$strContent6Location	= $arrPostParams['content_6_location'];
	    				$strContent6Target		= $arrPostParams['content_6_target'];
	    				
	    				if(empty($strContent6Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} elseif(empty($strContent6Location)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a location');
	    				} elseif(empty($strContent6Target)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a target');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_6_title'		=> $strContent6Title,
	    													'content_6_location'	=> $strContent6Location,
	    													'content_6_target'		=> $strContent6Target
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Text
	    			if($intActiveContentType == 7) {
						
	    				// Set Post Variables
	    				$strContent7Text 		= $arrPostParams['content_7_text'];
	    				
	    				if(empty($strContent7Text)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a text');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_7_text'		=> $strContent7Text
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Next Match
	    			if($intActiveContentType == 8) {
						
	    				// Set Post Variables
	    				$strContent8Home 		= $arrPostParams['content_8_home'];
	    				$strContent8Away 		= $arrPostParams['content_8_away'];
	    				$strContent8Details		= $arrPostParams['content_8_details'];
	    				
	    				if(empty($strContent8Home)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a home team');
	    				} elseif(empty($strContent8Away)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a away team');
	    				} elseif(empty($strContent8Details)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in the details');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_8_home'	=> $strContent8Home,
	    													'content_8_away'	=> $strContent8Away,
	    													'content_8_details'		=> $strContent8Details,
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Program
	    			if($intActiveContentType == 9) {
						
	    				// Set Defaults
	    				$arrActiveTags			= array();
	    				
	    				// Loop through Post for selected Tags
						foreach($arrPostParams as $strPostKey => $strPostValue) {
							// Check if tag was found
							if(strstr($strPostKey, 'team-program-')) {
								// Add Tag to active Tags array
								$arrActiveTags[] = $strPostValue;
							}
						}
	    				
	    				if(count($arrActiveTags) == 0) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any team');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_9_teams'	=> implode(',', $arrActiveTags)
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Results
	    			if($intActiveContentType == 10) {
						
	    				// Set Defaults
	    				$arrContent10Teams			= array();
	    				
	    				// Loop through Post for selected Tags
						foreach($arrPostParams as $strPostKey => $strPostValue) {
							// Check if tag was found
							if(strstr($strPostKey, 'team-results-')) {
								// Add Tag to active Tags array
								$arrContent10Teams[] = $strPostValue;
							}
						}
	    				
	    				if(count($arrContent10Teams) == 0) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any team');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_10_teams'	=> implode(',', $arrContent10Teams)
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Guestbook
	    			if($intActiveContentType == 11) {
						
    					// Add content data to array
    					$arrWidgetData['data']	= '';

    					// Add Widget Content
    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

    					// Check if Widget Content was succesfully updated
    					if(isset($intInsertID) && is_numeric($intInsertID)) {
							$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
							$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
						} else {
							// Return feedback
			    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
						}
	    				
	    			}
	    			
	    			// Livestream
	    			if($intActiveContentType == 12) {
						
	    				// Set Post Variables
	    				$strContent12Image 		= $arrPostParams['content_12_image'];
	    				$strContent12Location 	= $arrPostParams['content_12_location'];
	    				$strContent12Target 	= $arrPostParams['content_12_target'];
	    				
	    				if(empty($strContent12Image)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
	    				} elseif(empty($strContent12Location)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a location');
	    				} elseif(empty($strContent12Target)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a target');
	    				} else {
	    				
	    					// Add content data to array
	    					$arrWidgetData['data']	= 	serialize(array(	
	    													'content_12_image'		=> str_replace(ROOT_URL.'/upload/', '', $strContent12Image),
	    													'content_12_location'	=> $strContent12Location,
		    												'content_12_target'		=> $strContent12Target
		    											));
	
	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);
	
	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
							}
		    				
		    			}
		    			
	    			}
	    			
	    			// Youtube
	    			if($intActiveContentType == 14) {

	    				// Set Post Variables
	    				$strContent14Youtube 		= $arrPostParams['content_14_youtube'];
	    				
	    				if(empty($strContent14Youtube)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a youtube stream');
	    				} else {
	    					
	    					// Add content data to array
	    					$arrWidgetData['data']	= 	serialize(array(
	    							'content_14_youtube'	=> $strContent14Youtube
	    					));
	    					
	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);
	    					
	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
	    						$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
	    						$this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
	    					} else {
	    						// Return feedback
	    						$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
	    					}
	    					
	    				}
	    				
	    			}

                    // Blog
                    if($intActiveContentType == 15) {

                        // Add content data to array
                        $arrWidgetData['data']= '';

                        // Add Widget Content
                        $intInsertID 		= $objModelWidget->addWidgetContent($arrWidgetData);

                        // Check if Widget Content was succesfully updated
                        if(isset($intInsertID) && is_numeric($intInsertID)) {
                            $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added content')));
                            $this->_redirect('/admin/widgets/content/id/'.$arrWidget['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
                        } else {
                            // Return feedback
                            $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the content');
                        }

                    }

	    		}
    		
    		}
    		
    	}
    	
    	// Initialize Wysiwyg Editor
		$this->view->editorInit			= KZ_Controller_Editor::setEditor('tinymce'); 
    	
    	// Parse Data to View
    	$this->view->widget				= $arrWidget;
    	$this->view->widgets_content	= $arrWidgetContent;
    	$this->view->contentTypes 		= $arrContentTypes;
    	
    	$this->view->content_type_id 	= $intContentTypeID;
    	$this->view->name 				= $strName;
    	$this->view->status			 	= $intStatus;
    	
    	$this->view->teams				= $arrDistinctTeams;
    	
    	$this->view->content_3_url		= $strContent3Url;
		$this->view->content_4_username	= $strContent4Username;
		$this->view->content_5_title	= $strContent5Title;
		
		$this->view->content_6_title	= $strContent6Title;
		$this->view->content_6_location	= $strContent6Location;
		$this->view->content_6_target	= $strContent6Target;
		
		$this->view->content_7_text		= $strContent7Text;
		
		$this->view->content_8_home		= $strContent8Home;
	    $this->view->content_8_away		= $strContent8Away;
	    $this->view->content_8_details	= $strContent8Details;
	    
	    $this->view->content_9_teams	= $arrContent9Teams;

    	$this->view->content_10_teams	= $arrContent10Teams;
    	
    	$this->view->content_12_image		= $strContent12Image;
    	$this->view->content_12_location	= $strContent12Location;
		$this->view->content_12_target		= $strContent12Target;
		
		$this->view->content_14_youtube	= $strContent14Youtube;

	}
	
	public function contenteditAction()
	{
		
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget				= new KZ_Models_Widgets();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	$objModelMatches			= new KZ_Models_Matches();
    	
    	// Get Widget Content by ID
    	$arrWidgetContent				= $objModelWidget->getWidgetContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('widgets','content_type_id');
    	
    	// Set Default Team array
    	$arrDistinctTeams			= array();
    	
    	// Get All teams
		$arrTeams					= $objModelMatches->getTeams();
		
		// Loop through Team
		if(isset($arrTeams) && is_array($arrTeams) && count($arrTeams) > 0) {
			foreach($arrTeams as $intTeamKey => $arrTeams) {
				
				if(strstr($arrTeams['team_home_name'], 'KZ/Hiltex')) {
					
					$strHomeTeam		= str_replace('/Hiltex ', '', $arrTeams['team_home_name']);
					
					if(! in_array($strHomeTeam, $arrDistinctTeams)) {
						$arrDistinctTeams[]	= $strHomeTeam;
					}
				}
				
				if(strstr($arrTeams['team_away_name'], 'KZ/Hiltex')) {
					
					$strAwayTeam		= str_replace('/Hiltex ', '', $arrTeams['team_away_name']);
					
					if(! in_array($strAwayTeam, $arrDistinctTeams)) {
						$arrDistinctTeams[]	= $strAwayTeam;
					}
					
				}
				
			}	
		}
		
		// Sort Array by values
		asort($arrDistinctTeams);
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrWidgetContent['content_type_id'];
    	$strName					= $arrWidgetContent['name'];
    	$intStatus					= $arrWidgetContent['status'];
    	
		$strContent3Url 			= '';
		$strContent4Username		= '';
		$strContent5Title 			= '';
		
		$strContent6Title			= '';
		$strContent6Location		= '';
		$strContent6Target			= '';
		
		$strContent7Text			= '';
		
		$strContent8Home 			= '';
	    $strContent8Away 			= '';
	    $strContent8Details			= '';
	    
	    $arrContent9Teams			= array();
	    $arrContent10Teams			= array();
	    
	    $strContent12Image			= '';
	    $strContent12Location		= '';
		$strContent12Target			= '';
		
		$strContent14Youtube		= '';
		
		// Set Default Content Data
		$arrData					= unserialize($arrWidgetContent['data']);
		
		if($intContentTypeID == 3) {
			$strContent3Url				= $arrData['content_3_url'];
		}
		
		if($intContentTypeID == 4) {
			$strContent4Username		= $arrData['content_4_username'];
		}
		
		if($intContentTypeID == 5) {
			$strContent5Title			= $arrData['content_5_title'];
		}
		
		if($intContentTypeID == 6) {
			$strContent6Title			= $arrData['content_6_title'];
			$strContent6Location		= $arrData['content_6_location'];
			$strContent6Target			= $arrData['content_6_target'];
		}
		
		if($intContentTypeID == 7) {
			$strContent7Text			= $arrData['content_7_text'];
		}
		
		if($intContentTypeID == 8) {
			$strContent8Home			= $arrData['content_8_home'];
			$strContent8Away			= $arrData['content_8_away'];
			$strContent8Details			= $arrData['content_8_details'];
		}
		
		if($intContentTypeID == 9) {
			$arrContent9Teams			= ((! empty($arrData['content_9_teams'])) ? explode(',', $arrData['content_9_teams']) : array());
		}
		
		if($intContentTypeID == 10) {
			$arrContent10Teams			= ((! empty($arrData['content_10_teams'])) ? explode(',', $arrData['content_10_teams']) : array());
		}
		
		if($intContentTypeID == 12) {
			$strContent12Image			= $arrData['content_12_image'];
			$strContent12Location		= $arrData['content_12_location'];
			$strContent12Target			= $arrData['content_12_target'];
		}
		
		if($intContentTypeID == 14) {
			$strContent14Youtube		= $arrData['content_14_youtube'];
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
    			$arrWidgetData = array(
    				'widget_id'			=> $arrWidgetContent['widget_id'],
    				'content_type_id'	=> $intContentTypeID,
    				'name'				=> $strName,
    				'status'			=> $intStatus,
    				'user_id'			=> KZ_Controller_Session::getActiveUser()
    			);
    			
	    		if(isset($intActiveContentType) && ! empty($intActiveContentType) && is_numeric($intActiveContentType)) {
	    			
	    			// Facebook
	    			if($intActiveContentType == 3) {
	    				
	    				// Set Post Variables
	    				$strContent3Url 	= $arrPostParams['content_3_url'];
	    				
	    				if(empty($strContent3Url)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in an url');
	    				} else {
	    					
	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_3_url'	=> $strContent3Url
	    												));
	    					
	    					// Update Widget Content
	    					$intUpdateID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
	    						
	    						// Get Last Revision from Widget Content Backup
	    						$intLastRevision		= $objModelWidget->getLastWidgetContentBackupRevision($arrWidgetContent['widget_content_id']);
	    						
	    						// Set Widget Content Backup array
	    						$arrWidgetContentBackup 	= array(
	    							'widget_content_id' 	=> $arrWidgetContent['widget_content_id'],
	    							'widget_id'			=> $arrWidgetContent['widget_id'],
	    							'revision'			=> ($intLastRevision + 1),
	    							'content_type_id'	=> $arrWidgetContent['content_type_id'],
	    							'name'				=> $arrWidgetContent['name'],
	    							'data'				=> $arrWidgetContent['data'],
	    							'status'			=> $arrWidgetContent['status'],
	    							'user_id'			=> KZ_Controller_Session::getActiveUser()
	    						);
	    						
	    						// Backup overwritten Widget Content
	    						$intInsertID			= $objModelWidget->addWidgetContentBackup($arrWidgetContentBackup);
	    						
	    						
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Twitter
	    			if($intActiveContentType == 4) {
	    				
	    				// Set Post Variables
	    				$strContent4Username 	= $arrPostParams['content_4_username'];
	    				
	    				if(empty($strContent4Username)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in an username');
	    				} else {
	    					
	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_4_username'	=> $strContent4Username
	    												));
	    					
	    					// Update Widget Content
	    					$intUpdateID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    					
	    				}
	    				
	    			}
	    			
	    			// Title
	    			if($intActiveContentType == 5) {
	    				
	    				// Set Post Variables
	    				$strContent5Title 	= $arrPostParams['content_5_title'];
	    				
	    				if(empty($strContent5Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_5_title'	=> $strContent5Title
	    												));

	    					// Update Widget Content
	    					$intUpdateID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Link (bottom)
	    			if($intActiveContentType == 6) {
						
	    				// Set Post Variables
	    				$strContent6Title 		= $arrPostParams['content_6_title'];
	    				$strContent6Location	= $arrPostParams['content_6_location'];
	    				$strContent6Target		= $arrPostParams['content_6_target'];
	    				
	    				if(empty($strContent6Title)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
	    				} elseif(empty($strContent6Location)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a location');
	    				} elseif(empty($strContent6Target)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a target');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_6_title'		=> $strContent6Title,
	    													'content_6_location'	=> $strContent6Location,
	    													'content_6_target'		=> $strContent6Target
	    												));

	    					// Add Widget Content
	    					$intUpdateID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Text
	    			if($intActiveContentType == 7) {
						
	    				// Set Post Variables
	    				$strContent7Text 		= $arrPostParams['content_7_text'];
	    				
	    				if(empty($strContent7Text)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a text');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_7_text'		=> $strContent7Text
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'],$arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Next Match
	    			if($intActiveContentType == 8) {
						
	    				// Set Post Variables
	    				$strContent8Home 		= $arrPostParams['content_8_home'];
	    				$strContent8Away 		= $arrPostParams['content_8_away'];
	    				$strContent8Details		= $arrPostParams['content_8_details'];
	    				
	    				if(empty($strContent8Home)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a home team');
	    				} elseif(empty($strContent8Away)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a away team');
	    				} elseif(empty($strContent8Details)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in the details');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_8_home'	=> $strContent8Home,
	    													'content_8_away'	=> $strContent8Away,
	    													'content_8_details'		=> $strContent8Details,
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Program
	    			if($intActiveContentType == 9) {
						
	    				// Set Defaults
	    				$arrContent9Teams			= array();
	    				
	    				// Loop through Post for selected Tags
						foreach($arrPostParams as $strPostKey => $strPostValue) {
							// Check if tag was found
							if(strstr($strPostKey, 'team-program-')) {
								// Add Tag to active Tags array
								$arrContent9Teams[] = $strPostValue;
							}
						}
	    				
	    				if(count($arrContent9Teams) == 0) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any team');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_9_teams'	=> implode(',', $arrContent9Teams)
	    												));

	    					// Add Widget Content
	    					$intInsertID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Results
	    			if($intActiveContentType == 10) {
						
	    				// Set Defaults
	    				$arrContent10Teams			= array();
	    				
	    				// Loop through Post for selected Tags
						foreach($arrPostParams as $strPostKey => $strPostValue) {
							// Check if tag was found
							if(strstr($strPostKey, 'team-results-')) {
								// Add Tag to active Tags array
								$arrContent10Teams[] = $strPostValue;
							}
						}
	    				
	    				if(count($arrContent10Teams) == 0) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any team');
	    				} else {

	    					// Add content data to array
	    					$arrWidgetData['data']	= serialize(array(	
	    													'content_10_teams'	=> implode(',', $arrContent10Teams)
	    												));

	    					// Update Widget Content
	    					$intInsertID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

	    					// Check if Widget Content was succesfully updated
	    					if(isset($intInsertID) && is_numeric($intInsertID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    					
	    				}
	    				
	    			}
	    			
	    			// Guestbook
	    			if($intActiveContentType == 11) {
						
    					// Add content data to array
    					$arrWidgetData['data']	= '';

	    				// Update Widget Content
    					$intInsertID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);

    					// Check if Widget Content was succesfully updated
    					if(isset($intInsertID) && is_numeric($intInsertID)) {
							$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
							$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
						} else {
							// Return feedback
			    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
						}
	    				
	    			}
	    			
	    			// Livestream
	    			if($intActiveContentType == 12) {
						
	    				// Set Post Variables
	    				$strContent12Image 		= $arrPostParams['content_12_image'];
	    				$strContent12Location 	= $arrPostParams['content_12_location'];
	    				$strContent12Target 	= $arrPostParams['content_12_target'];
	    				
	    				if(empty($strContent12Image)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
	    				} elseif(empty($strContent12Location)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a location');
	    				} elseif(empty($strContent12Target)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a target');
	    				} else {
	    				
	    					// Add content data to array
	    					$arrWidgetData['data']	= 	serialize(array(	
		    												'content_12_image'		=> str_replace(ROOT_URL.'/upload/', '', $strContent12Image),
	    													'content_12_location'	=> $strContent12Location,
		    												'content_12_target'		=> $strContent12Target
		    											));
	
		    				// Add Widget Content
	    					$intUpdateID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);
	
	    					// Check if Widget Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
							} else {
								// Return feedback
				    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the content');
							}
	    				
	    				}
							
	    			}
	    			
	    			// Youtube
	    			if($intActiveContentType == 14) {
	    			
	    				// Set Post Variables
	    				$strContent14Youtube 		= $arrPostParams['content_14_youtube'];
	    				 
	    				if(empty($strContent14Youtube)) {
	    					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a youtube stream');
	    				} else {
	    			
	    					// Add content data to array
	    					$arrWidgetData['data']	= 	serialize(array(
	    							'content_14_youtube'	=> $strContent14Youtube
	    					));
	    			
	    					// Add Widget Content
	    					$intUpdateID 		= $objModelWidget->updateWidgetContent($arrWidgetContent['widget_content_id'], $arrWidgetData);
	
	    					// Check if Widget Content was succesfully updated
	    					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
								$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated content')));
								$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/#tab0');
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
    	$this->view->widget_content		= $arrWidgetContent;
    	$this->view->contentTypes 		= $arrContentTypes;
    	
    	$this->view->content_type_id 	= $intContentTypeID;
    	$this->view->name 				= $strName;
    	$this->view->status			 	= $intStatus;
    	
    	$this->view->teams				= $arrDistinctTeams;
    	
    	$this->view->content_3_url		= $strContent3Url;
		$this->view->content_4_username	= $strContent4Username;
		$this->view->content_5_title	= $strContent5Title;
		
		$this->view->content_6_title	= $strContent6Title;
		$this->view->content_6_location	= $strContent6Location;
		$this->view->content_6_target	= $strContent6Target;
		
		$this->view->content_7_text		= $strContent7Text;
		
		$this->view->content_8_home		= $strContent8Home;
	    $this->view->content_8_away		= $strContent8Away;
	    $this->view->content_8_details	= $strContent8Details;
	    
	    $this->view->content_9_teams	= $arrContent9Teams;

    	$this->view->content_10_teams	= $arrContent10Teams;
    	
    	$this->view->content_12_image		= $strContent12Image;
    	$this->view->content_12_location	= $strContent12Location;
		$this->view->content_12_target		= $strContent12Target;
		
		$this->view->content_14_youtube	= $strContent14Youtube;

	}
	
	public function contentdeleteAction()
	{

		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget				= new KZ_Models_Widgets();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Widget Content by ID
    	$arrWidgetContent				= $objModelWidget->getWidgetContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('widgets','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrWidgetContent['content_type_id'];
    	$strName					= $arrWidgetContent['name'];
    	$intStatus					= $arrWidgetContent['status'];
		
		// Set Default Content Data
		$arrData					= unserialize($arrWidgetContent['data']);
		
    	// Check for Post
    	if($this->getRequest()->isPost()) {
    		
    		// Delete Widget Content
			$intDeleteID 				= $objModelWidget->deleteWidgetContent($arrWidgetContent['widget_content_id']);

			// Check if Widget Content was succesfully updated
	    	if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted content')));
				$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContent['widget_id'].'/feedback/'.$strFeedback.'/');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the content');
			}

	    }
    	
    	// Parse Data to View
    	$this->view->widget_content		= $arrWidgetContent;
    	$this->view->contentTypes 		= $arrContentTypes;
    	
    	$this->view->content_type_id 	= $intContentTypeID;
    	$this->view->name 				= $strName;
    	$this->view->status			 	= $intStatus;
    	
    	$this->view->data				= $arrData;

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
    		$objModelWidget 		= new KZ_Models_Widgets();
    		
    		// Loop through rank
    		foreach($arrParams['rank'] as $intRankKey => $strRankData) {
    			
    			$intWidgetContentID	= str_replace('content_', '', $strRankData);
    			$intUpdateID 		= $objModelWidget->updateWidgetsContentRank($intWidgetContentID, ($intRankKey + 1));
    			
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
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget				= new KZ_Models_Widgets();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
		// Get Widget Content
    	$arrWidgetContent				= $objModelWidget->getWidgetContentByID($arrParams['id']);
    	
		// Check if Widget was found
    	if(! isset($arrWidgetContent) || ! is_array($arrWidgetContent) || count($arrWidgetContent) == 0) {
    		// return feedback
    		$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find widget content')));
    		$this->_redirect('/admin/widgets/index/feedback/'.$strFeedback.'/#tab0/');
    	}
    	
    	// Get Widget Content Backups by ID
    	$arrWidgetContentBackup		= $objModelWidget->getWidgetContentBackups($arrWidgetContent['widget_content_id']);
    	
    	// Get Widget
    	$arrWidget					= $objModelWidget->getWidgetByID($arrWidgetContent['widget_id']);

    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('widgets','content_type_id');
    	
    	// Parse Data to View
    	$this->view->widget					= $arrWidget;
    	$this->view->widget_content_backups	= $arrWidgetContentBackup;
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
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget				= new KZ_Models_Widgets();
    	$objModelContentType		= new KZ_Models_Contenttype();
    	
    	// Get Widget Content Backup by ID
    	$arrWidgetContentBackup		= $objModelWidget->getWidgetBackupContentByID($arrParams['id']);
    	
    	// Get Content Types
    	$arrContentTypes			= $objModelContentType->getContentTypes('widgets','content_type_id');
    	
    	// Set Default Variables
    	$intContentTypeID 			= $arrWidgetContentBackup['content_type_id'];
    	$strName					= $arrWidgetContentBackup['name'];
    	$intStatus					= $arrWidgetContentBackup['status'];
    	
    	$strContent1Title			= '';
    	$strContent1Text			= '';
    	
		$strContent2Title 			= '';
 		$strContent2Image 			= '';
		$strContent2Text 			= '';
    	
		// Set Default Content Data
		$arrData					= unserialize($arrWidgetContentBackup['data']);
		
		if($intContentTypeID == 1) {
			$strContent1Title			= $arrData['content_1_title'];
    		$strContent1Text			= $arrData['content_1_text'];
		}
		
		if($intContentTypeID == 2) {
			$strContent2Title			= $arrData['content_2_title'];
			$strContent2Image			= $arrData['content_2_image'];
    		$strContent2Text			= $arrData['content_2_text'];
		}
		
    	// Parse Data to View
    	$this->view->widget_content_backup	= $arrWidgetContentBackup;
    	$this->view->contentTypes 			= $arrContentTypes;
    	
    	$this->view->content_type_id 		= $intContentTypeID;
    	$this->view->name 					= $strName;
    	$this->view->status			 		= $intStatus;
    	
    	$this->view->content_1_title		= $strContent1Title;
    	$this->view->content_1_text			= $strContent1Text;
    	
    	$this->view->content_2_title		= $strContent2Title;
    	$this->view->content_2_image		= $strContent2Image;
    	$this->view->content_2_text			= $strContent2Text;
	}
	
	public function contentrestoreAction()
	{
		// Get All Params
		$arrParams 		= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/widgets/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelWidget			= new KZ_Models_Widgets();
    	
    	// Get Widget Backup By ID
    	$arrWidgetContentBackup	= $objModelWidget->getWidgetBackupContentByID($arrParams['id']);
		
    	// Set Widget Content Update array
    	$arrWidgetContentUpdate	= array(
    		'content_type_id'	=> $arrWidgetContentBackup['content_type_id'],
	    	'name'				=> $arrWidgetContentBackup['name'],
    		'data'				=> $arrWidgetContentBackup['data'],
    		'status'			=> $arrWidgetContentBackup['status'],
    		'user_id'			=> KZ_Controller_Session::getActiveUser()
    	);
    	
		// Update Widget Content
		$intUpdateID			= $objModelWidget->updateWidgetContent($arrWidgetContentBackup['widget_content_id'], $arrWidgetContentUpdate);
    	
		// Check if update was succesfull
		if(is_numeric($intUpdateID)) {
			$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully restored backup')));
			$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContentBackup['widget_id'].'/feedback/'.$strFeedback.'/');
		} else {
			// Return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Something went wrong trying to restore the backup')));
			$this->_redirect('/admin/widgets/content/id/'.$arrWidgetContentBackup['widget_id'].'/feedback/'.$strFeedback.'/');
		}
    	
	}
	
	public function mailingsAction()
	{

	}

	public function layoutAction()
	{
		// Set Models
		$objModelWidgets		= new KZ_Models_Widgets();
		
		// Get Layouts
		$arrLayouts				= $objModelWidgets->getWidgetLayouts();
		
		// Get Widgets
		$arrWidgets				= $objModelWidgets->getWidgets('widget_id');
		
		// Set Default Variables
		$strName				= '';
		$intStatus				= '';
		$arrSelectedWidgets		= array();
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Set Defaults
			$strSelectedWidgets	= '';
			$arrSelectedWidgets	= array();
			
			// Set Post Params
			$arrPostParams		= $this->_getAllParams();
			
			// Loop through Post Params
			foreach($arrPostParams as $strPostKey => $strPostValue) {

				// Check if widget was selected
				if(strstr($strPostKey, 'widget_')) {

					// Explode widget key
					$arrWidgetData			= explode('_', $strPostKey);
					
					// Add Selected Widget to array
					$arrSelectedWidgets[]	= $arrWidgetData[1];

				}
				
			}
			
			// Set Post Variables
			$strName			= $arrPostParams['name'];
			$intStatus			= $arrPostParams['status'];
			
			// Set Selected Widgets string
			if(count($arrSelectedWidgets) > 0) {
				$strSelectedWidgets	= implode(',', $arrSelectedWidgets);
			}
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strSelectedWidgets)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any widgets');
			} else {

				// Set Layout Data
				$arrLayoutData	= array(
					'name'			=> $strName,
					'structure' 	=> $strSelectedWidgets,
					'status'		=> $intStatus,
					'user_id'		=> KZ_Controller_Session::getActiveUser()
				);
				
				// Add Layout Data
				$intInsertID	= $objModelWidgets->addWidgetLayout($arrLayoutData);
				
				// Check if Insert was succesfull
				if(isset($intInsertID) && is_numeric($intInsertID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added layout')));
					$this->_redirect('/admin/widgets/layout/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the layout');
				}
				
			}
			
		}
		
		// Set Default Sorted Widgets array
		$arrSortedWidgets				= array();
		
		// Loop through all Widgets for sort order
		foreach($arrSelectedWidgets as $intWidgetID) {
			
			if(isset($arrWidgets[$intWidgetID]) && is_array(($arrWidgets[$intWidgetID]))) {
				
				$arrSortedWidgets[]	= $arrWidgets[$intWidgetID];
				unset($arrWidgets[$intWidgetID]);

			}

		}
		
		$arrSortedWidgets = array_merge($arrSortedWidgets, $arrWidgets);
		
		// Parse Variables to View
		$this->view->layouts			= $arrLayouts;
		$this->view->widgets			= $arrSortedWidgets;
		
		$this->view->name				= $strName;
		$this->view->status				= $intStatus;
		$this->view->selectedWidgets	= $arrSelectedWidgets;
	}
	
	public function layouteditAction()
	{
		// Get All Params
		$arrParams				= $this->_getAllParams();

		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/widgets/layout/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}

		// Set Models
		$objModelWidgets		= new KZ_Models_Widgets();

		// Get Layout
		$arrLayout				= $objModelWidgets->getWidgetLayout($arrParams['id']);

		// Check if Layout wasn't found
    	if(isset($arrLayout) && count($arrLayout) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find layout')));
    		$this->_redirect('/admin/widgets/layout/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
		
		// Get Widgets
		$arrWidgets				= $objModelWidgets->getWidgets('widget_id');
		
		// Set Default Variables
		$strName				= $arrLayout['name'];
		$intStatus				= $arrLayout['status'];
		$arrSelectedWidgets		= (($arrLayout['structure'] == '') ? array() : explode(',', $arrLayout['structure']));
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Set Defaults
			$strSelectedWidgets	= '';
			$arrSelectedWidgets	= array();
			
			// Set Post Params
			$arrPostParams		= $this->_getAllParams();
			
			// Loop through Post Params
			foreach($arrPostParams as $strPostKey => $strPostValue) {

				// Check if widget was selected
				if(strstr($strPostKey, 'widget_')) {

					// Explode widget key
					$arrWidgetData			= explode('_', $strPostKey);
					
					// Add Selected Widget to array
					$arrSelectedWidgets[]	= $arrWidgetData[1];

				}
				
			}
			
			// Set Post Variables
			$strName			= $arrPostParams['name'];
			$intStatus			= $arrPostParams['status'];
			
			// Set Selected Widgets string
			if(count($arrSelectedWidgets) > 0) {
				$strSelectedWidgets	= implode(',', $arrSelectedWidgets);
			}
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strSelectedWidgets)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select any widgets');
			} else {

				// Set Layout Data
				$arrLayoutData	= array(
					'name'			=> $strName,
					'structure' 	=> $strSelectedWidgets,
					'status'		=> $intStatus,
					'user_id'		=> KZ_Controller_Session::getActiveUser()
				);
				
				// Update Layout Data
				$intUpdateID	= $objModelWidgets->updateWidgetLayout($arrLayout['widget_layout_id'], $arrLayoutData);
				
				// Check if Update was succesfull
				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated layout')));
					$this->_redirect('/admin/widgets/layout/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the layout');
				}
				
			}
			
		}
		
		// Set Default Sorted Widgets array
		$arrSortedWidgets				= array();
		
		// Loop through all Widgets for sort order
		foreach($arrSelectedWidgets as $intWidgetID) {
			
			if(isset($arrWidgets[$intWidgetID]) && is_array(($arrWidgets[$intWidgetID]))) {
				
				$arrSortedWidgets[]	= $arrWidgets[$intWidgetID];
				unset($arrWidgets[$intWidgetID]);

			}

		}
		
		$arrSortedWidgets = array_merge($arrSortedWidgets, $arrWidgets);
		
		// Parse Variables to View
		$this->view->widgets			= $arrSortedWidgets;
		
		$this->view->name				= $strName;
		$this->view->status				= $intStatus;
		$this->view->selectedWidgets	= $arrSelectedWidgets;
	}
	
	public function layoutdeleteAction()
	{
		
		// Get All Params
		$arrParams				= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/widgets/layout/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
		// Set Models
		$objModelWidgets		= new KZ_Models_Widgets();
		
		// Get Layout
		$arrLayout				= $objModelWidgets->getWidgetLayout($arrParams['id']);
		
		// Check if Layout wasn't found
    	if(isset($arrLayout) && count($arrLayout) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find layout')));
    		$this->_redirect('/admin/widgets/layout/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
		
		// Get Widgets
		$arrWidgets				= $objModelWidgets->getWidgets('widget_id');
		
		// Set Default Variables
		$strName				= $arrLayout['name'];
		$intStatus				= $arrLayout['status'];
		$arrSelectedWidgets		= (($arrLayout['structure'] == '') ? array() : explode(',', $arrLayout['structure']));
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Delete Layout Data
			$intDeleteID	= $objModelWidgets->deleteWidgetLayout($arrLayout['widget_layout_id']);
			
			// Check if Delete was succesfull
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted layout')));
				$this->_redirect('/admin/widgets/layout/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the layout');
			}
			
		}
		
		// Set Default Sorted Widgets array
		$arrSortedWidgets				= array();
		
		// Loop through all Widgets for sort order
		foreach($arrSelectedWidgets as $intWidgetID) {
			
			if(isset($arrWidgets[$intWidgetID]) && is_array(($arrWidgets[$intWidgetID]))) {
				
				$arrSortedWidgets[]	= $arrWidgets[$intWidgetID];
				unset($arrWidgets[$intWidgetID]);

			}

		}
		
		$arrSortedWidgets = array_merge($arrSortedWidgets, $arrWidgets);
		
		// Parse Variables to View
		$this->view->widgets			= $arrSortedWidgets;
		
		$this->view->name				= $strName;
		$this->view->status				= $intStatus;
		$this->view->selectedWidgets	= $arrSelectedWidgets;
	}
	
}