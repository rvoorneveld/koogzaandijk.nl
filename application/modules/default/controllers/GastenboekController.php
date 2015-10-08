<?php
class GastenboekController extends KZ_Controller_Action
{
	public $latestnews;
	public $objConfig;
	
	public function init()
	{
		// Get Config
		$this->objConfig		    = Zend_Registry::get('Zend_Config');
		
		// Set Max Related items
		$intMaxItems				= (int)$this->objConfig->news->maxRelated * 2;
		
		// Set Models
		$objModelNews				= new KZ_Models_News();
		
		// Get Latest News
		$arrLatestNews				= $objModelNews->getLatestNews($intMaxItems);
		
		$this->latestnews			= $arrLatestNews;

	}
	
	public function indexAction()
	{
		// Set the Latest news
		$this->view->latest			= $this->latestnews;
	}
	
	public function berichtAction()
	{
		// Set Disabled IPS
		$arrDisabledIps             = $this->objConfig->guestbook->disabledips->toArray();

		// Check if current IP is blocked
		if(
			in_array($_SERVER['REMOTE_ADDR'],$arrDisabledIps)
			||  in_array($_SERVER['SERVER_ADDR'],$arrDisabledIps)
		) {
			exit;
		}

		// Set the Latest news	
		$this->view->latest			= $this->latestnews;
		$booIsSend					= false;

		// Set Default empty Errors
		$strError                   = '';

		// Set Default Variables
		$strName                    = '';
		$strEmail                   = '';
		$strMessage                 = '';

		// Check if we need to activate the Entry
		$strActiveParams			= $this->_getParam('act');
		if(!is_null($strActiveParams)) {
			// Get the Data
			$arrActivateData		= json_decode(base64_decode($strActiveParams), true);
			
			// Set the Model
			$objModelGuestbook		= new KZ_Models_Guestbook();
			
			// Check for valid data
			$booEntryExists			= $objModelGuestbook->validateData($arrActivateData);
			if($booEntryExists === true) {
				$this->_redirect('/gastenboek/');
			} 
		}
		
		// Check if we have POST data
		if($this->getRequest()->isPost()) {

			// Get all POST data
			$arrPostParams			= $this->_getAllParams();
			$booSuccess				= false;

			// Set Post Variables
			$strName                    = $arrPostParams['guestbook_name'];
			$strEmail                   = $arrPostParams['guestbook_email'];
			$strMessage                 = $arrPostParams['guestbook_message'];

			if(isset($arrPostParams['guestbook_email']) && $arrPostParams['guestbook_email'] != '') {
				// Check for a correct Captcha
				if (isset($arrPostParams['cid'])) {

					// Check for forbidden words in guestbook message
					if(stripos($arrPostParams['guestbook_message'],'viagra') === false) {

						$strCaptchaID 		= trim($arrPostParams['cid']);
						$objCaptchaSession	= new Zend_Session_Namespace('Zend_Form_Captcha_'.$strCaptchaID);

						if ($arrPostParams['captcha'] == $objCaptchaSession->word) {

							// Set the Model
							$objModelGuestbook			= new KZ_Models_Guestbook();

							// Set today as post day
							$objCurrentDate				= new Zend_Date();

							$arrEntryData['guestbook_name']				= $arrPostParams['guestbook_name'];
							$arrEntryData['guestbook_email']			= $arrPostParams['guestbook_email'];
							$arrEntryData['guestbook_message']			= stripslashes($arrPostParams['guestbook_message']);
							$arrEntryData['guestbook_entry_date']		= $objCurrentDate->toString('yyyy-MM-dd H:mm:ss');

							// Set Entry Active by default
							$arrEntryData['guestbook_entry_verified']	= 'y';

							// Set Customer IP
							$arrEntryData['guestbook_ip']	            = $_SERVER['REMOTE_ADDR'];
							$arrEntryData['guestbook_server_ip']        = $_SERVER['SERVER_ADDR'];

							$intGuestbookEntryID		= $objModelGuestbook->insertGuestbookData($arrEntryData);
							if(is_numeric($intGuestbookEntryID) && $intGuestbookEntryID !== false) {

								// Send the Verification E-mail
								//$arrEntryData['ID']		= $intGuestbookEntryID;
								//KZ_Models_Mail::sendMail('guestbook_verification', 'nl', $arrEntryData);

								// Set the Booleans
								$booIsSend			= true;
								$booSuccess			= true;
							}
						} else {
							$strError = 'U heeft de verificatiecode niet correct overgenomen.';
						}

					} else {
						$strError = 'U kunt dit bericht niet publiceren.';
					}

				} else {
					$strError = 'Er is iets mis met de verificatiecode. Neem contact op met de webmaster.';
				}

			} else {
				$strError = 'U heeft uw e-mail adres niet ingevuld';
			}
		} 
		
		// Check if we had a post or is first time
		$booShowSucces				= false;
		if($booIsSend === true && $booSuccess !== false) {
			$booShowSucces			= true;
		}
		$this->view->showsucces		= $booShowSucces;

		// parse errors to view
		$this->view->showerror	    = $strError;

		
		// Set the Captcha
		$captcha = new Zend_Captcha_Image();
		$captcha->setImgDir(APPLICATION_PATH . '/../public/assets/default/image/captcha/');
		$captcha->setImgUrl($this->view->baseUrl('/assets/default/image/captcha/'));
		$captcha->setFont(APPLICATION_PATH . '/../public/assets/default/font/rockwellstd-bold-webfont.ttf');
		$captcha->setWordlen(6);
		$captcha->setFontSize(28);
		$captcha->setLineNoiseLevel(8);
		$captcha->setWidth(220);
		$captcha->setHeight(80);
		$captcha->generate();
		$this->view->captcha = $captcha;

		// Parse Variables to View
		$this->view->guestbook_name     = $strName;
		$this->view->guestbook_email    = $strEmail;
		$this->view->guestbook_message  = $strMessage;

	}
}