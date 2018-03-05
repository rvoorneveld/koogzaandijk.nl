<?php
class KZ_Models_Volunteers extends KZ_Controller_Table
{
    protected $_name = 'volunteer';
    protected $_primary = 'id';

    public function getVolunteersByDate($strDateStart, $strDateEnd)
    {
        $objQuery = $this->select()
                    ->where('date >= ?', $strDateStart)
                    ->where('date <= ?', $strDateEnd)
                    ->order('date')
                    ->order('timeStart');
        return $this->returnData($objQuery);
    }

}
