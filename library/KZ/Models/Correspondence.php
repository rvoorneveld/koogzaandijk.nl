<?php
class KZ_Models_Correspondence extends KZ_Controller_Table
{
	
	protected $_name 	= 'correspondence';
	protected $_primary = 'correspondence_id';
	
	/**
	 * 
	 * Get correspondence by table and key
	 * 
	 * @param string $strForeignTable
	 * @param int $intForeignKey
	 * 
	 * @return object $objData
	 */
	public function getCorrespondence($strForeignTable, $intForeignKey)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from(array('c' => 'correspondence'))
					->joinLeft(array('u' => 'user'), 'u.user_id = c.user_id', array('u.name AS user'))
					->where('c.foreign_table = ?', $strForeignTable)
					->where('c.foreign_key = ?', $intForeignKey)
					->order('c.created DESC');
					
		$objData = $this->fetchAll($strQuery);
		return $objData;
					
	}
	
	/**
	 * Get Correspondence by unique $intCorrespondenceID
	 * @param int $intCorrespondenceID
	 * @return obj $objData
	 */
	public function getCorrespondenceById($intCorrespondenceID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from(array('c' => 'correspondence'))
					->joinLeft(array('u' => 'user'), 'u.user_id = c.user_id', array('u.name AS user'))
					->where('c.correspondence_id = ?', $intCorrespondenceID)
					->order('c.created DESC');
					
		$objData = $this->fetchRow($strQuery);
		return $objData;
		
	}
	
	public function addCorrespondence($arrData)
	{
		
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$insertID = $this->insert(array(
			'foreign_table' 	=> $arrData['foreign_table'],
			'foreign_key' 		=> $arrData['foreign_key'],
			'type'				=> $arrData['type'],
			'name'				=> $arrData['name'],
			'email'				=> $arrData['email'],
			'title'				=> $arrData['title'],
			'content'			=> $arrData['content'],
			'created' 			=> $strDate,
			'user_id' 			=> $arrData['user_id']
		));
		
		return $insertID;
		
	}
	
}