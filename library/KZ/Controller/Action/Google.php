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

    public function getVideos(): array
    {
        $date = new Zend_Date();
        $arrVideos = [];
        $objChannels = ($objYouTube = new Google_Service_YouTube($this->objClient))->channels->listChannels('contentDetails', [
            'mine' => true,
        ]);

        if (false === empty($objChannels) && true === is_object($objChannels)) {
            foreach ($objChannels->items as $objChannel) {
                $objPlaylistItems = $objYouTube->playlistItems->listPlaylistItems('snippet', [
                    'playlistId' => $objChannel->contentDetails->relatedPlaylists->uploads,
                    'maxResults' => 20,
                ]);

                if (null !== $objPlaylistItems && true === is_object($objPlaylistItems)) {
                    foreach ($objPlaylistItems as $objPlaylistItem) {
                        $arrVideos[] = [
                            'title' => $objPlaylistItem->snippet->title,
                            'video_id' => $objPlaylistItem->snippet['resourceId']->videoId,
                            'thumbnail' => $objPlaylistItem->snippet['thumbnails']['default']['url'],
                            'published_at' => $date->setDate($objPlaylistItem->snippet->publishedAt)->toString('dd-MM-yyyy HH:mm:ss'),
                        ];
                    }
                }
            }
        }
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
