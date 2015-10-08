<?php
class KZ_View_Helper_Guestbook extends Zend_View_Helper_Abstract
{

	public function guestbook($booIsFrontend = false) 
	{
		// Set the Model
		$objModelGuestbook		= new KZ_Models_Guestbook();
		
		// Set a limit when widget is used on FrontEnd
		$intItemsOnPage			= 20;
		$strPageNavigation		= '';
		$intItemsInNavigation	= 5;
		if($booIsFrontend === true) {
			$arrLimit				= array('count' 	=> 5, 
											'offset' 	=> 0);
		} else {
			$intPageNumber			= $this->view->GetParam('pagina');
			if(is_null($intPageNumber)) {
				$intPageNumber		= 1;
			}
			$arrLimit				= array('count' 	=> $intItemsOnPage,
											'offset' 	=> (($intPageNumber - 1) * $intItemsOnPage));
			
			
			// Get the Total amount
			$arrTotalEntries		= $objModelGuestbook->getTotalGuestbookEntries();
			$intTotalAmount			= $arrTotalEntries['total'];
			
			// Check if we need to create a page navigation
			if($intTotalAmount > $intItemsOnPage) {
				$intNumberOfPages		= ceil($intTotalAmount / $intItemsOnPage);
				$strPageNavigation		= '<ul class="paginator zapp">';
				
				// Add the First page link
				if($intNumberOfPages > 5) {
					$strPageNavigation		.= '<li><a href="/gastenboek/pagina/1/">&laquo; '.$this->view->translate('Eerste pagina').'</a>';
				}
				
				// Add the Previous Page navigation
				if($intPageNumber <= $intNumberOfPages && $intNumberOfPages > 1 && $intPageNumber > 1) {
					$strPageNavigation		.= '<li><a href="/gastenboek/pagina/'.($intPageNumber - 1).'"/>&lsaquo; '.$this->view->translate('Vorige pagina').'</a></li>';
				}
				
				// Set the Pages them selfs
				if($intPageNumber > 3) {
					$intStartPage		= ($intPageNumber - 2);
					$intEndPage			= ((($intPageNumber + 2) > $intNumberOfPages) ? $intNumberOfPages : ($intPageNumber + 2));
					for($intI = $intStartPage; $intI <= $intEndPage; $intI++) {
						$strPageNavigation	.= '<li class="number'.(($intPageNumber == $intI) ? ' active' : '').'"><a href="/gastenboek/pagina/'.$intI.'"/>'.$intI.'</a></li>';
					}
				} else {
					$intEndPage				= (($intNumberOfPages < $intItemsInNavigation) ? $intNumberOfPages : $intItemsInNavigation);
					for($intI = 1; $intI <= $intEndPage; $intI++) {
						$strPageNavigation	.= '<li class="number'.(($intPageNumber == $intI) ? ' active' : '').'"><a href="/gastenboek/pagina/'.$intI.'"/>'.$intI.'</a></li>';
					}
				}
				
				// Add the Next Page navigation
				if($intPageNumber < $intNumberOfPages && $intNumberOfPages > 1) {
					$strPageNavigation		.= '<li><a href="/gastenboek/pagina/'.($intPageNumber + 1).'"/>'.$this->view->translate('Volgende pagina').' &rsaquo;</a></li>';
				}
				
				// Add the Last page link
				if($intNumberOfPages > 5 && $intPageNumber < $intNumberOfPages) {
					$strPageNavigation		.= '<li><a href="/gastenboek/pagina/'.$intNumberOfPages.'/">'.$this->view->translate('Laatste pagina').' &raquo;</a>';
				}
				$strPageNavigation		.= '</ul>';
			}
		}


		// Get the Results
		$arrGuestbookData		= $objModelGuestbook->getGuestbookEntries($arrLimit);
		
		if(!empty($arrGuestbookData) && is_array($arrGuestbookData)) {
			
			echo $strPageNavigation.'<ol class="guestbook">';
			foreach($arrGuestbookData as $intGuestbookKey => $arrGuestbookValues) {
				
				$strGuestbookMessage 		= $arrGuestbookValues['guestbook_message'];
				
				if($booIsFrontend === true) {
					if(strlen($strGuestbookMessage) > 125) {
						$strGuestbookMessage	= substr($strGuestbookMessage, 0, 125).'...';
					}
				}
				
				if($this->view->getParam('controller') == 'gastenboek') {
					$strGuestbookMessage = nl2br(nl2br($strGuestbookMessage));
				}
				
				echo
				'<li>
					<ul>
						<li class="color_kz_yellow poster"><span class="name">'.stripslashes($arrGuestbookValues['guestbook_name']).'</span> <span class="date">'.$this->view->date()->format($arrGuestbookValues['guestbook_entry_date'], 'dd').' '.$this->view->date()->getMonth($this->view->date()->format($arrGuestbookValues['guestbook_entry_date'], 'M')).', '.$this->view->date()->format($arrGuestbookValues['guestbook_entry_date'], 'yyyy').' '.$this->view->date()->format($arrGuestbookValues['guestbook_entry_date'], 'HH:mm').'</span></li>
						<li>'.stripslashes($strGuestbookMessage).'</li>
					</ul>
				</li>';
			}
			echo '</ol>'.$strPageNavigation;
			
		} else {
			echo 'Er zijn nog geen berichten';
		}
	}

}
