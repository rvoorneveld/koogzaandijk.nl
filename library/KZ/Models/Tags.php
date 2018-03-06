<?php

class KZ_Models_Tags extends KZ_Controller_Table
{

    protected $_name = 'tag';
    protected $_primary = 'tag_id';

    /**
     *
     * Get All Tags
     * @params string $strReturnAssoc
     * @return    array    $arrData
     */
    public function getTags($strReturnAssoc = false)
    {
        // Set Query string
        $strQuery = $this->select();

        // Get Data
        $arrData = $this->returnData($strQuery);

        // Check if assoc array must be returned
        if ($strReturnAssoc !== false) {
            $arrReturnAssoc = array();
            foreach ($arrData as $intReturnKey => $arrReturnData) {
                $arrReturnAssoc[$arrReturnData[$strReturnAssoc]] = $arrReturnData;
            }
            return $arrReturnAssoc;
        }

        // Return array
        return $arrData;

    }

    /**
     *
     * Get Tag By ID
     * @param    integer $intTagID
     * @return    array    $arrTag
     */
    public function getTag($intTagID)
    {
        // Set Query string
        $strQuery = $this->select()
            ->where('tag_id = ?', $intTagID);

        // Return array
        return $this->returnData($strQuery, 'array', 'fetchRow');
    }

    /**
     *
     * Add Tag
     * @param    array $arrTag
     * @return    int    $intInsertID
     */
    public function addTag($arrTag)
    {
        // Create created date
        $objDate = new Zend_Date();
        $strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

        // Insert the Data
        $intInsertID = $this->insert(array_merge($arrTag, array('created' => $strDate)));

        // Return Insert ID
        return $intInsertID;
    }

    /**
     *
     * Update Tag by unique Tag ID
     * @param    integer $intTagID
     * @param    array $arrTag
     * @return    integer $intUpdateID
     */
    public function updateTag($intTagID, $arrTag)
    {
        // Create lastmodified date
        $objDate = new Zend_Date();
        $strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

        // Update the Data
        $intUpdateID = $this->update(array_merge($arrTag, array('lastmodified' => $strDate)), "tag_id = $intTagID");

        // Return Update ID
        return $intUpdateID;
    }

    /**
     *
     * Delete Tag by unique Tag ID
     * @param    integer $intTagID
     * @return    integer    $intDeleteID
     */
    public function deleteTag($intTagID)
    {

        $intDeleteID = $this->delete("tag_id = $intTagID");
        return $intDeleteID;
    }

}