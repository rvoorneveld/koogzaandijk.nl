<?php
class KZ_Models_Locales extends KZ_Controller_Table {

	protected $_name 	= 'locale';
	protected $_primary = 'locale_id';
	
	public function getLocales($booReturnAssoc = false)
	{
		$strQuery 	= $this->select();
		$arrData  	= $this->fetchAll($strQuery);
		
		if($booReturnAssoc !== false) {
			$arrReturn = array();
			foreach($arrData as $intReturnKey => $objReturnData) {
				$arrReturn[$objReturnData[$booReturnAssoc]] = $objReturnData->toArray();
			}
			return $arrReturn;
		}
		
		return $arrData;		
	}

}