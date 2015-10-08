<?php
class KZ_View_Helper_Message extends Zend_View_Helper_Abstract
{ 

	public function message($strType ,$strMessage)
	{
		// Build html
		$strReturn 	= '	<div id="message" class="notification '.$strType.'" style="display: none;">
							<p><strong>'.ucfirst($strType).'</strong> '.$strMessage.'</p>
						</div>';
		$strReturn .= ' <script type="text/javascript">
							jQuery(\'document\').ready(function() {
								jQuery(\'#message\').fadeIn(\'slow\',function(){jQuery(this).delay(\'4000\').fadeOut(\'slow\');});
							});
						</script>';
		
		return $strReturn;
		
	}

}