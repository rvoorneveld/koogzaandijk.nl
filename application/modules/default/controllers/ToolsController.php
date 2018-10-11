<?php
class Toolscontroller extends KZ_Controller_Action
{
    const UPLOAD_DIR = '/upload/';


    public function newsfilterAction()
    {
        // Disable layout and view
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Get Config
        $objConfig = Zend_Registry::get('Zend_Config');

        // Set Status
        $intStatus = 1;

        // Get Limit
        $intLimit = $objConfig->news->maxRelated;

        // Set All Params
        $arrParams = $this->getAllParams();

        // Get Category ID Param
        $intCategoryID = (false === empty($arrParams['id']) && true === is_numeric($arrParams['id'])) ? $arrParams['id'] : false;

        // Set Models
        $objModelNews = new KZ_Models_News();

        if (false === empty($arrParams['type']) && 'allnews' === $arrParams['type']) {
            // Get News By category ID
            $arrNews = $objModelNews->getNewsByCategoryID($intCategoryID, false, $intStatus, false, $intLimit);

            // Set Defaults
            $arrNewsByMonth = [];

            // Check if News was found
            if (isset($arrNews) && is_array($arrNews) && count($arrNews) > 0) {

                foreach ($arrNews as $intNewsKey => $arrNewsItem) {

                    // Get News Content
                    $arrNewsContent = $objModelNews->getNewsContent($arrNewsItem['news_id'], 1);

                    // Check if News content was found
                    if (isset($arrNewsContent) && is_array($arrNewsContent) && count($arrNewsContent) > 0) {

                        // Set Unserialized Content
                        $arrUnserializedContent = unserialize($arrNewsContent[0]['data']);

                        if (isset($arrUnserializedContent) && is_array($arrUnserializedContent) && count($arrUnserializedContent) > 0) {

                            foreach ($arrUnserializedContent as $strDataKey => $strDataValue) {

                                // Find Title
                                if (strstr($strDataKey, 'title')) {
                                    $arrNewsItem['content']['title'] = stripslashes($arrUnserializedContent[$strDataKey]);
                                }

                                // Find Text
                                if (strstr($strDataKey, 'text')) {
                                    $arrNewsItem['content']['text'] = stripslashes(KZ_View_Helper_Truncate::truncate(strip_tags(str_replace(array('<br />', '<br>'), ' ', $arrUnserializedContent[$strDataKey])), 50, '', '/nieuws/' . $arrNewsItem['nameSlug'] . '/'));
                                }

                            }

                        }

                        $intMonth = (int)$this->view->date()->format($arrNewsItem['date'], 'MM');

                        // Check if Month wasn't set yet
                        if (!isset($arrNewsByMonth[$intMonth])) {
                            // Set empty Month array
                            $arrNewsByMonth[$intMonth] = array();
                        }

                        $arrNewsByMonth[$intMonth][] = $arrNewsItem;

                    }

                }

            }

            echo json_encode($arrNewsByMonth);
            exit;

        }

        $arrData = [];
        $booBlogFilterSelected = false;
        if (false === $intCategoryID || true === $booBlogFilterSelected = $this->blogFilterSelected($intCategoryID)) {
            $objModelBlog = new KZ_Models_Blog();
            $arrBlog = $objModelBlog->getLatestBlogItems($intLimit);

            $objModelCategory = new KZ_Models_Categories();
            $arrCategory = $objModelCategory->getCategory(KZ_Controller_Action::BLOG_CATEGORY_ID);

            $arrData = [];
            if (false === empty($arrBlog) && true === is_array($arrBlog)) {
                $objDate = new Zend_Date();
                foreach ($arrBlog as $arrItem) {
                    $objDate->set($arrItem['created']);
                    $arrData[$objDate->toValue()] = [
                        'blog_id' => $arrItem['id'],
                        'name' => $arrItem['blogName'].': '.$arrItem['title'],
                        'nameSlug' => $arrItem['slug'],
                        'blogSlug' => 'blog/'.$arrItem['blogSlug'],
                        'date' => $objDate->toString('yyyy-MM-dd'),
                        'created' => $arrItem['created'],
                        'color' => $arrCategory['color'],
                        'category' => $arrCategory['name'],
                    ];
                }
            }
        }

        if (false === $booBlogFilterSelected) {
            // Get News By category ID
            $arrUnorderedData = $objModelNews->getNewsByCategoryID($intCategoryID, 2, $intStatus, false, $intLimit);
            if (false === empty($arrData) && true === is_array($arrData)) {
                $arrData = KZ_View_Helper_News::orderByDateAndTime($arrUnorderedData, 'news', $arrData);
            } else {
                $arrData = array_reverse($arrUnorderedData);
            }
        }

        echo json_encode($arrData);
        exit;

    }

    private function blogFilterSelected($id)
    {
        return KZ_Controller_Action::BLOG_CATEGORY_ID === (int)$id;
    }

	public function searchAction()
	{
		// Disable layout and view 
		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
		// Get All Params
		$arrParams				= $this->_getAllParams();
		/*$arrParams['search'] 	= 'Finale';*/
		
		// Check if Search param was found
		if(isset($arrParams['search']) && ! empty($arrParams['search'])) {
			
			// Set Models
			$objModelNews		= new KZ_Models_News();
			$objModelAgenda		= new KZ_Models_Agenda();
			$objModelPages		= new KZ_Models_Pages();
			
			// Set Defaults
			$strHtml					= '';
			
			// Get All matching News
			$arrMatchingNews	= $objModelNews->getMatchingNews($arrParams['search']);
			
			// Check if News was found
			if(isset($arrMatchingNews) && is_array($arrMatchingNews) && count($arrMatchingNews) > 0) {
									
				$strHtml		.= '<li class="category">'.$this->view->translate('News').'</li>';
				
				foreach($arrMatchingNews as $intMatchingNewsKey => $arrMatchingNewsItem) {
					$strHtml	.= '	<li class="result news">
											<a class="zapp" href="'.ROOT_URL.'/nieuws/'.$arrMatchingNewsItem['nameSlug'].'" title="'.stripslashes($arrMatchingNewsItem['name']).'">
												<span class="date_container">
													<span class="bullet" title="'.stripslashes($arrMatchingNewsItem['category_name']).'" style="background: '.$arrMatchingNewsItem['category_color'].';"></span>
													<span class="date newstag">
														<time datetime="'.$this->view->date()->format($arrMatchingNewsItem['date'], 'dd-MM-yyyy').'">'.$this->view->date()->format($arrMatchingNewsItem['date'], 'dd-MM').'</time>
													</span>
												</span>
												<span class="title">'.stripslashes($arrMatchingNewsItem['name']).'</span>
											</a>
										</li>';
				}
				
			}
			
			// Get All matching Agenda
			$arrMatchingAgenda	= $objModelAgenda->getMatchingAgenda($arrParams['search']);
			
			// Check if Agenda was found
			if(isset($arrMatchingAgenda) && is_array($arrMatchingAgenda) && count($arrMatchingAgenda) > 0) {
									
				$strHtml		.= '<li class="category">'.$this->view->translate('Agenda').'</li>';
				
				foreach($arrMatchingAgenda as $intMatchingAgendaKey => $arrMatchingAgendaItem) {
					
					$strHtml	.= '	<li class="result agenda">
											<a href="'.ROOT_URL.'/agenda/'.$arrMatchingAgendaItem['nameSlug'].'" title="'.stripslashes($arrMatchingAgendaItem['name']).'">
												<span class="date_container">
													<span class="date newstag">
														<time datetime="'.$this->view->date()->format($arrMatchingAgendaItem['date_start'], 'dd-MM-yyyy').'">'.$this->view->date()->format($arrMatchingAgendaItem['date_start'], 'dd-MM-yyyy').'</time>';

					if($arrMatchingAgendaItem['date_start'] != $arrMatchingAgendaItem['date_end']) {
						$strHtml	.= ' / <time datetime="'.$this->view->date()->format($arrMatchingAgendaItem['date_end'], 'dd-MM-yyyy').'">'.$this->view->date()->format($arrMatchingAgendaItem['date_end'], 'dd-MM-yyyy').'</time>';
					}
					
					$strHtml	.= '				</span>
												</span>
												<span class="title">'.stripslashes($arrMatchingAgendaItem['name']).'</span>
											</a>
										</li>';
				}
				
			}
			
			// Get All matching Pages
			$arrMatchingPages	= $objModelPages->getMatchingPages($arrParams['search']);
			
			// Check if Pages where found
			if(isset($arrMatchingPages) && is_array($arrMatchingPages) && count($arrMatchingPages) > 0) {
									
				$strHtml		.= '<li class="category">'.$this->view->translate('Pages').'</li>';
				
				foreach($arrMatchingPages as $intMatchingPageKey => $arrMatchingPageItem) {
					
					$strHtml	.= '	<li class="result pages">
											<a href="'.ROOT_URL.'/'.$arrMatchingPageItem['menu_url'].'" title="'.stripslashes($arrMatchingPageItem['menu_name']).'">
												<span class="title">'.stripslashes($arrMatchingPageItem['menu_name']).'</span>
											</a>
										</li>';
				}
				
			}
			
			if($strHtml == '') {
				$strHtml = '<li class="category">'.$this->view->translate('Geen resultaten').'</li>
							<li>Er zijn geen resultaten die overeenkomen met uw zoekopdracht</li>';
			}
			
			echo $strHtml;
		}

	}

	public function teaminfoAction()
	{
		// Disable layout and view 
		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
		// Get All Params
		$arrParams				= $this->_getAllParams();

		// Set Models
		$objModelMatches		= new KZ_Models_Matches();
		$objModelTeams			= new KZ_Models_Teams();
		
		// Get Match Data
		$arrMatch				= $objModelMatches->getMatchByID($arrParams['match_id']);

		// Set Team ID
		$intTeamID              = (($arrMatch['team_home_clubteam'] === '1') ? $arrMatch['team_home_id'] : $arrMatch['team_away_id']);

		// Get Team
		$arrTeam				= $objModelTeams->getTeams($intTeamID);

		// Set Defaults
		$strLocation 			= '';
		
		if(isset($arrTeam) && is_array($arrTeam) && count($arrTeam) > 0) {
			
			if(! empty($arrMatch['facility_name']) && ! empty($arrMatch['facility_id'])) {
				$strLocation = '<li><a href="https://www.korfbal.nl/competitie/#/clubs" target="_blank" title="Route naar '.stripslashes($arrMatch['facility_name']).'">'.stripslashes($arrMatch['facility_name']).'</a></li>';
			}
			
			echo '
				<div class="close_match_info"><a href="javascript:void(0);"><span aria-hidden="true">&#xe006;</span></a></div>
				<ul>					
					'.$strLocation.'
					<li><a href="/team/'.$arrTeam['name'].'">Compleet programma en standen</a></li>
					<li><a href="/wedstrijd/'.$arrParams['match_id'].'">Meer wedstrijd informatie</a></li>
				</ul>';
		}
		
		
	}

	public function rssAction()
	{
		// Disable layout and view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		// Set Models
		$objModelNews	= new KZ_Models_News();
		
		// Get all News
		$arrNews		= $objModelNews->getNews();
		
		// Set Default Rss feed data
		$arrFeedData = array(
				'title'			=> 'Korfbalvereniging KZ/Thermo4U',
				'description'	=> 'Korfbalvereniging KZ/Thermo4U Rss feed',
				'link'			=> 'http://www.koogzaandijk.nl/rss/',
				'charset'		=> 'utf8',
				'entries'		=>	array()
		);
		
		if(! empty($arrNews) && is_array($arrNews)) {
			foreach($arrNews as $intNewsKey => $arrNewsRow) {
				if($arrNewsRow['status'] == 1) {
					
					// Set Default News Description
					$strNewsDescription		= '';
					
					// Get News Content
					$arrNewsContent	= $objModelNews->getNewsContent($arrNewsRow['news_id']);

					if(! empty($arrNewsContent) && is_array($arrNewsContent)) {
						
						// Only show first piece of content in Rss description field
						$arrNewsContent 		= $arrNewsContent[0];
												
						// Unserialize Data
						$arrUnserializedData	= unserialize($arrNewsContent['data']);
						
						foreach($arrUnserializedData as $strDataKey => $strDataValue) {

							// Check for Text Title
							if(strstr($strDataKey, '_title')) {
								$strNewsTitle		= $strDataValue;
							}
							
							// Check for Text Key
							if(strstr($strDataKey, '_text')) {
								
								// Add Text to Description
								$strNewsDescription .= $strDataValue;
								
							}
							
						}

					}
					
					// Check if Title wasn't set by content
					if(empty($strNewsTitle)) {
						$strNewsTitle	= $arrNewsRow['name'];
					}
					
					// Set Feed Entry
					$arrFeedData['entries'][] = array(
						'guid'			=> $arrNewsRow['news_id'],
						'title'			=> stripslashes($strNewsTitle),
						'description'	=> substr(strip_tags(stripslashes($strNewsDescription)),0,100),
						'link'			=> ROOT_URL.'/nieuws/'.$arrNewsRow['nameSlug'].'/',
						'lastUpdate'	=> strtotime($arrNewsRow['lastmodified'])
					);
				}
			}
		}
		
		// Set Content Type to text/xml
		$this->getResponse()->setHeader('Content-type', 'text/xml');
		
// 		Zend_Debug::dump($arrFeedData);
// 		exit;
		
		// Import Array to Feed
		$strFeed = Zend_Feed::importArray($arrFeedData,'rss');
		
		// Parse Feed to View
		echo $strFeed->send();

	}

	public function loginAction()
	{
		// Disable layout and view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Set validation Object
		$objValidateEmail   = new Zend_Validate_EmailAddress();

		// Set Defaults
		$strError       = false;
		$intMemberID    = false;

		// Get All Params
		$arrParams  = $this->_getAllParams();

		// Look for Errors
		if(empty($arrParams['email'])) {
			$strError = $this->view->translate('U heeft geen e-mail adres ingevuld');
		} elseif(! $objValidateEmail->isValid($arrParams['email'])) {
			$strError = $this->view->translate('U heeft geen geldig e-mail adres ingevuld');
		} elseif(empty($arrParams['password'])) {
			$strError = $this->view->translate('U heeft uw wachtwoord niet ingevuld');
		} else {

			// Set Members Model
			$objModelMembers    = new KZ_Models_Members();

			// Get Member By Email and Password
			$arrMember          = $objModelMembers->getMemberByEmailAndPassword($arrParams['email'],md5($arrParams['password']));

			// Check if Member was found
			if(! empty($arrMember) && is_array($arrMember)) {

				// Set Member ID
				$intMemberID    = $arrMember['members_id'];

			} else {
				$strError       = $this->view->translate('De combinatie van uw gebruikersnaam en wachtwoord is niet correct');
			}

		}

		// Return Error when found
		if($strError !== false) {
			echo $strError;
			exit;
		}

		// No errors, header to profile page
		if($intMemberID !== false) {

			// Member was found, check if Member profile must be created

			// Set Profile Model
			$objModelProfile        = new KZ_Models_Profile();

			// Search Profile in Database
			$arrProfile = $objModelProfile->getProfileByMemberID($intMemberID);
			$strSecurityKey = $arrProfile['code'];

			// Get Member By Code and Password
			$objModelMembers = new KZ_Models_Members();
			$arrMember = $objModelMembers->getMember($intMemberID);

			// Check if Member Profile was found
			if(! empty($arrMember) && is_array($arrMember)) {

				// Set Profile Session
				$objModelSession = new KZ_Controller_Session();
				$objModelSession->setProfileSession($arrMember);

			}

			// Get Cookie Info
			$objModelCookie = new KZ_Controller_Action_Helper_Cookie();
			$strCookieData = $objModelCookie->getCookie('KZ_Logins');

			// Set Cookie Profile Codes array
			$arrSecurityCodes       = array();
			if($strCookieData !== false) {
				if(strstr($strCookieData,',')) {
					$arrSecurityCodes   = explode(',', $strCookieData);
				} else {
					$arrSecurityCodes   = array($strCookieData);
				}
			}

			// Check if new Security Key must be added to Cookie
			if(! in_array($strSecurityKey, $arrSecurityCodes)) {
				array_push($arrSecurityCodes,$strSecurityKey);
			}

			// Get Config
			$objConfig 				= Zend_Registry::get('Zend_Config');

			// Set Cookie
			$objModelCookie->setCookie('KZ_Logins', implode(',',$arrSecurityCodes), $objConfig->cookie->lifetime);

			// Set Date Object
			$objDate = new Zend_Date();
			$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

			// Check if Profile was found
			if(! empty($arrProfile) && is_array($arrProfile)) {

				// Update Profile
				$arrUpdateData = array(
					'code'          => $strSecurityKey,
					'lastmodified'  => $strDate
				);

				$objModelProfile->update($arrUpdateData, "member_id = $intMemberID");

			}

			echo $intMemberID;
			exit;

		}

	}

	public function loginautoAction()
	{
		// Disable layout and view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Get All Params
		$arrParams  = $this->_getAllParams();

		// Get Data
		if(! empty($arrParams['data'])) {

			// Decode The Data
			$strDecodedData     = base64_decode($arrParams['data']);

			// Explode Data
			$arrDecodedData     = explode('_',$strDecodedData);

			// Set Member ID
			$intMemberID        = $arrDecodedData[0];

			// Set Security Code
			$strSecurityKey     = $arrDecodedData[1];

			// Get Cookie Info
			$objModelCookie         = new KZ_Controller_Action_Helper_Cookie();
			$strCookieData          = $objModelCookie->getCookie('KZ_Logins');

			// Set Cookie Profile Codes array
			$arrSecurityCodes       = array();
			if($strCookieData !== false) {
				if(stristr($strCookieData,',')) {
					$arrSecurityCodes   = explode(',', $strCookieData);
				} else {
					$arrSecurityCodes   = array($strCookieData);
				}
			}

			// Check if new Security Key must be added to Cookie
			if(in_array($strSecurityKey, $arrSecurityCodes)) {

				// Get Member By Code and Password
				$objModelMembers     = new KZ_Models_Members();
				$arrMember          = $objModelMembers->getMember($intMemberID);

				// Check if Member was found
				if(! empty($arrMember) && is_array($arrMember)) {

					// Set Profile Session
					$objModelSession    = new KZ_Controller_Session();
					$objModelSession->setProfileSession($arrMember);

					// Return Member ID
					echo $intMemberID;
					exit;
				}

			}

		}

	}

	public function loginpasswordAction()
	{
		// Disable layout and view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Set Defaults
		$strError       = false;

		// Get All Params
		$arrParams  = $this->_getAllParams();

		if(empty($arrParams['password'])) {
			$strError = $this->view->translate('U heeft uw wachtwoord niet ingevuld');
		} elseif(empty($arrParams['data'])) {
			$strError = $this->view->translate('U kon niet worden ingelogd');
		} else {

			// Decode The Data
			$strSecurityKey     = base64_decode($arrParams['data']);

			// Set Members Model
			$objModelMembers    = new KZ_Models_Members();

			// Get Member By Code and Password
			$arrMember          = $objModelMembers->getMemberByCodeAndPassword($strSecurityKey,md5($arrParams['password']));

			// Check if Member was found
			if(! empty($arrMember) && is_array($arrMember)) {

				$objModelSession    = new KZ_Controller_Session();
				$objModelSession->setProfileSession($arrMember);

				// Set Member ID
				$intMemberID    = $arrMember['members_id'];

			} else {
				$strError       = $this->view->translate('Uw wachtwoord is niet correct');
			}

		}

		// Return Error when found
		if($strError !== false) {
			echo $strError;
			exit;
		}

		echo $intMemberID;
		exit;

	}

	public function logoutAction()
	{
		// Unset Profile Session
		$objControllerSession   = new KZ_Controller_Session();
		$objControllerSession->unsetProfileSession();

		// Redirect to Homepage
		$this->_redirect(ROOT_URL);
	}

	public function uploadavatarAction()
	{
		// Disable layout and view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Set document Root
		$strDocumentRoot		= $_SERVER['DOCUMENT_ROOT'];

		// Check if upload dir exists
		if(!is_dir($strDocumentRoot.'/upload/')) {
			mkdir($strDocumentRoot.'/upload/');
			chmod($strDocumentRoot.'/upload/', 0777);
		}

		// Set the Upload Directory
		$strUploadDirectory		= $strDocumentRoot.'/upload/avatars/';

		// Check if cards upload dir exists
		if(!is_dir($strUploadDirectory)) {
			mkdir($strUploadDirectory);
			chmod($strUploadDirectory, 0777);
		}

		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrParams  = $this->_getAllParams();

			// Set the Allowed File types
			$arrAllowedFileTypes		= array('jpg','gif','png','jpeg');

			// Upload the file(s)
			$jsonControllerUpload		= (new KZ_Controller_FileUpload)->doFileUpload($strUploadDirectory,$arrAllowedFileTypes, false, true);

			// Check for Error
			if($jsonControllerUpload == 'error') {
				// Disable Layout and View
				$this->_helper->layout()->disableLayout();
				$this->getHelper('viewRenderer')->setNoRender();
				echo $jsonControllerUpload;
				exit;
			}

			// Set the Return Data after upload
			$arrUploadData				= json_decode($jsonControllerUpload, true);

			// Set Image Session
			$objNameSpace				= new Zend_Session_Namespace('Frontend_Image_Upload');
			$objNameSpace->cardphoto	= $arrUploadData;

			echo $jsonControllerUpload;

		}
	}

	public function docropavatarAction() {

		// Disable Layout and View
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Check if we have post data for crop
		if($this->getRequest()->isPost()) {

			// Set Image Session
			$objNameSpace			= new Zend_Session_Namespace('Frontend_Image_Upload');
			$arrUploadData		    = $objNameSpace->cardphoto;

			// Get All Params
			$arrPostParams			= $this->_getAllParams();

			// Set the image location
			$strImage				= $_SERVER['DOCUMENT_ROOT'].'/upload/avatars/'.$arrUploadData['image_name'];

			// Set Original Image Dimensions
			$intOriginalImageWidth  = $arrUploadData['dimensions'][0];
			$intOriginalImageHeight = $arrUploadData['dimensions'][1];

			// Calculate Ratio
			$floRatio               = round($intOriginalImageWidth / $arrPostParams['w']);

			// Set the New Dimensions
			$arrNewDimensions		= array(
				'x'			            => $arrPostParams['x'],
				'y'			            => $arrPostParams['y'],
				'width'		            => $arrPostParams['w'],
				'height'	            => $arrPostParams['h'],
				'destination_width'     => $intOriginalImageWidth,
				'destination_height'    => $intOriginalImageHeight
			);

			// Set Image Object
			$objModelImage				= new KZ_Controller_Action_Helper_Image();

			// Resample the image
			$booImageResampled			= $objModelImage->resampleImage($strImage, $arrNewDimensions);

			// return feedback
			if($booImageResampled === true) {

				// Save Image to Profile
				$objSession     = new KZ_Controller_Session();
				$arrMember      = $objSession->getProfileSession();
				$intProfileID   = $arrMember['profile_id'];

				// Set Defaults
				$arrAvatarHistory   = array();

				if(! empty($arrMember['avatar_history'])) {
					// Set Avatar History array
					$arrAvatarHistory = explode(',',$arrMember['avatar_history']);
				}

				// Set Total Avatars History Count
				$intTotalAvatarHistory  = count($arrAvatarHistory);

				// Check if More that 7 avatars are listed
				if($intTotalAvatarHistory == 7) {
					unset($arrAvatarHistory[0]);
				}

				if(! empty($arrMember['avatar'])) {
					// Add Current Avatar to history
					array_push($arrAvatarHistory,$arrMember['avatar']);
				}

				// Set Avatar History
				$strAvatarHistory   = implode(',',$arrAvatarHistory);

				$objModelProfile    = new KZ_Models_Profile();
				$objModelProfile->updateAvatar($intProfileID,$arrUploadData['image_name'],$strAvatarHistory);

				// Save Avatar in Session
				$arrMember['avatar']            = $arrUploadData['image_name'];
				$arrMember['avatar_history']    = $strAvatarHistory;
				$objSession->setProfileSession($arrMember);

				echo '/upload/avatars/'.$arrUploadData['image_name'];
			}

		}

	}

	public function changeavatarAction()
	{
		// Disable Layout and View
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Get All Params
		$arrParams  = $this->_getAllParams();

		if(
				isset($arrParams['avatar'])
			&&  ! empty($arrParams['avatar'])
			&&  is_file($_SERVER['DOCUMENT_ROOT'].'/'.$arrParams['avatar'])
			&&  file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$arrParams['avatar'])
		) {

			// Set Plain Avatar Name
			$strAvatarName          = str_replace('/upload/avatars/', '', $arrParams['avatar']);

			// Get Profile Session
			$objControllerSession   = new KZ_Controller_Session();
			$arrMember              = $objControllerSession->getProfileSession();
			$intProfileID           = $arrMember['profile_id'];

			// Set Original Avatar
			$strOriginalAvatar      = $arrMember['avatar'];

			// Update Avatar and History
			$arrMember['avatar']    = $strAvatarName;

			// Get Avatar History
			$arrAvatarHistory       = explode(',',$arrMember['avatar_history']);

			// Set unique Array
			$arrAvatarHistory       = array_unique($arrAvatarHistory);

			// Add Original Avatar to History
			array_push($arrAvatarHistory, $strOriginalAvatar);

			// Search for Avatar in array
			$mixSearchedKey         = array_search($strAvatarName, $arrAvatarHistory);

			// Unset The matching history avatars
			if(is_numeric($mixSearchedKey)) {
				unset($arrAvatarHistory[$mixSearchedKey]);
			}

			// Set New Avatar History
			$arrMember['avatar_history'] = implode(',',$arrAvatarHistory);

			// Update Profile
			$objModelProfile    = new KZ_Models_Profile();
			$objModelProfile->updateAvatar($intProfileID,$arrMember['avatar'],$arrMember['avatar_history']);

			// Set Profile Session
			$objControllerSession->setProfileSession($arrMember);

			echo $arrParams['rel'];
		} else {
			echo false;
		}

	}

	public function updateprofilecontactAction()
	{
		// Disable Layout and View
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Get All Params
		$arrParams  = $this->_getAllParams();

		// Set Member ID
		$intMemberID        = $arrParams['members_id'];

		// Set Auto Login
		$intAutoLogin       = $arrParams['auto_login'];

		// Set Data
		unset($arrParams['action']);
		unset($arrParams['module']);
		unset($arrParams['controller']);
		unset($arrParams['members_id']);
		unset($arrParams['auto_login']);

		$arrUpdateData      = array(
			'share'         => $arrParams['share'],
			'mobile_number' => (($arrParams['mobile_number'] == '') ? NULL : $arrParams['mobile_number']),
			'email'         => (($arrParams['email'] == '') ? NULL : $arrParams['email']),
			'facebook'      => (($arrParams['facebook'] == '') ? NULL : $arrParams['facebook']),
			'twitter'       => (($arrParams['twitter'] == '') ? NULL : $arrParams['twitter'])
		);

		// Update Profile
		$objModelProfile    = new KZ_Models_Profile();

		$intUpdateID = $objModelProfile->updateProfileContact($intMemberID, $arrUpdateData);
		$objModelProfile->updateProfile($intMemberID, array('auto_login' => $intAutoLogin));

		echo $intUpdateID;
	}

	public function removefromserverAction()
	{
		// Disable the layout and the render file
		$this->_helper->layout()->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();

		// Check if we have a image or file selected
		$strImage = $this->_getParam('image');
		$strFile = $this->_getParam('file');

		// Only remove if file is in Post Data
		if (!is_null($strImage) && $strImage != '') {

			// Set the File Location
			$strImageFile = $_SERVER['DOCUMENT_ROOT'].$strImage;

			// Check if file exists, if so remove it
			if (file_exists($strImageFile)) {
				unlink($strImageFile);
			}

			// Set the Thumbnail location
			$strThumbFile = str_replace('/thumbs/','/upload/', $strImageFile);

			// Check if thumb file exists, if so remove it
			if (file_exists($strThumbFile)) {
				unlink($strThumbFile);
			}
		}

		// Only remove if file is in Post Data
		if (!is_null($strFile) && $strFile != '') {

			// Set the File Location
			$strFile = realpath(SERVER_URL.'/'.$strFile);

			// Check if file exists, if so remove it
			if (file_exists($strFile)) {
				unlink($strFile);
			}
		}
	}

	public function removefolderAction()
	{
		// Disable the layout and the render file
		$this->_helper->layout()->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();

		// Check if we have a folder and foldertype selected
		$strFolder = $this->_getParam('folder');
		$strFolderType = $this->_getParam('type');

		// Only remove if file is in Post Data
		if (!is_null($strFolder) && $strFolder != '' && $strFolderType != '' && !is_null($strFolderType)) {
			// Set the Folder to Remove
			$strCorrectedFolder = str_replace('|','/',$strFolder);

			// Set the File Location
			$strFullFolderLocation = $_SERVER['DOCUMENT_ROOT'].self::UPLOAD_DIR.$strCorrectedFolder.'/';

			// Set the Model
			$objModelLibrary = new KZ_Models_Library();

			// Remove the Folder and it content
			if (is_dir($strFullFolderLocation)) {
				$objModelLibrary->removeFolder($strFullFolderLocation);
			}

			// Set the Thumbnail location
			$strThumbFolder = str_replace('/upload/','/thumbs/',$strFullFolderLocation);
			// Remove the Thumbnail Folder and it content
			if (is_dir($strThumbFolder)) {
				$objModelLibrary->removeFolder($strThumbFolder);
			}
		}
	}

}
