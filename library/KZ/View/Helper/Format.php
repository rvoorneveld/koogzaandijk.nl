<?php

class KZ_View_Helper_Format extends Zend_View_Helper_Abstract
{

    const STATUS_INACTIVE = [0,10];

    public function status($intStatus)
    {
        switch(true) {
            case in_array($intStatus,self::STATUS_INACTIVE) :
                $intColor = 'red';
                $strText = 'Inactive';
                break;
            default:
                $intColor = 'green';
                $strText = 'Active';
                break;
        }
        return sprintf('<span class="tag %s">%s</span>',$intColor,$strText);
    }

	/**
	 * Function for getting and setting the correct image url in content
	 *
	 * @param $strSearchPattern
	 * @param $strInputData
	 * @param $booReverseReplace, if set to true add domainurl in order to show the content image correctly
	 * @return mixed
	 */
	public function setImagePath($strSearchPattern, $strInputData, $booReverseReplace = false)
	{
		// Check for empty
		if(empty($strInputData) || is_null($strInputData) || $strInputData == '') {
			return $strInputData;
		}

		// match all img tag source
		preg_match_all($strSearchPattern, stripslashes($strInputData), $arrImageResults);

		// Set Empty
		$arrCleanImages     = array();

		// Check for results
		if(!empty($arrImageResults)) {
			foreach($arrImageResults as $intKey => $arrImageValues) {
				if(is_array($arrImageValues)) {
					foreach($arrImageValues as $intKey => $strImageData) {
						if(strstr($strImageData, 'src=') === false) {
							$arrCleanImages[]   = $arrImageValues[$intKey];
						}
					}
				}
			}
		}

		// Get the Current Language
		$sesCurrentUser		= new Zend_Session_Namespace('Websitebuilder_Admin');
		$arrDomainModule    = (!empty($sesCurrentUser->user) && isset($sesCurrentUser->user['domainModule']) ? explode('-', $sesCurrentUser->user['domainModule']) : array());

		// Set the DomainName
		$strDomainName      = '';
		if(!empty($arrDomainModule)) {
			$strDomainName  = $arrDomainModule[0];
		}

		// Set the Exclude Array
		$arrExcludeSource   = array('', 'http:', 'www', 'preview');

		// By Default add Domain Url to Link
		$booAddDomainUrl    = true;

		// Check the found images
		$strNewImageLocation  = '';
		if(!empty($arrCleanImages)) {
			foreach($arrCleanImages as $intImageKey => $strImageSource) {
				$strNewImageLocation    = '';
				$arrImageSource         = explode('/', $strImageSource);
				$strImageFile           = end($arrImageSource);

				// Add Image Filename to Exclude Array
				$arrExcludeSource[]     = $strImageFile;

				// Loop over the Image Values and check if the value is not in de exclude array
				// and the Domainname is not in string
				foreach($arrImageSource as $intKey => $strSourceValue) {
					if(!in_array($strSourceValue, $arrExcludeSource) && strstr($strSourceValue, $strDomainName) === false) {
						$strNewImageLocation      .= $strSourceValue.'/';
					}
				}

				// Check for www. in new link, if so do not add Domain Url
				if(stristr($strNewImageLocation, 'www.') !== false) {
					$booAddDomainUrl    = false;
				}

				// Set the New Image Src
				$strNewImageSource      = $strNewImageLocation.$strImageFile;

				// Check if string start with a '/'
				if(substr($strNewImageSource, 0, 1) != '/' && $booAddDomainUrl === true) {
					$strNewImageSource  = '/'.$strNewImageLocation.$strImageFile;
				}

				// If we need to do the Reverse, add the Domain Url to the Src
				if($booReverseReplace === true && $booAddDomainUrl === true) {
					$strNewImageSource  = $sesCurrentUser->user['domainUrl'].$strNewImageSource;
				} elseif ($booAddDomainUrl === false) {
					$strNewImageSource  = 'http://'.$strNewImageSource;
				}

				// Set the New Input
				$strInputData       = str_replace($strImageSource, $strNewImageSource, $strInputData);
			}
		}

		return $strInputData;
	}

    public static function date($value,$format) {
	    return self::format($value,$format,'date');
    }

	public static function format($mixInputValue, $strOutputFormat, $strInputType = 'date', $strOpacity = '')
	{
		// Set an empty Output string
		$strOutputValue     = '';

		// Check which type we need to Format and set the Output string
		switch($strInputType)
		{
			case 'color':
				// Check for a Values to Transform
				$strOutputValue     = self::_convertcolor($mixInputValue, $strOutputFormat, $strOpacity);
				break;

			case 'folder':
				if(empty($mixInputValue)) {
					return '-';
				}
				$strOutputValue     = self::_setname($mixInputValue);
				break;

			case 'user':
				if(is_null($mixInputValue)) {
					return '-';
				}

				$strOutputValue     = self::_user($mixInputValue);
				break;

			case 'slug':
				if(empty($mixInputValue)) {
					return '-';
				}
				$strOutputValue     = self::_slug($mixInputValue);
				break;

			case 'date':
				if(empty($mixInputValue)) {
					return '-';
				}
				$strOutputValue     = self::_date($mixInputValue, $strOutputFormat);
				break;

		}

		return $strOutputValue;
	}

	// Transform a Color to a Given Output
	private static function _convertcolor($mixInputValue, $strConvertTo, $strOpacity)
	{
		if($mixInputValue == '' || is_null($mixInputValue) || empty($mixInputValue)) {
			return $mixInputValue;
		}

		switch($strConvertTo)
		{
			case 'rgb':
				$strHexValue = str_replace("#", "", $mixInputValue);

				if(strlen($strHexValue) == 3) {
					$r = hexdec(substr($strHexValue,0,1).substr($strHexValue,0,1));
					$g = hexdec(substr($strHexValue,1,1).substr($strHexValue,1,1));
					$b = hexdec(substr($strHexValue,2,1).substr($strHexValue,2,1));
				} else {
					$r = hexdec(substr($strHexValue,0,2));
					$g = hexdec(substr($strHexValue,2,2));
					$b = hexdec(substr($strHexValue,4,2));
				}

				$rgb = array($r, $g, $b);

				$strOutput      = 'rgb('.implode(',', $rgb).')';
				break;

			case 'rgba':
				$strHexValue = str_replace("#", "", $mixInputValue);

				if(strlen($strHexValue) == 3) {
					$r = hexdec(substr($strHexValue,0,1).substr($strHexValue,0,1));
					$g = hexdec(substr($strHexValue,1,1).substr($strHexValue,1,1));
					$b = hexdec(substr($strHexValue,2,1).substr($strHexValue,2,1));
				} else {
					$r = hexdec(substr($strHexValue,0,2));
					$g = hexdec(substr($strHexValue,2,2));
					$b = hexdec(substr($strHexValue,4,2));
				}

				$rgb = array($r, $g, $b);

				$strOutput      = 'rgba('.implode(',', $rgb).', '.$strOpacity.')';
				break;

			case 'hex':
				if(empty($mixInputValue) || !is_array($mixInputValue) || count($mixInputValue) < 3) {
					return $mixInputValue;
				}

				$hex = "#";
				$hex .= str_pad(dechex($mixInputValue[0]), 2, "0", STR_PAD_LEFT);
				$hex .= str_pad(dechex($mixInputValue[1]), 2, "0", STR_PAD_LEFT);
				$hex .= str_pad(dechex($mixInputValue[2]), 2, "0", STR_PAD_LEFT);

				$strOutput      = $hex;
				break;
		}

		return $strOutput;
	}

	// Transform a Name to a Browser / DB valid name
	private static function _setname($strFoldername)
	{
		// UTF-8 encode the html entities
		$strFoldername      = html_entity_decode($strFoldername);

		// $arrSpecialChars and $arrReplaceChars MUST HAVE the same amount of items
		$arrSpecialChars	= array('Š', 'š', 'Ð', 'Ž', 'ž', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö',
			'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò',
			'ó', 'ô', 'õ', 'ö', 'ø', 'ñ', 'ù', 'ú', 'û', 'ü', 'ý', 'ý', 'þ', 'ÿ', 'ƒ', "'", "`","(",")");

		$arrReplaceChars 	= array('S', 's', 'Dj','Z', 'z', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O',
			'O', 'U', 'U', 'U', 'U', 'Y', 'B', 'Ss','a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o',
			'o', 'o', 'o', 'o', 'o', 'n', 'u', 'u', 'u', 'u', 'y', 'y', 'b', 'y', 'f', "", "", "", "");

		$strNewName				= trim(str_replace($arrSpecialChars, $arrReplaceChars, $strFoldername));
		$strNewName				= str_replace(" ", "-", strtolower($strNewName));

		return $strNewName;
	}

	// Transform a UserID to the Users Name
	private static function _user($intUserID)
	{
		// Set the Default Return Value
		$strOutputValue     = $intUserID;

		$objModelUsers      = new Admin_Models_User();
		$arrUser            = $objModelUsers->getUserByUserID((int)$intUserID);
		if (is_array($arrUser) && isset($arrUser['name'])) {
			$strOutputValue = $arrUser['name'];
		}

		/* $arrAllUsers        = $objModelUsers->getAllUsers();

		// Add system to the users as key 0
		$arrSystemUser      = array('name' => 'System');
		array_unshift($arrAllUsers, $arrSystemUser);
		if(isset($arrAllUsers[$intUserID]) && $arrAllUsers[$intUserID] != '') {
			$strOutputValue     = $arrAllUsers[$intUserID]['name'];
		} */

		if(strlen($strOutputValue) > 12) {
			$strOutputValue     = substr($strOutputValue, 0, 12).'...';
		}
		return $strOutputValue;
	}

	// Transform the Given Menu Url to a Friendly Url
	private static function _slug($strPhrase)
	{
		$strResult  = preg_replace('/&([a-z])[a-z]+;/i','$1',htmlentities($strPhrase));
		$strResult	= strtolower($strResult);
		$strResult 	= preg_replace('/[^a-z0-9\s-]/','',$strResult);
		$strResult 	= trim(preg_replace('/[\s-]+/',' ',$strResult));
		$strResult 	= trim(substr($strResult,0,500));
		$strResult 	= preg_replace('/\s/','-',$strResult);

		return $strResult;
	}

	// Transform a given date to a given Format
	// Remember using 'YYYY' gives date issues, use 'yyyy' instead
	private static function _date($strDate, $strDateFormat)
	{
		if(is_null($strDate) || $strDate == '0000-00-00 00:00:00') {
			return '-';
		}
		// Create a new Zend_Date object for the date input
		$objDate		= new Zend_Date(strtotime($strDate));

		return $objDate->toString($strDateFormat);
	}

}
