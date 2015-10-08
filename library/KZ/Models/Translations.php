<?php
class KZ_Models_Translations extends KZ_Controller_Table
{
	
	protected $_name		= 'translations';
	protected $_primary		= 'translation_id';
	
	/**
	 * Get all translations by locale id
	 * @param int $intLocaleID
	 *
	 * @return array $arrReturn
	 */
	public function getTranslation($strLanguage)
	{
		$strQuery = $this->select()
						->from(array('t' => 'translations'), array('keycode','translation'))
						->where('language = ?', $strLanguage)
						->where('status = ?', 'ok');
	
		$arrData = $this->fetchAll($strQuery);
	
		// Create clean array
		$arrReturn	= array();
		foreach($arrData as $arrItem){
			$arrReturn[$arrItem['keycode']]	= stripslashes($arrItem['translation']);
		}
		// Return result
		return $arrReturn;
	}
	
	public function countMissingTranslations()
	{
		$strQuery = $this->select()
						->from(array('t' => 'translations'), array('totalMissing' => 'COUNT(translation_id)'))
						->where('status = ?', 'missing');
		
		return $this->returnData($strQuery, 'array', 'fetchRow');
	} 
	
	public function checkMissingTranslation($strKey, $strLanguage)
	{
		$strQuery		= $this->select(true)
								->columns(array('keycode','translation'))
								->where('language = ?', strtolower($strLanguage))
								->where('keycode = ?', $strKey)
								->where('status = ?', 'missing');
		
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function addMissingTranslation($strKey, $strLanguage)
	{
		$arrInsertData		= array('keycode' 		=> $strKey,
									'language'		=> strtolower($strLanguage),
									'translation'	=> '',
									'status'		=> 'missing');
		
		$this->insert($arrInsertData);
	}
	
	public function getAllTranslations()
	{
		$strQuery		= $this->select()
								->order('keycode');
		
		return $this->returnData($strQuery);
	}
	
	public function getTranslationByID($intTranslationID)
	{
		$strQuery		= $this->select()
								->where('translation_id = ?', $intTranslationID);
		
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function updateTranslation($intTranslationID, $strTranslation) 
	{
		$arrUpdateData		= array('translation' 	=> $strTranslation, 
									'status' 		=> 'ok');
		
		return $this->update($arrUpdateData, "translation_id = '".$intTranslationID."'");	
	}
}