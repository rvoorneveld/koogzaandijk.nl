<?php
class KZ_Controller_Table extends Zend_Db_Table_Abstract
{
    public $current_datetime;
    public function __construct($config = array())
    {
        $objDate = new Zend_Date();
        $this->current_datetime = $objDate->toString('yyyy-MM-dd HH:mm:ss');
        parent::__construct($config);
    }

    public function returnData($strQuery, $strReturnType = 'array', $strFetchType = 'fetchAll')
    {
        if (false === empty($strQuery)) {
            $mixReturnData = 'array' === $strReturnType ? [] : new stdClass();
            $method = ('fetchAll' === $strFetchType ? $strFetchType : 'fetchRow');

            if (null !== ($objResult = $this->$method($strQuery))
                && count($returnArray = $objResult->toArray()) > 0
            ) {
                return 'array' === $strReturnType ? $returnArray : $objResult;
            }
            return $mixReturnData;
        }
        return false;
    }
	
	public function _truncate($strTableName)
	{
		$strQuery	= "TRUNCATE table $strTableName";
		$objDb		= Zend_Db_Table::getDefaultAdapter();
		$objDb->query($strQuery);
	}
	
}