<?php
class Admin_BlogController extends KZ_Controller_Action
{
	public $objModelBlog;
	public $objTranslate;

	public function init()
	{
	    $this->view->format = new KZ_View_Helper_Format();
		$this->objModelBlog = new KZ_Models_Blog();
		$this->objTranslate = new KZ_View_Helper_Translate();

        // Parse Default States to View
        $this->view->states = [
            KZ_Controller_Action::STATE_INACTIVE => 'Inactive',
            KZ_Controller_Action::STATE_ACTIVE => 'Active',
        ];
	}

	public function indexAction()
	{

        // Set Defaults
        $arrDefaults = [
            'blog_id' => 1,
            'title' => '',
            'content' => '',
            'status' => KZ_Controller_Action::STATE_INACTIVE
        ];

        // Check if Post was set
        if($this->getRequest()->isPost()) {

            // Get All Params
            $arrPostParams = $this->_getAllParams();

            // Overwrite Defaults
            $arrDefaults = array_merge($arrDefaults,$arrPostParams);

            // Do form validation checks
            if(empty($arrDefaults['title'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
            } elseif(empty($arrDefaults['content'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select in a message');
            } elseif(empty($arrDefaults['status'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a status');
            } else {

                /**
                 * @todo make blog dynamic
                 */
                $arrBlogItemData = [
                    'blog_id' => 1,
                    'title' => $arrDefaults['title'],
                    'content' => htmlentities($arrDefaults['content']),
                    'status' => $arrDefaults['status']
                ];

                $intUpdateID = $this->objModelBlog->insertBlogItem($arrBlogItemData,$arrDefaults['id']);

                if(isset($intUpdateID) && is_numeric($intUpdateID)) {
                    $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully added blog item')));
                    $this->_redirect('/admin/blog/index/feedback/'.$strFeedback.'/#tab0');
                } else {
                    // Return feedback
                    $this->view->feedback = array('type' => 'error', 'message' => 'Something went wrong trying to add the blog item');
                }

            }

        }

        // Parse variables to View
		$this->view->blog = $this->objModelBlog->getBlog();
        $this->view->defaults = $arrDefaults;
	}
	
	public function editAction()
	{
		// Get All Params
		$arrParams = $this->_getAllParams();
		
		// Check if Param id is set
    	if(! isset($arrParams['id'])) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
    		$this->_redirect('/admin/blog/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
    	// Get Blog Item
    	$arrBlogItem = $this->objModelBlog->getBlogItemById($arrParams['id']);

    	// Check if Agenda wasn't found
    	if(isset($arrBlogItem) && count($arrBlogItem) <= 0) {
    		// return feedback
    		$strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find blog item')));
    		$this->_redirect('/admin/blog/index/feedback/'.$strSerializedFeedback.'/#tab0/');
    	}
    	
		// Set Defaults
		$arrDefaults = $arrBlogItem;
		
		// Check if Post was set
		if($this->getRequest()->isPost()) {

			// Get All Params
			$arrPostParams = $this->_getAllParams();

            // Overwrite Defaults
            $arrDefaults = array_merge($arrDefaults,$arrPostParams);

            // Do form validation checks
			if(empty($arrDefaults['title'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t fill in a title');
			} elseif(empty($arrDefaults['content'])) {
				$this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select in a message');
            } elseif(empty($arrDefaults['status'])) {
                $this->view->feedback = array('type' => 'error', 'message' => 'You didn\'t select a status');
			} else {

                $arrBlogItemData = [
                    'title' => $arrDefaults['title'],
                    'content' => htmlentities($arrDefaults['content']),
                    'status' => $arrDefaults['status']
                ];

                $intUpdateID = $this->objModelBlog->updateBlogItem($arrDefaults['id'],$arrBlogItemData);

                if(isset($intUpdateID) && is_numeric($intUpdateID)) {
                    $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully updated blog item')));
                    $this->_redirect('/admin/blog/index/feedback/'.$strFeedback.'/#tab0');
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
        if(! isset($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Missing param for id')));
            $this->_redirect('/admin/blog/index/feedback/'.$strSerializedFeedback.'/#tab0/');
        }

        // Get Blog Item
        $arrBlogItem = $this->objModelBlog->getBlogItemById($arrParams['id']);

        // Check if Agenda wasn't found
        if(isset($arrBlogItem) && count($arrBlogItem) <= 0) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(array('type' => 'error', 'message' => 'Unable to find blog item')));
            $this->_redirect('/admin/blog/index/feedback/'.$strSerializedFeedback.'/#tab0/');
        }

        // Set Defaults
        $arrDefaults = $arrBlogItem;

        // Check if Post was set
        if($this->getRequest()->isPost()) {

            $intDeleteID = $this->objModelBlog->deleteBlogItem($arrDefaults['id']);

            if(isset($intDeleteID) && is_numeric($intDeleteID)) {
                $strFeedback = base64_encode(serialize(array('type' => 'success', 'message' => 'Succesfully deleted blog item')));
                $this->_redirect('/admin/blog/index/feedback/'.$strFeedback.'/#tab0');
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
            $this->objTranslate->translate('Status'),
            $this->objTranslate->translate('Created'),
            $this->objTranslate->translate('Options')
        ];

    	// Set the DB Table Columns
    	$arrTableColums = [
    	    'id',
            'name',
            'title',
            'status',
            'created'
        ];

    	// Set the Search
    	$strSearchData			= null;
    	if(isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
    		$strSearchString		= $_POST['sSearch'];
    	
    		if(is_numeric($strSearchString)) {
    			$strSearchData		= "id LIKE '%".$strSearchString."%'";
    		} else {
    			$strSearchData		= "(name LIKE '%".$strSearchString."%' OR title LIKE '%".$strSearchString."%' )";
    		}
    	}
    	
    	// Set the Limit
    	$intResultsOnPage		= $this->_getParam('iDisplayLength');
    	$intStartNumber			= $this->_getParam('iDisplayStart');
    	$arrLimitData			= array('count' 	=> $intResultsOnPage,
    									'offset'	=> $intStartNumber);
    	
    	// Ordering
    	$arrOrderData					= array();
    	if(isset($_POST['iSortCol_0'])) {
    		$trsOrdering = "ORDER BY  ";
    		for($intI = 0; $intI < intval($_POST['iSortingCols']); $intI++ ) {
    			if($_POST['bSortable_'.intval($_POST['iSortCol_'.$intI])] == "true" ) {
    				$strSortColumns		= $arrTableColums[intval($_POST['iSortCol_'.$intI])];
    				$strSortDirection	= $_POST['sSortDir_'.$intI];
    				$arrOrderData[]		= $strSortColumns.' '.strtoupper($strSortDirection);
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
    	
    	if(!empty($arrBlog)) {
    		foreach($arrBlog as $key => $arrRow) {

    			$row = array();
    			for($i = 0; $i < count($arrColumns); $i++) {
    				if($arrColumns[$i] != ' ') {
    					
    					if(isset($arrTableColums[$i])) {
    						if($arrTableColums[$i] == 'date_start') {
    							if($arrRow['date_start'] != $arrRow['date_end']) {
    								$strRowData		= $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY').' / '.$this->view->date()->format($arrRow['date_end'], 'dd-MM-YYYY');
    							} else {
    								$strRowData		= $this->view->date()->format($arrRow['date_start'], 'dd-MM-YYYY');
    							}
    						}elseif($arrTableColums[$i] == 'status') {
    							$strRowData		= '<span class="tag '.(($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'green' : 'red').'">'.(($arrRow['status'] == KZ_Controller_Action::STATE_ACTIVE) ? 'active' : 'inactive').'</span>';
    						} elseif(in_array($arrTableColums[$i], array('created','lastmodified'))) {
    							$strRowData		= $this->view->date()->format($arrRow[$arrTableColums[$i]], 'dd-MM-YYYY HH:mm:ss');
    						} elseif($arrTableColums[$i] == 'user_id') {
    							$strRowData		= ((isset($this->view->users[$arrRow['user_id']]['name']) && ! empty($this->view->users[$arrRow['user_id']]['name'])) ? $this->view->users[$arrRow['user_id']]['name'] : '-');
    						} else {
    							$strRowData		= stripslashes($arrRow[$arrTableColums[$i]]);
    						}
    					} else {
    						
    						$strOptionsHtml = ' <ul class="actions">
													<li><a rel="tooltip" href="/admin/blog/edit/id/'.$arrRow['id'].'/" class="edit" original-title="'.$this->objTranslate->translate('Edit blog item').'">'.$this->objTranslate->translate('Edit blog item').'</a></li>
													<li><a rel="tooltip" href="/admin/blog/delete/id/'.$arrRow['id'].'/" class="delete" original-title="'.$this->objTranslate->translate('Delete blog item').'">'.$this->objTranslate->translate('Delete blog item').'</a></li>
												</ul>';

    						$strRowData		= $strOptionsHtml;
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
