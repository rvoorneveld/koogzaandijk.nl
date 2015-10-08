<?php
class KZ_Controller_Logger
{

	/**
	 * 
	 * @var Zend_Log
	 */
	protected 	$logger;
	
	/**
	 * 
	 * @var KZ_Controller_Logger
	 */
	static 		$fileLogger = null;
	
	protected function __construct()
	{
		// Get Log from Registry
		$this->logger = Zend_Registry::get('Log');
	}
	
	public static function getInstance()
	{
		
		if(self::$fileLogger === null) {
			self::$fileLogger = new self();
		}
		
		return self::$fileLogger;
			
	}
	
	public function getLog()
	{
		return $this->logger;	
	}
	
	public static function info($message)
	{
		self::getInstance()->getLog()->info($message);	
	}
	
}