<?php

// Require Google Library
require_once 'Google/autoload.php';

class KZ_Controller_Action_Google {

	public $objClient;

	public function __construct() {
		$this->objClient = new Google_Client();
	}

	public function createSession($strClientId,$strClientSecret,$strApiKey,$strRedirectUrl) {

		$this->objClient = new Google_Client();
		$this->objClient->setClientId($strClientId);
		$this->objClient->setClientSecret($strClientSecret);
		$this->objClient->setRedirectUri($strRedirectUrl);
		$this->objClient->setDeveloperKey($strApiKey);
		$this->objClient->setScopes(array('https://gdata.youtube.com'));
		$this->objClient->setAccessType('offline');

		return $this->objClient;
	}

	public function getAuthUrl()
	{
		return $this->objClient->createAuthUrl();
	}

	public function getVideos() {

		// Set Videos Array
		$arrVideos      = array();

		// Set Youtube Service
		$objYouTube     = new Google_Service_YouTube($this->objClient);

		// Set Params
		$arrParams      = array(
			'mine' => true
		);

		// Get Activities
		$objChannels    = $objYouTube->channels->listChannels('contentDetails',$arrParams);

		if(! empty($objChannels) && is_object($objChannels)) {

			foreach($objChannels->items as $objChannel) {

				// Set Playlist ID
				$strPlaylistID      = $objChannel->contentDetails->relatedPlaylists->uploads;

				// Get Videos for current Playlist
				$objPlaylistItems   = $objYouTube->playlistItems->listPlaylistItems('snippet', array(
					'playlistId' => $strPlaylistID,
					'maxResults' => 20
				));

				if(! empty($objPlaylistItems) && is_object($objPlaylistItems)) {

					foreach ($objPlaylistItems->items as $objPlaylistItem) {

						// Set Published Date Object
						$objPublishedDate = new Zend_Date($objPlaylistItem->snippet->publishedAt);

                        $arrVideos[] = [
                            'title' => $objPlaylistItem->snippet->title,
                            'video_id' => $objPlaylistItem->snippet['resourceId']->videoId,
                            'thumbnail' => $objPlaylistItem->snippet['thumbnails']['default']['url'],
                            'published_at' => $objPublishedDate->toString('dd-MM-yyyy HH:mm:ss'),
                        ];

					}

				}

			}

		}

		// Return the Videos
		return $arrVideos;

	}

	public function verify($strCode)
	{
		// Authenticate
		$this->objClient->authenticate($strCode);

		// Get Access Token Data
		$jsonAccessToken    = $this->objClient->getAccessToken();

		// Set Access Token
		if(! empty($jsonAccessToken)) {
			$this->objClient->setAccessToken($jsonAccessToken);
			self::_updateRefreshToken($jsonAccessToken);
		}
	}

	private function _updateRefreshToken($jsonAccessToken)
	{
		// Set Array from json Data
		$arrAccessData = json_decode($jsonAccessToken,true);
		if(isset($arrAccessData['refresh_token'])) {
			$objModelSettings = new KZ_Models_Settings();
			$objModelSettings->updateSettingsByKey('google_refreshtoken',array('value' => $arrAccessData['refresh_token']));
			return true;
		}
		return false;
	}

}