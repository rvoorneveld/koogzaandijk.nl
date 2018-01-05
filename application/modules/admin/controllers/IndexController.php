<?php
class Admin_IndexController extends KZ_Controller_Action {
	
	public function indexAction()
	{

		return $this->_redirect('/admin/pages/');
		exit;
	}
	
}