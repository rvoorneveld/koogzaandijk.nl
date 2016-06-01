<?php
class Admin_LibraryController extends KZ_Controller_Action {

	/**
	 * @var KZ_Controller_Fileupload
	 */
	private $objControllerUpload;

	/**
	 * @var KZ_Models_Galleries
	 */
	private $objModelGallery;

	/**
	 * @var KZ_Models_Library
	 */
	private $objModelLibrary;

	private $strUploadDir = '/upload';
	private $strThumbDir = '/thumbs';

	/**
	 * Private function for checking if image has a thumb, if not create it
	 *
	 * @param $arrDirectoryFiles
	 */
	private function _checkForThumbs($arrDirectoryFiles,$strFileLocation,$strThumbLocation,$strFolder = null)
	{
		if (!empty($arrDirectoryFiles) && is_array($arrDirectoryFiles)) {
			$strThumbLocation = SERVER_URL.$strThumbLocation;
			$strFileLocation = SERVER_URL.$strFileLocation;
			if (substr($strFileLocation,-1) != '/') {
				$strFileLocation .= '/';
			}
			if (substr($strThumbLocation,-1) != '/') {
				$strThumbLocation .= '/';
			}

			foreach ($arrDirectoryFiles as $intKey => $arrImageValues) {
				if (!is_null($strFolder)) {
					$strThumbFile = $strThumbLocation.$strFolder.'/'.$arrImageValues['name'];
					$strTempThumbFile = $strFileLocation.$strFolder.'/'.$arrImageValues['name'];
					if (!is_dir($strThumbLocation.$strFolder)) {
						mkdir($strThumbLocation.$strFolder);
					}
				} else {
					$strThumbFile = $strThumbLocation.$arrImageValues['name'];
					$strTempThumbFile = $strFileLocation.$arrImageValues['name'];
				}

				if (!file_exists($strThumbFile)) {
					$this->objControllerUpload->createThumbnail($strTempThumbFile,$strThumbFile);
				}
			}
		}
	}

	public function init()
	{
		// Set Ini Settings
		ini_set('max_execution_time', 	600);		// 10 Minutes execution time
		ini_set('memory_limit', 		'512M');

		$this->objControllerUpload = new KZ_Controller_Fileupload();
		$this->objModelGallery = new KZ_Models_Galleries();
		$this->objModelLibrary = new KZ_Models_Library();
	}

	/**
	 * Function for the Images
	 */
	public function imagesAction()
	{
		// Check for POST from Iframe Crop
		if ($this->_request->isPost()) {
			$arrPostParams = $this->_getAllParams();
			$booImageResampled = false;

			if (isset($arrPostParams['image']) && $arrPostParams['image'] != '') {
				$strImage = $_SERVER['DOCUMENT_ROOT'].$arrPostParams['image'];

				$arrNewDimensions = [
					'x' => $arrPostParams['x'],
					'y' => $arrPostParams['y'],
					'width' => $arrPostParams['w'],
					'height' => $arrPostParams['h'],
				];

				$booImageResampled = $this->objModelGallery->resampleImage($strImage,$arrNewDimensions);
			}

			if ($booImageResampled === true) {
				$strThumbFile = str_replace("{$this->strUploadDir}/","{$this->strThumbDir}/",$strImage);
				if (file_exists($strThumbFile)) {
					unlink($strThumbFile);
				}

				$this->objControllerUpload->createThumbnail($strImage,$strThumbFile);

				$strLocation = $this->view->url([
					'lang' => $arrPostParams['lang'],
					'controller' => $arrPostParams[$this->_request->getControllerKey()],
					'action' => $arrPostParams[$this->_request->getActionKey()],
				],false,true);

				$strFeedback = base64_encode(serialize(['type' => 'success','message' => 'Image is cropped succesfully']));
				$objFeedbackNamespace = new Zend_Session_Namespace('Feedback');
				$objFeedbackNamespace->feedback = $strFeedback;
				$this->_redirect($strLocation.'/');
			} else {
				$this->view->feedback = ['type' => 'error','message' => 'Something went wrong trying to crop the image. Please try again'];
			}
		}

		$booCreateFolders = true;
		$sesImageUpload = new Zend_Session_Namespace('Image_Upload');

		if (!is_dir(SERVER_URL.$this->strUploadDir)) {
			mkdir(SERVER_URL.$this->strUploadDir);
		}

		$strStartLocation = SERVER_URL.$this->strUploadDir;
		if (!is_dir($strStartLocation)) {
			mkdir($strStartLocation);
		}

		$sesImageUpload->startlocation = $this->strUploadDir;
		$strFileLocation = $this->strUploadDir;
		$strThumbLocation = $this->strThumbDir.'/';

		if ($this->_request->isXmlHttpRequest()) {
			$strFolder = str_replace('|','/',$this->_getParam('folder'));
			$sesImageUpload->folder = $strFolder;
			$arrDirectoryFiles = $this->objModelLibrary->getFilesInFolder($strStartLocation.'/'.$strFolder.'/',['jpg','gif','png','jpeg']);
			$this->_checkForThumbs($arrDirectoryFiles,$strFileLocation,$strThumbLocation,$strFolder);

			// Get the HTML
			$strFilesHtml = $this->view->partial('partials/folderimages.phtml',[
				'images' => $arrDirectoryFiles,
				'imageslocation' => $strFileLocation.'/'.$strFolder.'/',
				'thumblocation' => $strThumbLocation.$strFolder.'/',
			]);
			die($strFilesHtml);
		}

		$arrDirectories = $this->objModelLibrary->getFolders($strStartLocation.'/');

		$booImageUploadSession = Zend_Session::namespaceIsset('Image_Upload');
		if ($booImageUploadSession === true) {
			$sesImageUpload = new Zend_Session_Namespace('Image_Upload');
			$strStartLocation .= '/'.$sesImageUpload->folder;
			$strFileLocation .= '/'.$sesImageUpload->folder;
			$strThumbLocation .= $sesImageUpload->folder;
		}

		$arrDirectoryFiles = $this->objModelLibrary->getFilesInFolder($strStartLocation.'/',['jpg','gif','png','jpeg']);

		if (count(explode('/',$sesImageUpload->folder)) >= 3) {
			$booCreateFolders = false;
		}

		$this->_checkForThumbs($arrDirectoryFiles,$strFileLocation,$strThumbLocation);

		if (substr($strFileLocation,-1) != '/') {
			$strFileLocation .= '/';
		}

		if (substr($strThumbLocation,-1) != '/') {
			$strThumbLocation .= '/';
		}

		// Get PHP ini upload_max_filesize
		$intIniMaxUpload = ini_get('upload_max_filesize'); // File upload uses POST
		$intIniMaxPost = ini_get('post_max_size');

		// Send to view
		$this->view->assign([
			'maxupload' => str_replace('M','mb',$intIniMaxUpload),
			'maxpost' => str_replace('M','mb',$intIniMaxPost),
			'directories' => $arrDirectories,
			'images' => $arrDirectoryFiles,
			'imageslocation' => $strFileLocation,
			'thumblocation' => $strThumbLocation,
			'createfolder' => $booCreateFolders,
			'currentfolder' => $sesImageUpload->folder,
		]);
	}

	/**
	 * Function for the Image Upload
	 */
	public function imagesuploadAction()
	{
		// Disable the Layout and Rende file
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Check if we have the Session from the ImagesAction
		$booImageUploadSession = Zend_Session::namespaceIsset('Image_Upload');

		if ($booImageUploadSession === true) {
			// Get the Session
			$sesImageUpload = new Zend_Session_Namespace('Image_Upload');

			// Set the Allowed File types
			$arrAllowedFileTypes = ['jpg','gif','png','jpeg'];

			// Do the Upload
			$objControllerUpload = $this->objControllerUpload->doFileUpload($sesImageUpload,$arrAllowedFileTypes,false,true);

		}

	}

	/**
	 * Function for Files
	 */
	public function filesAction()
	{
		$sesFilesUpload = new Zend_Session_Namespace('Files_Upload');
		if (!is_dir(SERVER_URL.$this->strUploadDir)) {
			mkdir(SERVER_URL.$this->strUploadDir);
		}

		$strStartLocation = SERVER_URL.$this->strUploadDir.'/files';
		if (!is_dir($strStartLocation)) {
			mkdir($strStartLocation);
		}

		$sesFilesUpload->startlocation = $strStartLocation;
		$strFileLocation = $this->strUploadDir.'/files';
		$arrAllowedExtensions = ['doc','docx','pdf','xls','xlsx'];

		if ($this->_request->isXmlHttpRequest()) {
			$strFolder = str_replace('|','/',$this->_getParam('folder'));
			$sesFilesUpload->folder = $strFolder;
			$arrDirectoryFiles = $this->objModelLibrary->getFilesInFolder($strStartLocation.'/'.$strFolder.'/',$arrAllowedExtensions);
			$strFilesHtml = $this->view->partial('partials/folderfiles.phtml',[
				'files' => $arrDirectoryFiles,
				'filelocation' => $strFileLocation.'/'.$strFolder.'/',
			]);
			die($strFilesHtml);
		}

		$arrDirectories = $this->objModelLibrary->getFolders($strStartLocation.'/');
		$booFileUploadSession = Zend_Session::namespaceIsset('Files_Upload');
		if ($booFileUploadSession === true) {
			$sesFileUpload = new Zend_Session_Namespace('Files_Upload');

			// Override the Start Location and File Location for Image Thumbs
			$strStartLocation .= '/'.$sesFileUpload->folder;
			$strFileLocation .= '/'.$sesFileUpload->folder;
		}

		// Get the files in the Directory
		$arrDirectoryFiles = $this->objModelLibrary->getFilesInFolder($strStartLocation.'/',$arrAllowedExtensions);

		// Get PHP ini upload_max_filesize
		$intIniMaxUpload = ini_get('upload_max_filesize');
		$intIniMaxPost = ini_get('post_max_size');

		// Send to view
		$this->view->assign([
			'maxupload' => str_replace('M','mb',$intIniMaxUpload),
			'maxpost' => str_replace('M','mb',$intIniMaxPost),
			'directories' => $arrDirectories,
			'files' => $arrDirectoryFiles,
			'filelocation' => $strFileLocation,
			'currentfolder' => $sesFilesUpload->folder,
		]);
	}

	/**
	 * Function for the File Upload
	 */
	public function filesuploadAction()
	{
		// Disable the Layout and Rende file
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		// Check if we have the Session from the ImagesAction
		$booFilesUploadSession = Zend_Session::namespaceIsset('Files_Upload');
		if ($booFilesUploadSession === true) {

			// Get the Session
			$sesFilesUpload = new Zend_Session_Namespace('Files_Upload');

			// Set the Allowed File types
			$arrAllowedFileTypes = ['doc','docx','pdf','xls','xlsx'];

			// Do the Upload
			$objControllerUpload = $this->objControllerUpload->doFileUpload($sesFilesUpload,$arrAllowedFileTypes,false,false,false,false);

		}
	}

	public function galleriesAction()
	{
		// Set Models
		$objModelGalleries		= new KZ_Models_Galleries();

		// Set Default Variables
		$intStatus				= 0;
		$strName				= '';

		$arrGalleryTypes = $objModelGalleries->getGalleryTypes();

		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams		= $this->_getAllParams();

			$strName 			= $arrPostParams['name'];
			$intType 			= $arrPostParams['type_id'];
			$intStatus			= $arrPostParams['status'];
			$booWidhtHeight		= ((isset($arrPostParams['width_height'])) ? $arrPostParams['width_height'] : '');
			$intWidth			= ((isset($arrPostParams['width']))? $arrPostParams['width'] : '');
			$intHeight			= ((isset($arrPostParams['height']))? $arrPostParams['height'] : '');

			// Check type
			if(! empty($intType)) {
				if($intType == 'carousel') {

				}
			}

			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			}elseif(! empty($booWidhtHeight) && empty($intWidth)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in the width');
			}elseif(! empty($booWidhtHeight) && empty($intHeight)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in the height');
			} else {

				//array for database
				$arrAddGallery = array(
					'name'		=> $strName,
					'type_id'	=> $intType,
					'status'	=> $intStatus,
					'width'		=> $intWidth,
					'height'	=> $intHeight
				);

				if($arrAddGallery) {

					//Update hotel data
					$intUpdateID 	= $objModelGalleries->addGallery($arrAddGallery);

					if(isset($intUpdateID) && is_numeric($intUpdateID)) {
						$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully add Gallery')));
						$this->_redirect('/admin/library/galleriesimages/id/'.$intUpdateID.'/feedback/'.$strFeedback.'/#tab0');
					} else {
						// Return feedback
						$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the gallery');
					}
				}

			}
		}

		// Get all Hotel Galleries
		$arrGalleries			= $objModelGalleries->getGalleries();

		// Parse Variables to view
		$this->view->galleries 		= $arrGalleries;
		$this->view->galleriesTypes	= $arrGalleryTypes;
		$this->view->name			= $strName;
		$this->view->status 		= $intStatus;

	}

	public function gallerieseditAction()
	{
		// Get All Params
		$arrParams	= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/library/galleries/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Models
		$objModelGalleries		= new KZ_Models_Galleries();

		// Get Gallery by ID
		$arrGallery				= $objModelGalleries->getGallery($arrParams['id']);

		// Check if gallery was found
		if(! isset($arrGallery) || ! is_array($arrGallery) || count($arrGallery) == 0) {
			// return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find gallery')));
			$this->_redirect('/admin/library/galleries/feedback/'.$strFeedback.'/#overview/');
		}

		// Get the Gallery types
		$arrGalleryTypes		= $objModelGalleries->getGalleryTypes();

		// Set Default Variables
		$strName				= $arrGallery['name'];
		$intStatus				= $arrGallery['status'];
		$intGalleryType			= $arrGallery['type_id'];
		$booWidhtHeight			= (($arrGallery['width'] != '0' || $arrGallery['height'] != '0') ? 'checked=""' : '');
		$intWidth				= ((isset($arrGallery['width']))? $arrGallery['width'] : '');
		$intHeight				= ((isset($arrGallery['height']))? $arrGallery['height'] : '');

		if($this->getRequest()->isPost()) {

			// Set Post Params
			$arrPostParams	= $this->_getAllParams();

			// Set Post Variables
			$strName			= $arrPostParams['name'];
			$intStatus			= $arrPostParams['status'];
			$intGalleryType		= $arrPostParams['type_id'];
			$booWidhtHeight		= ((isset($arrPostParams['width_height'])) ? $arrPostParams['width_height'] : '');
			$intWidth			= ((isset($arrPostParams['width']))? $arrPostParams['width'] : '');
			$intHeight			= ((isset($arrPostParams['height']))? $arrPostParams['height'] : '');

			if(empty($strName)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} else {

				// Set Update array
				$arrUpdateGallery= array(
					'name'		=> $strName,
					'type_id'	=> $intGalleryType,
					'status'	=> $intStatus,
					'width'		=> $intWidth,
					'height'	=> $intHeight
				);

				// Update Tag
				$intUpdateID	= $objModelGalleries->updateGallery($arrParams['id'], $arrUpdateGallery);

				if(isset($intUpdateID) && is_numeric($intUpdateID)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated gallery')));
					$this->_redirect('/admin/library/galleries/feedback/'.$strFeedback.'/#tab0');
				} else {
					// Return feedback
					$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the gallery');
				}

			}

		}

		// Parse Variables to view
		$this->view->name			= $strName;
		$this->view->type			= $intGalleryType;
		$this->view->status			= $intStatus;
		$this->view->booFixed		= $booWidhtHeight;
		$this->view->width			= $intWidth;
		$this->view->height			= $intHeight;

		$this->view->gallerytypes	= $arrGalleryTypes;

	}

	public function galleriesdeleteAction()
	{
		// Get All Params
		$arrParams	= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/library/galleries/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Models
		$objModelGalleries		= new KZ_Models_Galleries();

		// Get Tag by ID
		$arrGallery				= $objModelGalleries->getGallery($arrParams['id']);

		// Check if category was found
		if(! isset($arrGallery) || ! is_array($arrGallery) || count($arrGallery) == 0) {
			// return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find gallery')));
			$this->_redirect('/admin/library/galleries/feedback/'.$strFeedback.'/#tab0/');
		}

		if($this->getRequest()->isPost()) {

			// Delete Tag
			$intDeleteID	= $objModelGalleries->deleteGallery($arrParams['id']);

			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted gallery')));
				$this->_redirect('/admin/library/galleries/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the gallery');
			}

		}

		// Parse Variables to view
		$this->view->name			= $arrGallery['name'];
		$this->view->status			= $arrGallery['status'];
		$this->view->created		= $arrGallery['created'];
		$this->view->lastmodified	= $arrGallery['lastmodified'];
	}

	public function galleriesimagesAction()
	{
		// Get All Params
		$arrParams				= $this->_getAllParams();

		// Save the Image Ordering for the gallery
		if($this->_request->isXmlHttpRequest()) {

			// Get the Post Data
			$arrPostParams		= $this->_getAllParams();

			if(isset($arrPostParams['imageorder']) && $arrPostParams['imageorder'] != '') {

				// Set the Model
				$objModelGalleries		= new KZ_Models_Galleries();

				// Create the ordering array
				$arrImageOrdering 		= explode(',', $arrPostParams['imageorder']);

				// Check if array is not empty and loop over the results and update the images
				if(!empty($arrImageOrdering) && is_array($arrImageOrdering)) {
					$intOrderNumber		= 1;
					foreach($arrImageOrdering as $key => $intImageID) {
						$objModelGalleries->updateImageOrder($intImageID, $intOrderNumber);
						$intOrderNumber++;
					}
				}
			}
		}

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/library/galleries/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Gallery ID
		$intGalleryID		 	= $arrParams['id'];

		// Set Models
		$objModelGalleries		= new KZ_Models_Galleries();
		$objModelLanguages		= new KZ_Models_Languages();

		// Get active languages
		$arrActiveLanguages		= $objModelLanguages->getActiveLanguages();

		// Get Main Language
		$arrMainLanguage		= $objModelLanguages->getMainLanguage();

		// Set selected Language
		$strLanguage			= ((isset($arrParams['language'])) ? $arrParams['language'] : $arrMainLanguage['language']);

		// Get Images
		$arrGalleryImages		=  $objModelGalleries->getGalleryImages($intGalleryID, $strLanguage);

		// Get Gallery by ID
		$arrGallery				= $objModelGalleries->getGallery($intGalleryID);

		//Check if gallery type isset
		$arrAttributes			= array();
		if(isset($arrGallery['type_id']) && ! empty($arrGallery['type_id'])) {

			// Get gallery type
			$arrGalleryType		= $objModelGalleries->getGalleryTypeAttributes($arrGallery['type_id']);
			$arrAttributes		= explode(',', $arrGalleryType[0]['attributes']);

		}

		$this->view->arrAttributes		= $arrAttributes;
		$this->view->gallery			= $arrGallery;
		$this->view->images				= $arrGalleryImages;
		$this->view->languages			= $arrActiveLanguages;
		$this->view->language			= $strLanguage;
		$this->view->webroot			= $_SERVER['DOCUMENT_ROOT'];
		$this->view->galleryID			= $arrParams['id'];

	}

	public function galleriesimagecropAction()
	{

		// Get All Params
		$arrParams				= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['imageid']) || !is_numeric($arrParams['imageid'])) {
			// return feedback
			$strSerializedFeedback 	= base64_encode(serialize(array('type' => 'error', 'message' => 'Missing id for image')));
			$this->_redirect('/admin/library/galleries/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Models
		$objModelGalleries		= new KZ_Models_Galleries();

		// Gallery ID
		$intGalleryID		 	= $arrParams['gallery_id'];

		// Get Gallery by ID
		$arrGallery				= $objModelGalleries->getGallery($intGalleryID);

		// Check if we have post data for crop
		if($this->getRequest()->isPost()) {
			// Get All Params
			$arrPostParams		= $this->_getAllParams();

			// Image ID
			$intImageID				= $arrParams['imageid'];

			// Get the Image values
			$arrImageValues			= $objModelGalleries->getGalleryImageByID($intImageID);

			// Set the image location
			$strImage				= $_SERVER['DOCUMENT_ROOT'].'/upload/'.$arrImageValues['image_url'];

			// Check if we need to resize or crop
			if(!isset($arrPostParams['resize'])) {
				// Set the New Dimensions
				$arrNewDimensions		= array('x'			=> $arrPostParams['x'],
					'y'			=> $arrPostParams['y'],
					'width'		=> $arrPostParams['w'],
					'height'	=> $arrPostParams['h']);


			} else {
				// Set the New Dimensions
				$arrNewDimensions		= array('x'			=> '0',
					'y'			=> '0',
					'width'		=> $arrGallery['width'],
					'height'	=> $arrGallery['height']);

			}

			// Resample the image
			$booImageResampled			= $objModelGalleries->resampleImage($strImage, $arrNewDimensions);

			// return feedback
			if($booImageResampled === true) {
				// Set the image to Active
				$objModelGalleries->updateImage($intImageID, array('status' => 1));

				// Redirect to the overview page
				$strSerializedFeedback 	= base64_encode(serialize(array('type' => 'success', 'message' => 'Image resized or cropped succesfully')));
				$this->_redirect('/admin/library/galleriesimages/id/'.$intGalleryID.'/feedback/'.$strSerializedFeedback.'/#tab0/');
			}
		}

		// Check if image can remain active
		if($arrGallery['width'] > 0 && $arrGallery['height'] > 0 ) {
			// Image ID
			$intImageID				= $arrParams['imageid'];

			// Get the Image values
			$arrImageValues			= $objModelGalleries->getGalleryImageByID($intImageID);

			// Set the image location
			$strImageLocation		= '/upload/'.$arrImageValues['image_url'];

			$this->view->gallery	= $arrGallery;
			$this->view->image		= $strImageLocation;
		} else {
			// return feedback
			$strSerializedFeedback 	= base64_encode(serialize(array('type' => 'error', 'message' => 'No need to crop the image')));
			$this->_redirect('/admin/library/galleriesimages/id/'.$intGalleryID.'/feedback/'.$strSerializedFeedback.'/#tab0/');
		}



	}

	public function galleriesimagesdeleteAction()
	{
		// Get All Params
		$arrParams	= $this->_getAllParams();

		// Check if Param id is set
		if(! isset($arrParams['id'])) {
			// return feedback
			$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
			$this->_redirect('/admin/library/galleriesimages/id/'.$arrParams['gallery_id'].'/feedback/'.$strSerializedFeedback.'/#tab0/');
		}

		// Set Models
		$objModelGalleries		= new KZ_Models_Galleries();

		// Get Tag by ID
		$arrImage			= $objModelGalleries->getGalleryImageByID($arrParams['id']);

		// Check if category was found
		if(! isset($arrImage) || ! is_array($arrImage) || count($arrImage) == 0) {
			// return feedback
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find Image')));
			$this->_redirect('/admin/library/galleriesimages/id/'.$arrParams['gallery_id'].'/feedback/'.$strFeedback.'/#tab0/');
		}

		if($this->getRequest()->isPost()) {
			// Set the Image location before delete
			$strImageLocation		= $arrImage['image_url'];

			// Delete Image from DB
			$intDeleteID			= $objModelGalleries->deleteImage($arrParams['id']);

			if(isset($intDeleteID) && is_numeric($intDeleteID)) {
				// Set document Root
				$strDocumentRoot		= $_SERVER['DOCUMENT_ROOT'];

				// Remove the physical file
				unlink($strDocumentRoot.'/upload/'.$strImageLocation);

				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted image')));
				$this->_redirect('/admin/library/galleriesimages/id/'.$arrParams['gallery_id'].'/feedback/'.$strFeedback.'/#tab0');
			} else {
				// Return feedback
				$this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the image');
			}

		}

		// Parse Variables to view
		$this->view->name			= $arrImage['image_url'];
		$this->view->status			= $arrImage['status'];
		$this->view->created		= $arrImage['created'];
		$this->view->lastmodified	= $arrImage['lastmodified'];
		$this->view->gallery_id		= $arrParams['gallery_id'];
	}

	/**
	 * Function for creating folders inside the upload directory
	 */
	public function createfolderAction()
	{
		// Disable the Layout and Rende file
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		// Get the Selected Type
		$strType = $this->_getParam('type');

		// Get the Folder Name
		$strNewFolderName = $this->_getParam('foldername');

		// Check if we have a type
		if (!is_null($strType) && !is_null($strNewFolderName)) {

			// Set the Correct Session
			$strSessionName = (($strType == 'image') ? 'Image_Upload' : 'Files_Upload');

			// Create a Internet Correct foldername without spaces etc.
			$objViewFormat = new KZ_View_Helper_Format();
			$strNewFolderName = $objViewFormat->format($strNewFolderName,null,'folder');

			// Check if we have the selected Session
			$booSessionExists = Zend_Session::namespaceIsset($strSessionName);
			if ($booSessionExists === true) {

				// Get the Session Data
				$sesData = new Zend_Session_Namespace($strSessionName);

				// Get the Starting Location
				$strStartLocation = $sesData->startlocation.'/'.$sesData->folder;

				// Check if we need to add the trailing slash to the Image Location
				if (substr($strStartLocation,-1) != '/') {
					$strStartLocation .= '/';
				}

				$strDocRoot = realpath($_SERVER['DOCUMENT_ROOT']);
				if (substr_count($strStartLocation,$strDocRoot) === 0) {
					$strStartLocation = $strDocRoot.DIRECTORY_SEPARATOR.$strStartLocation;
				}
				$strStartLocation = realpath($strStartLocation).DIRECTORY_SEPARATOR;

				// Check if New Directory exists
				if (is_dir($strStartLocation.$strNewFolderName)) {
					echo 'Directory already exists';
				} else {
					// Create the new folder
					mkdir($strStartLocation.$strNewFolderName) or die($strStartLocation.$strNewFolderName);
					echo true;
				}
			} else {
				echo 'Session not found';
			}
		} else {
			echo 'No type selected';
		}
	}
}
