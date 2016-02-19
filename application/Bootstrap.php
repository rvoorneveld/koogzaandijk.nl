<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	private $objConfig;
	private $objDatabase;
	private $objRouter;
	
	protected function _initConfig()
	{
		// Get config
		$this->objConfig = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', APPLICATION_ENV);
	
		// Put config in registry
		Zend_Registry::set('Zend_Config', $this->objConfig);
	}

	protected function _initCache()
	{
		// Bootstrap Zend_Cachemanager before other resources to avoid caching to wrong paths
		$this->bootstrap('cachemanager');
	}

	protected function _initSessions()
	{
		ini_set('session.cache_expire', (60 * 8));
		ini_set('session.gc_maxlifetime', (3600 * 8));
		session_save_path(APPLICATION_PATH . '/../cache/sessions');
	}

	protected function _initDatabase()
	{
		// Setup database
	   	$this->objDatabase = Zend_Db::factory($this->objConfig->database);
		Zend_Db_Table::setDefaultAdapter($this->objDatabase);
	}
	
	protected function _initHelpers()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->initView();
		$viewRenderer->view->addHelperPath('KZ/View/Helper/','KZ_View_Helper');
	}

	protected function _initLog()
	{
		if($this->hasPluginResource('Log')) {
			$objLogResource = $this->getPluginResource('Log');
			$objLog 		= $objLogResource->getLog();
			
			Zend_Registry::set('Log', $objLog);
		}
		
	}
	
	protected function _initRoutes()
	{
		// Get config
		$objConfigRoutes	= new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
		
		// Set frontcontroller
		$this->bootstrap('frontController');
		
		// Set router
		$this->objRouter = $this->frontController->getRouter();
		$this->objRouter->addConfig($objConfigRoutes,'routes');
		
		// Put routes in registry
		Zend_Registry::set('Zend_Routes', $this->objRouter);
	}
	
	protected function _initLocale()
	{
		date_default_timezone_set('Europe/Amsterdam');
				
		// Set locale
		$objLocale		= new Zend_Locale();
		$objLocale->setLocale($this->objConfig->admin->language);
		
		// Set language
		$strLanguage 	= $this->objConfig->admin->language;
		
		// Set Registry
		Zend_Registry::set('Zend_Locale', $objLocale);
		Zend_Registry::set('Language', $strLanguage);
	
	}
	
	protected function _initTranslate()
	{
		// Get Language
		$strLanguage 			= Zend_Registry::get('Language');
		
		// Set Models
		$objModelTranslations	= new KZ_Models_Translations();

		$fncGetTranslationArray = function() use($objModelTranslations,$strLanguage) {
			return $objModelTranslations->getTranslation($strLanguage);
		};

		if (($objCache = Zend_Translate::getCache()) instanceof Zend_Cache_Core) {
			$strKey = $objModelTranslations::getCacheKey($strLanguage);
			if (($arrAvailableTranslations = $objCache->load($strKey)) === false) {
				$objCache->save($arrAvailableTranslations = $fncGetTranslationArray(),$strKey);
			}
		} else {
			$arrAvailableTranslations = $fncGetTranslationArray();
		}
		
		// Translate
		$objTranslate	= new Zend_Translate(array(
			'adapter'	=> 'array',
			'content'	=> array_merge(array('default' => 'default'), $arrAvailableTranslations),
			'locale' 	=> $strLanguage
		));
		
		// Place Zend_Translation in registry
		Zend_Registry::set('Zend_Translate', $objTranslate);	
		Zend_Registry::set('KZ_Translate', $arrAvailableTranslations);
	}
	
	protected function _initCaching() {}
	
}