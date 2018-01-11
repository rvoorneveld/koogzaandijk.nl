<?php

class PageController extends KZ_Controller_Action
{

    public function indexAction()
    {

    }

    public function pageAction()
    {
        $arrParams = $this->getAllParams();

        if (true === empty($arrParams['title'])) {
            $this->redirect(ROOT_URL);
            exit;
        }

        $intMainPageID = 0;
        $strActiveRouteContact = false;

        $objConfig = Zend_Registry::get('Zend_Config');

        $objModelPages = new KZ_Models_Pages();
        $objModelNews = new KZ_Models_News();

        if (false === empty($arrParams['mainpage'])) {
            $arrPage = $objModelPages->getPageBySlug($arrParams['mainpage']);
            if (false === empty($arrPage) && true === is_array($arrPage)) {
                $intMainPageID = $arrPage['page_id'];
            }
        }

        if ('route-contact' === $strTitleSlug = $arrParams['title']) {
            $strActiveRouteContact = true;
        }

        $arrPage = $objModelPages->getPageBySlug($strTitleSlug, $intMainPageID);
        $this->_setSeo($arrPage);

        $this->view->assing([
            'page' => $arrPage,
            'content' => $objModelPages->getPageContent($arrPage['page_id'], 1),
            'latest' => $objModelNews->getLatestNews($intLimit = (int)$objConfig->news->maxRelated * 2),
            'activeRouteContact' => $strActiveRouteContact,
        ]);

    }

    public function newsAction()
    {
        // Get All Params
        $arrParams = $this->_getAllParams();

        // Check if title was set
        if (!isset($arrParams['title']) || empty($arrParams['title'])) {
            $this->_redirect(ROOT_URL);
            exit;
        }

        // Get Config
        $objConfig = Zend_Registry::get('Zend_Config');

        // Set Max Related items
        $intMaxItems = (int)$objConfig->news->maxRelated;

        // Set Models
        $objModelNews = new KZ_Models_News();
        $objModelTags = new KZ_Models_Tags();

        // Set Title Slug
        $strTitleSlug = $arrParams['title'];

        // Get News by slug
        $arrNews = $objModelNews->getNewsBySlug($strTitleSlug);

        // Check if News was found and active
        if (!isset($arrNews) || !is_array($arrNews) || count($arrNews) == 0) {
            $this->_redirect(ROOT_URL);
            exit;
        }

        // Set SEO
        $this->_setSeo($arrNews);

        // Get Related News
        $arrRelatedNews = $objModelNews->getNewsByTags($arrNews['news_id'], $arrNews['tags'], $intMaxItems);

        // Get Latest News
        $arrLatestNews = $objModelNews->getLatestNews($intMaxItems);

        // Get Tags
        $arrTags = $objModelTags->getTags('tag_id');

        // Set Status - active
        $intStatus = 1;

        $arrNewsContent = $objModelNews->getNewsContent($arrNews['news_id'], $intStatus);

        $this->view->news = $arrNews;
        $this->view->content = $arrNewsContent;
        $this->view->tags = $arrTags;
        $this->view->related = $arrRelatedNews;
        $this->view->latest = $arrLatestNews;

    }

    public function allnewsAction()
    {
        // Set Models
        $objModelNews = new KZ_Models_News();
        $objModelCategories = new KZ_Models_Categories();

        // Set Defaults
        $arrNewsByMonth = array();

        // Set Date Object
        $objDate = new Zend_Date();
        $intYear = (int)$objDate->toString('yyyy');

        // Get News Categories
        $arrCategories = $objModelCategories->getCategories('category_id');

        // Get All Active News
        $arrNews = $objModelNews->getNews(false, false, 1, $intYear);

        // Check if News was found
        if (isset($arrNews) && is_array($arrNews) && count($arrNews) > 0) {

            foreach ($arrNews as $intNewsKey => $arrNewsItem) {

                // Get News Content
                $arrNewsContent = $objModelNews->getNewsContent($arrNewsItem['news_id'], 1);

                // Check if News content was found
                if (isset($arrNewsContent) && is_array($arrNewsContent) && count($arrNewsContent) > 0) {

                    // Set Unserialized Content
                    $arrUnserializedContent = unserialize($arrNewsContent[0]['data']);

                    if (isset($arrUnserializedContent) && is_array($arrUnserializedContent) && count($arrUnserializedContent) > 0) {

                        foreach ($arrUnserializedContent as $strDataKey => $strDataValue) {

                            // Find Title
                            if (strstr($strDataKey, 'title')) {
                                $arrNewsItem['content']['title'] = stripslashes($arrUnserializedContent[$strDataKey]);
                            }

                            // Find Text
                            if (strstr($strDataKey, 'text')) {
                                $arrNewsItem['content']['text'] = stripslashes(KZ_View_Helper_Truncate::truncate(strip_tags(str_replace(array('<br />', '<br>'), ' ', $arrUnserializedContent[$strDataKey])), 50, '', '/nieuws/' . $arrNewsItem['nameSlug'] . '/'));
                            }

                        }

                    }

                    $intMonth = (int)$this->view->date()->format($arrNewsItem['date'], 'MM');

                    // Check if Month wasn't set yet
                    if (!isset($arrNewsByMonth[$intMonth])) {
                        // Set empty Month array
                        $arrNewsByMonth[$intMonth] = array();
                    }

                    $arrNewsByMonth[$intMonth][] = $arrNewsItem;

                }

            }

        }

        $this->view->news = $arrNewsByMonth;
        $this->view->categories = $arrCategories;

    }

    public function agendaAction()
    {
        // Get All Params
        $arrParams = $this->_getAllParams();

        // Check if title was set
        if (!isset($arrParams['title']) || empty($arrParams['title'])) {
            $this->_redirect(ROOT_URL);
            exit;
        }

        // Get Config
        $objConfig = Zend_Registry::get('Zend_Config');

        // Set Max Related items
        $intMaxItems = (int)$objConfig->news->maxRelated;

        // Set Models
        $objModelAgenda = new KZ_Models_Agenda();

        // Set Title Slug
        $strTitleSlug = $arrParams['title'];

        // Get Agemda by slug
        $arrAgenda = $objModelAgenda->getAgendaBySlug($strTitleSlug);

        // Check if Agenda was found and active
        if (!isset($arrAgenda) || !is_array($arrAgenda) || count($arrAgenda) == 0) {
            $this->_redirect(ROOT_URL);
            exit;
        }

        // Set Status - active
        $intStatus = 1;

        // Get Agenda Content
        $arrAgendaContent = $objModelAgenda->getAgendaContent($arrAgenda['agenda_id'], $intStatus);

        if ($arrAgenda['news_id'] > 0) {

            // Set News Model
            $objModelNews = new KZ_Models_News();

            // Get News Content
            $arrNewsContent = $objModelNews->getNewsContent($arrAgenda['news_id'], $intStatus);

            // Check if News Content was found
            if (isset($arrNewsContent) && is_array($arrNewsContent) && count($arrNewsContent) > 0) {
                // Set News Content as Agenda Content - Attached news item will be displayed instead of agenda content
                $arrAgendaContent = $arrNewsContent;
            }

        }

        // Set SEO
        $this->_setSeo($arrAgenda);

        // Set Date Object
        $objDate = new Zend_Date();
        $strDate = $objDate->toString('yyyy/MM/dd');

        // Get Related News
        $arrLatestAgenda = $objModelAgenda->getAgenda($strDate, $intStatus);

        $this->view->agenda = $arrAgenda;
        $this->view->content = $arrAgendaContent;
        $this->view->latest = $arrLatestAgenda;
    }

    public function allagendaAction()
    {
        // Set Models
        $objModelAgenda = new KZ_Models_Agenda();

        // Set Defaults
        $arrAgendaByMonth = array();

        // Set Current Date
        $objDate = new Zend_Date();

        // Set Status - active
        $intStatus = 1;

        // Get Agenda
        $arrAgenda = $objModelAgenda->getAgenda($objDate->toString('yyyy-MM-dd'), $intStatus);

        // Check if News was found
        if (isset($arrAgenda) && is_array($arrAgenda) && count($arrAgenda) > 0) {

            foreach ($arrAgenda as $intAgendaKey => $arrAgendaItem) {

                // Set Defaults
                $booAddAgenda = false;

                // Get News Content
                $arrAgendaContent = $objModelAgenda->getAgendaContent($arrAgendaItem['agenda_id'], 1);

                // Check if News content was found
                if (isset($arrAgendaContent) && is_array($arrAgendaContent) && count($arrAgendaContent) > 0) {

                    // Set Unserialized Content
                    $arrUnserializedContent = unserialize($arrAgendaContent[0]['data']);

                    if (isset($arrUnserializedContent) && is_array($arrUnserializedContent) && count($arrUnserializedContent) > 0) {

                        foreach ($arrUnserializedContent as $strDataKey => $strDataValue) {

                            // Find Title
                            if (strstr($strDataKey, 'title')) {
                                $arrAgendaItem['content']['title'] = $arrUnserializedContent[$strDataKey];
                            }

                            // Find Text
                            if (strstr($strDataKey, 'text')) {
                                $arrAgendaItem['content']['text'] = KZ_View_Helper_Truncate::truncate(strip_tags(str_replace(array('<br />', '<br>'), ' ', $arrUnserializedContent[$strDataKey])), 50, '', '/agenda/' . $arrAgendaItem['nameSlug'] . '/');
                            }

                            $booAddAgenda = true;

                        }

                    }
                }

                if ($arrAgendaItem['news_id'] > 0) {

                    // Set News Model
                    $objModelNews = new KZ_Models_News();

                    // Get News Content
                    $arrNewsContent = $objModelNews->getNewsContent($arrAgendaItem['news_id'], $intStatus);
                    $arrNews = $objModelNews->getNewsByID($arrAgendaItem['news_id']);

                    // Check if News Content was found
                    if (isset($arrNewsContent) && is_array($arrNewsContent) && count($arrNewsContent) > 0) {

                        // Set Unserialized Content
                        $arrUnserializedContent = unserialize($arrNewsContent[0]['data']);

                        if (isset($arrUnserializedContent) && is_array($arrUnserializedContent) && count($arrUnserializedContent) > 0) {

                            foreach ($arrUnserializedContent as $strDataKey => $strDataValue) {

                                // Find Title
                                if (strstr($strDataKey, 'title')) {
                                    $arrAgendaItem['content']['title'] = $arrUnserializedContent[$strDataKey];
                                }

                                // Find Text
                                if (strstr($strDataKey, 'text')) {
                                    $arrAgendaItem['content']['text'] = KZ_View_Helper_Truncate::truncate($arrUnserializedContent[$strDataKey], 20, '', '/nieuws/' . $arrNews['nameSlug'] . '/');
                                }

                                $booAddAgenda = true;

                            }

                        }

                    }
                }

                $intMonth = (int)$this->view->date()->format($arrAgendaItem['date_start'], 'MM');

                // Check if Month wasn't set yet
                if (!isset($arrAgendaByMonth[$intMonth])) {
                    // Set empty Month array
                    $arrAgendaByMonth[$intMonth] = array();
                }

                $arrAgendaByMonth[$intMonth][] = $arrAgendaItem;

            }

        }

        $this->view->agenda = $arrAgendaByMonth;

    }

    public function socialAction()
    {

    }

    private function _setSeo($arrData)
    {

        // Check if custom SEO Title was set
        if (isset($arrData['seo_title']) && $arrData['seo_title'] !== false && !empty($arrData['seo_title'])) {
            $this->view->seo_title = $arrData['seo_title'];
        }

        // Check if custom SEO Description was set
        if (isset($arrData['seo_description']) && $arrData['seo_description'] !== false && !empty($arrData['seo_description'])) {
            $this->view->seo_description = $arrData['seo_description'];
        }

        // Check if custom SEO Keywords where set
        if (isset($arrData['seo_keywords']) && $arrData['seo_keywords'] !== false && !empty($arrData['seo_keywords'])) {
            $this->view->seo_keywords = $arrData['seo_keywords'];
        }

    }

}
