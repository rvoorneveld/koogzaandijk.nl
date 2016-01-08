<?php

class KZ_Api_Services_GetResults extends KZ_Api_Services
{

	public 	$service 			= 'GetResults';
	public 	$version 			= '1.0';
	public	$code				= 'ef0baad8aa6d23b60';
	public 	$location			= 'http://www.knkv.nl/kcp';

	public function run()
	{
		$intWeek				= ((isset($this->params->week) && is_numeric((int)$this->params->week)) ?  '-'.(int)$this->params->week : -1);

		// Set Load From Cache
		$booLoadCached			= false;

		// Set Configuration array
		$arrConfig	= array(
			'file'          => 'xml',
			'f'             => 'get_data',
			't'             => 'result',
			't_id'          => '',
			'p'             => $intWeek,
			'full'          => 1
		);

		$strLoadedXml		= $this->_loadXml($booLoadCached, $arrConfig);

		// Load XML string and parse
		$objXml 			= simplexml_load_string($strLoadedXml);

		if(isset($objXml) && is_object($objXml)) {

			// Set Models
			$objModelMatches		= new KZ_Models_Matches();

			// Get Matches by Match ID
			$arrMatches				= $objModelMatches->getAssocMatchesByMatchID();

			// Set Defaults
			$intInsertedMatches		= 0;
			$intUpdatedMatches		= 0;
			$arrCompareFields		= array('week','year','season','poule_code','poule_name','team_home_id','team_home_name','team_home_clubteam','team_home_score','team_away_id','team_away_name','team_away_clubteam','team_away_score','date','time');

			// Loop trough Data
			foreach($objXml->children() as $arrAttributes) {

				// Set Week Number
				$intWeekNumber		= (int)$arrAttributes->number;

				foreach($arrAttributes->matches->match as $arrMatch) {

					// Set Date
					$arrDate				= explode('-', $arrMatch->date);

					// Set Match Variables
					$strAwayTeam			    = (string)$arrMatch->away_team_name;
					$intAwayTeamID			    = (int)$arrMatch->away_team_id;
					$intAwayTeamClub		    = (int)((isset($arrMatch->away_team_name['clubteam']) && (string)$arrMatch->away_team_name['clubteam'] == 'TRUE') ? 1 : 0);
					$intAwayScore			    = (int)$arrMatch->away_score;
					$strDate				    = (string)$arrMatch->date;
					$intMatchID				    = (int)$arrMatch->game_id;
					$intCompetitionProgramID    = (int)$arrMatch->program_id;
					$strHomeTeam			    = (string)$arrMatch->home_team_name;
					$intHomeTeamID			    = (int)$arrMatch->home_team_id;
					$intHomeTeamClub		    = (int)((isset($arrMatch->home_team_name['clubteam']) && (string)$arrMatch->home_team_name['clubteam'] == 'TRUE') ? 1 : 0);
					$intHomeScore			    = (int)$arrMatch->home_score;
					$strPoule				    = (string)$arrMatch->poule_name;
					$strClass				    = (string)$arrMatch->class;
					$strTime				    = (string)$arrMatch->time;
					$intYear				    = (int)$arrDate[0];

					// Set Unique Matches ID
					$intUniqueMatchesID         = $intMatchID.$intHomeTeamID.$intAwayTeamID;

					// Check if Match is a KZ/Hiltex match
					if($intHomeTeamClub == 1 || $intAwayTeamClub == 1) {

						$arrMatchData	= array(
							'week'					    => $intWeekNumber,
							'year'					    => $intYear,
							'poule_code'			    => $strClass,
							'poule_name'			    => $strPoule,
							'team_home_id'			    => $intHomeTeamID,
							'team_home_name'		    => $strHomeTeam,
							'team_home_clubteam'	    => $intHomeTeamClub,
							'team_home_score'		    => $intHomeScore,
							'team_away_id'			    => $intAwayTeamID,
							'team_away_name'		    => $strAwayTeam,
							'team_away_clubteam'	    => $intAwayTeamClub,
							'team_away_score'		    => $intAwayScore,
							'date'					    => $strDate,
							'time'					    => $strTime,
							'competition_program_id'    => $intCompetitionProgramID,
							'match_id'                  => $intMatchID
						);

						if(isset($arrMatches[$intUniqueMatchesID]) && is_array($arrMatches[$intUniqueMatchesID]) && count($arrMatches[$intUniqueMatchesID]) > 0) {
							$booUpdateMatch = false;
							foreach($arrCompareFields as $strFieldKey) {

								if($booUpdateMatch === true) {
									continue;
								}

								if($arrMatches[$intUniqueMatchesID][$strFieldKey] != $arrMatchData[$strFieldKey]) {
									$booUpdateMatch = true;
								}

							}

							if($booUpdateMatch === true) {

								// Update Existing Match, something changed
								$intUpdateID = $objModelMatches->updateMatch($arrMatches[$intUniqueMatchesID]['matches_id'], $arrMatchData);

								// Add One to Updated matches
								$intUpdatedMatches++;
							}

						} else {

							// Insert New Match
							$intInsertID = $objModelMatches->addMatch($arrMatchData);

							// Add One to Updated matches
							$intInsertedMatches++;
						}

					}

				}

			}

			// Set Response Data
			$arrData = array(
				'response' => array(
					'type' 		=> 'success',
					'service'	=> 'GetResults',
					'matches'  	=> array(
						'inserted'	=> $intInsertedMatches,
						'updated'	=> $intUpdatedMatches
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