<?php
class KZ_Controller_Table extends Zend_Db_Table_Abstract
{
    public $current_datetime;
    public function __construct($config = array())
    {
        $objDate = new Zend_Date();
        $this->current_datetime = $objDate->toString('yyyy-MM-dd HH:mm:ss');
        parent::__construct($config);
    }

    public function returnData($strQuery, $strReturnType = 'array', $strFetchType = 'fetchAll')
	{
		
		// Check if Query string is not empty
		if(! empty($strQuery)) {
			
			// Set Default Return type
			$mixReturnData = (($strReturnType == 'array') ? array() : new stdClass());
			
			// Check Fetch Type
			if($strFetchType == 'fetchAll') {
				
				// Fetch Multiple Rows
				$objResult = $this->fetchAll($strQuery);
			
			} else {
				// Fetch Single Row
				$objResult = $this->fetchRow($strQuery);
			}
			
			// Check if Result is not null and > 0
			if(! is_null($objResult) && count($objResult) > 0) {
				
				// Check if Return Type is array
				if($strReturnType == 'array') {
					// Return Array with Data
					return $objResult->toArray();
				}
				// Return Object with Data
				return $objResult;
				
			}
			// Return Default Object or Array
			return $mixReturnData;
			
		}

		// Return false
		return false;
		
	}
	
	public function _truncate($strTableName)
	{
		$strQuery	= "TRUNCATE table $strTableName";
		$objDb		= Zend_Db_Table::getDefaultAdapter();
		$objDb->query($strQuery);
	}
	
}