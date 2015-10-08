<?php

class KZ_Models_Mailings extends KZ_Controller_Table
{

	protected $_name 			= 'mailing';
	protected $_primary 		= 'mailing_id';
	
	/**
	 * Get all mailings
	 * @return object $objData
	 */
	public function getMailings()
	{
		$strQuery = $this->select();

		$objData = $this->fetchAll($strQuery);
		return $objData;
	}
	
	/**
	 * Get Mailing by unique Mailing ID
	 * @param int $intMailingID
	 * 
	 * @return object $objData
	 */
	public function getMailingByID($intMailingID)
	{
		$strQuery = $this->select()
					->where('mailing_id = ?', $intMailingID);
					
		$objData = $this->fetchRow($strQuery);
		return $objData;
		
	}
	
	/**
	 * Get Mailing by Language and Name
	 * @param string $strLanguage
	 * @param string $strMailingName
	 */
	public function getMailingByLanguageAndName($strLanguage, $strMailingName)
	{
		
		$strQuery = $this->select()
					->where('language = ?', $strLanguage)
					->where('name = ?', $strMailingName);
					
		$objData = $this->fetchRow($strQuery);
		return $objData;
		
	}
	
	/**
	 * Add new Mailing
	 * @param array $arrData
	 * @return int $intInsertID
	 */
	public function addMailing($arrData)
	{
		// Create Created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$intInsertID = $this->insert(array(
			'language' 		=> $arrData['language'],
			'name' 			=> $arrData['name'],
			'title'			=> $arrData['title'],
			'text'			=> $arrData['text'],
			'created'		=> $strDate
		));
		return $intInsertID;
	}
	
	/**
	 * Edit Mailing by unique Mailing ID 
	 * @param int $intMailingID
	 * @param array $arrData
	 * @return int $intUpdateID
	 */
	public function updateMailing($intMailingID, $arrData)
	{
		// Create Lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$intUpdateID = $this->update(array(
			'title'			=> $arrData['title'],
			'text'			=> $arrData['text'],
			'lastmodified'	=> $strDate
		), "mailing_id = $intMailingID");

		return $intUpdateID;

	}
	
}