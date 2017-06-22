<?php
class Admin_SocialController extends KZ_Controller_Action {

    public function shareAction()
    {
        // Get Config Object
        $objConfig = Zend_Registry::get('Zend_Config');

        // Disable layout and view
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Set Post Params
        $arrPostParams = $this->_getAllParams();

        if(! empty($arrPostParams['social']) && ! empty($arrPostParams['type']) && ! empty($arrPostParams['id'])) {

            switch($arrPostParams['social']) {

                case 'twitter':

                    $connection = '';
                    $connection->$host = "https://api.twitter.com/1.1/";
                    $connection->ssl_verifypeer = TRUE;

                    break;

            }

        }


    }

}