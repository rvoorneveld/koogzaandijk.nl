<?php

class KZ_Models_Services extends KZ_Controller_Table
{
	
	protected $_name 	= 'api_service';
	protected $_primary = 'api_service_id';
	
	/**
	 * Get all Services by optional status
	 * @param int $intStatus
	 * @return obj $objData
	 */
	public function getServices($intStatus = false)
	{
		$strQuery = $this->select();
					
		$objData = $this->fetchAll($strQuery);
		return $objData;
	}
	
}