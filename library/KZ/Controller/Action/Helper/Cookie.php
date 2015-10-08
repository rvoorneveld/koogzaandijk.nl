<?php
class KZ_Controller_Action_Helper_Cookie
{

	public function getCookie($strCookieName)
	{

		if(! isset($_COOKIE[$strCookieName])) {
			return false;
		}

		return $_COOKIE[$strCookieName];

	}

	public function setCookie($strName, $strValue, $intCookieLifetime)
	{
		setcookie($strName, $strValue, (time() + $intCookieLifetime), '/');
	}

	private function _checkSecurityCode($strSecurityCode)
	{
		// Check if Cookie was not found
		if($strSecurityCode === false) {

			// Set Difficulty level
			$intDifficultyLevel 	= 5;

			// Set Security Model
			$objModelSecurity       = new KZ_Controller_Action_Helper_Security();

			// Get Security Code
			$strSecurityCode		= $objModelSecurity->createSecurityCode($intDifficultyLevel);

			// Get Config
			$objConfig 				= Zend_Registry::get('Zend_Config');

			// Set Cookie
			self::setCookie('kz_logins', $strSecurityCode, $objConfig->cookie->lifetime);
		}
		// Return Security Code
		return $strSecurityCode;
	}

}