<?php

class KZ_View_Helper_News extends Zend_View_Helper_Abstract
{

    const NEWS_CATEGORIES_STATUS__ACTIVE = 1;

    public function news($booIsMobile = false)
    {
        $objConfig = Zend_Registry::get('Zend_Config');

        $objRequest = Zend_Controller_Front::getInstance()->getRequest();

        $strControllerName = $objRequest->getParam('controller');
        $strActionName = $objRequest->getParam('action');
        $strTitle = $objRequest->getParam('title');

        if ('page' === $strControllerName && 'index' === $strActionName) {
            $objModelNews = new KZ_Models_News();
            $objModelAgenda = new KZ_Models_Agenda();
            $objModelCategories = new KZ_Models_Categories();
            $objModelSettings = new KZ_Models_Settings();
            $objModelPage = new KZ_Models_Pages();
            $objModelBlog = new KZ_Models_Blog();

            // Set Defaults
            $intCategoryID = false;
            $arrCategory = false;
            $booLoadBlogItems = true;

            if ('home' !== $strTitleSlug = $objRequest->getParam('title')) {
                $booLoadBlogItems = false;
                $arrCategory = $objModelCategories->getCategoryBySlug($strTitleSlug);
                $intCategoryID = (false === empty($arrCategory) && true === is_array($arrCategory)) ? $arrCategory['category_id'] : false;
            }

            // Get News items
            $arrNewsItems = $objModelNews->getNews($arrContentTypeID = [1, 2,], $intCategoryID, $intStatus = 1, false, $intLimit = $objModelNews->resultsCount);
            $arrOrderedNews = $this->orderByDateAndTime($arrNewsItems);

            if (true === $booLoadBlogItems) {
                // Combine News And Blog items
                $arrOrderedNews = $this->orderByDateAndTime($objModelBlog->getLatestBlogItems($intLimit), 'blog', $arrOrderedNews);
            }

            // Set Current Date
            $objDate = new Zend_Date();
            $strDate = $objDate->toString('yyyy-MM-dd');

            // Get Agenda items
            $arrAgendaItems = $objModelAgenda->getAgenda($strDate, $intStatus, 8);

            // Get News Categories
            $arrCategories = $objModelCategories->getCategories('category_id');

            // Get Topstory
            $arrTopstory = $objModelSettings->getSettingsByKey('topstory');

            // Check if Topstory was found
            if (false === empty($arrTopstory) && true === is_array($arrTopstory)) {
                $arrTopstory = $objModelPage->getPageContent($arrTopstory['value']);
            }

            return $this->view->partial('snippets/news.phtml', [
                'news' => array_slice($arrOrderedNews, 0, $intLimit),
                'agenda' => $arrAgendaItems,
                'category' => $arrCategory,
                'categories' => $arrCategories,
                'topstory' => $arrTopstory,
                'news_categories' => $objModelNews->getNewsCategories(static::NEWS_CATEGORIES_STATUS__ACTIVE),
                'title' => $objModelNews->getNewsCategories($intStatus)
            ]);

        }

    }

    /**
     * @param $arrData
     * @param null $strType
     * @param null $arrReturn
     * @return array|null
     * @throws Zend_Date_Exception
     */
    public static function orderByDateAndTime($arrData, $strType = null, $arrReturn = null)
    {
        if (null === $arrReturn) {
            $arrReturn = [];
        }

        if (false === empty($arrData) && true === is_array($arrData)) {
            $objDate = new Zend_Date();
            foreach ($arrData as $arrDataRow) {
                switch ($strType) {
                    case 'blog':
                        $objDate->set($arrDataRow['created'], 'yyyy-MM-dd HH:mm:ss');
                        break;
                    case 'news':
                    default:
                        $objDate->setDate($arrDataRow['date'], 'yyyy-MM-dd');
                        $objDate->setTime($arrDataRow['time'], 'HH');
                        break;
                }
                $intTimeStamp = (int)$objDate->toValue();
                $arrReturn[$intTimeStamp] = $arrDataRow;
            }
        }
        krsort($arrReturn);
        return $arrReturn;
    }

}