<?php

class KZ_Models_Library extends KZ_Controller_Table
{

	public function getFolders($outerDir)
	{
		$arrExcludeDir			= array('.', '..', '.svn','mcith');
		$arrScannedDirs			= @scandir( $outerDir );
		if($arrScannedDirs !== false) {
			$arrReadDirs 		= array_diff( $arrScannedDirs, $arrExcludeDir);
			$arrDirectories		= array();
			foreach($arrReadDirs as $d) {
				if( is_dir($outerDir."/".$d)) {
					$arrDirectories[ $d ] = $this->getFolders( $outerDir."/".$d );
				}
			}

			return $arrDirectories;
		}
	}

	public function getFilesInFolder($strLocation, $arrFileTypes = array())
	{
		// Set the empty files array
		$arrFiles			= array();

		// Check if starting dir exists
		if(is_dir($strLocation)) {

			// Add /* for Glob
			$strLocation			.= '*';
			$arrLocationFiles		= glob($strLocation);
			if(!empty($arrLocationFiles)) {

				foreach($arrLocationFiles as $strFile)
				{
					// We dont' want the directories shown as files
					if(is_dir($strFile) === false) {

						// Get the Filename
						$arrFileName        = explode('/', $strFile);
						$strFileName	    = end($arrFileName);

						// Get the file extension
						$arrFileExtension   = explode('.', $strFileName);
						$strFileExtension	= end($arrFileExtension);

						if(in_array(strtolower($strFileExtension), $arrFileTypes)) {
							$arrFiles[]		= array('name'		=> $strFileName,
								'extension'	=> $strFileExtension);
						}
					}
				}
			}
		}

		return $arrFiles;
	}

	public function removeFolder($strFolderLocation)
	{
		if(is_file($strFolderLocation)){
			return unlink($strFolderLocation);
		} elseif(is_dir($strFolderLocation)) {
			$scan = glob(rtrim($strFolderLocation,'/').'/*');
			foreach($scan as $index=>$path){
				self::removeFolder($path);
			}
			return rmdir($strFolderLocation);
		}
	}

}