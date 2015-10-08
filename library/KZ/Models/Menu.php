<?php
class KZ_Models_Menu extends KZ_Controller_Table
{
	
	protected $_name	= 'menu_type';
	protected $_primary	= 'menu_type_id';
	
	public function getMenuTypes($strReturnAssoc = false)
	{
		$strQuery = $this->select();
		
		$arrData	= $this->returnData($strQuery);
		
		if($strReturnAssoc !== false) {
			$arrReturnAssoc = array();
			foreach($arrData as $intReturnKey => $arrReturnData) {
				$arrReturnAssoc[$arrReturnData[$strReturnAssoc]] = $arrReturnData;
			}
			return $arrReturnAssoc;	
		}
		
		return $arrData;
	}
	
}