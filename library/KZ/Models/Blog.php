<?php
class KZ_Models_Blog extends KZ_Controller_Table
{

	protected $_name = 'blog';
	protected $_primary = 'id';

    /**
     * Function for getting blog items
     * @return array|bool|null|stdClass|Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
     */
	public function getBlog()
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('blog_item')
					->joinLeft('blog','blog_item.blog_id = blog.id',['name']);
		return $this->returnData($strQuery);
	}

    /**
     * Function for getting a single blog item by id
     * @param $id
     * @return array|bool|null|stdClass|Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
     */
	public function getBlogItemById($id)
    {
        $strQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_item')
            ->join('blog','blog.id = blog_item.blog_id',['id AS blogId','name','url'])
            ->where('blog_item.id = ?',$id);
        return $this->returnData($strQuery,'array','fetchRow');
    }

    /**
     *
     * Get Blog item for the Datatable
     *
     * @return object $objData
     */
    public function getBlogItemsForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
    {
        if($booReturnTotal === true) {
            $strQuery 		= $this->select('COUNT(id) AS total');
            $objData 	= $this->fetchAll($strQuery);
            return count($objData);
        }

        $strQuery 		= $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_item', array('*'))
            ->join('blog','blog.id = blog_item.blog_id',['name','url']);

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

    public function insertBlogItem($arrBlogItem)
    {
        // Set Created Date
        $objDate = new Zend_Date();
        $strCreated = $objDate->toString('yyyy/MM/dd HH:mm:ss');
        return (Zend_Db_Table::getDefaultAdapter())->insert('blog_item', array_merge($arrBlogItem,['created' => $strCreated]));
    }

    public function updateBlogItem($intBlogItemId,$arrBlogItem)
    {
        return (Zend_Db_Table::getDefaultAdapter())->update('blog_item', $arrBlogItem, "id = {$intBlogItemId}");
    }

    public function deleteBlogItem($intBlogItemID)
    {
        return (Zend_Db_Table::getDefaultAdapter())->delete('blog_item',"id = {$intBlogItemID}");
    }

}