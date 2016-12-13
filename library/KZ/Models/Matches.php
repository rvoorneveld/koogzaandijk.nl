<?php
class KZ_Models_Matches extends KZ_Controller_Table
{
	
	protected $_name	= 'matches';
	protected $_primary	= 'matches_id';
	
	public function getTeams()
	{
		$strQuery = $this->select()
					->from($this->_name, array('team_home_name','team_away_name'))
					->where('team_home_name LIKE ?', '%KZ/Hiltex%')
					->orWhere('team_away_name LIKE ?', '%KZ/Hiltex%')
					->order('poule_name');
		
		return $this->returnData($strQuery);

	}
	
	public function getMatchByID($intMatchID)
	{
		$strQuery = $this->select()
					->where('match_id = ?', $intMatchID)
                    ->order('matches_id DESC');

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getMatch($intMatchID)
	{
		$strQuery = $this->select()
					->where('matches_id = ?', $intMatchID);
					
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 *
	 * Get Matches for the Datatable
	 *
	 * @return object $objData
	 */
	public function getMatchesForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null, $intYear = null, $intWeek = null)
	{
		if($booReturnTotal === true) {
			$strQuery 		= 	$this->select('COUNT(matches_id) AS total')
								->where('year = ?', $intYear)
								->where('week = ?', $intWeek);
			$objData 	= $this->fetchAll($strQuery);
			return count($objData);
		}
	
		$strQuery 		= $this->select()
								->setIntegrityCheck(false)
								->from('matches', array('*', 'CONCAT_WS(" - ", team_home_name, team_away_name) AS game', 'CONCAT_WS(" - ", team_home_score, team_away_score) AS result', 'CONCAT_WS(", ", year, week) AS year_week'))
								->where('year = ?', $intYear)
								->where('week = ?', $intWeek);

		if(!is_null($strSearchData)) {
			$strQuery->where($strSearchData);
		}
		
		// Set the Limit when isset
		if(!is_null($arrLimitData)) {
			$strQuery->limit($arrLimitData['count'], $arrLimitData['offset']);
		}
		
		// Set an order when isset
		if(!empty($arrOrderData)) {
			$strQuery->order($arrOrderData);
		}

		$objData = $this->fetchAll($strQuery);
		return $objData;
	}
	
	public function getMatches($intYear, $intWeek, $arrTeams = false, $booOnlyClubTeams = true, $intLimit = 10)
	{
		
		$strQuery	= 	$this->select()
						->where('year = ?', $intYear)
						->where('week = ?', $intWeek);

		$intTotalTeams 	= count($arrTeams);
		
		if($arrTeams !== false && is_array($arrTeams) && count($arrTeams) > 0) {
			
			foreach($arrTeams as $intTeamKey => $strTeam) {
				
				if($intTeamKey == 0) {
					$strQuery->where(' ( team_home_name = ?', $strTeam);
				} else {
					$strQuery->orWhere('team_home_name = ?', $strTeam);
				}
				
				$strQuery->orWhere('team_away_name = ?'.(($intTotalTeams == ($intTeamKey + 1)) ? ' ) ' : ''), $strTeam);

			}
			
		}
		
		if($booOnlyClubTeams === true) {
			$strQuery->where('( team_home_clubteam = ?', 1);
			$strQuery->orWhere(' team_away_clubteam = ? )', 1);
		}
		
		if($intLimit !== false && is_numeric($intLimit)) {
			$strQuery->limit($intLimit);
		}
		
		$arrData = $this->returnData($strQuery);
		
		if(isset($arrData) && is_array($arrData) && count($arrData) > 0) {
			
			$arrAssoc	        = array();
			$arrAssocSeniors    = array();
			
			foreach($arrData as $intMatchKey => $arrMatch) {
				
				$strTeam				= trim(str_replace('KZ/Hiltex','',((strstr($arrMatch['team_home_name'], 'KZ/Hiltex')) ? $arrMatch['team_home_name'] : $arrMatch['team_away_name'])));

				if(is_numeric($strTeam)) {
					$arrAssocSeniors[$strTeam][]    = $arrMatch;
				} else {
					$arrAssoc[$strTeam][]	        = $arrMatch;
				}

			}

			ksort($arrAssocSeniors);
			ksort($arrAssoc);

			return array_merge($arrAssocSeniors, $arrAssoc);

		}
		
		return array();
		
	}
	
	public function getDistinct($strColumn = 'year')
	{
		$strQuery = $this->select()
					->distinct()
					->from($this->_name, $strColumn)
					->order($strColumn);

		return $this->returnData($strQuery);
	}
	
	public function updateMatch($intMatchID, $arrData)
	{
		// Set Last modified Date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$intUpdateID	= $this->update(array_merge($arrData, array('lastmodified' => $strDate)), "matches_id = $intMatchID");
		return $intUpdateID;
	}
	
	public function addMatch($arrData)
	{
		// Set Created Date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		$intInsertID	= $this->insert(array_merge($arrData, array('created' => $strDate)));
		return $intInsertID;
	}
	
	public function deleteMatch($intMatchID)
	{
		$intDeleteID	= $this->delete("matches_id = $intMatchID");
		return $intDeleteID;
	}
	
	public function getAssocMatchesByMatchID()
	{
		$strQuery 	= $this->select()
						->where('team_home_clubteam = ?', 1)
						->orWhere('team_away_clubteam = ?', 1);

		$arrData	= $this->returnData($strQuery);

		if(isset($arrData) && is_array($arrData) && count($arrData) > 0) {
			$arrReturnAssoc = array();
			foreach($arrData as $intDataKey => $arrDataRow) {
				$arrReturnAssoc[$arrDataRow['match_id'].$arrDataRow['team_home_id'].$arrDataRow['team_away_id']]	= $arrDataRow;
			}
			return $arrReturnAssoc;
		}
		return array();
	}
	
	public function getPouleIDByTeamID($intTeamID, $strSeasonStartDate)
	{
		$strQuery = $this->select()
					->distinct(true)
					->from($this->_name, array('poule_name','poule_code'))
					->where('date > ?', $strSeasonStartDate)
					->where('( team_home_id = ?', $intTeamID)
					->orWhere('team_away_id = ? )', $intTeamID);

		return $this->returnData($strQuery);
	}

	public function getPouleIDByTeamIDs($arrTeamIds)
	{
		$strQuery = $this->select()
			->distinct(true)
			->from($this->_name, array('poule_name','poule_code'))
			->where('( team_home_id IN(?)', $arrTeamIds)
			->orWhere('team_away_id IN(?) )', $arrTeamIds);

		return $this->returnData($strQuery);
	}

	public function getMatchesByTeamID($intTeamID,$strSeasonStartDate = false)
	{
		$strQuery = $this->select()
					->where('( team_home_id LIKE ?', $intTeamID)
					->orWhere('team_away_id LIKE ? )', $intTeamID);

		if($strSeasonStartDate !== false) {
			$strQuery->where('date >= ?',$strSeasonStartDate);
		}

		$strQuery->order('date');


		return $this->returnData($strQuery);
	}
	
	public function getMatchesByPoule($strPouleName, $strSeasonStartDate, $booOnlyClubTeams = true)
	{
		$strQuery = $this->select()
					->where('poule_name = ?', $strPouleName)
					->where('date > ?', $strSeasonStartDate);
					
		if($booOnlyClubTeams === true) {
			$strQuery->where('( team_home_name LIKE ?', '%KZ/Hiltex%')
					->orWhere('team_away_name LIKE ? )', '%KZ/Hiltex%');
		}
		
		$strQuery->order('date');
		
		return $this->returnData($strQuery);

	}
	
}