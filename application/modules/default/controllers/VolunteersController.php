<?php
class Volunteerscontroller extends KZ_Controller_Action
{
    public $year;
    public $week;
    public $year_current;
    public $week_current;
    public $week_total;
    public $week_previous_total;
    public $week_previous;
    public $week_next;
    public $year_previous;
    public $year_next;
    public $weekendStart;
    public $weekendEnd;

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

        $objDate = new Zend_Date();
        $objDate->setYear($this->year);
        $objDate->setWeek($this->week);

        switch ($intDayOfWeek = (int)$objDate->get(Zend_Date::WEEKDAY_DIGIT)) {
            case 0:
                $this->weekendStart = $objDate->subDay(1)->toString('yyyy-MM-dd');
                $this->weekendEnd = $objDate->addDay(1)->toString('yyyy-MM-dd');
                break;
            default:
                $this->weekendStart = $objDate->addDay(6 - $intDayOfWeek)->toString('yyyy-MM-dd');
                $this->weekendEnd = $objDate->addDay(1)->toString('yyyy-MM-dd');
                break;
        }

        // Set Previous and Next Week
        $this->week_next			= $this->week + 1;
        $this->week_previous		= $this->week - 1;

        // Set Previous and Next Year
        $this->year_next			= $this->year;
        $this->year_previous		= $this->year;

        // Don't show previous link when in current week and current year
        if(
            $this->week <= $this->week_current
            &&	$this->year	<= $this->year_current
        ) {
            $this->week_previous = false;
            $this->year_previous = false;
        }

        // Add one year when last week in current year was matched
        if(
            $this->week == $this->week_total
        ) {
            $this->week_next = 1;
            $this->year_next = $this->year + 1;
        }

        if(
            $this->week == 1
        ) {
            $this->week_previous = $this->week_previous_total;
            $this->year_previous = $this->year - 1;
        }
    }

    public function barAction()
    {
        $this->view->assign([
            'volunteers' => (new KZ_Models_Volunteers())->getVolunteersByDate($this->weekendStart, $this->weekendEnd),
            'year' => $this->year,
            'week' => $this->week,
            'week_previous' => $this->week_previous,
            'week_next' => $this->week_next,
            'year_previous' => $this->year_previous,
            'year_next' => $this->year_next,
            'latest' => (new KZ_Models_News())->getLatestNews($this->objConfig->news->maxRelated),
        ]);
    }
}