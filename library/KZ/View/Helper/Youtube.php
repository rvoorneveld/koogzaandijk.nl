<?php

class KZ_View_Helper_Youtube extends Zend_View_Helper_Abstract
{
	
	public function youtube($strYoutubeStream)
	{

		// Set Cache Location
		$strCacheLocation   = CACHE_PATH.'/youtube/';

		// Set Cache File
		$strCacheFile       = 'videos.json';

		// Get Current Time
		$intTimestamp       = time();

		// Set Default Update Video's to false
		$booUpdateVideos    = false;

		if(! file_exists($strCacheLocation.$strCacheFile)) {
			// Update Videos file
			$booUpdateVideos = true;
		} else {

			// Get mtime of video file
			$intVideosModifiedTime = filemtime($strCacheLocation.$strCacheFile);

			// Renew Youtube Video script every 12 hours
			if(($intTimestamp - $intVideosModifiedTime) > 43200) {
				// Update Videos file
				$booUpdateVideos = true;
			}

		}

		if($booUpdateVideos === true) {

			// Set the variables
			$strClientId        = "939231172527-i8ltn0qg2ppjfs1u12n6hje2pef2b32n.apps.googleusercontent.com";
			$strClientSecret    = "jTMXzhZxoWpSEEGUuj4Bz2Vt";
			$strApiKey          = "AIzaSyC7DLDewYp4h_4UJmlhsRdXy6kSEDkjvXA";
			$strRedirectUrl     = "http://www.koogzaandijk.nl";

			// Load the Google API
			$objGoogle = new KZ_Controller_Action_Google();

			// Create the Google Session
			$objGoogleSession = $objGoogle->createSession(
				$strClientId,
				$strClientSecret,
				$strApiKey,
				$strRedirectUrl
			);

			// Get Settings
			$objModelSettings       = new KZ_Models_Settings();
			$strGoogleRefreshToken  = $objModelSettings->getSettingsByKey('google_refreshtoken', true);
			$strGoogleAcceptCode    = $objModelSettings->getSettingsByKey('google_acceptcode', true);

			// Check if Refresh Code was set
			if (!empty($strGoogleRefreshToken)) {
				try {
					$objGoogleSession->refreshToken($strGoogleRefreshToken);
				} catch (Exception $e) {
					// Refresh token is invalid so force approval request to user
					if (APPLICATION_ENV != 'production') {
						var_dump($e);
						exit;
					}
				}
			}

			// Check if Accept Code is empty and refresh token is empty
			if ($strGoogleAcceptCode == '' && $strGoogleRefreshToken == '') {

				// Get Auth Url
				$strAuthUrl = $objGoogle->getAuthUrl();

				// Mail Auth Url
				if (APPLICATION_ENV == 'production') {

					mail('development@hoteliers.com', 'Please do Google Plus authentication', $strAuthUrl);

					// Give feedback
					echo "Er zijn geen video's om weer te geven";
					exit;
				} else {
					echo $strAuthUrl;
					exit;
				}

			}

			if ($strGoogleAcceptCode != '' && $strGoogleRefreshToken == '') {

				// Authenticate and Get Access Token
				$objGoogle->verify($strGoogleAcceptCode);
				$strGoogleRefreshToken = $objModelSettings->getSettingsByKey('google_refreshtoken');

				try {
					$objGoogleSession->refreshToken($strGoogleRefreshToken);
				} catch (Exception $e) {
					// Refresh token is invalid so force approval request to user
					if (APPLICATION_ENV != 'production') {
						var_dump($e);
						exit;
					}
				}
			}

			// Get Videos from Youtube
			$arrVideos  = $objGoogle->getVideos();

			// Create the folder if it doesn't exist
			if(! file_exists($strCacheLocation)) {
				mkdir($strCacheLocation);
				chmod($strCacheLocation,0777);
			}

			// Create the file if it doesn't exist
			if(! file_exists($strCacheLocation.$strCacheFile)) {
				touch($strCacheLocation.$strCacheFile);
				chmod($strCacheLocation.$strCacheFile,0777);
			}

			// Save Videos to file
			file_put_contents($strCacheLocation.$strCacheFile,json_encode($arrVideos));

		} else {

			// Get Videos from cached file
			$strJsonEncodedVideos   = file_get_contents($strCacheLocation.$strCacheFile,true);
			$arrVideos              = json_decode($strJsonEncodedVideos,true);
		}

		// Set Defaults
		$strReturnData		= '';

		// Check if Youtube Videos where found
		if(! empty($arrVideos) && is_array($arrVideos)) {
			foreach($arrVideos as $arrVideo) {

				$strReturnData .= '	<li>
										<a class="video" title="" href="https://www.youtube.com/watch?v='.$arrVideo['video_id'].'&feature=youtube_gdata_player&autoplay=1">
											<img src="'.$arrVideo['thumbnail'].'" alt="" />
											<span class="video__info">'
												.str_replace('KZtv - ','',$arrVideo['title']).'<br />
												Gepubliceerd op: '.$arrVideo['published_at'].'
											</span>
										</a>
									</li>';
			}
		}

		return $strReturnData;
	}

}