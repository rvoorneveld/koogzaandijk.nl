<?php
class KZ_Models_Teams extends KZ_Controller_Table
{
	
	protected $_name	= 'teams';
	protected $_primary	= 'teams_id';
	
	public function addTeam($arrData)
	{
		$intInsertID	= $this->insert($arrData);
		return $intInsertID;
	}

	public function getTeams($mixTeamID = false)
	{
		$strQuery = $this->select();
		
		if($mixTeamID !== false && is_numeric($mixTeamID)) {
			$strQuery->where('id = ?', $mixTeamID);
		}

		if($mixTeamID !== false && is_array($mixTeamID)) {
			$strQuery->where('id IN(?)', $mixTeamID);
		}

		$strQuery->order('name');

		return $this->returnData($strQuery, 'array', (($mixTeamID !== false && is_numeric($mixTeamID)) ? 'fetchRow' : 'fetchAll'));
	}

	public function getTeamByTeamsId($intTeamsID)
	{
		$strQuery = $this->select()
					->where('teams_id = ?', $intTeamsID);

		return $this->returnData($strQuery,'array','fetchRow');
	}
	
	public function getDistinctTeams()
	{
		$strQuery = $this->select()
					->distinct(true)
					->from($this->_name, array('name'))
					->order(array('category', 'name'));
					
		return $this->returnData($strQuery);
	}

	public function getDistinctTeamsByTeamIDs($arrTeamIDs)
	{
		$strQuery = $this->select()
			->distinct(true)
			->from($this->_name, array('name','sport'))
			->where('id IN(?)', $arrTeamIDs)
			->order('sport DESC');

		return $this->returnData($strQuery);
	}
	
	public function getTeamIDs($strColumn, $strSeperator)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from($this->_name, array($strColumn));
		
		$arrData =	$this->returnData($strQuery);
		
		if(isset($arrData) && is_array($arrData) && count($arrData) > 0) {
			
			$arrReturn = array();
			
			foreach($arrData as $intDataKey => $arrDataRow) {
				
				$arrReturn[]	= $arrDataRow['id'];
				
			}
			
			return implode($strSeperator, $arrReturn);
			
		}
		
		return false;
		
	}
	
	public function getIDsByTeam($strTeam)
	{
		$strQuery = $this->select()
					->distinct(true)
					->from('teams', array('id','category','sport'))
					->where('name = ?', $strTeam)
					->order('sport ASC'); // DESC = Indoor, Outdoor : ASC = Outdoor, Indoor

		return $this->returnData($strQuery);
	}

	public function getTeamMembers($intTeamID, $mixReturnAssoc = false)
	{
		// Create Query
		$strQuery   = $this->select()
					->setIntegrityCheck(false)
					->from('team_member')
					->join('team_role', 'team_member.team_role_id = team_role.team_role_id', array('team_role.name as role'))
					->where('team_id = ?', $intTeamID)
					->order('team_role.team_role_id')
					->order('team_member.gender')
					->order('team_member.lastname');

		// Get Data
		$arrData    = $this->returnData($strQuery);

		// Check if Assoc array must be returned
		if($mixReturnAssoc !== false) {
			$arrReturnAssoc = array();
			if(! empty($arrData) && is_array($arrData)) {
				foreach($arrData as $arrDataRow) {
					$arrReturnAssoc[$arrDataRow[$mixReturnAssoc]][]   = $arrDataRow;
				}
				return $arrReturnAssoc;
			}
		}

		return $arrData;
	}

	public function getTeamMember($intMemberID)
	{
		// Create Query
		$strQuery   = $this->select()
			->setIntegrityCheck(false)
			->from('team_member')
			->join('team_role', 'team_member.team_role_id = team_role.team_role_id', array('team_role.name as role'))
			->where('team_member_id = ?', $intMemberID)
			->order('team_role.team_role_id')
			->order('team_member.gender')
			->order('team_member.lastname');

		// Get Data
		return $this->returnData($strQuery,'array','fetchRow');
	}

	public function getTeamRoles()
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('team_role');

		return $this->returnData($strQuery);
	}

	public function addTeamMember($arrMember)
	{
		// Create Created Date
		$objDate 		= new Zend_Date();
		$strDate 		= $objDate->toString('yyyy-MM-dd HH:mm:ss');
		$intUserID      = KZ_Controller_Session::getActiveUser();

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('team_member', array_merge($arrMember, array('created' => $strDate,'user_id' => $intUserID)));

		return $intInsertID;
	}

	public function updateTeamMember($intMemberID,$arrMember)
	{
		// Create Created Date
		$objDate 		= new Zend_Date();
		$strDate 		= $objDate->toString('yyyy-MM-dd HH:mm:ss');
		$intUserID      = KZ_Controller_Session::getActiveUser();

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('team_member', array_merge($arrMember, array('lastmodified' => $strDate,'user_id' => $intUserID)), "team_member_id = $intMemberID");

		return $intUpdateID;
	}

	public function deleteTeamMember($intTeamID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID 	= $objDb->delete('team_member', "team_member_id = $intTeamID");

		return $intDeleteID;
	}

	/**
	 *
	 * Get Teams for the Datatable
	 *
	 * @return object $objData
	 */
	public function getTeamsForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
	{
		if($booReturnTotal === true) {
			$strQuery 		= $this->select('COUNT(teams_id) AS total');
			$objData 	= $this->fetchAll($strQuery);
			return count($objData);
		}
	
		$strQuery 		= $this->select()
								->setIntegrityCheck(false)
								->from('teams', array('*'));

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

	public function getCompleteTeamData($arrTeamIds)
	{
		// Set Default Team Data
		$arrTeamData    = array();

		// Set Date Object
		$objDate        = new Zend_Date();
		$strDate        = $objDate->toString('yyyy-MM-dd');

		if(! empty($arrTeamIds) && is_array($arrTeamIds)) {
			foreach($arrTeamIds as $intTeamID) {

				// Set Defaults
				$strPouleName   = false;

				// Get Team Information
				$strQuery   = $this->select()
							->where('id = ?', $intTeamID);

				// Set Team Information Array
				$arrTeam    = $this->returnData($strQuery, 'array','fetchRow');

				// Get Match Data
				$strQuery   = $this->select()
							->setIntegrityCheck(false)
							->from('matches')
							->where('( team_home_id = ?', $intTeamID)
							->orWhere('team_away_id = ? )', $intTeamID)
							->where('date <= ?',$strDate)
							->order('date DESC')
							->limit(1);

				// Set Past Match Information Array
				$arrPastMatch = $this->returnData($strQuery,'array','fetchRow');

				// Get Match Data
				$strQuery   = $this->select()
					->setIntegrityCheck(false)
					->from('matches')
					->where('( team_home_id = ?', $intTeamID)
					->orWhere('team_away_id = ? )', $intTeamID)
					->where('date >= ?',$strDate)
					->order('date')
					->limit(1);

				// Set Future Match Information Array
				$arrFutureMatch = $this->returnData($strQuery,'array','fetchRow');

				// Check if Past Matches where found
				if(! empty($arrPastMatch) && is_array($arrPastMatch)) {
					// Set Poule Name
					$strPouleName   = $arrPastMatch['poule_name'];
				}

				// Check if Future Matches where found
				if($strPouleName === false && ! empty($arrFutureMatch) && is_array($arrFutureMatch)) {
					// Set Poule Name
					$strPouleName   = $arrFutureMatch['poule_name'];
				}

				// Get Standings if Poule Name was found
				if($strPouleName !== false) {

					// Get Poule Standings
					$strQuery   = $this->select()
								->setIntegrityCheck(false)
								->from('standings')
								->where('poule_name = ?',$strPouleName)
								->where('type = ?', $arrTeam['sport'])
								->where('clubteam = ?', 1);

					// Set Poule Standings Array
					$arrStandings  = $this->returnData($strQuery, 'array','fetchRow');

				}

				// Set Match Data
				$arrTeamData[$intTeamID]  = array(
					'team'          => $arrTeam,
					'last_match'    => $arrPastMatch,
					'next_match'    => $arrFutureMatch,
					'standings'     => $arrStandings
				);

			}

			return $arrTeamData;

		}

		return false;
	}

	public function insertMembersTeams($arrTeams)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('members_teams', $arrTeams);
		return $intInsertID;
	}

	public function updateMembersTeams($intMembersID, $arrTeams)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('members_teams', $arrTeams, "members_id = $intMembersID");
		return $intUpdateID;
	}

}