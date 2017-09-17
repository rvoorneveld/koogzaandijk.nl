<?php
class Admin_MatchesController extends KZ_Controller_Action
{
	
	public function indexAction()
	{
		// Get All Params
		$arrParams				= $this->_getAllParams();
		
		// Set Models
		$objModelMatches		= new KZ_Models_Matches();
		
		// Get Distinct Years
		$arrDistinctYears		= $objModelMatches->getDistinct('year');
		
		// Get Distinct Weeks
		$arrDistinctWeeks		= $objModelMatches->getDistinct('week');
		
		// Set Current Date
		$objCurrentDate			= new Zend_Date();
		
		$this->view->year		= ((isset($arrParams['year']) && ! empty($arrParams['year']) && is_numeric($arrParams['year'])) ? $arrParams['year'] : $objCurrentDate->get('yyyy'));
		$this->view->week		= ((isset($arrParams['week']) && ! empty($arrParams['week']) && is_numeric($arrParams['week'])) ? $arrParams['week'] : $objCurrentDate->get('w'));
		
		$this->view->years		= $arrDistinctYears;
		$this->view->weeks		= $arrDistinctWeeks;
	}

	public function editAction()
	{
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelMatches			= new KZ_Models_Matches();
    	
    	// Get Match
    	$arrMatch					= $objModelMatches->getMatch($arrParams['id']);
    	
    	// Check if Match wasn't found
    	if(isset($arrMatch) && is_array($arrMatch) && count($arrMatch) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find match')));
    		$this->_redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Default Variables
    	$intMatchID					= $arrMatch['match_id'];
    	$intCompetitionProgramID	= $arrMatch['competition_program_id'];
    	$strTeamHomeName			= $arrMatch['team_home_name'];
    	$strTeamAwayName			= $arrMatch['team_away_name'];
    	$strTeamHomeScore			= $arrMatch['team_home_score'];
    	$strTeamAwayScore			= $arrMatch['team_away_score'];
    	$strPouleCode				= $arrMatch['poule_code'];
    	$strPouleName				= $arrMatch['poule_name'];
    	$intYear					= $arrMatch['year'];
    	$intWeek					= $arrMatch['week'];
    	$strDate					= $this->view->date()->format($arrMatch['date'], 'dd-MM-yyyy');
    	$strTime					= $arrMatch['time'];
    	$strTimeDeparture			= $arrMatch['time_departure'];
    	$strOfficials				= $arrMatch['officials'];
		
    	if($this->getRequest()->isPost()) {
    		
    		// Get All Post Params
    		$arrPostData		= $this->_getAllParams();
    		
    		// Set Post Variables
    		$strDate			= $arrPostData['date'];
    		$strTime			= $arrPostData['time'];
    		$strTimeDeparture	= $arrPostData['time_departure'];
    		$strOfficials		= $arrPostData['officials'];
    		
    		// Check form
    		if(empty($strDate)) {
    			$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a date');
    		} elseif(empty($strTime)) {
    			$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a time');
    		} else {
    			
    			// Set Update array
    			$arrData = array(
    				'date'				=> $this->view->date()->format($strDate, 'yyyy-MM-dd'),
	    			'time'				=> $strTime,
	    			'time_departure'	=> $strTimeDeparture,
	    			'officials'			=> $strOfficials,
    				'team_home_score'	=> $this->_validateScore($arrPostData['team_home_score']),
    				'team_away_score'	=> $this->_validateScore($arrPostData['team_away_score'])
    			);
    			
    			// Update The Data
    			$intUpdateID	= $objModelMatches->updateMatch($arrMatch['matches_id'], $arrData);
    			
    			if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated match')));
					$this->_redirect('/admin/matches/index/year/'.$intYear.'/week/'.$intWeek.'/feedback/'.$strFeedback.'/');
				} else {
					// Return feedback
	    			$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the match');
				}
    			
    		}

    	}
    	
    	// Parse Variables to view
    	$this->view->match_id				= $intMatchID;
    	$this->view->competition_program_id	= $intCompetitionProgramID;
    	$this->view->team_home_name			= $strTeamHomeName;
    	$this->view->team_away_name			= $strTeamAwayName;
    	$this->view->team_home_score		= $strTeamHomeScore;
    	$this->view->team_away_score		= $strTeamAwayScore;
    	
    	$this->view->poule_code				= $strPouleCode;
    	$this->view->poule_name				= $strPouleName;
    	$this->view->year					= $intYear;
    	$this->view->week					= $intWeek;
    	$this->view->date					= $strDate;
    	$this->view->time					= $strTime;
    	$this->view->time_departure			= $strTimeDeparture;
    	$this->view->officials				= $strOfficials;
    	
	}
	
	public function weekupdateAction()
	{
		// Get All Params
		$arrParams				= $this->_getAllParams();
		
		// Set Models
		$objModelMatches		= new KZ_Models_Matches();
		
		// Get Distinct Years
		$arrDistinctYears		= $objModelMatches->getDistinct('year');
		
		// Get Distinct Weeks
		$arrDistinctWeeks		= $objModelMatches->getDistinct('week');
		
		// Set Current Date
		$objCurrentDate			= new Zend_Date();
		
		// Set Year and Week
		$intYear				= ((isset($arrParams['year']) && ! empty($arrParams['year']) && is_numeric($arrParams['year'])) ? $arrParams['year'] : $objCurrentDate->get('yyyy'));
		$intWeek				= ((isset($arrParams['week']) && ! empty($arrParams['week']) && is_numeric($arrParams['week'])) ? $arrParams['week'] : $objCurrentDate->get('w'));
		
		// Get All Matches by Week and Year
		$arrMatches				= $objModelMatches->getMatches($intYear, $intWeek, false, true, 50);
		
		// Check for Post
		if($this->getRequest()->isPost()) {
			
			// Set Defaults
			$arrUpdate			= array();
			
			// Get All Post Params
			$arrPostParams		= $this->_getAllParams();
			
			// Loop through Post
			foreach($arrPostParams as $strPostKey => $strPostValue) {
				
				if(
						strstr($strPostKey, 'team_home_score_')
					||	strstr($strPostKey, 'team_away_score_')
					||	strstr($strPostKey, 'time_departure_')
				) {
					
					if($strPostValue != '') {
						
						// Explode Post Key
						$arrPostKey 	= explode('_', $strPostKey);
						$intMatchesID	= (int)end($arrPostKey);
						
						$arrTemp		= array_pop($arrPostKey);
						
						// Check if Match doesn't exist yet
						if(! isset($arrUpdate[$intMatchesID]) || ! is_array($arrUpdate[$intMatchesID]) || count($arrUpdate[$intMatchesID]) == 0) {
							$arrUpdate[$intMatchesID]	= array(
								'team_home_score'	=> NULL,
								'team_away_score'	=> NULL,
								'time_departure'	=> ''
							);
						}
					
						// Remove matches ID from array and create string
						$strDatabaseKey 							= implode('_', $arrPostKey);
						
						// Set Update Value
						$arrUpdate[$intMatchesID][$strDatabaseKey]	= $strPostValue;
						
					}

				}
				
			}
			
			if(isset($arrUpdate) && is_array($arrUpdate) && count($arrUpdate) > 0) {
				foreach($arrUpdate as $intMatchesID => $arrMatch) {
					$objModelMatches->updateMatch($intMatchesID, $arrMatch);
				}
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated matches')));
				$this->_redirect('/admin/matches/index/feedback/'.$strFeedback.'/');
			} else {
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the matches');
			}
		}
		
		$this->view->years		= $arrDistinctYears;
		$this->view->year	 	= $intYear;
		$this->view->weeks		= $arrDistinctWeeks;
		$this->view->week		= $intWeek;
		$this->view->matches	= $arrMatches;
	}
	
	public function deleteAction()
	{
		
		// Get All Params
		$arrParams					= $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Models
    	$objModelMatches			= new KZ_Models_Matches();
    	
    	// Get Match
    	$arrMatch					= $objModelMatches->getMatch($arrParams['id']);
    	
    	// Check if Match wasn't found
    	if(isset($arrMatch) && is_array($arrMatch) && count($arrMatch) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find match')));
    		$this->_redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Set Default Variables
    	$intMatchID					= $arrMatch['match_id'];
    	$intCompetitionProgramID	= $arrMatch['competition_program_id'];
    	$strTeamHomeName			= $arrMatch['team_home_name'];
    	$strTeamAwayName			= $arrMatch['team_away_name'];
    	$strTeamHomeScore			= $arrMatch['team_home_score'];
    	$strTeamAwayScore			= $arrMatch['team_away_score'];
    	$strPouleCode				= $arrMatch['poule_code'];
    	$strPouleName				= $arrMatch['poule_name'];
    	$intYear					= $arrMatch['year'];
    	$intWeek					= $arrMatch['week'];
    	$strDate					= $this->view->date()->format($arrMatch['date'], 'dd-MM-yyyy');
    	$strTime					= $arrMatch['time'];
    	$strTimeDeparture			= $arrMatch['time_departure'];
    	$strOfficials				= $arrMatch['officials'];
		
    	if($this->getRequest()->isPost()) {
    		
    		// Delete The Data
    		$intDeleteID	= $objModelMatches->deleteMatch($arrMatch['matches_id']);
    			
    		if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted match')));
				$this->_redirect('/admin/matches/index/feedback/'.$strFeedback.'/');
			} else {
				// Return feedback
	    		$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the match');
			}
    			
	   	}
    	
    	// Parse Variables to view
    	$this->view->match_id				= $intMatchID;
    	$this->view->competition_program_id	= $intCompetitionProgramID;
    	$this->view->team_home_name			= $strTeamHomeName;
    	$this->view->team_away_name			= $strTeamAwayName;
    	$this->view->team_home_score		= $strTeamHomeScore;
    	$this->view->team_away_score		= $strTeamAwayScore;
    	
    	$this->view->poule_code				= $strPouleCode;
    	$this->view->poule_name				= $strPouleName;
    	$this->view->year					= $intYear;
    	$this->view->week					= $intWeek;
    	$this->view->date					= $strDate;
    	$this->view->time					= $strTime;
    	$this->view->time_departure			= $strTimeDeparture;
    	$this->view->officials				= $strOfficials;
		
	}
	
	/**
     * Function for generating the Property table
     * Used for the AJAX call for the Datatable
     */
    public function generatematchestableAction()
    {
    	// Disable Layout and View
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// Get Params
    	$arrParams						= $this->_getAllParams();
    	
    	// Set Models
    	$objModelMatches				= new KZ_Models_Matches();
    	$objTranslate					= new KZ_View_Helper_Translate();
    	
    	// Set the Columns
    	$arrColumns				= array($objTranslate->translate('ID'),
						    			$objTranslate->translate('KNKV ID'),
						    			$objTranslate->translate('Game'),
						    			$objTranslate->translate('Result'),
						    			$objTranslate->translate('Week'),
						    			$objTranslate->translate('Date'),
						    			$objTranslate->translate('Options'));

    	// Set the DB Table Columns
    	$arrTableColums			= array('matches_id',
						    			'match_id',
						    			'game',
    									'result',
						    			'year_week',
						    			'date');
    	
    	// Set the Search
    	$strSearchData			= null;
    	if($_POST['sSearch'] != "") {
    		$strSearchString		= $_POST['sSearch'];
    	
    		if(is_numeric($strSearchString)) {
    			$strSearchData		= "(matches_id LIKE '%".$strSearchString."%' OR match_id LIKE '%".$strSearchString."%' 
    									OR year LIKE '%".$strSearchString."%' OR week LIKE '%".$strSearchString."%' 
    									OR team_home_score LIKE '%".$strSearchString."%' OR team_away_score LIKE '%".$strSearchString."%' )";
    		} else {
    			$strSearchData		= "(team_home_name LIKE '%".$strSearchString."%' OR team_away_name LIKE '%".$strSearchString."%' )";
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
    	$intTotalMatches				= $objModelMatches->getMatchesForTable(true,null,null,null,$arrParams['year'],$arrParams['week']);
    	
    	// Select all properties
    	$objMatches 					= $objModelMatches->getMatchesForTable(false, $arrLimitData, $strSearchData, $arrOrderData, $arrParams['year'], $arrParams['week']);
    	$arrMatches						= $objMatches->toArray();

    	// Create the JSON Data
    	$output 				= array("sEcho" 				=> intval($_POST['sEcho']),
						    			"iTotalRecords" 		=> $intTotalMatches,
						    			"iTotalDisplayRecords" 	=> $intTotalMatches,
						    			"aaData"				=> array()
    	);
    	
    	if(!empty($arrMatches)) {
    		foreach($arrMatches as $key => $arrMatch) {

    			$row = array();
    			for($i = 0; $i < count($arrColumns); $i++) {
    				if($arrColumns[$i] != ' ') {
    					if(isset($arrTableColums[$i])) {
							if($arrTableColums[$i] == 'matches_id') {
								$strRowData		= '<a name="matchid_'.$arrMatch['matches_id'].'" href="/admin/matches/edit/id/'.$arrMatch['matches_id'].'"><strong>'.$arrMatch['matches_id'].'</strong></a>';
    						} elseif($arrTableColums[$i] == 'date') {
    							$strRowData		= $this->view->date()->format($arrMatch['date'], 'dd-MM-yyyy');
    						} else {
    							$strRowData		= stripslashes($arrMatch[$arrTableColums[$i]]);
    						}
    					} else {
    						// Add the Option Values
    						$strOptionsHtml			= '
    						<ul class="actions">
						    	<li><a rel="tooltip" href="/admin/matches/edit/id/'.$arrMatch['matches_id'].'/" class="edit" original-title="'.$objTranslate->translate('Edit match').'">'.$objTranslate->translate('Edit match').'</a></li>
						    	<li><a rel="tooltip" href="/admin/matches/delete/id/'.$arrMatch['matches_id'].'/" class="delete" original-title="'.$objTranslate->translate('Delete match').'">'.$objTranslate->translate('Delete match').'</a></li>
						    </ul>';

    						$strRowData				= $strOptionsHtml;
    					}
    					$row[] = $strRowData;
    				}
    			}
    			$output['aaData'][] = $row;
    		}
    	}
    	
    	// Send the Output
    	echo json_encode( $output );
    	
    	/*
    	 <tr>
			<td><?php echo $arrMatch['matches_id']; ?></td>
			<td><?php echo $arrMatch['match_id'].' ('.$arrMatch['competition_program_id'].')'; ?></td>
			<td><a href="/admin/matches/edit/id/<?php echo $arrMatch['matches_id']; ?>"><strong><?php echo $arrMatch['game']; ?></strong></a></td>
			<td><?php echo $arrMatch['year']; ?></td>
			<td><?php echo $arrMatch['week']; ?></td>
			<td><?php echo $arrMatch['result']; ?></td>
			<td>
				<ul class="actions">
					<li><a rel="tooltip" href="/admin/matches/edit/id/<?php echo $arrMatch['matches_id']; ?>/" class="edit" original-title="<?php echo $this->translate('Edit match'); ?>"><?php echo $this->translate('Edit match'); ?></a></li>
					<li><a rel="tooltip" href="/admin/matches/delete/id/<?php echo $arrMatch['matches_id']; ?>/" class="delete" original-title="<?php echo $this->translate('Delete match'); ?>"><?php echo $this->translate('Delete match'); ?></a></li>
				</ul>
			</td>
		</tr>
    	*/
    	
    }

    private function _validateScore($score = null){

        switch($score) {
            case 'c':
                return 'c';
                break;
            case $score <= 99:
                return (int)$score;
                break;
            default:
                return null;
                break;
        }

    }
	
}
