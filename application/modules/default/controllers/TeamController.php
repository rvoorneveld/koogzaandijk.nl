<?php

class TeamController extends KZ_Controller_Action
{
	public function indexAction()
    {
		// Get All Params
		$arrParams	= $this->_getAllParams();
		
		if(! isset($arrParams['team']) || $arrParams['team'] == '') {
			$this->_redirect(ROOT_URL);
			exit;
		} 
		
		// Set Models
		$objModelTeams		= new KZ_Models_Teams();
		$objModelMatches	= new KZ_Models_Matches();
		$objModelStandings	= new KZ_Models_Standings();

		// Set Defaults
		$arrMatchData		= array();
		
		// Get Season Start Date
		$strSeasonStartDate	= KZ_View_Helper_Date::getSeasonStartDate();

	    // Set Team
		$strTeam			= $arrParams['team'];
		
		// Get Team Competitions
		$arrTeams			= $objModelTeams->getIDsByTeam($strTeam);

	    // Get Team Members
	    $arrTeamMembers     = $objModelTeams->getTeamMembers($strTeam);

	    // Set Default Team Category
	    $strTeamCategory    = 'Senioren';

		// Check if Competition was found
		if(isset($arrTeams) && is_array($arrTeams) && count($arrTeams) > 0) {
			
			foreach($arrTeams as $intTeamKey => $arrTeam) {

				if($intTeamKey == 0) {
					$strTeamCategory = $arrTeam['category'];
				}
				
				if(isset($arrTeam['id']) && $arrTeam['id'] != '' && is_numeric($arrTeam['id'])) {

					// Set Team ID
					$intTeamID				= $arrTeam['id'];

					// Get Program for Team
					$arrPouleData			= $objModelMatches->getPouleIDByTeamID($intTeamID, $strSeasonStartDate);

					if(isset($arrPouleData) && is_array($arrPouleData) && count($arrPouleData) > 0) {
					
						foreach($arrPouleData as $intPouleDataKey => $arrPouleDataItem) {
							
							$strPouleName		= $arrPouleDataItem['poule_name'];
							$strPouleCode		= $arrPouleDataItem['poule_code'];
							$strSportType		= $arrTeam['sport'];
							
							// Get All Matches for Poule
							$arrPouleMatches	= $objModelMatches->getMatchesByTeamID($intTeamID,$strSeasonStartDate);
							
							// Get Standings for Poule
							$arrPouleStandings	= $objModelStandings->getStandingsByPoule($strPouleName, $strSportType);
	
							$arrMatchData[$strSportType] = array(
								'matches'		=> $arrPouleMatches,
								'standings'		=> $arrPouleStandings
							);
						
						}
						
					}

				}

			}
		
		}

	    // Set Date Object
	    $objDate                    = new Zend_Date();
		$intCurrentTime             = $objDate->getTimestamp();

	    // Get Indoor and Outdoor Dates
	    $intLastOutdoorMatch        = false;
	    $intLastIndoorMatch         = false;

	    if(isset($arrMatchData['Veld'])) {
	        if(isset($arrMatchData['Veld']['matches']) && ! empty($arrMatchData['Veld']['matches']) && is_array($arrMatchData['Veld']['matches'])) {
		        // Get Total Matches
		        $intTotalMatches    = count($arrMatchData['Veld']['matches']);
				$intLastKey         = $intTotalMatches - 1;
		        if(isset($arrMatchData['Veld']['matches'][$intLastKey]) && ! empty($arrMatchData['Veld']['matches'][$intLastKey]) && is_array($arrMatchData['Veld']['matches'][$intLastKey])) {
			        if(isset($arrMatchData['Veld']['matches'][$intLastKey]['date'])) {
				        $intLastOutdoorMatch    = strtotime($arrMatchData['Veld']['matches'][$intLastKey]['date']);
			        }
		        }
	        }
	    }

	    if(isset($arrMatchData['Zaal'])) {
		    if(isset($arrMatchData['Zaal']['matches']) && ! empty($arrMatchData['Zaal']['matches']) && is_array($arrMatchData['Zaal']['matches'])) {
			    // Get Total Matches
			    $intTotalMatches    = count($arrMatchData['Zaal']['matches']);
			    $intLastKey         = $intTotalMatches - 1;
			    if(isset($arrMatchData['Zaal']['matches'][$intLastKey]) && ! empty($arrMatchData['Zaal']['matches'][$intLastKey]) && is_array($arrMatchData['Zaal']['matches'][$intLastKey])) {
				    if(isset($arrMatchData['Zaal']['matches'][$intLastKey]['date'])) {
					    $intLastIndoorMatch    = strtotime($arrMatchData['Zaal']['matches'][$intLastKey]['date']);
				    }
			    }
		    }
	    }

	    if($intLastIndoorMatch !== false && $intLastOutdoorMatch !== false) {
		    if(
			        $intCurrentTime >= $intLastOutdoorMatch
			    &&  $intCurrentTime <= $intLastIndoorMatch
		    ){
			    krsort($arrMatchData);
		    }
	    }

	    // Sort Team Members
	    $arrSortedTeamMembers   = self::_sortTeamMembers($arrTeamMembers);

	    if(! empty($arrSortedTeamMembers) && is_array($arrSortedTeamMembers))
	    {
		    $arrMatchData   = array_merge($arrMatchData, array('Team' => $arrSortedTeamMembers));
	    }

	    $this->view->data	    = $arrMatchData;
	    $this->view->category   = $strTeamCategory;

    }

	private function _sortTeamMembers($arrTeamMembers)
	{
		$arrSortedTeamMembers = array();

		// Loop Through Team Members if set
		if(! empty($arrTeamMembers) && is_array($arrTeamMembers)) {
			foreach($arrTeamMembers as $arrTeamMember) {
				// Save Team Member by Role and Gender
				$arrSortedTeamMembers[$arrTeamMember['role']][$arrTeamMember['gender']][] = $arrTeamMember;
			}
		}

		return $arrSortedTeamMembers;
	}


	
}