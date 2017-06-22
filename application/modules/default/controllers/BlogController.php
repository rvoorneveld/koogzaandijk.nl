<?php
class BlogController extends KZ_Controller_Action
{
    protected $objModelBlog;
    protected $blogger;
    protected $item;
    protected $profile;
    protected $reactions = false;

    public function init()
    {
        // Get Blogger By Slug
        $this->objModelBlog = new KZ_Models_Blog();
        $this->blogger = $this->objModelBlog->getBloggerBySlug($this->_getParam('blogger'));
        $this->item = $this->objModelBlog->getBlogItemBySlug($this->_getParam('item'));

        // Check if Data was found
        $booBlogger = self::check($this->blogger);
        $booBlogItem = self::check($this->item);

        // Get Profile Session Namespace
        $objControllerSession = new KZ_Controller_Session();
        $this->profile = $objControllerSession->getProfileSession();

        if($this->profile) {
            // Get Reactions on Blog
            $this->reactions = $this->objModelBlog->getBlogItemReactions($this->item['id'],KZ_Controller_Action::STATE_ACTIVE);
        }

        if($booBlogger === true  && $booBlogItem !== true) {
        	// Blog items overview for single blogger
            $this->forward('singleblogger');
        }

        if($booBlogger === false && $booBlogItem === false) {
			$this->forward('bloggers');
        }
    }

    public function indexAction(){

        if($this->profile !== false) {
            if ($this->getRequest()->isPost()) {

                if(empty($this->_getParam('reaction'))) {
                    $strType = 'error';
                    $strMessage = 'U bent vergeten een reactie in te vullen';
                } else {
                    $arrReaction = [
                        'blog_item_id' => $this->item['id'],
                        'profile_id' => $this->profile['profile_id'],
                        'created' => (new Zend_Date())->toString('yyyy-MM-dd HH:mm:ss'),
                        'reaction' => htmlentities($this->_getParam('reaction')),
                        'status' => KZ_Controller_Action::STATE_ACTIVE
                    ];

                    $intSucces = $this->objModelBlog->addReaction($arrReaction);

                    if (!empty($intSucces) && is_numeric($intSucces)) {
                        $strType = 'success';
                        $strMessage = 'Uw reactie is met succes opgeslagen';

                        // Reload Reactions on Blog
                        $this->reactions = $this->objModelBlog->getBlogItemReactions($this->item['id'],KZ_Controller_Action::STATE_ACTIVE);

                    } else {
                        $strType = 'error';
                        $strMessage = 'Er is iets mis gegaan met het toevoegen van uw reactie';
                    }
                }

                $this->view->assign([
                    'feedback' => [
                        'type' => $strType,
                        'message' => $strMessage
                    ]
                ]);

            }
        }

        $this->view->assign([
            'blogger' => $this->blogger,
            'item' => $this->item,
            'recent' => self::excludeCurrentBlogItem($this->objModelBlog->getBloggerItems($this->blogger['id']),$this->item['id']),
            'profile' => $this->profile,
            'reactions' => $this->reactions,
        ]);

    }

    public function singlebloggerAction()
    {
        $this->view->assign([
            'items' => $this->objModelBlog->getBloggerItems($this->blogger['id']),
            'blogger' => $this->blogger,
        ]);
    }

    public function bloggersAction()
    {
        $this->view->assign([
            'bloggers' => $this->objModelBlog->getBloggers(),
        ]);
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