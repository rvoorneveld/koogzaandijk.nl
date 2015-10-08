<?php

class KZ_View_Helper_Date extends Zend_View_Helper_Abstract
{
	
	/**
	 * Return formatted date
	 * @param string $strDate
	 * @param string $strFormat
	 * 
	 * @return string $strFormattedDate
	 */
	public function format($strDate, $strFormat, $booReturnObject = false)
	{
		// Check if date exists
		if(is_null($strDate)) {
			return '-';
		}

		// Create a new Zend_Date object for the date input
		$objDate		= new Zend_Date(strtotime($strDate));
		
		if($booReturnObject === true) {
			
			// Return Date object
			return $objDate;
			
		} else {
			// Set Formatted Date
			$strFormattedDate	= $objDate->toString($strFormat);
			
			// Return Formatted date
			return $strFormattedDate;	
		}
	}
	
	public function getMonth($intMonth, $booReverse = false, $booFullText = true)
	{
		if($booReverse === true) {

			$arrMonths = array('Jan' => 1,'Feb' => 2,'Mar' => 3,'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);
			
			return ((isset($arrMonths[$intMonth])) ? $arrMonths[$intMonth] : 0);
			
		}

		if($booFullText === false) {
			// Set Months array
			$arrMonths = array('','jan','feb','maa','apr','mei','jun','jul','aug','sep','okt','nov','dec');
			return $arrMonths[$intMonth];
		}

		$arrMonths = array('','januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december');
		return ucfirst($arrMonths[$intMonth]);

	}
	
	public function leadzero($intNumber)
	{
		if(substr($intNumber, 0, 1) == 0)
		{
			return substr($intNumber, 1);
		}
		
		return $intNumber;
		
	}
	
	public function getSeasonStartDate()
	{
		return '2015-08-01';
	}
}