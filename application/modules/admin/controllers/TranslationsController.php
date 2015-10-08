<?php 
class Admin_TranslationsController extends KZ_Controller_Action
{
	
	public function indexAction()
	{
		$objModelTranslations			= new KZ_Models_Translations();
		
		$arrAllTranslations				= $objModelTranslations->getAllTranslations();
	
		$arrTranslations['missing']		= array();
		$arrTranslations['found']		= array();
		if(!empty($arrAllTranslations)) {
			foreach($arrAllTranslations as $key => $arrValues) {
				if($arrValues['status'] == 'missing') {
					$arrTranslations['missing'][]		= $arrValues;
				} else {
					$arrTranslations['found'][]			= $arrValues;
				}
			}
		}
	
		$this->view->translation			= $arrTranslations['found'];
		$this->view->missingtranslation		= $arrTranslations['missing'];
	}
	
	public function editAction()
	{
		
		$objModelTranslations			= new KZ_Models_Translations();
		
		$intTranslationID		= $this->_getParam('id');
	
		if(is_null($intTranslationID)) {
			$this->_redirect('/admin/translations/');
		}
	
		if($this->getRequest()->isPost()) {
			// Get all the post vars
			$arrPostData 			= $this->getRequest()->getParams();
	
			// Save the data
			$intAffectedRows		= $objModelTranslations->updateTranslation($intTranslationID, nl2br($arrPostData['translation']));
	
			if($intAffectedRows > 0) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Translation saved succesfully')));
				$this->_redirect('/admin/translations/index/feedback/'.$strFeedback.'/#overview');
			} else {
				$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Error saving the Translation')));
				$this->_redirect('/admin/translations/index/feedback/'.$strFeedback.'/#overview');
			}
		}
	
		// Get the single translation data
		$arrTranslationData					= $objModelTranslations->getTranslationByID($intTranslationID);
	
		// Send data to the view
		$this->view->translationdata		= $arrTranslationData;
	
	}
	
}