<?php
class KZ_Models_Settings extends KZ_Controller_Table
{
	
	protected $_name	= 'settings';
	protected $_primary	= 'settings_id';
	
	public function getSettingsByID($intSettingsID)
	{
		$strQuery	= 	$this->select()
						->where('settings_id = ?', $intSettingsID);
					
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getSettingsByKey($strSettingsKey,$booValueOnly = false)
	{
		$strQuery	= 	$this->select()
						->where('`key` = ?', $strSettingsKey);

		$arrData    = $this->returnData($strQuery, 'array', 'fetchRow');

		if($booValueOnly === true) {
			return $arrData['value'];
		}

		return $arrData;
	}
	
	public function updateSettingsByID($intSettingsID, $arrSettingsData)
	{
		$intUpdateID = 	$this->update($arrSettingsData, "settings_id = $intSettingsID");
		return $intUpdateID;
	}
	
	public function updateSettingsByKey($strSettingsKey, $arrSettingsData)
	{
		$intUpdateID = 	$this->update($arrSettingsData, "`key` = '$strSettingsKey'");
		return $intUpdateID;
	}
	
}