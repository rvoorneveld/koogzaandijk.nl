<?php

class KZ_Models_Api extends KZ_Controller_Table
{
	
	protected $_name 	= 'api_log';
	protected $_primary = 'api_log_id';
	
	/**
	 * Get Log Data
	 * @return obj $objData
	 */
	public function getLog($intLogID = false)
	{
		$strQuery = $this->select();
					
		if($intLogID === false) {
			$objData = $this->fetchAll($strQuery);
			return $objData;	
		} else {

			$strQuery->where('api_log_id = ?', $intLogID);
			
			$objData = $this->fetchRow($strQuery);
			return $objData;

		}
		
		
	}

}