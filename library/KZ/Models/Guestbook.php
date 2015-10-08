<?php
class KZ_Models_Guestbook extends KZ_Controller_Table
{

	protected $_name 	= 'guestbook';
	protected $_primary = 'guestbook_entry_id';

	/**
	 * Function for getting the all Entries or the Front 2 entries
	 * @param string $strLimit
	 */
	public function getGuestbookEntries($arrLimit = array())
	{
		$strQuery		= $this->select()
								->where('guestbook_entry_verified = ?', 'y')
								->order('guestbook_entry_date DESC');

		if(!empty($arrLimit)) {
			$strQuery->limit($arrLimit['count'], $arrLimit['offset']);
		}
		
		return $this->returnData($strQuery);
	}
	
	public function getGuestbookEntry($intEntryID)
	{
		$strQuery = $this->select()
					->where('guestbook_entry_id = ?', $intEntryID);
		
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getAllGuestbookEntries() {
		$strQuery		= $this->select()
								->order('guestbook_entry_date DESC');
		
		return $this->returnData($strQuery);
	}
	
	/**
	 * Function for getting the Total amount of Guestbook entries
	 * 
	 *
	 * @return array
	 */
	public function getTotalGuestbookEntries()
	{
		$strQuery		= $this->select()
								->setIntegrityCheck(false)
								->from('guestbook', array('COUNT(guestbook_entry_id) AS total'))
								->where('guestbook_entry_verified = ?', 'y');
	
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * Function for inserting the Guestbook entry
	 * 
	 * @param array $arrPostData
	 */
	public function insertGuestbookData($arrPostData)
	{
		return $this->insert($arrPostData);
	}
	
	/**
	 *
	 * Get Members for the Datatable
	 *
	 * @return object $objData
	 */
	public function getEntriesForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
	{
		if($booReturnTotal === true) {
			$strQuery 		= $this->select('COUNT(guestbook_id) AS total');
			$objData 		= $this->fetchAll($strQuery);
			return count($objData);
		}
	
		$strQuery 		= $this->select()
		->setIntegrityCheck(false)
		->from('guestbook', array('*'));
	
		if(!is_null($strSearchData)) {
			$strQuery->where($strSearchData);
		}
	
		// Set the Limit when isset
		if(!is_null($arrLimitData)) {
			$strQuery->limit($arrLimitData['count'], $arrLimitData['offset']);
		}
	
		// Set an order when isset
		if(!empty($arrOrderData)) {
			$strQuery->order($arrOrderData);
		}
	
		$objData = $this->fetchAll($strQuery);
		return $objData;
	}
	
	
	/**
	 * Function for validating the Guestbook entry
	 * 
	 * @param array $arrData
	 */
	public function validateData($arrData)
	{
		$strQuery		= $this->select()
								->where('guestbook_email = ?', $arrData['email'])
								->where('guestbook_entry_id = ?', $arrData['ID']);
		
		$arrResult		= $this->returnData($strQuery, 'array', 'fetchRow');
		
		if(is_array($arrResult) && !empty($arrResult) && count($arrResult) > 0) {
			$this->_setValidated($arrResult['guestbook_entry_id']);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Private function for setting the Guestbook entry to Validated
	 * 
	 * @param integer $intEntryID
	 */
	private function _setValidated($intEntryID)
	{
		$this->update(array('guestbook_entry_verified' => 'y'), "guestbook_entry_id = '".$intEntryID."'");
	}
}