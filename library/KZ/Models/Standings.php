<?php
class KZ_Models_Standings extends KZ_Controller_Table
{
	
	protected $_name	= 'standings';
	protected $_primary	= 'standings_id';
	
	public function addStanding($arrData)
	{
		$intInsertID	= $this->insert($arrData);
		return $intInsertID;
	}
	
	public function getStandingsByPoule($strPouleName, $strSportType)
	{
		$strQuery = $this->select()
					->where('poule_name = ?', $strPouleName)
					->where('type LIKE(?)', ucfirst($strSportType))
					->order('position');
		return $this->returnData($strQuery);
	}

	public function getStandingsByPouleName($strPouleName)
	{
		$strQuery = $this->select()
			->where('poule_name = ?', $strPouleName)
			->where('clubteam = ?', 1)
			->order('position');

		return $this->returnData($strQuery);
	}
	
}