<?php
class KZ_Models_Cronjobs extends KZ_Controller_Table
{
	
	protected $_name 		= 'cronjob';
	protected $_primary		= 'cronjob_id';
	
	/**
	 * 
	 * Get Single Cronjob by ID
	 * @param integer $intCronjobID
	 * @return object $objData
	 */
	public function getCronjob($intCronjobID)
	{
		$strQuery = $this->select()
					->where('cronjob_id = ?', $intCronjobID);
					
		$objData 	= $this->fetchRow($strQuery);
		return $objData;
	}
	
	/**
	 * 
	 * Get Cronjobs
	 * @return object $objData
	 */
	public function getCronjobs($arrDates = false, $intStatus = false)
	{
		$strQuery = $this->select();
		
		if($arrDates !== false && is_array($arrDates)) {
			
			foreach($arrDates as $strKey => $strValue)
			{
				
				$strQuery	->where("( ".$strKey." LIKE ?", '%['.$strValue.']%')
							->orWhere($strKey.' = ? )', '*');
				
			}
			
		}
		
		if($intStatus !== false && is_numeric($intStatus)) {
			$strQuery->where('status = ?', $intStatus);
		}
		
		$objData = $this->fetchAll($strQuery);
		return $objData;

	}
	
	/**
	 * 
	 * Add Cronjob
	 * @param array $arrData
	 * @return int $intInsertID
	 */
	public function addCronjob($arrData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Add Cronjob
		$intInsertID = $this->insert(array_merge($arrData, array('created' => $strDate)));
		return $intInsertID;
	}
	
	/**
	 * 
	 * Update Cronjob
	 * @param integer $intCronjobID
	 * @param array $arrData
	 * @return int $intUpdateID
	 */
	public function updateCronjob($intCronjobID, $arrData)
	{
		
		// Create last modified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$intUpdateID = $this->update(array_merge($arrData, array('lastmodified' => $strDate)), "cronjob_id = $intCronjobID");
		return $intUpdateID;
		
	}
	
	/**
	 * 
	 * Delete Cronjob
	 * @param integer $intCronjobID
	 * @return integer $intDeleteID
	 */
	public function deleteCronjob($intCronjobID)
	{
		$intDeleteID = $this->delete("cronjob_id = $intCronjobID");
		return $intDeleteID;
	}
	
}