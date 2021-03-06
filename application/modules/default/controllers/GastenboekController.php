<?php

class GastenboekController extends KZ_Controller_Action
{
    public $latestnews;
    public $objConfig;

    public $strGoogleSiteKey;
    public $strGooglePrivateKey;

    public function init()
    {
        // Get Config
        $this->objConfig = Zend_Registry::get('Zend_Config');

        $this->latestnews = ($objModelNews = new KZ_Models_News())->getLatestNews($objModelNews->resultsCount);

        // Set Google Recaptcha
        $this->strGoogleSiteKey = $this->objConfig->google->recaptcha->sitekey;
        $this->strGooglePrivateKey = $this->objConfig->google->recaptcha->privatekey;
    }

    public function indexAction()
    {
        // Set the Latest news
        $this->view->assign([
            'latest' => $this->latestnews,
        ]);
    }

    public function berichtAction()
    {
        // Set Disabled IPS
        $arrDisabledIps = $this->objConfig->guestbook->disabledips->toArray();

        // Check if current IP is blocked
        if (true === in_array($_SERVER['REMOTE_ADDR'], $arrDisabledIps, true) || true === in_array($_SERVER['SERVER_ADDR'], $arrDisabledIps, true)) {
            exit;
        }

        // Set the Latest news
        $this->view->assign([
            'latest' => $this->latestnews,
        ]);

        $booIsSend = false;

        // Set Default empty Errors
        $strError = '';

        // Set Default Variables
        $strName = '';
        $strEmail = '';
        $strMessage = '';

        // Check if we need to activate the Entry
        $strActiveParams = $this->_getParam('act');

        if (null !== $strActiveParams) {
            // Get the Data
            $arrActivateData = json_decode(base64_decode($strActiveParams), true);

            // Set the Model
            $objModelGuestbook = new KZ_Models_Guestbook();

            // Check for valid data
            $booEntryExists = $objModelGuestbook->validateData($arrActivateData);
            if (true === $booEntryExists) {
                $this->redirect('/gastenboek/');
            }
        }

        $booSuccess = false;

        // Check if we have POST data
        if (true === $this->getRequest()->isPost()) {
            // Get all POST data
            $arrPostParams = $this->getAllParams();

            if (false === empty($strResponseFromPost = $arrPostParams['g-recaptcha-response']) && true === $this->verifyIfGoogleRecaptchaSucceeded($strResponseFromPost)) {
                // Set Post Variables
                $strName = $arrPostParams['guestbook_name'];
                $strEmail = $arrPostParams['guestbook_email'];
                $strMessage = $arrPostParams['guestbook_message'];

                if (false === empty($arrPostParams['guestbook_email'])) {
                    // Set the Model
                    $objModelGuestbook = new KZ_Models_Guestbook();

                    // Set today as post day
                    $objCurrentDate = new Zend_Date();

                    $arrEntryData['guestbook_name'] = $arrPostParams['guestbook_name'];
                    $arrEntryData['guestbook_email'] = $arrPostParams['guestbook_email'];
                    $arrEntryData['guestbook_message'] = stripslashes($arrPostParams['guestbook_message']);
                    $arrEntryData['guestbook_entry_date'] = $objCurrentDate->toString('yyyy-MM-dd H:mm:ss');

                    // Set Entry Active by default
                    $arrEntryData['guestbook_entry_verified'] = 'y';

                    // Set Customer IP
                    $arrEntryData['guestbook_ip'] = $_SERVER['REMOTE_ADDR'];
                    $arrEntryData['guestbook_server_ip'] = $_SERVER['SERVER_ADDR'];

                    $intGuestbookEntryID = $objModelGuestbook->insertGuestbookData($arrEntryData);
                    if (true === is_numeric($intGuestbookEntryID) && false !== $intGuestbookEntryID) {
                        // Send the Verification E-mail
                        //$arrEntryData['ID']		= $intGuestbookEntryID;
                        //KZ_Models_Mail::sendMail('guestbook_verification', 'nl', $arrEntryData);

                        // Set the Booleans
                        $booIsSend = true;
                        $booSuccess = true;
                    }
                } else {
                    $strError = 'U heeft uw e-mail adres niet ingevuld';
                }
            } else {
                $strError = 'U bent niet door de robots verificatie check gekomen.';
            }
        }

        // Check if we had a post or is first time
        $booShowSucces = false;
        if (true === $booIsSend && false !== $booSuccess) {
            $booShowSucces = true;
        }

        $this->view->assign([
            'showsucces' => $booShowSucces,
            'showerror' => $strError,
            'guestbook_name' => $strName,
            'guestbook_email' => $strEmail,
            'guestbook_message' => $strMessage,
            'google_recaptcha_sitekey' => $this->strGoogleSiteKey,
        ]);
    }

    private function verifyIfGoogleRecaptchaSucceeded($response)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify?secret='.$this->strGooglePrivateKey.'&response='.$response.'&remoteip='.$_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $strRecaptchaResponse = curl_exec($ch);
        curl_close($ch);

        if (null !== $strRecaptchaResponse) {
            if (false === empty($arrResponse = json_decode($strRecaptchaResponse, true)) && true === is_array($arrResponse) && null !== $booVerifiedResponse = $arrResponse['success']) {
                return $booVerifiedResponse;
            }
        }

        return false;
    }
}