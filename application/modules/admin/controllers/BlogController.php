<?php

class Admin_BlogController extends KZ_Controller_Action
{
    public $objModelBlog;
    public $objTranslate;
    public $user;
    public $bloggers;

    public function init()
    {

        // Get The User
        $objNamespace = new Zend_Session_Namespace('KZ_Admin');
        $this->user = $objNamespace->user;

        $this->view->format = new KZ_View_Helper_Format();
        $this->objModelBlog = new KZ_Models_Blog();
        $this->objTranslate = new KZ_View_Helper_Translate();

        $this->bloggers = $this->objModelBlog->getBloggers();

        // Parse Default States to View
        $this->view->states = [
            KZ_Controller_Action::STATE_INACTIVE => 'Inactive',
            KZ_Controller_Action::STATE_ACTIVE => 'Active',
        ];

        // Parse Editor to View
        $this->view->editorInit = KZ_Controller_Editor::setEditor('tinymce');
    }


    /** BLOG ITEMS */
    public function indexAction()
    {

        // Set Defaults
        $arrDefaults = [
            'blogger_id' => $this->user['blogger_id'],
            'title' => '',
            'content' => '',
            'status' => KZ_Controller_Action::STATE_INACTIVE
        ];

        // Check if Post was set
        if ($this->getRequest()->isPost()) {

            // Get All Params
            $arrPostParams = $this->_getAllParams();

            // Overwrite Defaults
            $arrDefaults = array_merge($arrDefaults, $arrPostParams);

            // Do form validation checks
            if (empty($arrDefaults['title'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
            } elseif (empty($arrDefaults['content'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select in a message');
            } elseif (empty($arrDefaults['status'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a status');
            } else {

                $arrBlogItemData = [
                    'blogger_id' => $arrDefaults['blogger_id'],
                    'title' => $title = $arrDefaults['title'],
                    'slug' => KZ_Controller_Action_Helper_Slug::slug($title),
                    'content' => htmlentities($arrDefaults['content']),
                    'status' => $arrDefaults['status'],
                ];

                $intInsertId = $this->objModelBlog->insertBlogItem($arrBlogItemData);

                if (isset($intInsertId) && is_numeric($intInsertId)) {
                    $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added blog item')));
                    $this->_redirect('/admin/blog/index/feedback/' . $strFeedback . '/#tab0');
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the blog item');
                }

            }

        }

        // Parse variables to View
        $this->view->defaults = $arrDefaults;
        $this->view->bloggers = $this->bloggers;
        $this->view->isBlogger = null !== $this->user['blogger_id'];
    }

    public function editAction()
    {
        // Get All Params
        $arrParams = $this->_getAllParams();

        // Check if Param id is set
        if (!isset($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
            $this->_redirect('/admin/blog/index/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Get Blog Item Ids for current User and check for a match for security
        if (false === empty($bloggerId = $this->user['blogger_id']) && null !== $bloggerId) {
            $arrBlogItemIds = $this->objModelBlog->getBlogItemIdsByBloggerId($bloggerId);
            if (false === in_array($arrParams['id'], $arrBlogItemIds)) {
                header("Location: /admin/blog/");
                exit;
            }
        }

        // Get Blog Item
        $arrBlogItem = $this->objModelBlog->getBlogItemById($arrParams['id']);

        // Check if Blog item wasn't found
        if (isset($arrBlogItem) && count($arrBlogItem) <= 0) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find blog item')));
            $this->_redirect('/admin/blog/index/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Set Defaults
        $arrDefaults = $arrBlogItem;

        // Check if Post was set
        if ($this->getRequest()->isPost()) {

            // Get All Params
            $arrPostParams = $this->_getAllParams();

            // Overwrite Defaults
            $arrDefaults = array_merge($arrDefaults, $arrPostParams);

            // Do form validation checks
            if (empty($arrDefaults['title'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
            } elseif (empty($arrDefaults['content'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select in a message');
            } elseif (empty($arrDefaults['status'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a status');
            } else {

                $arrBlogItemData = [
                    'title' => $title = $arrDefaults['title'],
                    'slug' => KZ_Controller_Action_Helper_Slug::slug($title),
                    'content' => htmlentities($arrDefaults['content']),
                    'status' => $arrDefaults['status'],
                ];

                $intUpdateID = $this->objModelBlog->updateBlogItem($arrDefaults['id'], $arrBlogItemData);

                if (isset($intUpdateID) && is_numeric($intUpdateID)) {
                    $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated blog item')));
                    $this->_redirect('/admin/blog/index/feedback/' . $strFeedback . '/#tab0');
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the blog item');
                }

            }

        }

        // Parse variables to View
        $this->view->defaults = $arrDefaults;

    }

    public function deleteAction()
    {
        // Get All Params
        $arrParams = $this->_getAllParams();

        // Check if Param id is set
        if (!isset($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
            $this->_redirect('/admin/blog/index/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Get Blog Item Ids for current User and check for a match for security
        if (!empty($this->user['blogger_id']) && !is_null($this->user['blogger_id'])) {
            $arrBlogItemIds = $this->objModelBlog->getBlogItemIdsByBloggerId($this->user['blogger_id']);
            if (!in_array($arrParams['id'], $arrBlogItemIds)) {
                header("Location: /admin/blog/");
                exit;
            }
        }

        // Get Blog Item
        $arrBlogItem = $this->objModelBlog->getBlogItemById($arrParams['id']);

        // Check if Blog item wasn't found
        if (isset($arrBlogItem) && count($arrBlogItem) <= 0) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find blog item')));
            $this->_redirect('/admin/blog/index/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Set Defaults
        $arrDefaults = $arrBlogItem;

        // Check if Post was set
        if ($this->getRequest()->isPost()) {

            $intDeleteID = $this->objModelBlog->deleteBlogItem($arrDefaults['id']);

            if (isset($intDeleteID) && is_numeric($intDeleteID)) {
                $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted blog item')));
                $this->_redirect('/admin/blog/index/feedback/' . $strFeedback . '/#tab0');
            } else {
                // Return feedback
                $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the blog item');
            }

        }

        // Parse variables to View
        $this->view->defaults = $arrDefaults;
    }

    /**
     * Function for generating the Blog items table
     * Used for the AJAX call for the Datatable
     */
    public function generateitemsdatatableAction()
    {
        // Disable Layout and View
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Set the Columns
        $arrColumns = [
            $this->objTranslate->translate('ID'),
            $this->objTranslate->translate('Name'),
            $this->objTranslate->translate('Title'),
            $this->objTranslate->translate('Facebook'),
            $this->objTranslate->translate('Twitter'),
            $this->objTranslate->translate('Status'),
            $this->objTranslate->translate('Created'),
            $this->objTranslate->translate('Options')
        ];

        // Set the DB Table Columns
        $arrTableColums = [
            'id',
            'name',
            'title',
            'shared_facebook',
            'shared_twitter',
            'status',
            'created'
        ];

        // Set the Search
        $strSearchData = null;
        if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
            $strSearchString = $_POST['sSearch'];

            if (is_numeric($strSearchString)) {
                $strSearchData = "id LIKE '%" . $strSearchString . "%'";
            } else {
                $strSearchData = "(name LIKE '%" . $strSearchString . "%' OR title LIKE '%" . $strSearchString . "%' )";
            }
        }

        // Set the Limit
        $intResultsOnPage = $this->_getParam('iDisplayLength');
        $intStartNumber = $this->_getParam('iDisplayStart');
        $arrLimitData = array('count' => $intResultsOnPage,
            'offset' => $intStartNumber);

        // Ordering
        $arrOrderData = array();
        if (isset($_POST['iSortCol_0'])) {
            $trsOrdering = "ORDER BY  ";
            for ($intI = 0; $intI < intval($_POST['iSortingCols']); $intI++) {
                if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $intI])] == "true") {
                    $strSortColumns = $arrTableColums[intval($_POST['iSortCol_' . $intI])];
                    $strSortDirection = $_POST['sSortDir_' . $intI];
                    $arrOrderData[] = $strSortColumns . ' ' . strtoupper($strSortDirection);
                }
            }
        }

        // Get the Totals
        $intTotal = $this->objModelBlog->getBlogItemsForTable(true);

        // Select all Blog items
        $objBlog = $this->objModelBlog->getBlogItemsForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
        $arrBlog = $objBlog->toArray();

        // Create the JSON Data
        $output = array("sEcho" => ((isset($_POST['sEcho'])) ? intval($_POST['sEcho']) : 0),
            "iTotalRecords" => $intTotal,
            "iTotalDisplayRecords" => $intResultsOnPage,
            "aaData" => []
        );

        if (!empty($arrBlog)) {
            foreach ($arrBlog as $key => $arrRow) {

                $row = array();
                for ($i = 0; $i < count($arrColumns); $i++) {
                    if ($arrColumns[$i] != ' ') {

                        if (isset($arrTableColums[$i])) {
                            if ($arrTableColums[$i] == 'date_start') {
                                if ($arrRow['date_start'] != $arrRow['date_end']) {
                                    $strRowData = $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY') . ' / ' . $this->view->date()->format($arrRow['date_end'], 'dd-MM-YYYY');
                                } else {
                                    $strRowData = $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY');
                                }
                            } elseif ($arrTableColums[$i] == 'status') {
                                $strRowData = '<span class="tag ' . (($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'green' : 'red') . '">' . (($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'active' : 'inactive') . '</span>';
                            } elseif (in_array($arrTableColums[$i], array('created', 'lastmodified'))) {
                                $strRowData = $this->view->date()->format($arrRow[$arrTableColums[$i]], 'dd-MM-YYYY HH:mm:ss');
                            } elseif (strpos($arrTableColums[$i], 'shared_') !== false) {
                                $strRowData = '<span style="cursor:pointer;" class="js-share tag ' . (($arrRow[$arrTableColums[$i]] == KZ_Controller_Action::STATE_ACTIVE) ? 'green' : 'red') . '" js-share" data-share-social="' . str_replace('shared_', '', $arrTableColums[$i]) . '" data-share-type="blog" data-share-id="' . $arrRow['id'] . '">' . (($arrRow[$arrTableColums[$i]] == KZ_Controller_Action::STATE_ACTIVE) ? 'shared' : 'unshared') . '</span>';
                            } elseif ($arrTableColums[$i] == 'user_id') {
                                $strRowData = ((isset($this->view->users[$arrRow['user_id']]['name']) && !empty($this->view->users[$arrRow['user_id']]['name'])) ? $this->view->users[$arrRow['user_id']]['name'] : '-');
                            } else {
                                $strRowData = stripslashes($arrRow[$arrTableColums[$i]]);
                            }
                        } else {

                            $strOptionsHtml = ' <ul class="actions">
													<li><a rel="tooltip" href="/admin/blog/edit/id/' . $arrRow['id'] . '/" class="edit" original-title="' . $this->objTranslate->translate('Edit blog item') . '">' . $this->objTranslate->translate('Edit blog item') . '</a></li>
													<li><a rel="tooltip" href="/admin/blog/delete/id/' . $arrRow['id'] . '/" class="delete" original-title="' . $this->objTranslate->translate('Delete blog item') . '">' . $this->objTranslate->translate('Delete blog item') . '</a></li>
												</ul>';

                            $strRowData = $strOptionsHtml;
                        }
                        $row[] = $strRowData;
                    }
                }

                $output['aaData'][] = $row;
            }
        }

        // Send the Output
        echo json_encode($output);

    }


    /** BLOGGERS */
    public function bloggersAction()
    {

        // Set Defaults
        $arrDefaults = [
            'name' => '',
            'slug' => '',
            'photo' => '',
            'status' => KZ_Controller_Action::STATE_INACTIVE
        ];

        // Check if Post was set
        if ($this->getRequest()->isPost()) {

            // Get All Params
            $arrPostParams = $this->_getAllParams();

            // Overwrite Defaults
            $arrDefaults = array_merge($arrDefaults, $arrPostParams);

            // Do form validation checks
            if (empty($arrDefaults['name'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
            } elseif (empty($arrDefaults['photo'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
            } elseif (empty($arrDefaults['status'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a status');
            } else {

                $arrBlogger = [
                    'name' => $arrDefaults['name'],
                    'slug' => KZ_Controller_Action_Helper_Slug::slug($arrDefaults['name']),
                    'photo' => $arrDefaults['photo'],
                    'status' => $arrDefaults['status'],
                ];

                $intInsertId = $this->objModelBlog->insertBlogger($arrBlogger);

                if (isset($intInsertId) && is_numeric($intInsertId)) {
                    $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added blogger')));
                    $this->_redirect('/admin/blog/bloggers/feedback/' . $strFeedback . '/#tab0');
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the blogger');
                }

            }

        }

        // Parse variables to View
        $this->view->defaults = $arrDefaults;
    }

    public function bloggereditAction()
    {
        // Get All Params
        $arrParams = $this->_getAllParams();

        // Check if Param id is set
        if (!isset($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
            $this->_redirect('/admin/blog/bloggers/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Get Blogger
        $arrBlogger = $this->objModelBlog->getBloggerById($arrParams['id']);

        // Check if Blogger wasn't found
        if (isset($arrBlogger) && count($arrBlogger) <= 0) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find blogger')));
            $this->_redirect('/admin/blog/bloggers/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Set Defaults
        $arrDefaults = $arrBlogger;

        // Check if Post was set
        if ($this->getRequest()->isPost()) {

            // Get All Params
            $arrPostParams = $this->_getAllParams();

            // Overwrite Defaults
            $arrDefaults = array_merge($arrDefaults, $arrPostParams);

            // Do form validation checks
            if (empty($arrDefaults['name'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a name');
            } elseif (empty($arrDefaults['photo'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select an image');
            } elseif (empty($arrDefaults['status'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a status');
            } else {

                $arrBlogger = [
                    'name' => $arrDefaults['name'],
                    'photo' => $arrDefaults['photo'],
                    'status' => $arrDefaults['status'],
                ];

                $intUpdateID = $this->objModelBlog->updateBlogger($arrDefaults['id'], $arrBlogger);

                if (isset($intUpdateID) && is_numeric($intUpdateID)) {
                    $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated blogger')));
                    $this->_redirect('/admin/blog/bloggers/feedback/' . $strFeedback . '/#tab0');
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the blogger');
                }

            }

        }

        // Parse variables to View
        $this->view->defaults = $arrDefaults;

    }

    public function bloggerdeleteAction()
    {
        // Get All Params
        $arrParams = $this->_getAllParams();

        // Check if Param id is set
        if (!isset($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
            $this->_redirect('/admin/blog/bloggers/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Get Blogger
        $arrBlogger = $this->objModelBlog->getBloggerById($arrParams['id']);

        // Check if Blogger wasn't found
        if (isset($arrBlogger) && count($arrBlogger) <= 0) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find blogger')));
            $this->_redirect('/admin/blog/bloggers/feedback/' . $strSerializedFeedback . '/#tab0/');
        }

        // Set Defaults
        $arrDefaults = $arrBlogger;

        // Check if Post was set
        if ($this->getRequest()->isPost()) {

            $intDeleteID = $this->objModelBlog->deleteBlogger($arrDefaults['id']);

            if (isset($intDeleteID) && is_numeric($intDeleteID)) {
                $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted blogger')));
                $this->_redirect('/admin/blog/index/feedback/' . $strFeedback . '/#tab0');
            } else {
                // Return feedback
                $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to delete the blogger');
            }

        }

        // Parse variables to View
        $this->view->defaults = $arrDefaults;
    }

    /**
     * Function for generating the Bloggers table
     * Used for the AJAX call for the Datatable
     */
    public function generatebloggersdatatableAction()
    {
        // Disable Layout and View
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Set the Columns
        $arrColumns = [
            $this->objTranslate->translate('ID'),
            $this->objTranslate->translate('Name'),
            $this->objTranslate->translate('Status'),
            $this->objTranslate->translate('Options')
        ];

        // Set the DB Table Columns
        $arrTableColums = [
            'id',
            'name',
            'status'
        ];

        // Set the Search
        $strSearchData = null;
        if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
            $strSearchString = $_POST['sSearch'];

            if (is_numeric($strSearchString)) {
                $strSearchData = "id LIKE '%" . $strSearchString . "%'";
            } else {
                $strSearchData = "(name LIKE '%" . $strSearchString . "%' )";
            }
        }

        // Set the Limit
        $intResultsOnPage = $this->_getParam('iDisplayLength');
        $intStartNumber = $this->_getParam('iDisplayStart');
        $arrLimitData = array('count' => $intResultsOnPage,
            'offset' => $intStartNumber);

        // Ordering
        $arrOrderData = array();
        if (isset($_POST['iSortCol_0'])) {
            $trsOrdering = "ORDER BY  ";
            for ($intI = 0; $intI < intval($_POST['iSortingCols']); $intI++) {
                if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $intI])] == "true") {
                    $strSortColumns = $arrTableColums[intval($_POST['iSortCol_' . $intI])];
                    $strSortDirection = $_POST['sSortDir_' . $intI];
                    $arrOrderData[] = $strSortColumns . ' ' . strtoupper($strSortDirection);
                }
            }
        }

        // Get the Totals
        $intTotal = $this->objModelBlog->getBloggersForTable(true);

        // Select all Blog items
        $objBlog = $this->objModelBlog->getBloggersForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
        $arrBlog = $objBlog->toArray();

        // Create the JSON Data
        $output = array("sEcho" => ((isset($_POST['sEcho'])) ? intval($_POST['sEcho']) : 0),
            "iTotalRecords" => $intTotal,
            "iTotalDisplayRecords" => $intResultsOnPage,
            "aaData" => []
        );

        if (!empty($arrBlog)) {
            foreach ($arrBlog as $key => $arrRow) {

                $row = array();
                for ($i = 0; $i < count($arrColumns); $i++) {
                    if ($arrColumns[$i] != ' ') {

                        if (isset($arrTableColums[$i])) {
                            if ($arrTableColums[$i] == 'date_start') {
                                if ($arrRow['date_start'] != $arrRow['date_end']) {
                                    $strRowData = $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY') . ' / ' . $this->view->date()->format($arrRow['date_end'], 'dd-MM-YYYY');
                                } else {
                                    $strRowData = $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY');
                                }
                            } elseif ($arrTableColums[$i] == 'status') {
                                $strRowData = '<span class="tag ' . (($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'green' : 'red') . '">' . (($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'active' : 'inactive') . '</span>';
                            } elseif (in_array($arrTableColums[$i], array('created', 'lastmodified'))) {
                                $strRowData = $this->view->date()->format($arrRow[$arrTableColums[$i]], 'dd-MM-YYYY HH:mm:ss');
                            } elseif ($arrTableColums[$i] == 'user_id') {
                                $strRowData = ((isset($this->view->users[$arrRow['user_id']]['name']) && !empty($this->view->users[$arrRow['user_id']]['name'])) ? $this->view->users[$arrRow['user_id']]['name'] : '-');
                            } else {
                                $strRowData = stripslashes($arrRow[$arrTableColums[$i]]);
                            }
                        } else {

                            $strOptionsHtml = ' <ul class="actions">
													<li><a rel="tooltip" href="/admin/blog/bloggeredit/id/' . $arrRow['id'] . '/" class="edit" original-title="' . $this->objTranslate->translate('Edit blogger') . '">' . $this->objTranslate->translate('Edit blogger') . '</a></li>
													<li><a rel="tooltip" href="/admin/blog/bloggerdelete/id/' . $arrRow['id'] . '/" class="delete" original-title="' . $this->objTranslate->translate('Delete blogger') . '">' . $this->objTranslate->translate('Delete blogger') . '</a></li>
												</ul>';

                            $strRowData = $strOptionsHtml;
                        }
                        $row[] = $strRowData;
                    }
                }

                $output['aaData'][] = $row;
            }
        }

        // Send the Output
        echo json_encode($output);

    }

    public function reactionsAction()
    {
    }

    public function reactioneditAction()
    {
        // Get All Params
        $arrParams = $this->_getAllParams();

        // Check if Param id is set
        if (!isset($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
            $this->_redirect('/admin/blog/reactions/feedback/' . $strSerializedFeedback . '/');
        }

        // Get Reaction
        $arrReaction = $this->objModelBlog->getReactionById($arrParams['id']);

        // Check if Blogger wasn't found
        if (isset($arrBlogger) && count($arrBlogger) <= 0) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find reaction')));
            $this->_redirect('/admin/blog/reactions/feedback/' . $strSerializedFeedback . '/');
        }

        // Set Defaults
        $arrDefaults = $arrReaction;

        // Check if Post was set
        if ($this->getRequest()->isPost()) {

            // Get All Params
            $arrPostParams = $this->_getAllParams();

            // Overwrite Defaults
            $arrDefaults = array_merge($arrDefaults, $arrPostParams);

            // Do form validation checks
            if (empty($arrDefaults['status'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a status');
            } else {

                $arrReaction = [
                    'status' => $arrDefaults['status']
                ];

                $intUpdateID = $this->objModelBlog->updateReaction($arrDefaults['id'], $arrReaction);

                if (isset($intUpdateID) && is_numeric($intUpdateID)) {
                    $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated reaction')));
                    $this->_redirect('/admin/blog/reactions/feedback/' . $strFeedback . '/');
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to update the reaction');
                }

            }

        }

        // Parse variables to View
        $this->view->defaults = $arrDefaults;

    }

    /**
     * Function for generating the Blog items table
     * Used for the AJAX call for the Datatable
     */
    public function generatereactionsdatatableAction()
    {
        // Disable Layout and View
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Set the Columns
        $arrColumns = [
            $this->objTranslate->translate('ID'),
            $this->objTranslate->translate('Reaction'),
            $this->objTranslate->translate('Name'),
            $this->objTranslate->translate('Status'),
            $this->objTranslate->translate('Created'),
            $this->objTranslate->translate('Options')
        ];

        // Set the DB Table Columns
        $arrTableColums = [
            'id',
            'reaction',
            'name',
            'status',
            'created'
        ];

        // Set the Search
        $strSearchData = null;
        if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
            $strSearchString = $_POST['sSearch'];

            if (is_numeric($strSearchString)) {
                $strSearchData = "id LIKE '%" . $strSearchString . "%'";
            } else {
                $strSearchData = "(reaction LIKE '%" . $strSearchString . "%', members.name LIKE '%" . $strSearchString . "%' )";
            }
        }

        // Set the Limit
        $intResultsOnPage = $this->_getParam('iDisplayLength');
        $intStartNumber = $this->_getParam('iDisplayStart');
        $arrLimitData = array(
            'count' => $intResultsOnPage,
            'offset' => $intStartNumber
        );

        // Ordering
        $arrOrderData = array();
        if (isset($_POST['iSortCol_0'])) {
            $trsOrdering = "ORDER BY  ";
            for ($intI = 0; $intI < intval($_POST['iSortingCols']); $intI++) {
                if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $intI])] == "true") {
                    $strSortColumns = $arrTableColums[intval($_POST['iSortCol_' . $intI])];
                    $strSortDirection = $_POST['sSortDir_' . $intI];
                    $arrOrderData[] = $strSortColumns . ' ' . strtoupper($strSortDirection);
                }
            }
        }

        // Get the Totals
        $intTotal = $this->objModelBlog->getBlogReactionsForTable(true);

        // Select all Blog items
        $objBlog = $this->objModelBlog->getBlogReactionsForTable(false, $arrLimitData, $strSearchData, $arrOrderData);
        $arrBlog = $objBlog->toArray();

        // Create the JSON Data
        $output = array("sEcho" => ((isset($_POST['sEcho'])) ? intval($_POST['sEcho']) : 0),
            "iTotalRecords" => $intTotal,
            "iTotalDisplayRecords" => $intResultsOnPage,
            "aaData" => []
        );

        if (!empty($arrBlog)) {
            foreach ($arrBlog as $key => $arrRow) {

                $row = array();
                for ($i = 0; $i < count($arrColumns); $i++) {
                    if ($arrColumns[$i] != ' ') {

                        if (isset($arrTableColums[$i])) {
                            if ($arrTableColums[$i] == 'date_start') {
                                if ($arrRow['date_start'] != $arrRow['date_end']) {
                                    $strRowData = $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY') . ' / ' . $this->view->date()->format($arrRow['date_end'], 'dd-MM-YYYY');
                                } else {
                                    $strRowData = $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY');
                                }
                            } elseif ($arrTableColums[$i] == 'status') {
                                $strRowData = '<span class="tag ' . (($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'green' : 'red') . '">' . (($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'active' : 'inactive') . '</span>';
                            } elseif (in_array($arrTableColums[$i], array('created', 'lastmodified'))) {
                                $strRowData = $this->view->date()->format($arrRow[$arrTableColums[$i]], 'dd-MM-YYYY HH:mm:ss');
                            } elseif ($arrTableColums[$i] == 'reaction') {
                                $strRowData = substr($arrRow[$arrTableColums[$i]], 0, 50) . ((strlen($arrRow[$arrTableColums[$i]]) > 50) ? '..' : '');
                            } elseif ($arrTableColums[$i] == 'user_id') {
                                $strRowData = ((isset($this->view->users[$arrRow['user_id']]['name']) && !empty($this->view->users[$arrRow['user_id']]['name'])) ? $this->view->users[$arrRow['user_id']]['name'] : '-');
                            } else {
                                $strRowData = stripslashes($arrRow[$arrTableColums[$i]]);
                            }
                        } else {

                            $strOptionsHtml = ' <ul class="actions">
													<li><a rel="tooltip" href="/admin/blog/reactionedit/id/' . $arrRow['id'] . '/" class="edit" original-title="' . $this->objTranslate->translate('Edit reaction') . '">' . $this->objTranslate->translate('Edit reaction') . '</a></li>
												</ul>';

                            $strRowData = $strOptionsHtml;
                        }
                        $row[] = $strRowData;
                    }
                }

                $output['aaData'][] = $row;
            }
        }

        // Send the Output
        echo json_encode($output);

    }

}
