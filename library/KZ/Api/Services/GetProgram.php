<?php

class KZ_Api_Services_GetProgram extends KZ_Api_Services
{

	public 	$service 			= 'GetProgram';
	public 	$version 			= '1.0';
	public	$code				= 'ef0baad8aa6d23b60';
	public 	$location			= 'http://www.knkv.nl/kcp';

	public function run()
	{
		// Set Models
		$objModelMatches		= new KZ_Models_Matches();
		$objModelTeams			= new KZ_Models_Teams();

		// Get Teams
		//$strTeamIDs				= $objModelTeams->getTeamIDs('id',',');

		// Set Load From Cache
		$booLoadCached			= false;

		// Set Configuration array
		$arrConfig	= array(
			'file'	=> 'xml',
			'f'		=> 'get_data',
			't'		=> 'program',
			't_id'	=> '',
			'p'		=> 0,
			'full'	=> 1
		);

		$strLoadedXml		    = $this->_loadXml($booLoadCached, $arrConfig);

		// Load XML string and parse
		$objXml = simplexml_load_string($strLoadedXml);

		if(isset($objXml) && is_object($objXml)) {

			// Get Matches by Match ID
			$arrMatches				= $objModelMatches->getAssocMatchesByMatchID();

			// Set Defaults
			$intInsertedMatches		= 0;
			$intUpdatedMatches		= 0;
			$arrCompareFields		= array('week','year','season','poule_code','poule_name','team_home_id','team_home_name','team_home_clubteam','team_away_id','team_away_name','team_away_clubteam','date','time','facility_id','facility_name','facility_addition');

			// Set a Default Empty Match Changed Array
			$arrChangedData      = array();

			// Loop trough Data
			foreach($objXml->children() as $arrAttributes) {

				// Set Week Number
				$intWeekNumber		= (int)$arrAttributes->number;

				foreach($arrAttributes->matches->match as $arrMatch) {

					// Set Date
					$arrDate					= explode('-', $arrMatch->date);

					// Set Match Variables
					$strAwayTeam			    = (string)$arrMatch->away_team_name;
					$intAwayTeamID			    = (int)$arrMatch->away_team_id;
					$intAwayTeamClub		    = (int)((isset($arrMatch->away_team_name['clubteam']) && (string)$arrMatch->away_team_name['clubteam'] == 'TRUE') ? 1 : 0);
					$strDate				    = (string)$arrMatch->date;
					$strFacilityID			    = (string)$arrMatch->facility_id;
					$strFacilityName		    = (string)$arrMatch->facility_name;
					$strFacilityAddition	    = (string)$arrMatch->field;
					$intMatchID				    = (int)$arrMatch->game_id;
					$intCompetitionProgramID    = (int)$arrMatch->program_id;
					$strHomeTeam			    = (string)$arrMatch->home_team_name;
					$intHomeTeamID			    = (int)$arrMatch->home_team_id;
					$intHomeTeamClub		    = (int)((isset($arrMatch->home_team_name['clubteam']) && (string)$arrMatch->home_team_name['clubteam'] == 'TRUE') ? 1 : 0);
					$strOfficials			    = (string)$arrMatch->match_officials;
					$strPoule				    = (string)$arrMatch->poule_name;
					$strClass				    = (string)$arrMatch->class;
					$strTime				    = (string)$arrMatch->time;
					$intYear				    = (int)$arrMatch->year;
					$intSeason				    = (int)$arrMatch->year;

					// Set Unique Matches ID
					$intUniqueMatchesID         = $intMatchID.$intHomeTeamID.$intAwayTeamID;

					// Check if Match is a KZ/Hiltex match
					if($intHomeTeamClub == 1 || $intAwayTeamClub == 1) {

						$arrMatchData	= array(
							'week'					=> $intWeekNumber,
							'year'					=> $intYear,
							'poule_code'			=> $strClass,
							'poule_name'			=> $strPoule,
							'team_home_id'			=> $intHomeTeamID,
							'team_home_name'		=> $strHomeTeam,
							'team_home_clubteam'	=> $intHomeTeamClub,
							'team_away_id'			=> $intAwayTeamID,
							'team_away_name'		=> $strAwayTeam,
							'team_away_clubteam'	=> $intAwayTeamClub,
							'date'					=> $strDate,
							'time'					=> $strTime,
							'facility_id'			=> $strFacilityID,
							'facility_name'			=> $strFacilityName,
							'facility_addition'		=> $strFacilityAddition,
							'officials'             => $strOfficials,
							'competition_program_id'=> $intCompetitionProgramID,
							'match_id'              => $intMatchID
						);

						if(isset($arrMatches[$intUniqueMatchesID]) && is_array($arrMatches[$intUniqueMatchesID]) && count($arrMatches[$intUniqueMatchesID]) > 0) {

							$booUpdateMatch = false;
							foreach($arrCompareFields as $strFieldKey) {

								if($booUpdateMatch === true) {
									continue;
								}

								if(isset($arrMatches[$intUniqueMatchesID][$strFieldKey]) && isset($arrMatchData[$strFieldKey]) && $arrMatches[$intUniqueMatchesID][$strFieldKey] != $arrMatchData[$strFieldKey])
								{
									$arrChangedData[$intUniqueMatchesID][$strFieldKey]  = array(
										'old'  => $arrMatches[$intUniqueMatchesID][$strFieldKey],
										'new'  => $arrMatchData[$strFieldKey]
									);

									// Only add the home and away team once
									if(!isset($arrChangedData[$intMatchID]['teams'])) {
										$arrChangedData[$intUniqueMatchesID]['teams'] = $arrMatches[$intUniqueMatchesID]['team_home_name'].' tegen '.$arrMatches[$intUniqueMatchesID]['team_away_name'];
									}
									$booUpdateMatch = true;
								}
							}

							if($booUpdateMatch === true) {

								// Update Existing Match, something changed
								$intUpdateID    = $objModelMatches->updateMatch($arrMatches[$intUniqueMatchesID]['matches_id'], $arrMatchData);

								// Add the Changed Match data to an Default Empty Array
								$arrChangedMatches[$arrMatches[$intUniqueMatchesID]['matches_id']]      = $arrMatch;

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

			// Check for changes, if so send e-mail
//            if(!empty($arrChangedData) && is_array($arrChangedData)) {
//
//                // Set the Mail Model
//                $objModelMail       = new KZ_Models_Mail();
//
//                // Send the Mail
//                $objModelMail->sendMail('program_changes', 'nl', $arrChangedData);
//            }

			// Set Response Data
			$arrData = array(
				'response' => array(
					'type' 		=> 'success',
					'service'	=> 'GetProgram',
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