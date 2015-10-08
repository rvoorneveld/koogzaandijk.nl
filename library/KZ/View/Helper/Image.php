<?php

class KZ_View_Helper_Image {
	
	public static function image($strLocation, $intDestinationImageWidth, $intDestinationImageHeight) {
		
		// Get Root Url
		$strRootUrl				= ROOT_URL;
		
		// Set source image link
		$strSourceLink 			= 'upload/'.$strLocation;
		
		if(strstr($strLocation,'/')) {
			$arrDestinationFolders	= explode('/', $strLocation);
			$intTotalFolders		= count($arrDestinationFolders) - 1;
			
			$strDestinationPath		= 'upload/'.$intDestinationImageWidth.'_'.$intDestinationImageHeight;
			$strDestinationLink		= $strDestinationPath;
			
			for($i = 0; $i <= $intTotalFolders; $i++) {
				if($i < $intTotalFolders) {
					$strDestinationPath .= '/'.$arrDestinationFolders[$i];
				}
				$strDestinationLink	.= '/'.$arrDestinationFolders[$i];
			}
		} else {
			// Set destination path
			$strDestinationPath		= 'upload/'.$intDestinationImageWidth.'_'.$intDestinationImageHeight;
			
			// Set destination image link
			$strDestinationLink		= 'upload/'.$intDestinationImageWidth.'_'.$intDestinationImageHeight.'/'.$strLocation;
		}
		// Check if destination image must be created
		if(file_exists($strDestinationLink) === false) {
			
			// Check if destination dir exists
			$booDestinationDir = is_dir($strDestinationPath);
			
			if($booDestinationDir === false) {
				// Create destination path dir
				mkdir($strDestinationPath, 0777);
			}
			
			// Get Source Image Dimensions
			list($intSourceImageWidth, $intSourceImageHeight) = getimagesize($strSourceLink);

			// Get Source Image Extention
			$strImageExtention = strtolower(pathinfo($strSourceLink, PATHINFO_EXTENSION));
			
			if($strImageExtention == 'gif') {
				
				$resImageSource 	= imagecreatefromgif($strSourceLink);
				$resImageResampled 	= imagecreatetruecolor($intDestinationImageWidth, $intDestinationImageHeight);
				imagecopyresampled($resImageResampled, $resImageSource, 0, 0, 0, 0, $intDestinationImageWidth, $intDestinationImageHeight, $intSourceImageWidth, $intSourceImageHeight);
				imagegif($resImageResampled, $strDestinationLink);
				
			} elseif($strImageExtention == 'png') {
				
				$resImageSource 	= imagecreatefrompng($strSourceLink);
				$resImageResampled 	= imagecreatetruecolor($intDestinationImageWidth, $intDestinationImageHeight);
				imagecopyresampled($resImageResampled, $resImageSource, 0, 0, 0, 0, $intDestinationImageWidth, $intDestinationImageHeight, $intSourceImageWidth, $intSourceImageHeight);
				imagepng($resImageResampled, $strDestinationLink);
				
			} else {
				
				$resImageSource 	= imagecreatefromjpeg($strSourceLink);
				$resImageResampled 	= imagecreatetruecolor($intDestinationImageWidth, $intDestinationImageHeight);
				imagecopyresampled($resImageResampled, $resImageSource, 0, 0, 0, 0, $intDestinationImageWidth, $intDestinationImageHeight, $intSourceImageWidth, $intSourceImageHeight);
				imagejpeg($resImageResampled, $strDestinationLink);
				
			}
			
			imagedestroy($resImageSource);
	        imagedestroy($resImageResampled);
		
		}

		return $strRootUrl.'/'.$strDestinationLink;
		
	}
	
}