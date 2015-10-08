<?php
class KZ_Controller_Action_Helper_Security
{

	public static function createSecurityCode($intDifficultyLevel = 3)
	{
		// Set Alphabetical array
		$arrAlphabetical		= array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

		// Set Security Code
		$strSecurityCode		= '';

		// Create Difficult Security Code
		for($i = 1; $i <= $intDifficultyLevel; $i++) {
			$strSecurityCode .= rand(10,99).$arrAlphabetical[array_rand($arrAlphabetical)].strtoupper($arrAlphabetical[array_rand($arrAlphabetical)]);
		}

		// Return Security Code string
		return $strSecurityCode;

	}

}