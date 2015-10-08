<?php
class KZ_Models_Users extends KZ_Controller_Table
{
	
	protected $_name 	= 'user';
	protected $_primary = 'user_id';
	
	/**
	 * Function for getting all the users
	 * 
	 * @return arr $arrResults
	 */
	public function getAllUsers($strReturnAssoc = false)
	{
		$strQuery			= $this->select()
									->setIntegrityCheck(false)
									->from(array('u' => 'user'))
									->join(array('g' => 'user_group'), 'g.user_group_id = u.user_group_id', array('g.name as authlevel'))
									->order('u.name');
		
		$arrData = $this->returnData($strQuery);
					
		if($strReturnAssoc !== false) {
			$arrReturnAssoc = array();
			foreach($arrData as $intReturnKey => $arrReturnData) {
				$arrReturnAssoc[$arrReturnData[$strReturnAssoc]] = $arrReturnData;
			}
			return $arrReturnAssoc;	
		}
					
		// Return array	
		return $arrData;
	}
	
	/**
	 * Function for getting all available user group auth levels
	 */
	public function getAllUserGroupLevels()
	{
		$strQuery			= $this->select()
									->setIntegrityCheck(false)
									->from('user_group');
	
		return $this->returnData($strQuery);
	}
	
	/**
	 * Function for getting all user group permissions for Zend_Acl
	 * 
	 * @return arr
	 */
	public function getUserGroupPermissions()
	{
		$strQuery		= $this->select()
								->setIntegrityCheck(false)
								->from(array('ug' => 'user_group'), array('name', 'authlevel'))
								->joinLeft(array('gl' => 'user_group_authlevel'), 'gl.user_group_authlevel_id = ug.user_group_id', array('permissions', 'controllers'));
		
		return $this->returnData($strQuery);
	}
	
	public function getGroupPermissions($intGroupID)
	{
		$strQuery		= $this->select()
								->setIntegrityCheck(false)
								->from(array('ug' => 'user_group'), array('name', 'authlevel'))
								->joinLeft(array('gl' => 'user_group_authlevel'), 'gl.user_group_authlevel_id = ug.user_group_id', array('permissions', 'controllers', 'actions'))
								->where('ug.user_group_id = ?', $intGroupID);
								
		
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * Function for getting the userinformation based on the userID
	 *
	 * @param int $intUserID
	 * @return arr $arrResults
	 */
	public function getUserByUserID($intUserID)
	{
		$strQuery			= $this->select()
									->where('user_id = ?', $intUserID);
		
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * Function for checking if the e-mail entered by the user is in the system
	 * Function used by ForgotPassword
	 * @param str $strEmailAddress
	 * 
	 * @return true or null
	 */
	public function checkUserByEmail($strEmailAddress) 
	{
		$strQuery		= $this->select()
								->where('email = ?', $strEmailAddress)
								->where('status = ?', 1);
		
		$objResult		= $this->fetchRow($strQuery);
		if(!is_null($objResult) && count($objResult) > 0) {
			$arrUserData		= $objResult->toArray();
			$strPassword		= $this->_genUniquePass('10');
			
			// Save the new pasword in DB
			$arrUserData['password']	= base64_encode($strPassword);
			$this->saveUser($arrUserData['user_id'], $arrUserData);
			
			// Send new password to user_email in DB
			KZ_Models_Mail::sendMail('lostpassword', 'nl', $arrUserData);
			
			// return true for Succes message and redirect to /login/
			return true;
		} else {
			return null;
		}
	}
	
	/** Get User by unique email and password
	 * @param string $strEmail
	 * @param string $strPassword
	 * 
	 * @return object $objData
	 */
	public function getUserByEmailAndPassword($strEmail, $strPassword)
	{
		$strQuery = $this->select('*')
					->setIntegrityCheck(false)
					->joinRight('user_group', 'user_group.user_group_id = user.user_group_id', 'user_group.name as groupName')
					->where("user.email = ?", $strEmail)
					->where("user.password = ?", base64_encode($strPassword));
		
		$objData = $this->fetchRow($strQuery);
		return $objData;
	}
	
	/**
	 * Function for checking the Admin Login
	 * 
	 * @param arr $arrLoginData
	 */
	public function checkUserLogin($strEmailAddress, $strPassword) 
	{
		$strQuery			= $this->select()
									->where('email = ?', $strEmailAddress)
									->where('password = ?', base64_encode($strPassword))
									->where('isActive = ?', 'y');
		
		$objResults			= $this->fetchRow($strQuery);
		
		if(!is_null($objResults) && count($objResults) > 0) {
			$arrUserData		= $objResults->toArray();;
			$arrUserLevelData	= $this->_getUserGroupAndLevel($arrUserData['user_groupID']);
			if(!is_null($arrUserLevelData)) {
				$arrUserLevelData['user_id']		= $objResults->userID;
				$arrUserLevelData['full_name']		= $objResults->full_name;
				$arrUserLevelData['login_name']		= $objResults->loginname;
				$arrUserLevelData['companyID']		= $objResults->user_companyID;
				return $arrUserLevelData;
			}
			return null;
		}
		
		return null;
	}

	/**
	 * Function for adding user
	 * Function used at 'Users' menu
	 */
	public function addAdminUser($arrUserData)
	{
		// Remove params form postdata for saving
		unset($arrUserData['controller']);
		unset($arrUserData['action']);
		unset($arrUserData['module']);
		unset($arrUserData['feedback']);

		// Create a password
		$strPassword				= $this->_genUniquePass('10');
			
		// Assign the password to the insert array
		$arrUserData['password']	= base64_encode($strPassword);
		
		// Send new password to user_email in DB
		KZ_Models_Mail::sendMail('newaccount', 'nl', $arrUserData);
				
		return $this->insert($arrUserData);
	}
	
	/**
	 * Function for saving the new user information
	 */
	public function saveUser($intUserID, $arrUserData)
	{
		// Remove params form postdata for saving
		unset($arrUserData['controller']);
		unset($arrUserData['action']);
		unset($arrUserData['module']);
		unset($arrUserData['id']);
		unset($arrUserData['userID']);
		unset($arrUserData['feedback']);
		
		return $this->update($arrUserData, "user_id = '".$intUserID."'");
	}
	
	/**
	 * Function for Deleting a User
	 * @param int $intUserID
	 * 
	 * @return NumAffectedRows
	 */
	public function deleteUser($intUserID)
	{
		return $this->delete("user_id = '".$intUserID."'");
	}
	
	/**
	 * Function for Deleting all Company Users
	 */
	public function deleteCompanyUsers($intCompanyID)
	{
		$this->delete("user_companyID = '".$intCompanyID."'");
	}
	
	/**
	 * Private function for generation a unique password
	 * 
	 * @param int $intMaxLength
	 * 
	 * @return str Unique password
	 */
	private function _genUniquePass($intMaxLength = 8) {
		$strUniqueString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		return substr(str_shuffle($strUniqueString), 0, $intMaxLength);
	}
	
	private function _getUserGroupAndLevel($intUserGroupID)
	{
		$strQuery			= $this->select()
									->setIntegrityCheck(false)
									->from(array('ug' => 'user_groups'), array('ug.group_name', 'ug.group_authlevel'))
									->join(array('gl' => 'group_levels'), 'gl.group_levelID = ug.group_authlevel', array('gl.permissions'))
									->where('ug.groupID = ?', $intUserGroupID);

		$objResults			= $this->fetchRow($strQuery);
		if(!is_null($objResults) && count($objResults) > 0) {
			return $objResults->toArray();
		}
		
		return null;
	}
	
}