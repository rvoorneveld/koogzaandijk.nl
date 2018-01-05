<?php
class Admin_IndexController extends KZ_Controller_Action {
	
	public function indexAction()
	{

		return $this->redirect('/admin/pages/');
		exit;
	}
	
}