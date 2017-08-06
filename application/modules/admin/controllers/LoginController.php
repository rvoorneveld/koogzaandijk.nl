<?php
class Admin_LoginController extends KZ_Controller_Action
{
	
	public function indexAction()
	{
		// Change view to login screen
		$this->_helper->_layout->setLayout('login');
		
		// Get All Params
		$arrParams = $this->_getAllParams();
		
		// Parse defaults to view
		$this->view->email = '';
		
		if($this->getRequest()->isPost()) {
			
			// Set validate object
			$objValidateEmail 	= new Zend_Validate_EmailAddress();
			
			// Parse post to view
			$this->view->email 	= $arrParams['email'];
			
			if(empty($arrParams['email'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in your email address');
			} elseif(! $objValidateEmail->isValid($arrParams['email'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a valid email address');
			} elseif(empty($arrParams['password'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in your password');
			} else {
				
				// Set user model
				$objModelUsers 	= new KZ_Models_Users();
				
				// Get user by e-mail and password
				$objUser		= $objModelUsers->getUserByEmailAndPassword($arrParams['email'], $arrParams['password']);
				
				// Check if user exists
				if(! is_null($objUser)) {
					
					// Create new Zend Session Namespace
					$objNamespace 		= new Zend_Session_Namespace('KZ_Admin');
					$objNamespace->setExpirationSeconds((3600 * 8), 'user'); // Expire in 8 hours
					$objNamespace->user = $objUser->toArray();

					if(! empty($objNamespace->user['blogger_id'])) {
                        $this->_redirect('/admin/blog/');
                        exit;
                    }

					$this->_redirect('/admin/pages/');
					exit;
					
				} else {
					$this->view->feedback = array('type' => 'error', 'message' => 'The combination of your email address and password is incorrect');
				}
				
			}
			
		}
		
	}
	
	public function lostpasswordAction()
	{
		// Change view to login screen
		$this->_helper->_layout->setLayout('login');
		
		if($this->getRequest()->isPost()) {
			
			// Get all the post data
			$arrPostData 			= $this->getRequest()->getParams();
				
			if(!empty($arrPostData)) {
				$objUsers			= new KZ_Models_Users();
				$arrUserData		= $objUsers->checkUserByEmail($arrPostData['email']);
		
				if(!is_null($arrUserData)) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'A new password has been send to you e-mail address')));
					$this->_redirect('/admin/login/index/feedback/'.$strFeedback.'/');
				} else {
					$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'E-mail adres not found in our system')));
					$this->_redirect('/admin/login/lostpassword/feedback/'.$strFeedback.'/');
				}
			}
		}
	}

}