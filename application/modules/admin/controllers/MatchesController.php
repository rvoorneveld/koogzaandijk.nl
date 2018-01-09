<?php

class Admin_MatchesController extends KZ_Controller_Action
{

    public function indexAction()
    {
        // Get All Params
        $arrParams = $this->getAllParams();

        // Set Models
        $objModelMatches = new KZ_Models_Matches();

        // Get Distinct Years
        $arrDistinctYears = $objModelMatches->getDistinct();

        // Get Distinct Weeks
        $arrDistinctWeeks = $objModelMatches->getDistinct('week');

        // Set Current Date
        $objCurrentDate = new Zend_Date();

        $this->view->year = ((false === empty($arrParams['year']) && true === is_numeric($arrParams['year'])) ? $arrParams['year'] : $objCurrentDate->get('yyyy'));
        $this->view->week = ((false === empty($arrParams['week']) && true === is_numeric($arrParams['week'])) ? $arrParams['week'] : $objCurrentDate->get('w'));

        $this->view->years = $arrDistinctYears;
        $this->view->weeks = $arrDistinctWeeks;
    }

    public function editAction()
    {
        // Get All Params
        $arrParams = $this->getAllParams();

        // Check if Param id is set
        if (true === empty($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(['type' => 'error', 'message' => 'Missing param for id']));
            $this->redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
        }

        // Get Match
        $objModelMatches = new KZ_Models_Matches();
        $arrMatch = $objModelMatches->getMatch($arrParams['id']);

        // Check if Match wasn't found
        if (true === empty($arrMatch)) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(['type' => 'error', 'message' => 'Unable to find match']));
            $this->redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
        }

        // Set Default Variables
        $intMatchID = $arrMatch['match_id'];
        $intCompetitionProgramID = $arrMatch['competition_program_id'];
        $strTeamHomeName = $arrMatch['team_home_name'];
        $strTeamAwayName = $arrMatch['team_away_name'];
        $strTeamHomeScore = $arrMatch['team_home_score'];
        $strTeamAwayScore = $arrMatch['team_away_score'];
        $strPouleCode = $arrMatch['poule_code'];
        $strPouleName = $arrMatch['poule_name'];
        $intYear = $arrMatch['year'];
        $intWeek = $arrMatch['week'];
        $strDate = $this->view->date()->format($arrMatch['date'], 'dd-MM-yyyy');
        $strTime = $arrMatch['time'];
        $strTimeDeparture = $arrMatch['time_departure'];
        $strOfficials = $arrMatch['officials'];

        if (true === $this->getRequest()->isPost()) {
            // Get All Post Params
            $arrPostData = $this->getAllParams();

            // Set Post Variables
            $strDate = $arrPostData['date'];
            $strTime = $arrPostData['time'];
            $strTimeDeparture = $arrPostData['time_departure'];
            $strOfficials = $arrPostData['officials'];

            // Check form
            if (true === empty($strDate)) {
                $this->view->feedback = ['type' => 'error', 'message' => 'You didn\'t fill in a date'];
            } elseif (true === empty($strTime)) {
                $this->view->feedback = ['type' => 'error', 'message' => 'You didn\'t fill in a time'];
            } else {
                // Set Update array
                $arrData = [
                    'date' => $this->view->date()->format($strDate, 'yyyy-MM-dd'),
                    'time' => $strTime,
                    'time_departure' => $strTimeDeparture,
                    'officials' => $strOfficials,
                    'team_home_score' => $this->_validateScore($arrPostData['team_home_score']),
                    'team_away_score' => $this->_validateScore($arrPostData['team_away_score']),
                ];

                // Update The Data
                $intUpdateID = $objModelMatches->updateMatch($arrMatch['matches_id'], $arrData);

                if (false === empty($intUpdateID) && true === is_numeric($intUpdateID)) {
                    $strFeedback = base64_encode(serialize(['type' => 'success', 'message' => 'Succesfully updated match']));
                    $this->redirect('/admin/matches/index/year/'.$intYear.'/week/'.$intWeek.'/feedback/'.$strFeedback.'/');
                } else {
                    // Return feedback
                    $this->view->feedback = ['type' => 'error', 'message' => 'Something went wrong trying to update the match'];
                }
            }
        }

        $this->view->assign([
            'match_id' => $intMatchID,
            'competition_program_id' => $intCompetitionProgramID,
            'team_home_name' => $strTeamHomeName,
            'team_away_name' => $strTeamAwayName,
            'team_home_score' => $strTeamHomeScore,
            'team_away_score' => $strTeamAwayScore,
            'poule_code' => $strPouleCode,
            'poule_name' => $strPouleName,
            'year' => $intYear,
            'week' => $intWeek,
            'date' => $strDate,
            'time' => $strTime,
            'time_departure' => $strTimeDeparture,
            'officials' => $strOfficials,
        ]);
    }

    public function weekupdateAction()
    {
        // Get All Params
        $arrParams = $this->getAllParams();

        // Set the Matches Model
        $objModelMatches = new KZ_Models_Matches();

        // Get Distinct Years
        $arrDistinctYears = $objModelMatches->getDistinct();

        // Get Distinct Weeks
        $arrDistinctWeeks = $objModelMatches->getDistinct('week');

        // Set Current Date
        $objCurrentDate = new Zend_Date();

        // Set Year and Week
        $intYear = ((false === empty($arrParams['year']) && true === is_numeric($arrParams['year'])) ? $arrParams['year'] : $objCurrentDate->get('yyyy'));
        $intWeek = ((false === empty($arrParams['week']) && true === is_numeric($arrParams['week'])) ? $arrParams['week'] : $objCurrentDate->get('w'));

        // Get All Matches by Week and Year
        $arrMatches = $objModelMatches->getMatches($intYear, $intWeek, false, true, 50);

        // Check for Post
        if (true === $this->getRequest()->isPost()) {
            // Set Defaults
            $arrUpdate = [];

            // Get All Post Params
            $arrPostParams = $this->getAllParams();

            // Loop through Post
            foreach ($arrPostParams as $strPostKey => $strPostValue) {
                if (strstr($strPostKey, 'team_home_score_') || strstr($strPostKey, 'team_away_score_') || strstr($strPostKey, 'time_departure_')) {
                    if (null !== $strPostValue) {
                        // Explode Post Key
                        $arrPostKey = explode('_', $strPostKey);
                        $intMatchesID = (int)end($arrPostKey);

                        // Check if Match doesn't exist yet
                        if (true === empty($arrUpdate[$intMatchesID]) || false === is_array($arrUpdate[$intMatchesID]) || 0 === count($arrUpdate[$intMatchesID])) {
                            $arrUpdate[$intMatchesID] = [
                                'team_home_score' => null,
                                'team_away_score' => null,
                                'time_departure' => '',
                            ];
                        }

                        // Remove matches ID from array and create string
                        array_pop($arrPostKey);
                        $strDatabaseKey = implode('_', $arrPostKey);

                        // Set Update Value
                        $arrUpdate[$intMatchesID][$strDatabaseKey] = $strPostValue;
                    }
                }
            }

            if (false === empty($arrUpdate) && true === is_array($arrUpdate) && 0 <= count($arrUpdate)) {
                foreach ($arrUpdate as $intMatchesID => $arrMatch) {
                    $objModelMatches->updateMatch($intMatchesID, $arrMatch);
                }
                $strFeedback = base64_encode(serialize(['type' => 'success', 'message' => 'Succesfully updated matches']));
                $this->redirect('/admin/matches/index/feedback/'.$strFeedback.'/');
            } else {
                $this->view->feedback = ['type' => 'error', 'message' => 'Something went wrong trying to update the matches'];
            }
        }

        $this->view->assign([
            'years' => $arrDistinctYears,
            'year' => $intYear,
            'weeks' => $arrDistinctWeeks,
            'week' => $intWeek,
            'matches' => $arrMatches,
        ]);
    }

    public function deleteAction()
    {
        // Get All Params
        $arrParams = $this->getAllParams();

        // Check if Param id is set
        if (true === empty($arrParams['id'])) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(['type' => 'error', 'message' => 'Missing param for id']));
            $this->redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
        }

        // Get Match
        $objModelMatches = new KZ_Models_Matches();
        $arrMatch = $objModelMatches->getMatch($arrParams['id']);

        // Check if Match wasn't found
        if (true === empty($arrMatch)) {
            // return feedback
            $strSerializedFeedback = base64_encode(serialize(['type' => 'error', 'message' => 'Unable to find match']));
            $this->redirect('/admin/matches/index/feedback/'.$strSerializedFeedback.'/#tab0/');
        }

        // Set Default Variables
        $intMatchID = $arrMatch['match_id'];
        $intCompetitionProgramID = $arrMatch['competition_program_id'];
        $strTeamHomeName = $arrMatch['team_home_name'];
        $strTeamAwayName = $arrMatch['team_away_name'];
        $strTeamHomeScore = $arrMatch['team_home_score'];
        $strTeamAwayScore = $arrMatch['team_away_score'];
        $strPouleCode = $arrMatch['poule_code'];
        $strPouleName = $arrMatch['poule_name'];
        $intYear = $arrMatch['year'];
        $intWeek = $arrMatch['week'];
        $strDate = $this->view->date()->format($arrMatch['date'], 'dd-MM-yyyy');
        $strTime = $arrMatch['time'];
        $strTimeDeparture = $arrMatch['time_departure'];
        $strOfficials = $arrMatch['officials'];

        if (true === $this->getRequest()->isPost()) {
            // Delete The Data
            $intDeleteID = $objModelMatches->deleteMatch($arrMatch['matches_id']);

            if (false === empty($intDeleteID) && true === is_numeric($intDeleteID)) {
                $strFeedback = base64_encode(serialize(['type' => 'success', 'message' => 'Succesfully deleted match']));
                $this->redirect('/admin/matches/index/feedback/'.$strFeedback.'/');
            } else {
                // Return feedback
                $this->view->feedback = ['type' => 'error', 'message' => 'Something went wrong trying to delete the match'];
            }
        }

        // Parse Variables to view
        $this->view->assign([
            'match_id' => $intMatchID,
            'competition_program_id' => $intCompetitionProgramID,
            'team_home_name' => $strTeamHomeName,
            'team_away_name' => $strTeamAwayName,
            'team_home_score' => $strTeamHomeScore,
            'team_away_score' => $strTeamAwayScore,
            'poule_code' => $strPouleCode,
            'poule_name' => $strPouleName,
            'year' => $intYear,
            'week' => $intWeek,
            'date' => $strDate,
            'time' => $strTime,
            'time_departure' => $strTimeDeparture,
            'officials' => $strOfficials,
        ]);
    }

    /**
     * Function for generating the Property table
     * Used for the AJAX call for the Datatable
     */
    public function generatematchestableAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Get Params
        $arrParams = $this->getAllParams();

        // Set Models
        $objModelMatches = new KZ_Models_Matches();
        $objTranslate = new KZ_View_Helper_Translate();

        // Set the Columns
        $arrColumns = [
            $objTranslate->translate('ID'),
            $objTranslate->translate('KNKV ID'),
            $objTranslate->translate('Game'),
            $objTranslate->translate('Result'),
            $objTranslate->translate('Week'),
            $objTranslate->translate('Date'),
            $objTranslate->translate('Options'),
        ];

        // Set the DB Table Columns
        $arrTableColums = [
            'matches_id',
            'match_id',
            'game',
            'result',
            'year_week',
            'date',
        ];

        // Set the Search
        $strSearchData = null;
        if ('' !== $_POST['sSearch']) {
            $strSearchString = $_POST['sSearch'];

            if (true === is_numeric($strSearchString)) {
                $strSearchData = "(matches_id LIKE '%".$strSearchString."%' OR match_id LIKE '%".$strSearchString."%' 
                                    OR year LIKE '%".$strSearchString."%' OR week LIKE '%".$strSearchString."%' 
                                    OR team_home_score LIKE '%".$strSearchString."%' OR team_away_score LIKE '%".$strSearchString."%' )";
            } else {
                $strSearchData = "(team_home_name LIKE '%".$strSearchString."%' OR team_away_name LIKE '%".$strSearchString."%' )";
            }
        }

        // Set the Limit
        $intResultsOnPage = $this->_getParam('iDisplayLength');
        $intStartNumber = $this->_getParam('iDisplayStart');
        $arrLimitData = [
            'count' => $intResultsOnPage,
            'offset' => $intStartNumber
        ];

        // Ordering
        $arrOrderData = [];
        if (false === empty($_POST['iSortCol_0'])) {
            $intTotalSortingColumns = (int)$_POST['iSortingCols'];
            for ($intI = 0; $intI < $intTotalSortingColumns; $intI++) {
                $intColumnNumber = (int)$_POST['iSortCol_'.$intI];
                if ('true' === $_POST['bSortable_'.$intColumnNumber]) {
                    $strSortColumns = $arrTableColums[$intColumnNumber];
                    $strSortDirection = $_POST['sSortDir_'.$intI];
                    $arrOrderData[] = $strSortColumns.' '.strtoupper($strSortDirection);
                }
            }
        }

        // Get the Totals
        $intTotalMatches = $objModelMatches->getMatchesForTable(true, null, null, null, $arrParams['year'], $arrParams['week']);

        // Select all properties
        $objMatches = $objModelMatches->getMatchesForTable(false, $arrLimitData, $strSearchData, $arrOrderData, $arrParams['year'], $arrParams['week']);
        $arrMatches = $objMatches->toArray();

        // Create the JSON Data
        $output = [
            'sEcho' => (int)$_POST['sEcho'],
            'iTotalRecords' => $intTotalMatches,
            'iTotalDisplayRecords' => $intTotalMatches,
            'aaData' => [],
        ];

        if (false === empty($arrMatches)) {
            foreach ($arrMatches as $key => $arrMatch) {
                $row = [];
                $intTotalColumns = count($arrColumns);
                for ($i = 0; $i < $intTotalColumns; $i++) {
                    if (' ' !== $arrColumns[$i]) {
                        if (false === empty($arrTableColums[$i])) {
                            if ('matches_id' === $arrTableColums[$i]) {
                                $strRowData = '<a name="matchid_'.$arrMatch['matches_id'].'" href="/admin/matches/edit/id/'.$arrMatch['matches_id'].'"><strong>'.$arrMatch['matches_id'].'</strong></a>';
                            } elseif ('date' === $arrTableColums[$i]) {
                                $strRowData = $this->view->date()->format($arrMatch['date'], 'dd-MM-yyyy');
                            } else {
                                $strRowData = stripslashes($arrMatch[$arrTableColums[$i]]);
                            }
                        } else {
                            // Add the Option Values
                            $strOptionsHtml = '
                                <ul class="actions">
                                    <li><a rel="tooltip" href="/admin/matches/edit/id/'.$arrMatch['matches_id'].'/" class="edit" original-title="'.$objTranslate->translate('Edit match').'">'.$objTranslate->translate('Edit match').'</a></li>
                                    <li><a rel="tooltip" href="/admin/matches/delete/id/'.$arrMatch['matches_id'].'/" class="delete" original-title="'.$objTranslate->translate('Delete match').'">'.$objTranslate->translate('Delete match').'</a></li>
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

    private function _validateScore($score = null)
    {
        switch ($score) {
            case 'c':
                return 'c';
                break;
            case $score <= 99:
                return (int)$score;
                break;
            default:
                return null;
                break;
        }
    }

}
