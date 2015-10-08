<?php

/**
 * @todo Better ErrorHandling
 * @author arjan
 *
 */
class KZ_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// Get the current Module 
		$strModuleName 		= $this->getRequest()->getModuleName();

		// Only the admin need ACL
		if($strModuleName == 'admin') {
		
			// Create the Acces Control List Object
			$objAcl				= new Zend_Acl();
			
			// Get the Application Config
			$objConfig			= Zend_Registry::get('Zend_Config');
			
			// Get the Database Group Levels
			$objUsers			= new KZ_Models_Users();
			$arrGroupLevels		= $objUsers->getUserGroupPermissions();
			
			// Add the different roles
			foreach($arrGroupLevels as $key => $arrGroupLevelValues) {
				$strRoleName	= strtolower($arrGroupLevelValues['name']);
				$objAcl			->addRole(new Zend_Acl_Role($strRoleName));
			}
			
			// Add all the admin Controller Resources
			foreach($objConfig->controllers as $intControllerKey => $strConfigController) {
				$objAcl->add(new Zend_Acl_Resource(strtolower($strConfigController)));
			}
			
			// Resources All users can access
			$objAcl->allow(null, array('index', 'error', 'login', 'logout'));
			
			// Set the allowed resources 
			foreach($arrGroupLevels as $key => $arrGroupLevelValues) {
				$strRoleName	= strtolower($arrGroupLevelValues['name']);
				if($strRoleName != 'administrator') {
					// Get the allowed controllers by groupLevel
					$arrAllowedResources		= explode(',', $arrGroupLevelValues['controllers']);
					foreach($arrAllowedResources as $key => $strController) {
						$objAcl->allow($strRoleName, 'dashboard',	array($arrGroupLevelValues['permissions']));
					}
				} else {
					$objAcl->allow('administrator', null);
				}
			}
			
			// Set the allowed resources
			foreach($arrGroupLevels as $key => $arrGroupLevelValues) {
				$strRoleName	= strtolower($arrGroupLevelValues['name']);
				if($strRoleName != 'administrator') {
					// Get the allowed controllers by groupLevel
					$arrAllowedResources		= explode(',', $arrGroupLevelValues['controllers']);
					$arrPermissions				= ($arrGroupLevelValues['permissions'] != 'null') ? explode(',', $arrGroupLevelValues['permissions']) : null;
					
					foreach($arrAllowedResources as $key => $strController) {
						$objAcl->allow($strRoleName, trim($strController), $arrPermissions);
					}
				} else {
					$objAcl->allow('administrator', null);
				}
			}
			
			// Get the Current User group level
			$objNamespace 			= new Zend_Session_Namespace('KZ_Admin');
			$arrUserData			= $objNamespace->user;
			
			// Set the user role
			$strUserRole		= (strtolower($arrUserData['groupName']) != '') ? strtolower($arrUserData['groupName']) : null;
	
			// Get the current Action and Controller
			$strController		= $request->controller;
			$strAction			= $request->action;
			
			// Set error if not allowed to Access the controller -> Action
			if(!$objAcl->isAllowed($strUserRole, $strController,$strAction)) {

			}
		}
	}
}