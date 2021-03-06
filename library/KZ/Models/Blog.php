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
					->joinLeft('blog_blogger','blog_item.blogger_id = blog_blogger.id',['name']);
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
            ->join('blog_blogger','blog_blogger.id = blog_item.blogger_id',['id AS blogId','name','slug'])
            ->where('blog_item.id = ?',$id);
        return $this->returnData($strQuery,'array','fetchRow');
    }

    public function getBlogItemBySlug($slug)
    {
        $strQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_item')
            ->join('blog_blogger','blog_blogger.id = blog_item.blogger_id',['id AS blogId','name','slug'])
            ->where('blog_item.slug = ?',$slug);
        return $this->returnData($strQuery,'array','fetchRow');
    }

    /**
     * @param null $limit
     * @return array
     */
    public function getLatestBlogItems($limit = null)
    {
        $strQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_item')
            ->join('blog_blogger', 'blog_blogger.id = blog_item.blogger_id', ['id AS blogId', 'name as blogName', 'slug as blogSlug',])
            ->where('blog_blogger.status = ?', KZ_Controller_Action::STATE_ACTIVE)
            ->order('blog_item.created DESC');

        if (null !== $limit && true === is_numeric($limit)) {
            $strQuery->limit($limit);
        }

        return $this->returnData($strQuery);
    }

    public function getBlogItemsByBlogger($id,$max = false)
    {
        $strQuery = $this->select()
                    ->setIntegrityCheck(false)
                    ->from('blog_item')
                    ->where('blog_item.blogger_id = ?',$id);

        if($max !== false && is_numeric($max)) {
            $strQuery->limit($max);
        }

        $strQuery->order('created DESC');

        return $this->returnData($strQuery);
    }

    public function getBlogItemIdsByBloggerId($id)
    {
        $objQuery = $this->select()
                    ->from('blog_item', ['id',])
                    ->setIntegrityCheck(false)
                    ->where('blogger_id = ?',$id);
        $arrData = $this->returnData($objQuery);

        if(! empty($arrData) && is_array($arrData)) {
            $arrReturn = [];
            foreach($arrData as $arrRow) {
                array_push($arrReturn,$arrRow['id']);
            }
            return $arrReturn;
        }
        return [];
    }

    /**
     * Get Blog item for the Datatable
     *
     * @return object $objData
     */
    public function getBlogItemsForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
    {
        // Get The User
        $objNamespace = new Zend_Session_Namespace('KZ_Admin');
        $this->user = $objNamespace->user;
        $intBloggerID = $this->user['blogger_id'];

        if($booReturnTotal === true) {
            $strQuery = $this->select('COUNT(id) AS total');
            if(! empty($intBloggerID) && ! is_null($intBloggerID)) {
                $strQuery->where('id = ?',$intBloggerID);
            }
            $objData 	= $this->fetchAll($strQuery);
            return count($objData);
        }

        $strQuery 		= $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_item', array('*'))
            ->join('blog_blogger','blog_blogger.id = blog_item.blogger_id',['name','slug']);

        if(!is_null($strSearchData)) {
            $strQuery->where($strSearchData);
        }

        if(! empty($intBloggerID) && ! is_null($intBloggerID)) {
            $strQuery->where('blog_blogger.id = ?',$intBloggerID);
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
        return Zend_Db_Table::getDefaultAdapter()->insert('blog_item', array_merge($arrBlogItem,['created' => (new Zend_Date())->toString('yyyy/MM/dd HH:mm:ss')]));
    }

    /**
     * Function for updating a blog item
     * @param $intBlogItemId
     * @param $arrBlogItem
     * @return int
     */
    public function updateBlogItem($intBlogItemId,$arrBlogItem)
    {
        return Zend_Db_Table::getDefaultAdapter()->update('blog_item', $arrBlogItem, "id = {$intBlogItemId}");
    }

    /**
     * Function for deleting a blog item
     * @param $intBlogItemID
     * @return int
     */
    public function deleteBlogItem($intBlogItemID)
    {
        return Zend_Db_Table::getDefaultAdapter()->delete('blog_item',"id = {$intBlogItemID}");
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
     * Function for getting blogger by url slug
     *
     * @param $slug
     * @return array|bool|null|stdClass|Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
     */
    public function getBloggerBySlug($slug)
    {
        $strQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_blogger')
            ->where('slug = ?',$slug);
        return $this->returnData($strQuery,'array','fetchRow');
    }

    public function getBloggerItems($intBloggerId,$intTotalItems = 10)
    {
        $strQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_item')
            ->joinLeft('blog_blogger','blog_blogger.id = blog_item.blogger_id',[])
            ->where('blog_blogger.id = ?',$intBloggerId)
            ->order('blog_item.created DESC')
            ->limit($intTotalItems);
        return $this->returnData($strQuery);
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
     * Function for getting bloggers
     * @param $max
     * @param $active
     * @return array
     */
    public function getBloggers($max = false, $active = true)
    {
        $strQuery = $this->select();

        if (true === $active) {
            $strQuery->where('status = ?', KZ_Controller_Action::STATE_ACTIVE);
        }

        if (false !== $max && true === is_numeric($max)) {
            $strQuery->order(new Zend_Db_Expr('RAND()'))->limit($max);
        } else {
            $strQuery->order('name');
        }

        return $this->returnData($strQuery);
    }

    /**
     * Function for adding a blogger
     * @param $data
     * @return int
     */
    public function insertBlogger($data)
    {
        return Zend_Db_Table::getDefaultAdapter()->insert('blog_blogger', $data);
    }

    /**
     * Function for updating a blogger
     * @param $intBloggerId
     * @param $arrBlogger
     * @return int
     */
    public function updateBlogger($intBloggerId,$arrBlogger)
    {
        return Zend_Db_Table::getDefaultAdapter()->update('blog_blogger', $arrBlogger, "id = {$intBloggerId}");
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
        return Zend_Db_Table::getDefaultAdapter()->delete('blog_blogger',"id = {$intBloggerId}");
    }

    public function getBlogItemReactions($id,$status = false)
    {
        $strQuery = $this->select()
                    ->setIntegrityCheck(false)
                    ->from('blog_reaction','*')
                    ->joinLeft('profile', 'profile.profile_id = blog_reaction.profile_id',['profile.avatar'])
                    ->joinLeft('members', 'members.members_id = profile.member_id',['members.firstname','members.insertion','members.lastname'])
                    ->where('blog_item_id = ?',$id);

        if(! empty($status) && in_array($status,[KZ_Controller_Action::STATE_ACTIVE,KZ_Controller_Action::STATE_INACTIVE])) {
            $strQuery->where('status = ?',$status);
        }

        $strQuery->order('blog_reaction.created DESC');




        return $this->returnData($strQuery);
    }

    public function getReactionById($id)
    {
        $strQuery = $this->select()
                    ->setIntegrityCheck(false)
                    ->from('blog_reaction')
                    ->where('id = ?', $id);
        return $this->returnData($strQuery,'array', 'fetchRow');
    }

    public function updateReaction($id,$data)
    {
        // Update the Reaction
        return Zend_Db_Table::getDefaultAdapter()->update('blog_reaction',$data,"id = {$id}");
    }

    public function addReaction($reaction)
    {
        return Zend_Db_Table::getDefaultAdapter()->insert('blog_reaction', $reaction);
    }

    public function getBlogReactionsForTable($booReturnTotal = false, $arrLimitData = null, $strSearchData = null, $arrOrderData = null)
    {

        if($booReturnTotal === true) {
            $strQuery = $this->select('COUNT(id) AS total')
                        ->setIntegrityCheck(false)
                        ->from('blog_reaction');
            $objData 	= $this->fetchAll($strQuery);
            return count($objData);
        }

        $strQuery 		= $this->select()
            ->setIntegrityCheck(false)
            ->from('blog_reaction', array('*'))
            ->join('profile','blog_reaction.profile_id = profile.profile_id',[])
            ->join('members', 'members.members_id = profile.member_id',"CONCAT(members.firstname, ' ',members.insertion, ' ', members.lastname) AS name");

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