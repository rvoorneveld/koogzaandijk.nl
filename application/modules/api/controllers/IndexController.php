<?php

class Api_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        // Set xml header
        $this->getResponse()
             ->setHeader('Content-type','text/xml');
    }

    public function indexAction()
    {
        // Get xml model
        $objApi = new KZ_Api_Api();

        // Check if XML string is send
        if($this->_getParam('xml')){
            
            // Start the xmlApi send allParams for logging
            echo $objApi->parse($this->_getAllParams());
        }else{
            // No xml is send 
            echo $objApi->createError('101','no xml send');
        }

    }

}