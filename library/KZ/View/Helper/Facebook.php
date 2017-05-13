<?php
class KZ_View_Helper_Facebook extends Zend_View_Helper_Abstract
{
	
	public function facebook($strFacebookFeed)
    {

		$s = curl_init();
        curl_setopt($s,CURLOPT_URL,$strFacebookFeed);
        $strFacebookContentJson = curl_exec($s);

        $objFacebookContent     = json_decode($strFacebookContentJson);

		if(isset($objFacebookContent) && is_object($objFacebookContent)) {
			
			echo '<ul class="facebook">';
			
			$intTotal = 1;

			foreach($objFacebookContent->feed->data as $intPostKey => $arrPost) {

				if(isset($arrPost->message) && $arrPost->message != ' ' && $intTotal <= 5) {
				
					$intTotal++;

					// Set Created Date Object
					$objCreatedDate     = new Zend_Date($arrPost->created_time);
					$intCurrentMonth    = $objCreatedDate->toString('M');
					$strCurrentMonth    = $this->view->date()->getMonth($intCurrentMonth,false,true);

				echo '	<li>
							<span class="date">'.$this->view->translate($objCreatedDate->toString('EE')).' '.$objCreatedDate->toString('dd').' '.$strCurrentMonth.' '.$objCreatedDate->toString('yyyy').' om '.$objCreatedDate->toString('HH:mm').' uur</span>';
				if(isset($arrPost->link)) {
					echo '<a href="'.$arrPost->link.'" title="'.$arrPost->message.'" target="_blank">';
				}

					echo $arrPost->message;

				if(isset($arrPost->link)) {
					echo '</a>';
				}

				echo '</li>';
				
				}

			}
			
			echo '</ul>';
			
		}
		
	}
	
}
