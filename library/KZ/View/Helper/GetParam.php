<?php

class KZ_View_Helper_GetParam extends Zend_View_Helper_Abstract 
{
	public function __construct() { }
	
	public function GetParam($strParam)
	{
		$objFrontController = Zend_Controller_Front::getInstance();
		return $objFrontController->getRequest()->getParam($strParam);
	}
}