<?php
class KZ_Models_News extends KZ_Controller_Table
{
	
	protected $_name 	= 'news';
	protected $_primary = 'news_id';

    public $resultsCount;

	public function __construct($config = array())
    {
        $this->resultsCount = $this->getTotalResultsCount();
        parent::__construct($config);
    }

    /**
	 * 
	 * Get All Newss
	 * @return 	array 	$arrData
	 */
	public function getNews($intNewsTypeID = false, $intCategoryID = false, $intStatus = false, $intYear = false, $intLimit = false)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->setIntegrityCheck(false)
						->from('news', array('*'))
						->joinLeft('category', 'category.category_id = news.category_id', array('category.name as category', 'category.color as category_color'));
		
		if($intNewsTypeID !== false) {
			
			if(is_array($intNewsTypeID)) {
				$strQuery->where('news.news_type_id IN(?)', $intNewsTypeID);	
			} else {
				$strQuery->where('news.news_type_id = ?', $intNewsTypeID);	
			}

		}
		
		if($intCategoryID !== false) {
			
			if(is_array($intCategoryID)) {
				$strQuery->where('category.category_id IN(?)', $intCategoryID);	
			} else {
				$strQuery->where('category.category_id = ?', $intCategoryID);	
			}
			
		}
		
		if($intStatus !== false) {
			$strQuery->where('news.status = ?', $intStatus)
            ->where(' ( news.activate_at < ?',$this->current_datetime)
            ->orWhere('news.activate_at IS NULL )');
		}
		
		if($intYear !== false && is_numeric($intYear) && strlen($intYear) == 4) {
			$strQuery->where('news.date >= ?', $intYear.'-01-01')
				->where('news.date <= ?', $intYear.'-12-31');
		}

		if($intLimit !== false) {
			$strQuery->limit($intLimit);
		}
		
		$strQuery->order(array('date DESC', 'time DESC'));
		
		// Return array	
		return $this->returnData($strQuery);
	}
	
	/**
	 * Get Matching News (Search)
	 */ 
	public function getMatchingNews($strKeywords, $intLimit = 10)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->distinct(true)
					->from('news', array('news_id','name','nameSlug','image','created','category_id','date'))
					->joinLeft('news_content', 'news_content.news_id = news.news_id', array())
					->joinLeft('category', 'category.category_id = news.category_id', array('name AS category_name', 'color AS category_color'))
					->where('(news.status = ?', 1)
					->where('news_content.status = ?)', 1)
                    ->where(' ( news.activate_at < ?',$this->current_datetime)
                    ->orWhere('news.activate_at IS NULL )')
					->where("(news.name LIKE ?", '%'.$strKeywords.'%')
					->orWhere("news.name LIKE ?", $strKeywords.'%')
					->orWhere("news.name LIKE ?", '%'.$strKeywords)
					->orWhere("news.name LIKE ?", $strKeywords)
					->orWhere("news_content.data LIKE ?", '%'.$strKeywords.'%')
					->orWhere("news_content.data LIKE ?", $strKeywords.'%')
					->orWhere("news_content.data LIKE ?", '%'.$strKeywords)
					->orWhere("news_content.data LIKE ?)", $strKeywords)
					->order(array('date DESC', 'time DESC', 'news_id DESC'))
					->limit($intLimit);
					
		return $this->returnData($strQuery);
	}
	
	/**
	 * 
	 * Get News by category ID
	 * @return 	array 	$arrData
	 */
	public function getNewsByCategoryID($intCategoryID = false, $intNewsTypeID = false, $intStatus = 1, $intYear = false, $intLimit = 10)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->setIntegrityCheck(false)
						->from('news', array('news_id', 'name', 'nameSlug', 'created', 'date', 'time'))
						->joinLeft('category', 'category.category_id = news.category_id', array('category.name as category', 'category.color as color'));

		if($intCategoryID !== false) {
			$strQuery->where('news.category_id = ?', $intCategoryID);
		}
		
		if($intNewsTypeID !== false) {
			$strQuery->where('news.news_type_id = ?', $intNewsTypeID);
		}
		
		if($intYear !== false && is_numeric($intYear) && strlen($intYear) == 4) {
			$strQuery->where('news.date >= ?', $intYear.'-01-01')
				->where('news.date <= ?', $intYear.'-12-31');
		}
						
		$strQuery->order(array('date DESC','time DESC','news_id DESC'))
				 ->limit($intLimit);

		// Return array	
		return $this->returnData($strQuery);
	}
	
	/**
	 * 
	 * Get last Rank By News ID
	 * @param integer $intNewsID
	 * @return integer $intLastRank;
	 */
	public function getLastNewsRank($intNewsID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news_content', array('MAX(rank) as lastRank'))
					->where('news_content.news_id = ?', $intNewsID);

		$arrData = $this->returnData($strQuery, 'array','fetchRow');
		
		if(isset($arrData) && is_array($arrData)) {
			
			if(isset($arrData['lastRank']) && is_numeric($arrData['lastRank'])) {
				 return $arrData['lastRank'];  
			}
			
		}
		
		return 0;
		
	}

    public function getLastNewsCategoriesRank()
    {
        $strQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('news_category', array('MAX(rank) as lastRank'));

        $arrData = $this->returnData($strQuery, 'array','fetchRow');

        if(isset($arrData) && is_array($arrData)) {

            if(isset($arrData['lastRank']) && is_numeric($arrData['lastRank'])) {
                return $arrData['lastRank'];
            }

        }

        return 0;

    }

	/**
	 * Get News Types
	 * @param boolean $strReturnAssoc
	 * @return array $arrNewsTypes
	 */
	public function getNewsTypes($strReturnAssoc = false)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news_type');

		$arrData = $this->returnData($strQuery);
					
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
	 * Get News By ID
	 * @param 	integer $intNewsID
	 * @return 	array 	$arrNews
	 */
	public function getNewsByID($intNewsID)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->where('news_id = ?', $intNewsID);

		// Return array	
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getNewsBySlug($strSlug, $intStatus = 1)
	{
		$strQuery = $this->select()
					->where('nameSlug = ?', $strSlug)
					->where('status = ?', $intStatus);

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}

    public function getMatchingNewsBySlug($strSlug, $strReturnAssoc = false)
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
     * @param $intNewsID
     * @param $strTags
     * @param int $intLimit
     * @return array
     */
    public function getNewsByTags($intNewsID, $strTags, $intLimit = 10)
    {

        $objQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('news', '*')
            ->joinLeft('category', 'category.category_id = news.category_id', ['category.name as category', 'category.color as category_color',])
            ->where('news.news_id != ?', $intNewsID);

        if (false !== strpos($strTags, ',')) {
            $arrTags = explode(',', $strTags);
            $intTotalTags = count($arrTags);
            foreach ($arrTags as $intTagKey => $intTagID) {
                $strCloseStatement = ((($intTagKey + 1) === $intTotalTags) ? ' )' : '');
                if (0 === $intTagKey) {
                    $objQuery->where('( news.tags LIKE ?', '%,' . $intTagID)
                        ->orWhere('news.tags LIKE ?', $intTagID . ',%')
                        ->orWhere('news.tags = ?' . $strCloseStatement, $intTagID);
                } else {
                    $objQuery->orWhere('news.tags LIKE ?', '%,' . $intTagID)
                        ->orWhere('news.tags LIKE ?', $intTagID . ',%')
                        ->orWhere('news.tags = ?' . $strCloseStatement, $intTagID);
                }
            }
        } else {
            $objQuery->where('news.tags = ?', $strTags);
        }

        $objQuery->where('news.status = ?', 1)
            ->order('news.date DESC')
            ->limit($intLimit);
        return $this->returnData($objQuery);
    }
	
	public function getLatestNews($intLimit = 10)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news', '*')
					->joinLeft('category', 'category.category_id = news.category_id', array('category.name as category', 'category.color as category_color'))
					->where('news.status = ?', 1)
                    ->where(' ( news.activate_at < ?',$this->current_datetime)
		            ->orWhere('news.activate_at IS NULL )')
					->order('news.date DESC')
					->limit($intLimit);

		return $this->returnData($strQuery);
		
	}
	
	/**
	 * 
	 * Add News
	 * @param 	array 	$arrNews
	 * @return 	int 	$intInsertID
	 */
	public function addNews($arrNews)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Insert the Data
		$intInsertID = $this->insert(array_merge($arrNews, array('created' => $strDate)));
		
		// Return Insert ID
		return $intInsertID;
	}
	
	/**
	 * 
	 * Update News by unique News ID
	 * @param 	integer $intNewsID
	 * @param 	array 	$arrNews
	 * @return 	integer $intUpdateID
	 */
	public function updateNews($intNewsID, $arrNews)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Update the Data
		$intUpdateID = $this->update(array_merge($arrNews, array('lastmodified' => $strDate)), "news_id = $intNewsID");
		
		// Return Update ID
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete News by unique News ID
	 * @param 	integer $intNewsID
	 * @return	integer	$intDeleteID
	 */
	public function deleteNews($intNewsID)
	{
		
		$intDeleteID = $this->delete("news_id = $intNewsID");
		return $intDeleteID;
	}

	/**
	 * 
	 * Get News Content by News ID
	 * @param integer $intNewsID
	 * @return array $arrNewsContent
	 */
	public function getNewsContent($intNewsID, $intStatus = false)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news_content')
					->join('content_type', 'content_type.content_type_id = news_content.content_type_id', array('template'))
					->where('news_id = ?', $intNewsID);
					
		if($intStatus !== false) {
			$strQuery->where('news_content.status = ?', $intStatus);
		}
		
		$strQuery->order('news_content.rank');
					
		return $this->returnData($strQuery);

	}
	
	/**
	 * 
	 * Get News Content By ID
	 * @param integer $intNewsContentID
	 * @return array $arrNewsContent
	 */
	public function getNewsContentByID($intNewsContentID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news_content')
					->where('news_content_id = ?', $intNewsContentID);
					
		return $this->returnData($strQuery, 'array', 'fetchrow');
		
	}
	
	/**
	 * 
	 * Get News Content Backups
	 * @param integer $intNewsID
	 * @return array $arrNewsContentBackups
	 */
	public function getNewsContentBackups($intNewsID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news_content_backup')
					->where('news_content_id = ?', $intNewsID)
					->order('revision DESC');

		return $this->returnData($strQuery);
		
	}
	
	/**
	 * 
	 * Get News Content Backup by ID
	 * @param integer $intNewsContentBackupID
	 * @return array $arrNewsContentBackup
	 */
	public function getNewsBackupContentByID($intNewsContentBackupID)
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news_content_backup')
					->where('news_content_backup_id = ?', $intNewsContentBackupID);

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	/**
	 * 
	 * Add News Content Backup
	 * @param array $arrContentBackupData
	 * @return array $intInsertID
	 */
	public function addNewsContentBackup($arrContentBackupData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('news_content_backup', array_merge($arrContentBackupData, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	/**
	 * 
	 * Get Last News Content Backup Revision By News Content Backup ID
	 * @param integer $intContentBackupID
	 * @return integer $intLastNewsContentBackupRevision
	 */
	public function getLastNewsContentBackupRevision($intContentBackupID)
	{
		
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('news_content_backup', array('MAX(revision) as lastRevision'))
					->where('news_content_backup.news_content_id = ?', $intContentBackupID);

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
	 * Update News Content By News Content ID
	 * @param integer $intNewsContentID
	 * @param array $arrNewsContentData
	 * @return integer $intUpdateID
	 */
	public function updateNewsContent($intNewsContentID, $arrNewsContentData)
	{
		// Create lastmodified date
		$objDate 		= new Zend_Date();
		$strDate 		= $objDate->toString('yyyy-MM-dd HH:mm:ss');

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('news_content', array_merge($arrNewsContentData, array('lastmodified' => $strDate)), "`news_content_id` = $intNewsContentID");
		
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete News Content By News Content ID
	 * @param integer $intNewsContentID
	 * @return integer $intDeleteID
	 */
	public function deleteNewsContent($intNewsContentID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('news_content', "`news_content_id` = $intNewsContentID");
		
		return $intDeleteID;
	}
	
	public function deleteNewsContentByNewsID($intNewsID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('news_content', "`news_id` = $intNewsID");
		
		return $intDeleteID;
	}
	
	/**
	 * 
	 * Update News Content Rank
	 * @param integer $intNewsContentID
	 * @param integer_type $intRank
	 */
	public function updateNewsContentRank($intNewsContentID, $intRank)
	{

		$arrUpdateData = array(
			'rank'		=> $intRank
		);

		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('news_content', $arrUpdateData, "`news_content_id` = $intNewsContentID");
		
		return $intUpdateID;
		
	}
	
	/**
	 * 
	 * Add News Content
	 * @param array $arrNewsContent
	 * @return integer $intInsertID
	 */
	public function addNewsContent($arrNewsContent) {
		
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('news_content', array_merge($arrNewsContent, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	public function getNewsCategories($intStatus = false)
	{
		$strQuery = $this->select()
		->setIntegrityCheck(false)
		->from('news_category');
		
		if($intStatus !== false) {
			$strQuery->where('status = ?', $intStatus);
		}

		$strQuery->order('rank');
		
		$arrData = $this->returnData($strQuery);
		
		if(! empty($arrData) && is_array($arrData)) {
			
			// Set Models
			$objModelAgenda	= new KZ_Models_Agenda();
			$objModelPage	= new KZ_Models_Pages();
			
			foreach($arrData as $intDataKey => $arrDataRow) {
				
				switch($arrDataRow['foreign_table']) {
					
					case 'other':

						break;
					
					case 'news':
							$arrDataNews	= self::getNewsByID($arrDataRow['foreign_key']);
							$arrData[$intDataKey]['link']	= '/nieuws/'.$arrDataNews['nameSlug'];
						break;
						
					case 'agenda':
							$arrDataAgenda	= $objModelAgenda->getAgendaByID($arrDataRow['foreign_key']);
							$arrData[$intDataKey]['link']	= '/agenda/'.$arrDataAgenda['nameSlug'];	
						break;
					
					default:
					case 'page':
							$arrDataPage	= $objModelPage->getPageByID($arrDataRow['foreign_key']);
							$arrData[$intDataKey]['link']	= '/'.$arrDataPage['menu_url'];
						break;
				}

			}
		
		}
		
		return $arrData;
	}
	
	public function getNewsCategory($intNewsCategoryID)
	{
		$strQuery = $this->select()
		->setIntegrityCheck(false)
		->from('news_category')
		->where('news_category_id = ?', $intNewsCategoryID);
	
		return $this->returnData($strQuery,'array','fetchRow');
	}
	
	public function addNewsCategory($arrData)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intInsertID 	= $objDb->insert('news_category', array_merge($arrData, array('created' => $strDate)));
		
		return $intInsertID;
	}
	
	public function updateNewsCategory($intID, $arrData)
	{
		// Create Lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
	
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('news_category', array_merge($arrData, array('lastmodified' => $strDate)), "news_category_id = $intID");
	
		return $intUpdateID;
	}
	
	public function deleteNewsCategory($intID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID 	= $objDb->delete('news_category', "news_category_id = $intID");
	
		return $intDeleteID;
	}
	
	/**
	 *
	 * Get News for the Datatable
	 *
	 * @return object $objData
	 */
	public function getNewsForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
	{
		if($booReturnTotal === true) {
			$strQuery 		= $this->select('COUNT(news_id) AS total');
			$objData 	= $this->fetchAll($strQuery);
			return count($objData);
		}
	
		$strQuery 		= $this->select()
								->setIntegrityCheck(false)
								->from('news', array('*'))
								->joinLeft('category', 'category.category_id = news.category_id', array('category.name as category', 'category.color as category_color'));

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