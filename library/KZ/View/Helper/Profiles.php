<?php
class KZ_View_Helper_Profiles extends Zend_View_Helper_Abstract {

	public function profiles($strProfileType = false)
	{

		// Set Korfball Guru's array
		$arrKorfballGurus   = array('placeholder');

		if($strProfileType == 'button') {

			// Get Profile Session Namespace
			$objControllerSession   = new KZ_Controller_Session();
			$arrMember              = $objControllerSession->getProfileSession();

			return $this->view->partial('snippets/profiles_button.phtml',array(
				'member'            => $arrMember,
				'arrKorfballgurus'  => $arrKorfballGurus
			));

		} else {

			// Set Defaults
			$arrProfiles        = array();

			// Get Cookies
			$objModelCookie     = new KZ_Controller_Action_Helper_Cookie();

			// Set Models
			$objModelProfile    = new KZ_Models_Profile();

			// Get Cookie value if found for kz_logins
			$strSecurityCodes 	= $objModelCookie->getCookie('KZ_Logins');

			// Check if kz_logins where found
			if($strSecurityCodes !== false) {

				$arrProfileCodes    = explode(',',$strSecurityCodes);

				// Get Profiles By Codes
				$arrProfiles        = $objModelProfile->getProfilesByCodes($arrProfileCodes);

			}

			return $this->view->partial('snippets/profiles.phtml',array(
				'profiles'          => $arrProfiles,
				'arrKorfballgurus'  => $arrKorfballGurus
			));

		}

	}

}