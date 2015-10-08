<?php
class Admin_GuestbookController extends KZ_Controller_Action {

	public function indexAction()
	{
		// Set Models
		$objModelGuestbook		= new KZ_Models_Guestbook();
		
		// Get All Guestbook entries
		$arrGuestbookEntries	= $objModelGuestbook->getAllGuestbookEntries();
		
		// Parse variables to view
		$this->view->entries	= $arrGuestbookEntries;
	}
	
	public function showAction()
	{
		// Get All Params
		$arrParams 			= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/guestbook/index/feedback/'.$strSerializedFeedback.'/');
		}
		 
		// Set Models
		$objModelGuestbook		= new KZ_Models_Guestbook();
		 
		// Get Guestbook Entry
		$arrGuestbookEntry		= $objModelGuestbook->getGuestbookEntry($arrParams['id']);
		 
		// Check if Guestbook Entry wasn't found
		if(isset($arrGuestbookEntry) && is_array($arrGuestbookEntry) && count($arrGuestbookEntry) <= 0) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find guestbook entry')));
			$this->_redirect('/admin/guestbook/index/feedback/'.$strSerializedFeedback.'/');
		}
		 
		// Parse variables to view
		$this->view->entry			= $arrGuestbookEntry;
	}
	
	public function editAction()
	{
		// Get All Params
		$arrParams 			= $this->_getAllParams();
		
		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/guestbook/index/feedback/'.$strSerializedFeedback.'/');
		}
			
		// Set Models
		$objModelGuestbook		= new KZ_Models_Guestbook();
			
		// Get Guestbook Entry
		$arrGuestbookEntry		= $objModelGuestbook->getGuestbookEntry($arrParams['id']);
			
		// Check if Guestbook Entry wasn't found
		if(isset($arrGuestbookEntry) && is_array($arrGuestbookEntry) && count($arrGuestbookEntry) <= 0) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find guestbook entry')));
			$this->_redirect('/admin/guestbook/index/feedback/'.$strSerializedFeedback.'/');
		}
		
		// Set Default Variables
		$strName		= $arrGuestbookEntry['guestbook_name'];
		$strEmail		= $arrGuestbookEntry['guestbook_email'];
		$strMessage		= stripslashes($arrGuestbookEntry['guestbook_message']);
		$strStatus		= $arrGuestbookEntry['guestbook_entry_verified'];
		
		if($this->getRequest()->isPost()) {
			
			// Get Post Params
			$arrPostParams		= $this->_getAllParams();
			
			// Set Post Variables
			$strName			= $arrPostParams['name'];
			$strEmail			= $arrPostParams['email'];
			$strMessage			= stripslashes($arrPostParams['message']);
			$strStatus			= $arrPostParams['status'];
			
			// Set Validate Object
			$objValidateEmail	= new Zend_Validate_EmailAddress();
			
			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($strEmail)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in an email');
			} elseif(! $objValidateEmail->isValid($strEmail)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'Your email is not valid');
			} elseif(empty($strMessage)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'Your didn\'t fill in a message');
			} else {
				
				// Set Update Array
				$arrUpdateEntry = array(
					'guestbook_name'			=> $strName,
					'guestbook_email'			=> $strEmail,
					'guestbook_message'			=> $strMessage,
					'guestbook_entry_verified'	=> $strStatus						
				);
				
				$intUpdateID = $objModelGuestbook->update($arrUpdateEntry, "guestbook_entry_id = {$arrGuestbookEntry['guestbook_entry_id']}");
				
				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated guestbook entry')));
					$this->_redirect('/admin/guestbook/index/feedback/'.$strFeedback.'/');
				} else {
					// Return feedback
					$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the guestbook entry');
				}
				
			}
			
		}
			
		// Parse variables to view
		$this->view->entry			= $arrGuestbookEntry;
		$this->view->name			= $strName;
		$this->view->email			= $strEmail;
		$this->view->message		= $strMessage;
		$this->view->status			= $strStatus;
	}
	
	public function deleteAction()
	{
		// Get All Params
		$arrParams 			= $this->_getAllParams();
		
		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/guestbook/index/feedback/'.$strSerializedFeedback.'/');
		}
			
		// Set Models
		$objModelGuestbook		= new KZ_Models_Guestbook();
			
		// Get Guestbook Entry
		$arrGuestbookEntry		= $objModelGuestbook->getGuestbookEntry($arrParams['id']);
			
		// Check if Guestbook Entry wasn't found
		if(isset($arrGuestbookEntry) && is_array($arrGuestbookEntry) && count($arrGuestbookEntry) <= 0) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find guestbook entry')));
			$this->_redirect('/admin/guestbook/index/feedback/'.$strSerializedFeedback.'/');
		}
		
		if($this->getRequest()->isPost())
		{
			// Delete Guestbook entry
			$intDeleteID = $objModelGuestbook->delete("guestbook_entry_id = {$arrParams['id']}");

			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted guestbook entry')));
				$this->_redirect('/admin/guestbook/index/feedback/'.$strFeedback.'/');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the guestbook entry');
			}

		}	
		// Parse variables to view
		$this->view->entry			= $arrGuestbookEntry;
	}
	
	public function generateentriestableAction()
	{
		// Disable Layout and View
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 
		// Set Models
		$objModelGuestbook		= new KZ_Models_Guestbook();
		$objTranslate			= new KZ_View_Helper_Translate();
		 
		// Set the Columns
		$arrColumns				= array(
			$objTranslate->translate('ID'),
			$objTranslate->translate('Name'),
			$objTranslate->translate('Email'),
			$objTranslate->translate('Message'),
			$objTranslate->translate('Date'),
			$objTranslate->translate('Status'),
			$objTranslate->translate('Options')
		);
		
		// Set the DB Table Columns
		$arrTableColums			= array(
			'guestbook_entry_id',
			'guestbook_name',
			'guestbook_email',
			'guestbook_message',
			'guestbook_entry_date',
			'guestbook_entry_verified'
		);
		
	    // Set the Search
	    $strSearchData			= null;
    	if($_POST['sSearch'] != "") {
    		$strSearchString		= $_POST['sSearch'];
    	
    		if(is_numeric($strSearchString)) {
    			$strSearchData		= "guestbook_entry_id LIKE '%".$strSearchString."%'";
    		} else {
    			$strSearchData		= "(guestbook_name LIKE '%".$strSearchString."%' OR guestbook_email LIKE '%".$strSearchString."%' OR guestbook_message LIKE '%".$strSearchString."%')";
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
    	$intTotalEntries			= $objModelGuestbook->getEntriesForTable(true);
    	
    	// Select all Entries
    	$objEntries 				= $objModelGuestbook->getEntriesForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
    	$arrEntries					= $objEntries->toArray();

    	// Create the JSON Data
    	$output 					= array("sEcho" 				=> intval($_POST['sEcho']),
							    			"iTotalRecords" 		=> $intTotalEntries,
							    			"iTotalDisplayRecords" 	=> $intTotalEntries,
							    			"aaData"				=> array()
    	);
    	
    	if(!empty($arrEntries)) {
    		foreach($arrEntries as $key => $arrEntry) {

    			$row = array();
    			for($i = 0; $i < count($arrColumns); $i++) {
    				if($arrColumns[$i] != ' ') {
    					
    					if(isset($arrTableColums[$i])) {
    						if($arrTableColums[$i] == 'guestbook_entry_verified') {
    							$strRowData		= '<span class="tag '.(($arrEntry[$arrTableColums[$i]] == 'y') ? 'green' : 'red').'">'.$objTranslate->translate((($arrEntry[$arrTableColums[$i]] == 'y') ? 'Active' : 'Inactive')).'</span>';
    						} elseif($arrTableColums[$i] == 'guestbook_entry_date') { 
    							$strRowData		= KZ_View_Helper_Date::format($arrEntry[$arrTableColums[$i]], 'dd-MM-YYYY');
    						} elseif($arrTableColums[$i] == 'guestbook_message') {
    							$strRowData		= substr(stripslashes(strip_tags($arrEntry[$arrTableColums[$i]])), 0, 75).'...';
    						} else {
    							$strRowData		= stripslashes($arrEntry[$arrTableColums[$i]]);
    						}
    					} else {
    						$strOptionsHtml = '<ul class="actions">
													<li><a rel="tooltip" href="/admin/guestbook/show/id/'.$arrEntry['guestbook_entry_id'].'/" class="view" original-title="'.$objTranslate->translate('Show entry').'">'.$objTranslate->translate('Show entry').'</a></li>
													<li><a rel="tooltip" href="/admin/guestbook/edit/id/'.$arrEntry['guestbook_entry_id'].'/" class="edit" original-title="'.$objTranslate->translate('Edit entry').'">'.$objTranslate->translate('Edit entry').'</a></li>
													<li><a rel="tooltip" href="/admin/guestbook/delete/id/'.$arrEntry['guestbook_entry_id'].'/" class="delete" original-title="'.$objTranslate->translate('Delete entry').'">'.$objTranslate->translate('Delete entry').'</a></li>
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