<?php
class KZ_Models_Agenda extends KZ_Controller_Table
{
	
	protected $_name 	= 'agenda';
	protected $_primary = 'agenda_id';
	
	/**
	 * 
	 * Get All Agendas
	 * @return 	array 	$arrData
	 */
	public function getAgenda($strDate = false, $intStatus = false, $intLimit = false)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->setIntegrityCheck(false)
						->from('agenda', array('*'));
		
		if($strDate !== false) {
			$strQuery->where('date_end >= ?', $strDate);
		}
		
		if($intStatus !== false) {
			$strQuery->where('status = ?', $intStatus);
		}
		
		if($intLimit !== false) {
			$strQuery->limit($intLimit);
		}
		
		$strQuery->order('date_start');
		
		// Return array	
		return $this->returnData($strQuery);
	}
	
	/**
	 * Get Matching Agenda (Search)
	 */ 
	public function getMatchingAgenda($strKeywords)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->distinct(true)
					->from('agenda', array('agenda_id','name','nameSlug','date_start', 'date_end', 'time_start','time_end','created'))
					->joinLeft('agenda_content', 'agenda_content.agenda_id = agenda.agenda_id', array())
					->where('(agenda.status = ?', 1)
					->where('agenda_content.status = ?)', 1)
					->where("(agenda.name LIKE ?", '%'.$strKeywords.'%')
					->orWhere("agenda.name LIKE ?", $strKeywords.'%')
					->orWhere("agenda.name LIKE ?", '%'.$strKeywords)
					->orWhere("agenda.name LIKE ?", $strKeywords)
					->orWhere("agenda_content.data LIKE ?", '%'.$strKeywords.'%')
					->orWhere("agenda_content.data LIKE ?", $strKeywords.'%')
					->orWhere("agenda_content.data LIKE ?", '%'.$strKeywords)
					->orWhere("agenda_content.data LIKE ?)", $strKeywords)
					->order('date_start');

		return $this->returnData($strQuery);
	}
	
	/**
	 * 
	 * Get last Rank By Agenda ID
	 * @param integer $intAgendaID
	 * @return integer $intLastRank;
	 */
	public function getLastAgendaRank($intAgendaID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('agenda_content', array('MAX(rank) as lastRank'))
					->where('agenda_content.agenda_id = ?', $intAgendaID);

		$arrData = $this->returnData($strQuery, 'array','fetchRow');
		
		if(isset($arrData) && is_array($arrData)) {
			
			if(isset($arrData['lastRank']) && is_numeric($arrData['lastRank'])) {
				 return $arrData['lastRank'];  
			}
			
		}
		
		return 0;
		
	}
	
	/**
	 * 
	 * Get Agenda By ID
	 * @param 	integer $intAgendaID
	 * @return 	array 	$arrAgenda
	 */
	public function getAgendaByID($intAgendaID)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->where('agenda_id = ?', $intAgendaID);

		// Return array	
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getAgendaBySlug($strTitleSlug, $intStatus = 1)
	{
		$strQuery = $this->select()
					->where('nameSlug = ?', $strTitleSlug)
					->where('status = ?', $intStatus);
					
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}

    public function getMatchingAgendaBySlug($strSlug, $strReturnAssoc = false)
    {
        $strQuery = $this->select()
            ->where('nameSlug = ?', $strSlug);

        $arrData = $this->returnData($strQuery);

        if($strReturnAssoc !== false && ! empty($strReturnAssoc)) {

            // Set Default Return Assoc Data
            $arrReturnAssoc = array();

            // Loop Through Data
            foreach($arrData as $arrDataRow) {
                $arrReturnAssoc[$arrDataRow[$strReturnAssoc]]   = $arrDataRow;
            }

            // Return The Associative Array
            return $arrReturnAssoc;

        }

        return $arrData;

    }
	
	/**
	 * 
	 * Add Agenda
	 * @param 	array 	$arrAgenda
	 * @return 	int 	$intInsertID
	 */
	public function addAgenda($arrAgenda)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Insert the Data
		$intInsertID = $this->insert(array_merge($arrAgenda, array('created' => $strDate)));
		
		// Return Insert ID
		return $intInsertID;
	}
	
	/**
	 * 
	 * Update Agenda by unique Agenda ID
	 * @param 	integer $intAgendaID
	 * @param 	array 	$arrAgenda
	 * @return 	integer $intUpdateID
	 */
	public function updateAgenda($intAgendaID, $arrAgenda)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Update the Data
		$intUpdateID = $this->update(array_merge($arrAgenda, array('lastmodified' => $strDate)), "agenda_id = $intAgendaID");
		
		// Return Update ID
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete Agenda by unique Agenda ID
	 * @param 	integer $intAgendaID
	 * @return	integer	$intDeleteID
	 */
	public function deleteAgenda($intAgendaID)
	{
		
		$intDeleteID = $this->delete("agenda_id = $intAgendaID");
		return $intDeleteID;
	}

	/**
	 * 
	 * Get Agenda Content by Agenda ID
	 * @param integer $intAgendaID
	 * @return array $arrAgendaContent
	 */
	public function getAgendaContent($intAgendaID, $intStatus = 1)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('agenda_content')
					->join('content_type', 'content_type.content_type_id = agenda_content.content_type_id', array('template'))
					->where('agenda_content.agenda_id = ?', $intAgendaID)
					->where('agenda_content.status = ?', $intStatus)
					->order('agenda_content.rank');
					
		return $this->returnData($strQuery);

	}
	
	/**
	 * 
	 * Get Agenda Content By ID
	 * @param integer $intAgendaContentID
	 * @return array $arrAgendaContent
	 */
	public function getAgendaContentByID($intAgendaContentID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('agenda_content')
					->where('agenda_content_id = ?', $intAgendaContentID);
					
		return $this->returnData($strQuery, 'array', 'fetchrow');
		
	}
	
	/**
	 * 
	 * Get Agenda Content Backups
	 * @param integer $intAgendaID
	 * @return array $arrAgendaContentBackups
	 */
	public function getAgendaContentBackups($intAgendaID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('agenda_content_backup')
					->where('agenda_content_id = ?', $intAgendaID)
					->order('revision DESC');

		return $this->returnData($strQuery);
		
	}
	
	/**
	 * 
	 * Get Agenda Content Backup by ID
	 * @param integer $intAgendaContentBackupID
	 * @return array $arrAgendaContentBackup
	 */
	public function getAgendaBackupContentByID($intAgendaContentBackupID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('agenda_content_backup')
					->where('agenda_content_backup_id = ?', $intAgendaContentBackupID);

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * 
	 * Add Agenda Content Backup
	 * @param array $arrContentBackupData
	 * @return array $intInsertID
	 */
	public function addAgendaContentBackup($arrContentBackupData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('agenda_content_backup', array_merge($arrContentBackupData, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	/**
	 * 
	 * Get Last Agenda Content Backup Revision By Agenda Content Backup ID
	 * @param integer $intContentBackupID
	 * @return integer $intLastAgendaContentBackupRevision
	 */
	public function getLastAgendaContentBackupRevision($intContentBackupID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('agenda_content_backup', array('MAX(revision) as lastRevision'))
					->where('agenda_content_backup.agenda_content_id = ?', $intContentBackupID);

		$arrData = $this->returnData($strQuery, 'array','fetchRow');
		
		if(isset($arrData) && is_array($arrData)) {
			
			if(isset($arrData['lastRevision']) && is_numeric($arrData['lastRevision'])) {
				 return $arrData['lastRevision'];  
			}
			
		}
		
		return 0;
	}

	
	/**
	 * 
	 * Update Agenda Content By Agenda Content ID
	 * @param integer $intAgendaContentID
	 * @param array $arrAgendaContentData
	 * @return integer $intUpdateID
	 */
	public function updateAgendaContent($intAgendaContentID, $arrAgendaContentData)
	{
		// Create lastmodified date
		$objDate 		= new Zend_Date();
		$strDate 		= $objDate->toString('yyyy-MM-dd HH:mm:ss');

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('agenda_content', array_merge($arrAgendaContentData, array('lastmodified' => $strDate)), "`agenda_content_id` = $intAgendaContentID");
		
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete Agenda Content By Agenda Content ID
	 * @param integer $intAgendaContentID
	 * @return integer $intDeleteID
	 */
	public function deleteAgendaContent($intAgendaContentID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('agenda_content', "`agenda_content_id` = $intAgendaContentID");
		
		return $intDeleteID;
	}
	
	public function deleteAgendaContentByAgendaID($intAgendaID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('agenda_content', "`agenda_id` = $intAgendaID");
		
		return $intDeleteID;
	}
	
	/**
	 * 
	 * Update Agenda Content Rank
	 * @param integer $intAgendaContentID
	 * @param integer_type $intRank
	 */
	public function updateAgendaContentRank($intAgendaContentID, $intRank)
	{

		$arrUpdateData = array(
			'rank'		=> $intRank
		);

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('agenda_content', $arrUpdateData, "`agenda_content_id` = $intAgendaContentID");
		
		return $intUpdateID;
		
	}
	
	/**
	 * 
	 * Add Agenda Content
	 * @param array $arrAgendaContent
	 * @return integer $intInsertID
	 */
	public function addAgendaContent($arrAgendaContent) {
		
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('agenda_content', array_merge($arrAgendaContent, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	/**
	 *
	 * Get Agenda for the Datatable
	 *
	 * @return object $objData
	 */
	public function getAgendaForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
	{
		if($booReturnTotal === true) {
			$strQuery 		= $this->select('COUNT(agenda_id) AS total');
			$objData 	= $this->fetchAll($strQuery);
			return count($objData);
		}
	
		$strQuery 		= $this->select()
								->setIntegrityCheck(false)
								->from('agenda', array('*'));

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
}