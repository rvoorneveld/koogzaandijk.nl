<?php
class Admin_DashboardController extends KZ_Controller_Action
{
	
	public function indexAction(){

        $objDashboardViewHelper = new KZ_View_Helper_Dashboard();
        $this->view->gacharts = $objDashboardViewHelper->getGaCharts();

    }
	
}