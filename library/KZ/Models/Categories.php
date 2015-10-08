<?php
class KZ_Models_Categories extends KZ_Controller_Table
{
	
	protected $_name 	= 'category';
	protected $_primary = 'category_id';
	
	/**
	 * 
	 * Get All Categories
	 * @param string $strReturnAssoc
	 * @return 	array 	$arrData
	 */
	public function getCategories($strReturnAssoc = false)
	{
		// Set Query string
		$strQuery 	= $this->select();

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
	 * Get Category By ID
	 * @param 	integer $intCategoryID
	 * @return 	array 	$arrCategory
	 */
	public function getCategory($intCategoryID)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->where('category_id = ?', $intCategoryID);

		// Return array	
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}
	
	public function getCategoryBySlug($strSlug)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
						->where('name = ?', $strSlug);

		// Return array	
		return $this->returnData($strQuery, 'array', 'fetchRow');
		
	}
	
	/**
	 * 
	 * Add Category
	 * @param 	array 	$arrCategory
	 * @return 	int 	$intInsertID
	 */
	public function addCategory($arrCategory)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Insert the Data
		$intInsertID = $this->insert(array_merge($arrCategory, array('created' => $strDate)));
		
		// Return Insert ID
		return $intInsertID;
	}
	
	/**
	 * 
	 * Update Category by unique Category ID
	 * @param 	integer $intCategoryID
	 * @param 	array 	$arrCategory
	 * @return 	integer $intUpdateID
	 */
	public function updateCategory($intCategoryID, $arrCategory)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');
		
		// Update the Data
		$intUpdateID = $this->update(array_merge($arrCategory, array('lastmodified' => $strDate)), "category_id = $intCategoryID");
		
		// Return Update ID
		return $intUpdateID;
	}
	
	/**
	 * 
	 * Delete Category by unique Category ID
	 * @param 	integer $intCategoryID
	 * @return	integer	$intDeleteID
	 */
	public function deleteCategory($intCategoryID)
	{
		
		$intDeleteID = $this->delete("category_id = $intCategoryID");
		return $intDeleteID;
	}
	
}