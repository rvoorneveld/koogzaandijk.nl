<?php

class KZ_Application_Resource_Cachemanager extends Zend_Application_Resource_Cachemanager
{
	public function init()
	{
		$objCacheManager = $this->getCacheManager();
		$objCache = $objCacheManager->getCache('default');
		$objRegistry = Zend_Registry::getInstance();

		Zend_Locale::setCache($objCache);
		Zend_Translate::setCache($objCache);
		Zend_Db_Table::setDefaultMetadataCache($objCache);

		foreach (['Zend_Locale','ZendLocale','Zend_Translate'] as $strRegKey) {
			if ($objRegistry->isRegistered($strRegKey)) {
				$objRegistry->get($strRegKey)->setCache($objCache);
			}
		}

		return parent::init();
	}
}