<?php

class KZ_View_Helper_Facebook extends Zend_View_Helper_Abstract
{

    public function facebook($strFacebookFeed)
    {
        $strFacebookContentJson = file_get_contents($strFacebookFeed);
        $objFacebookContent = json_decode($strFacebookContentJson);

        if (false === empty($objFacebookContent) && true === is_object($objFacebookContent)) {
            echo '<ul class="facebook">';

            $intTotal = 1;

            foreach ($objFacebookContent->feed->data as $intPostKey => $arrPost) {
                if (false === empty($arrPost->message) && $intTotal <= 5) {
                    $intTotal++;

                    // Set Created Date Object
                    $objCreatedDate = new Zend_Date($arrPost->created_time);
                    $intCurrentMonth = $objCreatedDate->toString('M');
                    $strCurrentMonth = $this->view->date()->getMonth($intCurrentMonth, false, true);

                    echo '	<li>
							<span class="date">'.$this->view->translate($objCreatedDate->toString('EE')).' '.$objCreatedDate->toString('dd').' '.$strCurrentMonth.' '.$objCreatedDate->toString('yyyy').' om '.$objCreatedDate->toString('HH:mm').' uur</span>';
                    if (false === empty($arrPost->link)) {
                        echo '<a href="'.$arrPost->link.'" title="'.$arrPost->message.'" target="_blank">';
                    }

                    echo $arrPost->message;

                    if (false === empty($arrPost->link)) {
                        echo '</a>';
                    }

                    echo '</li>';
                }
            }

            echo '</ul>';
        }
    }

}
