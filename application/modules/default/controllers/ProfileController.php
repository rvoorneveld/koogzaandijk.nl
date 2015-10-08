<?php
class ProfileController extends KZ_Controller_Action
{
	// Default Member Array, is filled when logged in
	public $arrMember;
	public $arrMemberTeams;
	public $arrCoachingTeams;
	public $arrPlayerTeams;
	public $arrProfile;
	public $arrProfileContact;
	public $arrCoachTeamIds;
	public $arrPlayerTeamIds;

	public function init()
	{
		// Get Profile Session Namespace
		$objControllerSession   = new KZ_Controller_Session();
		$this->arrMember        = $objControllerSession->getProfileSession();

		// Check if Member Profile was found
		if(empty($this->arrMember) || ! is_array($this->arrMember)) {

			// Log off
			$objControllerSession   = new KZ_Controller_Session();
			$objControllerSession->unsetProfileSession();

			$this->_redirect(ROOT_URL);
			exit;
		}

		// Set Member ID
		$intMemberID            = $this->arrMember['member_id'];

		// Get Member Teams
		$objModelMembers        = new KZ_Models_Members();

		// Get Member Teams By members ID
		$this->arrMemberTeams   = $objModelMembers->getMemberTeams($intMemberID);

		// Set Default Team IDs
		$this->arrCoachTeamIds  = array();
		$this->arrPlayerTeamIds = array();
		$this->arrCoachingTeams = false;
		$this->arrPlayerTeams   = false;

		if(! empty($this->arrMemberTeams) && is_array($this->arrMemberTeams)) {

			// Set Models
			$objModelTeams          = new KZ_Models_Teams();

			// Get Coach Teams Array
			if(! empty($this->arrMemberTeams['coach_teams'])) {
				$this->arrCoachTeamIds    = explode(',',$this->arrMemberTeams['coach_teams']);
			}

			// Get Players Teams Array
			if(! empty($this->arrMemberTeams['player_teams'])) {
				$this->arrPlayerTeamIds   = explode(',',$this->arrMemberTeams['player_teams']);
			}

			if($this->arrCoachTeamIds !== false && is_array($this->arrCoachTeamIds)) {

				// Get Coaching Teams information
				$this->arrCoachingTeams = $objModelTeams->getCompleteTeamData($this->arrCoachTeamIds);

			}

			if($this->arrPlayerTeamIds !== false && is_array($this->arrPlayerTeamIds)) {

				// Get Players Teams information
				$this->arrPlayerTeams   = $objModelTeams->getCompleteTeamData($this->arrPlayerTeamIds);
			}

		}

		// Get Profile
		$objModelProfile    = new KZ_Models_Profile();

		// Get Profile
		$this->arrProfile   = $objModelProfile->getProfileByMemberID($intMemberID);

		if(! empty($this->arrProfile) && is_array($this->arrProfile)) {

			// Get Profile Contact Information
			$this->arrProfileContact    = $objModelProfile->getProfileContact($intMemberID);

		} else {
			// No Profile Found, go to homepage
			$this->_redirect(ROOT_URL);
			exit;
		}

	}

	public function indexAction()
	{
		// Parse Data to View
		$this->view->member             = $this->arrMember;
		$this->view->member_teams       = $this->arrMemberTeams;
		$this->view->teams_coach        = $this->arrCoachingTeams;
		$this->view->teams_player       = $this->arrPlayerTeams;
		$this->view->profile            = $this->arrProfile;
		$this->view->profile_contact    = $this->arrProfileContact;
	}

	public function contactgegevensAction()
	{
		// Set Models
		$objModelMembers        = new KZ_Models_Members();

		// Set Unique Members
		$arrUniqueMembers       = array_unique(array_merge($this->arrCoachTeamIds,$this->arrPlayerTeamIds));

		// Get Contacts
		$arrContacts            = $objModelMembers->getConnectedMembers($arrUniqueMembers);

		// Parse Contacts to View
		$this->view->contacts   = $arrContacts;
		$this->view->profile    = $this->arrMember;
	}

	public function wijzigAction()
	{
		$objResponsive          = new KZ_Controller_Action_Helper_Responsive();
		$booIsDesktop           = $objResponsive->isDesktop();

		// Set Defaults
		$arrDefaults                    = array();
		$arrDefaults['auto_login']      = $this->arrProfile['auto_login'];
		$arrDefaults['members_id']      = $this->arrProfileContact['member_id'];
		$arrDefaults['share']           = $this->arrProfileContact['share'];
		$arrDefaults['mobile_number']   = $this->arrProfileContact['mobile_number'];
		$arrDefaults['email']           = $this->arrProfileContact['email'];
		$arrDefaults['facebook']        = $this->arrProfileContact['facebook'];
		$arrDefaults['twitter']         = $this->arrProfileContact['twitter'];

		$this->view->isDesktop  = $booIsDesktop;
		$this->view->profile    = $this->arrMember;
		$this->view->defaults   = $arrDefaults;
	}

}