<?php

class KZ_Controller_Action_Helper_Responsive
{

	public $browser_detect;
	
	public function __construct()
	{
		require_once(APPLICATION_PATH . '/../library/Responsive/Mobile_Detect.php');
		$this->browser_detect 	= new Mobile_Detect();
	}
	
	public function isMobile()
	{
		if($this->browser_detect->isMobile() === true && $this->browser_detect->isTablet() === false) {
			return true;
		}
		return false;
	}
	
	public function isTablet()
	{
		return $this->browser_detect->isTablet();
	}
	
	public function isDesktop() {
		if($this->browser_detect->isMobile() === false && $this->browser_detect->isTablet() === false) {
			return true;
		}
		return false;
	}
	
}