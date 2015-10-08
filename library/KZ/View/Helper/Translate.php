<?php

class KZ_View_Helper_Translate extends Zend_View_Helper_Abstract
{
	
	public function translate($strKey)
	{
		// Check if key was set
		if(! isset($strKey) || empty($strKey) || is_null($strKey)) {
			return '';
		}
		
		$arrTranslations = Zend_Registry::get('KZ_Translate');

		if(! array_key_exists($strKey, $arrTranslations)) {
			// Set Language
			$strLanguage 			= Zend_Registry::get('Language');
			
			// Set Model
			$objModelTranslations  	= new KZ_Models_Translations();
			
			// Check if missing translation already exists
			$objMissingTranslation 	= $objModelTranslations->checkMissingTranslation($strKey, $strLanguage);

			if(empty($objMissingTranslation)) {

				// Set Missing Translations
				$objModelTranslations->addMissingTranslation($strKey, $strLanguage);
				
			}
			
			return $strKey;
		}
		
		$objTranslate 	= new Zend_View_Helper_Translate();
		return $objTranslate->translate($strKey);
	}
	
}