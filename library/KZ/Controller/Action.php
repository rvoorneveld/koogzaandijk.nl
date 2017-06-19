<?php

class KZ_Controller_Action extends Zend_Controller_Action
{

    const STATE_ACTIVE = 20;
    const STATE_INACTIVE = 10;

	private $objConfig;
	private $strModuleName;
	private $strControllerName;
	private $strActionName;
	private $strApplicationEnv;
	 
	public function preDispatch()
	{
		// Get config
		$this->objConfig 			= Zend_Registry::get('Zend_Config');
		
		// Load request params
		$this->strModuleName		= $this->getRequest()->getModuleName();
		$this->strControllerName	= $this->getRequest()->getControllerName();
		$this->strActionName		= $this->getRequest()->getActionName();
		
		// Get Application Environment
		$this->strApplicationEnv	= APPLICATION_ENV;
		
		// Set Module
		$objModule			= $this->_setModule();
		
		// Set Environment
		$objEnvironment 	= $this->_setEnvironment();
		
		// Get Namespace
		$objNamespace		= $this->_setSessionNamespace();
		
		// Set Google Analytics
		$objAnalytics		= $this->_setGoogleAnalytics();
		
		// Set Meta
		$objMeta			= $this->_setMeta();
		
		// Set Defaults		
		$objDefaults		= $this->_setDefaults();
    	
	}
	
	protected function _setModule()
	{
		// Set Responsive Object
		$objResponsive	 	= new KZ_Controller_Action_Helper_Responsive();
		
		// Check for mobile device
		$booMobile			= $objResponsive->isMobile();

		// Check if Module is Default and Device is Mobile
		if($this->strModuleName == 'default' && $booMobile === true) {
			// Set Module name Mobile
			$this->strModuleName	= 'mobile';
		}
		
  		//$this->strModuleName	= 'mobile';
		
		// Set layout options
		$options = array(
			'layout'     => $this->strModuleName,
			'layoutPath' => APPLICATION_PATH . '/modules/'.$this->strModuleName.'/views/layouts',
		);
		
		// Start layout
		$layout 			= Zend_Layout::startMvc($options);
	}
	
	protected function _setEnvironment()
	{

		// Check development
		if($this->strApplicationEnv == 'development') {

            $options = array(
                'auth'     => 'CRAMMD5',
                'username' => 'e86e627d406489',
                'password' => '98e61393e25a07',
                'port' => 2525
            );

            $mailTransport = new Zend_Mail_Transport_Smtp('smtp.mailtrap.io', $options);
            Zend_Mail::setDefaultTransport($mailTransport);

		}
	
	}
	
	protected function _setSessionNamespace()
	{
		
		if($this->strModuleName == 'admin') {
			
			// Get Session
			$objNamespace = new Zend_Session_Namespace('KZ_Admin');
			
			// Check if user is logged in
			if(! isset($objNamespace->user) && $this->strControllerName !== 'login') {
				// Redirect to login page
				$this->_redirect('/admin/login');
				exit;
			} else {
				
				// Get User Session
				$arrUser 					= $objNamespace->user;
				
				// Parse user data to view
				$this->view->login_name		= $arrUser['name'];
				$this->view->login_email	= $arrUser['email'];
				$this->view->login_group	= $arrUser['groupName'];
				$this->view->login_groupID	= $arrUser['user_group_id'];
			}
			
			return false;
		}
		
		return false; 
	}
	
	protected function _setGoogleAnalytics()
	{
		$objGoogleAnalytics = '';
		
		// Set Google Analytics for production website on default module
		if(APPLICATION_ENV == 'production' && $this->strModuleName == 'default') {
			
			// Get Google Analytics Key
			$strGoogleAnalyticsKey 			= $this->objConfig->default->application->analytics;
			
			// Set View
			$objView 						= new Zend_View();
			$objView->setBasePath(APPLICATION_PATH.'/modules/default/views/');
	   		$objView->addHelperPath('KZ/View/Helper/', 'KZ_View_Helper');
	   		
	   		// Parse Google Analytics partial to view
			$objGoogleAnalytics 			= $objView->partial('snippets/googleanalytics.phtml', array(
				'key'	=> $strGoogleAnalyticsKey
			));
		}
		
		$this->view->googleanalytics 		= $objGoogleAnalytics;
	}
	
	protected function _setMeta()
	{
		if(
				$this->strModuleName == 'admin'
			||	($this->strModuleName == 'default' && $this->strControllerName == 'preview')
		) {
			
			$this->view->headMeta()->setName('Robots', 'noindex,nofollow');
			$this->view->headMeta()->setName('Googlebot', 'noindex,nofollow');

		} else {
			
			$this->view->headMeta()->setName('Robots', 'index,follow');
			$this->view->headMeta()->setName('Googlebot', 'index,follow');
			
		}

	}
	
	protected function _setDefaults()
	{
		// Set default Mail settings
		Zend_Mail::setDefaultFrom($this->objConfig->default->application->email,$this->objConfig->default->application->name);
		Zend_Mail::setDefaultReplyTo($this->objConfig->default->application->email,$this->objConfig->default->application->name);
		
		// Check for browser feedback
		$arrParams = $this->getRequest()->getParams();
    	if(isset($arrParams['feedback'])) {
    		$this->view->feedback = unserialize(base64_decode($arrParams['feedback']));
    	}
    	
    	// Set Models
		$objModelUsers		= new KZ_Models_Users();
		
		// Get All Users
		$this->arrUsers		= $objModelUsers->getAllUsers('user_id');
		
		// Search Engine Optimalisation
		$this->view->seo_title			= $this->objConfig->seo->title;
		$this->view->seo_description	= $this->objConfig->seo->description;
		$this->view->seo_keywords		= $this->objConfig->seo->keywords;
		
    	// Parse some basic variables to view
		$this->view->root_url 			= ROOT_URL;
		$this->view->applicationName	= $this->objConfig->default->application->name;
		$this->view->applicationEmail	= $this->objConfig->default->application->email;
		$this->view->version 			= $this->objConfig->default->application->version;
		$this->view->users				= $this->arrUsers;

	}

}