<?php
class KZ_Models_Pages extends KZ_Controller_Table
{
	
	protected $_name 	= 'page';
	protected $_primary = 'page_id';
	
	/**
	 * 
	 * Get All Pagess
	 * @return 	array 	$arrData
	 */
	public function getPages($intMenu = false, $intMenuTypeID = false, $strOrder = false, $strReturnAssoc = false)
	{
		// Set Query string
		$strQuery 	= 	$this->select();
						
		if($intMenu !== false) {
			$strQuery->where('page.menu = ?', $intMenu);
		}
		
		if($intMenuTypeID !== false) {
			$strQuery->where('page.menu_type_id = ?', $intMenuTypeID);
		}	
		
		if($strOrder !== false) {
			$strQuery->order($strOrder);
		}
		
		$objData = $this->returnData($strQuery);
		
		if($strReturnAssoc !== false) {
			$arrReturnAssoc = array();
			foreach($objData as $intReturnKey => $arrReturnData) {
				$arrReturnAssoc[$arrReturnData[$strReturnAssoc]][] = $arrReturnData;
			}
			return $arrReturnAssoc;	
		}
		
		return $objData;
	}
	
	/**
	 * Get Matching Pages (Search)
	 */ 
	public function getMatchingPages($strKeywords, $intLimit = 10)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->distinct(true)
					->from('page', array('page_id','menu_name','menu_url'))
					->joinLeft('page_content', 'page_content.page_id = page.page_id', array())
					->where('(page.status = ?', 1)
					->where('page_content.status = ?)', 1)
					->where("(page.name LIKE ?", '%'.$strKeywords.'%')
					->orWhere("page.name LIKE ?", $strKeywords.'%')
					->orWhere("page.name LIKE ?", '%'.$strKeywords)
					->orWhere("page.name LIKE ?", $strKeywords)
					->orWhere("page_content.data LIKE ?", '%'.$strKeywords.'%')
					->orWhere("page_content.data LIKE ?", $strKeywords.'%')
					->orWhere("page_content.data LIKE ?", '%'.$strKeywords)
					->orWhere("page_content.data LIKE ?)", $strKeywords)
					->order(array('page.menu ASC', 'page.rank ASC'))
					->limit($intLimit);
					
		return $this->returnData($strQuery);
	}
	
	/**
	 * 
	 * Get last Rank By Page ID
	 * @param integer $intPageID
	 * @return integer $intLastRank;
	 */
	public function getLastPageRank($intPageID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('page_content', array('MAX(rank) as lastRank'))
					->where('page_content.page_id = ?', $intPageID);

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
	 * Get Page By ID
	 * @param 	integer $intPageID
	 * @return 	array 	$arrPage
	 */
	public function getPageByID($intPageID)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->where('page_id = ?', $intPageID);

		// Return array	
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getPageBySlug($strSlug, $intMainPageID = null)
	{
		$strQuery = $this->select()
					->where('menu_url = ?', $strSlug);
					
		if(! is_null($intMainPageID) && is_numeric($intMainPageID)) {
			$strQuery->where('parent_id = ?', $intMainPageID);
		}

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}

    public function getMatchingPagesBySlug($strSlug, $strReturnAssoc = false)
    {
        $strQuery = $this->select()
            ->where('menu_url = ?', $strSlug);

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
	 * Add Pages
	 * @param 	array 	$arrPages
	 * @return 	int 	$intInsertID
	 */
	public function addPage($arrPages)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Insert the Data
		$intInsertID = $this->insert(array_merge($arrPages, array('created' => $strDate)));
		
		// Return Insert ID
		return $intInsertID;
	}
	
	/**
	 * 
	 * Update Page by unique Page ID
	 * @param 	integer $intPageID
	 * @param 	array 	$arrPage
	 * @return 	integer $intUpdateID
	 */
	public function updatePage($intPageID, $arrPage)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Update the Data
		$intUpdateID = $this->update(array_merge($arrPage, array('lastmodified' => $strDate)), "page_id = $intPageID");
		
		// Return Update ID
		return $intUpdateID;
	}
	
	/**
	 * Function for updating the Page ranks / ordering
	 *
	 * @param array $arrPageData
	 */
	public function updatePageRanks($arrPageData)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
	
		// Set Page ID and remove it from update data
		$intPageID			= $arrPageData['pageID'];
		unset($arrPageData['pageID']);
	
		// Update the Data
		$this->update(array_merge($arrPageData, array('lastmodified' => $strDate)), "page_id = '".$intPageID."'");
	}
	
	public function updateSubPagesAfterPageDelete($arrUpdateData, $intPageID)
	{
		return $this->update($arrUpdateData, "parent_id = '".$intPageID."'");
	}
	
	public function updatePageOrderingAfterDelete($intPageContentRank, $intMenuID)
	{
		// Set the Update Query
		$strQuery		= "UPDATE page SET `rank` = (rank - 1) WHERE rank > '".$intPageContentRank."' AND menu = '".$intMenuID."'";
	
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$objDb->query($strQuery);
	}
	
	/**
	 * 
	 * Delete Pages by unique Pages ID
	 * @param 	integer $intPagesID
	 * @return	integer	$intDeleteID
	 */
	public function deletePage($intPageID)
	{
		
		$intDeleteID = $this->delete("page_id = $intPageID");
		return $intDeleteID;
	}

	/**
	 * 
	 * Get Pages Content by Pages ID
	 * @param integer $intPagesID
	 * @return array $arrPagesContent
	 */
	public function getPageContent($intPagesID, $intStatus = false)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('page_content')
					->join('content_type', 'content_type.content_type_id = page_content.content_type_id', array('template'))
					->where('page_content.page_id = ?', $intPagesID);
					
		if($intStatus !== false) {
			$strQuery->where('page_content.status = ?', $intStatus);
		}
		
		$strQuery->order('page_content.rank');
					
		return $this->returnData($strQuery);

	}
	
	/**
	 * 
	 * Get Pages Content By ID
	 * @param integer $intPagesContentID
	 * @return array $arrPagesContent
	 */
	public function getPageContentByID($intPagesContentID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('page_content')
					->where('page_content_id = ?', $intPagesContentID);
					
		return $this->returnData($strQuery, 'array', 'fetchrow');
		
	}
	
	/**
	 * 
	 * Get Page Content Backups
	 * @param integer $intPageID
	 * @return array $arrPageContentBackups
	 */
	public function getPageContentBackups($intPageID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('page_content_backup')
					->where('page_content_id = ?', $intPageID)
					->order('revision DESC');

		return $this->returnData($strQuery);
		
	}
	
	/**
	 * 
	 * Get Page Content Backup by ID
	 * @param integer $intPageContentBackupID
	 * @return array $arrPageContentBackup
	 */
	public function getPageBackupContentByID($intPageContentBackupID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('page_content_backup')
					->where('page_content_backup_id = ?', $intPageContentBackupID);

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * 
	 * Add Pages Content Backup
	 * @param array $arrContentBackupData
	 * @return array $intInsertID
	 */
	public function addPageContentBackup($arrContentBackupData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('page_content_backup', array_merge($arrContentBackupData, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	/**
	 * 
	 * Get Last Page Content Backup Revision By Page Content Backup ID
	 * @param integer $intContentBackupID
	 * @return integer $intLastPageContentBackupRevision
	 */
	public function getLastPageContentBackupRevision($intContentBackupID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('page_content_backup', array('MAX(revision) as lastRevision'))
					->where('page_content_backup.page_content_id = ?', $intContentBackupID);

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
	 * Update Page Content By Page Content ID
	 * @param integer $intPageContentID
	 * @param array $arrPageContentData
	 * @return integer $intUpdateID
	 */
	public function updatePageContent($intPageContentID, $arrPageContentData)
	{
		// Create lastmodified date
		$objDate 		= new Zend_Date();
		$strDate 		= $objDate->toString('yyyy-MM-dd HH:mm:ss');

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('page_content', array_merge($arrPageContentData, array('lastmodified' => $strDate)), "`page_content_id` = $intPageContentID");
		
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete Page Content By Page Content ID
	 * @param integer $intPagesContentID
	 * @return integer $intDeleteID
	 */
	public function deletePageContent($intPageContentID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('page_content', "`page_content_id` = $intPageContentID");
		
		return $intDeleteID;
	}
	
	public function deletePageContentByPageID($intPageID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('page_content', "`page_id` = $intPageID");
		
		return $intDeleteID;
	}
	
	/**
	 * 
	 * Update Page Content Rank
	 * @param integer $intPageContentID
	 * @param integer_type $intRank
	 */
	public function updatePagesContentRank($intPageContentID, $intRank)
	{

		$arrUpdateData = array(
			'rank'		=> $intRank
		);

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('page_content', $arrUpdateData, "`page_content_id` = $intPageContentID");
		
		return $intUpdateID;
		
	}
	
	/**
	 * 
	 * Add Page Content
	 * @param array $arrPageContent
	 * @return integer $intInsertID
	 */
	public function addPageContent($arrPageContent) {
		
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('page_content', array_merge($arrPageContent, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
}