<?php
class KZ_Controller_Action_Helper_Slug
{

	public static function slug($strPhrase)
	{
		
		$strResult	= strtolower($strPhrase);
		$strResult 	= preg_replace('/[^a-z0-9\s-]/','',$strResult);
		$strResult 	= trim(preg_replace('/[\s-]+/',' ',$strResult));
		$strResult 	= trim(substr($strResult,0,500));
		$strResult 	= preg_replace('/\s/','-',$strResult);
		
		return $strResult;
		
	}
	
}