<?php
class KZ_Models_Members extends KZ_Controller_Table
{

	protected $_name 	= 'members';
	protected $_primary = 'members_id';
	
	public function getMembers()
	{
		$strQuery = $this->select();
		return $this->returnData($strQuery);
	}

	/**
	 *
	 * Get Members for the Datatable
	 *
	 * @return object $objData
	 */
	public function getMembersForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
	{
		if($booReturnTotal === true) {
			$strQuery 		= $this->select('COUNT(members_id) AS total');
			$objData 	= $this->fetchAll($strQuery);
			return count($objData);
		}
	
		$strQuery 		= $this->select()
								->setIntegrityCheck(false)
								->from('members', array('*'));

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

	public function getMemberByEmailAndPassword($strEmail,$strPassword)
	{
		$strQuery   = $this->select()
					->where('email = ?',$strEmail)
					->where('password = ?',$strPassword);

		return $this->returnData($strQuery,'array','fetchRow');
	}

	public function getMemberByCodeAndPassword($strCode,$strPassword)
	{
		$strQuery   = $this->select()
					->setIntegrityCheck(false)
					->from('members')
					->join('profile','profile.member_id = members.members_id')
					->where('members.password = ?',$strPassword)
					->where('profile.code = ?',$strCode);

		return $this->returnData($strQuery,'array','fetchRow');
	}

	public function getMember($intMemberID)
	{
		$strQuery   = $this->select()
			->setIntegrityCheck(false)
			->from('members')
			->joinLeft('profile','profile.member_id = members.members_id')
			->where('members.members_id = ?',$intMemberID);

		return $this->returnData($strQuery,'array','fetchRow');
	}

	public function getMemberTeams($intMemberID)
	{
		$strQuery   = $this->select()
			->setIntegrityCheck(false)
			->from('members_teams')
			->where('members_teams.members_id = ?',$intMemberID);

		return $this->returnData($strQuery,'array','fetchRow');
	}

	public function getConnectedMembers($arrMembers)
	{

		if(! empty($arrMembers) && is_array($arrMembers)) {

			$strQuery = $this->select()
						->setIntegrityCheck(false)
						->from('members_teams')
						->join('profile', 'profile.member_id = members_teams.members_id')
						->join('members', 'members.members_id = members_teams.members_id')
						->joinLeft('profile_contact', 'profile_contact.member_id = members_teams.members_id');

			// Get Total Members
			$intTotalMembers = count($arrMembers);

			foreach($arrMembers as $intMemberKey => $intMemberID)
			{
				if($intMemberKey == 0) {
					$strQuery->where('( coach_teams LIKE ?', '%,'.$intMemberID.',%');
				} else {
					$strQuery->orWhere('coach_teams LIKE ?', '%,'.$intMemberID.',%');
				}

				$strQuery->orWhere('coach_teams LIKE ?', '%,'.$intMemberID);
				$strQuery->orWhere('coach_teams LIKE ?', $intMemberID.',%');
				$strQuery->orWhere('coach_teams = ?', $intMemberID);

				$strQuery->orwhere('player_teams LIKE ?', '%,'.$intMemberID.',%');
				$strQuery->orWhere('player_teams LIKE ?', '%,'.$intMemberID);
				$strQuery->orWhere('player_teams LIKE ?', $intMemberID.',%');

				if($intMemberKey+1 < $intTotalMembers) {
					$strQuery->orWhere('player_teams = ?', $intMemberID);
				} else {
					$strQuery->orWhere('player_teams = ? )', $intMemberID);
				}
			}

			//$strQuery->order('profile_contact.lastname');

			return $this->returnData($strQuery);
		}

		return false;

	}

	public function updateMember($intMemberID, $arrData)
	{
		// Set Last Modified Date
		$objDate            = new Zend_Date();
		$strLastmodified    = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		$intUpdateID = $this->update(array_merge($arrData, array('lastmodified' => $strLastmodified)),"members_id = $intMemberID");
		return $intUpdateID;
	}

}