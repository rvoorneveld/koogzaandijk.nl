<?php
class Admin_LogoutController extends KZ_Controller_Action
{
	
	public function indexAction()
	{
		// Disable view
		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
		
    	// Unset User Session
		$objNamespace 		= new Zend_Session_Namespace('KZ_Admin');
		unset($objNamespace->user);
		
		// Create feedback
		$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully logged out')));
		$this->_redirect('/admin/login/index/feedback/'.$strFeedback);
		exit;
	}
	
}