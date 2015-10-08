<?php

class KZ_Controller_Action_Helper_Image
{
	/**
	 * Function for croppingor resizing a image
	 *
	 * @param string $strImageType
	 * @param array $arrNewDimensions
	 */
	public function resampleImage($strImage, $arrNewDimensions = array())
	{
		if(!empty($arrNewDimensions)) {

			list($oldWidth, $oldHeight, $strImageType)		= getimagesize($strImage);

			switch ($strImageType) {
				case IMAGETYPE_PNG:
					$strOldImage 	= imagecreatefrompng($strImage);
					break;
				case IMAGETYPE_JPEG:
					$strOldImage 	= imagecreatefromjpeg($strImage);
					break;
				case IMAGETYPE_GIF:
					$strOldImage 	= imagecreatefromgif($strImage);
					break;
			}

			// Set default width and height
			$intImageWidth              = (($oldWidth/300)*$arrNewDimensions['width']);
			$intImageHeight             = (($oldWidth/300)*$arrNewDimensions['height']);

			$intSourceX                 = (($oldWidth/300)*$arrNewDimensions['x']);
			$intSourceY                 = (($oldWidth/300)*$arrNewDimensions['y']);

			// Create new image with Crop Width and Height
			$strNewImage		        = imagecreatetruecolor(300, 300);

			imagealphablending($strNewImage, false);
			imagesavealpha($strNewImage, true);

			imagecopyresampled($strNewImage, $strOldImage, 0, 0, $intSourceX, $intSourceY, 300, 300, $intImageWidth, $intImageHeight);

			switch ($strImageType) {
				case IMAGETYPE_PNG:
					imagepng($strNewImage, $strImage);
					break;
				case IMAGETYPE_JPEG:
					imagejpeg($strNewImage, $strImage);
					break;
				case IMAGETYPE_GIF:
					imagegif($strNewImage, $strImage);
					break;
			}

			// all done, so return true
			return true;
		}
	}
}