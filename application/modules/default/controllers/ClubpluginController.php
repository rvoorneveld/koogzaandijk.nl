<?php
class ClubpluginController extends KZ_Controller_Action
{
	public $year;
	public $week;
	public $year_current;
	public $week_current;
	public $week_total;
	public $week_previous_total;
	public $filter;
	
	public function init()
	{
		// Set Date Object
		$objCurrentDate			= new Zend_Date();
		
		// Set Year
		$this->year_current		= (int)$objCurrentDate->get(Zend_Date::YEAR_8601);

		// Set Week
		$this->week_current		= (int)$objCurrentDate->toString('w');

		// Get All Params
		$arrParams				= $this->_getAllParams();

		// Set Defaults
		$this->filter_week		= false;
		$this->filter_year		= false;
		
		// Check if Week was set
		if(
				isset($arrParams['week']) 
			&& 	! empty($arrParams['week']) 
			&& 	is_numeric($arrParams['week'])
			&& 	$arrParams['week'] > 0
		) {
			$this->filter_week	= true;
			$this->week 		= (int)$arrParams['week'];
		} else {
			if($arrParams['action'] == 'results') {
				$this->week = $this->week_current - 1;
			} else {
				$this->week = $this->week_current;
			}
		}
		
		// Check if Year was set
		if(
				isset($arrParams['year']) 
			&& 	! empty($arrParams['year']) 
			&& 	is_numeric($arrParams['year'])
			&& 	$arrParams['year'] > 0
			&&	strlen($arrParams['year']) == 4
		) {
			$this->filter_year		= true;
			$this->year 			= (int)$arrParams['year'];
		} else {
			$this->year = $this->year_current;
		}
		
		// Get Total weeks for current year
		$objDate = new Zend_Date();
		$objDate->setYear($this->year);
		$objDate->setMonth(12);
		$objDate->setDay(31);

		$this->week_total	= 	(int)$objDate->get('w');

		if($this->week_total == 1) {
			$objDate = new Zend_Date();
			$objDate->setDay(24);
			$objDate->setMonth(12);
			$objDate->setYear($this->year);

			$this->week_total	= 	(int)$objDate->get('w');
		}
		
		// Get Total weeks for previous year
		$objDate = new Zend_Date();
		$objDate->setDay(31);
		$objDate->setMonth(12);
		$objDate->setYear($this->year - 1);
		
		$this->week_previous_total	= 	(int)$objDate->get('w');
		
		if($this->week_previous_total == 1) {
			$objDate = new Zend_Date();
			$objDate->setDay(24);
			$objDate->setMonth(12);
			$objDate->setYear($this->year - 1);
			
			$this->week_previous_total	= 	(int)$objDate->get('w');
		}

	}
	
	public function programAction()
	{
		$arrMatches = (new KZ_Models_Matches())->getMatches($this->year, $this->week, false, true, false, true);
		$arrLatestNews = ($objModelNews = new KZ_Models_News())->getLatestNews($objModelNews->resultsCount);
		
		// Set Previous and Next Week
		$intWeekNext			= $this->week + 1;
		$intWeekPrevious		= $this->week - 1;
		
		// Set Previous and Next Year
		$intYearNext			= $this->year;
		$intYearPrevious		= $this->year;
		
		// Don't show previous link when in current week and current year
		if(
				$this->week <= $this->week_current
			&&	$this->year	<= $this->year_current
		) {
			$intWeekPrevious = false;
			$intYearPrevious = false;
		}
		
		// Add one year when last week in current year was matched
		if(
			$this->week == $this->week_total
		) {
			$intWeekNext		= 1;
			$intYearNext		= $this->year + 1;
		}
		
		if(
			$this->week == 1
		) {
			$intWeekPrevious	= $this->week_previous_total;
			$intYearPrevious	= $this->year - 1;
		}

		$this->view->matches 		= $arrMatches;
		$this->view->week			= $this->week;
		$this->view->week_previous	= $intWeekPrevious;
		$this->view->week_next		= $intWeekNext;
		$this->view->year_previous	= $intYearPrevious;
		$this->view->year_next		= $intYearNext;
		$this->view->latest			= $arrLatestNews;
	}
	
	public function resultsAction()
	{
		$objModelMatches		= new KZ_Models_Matches();
		$objModelNews			= new KZ_Models_News();

		if(($this->week_current - 1) == $this->week && $this->filter_week === false && $this->filter_year === false) {
			
			// Get Matches
			$arrThisWeekMatches		= $objModelMatches->getMatches($this->year, $this->week_current, false, true, false);
			
			// Loop through this weeks matches
			if(isset($arrThisWeekMatches) && is_array($arrThisWeekMatches) && count($arrThisWeekMatches) > 0) {
			
				foreach($arrThisWeekMatches as $strTeam => $arrThisWeekMatchesByTeam) {
								
					foreach($arrThisWeekMatchesByTeam as $intMatchKey => $arrThisWeekMatch) {
		
						if(
							 	is_numeric($arrThisWeekMatch['team_home_score']) 
							&& 	$arrThisWeekMatch['team_home_score'] > 0
							&& 	is_numeric($arrThisWeekMatch['team_away_score']) 
							&& 	$arrThisWeekMatch['team_away_score'] > 0
						) {
							
							$booShowCurrentWeek = true;
							$this->week 		= $this->week_current;
							
						}
						
					}
					
				}
			
			}
		}
		
		// Get Matches
		$arrMatches				= $objModelMatches->getMatches($this->year, $this->week, false, true, false);
		
		// Get Latest News
		$arrLatestNews			= $objModelNews->getLatestNews($objModelNews->resultsCount);
		
		// Set Previous and Next Week
		$intWeekNext			= $this->week + 1;
		$intWeekPrevious		= $this->week - 1;
		
		// Set Previous and Next Year
		$intYearNext			= $this->year;
		$intYearPrevious		= $this->year;
		
		// Don't show next link when in current week - 1 and current year
		if(
				$this->week >= $this->week_current
			&&	$this->year	>= $this->year_current
		) {
			$intWeekNext = false;
			$intYearNext = false;
		}
		
		if(
			$this->week == 1
		) {
			$intWeekPrevious	= $this->week_previous_total;
			$intYearPrevious	= $this->year - 1;
		}
		
		$this->view->matches 		= $arrMatches;
		$this->view->week			= $this->week;
		$this->view->week_previous	= $intWeekPrevious;
		$this->view->week_next		= $intWeekNext;
		$this->view->year_previous	= $intYearPrevious;
		$this->view->year_next		= $intYearNext;
		$this->view->latest			= $arrLatestNews;
		
	}
	
	public function matchAction()
	{
		// Get All Params
		$arrParams	= $this->_getAllParams();
		
		// Check if Match ID was set
		if(isset($arrParams['match_id']) && ! empty($arrParams['match_id']) && is_numeric($arrParams['match_id'])) {
			
			// Set Models
			$objModelMatches	= new KZ_Models_Matches();
			
			// Get Match By ID
			$arrMatch			= $objModelMatches->getMatchByID($arrParams['match_id']);
			
			if(isset($arrMatch) && is_array($arrMatch) && count($arrMatch) > 0) {
				$this->view->assign([
                    'match' => $arrMatch,
                    'latest' => ($objModelNews = new KZ_Models_News())->getLatestNews($objModelNews->resultsCount)
                ]);
			} else {
				$this->redirect(ROOT_URL);
				exit;
			}
			
		} else {
			$this->redirect(ROOT_URL);
			exit;
		}
		
	}

}