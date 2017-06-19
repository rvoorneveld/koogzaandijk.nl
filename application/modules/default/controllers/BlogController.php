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
        self::check([$this->blogger,$this->item]);

        $this->view->assign([
            'blogger' => $this->blogger,
            'item' => $this->item,
            'recent' => self::excludeCurrentBlogItem($this->objModelBlog->getBloggerItems($this->blogger['id']),$this->item['id'])
        ]);
    }

    public function indexAction(){}

    private function check($arrData)
    {
        foreach($arrData as $arrDataItem) {
            if(empty($arrDataItem) || ! is_array($arrDataItem)) {
                $this->_redirect(ROOT_URL, array('code'=>301));
            }
        }
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