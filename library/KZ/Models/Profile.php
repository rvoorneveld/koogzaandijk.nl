<?php
class KZ_Models_Profile extends KZ_Controller_Table
{

	protected $_name 	= 'profile';
	protected $_primary = 'profile_id';
	
	public function getProfileByMemberID($intMemberID)
	{
		$strQuery = $this->select()
					->where('member_id = ?',$intMemberID);
		return $this->returnData($strQuery,'array','fetchRow');
	}

	public function getProfileContact($intMemberID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('profile_contact')
					->where('member_id = ?',$intMemberID);
		return $this->returnData($strQuery,'array','fetchRow');
	}

	public function getProfilesByCodes($arrCodes)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('profile')
					->where('profile.code IN(?)', $arrCodes)
					->join('members', 'members.members_id = profile.member_id', array('firstname','insertion','lastname'));

		return $this->returnData($strQuery);
	}

	public function updateAvatar($intProfileID,$strAvatar,$strAvatarHistory = false)
	{
		// Set Last Modified Date
		$objDate    = new Zend_Date();
		$strDate    = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		$arrUpdateData = array('avatar' => $strAvatar, 'lastmodified' => $strDate);

		if($strAvatarHistory !== false) {
			$arrUpdateData = array_merge($arrUpdateData, array('avatar_history' => $strAvatarHistory));
		}

		$intUpdateID = $this->update($arrUpdateData, "profile_id = $intProfileID");
		return $intUpdateID;
	}

	public function updateProfile($intMemberID, $arrData)
	{
		$intUpdateID = $this->update($arrData, "member_id = $intMemberID");
		return $intUpdateID;
	}

	public function updateProfileContact($intMemberID, $arrData)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('profile_contact', $arrData, "member_id = $intMemberID");

		return $intUpdateID;
	}

	public function addProfile($intMemberID,$strSecurityKey)
	{
		// Set Created Date
		$objDate    = new Zend_Date();
		$strDate    = $objDate->toString('yyyy-MM-dd hh:mm:ss');

		// Set Data Array
		$arrProfileData = array(
			'member_id' => $intMemberID,
			'code'      => $strSecurityKey,
			'created'   => $strDate
		);

		// Insert Profile
		$intInsertID    = $this->insert($arrProfileData);

		// Insert Profile Contact
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$objDb->insert('profile_contact', array('member_id' => $intMemberID));

		return $intInsertID;
	}

}