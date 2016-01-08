<?php

class KZ_Api_Services_GetStandings extends KZ_Api_Services
{
	
	public 	$service 			= 'GetStandings';
    public 	$version 			= '1.0';
    public	$code				= 'ef0baad8aa6d23b60';
    public 	$location			= 'http://www.knkv.nl/kcp';

    public function run()
    {
    	// Set Models
    	$objModelStandings		= new KZ_Models_Standings();
    	$objModelTeams			= new KZ_Models_Teams();
    	 
    	// Get Teams
    	$strTeamIDs				= $objModelTeams->getTeamIDs('id',',');
    	
    	// Set Load From Cache
    	$booLoadCached			= false;
    	
    	// Set Configuration array
        $arrConfig	= array(
                'file'          => 'xml',
                'f'             => 'get_data',
                't'             => 'standing',
                't_id'          => $strTeamIDs,
                'p'             => 0,
                'full'          => 1
        );
        
        $strLoadedXml		= $this->_loadXml($booLoadCached, $arrConfig);

        // Load XML string and parse
        $objXml 			= simplexml_load_string($strLoadedXml);
        
    	if(isset($objXml) && is_object($objXml)) {
	    	
    		// Set Defaults
    		$intInsertedStandings	= 0;

    		// Empty Current Standings
    		$objModelStandings->_truncate('standings');
    		
	    	// Loop trough Data
	    	foreach($objXml->children() as $arrAttributes) {
	    		
	    		// Set Variables
	    		$intPouleID			= (int)$arrAttributes->poule_id;
	    		$strPouleName		= (string)$arrAttributes->poule_name;
	    		$strType			= (string)$arrAttributes->sport;
	    		$strTypeAddition	= (string)$arrAttributes->serie;
	    		
	    		foreach($arrAttributes->lines->line as $arrLine) {

	    			// Set Position Variables
	    			$intClubTeam			= ((isset($arrLine['clubteam']) && (string)$arrLine['clubteam'] == 'TRUE') ? 1 : 0);
	    			$intPosition			= (int)$arrLine->position;
	    			$strTeamName			= (string)$arrLine->team_name;
	    			$intPlayed				= (int)$arrLine->played;
	    			$intPoints				= (int)$arrLine->points;
	    			$intWon					= (int)$arrLine->won;
	    			$intLost				= (int)$arrLine->lost;
	    			$intDraw				= (int)$arrLine->draw;
	    			$intGoalsFor			= (int)$arrLine->goals_for;
	    			$intGoalsAgainst		= (int)$arrLine->goals_against;
	    			$intDifference			= (int)$arrLine->difference;
	    			$intPenalties			= (int)$arrLine->penalties;
    			
	    			$arrStandingsData	= array(
	    				'poule_id'			=> $intPouleID,
	    				'poule_name'		=> $strPouleName,
	    				'type'				=> $strType,
	    				'type_addition'		=> $strTypeAddition,
	    				'position'			=> $intPosition,
	    				'clubteam'			=> $intClubTeam,
	    				'team_name'			=> $strTeamName,
	    				'played'			=> $intPlayed,
	    				'points'			=> $intPoints,
	    				'won'				=> $intWon,
	    				'lost'				=> $intLost,
	    				'draw'				=> $intDraw,
	    				'goals_for'			=> $intGoalsFor,
	    				'goals_against'		=> $intGoalsAgainst,
	    				'difference'		=> $intDifference,
	    				'penalties'			=> $intPenalties
	    			);

    				// Insert New Match
   					$intInsertID 		= $objModelStandings->addStanding($arrStandingsData);

   					// Add One to Inserted standings
	    			$intInsertedStandings++;
	    			    			
	    		}
	    		
	    	}

	    	// Set Response Data
    		$arrData = array(
    			'response' => array(
    				'type' 		=> 'success',
    				'service'	=> 'GetStandings',
    				'standings'  	=> array(
    					'inserted'	=> $intInsertedStandings
    				)
    		));
    		
    		// Return Xml Data
    		return $arrData;
    		
    	} else {
    		// Create Error
    		return $this->createError('1002','invalid xml format');
    	}
    	
    	// Return Screen Feedback on Development and Preview
    	if(APPLICATION_ENV != 'production') {
    		Zend_Debug::dump($objXml);
    		exit;
    	}
    	
    	// Create Error
    	return $this->createError('1001','no xml returned from webservice');
    	    	
    }
    
}