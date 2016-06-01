<?php
class KZ_Models_Galleries extends KZ_Controller_Table
{

	protected $_name 	= 'galleries';
	protected $_primary = 'gallery_id';


	/*
	 * Get all galleries
	 * @param string $strReturnAssoc
	 * @return 	array 	$arrData
	 */
	public function getGalleries($strReturnAssoc = false)
	{
		// Set Query string
		$strQuery 	= $this->select()
			->order('name');

		// Get Data
		$arrData = $this->returnData($strQuery);

		// Check if assoc array must be returned
		if($strReturnAssoc !== false) {
			$arrReturnAssoc = array();
			foreach($arrData as $intReturnKey => $arrReturnData) {
				$arrReturnAssoc[$arrReturnData[$strReturnAssoc]] = $arrReturnData;
			}
			return $arrReturnAssoc;
		}

		// Return array
		return $arrData;
	}

	/**
	 *
	 * Get Tag By ID
	 * @param 	integer $intTagID
	 * @return 	array 	$arrTag
	 */
	public function getGallery($intGalleryID)
	{
		// Set Query string
		$strQuery 	= 	$this->select()
			->where('gallery_id = ?', $intGalleryID);

		// Return array
		return $this->returnData($strQuery, 'array', 'fetchRow');
	}

	/*
	 * Add gallery
	 * @param string $strReturnAssoc
	 * @return 	array 	$arrData
	 */
	public function addGallery($arrGallery)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		// Insert the Data
		$intInsertID = $this->insert(array_merge($arrGallery, array('created' => $strDate)));

		// Return Insert ID
		return $intInsertID;
	}

	/**
	 *
	 * Update Gallery by unique Gallery ID
	 * @param 	integer $intGalleryID
	 * @param 	array 	$arrGallery
	 * @return 	integer $intUpdateID
	 */
	public function updateGallery($intGalleryID, $arrGallery)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		// Update the Data
		$intUpdateID = $this->update(array_merge($arrGallery, array('lastmodified' => $strDate)), "gallery_id = $intGalleryID");

		// Return Update ID
		return $intUpdateID;
	}

	/**
	 *
	 * Delete Tag by unique Gallery ID
	 * @param 	integer $intGalleryID
	 * @return	integer	$intDeleteID
	 */
	public function deleteGallery($intGalleryID)
	{

		$intDeleteID = $this->delete("gallery_id = $intGalleryID");
		return $intDeleteID;
	}

	/**
	 *
	 * Get Images by Gallery ID
	 * @param integer #intImageID
	 * @return array $arrGalleryImages
	 */
	public function getGalleryImages($intGalleryID, $strLanguage, $intStatus = null)
	{
		$strQuery = $this->select()
			->setIntegrityCheck(false)
			->from('galleries_images')
			->join('gallery_images_attributes', 'galleries_images.image_id = gallery_images_attributes.gallery_image_id', array('name', 'description', 'subscription'))
			->where('gallery_id = '.$intGalleryID)
			->where('gallery_images_attributes.language = "'.$strLanguage.'"')
			->order('rank ASC');

		if(!is_null($intStatus)) {
			$strQuery->where('galleries_images.status = ?', '1');
		}

		return $this->returnData($strQuery);
	}

	public function getGalleryImageByID($intImageID)
	{
		$strQuery = $this->select()
			->setIntegrityCheck(false)
			->from('galleries_images')
			->where('image_id = '.$intImageID);

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}

	/**
	 *
	 * Add Images
	 * @param integer $arrImages
	 * @return array $arrGalleryImages
	 */
	public function addImage($arrImages)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		// Insert the Data
		$objDb				= Zend_Db_Table::getDefaultAdapter();

		$intInsertID 		= $objDb->insert('galleries_images',array_merge($arrImages, array('created' => $strDate)));

		// Get Image ID
		$intLastInsertID	= $this->getAdapter()->lastInsertId();

		// Return Insert ID
		return $intLastInsertID;
	}

	/**
	 * Function for updating an image
	 *
	 * @param integer $intImageID
	 * @param array $arrUpdateData
	 */
	public function updateImage($intImageID, $arrUpdateData)
	{
		// Create the modified date
		$objDate 			= new Zend_Date();
		$strDate 			= $objDate->toString('yyyy-MM-dd HH:mm:ss');

		// Insert the Data
		$objDatabase		= Zend_Db_Table::getDefaultAdapter();

		$objDatabase->update('galleries_images', array_merge($arrUpdateData, array('lastmodified' => $strDate)), "image_id = '".$intImageID."'");
	}

	/**
	 * Function for setting the Image ordering in a gallery
	 *
	 * @param integer $intImageID
	 * @param integer $intOrderNumber
	 */
	public function updateImageOrder($intImageID, $intOrderNumber)
	{
		$objDatabase 		= new Zend_Db_Table(array('name' => 'galleries_images'));
		$objDatabase->update(array('rank'	=> $intOrderNumber), "image_id = '".$intImageID."'");
	}

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

			// Create new image with Crop Width and Height
			$strNewImage		= imagecreatetruecolor($arrNewDimensions['width'], $arrNewDimensions['height']);

			imagealphablending($strNewImage, false);
			imagesavealpha($strNewImage, true);

			// Set default width and height
			$intImageWidth		= $arrNewDimensions['width'];
			$intImageHeight		= $arrNewDimensions['height'];

			// Check if we need to resize the image
			if($arrNewDimensions['x'] == '0' && $arrNewDimensions['y'] == '0') {
				$intImageWidth		= $oldWidth;
				$intImageHeight		= $oldHeight;
			}

			imagecopyresampled($strNewImage, $strOldImage, 0, 0, $arrNewDimensions['x'], $arrNewDimensions['y'], $arrNewDimensions['width'], $arrNewDimensions['height'], $intImageWidth, $intImageHeight);

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

	/**
	 * Function for deleting the image from the DB
	 *
	 * @param integer $intImageID
	 */
	public function deleteImage($intImageID)
	{
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intDeleteID	= $objDb->delete('galleries_images', "`image_id` = $intImageID");
		return $intDeleteID;
	}

	/**
	 *
	 * Add Image Attributes for each active language
	 * @param integer $arrImageAttributes
	 * @return int $intInsertID
	 *
	 */
	public function addImageAttributes($arrImageAttributes)
	{
		// Create created date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		// Insert the Data
		$objDb				= Zend_Db_Table::getDefaultAdapter();

		$intInsertID 		= $objDb->insert('gallery_images_attributes',array_merge($arrImageAttributes, array('created' => $strDate)));

		$intImageID		= $this->getAdapter()->lastInsertId();

		// Return Insert ID
		return $intImageID;
	}

	/**
	 *
	 * Get gallery Types
	 * @param integer $intGalleryID
	 * @return array $arrGalleryTypes
	 */
	public function getGalleryTypes()
	{
		$strQuery = $this->select()
			->setIntegrityCheck(false)
			->from('gallery_types');

		return $this->returnData($strQuery);
	}

	public function getGalleryTypeAttributes($intTypeID)
	{
		$strQuery = $this->select()
			->setIntegrityCheck(false)
			->from('gallery_types')
			->where('gallery_type_id = '.$intTypeID);

		return $this->returnData($strQuery);
	}

	public function updateImageInput($intImageID, $arrImage, $strLanguage)
	{
		// Create lastmodified date
		$objDate = new Zend_Date();
		$strDate = $objDate->toString('yyyy-MM-dd HH:mm:ss');

		// Update the Data
		$objDb			= Zend_Db_Table::getDefaultAdapter();
		$intUpdateID 	= $objDb->update('gallery_images_attributes', array_merge($arrImage, array('lastmodified' => $strDate)), "gallery_image_id = $intImageID AND language = '".$strLanguage."'");

		// Return Update ID
		return $intUpdateID;
	}


	/**
	 *
	 * Get image Attributes
	 * @param integer $intImageID
	 * @return array $arrData
	 */
	public function getImageAttributes($intImageID, $strLanguage)
	{

		$strQuery = $this->select()
			->setIntegrityCheck(false)
			->from('gallery_images_attributes')
			->where('gallery_image_id = '.$intImageID)
			->where('language = "'. $strLanguage.'"');

		return $this->returnData($strQuery, 'array', 'fetchRow');
	}

	public function deleteGalleryImage($intImageID)
	{
		$this->_name		= 'galleries_images';
		return $this->delete("WHERE image_id = '".$intImageID."'");

	}

	public function deleteGalleryImageAttributes($intImageID)
	{
		$this->_name		= 'gallery_image_attributes';
		return $this->delete("WHERE gallery_image_id = '".$intImageID."'");

	}
}