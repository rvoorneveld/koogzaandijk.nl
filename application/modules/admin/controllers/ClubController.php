<?php
class Admin_ClubController extends KZ_Controller_Action
{
	
	public function teamsAction()
	{

	}
	
	public function membersAction()
	{
		// Set Models
		$objModelMembers		= new KZ_Models_Members();
		
		// Get Members
		$arrMembers				= $objModelMembers->getMembers();
		
		// Parse Variables to View
		$this->view->members	= $arrMembers;
	}

	public function memberseditAction()
	{

		// Set Defaults
		$arrTeamsPlaying        = array();
		$arrTeamsCoaching       = array();

		// Get All Params
		$arrParams              = $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/club/teams/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Models
		$objModelMembers		= new KZ_Models_Members();

		// Get Member
		$arrMember				= $objModelMembers->getMember($arrParams['id']);

		// Check if Member was found
		if(empty($arrMember) || ! is_array($arrMember)) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find member')));
			$this->_redirect('/admin/club/teams/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Invited
		$intInvited             = $arrMember['invited'];
		$strInvitedDate         = ((! is_null($arrMember['invited_date'])) ? $this->view->date()->format($arrMember['invited_date'],'dd-MM-yyyy HH:mm:ss') : '');

		// Get Member Info
		$arrMemberInfo          = $objModelMembers->getMemberTeams($arrMember['members_id']);

		// Set Teams Playing and Teams Coaching
		if(! empty($arrMemberInfo) && is_array($arrMemberInfo)) {

			if(! empty($arrMemberInfo['player_teams'])) {
				$arrTeamsPlaying    = explode(',',$arrMemberInfo['player_teams']);
			}

			if(! empty($arrMemberInfo['coach_teams'])) {
				$arrTeamsCoaching   = explode(',',$arrMemberInfo['coach_teams']);
			}

		}

		// Get Teams
		$objModelTeams          = new KZ_Models_Teams();
		$arrTeams               = $objModelTeams->getTeams();

		// Set Assoc Teams
		$arrAssocTeams          = array();

		if(! empty($arrTeams) && is_array($arrTeams)) {
			foreach($arrTeams as $arrTeam) {
				$arrAssocTeams[$arrTeam['id']] = $arrTeam;
			}
		}

		// Set Defaults
		$arrDefaults            = $arrMember;

		// Check for Post
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams      = $this->_getAllParams();

			// Overwrite Defaults with Post Data
			$arrDefaults        = $arrPostParams;

			// Validate Email
			$objValidateEmail   = new Zend_Validate_EmailAddress();
			$booValidEmail      = $objValidateEmail->isValid($arrDefaults['email']);

			if(empty($arrDefaults['firstname'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a firstname');
			} elseif(empty($arrDefaults['lastname'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a lastname');
			} elseif(empty($arrDefaults['email'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in an email address');
			} elseif($booValidEmail !== true) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a valid email address');
			} else {

				// Check if password wasn't set yet
				if($arrMember['password'] == '' && $arrDefaults['password'] == '') {
					$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a password');
				} else {

					// Set Update Member array
					$arrUpdateMember    = array(
						'gender'        => $arrDefaults['gender'],
						'firstname'     => $arrDefaults['firstname'],
						'insertion'     => $arrDefaults['insertion'],
						'lastname'      => $arrDefaults['lastname'],
						'email'         => $arrDefaults['email']
					);

					if(! empty($arrDefaults['password'])) {

						// Encrypt Password
						$strEncryptedPassword       = md5($arrDefaults['password']);

						// Set Decrypted Password for Email
						$strDecryptedPassword       = $arrDefaults['password'];

						// Add Password to Update Data
						$arrUpdateMember['password'] = $strEncryptedPassword;

					} else {
						// Set Decrypted Password for Email
						$strDecryptedPassword        = false;
					}

					// Update Member
					$intUpdateID = $objModelMembers->updateMember($arrMember['members_id'],$arrUpdateMember);

					// Check for Succesfull update
					if(isset($intUpdateID) && is_numeric($intUpdateID)) {

						// Set Team Update Info
						$arrUpdateTeamsPlaying      = array();
						$arrUpdateTeamsCoaching     = array();

						foreach($arrDefaults as $strPostKey => $strPostValue) {
							if(strstr($strPostKey,'team-playing-')) {
								$arrUpdateTeamsPlaying[]    = $strPostValue;
							}
						}

						foreach($arrDefaults as $strPostKey => $strPostValue) {
							if(strstr($strPostKey,'team-coaching-')) {
								$arrUpdateTeamsCoaching[]    = $strPostValue;
							}
						}

						$strTeamsPlaying        = '';
						if(! empty($arrUpdateTeamsPlaying) && is_array($arrUpdateTeamsPlaying)) {
							$strTeamsPlaying    = implode(',',$arrUpdateTeamsPlaying);
						}

						$strTeamsCoaching       = '';
						if(! empty($arrUpdateTeamsCoaching) && is_array($arrUpdateTeamsCoaching)) {
							$strTeamsCoaching   = implode(',',$arrUpdateTeamsCoaching);
						}

						$arrUpdateTeams = array(
							'coach_teams'   => $strTeamsCoaching,
							'player_teams'  => $strTeamsPlaying
						);

						// Check if Member Info was already Found
						if(! empty($arrMemberInfo) && is_array($arrMemberInfo)) {
							// Update the data
							$objModelTeams->updateMembersTeams($arrMember['members_id'], $arrUpdateTeams);
						} else {

							// Add Members id to Teams array
							$arrUpdateTeams['members_id']   = $arrMember['members_id'];

							// Insert the Data
							$objModelTeams->insertMembersTeams($arrUpdateTeams);
						}

						// Check if Member must be invited by email
						if($arrPostParams['invited'] == 1) {

							// Set Mail Replace Data
							$arrMailData    = array(
								'firstname'     => $arrDefaults['firstname'],
								'insertion'     => $arrDefaults['insertion'],
								'lastname'      => $arrDefaults['lastname'],
								'email'         => $arrDefaults['email'],
								'root_url'      => ROOT_URL,
								'password'      => $strDecryptedPassword
							);

							// Set Body
							$strBodyHtml    = $this->view->partial('/mail/profile_invite.phtml', $arrMailData);
							$strBodyPlain   = nl2br(strip_tags($strBodyHtml));

							$objMail = new Zend_Mail();
							$objMail->setSubject('Uw profiel op koogzaandijk.nl');
							$objMail->setBodyHtml($strBodyHtml);
							$objMail->setBodyText($strBodyPlain);
							$objMail->addTo($arrDefaults['email'],$arrDefaults['firstname'].(($arrDefaults['insertion'] == '') ? ' ' : ' '.$arrDefaults['insertion'].' ').$arrDefaults['lastname']);
							$objMail->addBcc('rick@mediaconcepts.nl');
							$objMail->send();

							// Set Date Object
							$objDate = new Zend_Date();
							$strDate    = $objDate->toString('yyyy-MM-dd HH:mm:ss');

							$arrUpdateMember = array(
								'invited'       => 1,
								'invited_date'  => $strDate
							);

							$objModelMembers->updateMember($arrMember['members_id'], $arrUpdateMember);

						}

						// Return Feedback
						$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated member')));
						$this->_redirect('/admin/club/members/feedback/'.$strFeedback.'/#tab0');

					} else {
						// Return feedback
						$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the member');
					}

				}

			}

		}

		// Parse Variables to View
		$this->view->defaults	    = $arrDefaults;
		$this->view->teams_playing	= $arrTeamsPlaying;
		$this->view->teams_coaching	= $arrTeamsCoaching;
		$this->view->teams          = $arrAssocTeams;
		$this->view->invited        = $intInvited;
		$this->view->invited_date   = $strInvitedDate;

	}

	public function teamsmembersAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/pages/index/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Defaults
		$arrDefaults            = array(
			'team_role_id'  => 1,
			'gender'        => 'm',
			'firstname'     => '',
			'lastname'      => '',
			'link'          => ''
		);

		// Set Models
		$objModelTeams          = new KZ_Models_Teams();

		// Get Team Members
		$arrTeamInfo            = $objModelTeams->getTeamByTeamsId($arrParams['id']);
		$arrTeamMembers         = $objModelTeams->getTeamMembers($arrTeamInfo['name']);
		$arrTeamRoles           = $objModelTeams->getTeamRoles();

		// Check for Post
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrParams					= $this->_getAllParams();

			// Set Defaults
			$arrDefaults                = $arrParams;

			if(empty($arrDefaults['firstname'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a firstname');
			} elseif(empty($arrDefaults['lastname'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a lastname');
			} else {

				// Set Team Member Array
				$arrMemberData      = array(
					'team_id'       => $arrTeamInfo['name'],
					'team_role_id'  => $arrDefaults['team_role_id'],
					'gender'        => $arrDefaults['gender'],
					'firstname'     => $arrDefaults['firstname'],
					'lastname'      => $arrDefaults['lastname'],
					'link'          => $arrDefaults['link']
				);

				// Add Team member
				$intInsertID		= $objModelTeams->addTeamMember($arrMemberData);

				// Check for Succesfull page insert
				if(isset($intInsertID) && is_numeric($intInsertID)) {
					// Return Feedback
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added team member')));
					$this->_redirect('/admin/club/teamsmembers/id/'.$arrParams['id'].'/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
					$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the team member');
				}

			}

		}

		// Parse variables to view
		$this->view->defaults   = $arrDefaults;
		$this->view->members    = $arrTeamMembers;
		$this->view->roles      = $arrTeamRoles;

	}

	public function teamsmemberseditAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/club/teams/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Models
		$objModelTeams      = new KZ_Models_Teams();

		// Get Members by Team
		$arrTeamMember      = $objModelTeams->getTeamMember($arrParams['id']);

		// Get Team Roles
		$arrTeamRoles       = $objModelTeams->getTeamRoles();

		// Set Defaults
		$arrDefaults        = $arrTeamMember;

		// Check for Post
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrParams					= $this->_getAllParams();

			// Set Defaults
			$arrDefaults                = $arrParams;

			if(empty($arrDefaults['firstname'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a firstname');
			} elseif(empty($arrDefaults['lastname'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a lastname');
			} else {

				// Set Team Member Array
				$arrMemberData      = array(
					'team_role_id'  => $arrDefaults['team_role_id'],
					'gender'        => $arrDefaults['gender'],
					'firstname'     => $arrDefaults['firstname'],
					'lastname'      => $arrDefaults['lastname'],
					'link'          => $arrDefaults['link']
				);

				// Save Team member
				$intUpdateID		= $objModelTeams->updateTeamMember($arrParams['id'],$arrMemberData);

				// Check for Succesfull page insert
				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					// Return Feedback
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully saved team member')));
					$this->_redirect('/admin/club/teamsmembers/id/'.$arrTeamMember['team_id'].'/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
					$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to save the team member');
				}

			}

		}

		// Parse variables to view
		$this->view->defaults   = $arrDefaults;
		$this->view->roles      = $arrTeamRoles;

	}

	public function teamsmembersdeleteAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/club/teams/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Models
		$objModelTeams      = new KZ_Models_Teams();

		// Get Members by Team
		$arrTeamMember      = $objModelTeams->getTeamMember($arrParams['id']);

		// Get Team Roles
		$arrTeamRoles       = $objModelTeams->getTeamRoles();

		// Set Defaults
		$arrDefaults        = $arrTeamMember;

		// Check for Post
		if($this->getRequest()->isPost()) {

			// Delete Team member
			$intDeleteID        = $objModelTeams->deleteTeamMember($arrParams['id']);

			// Check for Succesfull delete
			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				// Return Feedback
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted team member')));
				$this->_redirect('/admin/club/teamsmembers/id/'.$arrTeamMember['team_id'].'/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the team member');
			}

		}

		// Parse variables to view
		$this->view->defaults   = $arrDefaults;
		$this->view->roles      = $arrTeamRoles;

	}

	/**
     * Function for generating the Teams table
     * Used for the AJAX call for the Datatable
     */
    public function generateteamstableAction()
    {
    	// Disable Layout and View
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// Set Models
    	$objModelTeams			= new KZ_Models_Teams();
    	$objTranslate			= new KZ_View_Helper_Translate();
    	
	   	// Set the Columns
    	$arrColumns				= array($objTranslate->translate('ID'),
						    			$objTranslate->translate('Name'),
						    			$objTranslate->translate('Category'),
						    			$objTranslate->translate('Sport'),
						    			$objTranslate->translate('Season'),
						    			$objTranslate->translate('Options'));

    	// Set the DB Table Columns
    	$arrTableColums			= array('teams_id',
						    			'name',
    									'category',
						    			'sport',
						    			'season_serie');

    	// Set the Search
    	$strSearchData			= null;
    	if($_POST['sSearch'] != "") {
    		$strSearchString		= $_POST['sSearch'];
    	
    		if(is_numeric($strSearchString)) {
    			$strSearchData		= "members_id LIKE '%".$strSearchString."%'";
    		} else {
    			$strSearchData		= "(name LIKE '%".$strSearchString."%' OR category LIKE '%".$strSearchString."%' OR sport LIKE '%".$strSearchString."%' OR season LIKE '%".$strSearchString."%')";
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
    	$intTotalTeams				= $objModelTeams->getTeamsForTable(true);
    	
    	// Select all Teams
    	$objTeams 					= $objModelTeams->getTeamsForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
    	$arrTeams					= $objTeams->toArray();

    	// Create the JSON Data
    	$output 					= array("sEcho" 				=> intval($_POST['sEcho']),
							    			"iTotalRecords" 		=> $intTotalTeams,
							    			"iTotalDisplayRecords" 	=> $intTotalTeams,
							    			"aaData"				=> array()
    	);
    	
    	if(!empty($arrTeams)) {
    		foreach($arrTeams as $key => $arrTeamValues) {

    			$row = array();
    			for($i = 0; $i < count($arrColumns); $i++) {
    				if($arrColumns[$i] != ' ') {
    					
    					if(isset($arrTableColums[$i])) {
   							$strRowData		= stripslashes($arrTeamValues[$arrTableColums[$i]]);
    					} else {
    						$strOptionsHtml = '<ul class="actions">
													<li><a rel="tooltip" href="/admin/club/teamsmembers/id/'.$arrTeamValues['teams_id'].'/" class="users" original-title="'.$objTranslate->translate('Show team').'">'.$objTranslate->translate('Show team').'</a></li>
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
	
	/**
     * Function for generating the Teams table
     * Used for the AJAX call for the Datatable
     */
	public function generatememberstableAction()
    {
    	// Disable Layout and View
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// Set Models
    	$objModelMembers		= new KZ_Models_Members();
    	$objTranslate			= new KZ_View_Helper_Translate();
    	
	   	// Set the Columns
    	$arrColumns				= array($objTranslate->translate('ID'),
						    			$objTranslate->translate('Name'),
						    			$objTranslate->translate('Email'),
						    			$objTranslate->translate('Phone'),
						    			$objTranslate->translate('Category'),
						    			$objTranslate->translate('Invited'),
						    			$objTranslate->translate('Options'));

    	// Set the DB Table Columns
    	$arrTableColums			= array('members_id',
						    			'firstname',
    									'email',
						    			'phonenr',
						    			'phonenr',
						    			'invited');

    	// Set the Search
    	$strSearchData			= null;
    	if($_POST['sSearch'] != "") {
    		$strSearchString		= $_POST['sSearch'];
    	
    		if(is_numeric($strSearchString)) {
    			$strSearchData		= "teams_id LIKE '%".$strSearchString."%'";
    		} else {
    			$strSearchData		= "(firstname LIKE '%".$strSearchString."%' OR insertion LIKE '%".$strSearchString."%' OR lastname LIKE '%".$strSearchString."%' OR email LIKE '%".$strSearchString."%' OR email_second LIKE '%".$strSearchString."%' OR phonenr LIKE '%".$strSearchString."%' OR mobilenr LIKE '%".$strSearchString."%' OR category_contribution LIKE '%".$strSearchString."%' OR category_age LIKE '%".$strSearchString."%')";
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
    	$intTotalMembers			= $objModelMembers->getMembersForTable(true);
    	
    	// Select all Members
    	$objMembers 				= $objModelMembers->getMembersForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
    	$arrMembers					= $objMembers->toArray();

    	// Create the JSON Data
    	$output 					= array("sEcho" 				=> intval($_POST['sEcho']),
							    			"iTotalRecords" 		=> $intTotalMembers,
							    			"iTotalDisplayRecords" 	=> $intTotalMembers,
							    			"aaData"				=> array()
    	);
    	
    	if(!empty($arrMembers)) {
    		foreach($arrMembers as $arrMember) {

    			$row = array();
    			for($i = 0; $i < count($arrColumns); $i++) {
	                if($arrColumns[$i] != ' ') {

		                if(isset($arrTableColums[$i])) {
			                if($arrTableColums[$i] == 'firstname') {
				                $strRowData		= $arrMember['firstname'].' '.$arrMember['insertion'].' '.$arrMember['lastname'];
			                }elseif($arrTableColums[$i] == 'email') {
				                $strRowData		= $arrMember['email'].((! empty($arrMember['email_second'])) ? ', '.$arrMember['email_second'] : '');
			                }elseif($arrTableColums[$i] == 'category_contribution') {
				                $strRowData		= $arrMember['category_contribution'].' - '.$arrMember['category_age'];
			                } elseif($arrTableColums[$i] == 'invited') {
				                $strRowData		= (($arrMember['invited'] == 1) ? 'Yes' : 'No');
			                } else {
				                $strRowData		= stripslashes($arrMember[$arrTableColums[$i]]);
			                }
		                } else {

	                        $strOptionsHtml = '<ul class="actions">
													<li><a rel="tooltip" href="/admin/club/membersshow/id/'.$arrMember['members_id'].'/" class="show" original-title="'.$objTranslate->translate('Show member').'">'.$objTranslate->translate('Show member').'</a></li>
													<li><a rel="tooltip" href="/admin/club/membersedit/id/'.$arrMember['members_id'].'/" class="edit" original-title="'.$objTranslate->translate('Edit member').'">'.$objTranslate->translate('Edit member').'</a></li>
													<li><a rel="tooltip" href="/admin/club/membersdelete/id/'.$arrMember['members_id'].'/" class="delete" original-title="'.$objTranslate->translate('Delete member').'">'.$objTranslate->translate('Delete member').'</a></li>
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
