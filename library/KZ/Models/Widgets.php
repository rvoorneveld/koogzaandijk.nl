<?php
class KZ_Models_Widgets extends KZ_Controller_Table
{
	
	protected $_name 	= 'widget';
	protected $_primary = 'widget_id';
	
	/**
	 * 
	 * Get All Widgetss
	 * @return 	array 	$arrData
	 */
	public function getWidgets($strReturnAssoc = false)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->setIntegrityCheck(false)
						->from('widget', array('*'));

		// Get Data
		$arrData = $this->returnData($strQuery);
		
		// Check if assoc array must be returned
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
	
	/**
	 * 
	 * Get last Rank By Widget ID
	 * @param integer $intWidgetID
	 * @return integer $intLastRank;
	 */
	public function getLastWidgetRank($intWidgetID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_content', array('MAX(rank) as lastRank'))
					->where('widget_content.widget_id = ?', $intWidgetID);

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
	 * Get Widget By ID
	 * @param 	integer $intWidgetID
	 * @return 	array 	$arrWidget
	 */
	public function getWidgetByID($intWidgetID, $intStatus = false)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->where('widget_id = ?', $intWidgetID);

		if($intStatus !== false && is_numeric($intStatus)) {
			$strQuery->where('status = ?', $intStatus);
		}
						
		// Return array	
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * 
	 * Add Widgets
	 * @param 	array 	$arrWidgets
	 * @return 	int 	$intInsertID
	 */
	public function addWidget($arrWidgets)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Insert the Data
		$intInsertID = $this->insert(array_merge($arrWidgets, array('created' => $strDate)));
		
		// Return Insert ID
		return $intInsertID;
	}
	
	/**
	 * 
	 * Update Widget by unique Widget ID
	 * @param 	integer $intWidgetID
	 * @param 	array 	$arrWidget
	 * @return 	integer $intUpdateID
	 */
	public function updateWidget($intWidgetID, $arrWidget)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Update the Data
		$intUpdateID = $this->update(array_merge($arrWidget, array('lastmodified' => $strDate)), "widget_id = $intWidgetID");
		
		// Return Update ID
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete Widgets by unique Widgets ID
	 * @param 	integer $intWidgetsID
	 * @return	integer	$intDeleteID
	 */
	public function deleteWidget($intWidgetID)
	{
		
		$intDeleteID = $this->delete("widget_id = $intWidgetID");
		return $intDeleteID;
	}

	/**
	 * 
	 * Get Widgets Content by Widgets ID
	 * @param integer $intWidgetsID
	 * @return array $arrWidgetsContent
	 */
	public function getWidgetContent($intWidgetsID, $intStatus = false)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_content')
					->join('content_type', 'content_type.content_type_id = widget_content.content_type_id', array('content_type.template'))
					->where('widget_id = ?', $intWidgetsID);

		if($intStatus !== false && is_numeric($intStatus)) {
			$strQuery->where('widget_content.status = ?', $intStatus);
		}
		
		$strQuery	->order('rank');
					
		return $this->returnData($strQuery);

	}
	
	/**
	 * 
	 * Get Widgets Content By ID
	 * @param integer $intWidgetsContentID
	 * @return array $arrWidgetsContent
	 */
	public function getWidgetContentByID($intWidgetsContentID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_content')
					->where('widget_content_id = ?', $intWidgetsContentID);
					
		return $this->returnData($strQuery, 'array', 'fetchrow');
		
	}
	
	/**
	 * 
	 * Get Widget Content Backups
	 * @param integer $intWidgetID
	 * @return array $arrWidgetContentBackups
	 */
	public function getWidgetContentBackups($intWidgetID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_content_backup')
					->where('widget_content_id = ?', $intWidgetID)
					->order('revision DESC');

		return $this->returnData($strQuery);
		
	}
	
	/**
	 * 
	 * Get Widget Content Backup by ID
	 * @param integer $intWidgetContentBackupID
	 * @return array $arrWidgetContentBackup
	 */
	public function getWidgetBackupContentByID($intWidgetContentBackupID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_content_backup')
					->where('widget_content_backup_id = ?', $intWidgetContentBackupID);

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * 
	 * Add Widgets Content Backup
	 * @param array $arrContentBackupData
	 * @return array $intInsertID
	 */
	public function addWidgetContentBackup($arrContentBackupData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('widget_content_backup', array_merge($arrContentBackupData, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	/**
	 * 
	 * Get Last Widget Content Backup Revision By Widget Content Backup ID
	 * @param integer $intContentBackupID
	 * @return integer $intLastWidgetContentBackupRevision
	 */
	public function getLastWidgetContentBackupRevision($intContentBackupID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_content_backup', array('MAX(revision) as lastRevision'))
					->where('widget_content_backup.widget_content_id = ?', $intContentBackupID);

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
	 * Update Widget Content By Widget Content ID
	 * @param integer $intWidgetContentID
	 * @param array $arrWidgetContentData
	 * @return integer $intUpdateID
	 */
	public function updateWidgetContent($intWidgetContentID, $arrWidgetContentData)
	{
		// Create lastmodified date
		$objDate 		= new Zend_Date();
		$strDate 		= $objDate->toString('yyyy-MM-dd HH:mm:ss');

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('widget_content', array_merge($arrWidgetContentData, array('lastmodified' => $strDate)), "`widget_content_id` = $intWidgetContentID");
		
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete Widget Content By Widget Content ID
	 * @param integer $intWidgetsContentID
	 * @return integer $intDeleteID
	 */
	public function deleteWidgetContent($intWidgetContentID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('widget_content', "`widget_content_id` = $intWidgetContentID");
		
		return $intDeleteID;
	}
	
	public function deleteWidgetContentByWidgetID($intWidgetID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('widget_content', "`widget_id` = $intWidgetID");
		
		return $intDeleteID;
	}
	
	/**
	 * 
	 * Update Widget Content Rank
	 * @param integer $intWidgetContentID
	 * @param integer_type $intRank
	 */
	public function updateWidgetsContentRank($intWidgetContentID, $intRank)
	{

		$arrUpdateData = array(
			'rank'		=> $intRank
		);

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('widget_content', $arrUpdateData, "`widget_content_id` = $intWidgetContentID");
		
		return $intUpdateID;
		
	}
	
	/**
	 * 
	 * Add Widget Content
	 * @param array $arrWidgetContent
	 * @return integer $intInsertID
	 */
	public function addWidgetContent($arrWidgetContent) {
		
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('widget_content', array_merge($arrWidgetContent, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	public function getWidgetLayouts($intStatus = false)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_layout');

		if($intStatus !== false) {
			$strQuery->where('status = ?', $intStatus);
		}
	
		return $this->returnData($strQuery);
	}
	
	public function getWidgetLayout($intLayoutID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_layout')
					->where('widget_layout_id = ?', $intLayoutID);
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getWidgetLayoutByAction($strAttachedAction)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('widget_layout')
					->where('`attached` LIKE ?', '%'.$strAttachedAction.'%');
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getWidgetLayoutByID($intWidgetLayoutID)
	{
		$strQuery = $this->select()
		->setIntegrityCheck(false)
		->from('widget_layout')
		->where('widget_layout_id = ?', $intWidgetLayoutID);
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function addWidgetLayout($arrWidgetData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('widget_layout', array_merge($arrWidgetData, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	public function updateWidgetLayout($intLayoutID, $arrWidgetData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('widget_layout', array_merge($arrWidgetData, array('lastmodified' => $strDate)), "widget_layout_id = $intLayoutID");
		
		return $intUpdateID;
	}
	
	public function deleteWidgetLayout($intLayoutID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID 	= $objDb->delete('widget_layout', "widget_layout_id = $intLayoutID");
		return $intDeleteID;
	}
	
}