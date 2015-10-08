<?php
class KZ_Models_Colors extends KZ_Controller_Table
{
	
	protected $_name 	= 'color';
	protected $_primary = 'color_id';
	
	/**
	 * 
	 * Get All Colors
	 * @return array $arrData
	 */
	public function getColors($strReturnAssoc = false, $strWidgetOnly = false)
	{
		$strQuery = $this->select();
		
		if($strWidgetOnly !== false) {
			$strQuery->where('widgetOnly = ?', $strWidgetOnly);
		}
					
		
		if($strReturnAssoc !== false) {
			
			$arrAssocData		= array();
			$arrReturnData 		= $this->returnData($strQuery);
			
			if(isset($arrReturnData) && is_array($arrReturnData)) {
				
				foreach($arrReturnData as $intDataKey => $arrData) {
					$arrAssocData[$arrData['code']] = $arrData;
				}
				
			}
			
			return $arrAssocData;
			
			
			
		}
		
		return $this->returnData($strQuery);
		
	}
	
}