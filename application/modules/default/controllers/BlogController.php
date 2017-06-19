<?php
class BlogController extends KZ_Controller_Action
{
    protected $objModelBlog;
    protected $blogger;
    protected $item;

    public function init()
    {
        // Get Blogger By Slug
        $this->objModelBlog = new KZ_Models_Blog();
        $this->blogger = $this->objModelBlog->getBloggerBySlug($this->_getParam('blogger'));
        $this->item = $this->objModelBlog->getBlogItemBySlug($this->_getParam('item'));

        // Check if Data was found
        $booBlogger = self::check($this->blogger);
        $booBlogItem = self::check($this->item);

        if($booBlogger === true  && $booBlogItem !== true) {
        	// Blog items overview for single blogger
	        $this->singlebloggerAction();
        }

        if($booBlogger === false && $booBlogItem === false) {
			// Bloggers overview
			$this->bloggersAction();
        }

        $this->view->assign([
            'blogger' => $this->blogger,
            'item' => $this->item,
            'recent' => self::excludeCurrentBlogItem($this->objModelBlog->getBloggerItems($this->blogger['id']),$this->item['id'])
        ]);
    }

    public function indexAction(){}

    private function singlebloggerAction()
    {
    	var_dump($this->blogger);
    	exit;
    }

    private function bloggersAction()
    {
    	echo 'show bloggers';
    	exit;
    }

    private function check($arrData)
    {
        if(empty($arrData) || ! is_array($arrData)) {
            return false;
        }
        return true;
    }

    private function excludeCurrentBlogItem($items,$id)
    {
        $filtered = [];
        foreach($items as $item) {
            if($item['id'] == $id) { continue; }
            $filtered[$item['id']] = $item;
        }
        return $filtered;
    }
}