<?php

class KZ_View_Helper_Dashboard extends Zend_View_Helper_Abstract
{

    public function getGaCharts()
    {

        $objConfig = Zend_Registry::get('Zend_Config');
        $strUaCode = $objConfig->default->application->analytics;

        if (!class_exists('Google_Client')) {
            echo 'Install the Google Client Library';
            exit;
        }

        // Set the Google Client
        $objClient = new Google_Client();

        // Get the Google Service Account Config File
        $strConfigFile = APPLICATION_PATH.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'google--service-account.json';

        $objClient->setAuthConfig(json_decode(file_get_contents($strConfigFile),true));
        $objClient->setScopes([Google_Service_Analytics::ANALYTICS_READONLY,]);

        if ($objClient->isAccessTokenExpired()) {
            $objClient->refreshTokenWithAssertion();
        }

        if($arrAccessToken = $objClient->getAccessToken() !== null) {
            try {
                $objManagementProfiles = (new Google_Service_Analytics($objClient))->management_profiles;
                $intDataId = current($objManagementProfiles->listManagementProfiles((int)explode('-',$strUaCode)[1],$strUaCode)->getItems())->getId();
                return $this->googleanalytics($arrAccessToken['access_token'],$intDataId);
            } catch (Exception $objException) {
                var_dump($objException);
                exit;
            }
        }

    }

    public function googleanalytics($strAuthToken,$intDataId)
    {
        // The template file to render
        $strTemplateFile = 'googleanalytics.phtml';

        // Create new View
        $this->objView = (new Zend_View())->setScriptPath(APPLICATION_PATH.'/modules/admin/views/scripts/dashboard/')
            ->setHelperPath(LIBRARY_PATH.'/Websitebuilder/View/Helper/');

        // Parse variables to the view
        return $this->renderPartial($strTemplateFile,[
            'strAuthToken' => $strAuthToken,
            'intDataId' => $intDataId,
        ]);
    }

}