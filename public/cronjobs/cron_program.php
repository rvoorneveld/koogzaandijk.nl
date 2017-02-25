<?php

    set_include_path(implode(PATH_SEPARATOR,[
        realpath(realpath(dirname(__FILE__)).'/../../library'),
        get_include_path(),
    ]));

    require_once __DIR__.'/../../vendor/autoload.php';

	// Pdo mysql
	/*$arrDatabasePreview	= array(
		'host'     => 'localhost',
		'username' => 'kzusr1910',
		'password' => 'b6CSdFsDBfvAmb9z',
		'dbname'   => 'koogzaandijk_v1_prev'
	);*/
	
	$arrDatabaseProduction = array(
		'host'		=> 'localhost',
		'dbname'	=> 'koogzaandijk_v1_live',
		'username'	=> 'kzusr2015',
		'password'	=> 'c6DKiLeUEakRlk3a'
	);
	
	$objDatabase	= new Zend_Db_Adapter_Pdo_Mysql($arrDatabaseProduction);
	
    // Get all params
    $arrParams              	= $_POST;
	
    // Set Date
	$arrDate					= explode('-', $arrParams['match']['date']);

	// Set Variables
	$intCompetitionProgramID	= (int)$arrParams['match']['competition_program_id'];
	$intMatchID					= (int)$arrParams['match']['game_id'];
	$intWeek					= (int)$arrParams['week'];
	$intYear					= (int)$arrDate[0];
	$strPouleCode				= $arrParams['match']['poule_code'];
	$strPouleName				= $arrParams['match']['poule_name'];
	$strTeamHomeID				= $arrParams['match']['home_team_id'];
	$strTeamHomeName			= $arrParams['match']['home_team_name'];
	$intTeamHomeScore			= ((isset($arrParams['match']['home_score']) && ! empty($arrParams['match']['home_score']) && is_numeric($arrParams['match']['home_score'])) ? $arrParams['match']['home_score'] : NULL);
	$strTeamAwayID				= $arrParams['match']['away_team_id'];
	$strTeamAwayName			= $arrParams['match']['away_team_name'];
	$intTeamAwayScore			= ((isset($arrParams['match']['away_score']) && ! empty($arrParams['match']['away_score']) && is_numeric($arrParams['match']['away_score'])) ? $arrParams['match']['away_score'] : NULL);
	$strDate					= $arrParams['match']['date'];
	$strTime					= $arrParams['match']['time'];
	$strFacilityID				= $arrParams['match']['facility_id'];
	$strFacilityName			= $arrParams['match']['facility_name'];
	$strOfficials				= $arrParams['match']['match_officials'];
	
	// Get Match
	$strQuery					= "SELECT matches_id FROM matches WHERE match_id = '$intMatchID'";
	$arrMatch       			= $objDatabase->fetchRow($strQuery);

	$arrMatchData = array(
		'competition_program_id'	=> $intCompetitionProgramID,
		'match_id'					=> $intMatchID,
		'week'						=> $intWeek,
		'year'						=> $intYear,
		'poule_code'				=> $strPouleCode,
		'poule_name'				=> $strPouleName,
		'team_home_id'				=> $strTeamHomeID,
		'team_home_name'			=> $strTeamHomeName,
		'team_home_score'			=> $intTeamHomeScore,
		'team_away_id'				=> $strTeamAwayID,
		'team_away_name'			=> $strTeamAwayName,
		'team_away_score'			=> $intTeamAwayScore,
		'date'						=> $strDate,
		'time'						=> $strTime,
		'facility_id'				=> $strFacilityID,
		'facility_name'				=> $strFacilityName,
		'officials'					=> $strOfficials
	);
	
	// Check if Match already exists
	if(is_array($arrMatch) && isset($arrMatch['matches_id']) && $arrMatch['matches_id'] != '') {
		$objDatabase->update('matches', $arrMatchData, "matches_id = {$arrMatch['matches_id']}");
	} else {
		$objDatabase->insert('matches', $arrMatchData);
	}
?>
