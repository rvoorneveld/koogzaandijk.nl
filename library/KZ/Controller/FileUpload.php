<?php
class KZ_Controller_FileUpload extends KZ_Controller_Action
{

	public static function doFileUpload($sesFileData, $arrAllowedFileTypes = array(), $booKeepFilename = true, $booResizeImage = false)
	{
		// Get the Starting Location and Selected folder
		if(is_object($sesFileData) && isset($sesFileData->startlocation)) {
			$strStartLocation			= $sesFileData->startlocation;
			$strCurrentFolder			= ((isset($sesFileData->folder) && $sesFileData->folder != '') ? $sesFileData->folder : '');

			// Set the Upload Directory
			$strUploadDirectory			= $strStartLocation.'/'.$strCurrentFolder;
			if($strCurrentFolder != '') {
				$strUploadDirectory		= $strUploadDirectory.'/';
			}
		} else {
			$strUploadDirectory			= $sesFileData;
		}

		// Get Uploaded file
		$objUpload 				= new Zend_File_Transfer_Adapter_Http();

		// Set destination
		$objUpload->setDestination($strUploadDirectory);

		// Returns the sizes from all files as array if more than one file was uploaded
		$objFile 				= $objUpload->getFileInfo();

		$strImgName				= $objFile['file']['name'];

		// Get file extention
		$strFileExtention 		= strtolower(end(explode('.', $objFile['file']['name'])));

		// Do the Upload if there are no file restrictions or fileextension is in allowed files
		if(empty($arrAllowedFileTypes) || in_array($strFileExtention, $arrAllowedFileTypes)) {

			// Set  Orginal Name as Default Hash
			$strMD5HashLocation     = $strImgName;

			// Check if we need to rename the filename
			if($booKeepFilename === false) {

				// Set file name
				$strFileName 			= str_replace('.'.$strFileExtention, '', $strImgName);

				// Create md5 hash for filename
				$strMD5Hash 			= md5(time().$strFileName);

				// Add file extention to md5 hash
				$strMD5HashLocation		= $strMD5Hash.'.jpg';

				// Set the New filename
				$objUpload->addFilter('Rename', array(	'target' => $strUploadDirectory.$strMD5HashLocation,
					'overwrite' => false));
			}

			// Check if we need to Resize the image
			if($booResizeImage === true) {

				// Get the Uploaded file Dimensions
				$arrImageDimensions		= getimagesize($objFile['file']['tmp_name']);
				$intFileWidth			= $arrImageDimensions[0];
				$intFileHeight			= $arrImageDimensions[1];

				// 210 breed, 280 hoog
				if($intFileWidth < 210 || $intFileHeight < 280) {
					return 'error';
				}

				// Check for landscape or portrait
				if($intFileWidth > $intFileHeight) {
					$strImageStyle		= 'landscape';
				} elseif($intFileWidth < $intFileHeight) {
					$strImageStyle		= 'portrait';
				} else {
					$strImageStyle		= 'square';
				}

				// Do the Resize if needed
				switch($strImageStyle) {
					case 'square':
						// Set the max Image dimensions
						$intMaxWidth		= '1600';
						$intMaxHeight		= $intMaxWidth;
						if($intFileWidth > $intMaxWidth) {
							// Set the Resize
							$objUpload->addFilter('Resize', array(	'width' 		=> $intMaxWidth,
								'height'		=> $intMaxHeight,
								'keepRatio'		=> false));
						}
						break;

					case 'landscape':
						// Set the max Image dimensions
						$intMaxWidth		= '1600';
						$intMaxHeight		= (($intMaxWidth / 4) * 3);
						if($intFileWidth > $intMaxWidth) {
							// Set the Resize
							$objUpload->addFilter('Resize', array(	'width' 		=> $intMaxWidth,
								'height'		=> $intMaxHeight,
								'keepRatio'		=> false));
						}
						break;

					case 'portrait':
						// Set the max Image dimensions
						$intMaxWidth		= '1200';
						$intMaxHeight		= (($intMaxWidth / 3) * 4);
						if($intFileWidth > $intMaxWidth) {
							$objUpload->addFilter('Resize', array('width' 		=> $intMaxWidth,
								'height'		=> $intMaxHeight,
								'keepRatio'	=> false));
						}

						break;
				}
			}

			// Do the Upload
			if($strFileExtention == 'xls') {
				if($objUpload->isValid()) {
					try {
						// upload received file(s)
						$objUpload->receive();
					} catch (Zend_File_Transfer_Exception $e) {
						$e->getMessage();
					}
				}
			} else {
				switch($strFileExtention) {
					case 'jpg':
						$src = imagecreatefromjpeg($objFile['file']['tmp_name']);
						imagejpeg($src,$strUploadDirectory.$strMD5HashLocation);
						break;
					case 'jpeg':
						$src = imagecreatefromjpeg($objFile['file']['tmp_name']);
						imagejpeg($src,$strUploadDirectory.$strMD5HashLocation);
						break;
					case 'gif':
						$src = imagecreatefromgif($objFile['file']['tmp_name']);
						imagejpeg($src,$strUploadDirectory.$strMD5HashLocation);
						break;
					case 'png':
						$imgPng     = imagecreatefrompng($objFile['file']['tmp_name']);
						imagealphablending($imgPng, true);
						imagesavealpha($imgPng, true);

						// Save the new PNG
						imagepng($imgPng, $strUploadDirectory.$strMD5HashLocation);
						break;
				}
			}
		}

		// Add a return message
		return json_encode(array('filename' 		=> $strImgName,
			'image_name'		=> $strMD5HashLocation,
			'image_location'	=> $objUpload->getFileName(),
			'dimensions'		=> $arrImageDimensions));

	}

}