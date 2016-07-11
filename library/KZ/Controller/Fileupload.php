<?php
class KZ_Controller_Fileupload
{
    /**
     * Function for Handeling all File Uploads
     *
     * @param $sesFileData
     * @param array $arrAllowedFileTypes
     * @param bool $booKeepFilename
     * @param bool $booResizeImage
     * @param bool $booCreateThumbnail
     * @return string
     */
    public function doFileUpload($sesFileData, $arrAllowedFileTypes = array(), $booKeepFilename = true, $booResizeImage = false, $booCreateThumbnail = false)
    {
        // Get the Starting Location and Selected folder
        if(is_object($sesFileData) && isset($sesFileData->startlocation)) {
            $strStartLocation			= $sesFileData->startlocation;
            $strCurrentFolder			= ((isset($sesFileData->folder) && $sesFileData->folder != '') ? $sesFileData->folder : '');

            // Set the Upload Directory
            $strUploadDirectory = $strStartLocation.'/'.$strCurrentFolder;
            if (!is_dir($strUploadDirectory) && is_dir($_SERVER['DOCUMENT_ROOT']).$strUploadDirectory) {
                $strUploadDirectory = $_SERVER['DOCUMENT_ROOT'].$strUploadDirectory;
            }
            if($strCurrentFolder != '') {
                $strUploadDirectory		= $strUploadDirectory.'/';
            }
        } else {
            $strUploadDirectory			= $_SERVER['DOCUMENT_ROOT'].$sesFileData;
        }
        $strUploadDirectory = realpath($strUploadDirectory).'/';

        // Get Uploaded file
        $objUpload 				= new Zend_File_Transfer_Adapter_Http();

        // Set destination
        $objUpload->setDestination($strUploadDirectory);

        // Returns the sizes from all files as array if more than one file was uploaded
        $objFile 				= $objUpload->getFileInfo();
        $strImgName				= $objFile['file']['name'];

        // Find and replace all unwanted chars from the filename
        $objFormatHelper        = new KZ_View_Helper_Format();
        $strCleanFileName       = $objFormatHelper->format($strImgName, null, 'folder');

        // Set the New Clean filename
        $objUpload->addFilter(
            'Rename',
            array(
                'target'    => $strUploadDirectory.$strCleanFileName,
                'overwrite' => false
            )
        );

        // Set Default values
        $arrImageDimensions     = array();
        $strMD5HashLocation     = $strCleanFileName;

        // Get file extention
        $arrUploadFileData      = explode('.', $objFile['file']['name']);
        $strFileExtention 		= end($arrUploadFileData);
        $strFileExtention       = strtolower($strFileExtention);

        // Check for Video Upload, if so no thumb and resizing needed
        if($strFileExtention == 'mp4') {
            $booResizeImage     = false;
            $booCreateThumbnail = false;
        }

        // Do the Upload if there are no file restrictions or fileextension is in allowed files
        if(empty($arrAllowedFileTypes) || in_array($strFileExtention, $arrAllowedFileTypes)) {
            $arrFullMimetypeList = $this->_getMimetypeList();
            $arrValidMimetypeList = [];
            foreach ($arrAllowedFileTypes as $strExtension) {
                if (!isset($arrFullMimetypeList[$strExtension])) {
                    continue;
                }
                $arrValidMimetypeList[$strExtension] = $arrFullMimetypeList[$strExtension];
            }

            if (!empty($arrValidMimetypeList)) {
                $objUpload->addValidator(new Zend_Validate_File_MimeType($arrValidMimetypeList));
            }

            // Check if we need to rename the filename
            if($booKeepFilename === false) {

                // Check if file with the same name already exists in upload destination
                if(file_exists($strUploadDirectory.$strCleanFileName)) {

                    // Set file name
                    $strFileName 	    = str_replace('.'.$strFileExtention, '', $strCleanFileName);

                    // Check for multiple version of the same filename
                    $arrFilesSameName   = $this->checkFilenameInDir($strFileName, $strFileExtention, $strUploadDirectory);
                    if(!empty($arrFilesSameName)) {
                        // Only 1 file with the Same name, so add -2 to the Filename
                        if(count($arrFilesSameName) == 1) {
                            $strCleanFileName     = $strFileName.'-2.'.$strFileExtention;
                        } else {
                            $intFileNumber  = 1;
                            foreach($arrFilesSameName as $intKey => $strCurrentFileName) {
                                // Set file name
                                $strSameFileName 	= str_replace('.'.$strFileExtention, '', $strCurrentFileName);
                                $arrFileValues      = explode('-', $strSameFileName);
                                if(!empty($arrFileValues) && count($arrFileValues) > 1) {
                                    $intCurrentFileNumber       = end($arrFileValues);
                                    if($intCurrentFileNumber > $intFileNumber) {
                                        $intFileNumber      = $intCurrentFileNumber;
                                    }
                                }
                            }

                            // Set the new filename
                            $strCleanFileName     = $strFileName.'-'.($intFileNumber + 1).'.'.$strFileExtention;
                        }
                    }

                }

                // Set the New filename
                $objUpload->addFilter(
                    'Rename',
                    array(
                        'target'    => $strUploadDirectory.$strCleanFileName,
                        'overwrite' => false
                    )
                );
            } else {

                // Check if file with the same name already exists in upload destination
                if(file_exists($strUploadDirectory.$strCleanFileName)) {
                    unlink($strUploadDirectory.$strCleanFileName);
                }

            }

            // Check if we need to Resize the image
            if($booResizeImage === true) {

                // Get the Uploaded file Dimensions
                $arrImageDimensions		= getimagesize($objFile['file']['tmp_name']);
                $intFileWidth			= $arrImageDimensions[0];
                $intFileHeight			= $arrImageDimensions[1];

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
                            $objUpload->addFilter(new KZ_Filter_File_Resize([
                                'width' 		=> $intMaxWidth,
                                'height'		=> $intMaxHeight,
                                'keepRatio'		=> false,
                            ]));
                        }
                        break;

                    case 'landscape':
                        // Set the max Image dimensions
                        $intMaxWidth		= '1600';
                        $intMaxHeight		= (($intMaxWidth / 4) * 3);
                        if($intFileWidth > $intMaxWidth) {
                            // Set the Resize
                            $objUpload->addFilter(new KZ_Filter_File_Resize([
                                'width' 		=> $intMaxWidth,
                                'height'		=> $intMaxHeight,
                                'keepRatio'		=> true
                            ]));
                        }
                        break;

                    case 'portrait':
                        // Set the max Image dimensions
                        $intMaxWidth		= '1200';
                        $intMaxHeight		= (($intMaxWidth / 3) * 4);
                        if($intFileWidth > $intMaxWidth) {
                            $objUpload->addFilter(new KZ_Filter_File_Resize([
                                'width' 		=> $intMaxWidth,
                                'height'		=> $intMaxHeight,
                                'keepRatio'		=> true
                            ]));
                        }

                        break;
                }
            }

            // Do the Upload
            if($objUpload->isValid()) {
                try {
                    // upload received file(s)
                    $objUpload->receive();

                    if($booCreateThumbnail === true) {

                        // Get the Config
                        $objConfig					= Zend_Registry::get('Zend_Config');

                        // Set the Admin Thumbnail upload Folder
                        $strThumbUploadDirectory	= str_replace('/upload/', '/thumbs/', $strUploadDirectory);

                        // Check if image thumb upload dir exists
                        if(!is_dir($strThumbUploadDirectory)) {
                            mkdir($strThumbUploadDirectory,0755,true);
                        }

                        // Returns the sizes from all files as array if more than one file was uploaded
                        $objThumbnailFile 		= $objUpload->getFileInfo();

                        // Get file extention
                        // Get file extention
                        $arrUploadFileData      = explode('.', $objThumbnailFile['file']['name']);
                        $strFileExtention 		= end($arrUploadFileData);
                        $strFileExtention       = strtolower($strFileExtention);

                        // Do the Upload if there are no file restrictions or fileextension is in allowed files
                        if(empty($arrAllowedFileTypes) || in_array($strFileExtention, $arrAllowedFileTypes)) {

                            // Set the Filename
                            $strTempFilename		= $objThumbnailFile['file']['tmp_name'];
                            $strFilename			= $strThumbUploadDirectory.'/'.$strCleanFileName;

                            // Create the Thumbnail
                            self::createThumbnail($strTempFilename, $strFilename);
                        }
                    }
                } catch (Zend_File_Transfer_Exception $e) {
                    $e->getMessage();
                }
            }
        }

        // Add a return message
        return json_encode(
            array(
                'filename' 		    => $strCleanFileName,
                'image_name'		=> $strMD5HashLocation,
                'image_location'    => $objUpload->getFileName(),
                'dimensions'		=> $arrImageDimensions
            )
        );

    }

    /**
     * Function for creating the Image Thumbnails
     * @param $strTempFilename
     * @param $strFilename
     */
    public function createThumbnail($strTempFilename, $strFilename, $arrMaxDimensions = array())
    {
        // Get the Config
        $objConfig			= Zend_Registry::get('Zend_Config');

        // Get the Max Thumb width and height from config
        if(empty($arrMaxDimensions)) {
            $booRemoveCurrent   = true;
            $booKeepRatio       = true;
            $intMaxWidth	    = $objConfig->thumbs->width;
            $intMaxHeight	    = $objConfig->thumbs->height;
        } else {
            $booRemoveCurrent   = (isset($arrMaxDimensions['remove']) ? $arrMaxDimensions['remove'] : true);
            $booKeepRatio       = (isset($arrMaxDimensions['keepratio']) ? $arrMaxDimensions['keepratio'] : true);
            $intMaxWidth	    = $arrMaxDimensions['width'];
            $intMaxHeight	    = $arrMaxDimensions['height'];
        }

        if(isset($arrMaxDimensions['preserve_dimensions']) && $arrMaxDimensions['preserve_dimensions'] === true) {
            list($intMaxWidth, $intMaxHeight)       = getimagesize($_SERVER['DOCUMENT_ROOT'].$strTempFilename);
        }


        // Set the Resize Model
        $objFileResize			= new KZ_Filter_File_Resize_Adapter_Gd();

        // Check if current file already exists as filename.
        // If so remove current to prevent errors
        if(file_exists($strFilename) && $booRemoveCurrent === true) {
            unlink($strFilename);
        }

        // Do the Resize and upload the Thumbnail
        $objFileResize->resize($intMaxWidth, $intMaxHeight, $booKeepRatio, $strTempFilename, $strFilename);

        return true;
    }

    /**
     * Function for checking is a filename exists in a folder
     *
     * @param $strFilename
     * @param $strFileExtension
     * @param $arrDestinationDir
     * @return array
     */
    public function checkFilenameInDir($strFilename, $strFileExtension, $arrDestinationDir)
    {
        // create an array to hold directory list
        $arrResults     = array();

        // create a handler for the directory
        $objHandler     = opendir($arrDestinationDir);

        // open directory and walk through the filenames
        while ($strFile = readdir($objHandler)) {

            // if file isn't this directory or its parent, add it to the results
            if(is_dir($arrDestinationDir.$strFile) === false) {

                if ($strFile != "." && $strFile != "..") {

                    preg_match("/^({$strFilename}.*.".$strFileExtension.")/i" , $strFile, $arrFiles);
                    if(isset($arrFiles[0]) && $arrFiles[0] != '') {
                        $arrResults[]       = $arrFiles[0];
                    }
                }
            }
        }

        return $arrResults;
    }

    protected function _getMimetypeList()
    {
        $objCache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->cachemanager->getCache('default');
        $strCacheNs = hash('md5',serialize([__METHOD__,func_get_args()]));

        if (($arrList = $objCache->load($strCacheNs)) === false) {
            $strSourceUrl = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
            $arrList = [];
            foreach (file($strSourceUrl) as $strLine) {
                switch (true) {
                    case !isset($strLine[0]):
                    case $strLine[0] === '#':
                    case !preg_match_all('#([^\s]+)#',$strLine,$arrOut):
                    case !isset($arrOut[1]):
                    case ($intCount = count($arrOut[1])) <= 1:
                        continue;
                    default:
                        for($intInc = 1; $intInc < $intCount; $intInc++) {
                            $arrList[$arrOut[1][$intInc]] = $arrOut[1][0];
                        }
                        break;
                }
                unset($intInc,$intCount,$arrOut);
            }
            $objCache->save($arrList,$strCacheNs,[],86400*7);
        }
        return $arrList;
    }

}