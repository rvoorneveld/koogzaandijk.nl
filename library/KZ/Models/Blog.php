<?php
class KZ_Models_Blog extends KZ_Controller_Table
{

	protected $_name = 'blog_blogger';
	protected $_primary = 'id';

	/** BLOG ITEMS */

    /**
     * Function for getting blog items
     * @return array|bool|null|stdClass|Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
     */
	public function getBlog()
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('blog_item')
					->joinLeft('blog','blog_item.blogger_id = blog.id',['name']);
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
            ->join('blog','blog.id = blog_item.blogger_id',['id AS blogId','name','url'])
            ->where('blog_item.id = ?',$id);
        return $this->returnData($strQuery,'array','fetchRow');
    }

    /**
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
            ->join('blog','blog.id = blog_item.blogger_id',['name','url']);

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
     * Function for adding a blog item
     * @param $arrBlogItem
     * @return int
     */
    public function insertBlogItem($arrBlogItem)
    {
        // Set Created Date
        $objDate = new Zend_Date();
        $strCreated = $objDate->toString('yyyy/MM/dd HH:mm:ss');
        return (Zend_Db_Table::getDefaultAdapter())->insert('blog_item', array_merge($arrBlogItem,['created' => $strCreated]));
    }

    /**
     * Function for updating a blog item
     * @param $intBlogItemId
     * @param $arrBlogItem
     * @return int
     */
    public function updateBlogItem($intBlogItemId,$arrBlogItem)
    {
        return (Zend_Db_Table::getDefaultAdapter())->update('blog_item', $arrBlogItem, "id = {$intBlogItemId}");
    }

    /**
     * Function for deleting a blog item
     * @param $intBlogItemID
     * @return int
     */
    public function deleteBlogItem($intBlogItemID)
    {
        return (Zend_Db_Table::getDefaultAdapter())->delete('blog_item',"id = {$intBlogItemID}");
    }


    /** BLOGGER */

    /**
     * Function for getting a single blogger by id
     * @param $id
     * @return array|bool|null|stdClass|Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
     */
    public function getBloggerById($id)
    {
        $strQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_blogger')
            ->where('id = ?',$id);
        return $this->returnData($strQuery,'array','fetchRow');
    }

    /**
     * Get Bloggers for the Datatable
     * @param bool $booReturnTotal
     * @param null $arrLimitData
     * @param null $strSearchData
     * @param null $arrOrderData
     * @return int|Zend_Db_Table_Rowset_Abstract
     */
    public function getBloggersForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
    {
        if($booReturnTotal === true) {
            $strQuery 		= $this->select('COUNT(id) AS total')
                                ->setIntegrityCheck(false)
                                ->from('blog_blogger');
            $objData 	= $this->fetchAll($strQuery);
            return count($objData);
        }

        $strQuery 		= $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_blogger', array('*'));

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
     * Function for adding a blogger
     * @param $arrBlogger
     * @return int
     */
    public function insertBlogger($arrBlogger)
    {
        return (Zend_Db_Table::getDefaultAdapter())->insert('blog_blogger',$arrBlogger);
    }

    /**
     * Function for updating a blogger
     * @param $intBloggerId
     * @param $arrBlogger
     * @return int
     */
    public function updateBlogger($intBloggerId,$arrBlogger)
    {
        return (Zend_Db_Table::getDefaultAdapter())->update('blog_blogger', $arrBlogger, "id = {$intBloggerId}");
    }

    /**
     * Function for deleting a blogger
     * @param $intBloggerId
     * @return int
     */
    public function deleteBlogger($intBloggerId)
    {
        // Delete Blog items by Blogger id
        Zend_Db_Table::getDefaultAdapter()->delete('blog_item',"blogger_id = {$intBloggerId}");

        // Delete the Blogger
        return (Zend_Db_Table::getDefaultAdapter())->delete('blog_blogger',"id = {$intBloggerId}");
    }

}