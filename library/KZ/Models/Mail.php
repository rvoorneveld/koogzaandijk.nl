<?php
class KZ_Models_Mail extends KZ_Controller_Table
{
	
	public function sendMail($strMailTemplateName, $strLanguage, $arrData)
	{
		// Get config
		$objConfig 				= Zend_Registry::get('Zend_Config');
		
		// Set the Model
		$objModelMailing		= new KZ_Models_Mailings();
		
		// Get the Mailing Template
		$objMailingTemplate		= $objModelMailing->getMailingByLanguageAndName($strLanguage, $strMailTemplateName);
		
		// Check if Mailingtemplate isn't empty
		if(!is_null($objMailingTemplate)) {
			
			// Set some default vars for mail
			$booAddAttachment		= false;
			$arrBccAddresses		= array();
			
			// Set Mailing Path
			$strMailingPath			= $objConfig->default->resources->viewPath;
			
			switch($strMailTemplateName) 
			{
				
				case 'lostpassword':
						// Transform object to single Array
						$arrMailingTemplate			= $objMailingTemplate->toArray();
						
						// Set the vars
						$strMailingSubject			= $arrMailingTemplate['title'];
						$strMailingText				= $arrMailingTemplate['text'];
						
						// Replace the vars in text
						$strMailingText				= str_replace('{full_name}', $arrData['name'], $strMailingText);
						$strMailingText				= str_replace('{password}', base64_decode($arrData['password']), $strMailingText);
						
						// Set the Mail Vars
						$strToAddres				= $arrData['email'];
						$strMailingSubject			= $arrMailingTemplate['title'];
									
					break;
					
				case 'newaccount':
						// Transform object to single Array
						$arrMailingTemplate			= $objMailingTemplate->toArray();
						
						// Set the vars
						$strMailingSubject			= $arrMailingTemplate['title'];
						$strMailingText				= $arrMailingTemplate['text'];
						
						// Replace the vars in text
						$strMailingText				= str_replace('{full_name}', $arrData['name'], $strMailingText);
						$strMailingText				= str_replace('{username}', $arrData['email'], $strMailingText);
						$strMailingText				= str_replace('{password}', base64_decode($arrData['password']), $strMailingText);
						
						// Set the Mail Vars
						$strToAddres				= $arrData['email'];
						$strMailingSubject			= $arrMailingTemplate['title'];
									
					break;
					
				case 'guestbook_verification':
					// Transform object to single Array
					$arrMailingTemplate			= $objMailingTemplate->toArray();
				
					// Set the vars
					$strMailingSubject			= $arrMailingTemplate['title'];
					$strMailingText				= $arrMailingTemplate['text'];
				
					// Replace the vars in text
					$arrLinkData				= array('email' => $arrData['guestbook_email'], 'ID' => $arrData['ID']);
					$strActivateLink			= '<a href="'.ROOT_URL.'/gastenboek/bericht/act/'.base64_encode(json_encode($arrLinkData)).'">Klik hier</a>.';
					$strMailingText				= str_replace('{verificatie_link}', $strActivateLink, $strMailingText);
					$strMailingText				= str_replace('{guestbook_name}', $arrData['guestbook_name'], $strMailingText);
				
					// Set the Mail Vars
					$strToAddres				= $arrData['guestbook_email'];
					$strMailingSubject			= $arrMailingTemplate['title'];
				
					break;

                case 'program_changes':
                    // Transform object to single Array
                    $arrMailingTemplate			= $objMailingTemplate->toArray();

                    // Set the vars
                    $strMailingSubject			= $arrMailingTemplate['title'];
                    $strMailingText				= $arrMailingTemplate['text'];

                    // Loop over the Data and create a string to replace the tag with
                    $strMatchData               = 'Geen aanpassingen gevonden';
                    if(!empty($arrData)) {
                        foreach($arrData as $intMatchID => $arrMatchChanges) {
                            $strMatch           = 'ID '.$intMatchID;
                            if(isset($arrMatchChanges['teams'])) {
                                $strMatch           = $arrMatchChanges['teams'];
                                unset($arrMatchChanges['teams']);
                            }

                            $strMatchData       = 'Match '.$strMatch.' heeft de onderstaande verandering:<br />';
                            foreach($arrMatchChanges as $intField => $arrValues) {
                                $strMatchData       .= $intField.' is veranderd van: <i style="color:red">'.$arrValues['old'].'</i> naar: <i>'.$arrValues['new'].'</i><br />';
                            }

                            $strMatchData       .= '<br />';
                        }
                    }

                    // Replace the Content tag
                    $strMailingText				= str_replace('{matches}', $strMatchData, $strMailingText);

                    // Set the Mail Vars
                    $strToAddres				= 'redactie@koogzaandijk.nl';
                    break;
			}
			
			// Set Mailing From Name and From Email
			$strFromEmail			= $objConfig->default->application->email;
			$strFromName			= $objConfig->default->application->name;
			
			// Replace Mailing Defaults
			$strMailingText			= str_replace('{application_email}', $strFromEmail, $strMailingText);
			$strMailingText			= str_replace('{application_name}', $strFromName, $strMailingText);

			// Set View Object and change BasePath
			$objView = new Zend_View();
			$objView->setBasePath($strMailingPath);
			$objView->addHelperPath('KZ/View/Helper/', 'KZ_View_Helper');

			// Get Mailing Template
			$strTemplate	= $objView->partial('mailings/main.phtml', array(
					'root_url'		=> ROOT_URL,
					'title' 		=> $strMailingSubject,
					'content' 		=> $strMailingText
			));
			
			$objMail		= new Zend_Mail('utf-8');
			
			if(APPLICATION_ENV == 'development') {
				$strToAddres = 'rick@mediaconcepts.nl';
			}
			
			$objMail->addTo($strToAddres)
					->setFrom($strFromEmail, $strFromName)
					->setSubject($strMailingSubject)
					->setBodyHtml($strTemplate);

			// add the bcc's
			if(isset($arrBccAddresses) && !empty($arrBccAddresses)) {
				foreach($arrBccAddresses as $key => $strEmailAddress) {
					$objMail->addBcc($strEmailAddress);
				}
			}
			
			if($booAddAttachment === true) {
				$objAttachment					= new Zend_Mime_Part($strPdfData);
				$objAttachment->type			= 'application/pdf';
				$objAttachment->encoding 		= Zend_Mime::ENCODING_BASE64;
				$objAttachment->disposition 	= Zend_Mime::DISPOSITION_ATTACHMENT;
				$objAttachment->filename		= 'booking_'.$intBookingID.'.pdf';

				$objMail->addAttachment($objAttachment);
			}

			if(! $objMail->send()) {
				Zend_Debug::dump($strFeedback);
				die;
			}
			
		} else {
			echo 'no template found';
			die;
		}
	}
	
}