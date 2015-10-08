<?php

class KZ_Controller_Action_Helper_Score
{

	public function align($intScore, $strAlign)
	{
		if($intScore < 10) {
			if($strAlign == 'right') {
				return $intScore.'&nbsp;&nbsp;';
			} else {
				return '&nbsp;&nbsp;'.$intScore;
			}
		}
		return $intScore;
	}

}