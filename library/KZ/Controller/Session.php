<?php
class KZ_Controller_Session extends Zend_Db_Table_Abstract
{
	
	public function getActiveUser()
	{
		
		// Get the Current User group level
		$objNamespace 			= new Zend_Session_Namespace('KZ_Admin');
		$arrUserData			= $objNamespace->user;
		
		return $arrUserData['user_id'];
		
	}

	public function setProfileSession($arrMember)
	{
		// Create new Zend Session Namespace
		$objNamespace 		    = new Zend_Session_Namespace('KZ_Profile');
		$objNamespace->profile  = $arrMember;
	}

	public function unsetProfileSession()
	{
		// Create new Zend Session Namespace
		$objNamespace 		    = new Zend_Session_Namespace('KZ_Profile');
		$objNamespace->unsetAll();
	}

	public function getProfileSession()
	{
		$objNamespace 		    = new Zend_Session_Namespace('KZ_Profile');
		return $objNamespace->profile;
	}

}