<?php

class Admin_UsersController extends KZ_Controller_Action
{

	private $users;
	
	public function init()
	{
		// Set the Users Model
		$this->users			= new KZ_Models_Users();
	}
	
	public function indexAction()
	{
		// Set Defaults Variables
		$intAccessLevel				= '';
		$intBlogID                  = NULL;
		$strName					= '';
		$strEmail					= '';
		$intStatus					= 0;
		
		if($this->getRequest()->isPost()) {
			
			// Get all the post vars
			$arrPostData 			= $this->_getAllParams();
			
			// Set Post Variables
			$intAccessLevel				= $arrPostData['user_group_id'];
			$arrPostData['name']		= ((isset($arrPostData['name']) && ! empty($arrPostData['name'])) ? $arrPostData['name'] : '');
			$arrPostData['email']		= ((isset($arrPostData['email']) && ! empty($arrPostData['email'])) ? $arrPostData['email'] : '');
            $arrPostData['blogger_id']	= ((isset($arrPostData['blogger_id']) && ! empty($arrPostData['blogger_id'])) ? $arrPostData['blogger_id'] : NULL);

            // Check if User Group doesn't equal a blogger
            if($arrPostData['user_group_id'] != 4) {
                $arrPostData['blogger_id'] = NULL;
            }

			// Set Email Validation Object
			$objEmailValidator		= new Zend_Validate_EmailAddress();
			
			if(empty($intAccessLevel)) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an access level');
			} elseif(empty($arrPostData['name'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
			} elseif(empty($arrPostData['email'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in an email');
			} elseif(! $objEmailValidator->isValid($arrPostData['email'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a valid email');
			} else {
				
				// Save the data
				$intAffectedRows		= $this->users->addAdminUser($arrPostData);
	
				if($intAffectedRows > 0) {
					$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'User information saved succesfully')));
				} else {
					$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Error saving new User information')));
				}
				
				$this->_redirect('/admin/users/index/feedback/'.$strFeedback.'/');
				
			}

		}

		// Get all users
		$arrUsers					= $this->users->getAllUsers();

		// Get All Bloggers
        $objModelBlog               = new KZ_Models_Blog();
        $arrBloggers                = $objModelBlog->getBloggers();
	
		// Get all User Auth Groups
		$arrUserGroups				= $this->users->getAllUserGroupLevels();
	
		// Send to the view
		$this->view->users			= $arrUsers;
		$this->view->usergroups		= $arrUserGroups;
		$this->view->bloggers       = $arrBloggers;
	}
	
	public function editAction()
	{
		// Get the user information by ID
		$intUserID					= $this->_getParam('id');
	
		// Check if a userID is given
		if(is_null($intUserID)) {
			$this->_redirect('/admin/users/');
		}
	
		if($this->getRequest()->isPost()) {
			// Get all the post vars
			$arrPostData 			= $this->getRequest()->getParams();
	
			// Check if password needs to be changed
			if(isset($arrPostData['password']) && $arrPostData['password'] != '') {
				if($arrPostData['password'] == $arrPostData['confirm_password']) {
					$arrPostData['password']	= base64_encode($arrPostData['password']);
					unset($arrPostData['confirm_password']);
				} else {
					$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Password are not the same')));
					$this->_redirect('/admin/users/edit/id/'.$intUserID.'/feedback/'.$strFeedback.'/#overview');
				}
			} else {
				unset($arrPostData['password']);
				unset($arrPostData['confirm_password']);
			}
			// Save the data
			$intAffectedRows		= $this->users->saveUser($intUserID, $arrPostData);
	
			// Check if user is updated or not
			if($intAffectedRows > 0) {
				$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'User information saved succesfully')));
			} else {
				$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Error saving new User information or No changes where made')));
			}
			$this->_redirect('/admin/users/index/feedback/'.$strFeedback.'/#overview');
		}
	
		$arrUserInfo				= $this->users->getUserByUserID($intUserID);
	
		// Get all User Auth Groups
		$arrUserGroups				= $this->users->getAllUserGroupLevels();
	
		// Send to the view
		$this->view->user				= $arrUserInfo;
		$this->view->usergroups			= $arrUserGroups;
	}
	
	public function deleteAction()
	{
		// Disable the layout and view
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$intUserID 			= $this->_getParam('id');
		
		// Check if a company is given
		if(is_null($intUserID)) {
			$this->_redirect('/admin/users/');
		}
		
		// Check if user is deleted or not
		$intNumberRowsAffected	= $this->users->deleteUser($intUserID);
		if($intNumberRowsAffected > 0) {
			$strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'User succesfully deleted')));
		} else {
			$strFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'User not found')));
		}
		
		$this->_redirect('/admin/users/index/feedback/'.$strFeedback.'/#overview');
	}


}