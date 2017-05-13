<?php
class BlogController extends KZ_Controller_Action
{
    protected $blogger;
    protected $item;

    public function init()
    {
        // Get Blogger By Slug
        $objModelBlog = new KZ_Models_Blog();
        $this->blogger = $objModelBlog->getBloggerBySlug($this->_getParam('blogger'));
        $this->item = $objModelBlog->getBlogItemBySlug($this->_getParam('item'));

        // Parse Variables to View
        $this->view->blogger = $this->blogger;
        $this->view->item = $this->item;
    }

    public function indexAction()
    {

    }
}