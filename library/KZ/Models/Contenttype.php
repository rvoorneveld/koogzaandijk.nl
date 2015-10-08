<?php
class KZ_Models_Contenttype extends KZ_Controller_Table
{
	
	protected $_name 	= 'content_type';
	protected $_primary = 'content_type_id';
	
	/**
	 * 
	 * Get All Content Types
	 * @param string $strReturnAssoc
	 * @return 	array 	$arrData
	 */
	public function getContentTypes($strContent, $strReturnAssoc = false)
	{
		// Set Query string
		$strQuery 	= $this->select()
						->where("content_available LIKE ?", '%'.$strContent.'%');

		$arrData = $this->returnData($strQuery);
					
		if($strReturnAssoc !== false) {
			$arrReturnAssoc = array();
			foreach($arrData as $intReturnKey => $arrReturnData) {
				$arrReturnAssoc[$arrReturnData[$strReturnAssoc]] = $arrReturnData;
			}
			return $arrReturnAssoc;	
		}
					
		// Return array	
		return $arrData;
		
	}

}