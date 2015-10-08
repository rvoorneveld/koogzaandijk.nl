<?php
class KZ_View_Helper_Notification extends Zend_View_Helper_Abstract
{ 

	public function notification($strType ,$strMessage)
	{
		// Build html
		$strReturn 	= '	<div class="notification '.$strType.'">
							<p><strong>'.ucfirst($strType).'</strong> '.$strMessage.'</p>
						</div>';
		
		return $strReturn;
		
	}

}