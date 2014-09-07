<?php

/**
 * FILELOGIX TEMPLATES CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
namespace FLX\Controllers;

class templates extends \controller
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $userID;
	private $view = "home";
	private $auth;
	private $vars = array();
	private $lists;
	private $registration;
	private $request;

	/**
  	 * Create instance, load current info based on session info
  	 *
  	 * @return bool
  	 */
	
	public function __construct($db, $sessionID, $userID, $request) {
	  $this->db = $db;
	  $this->sessionID = $sessionID;
	  $this->userID = $userID;
	  $this->registration = new \registration($this->db);
	  $this->request = $request;
	 
	  
	  $this->auth = new \auth($this->db);
	  
	  if (!$this->auth->validate($this->userID)) {
		  
		  $_SESSION["returnURL"] = $this->request->getRequest();

		  $this->view = "login";
		  
		  return;
	  }
	  
	  unset($_SESSION["returnURL"]);
	  
	  $this->lists = new \lists($this->db);

	  $this->vars["username"] = "@" . $this->auth->getShortName($this->userID);

	  
	  $this->dflt();
	  		
//	  $this->db->insert("FLX_CONNECTIONS", array("type"=>$this->type, "sessionID"=>$this->sessionID, "httpHost"=>$this->httpHost, "ipAddress"=>$this->ipAddress, "userAgent"=>$this->userAgent, "fingerprint"=>$this->fingerprint, "requestURI"=>$this->requestURI, "_server"=>print_r($_SERVER,1), "_get"=>print_r($_GET,1), "_post"=>print_r($_POST,1)));	

//	  error_log($this->db->lastQuery());
	}
	
	/**
  	 * Opens the controller - responsible for authentication and loading defaults
  	 *
  	 * @return bool true if success, false if failure
  	 */
	
	public function open() {
/*
	  $this->users=$this->db->query("select * from connections");
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
      }
      
    
*/	

		$this->vars["active"] = "dashboard";
	
	}
	
	
	/**
  	 * Loads the controller, handles any templating and pre-display logic for the requested view
  	 *
  	 * @return bool
  	 */
	
	public function load($view) {
/*
	  $this->users=$this->db->query("select * from connections");
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
      }
      
*/
//        $this->vars["loginAlert"]="<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" style=\"text-indent: 0px; height: 100%;\" data-dismiss=\"alert\">&times;</button> <strong>Warning!</strong> Invalid username or password.</div>";
		if (!$this->auth->validate()) {
			$this->vars["showLogin"]="true";
		}
		else {
			$this->vars["showLogin"]="false";
		}
		$this->vars["loginUsername"]="wbenwick@filelogix.net";


		$this->vars["dropdowns"] = $this->lists->retrieveByID("1");

		$this->vars["clipboard"] = "<br><br><br><br>";
		$this->vars["briefcase"] = "<br><br><br><br>";
		
		$template = new template($this->db, "globalSearch");
		$this->vars["globalSearchStr"] = $template->fetch(null);
		
		$template = new template($this->db, "support");
		$this->vars["supportStr"] = $template->fetch(null);
/*		
		$notes=array( "note4"=>"", "note1"=>"<div class='links'><a href='#/invoices/open'><center><h6>View Open Invoices</h6></center></a></div>", "note2"=>"<div class='links'><a href='#/cabinet/claims/pending'><center><h6>View Pending Claims</h6></center></a></div>", "note3"=>"<div class='links'><a href='#/messages/new'><center><h4>New Messages Waiting (10)</h4></center></a></div>", "note5"=>"");

*/
		$notes = $this->lists->retrieveByID("2");	
		$papers = $this->lists->retrieveByID("3");
		
		$this->vars["stickyNotes"]=$notes;	
		$this->vars["papers"]=$papers;
		
		if (!$this->vars["players"]) {
					$players = $this->registration->getRecentPlayers();
		
					$this->vars["players"] = $players;
				
		}

	}

	public function dflt() {
	  if ($this->auth->validate($this->userID)) {

		$players = $this->registration->getRecentPlayers();
		$playerCount = $this->registration->getPlayerCount();
		$familyCount = $this->registration->getFamilyCount();
		$sportCount = $this->registration->getSportCount();
		$teamCount = $this->registration->getTeamCount();

		$this->vars["players"] = $players;
		$this->vars["playerCount"] = $playerCount;
		$this->vars["familyCount"] = $familyCount;
		$this->vars["sportCount"] = $sportCount;
		$this->vars["teamCount"] = $teamCount;
	
		$this->vars["active"] = "dashboard";
		
		$this->view = "home";
		
		return true;		
	  }
	  else {
		 return false;
	  }
	}
	
	public function players() {
		
	if ($this->auth->validate($this->userID)) {
	
		$players = $this->registration->getAllPlayers();
		
		$this->vars["players"] = $players;
	
		$this->vars["active"] = "players";
		
		$this->view = "players";
		
		return true;
	 }
	 else {
		 return false;
	 }
	}

	public function teams($params) {
		
	if ($this->auth->validate($this->userID)) {
	
		if ($params[2]=="change") {
		
			if ($_POST["action"]=="Show All") {
				
		  		$this->view = "";
		  		$this->transfer = "/portal/home/teams/all";
		  		return true;
				
			}

			else if ($_POST["action"]=="Show Unassigned") {
				
		  		$this->view = "";
		  		$this->transfer = "/portal/home/teams/unassigned";
		  		return true;
				
			}
				
			else if ($_POST["action"]=="Cancel") {
				
		  		$this->view = "";
		  		$this->transfer = "/portal/home/teams";
		  		return true;
				
			}

			else {	
				$this->vars["active"] = "teams";
				
				$this->view = "teams-change";

				$teams = $this->registration->getAllTeams("1");
		
				$this->vars["teams"] = $teams;

				
				return true;
			}
			
		}
		
		else if ($params[2]=="all") {

				$roster = $this->registration->getRosterWithBalances(null);
						
				$this->vars["teamName"] = "All Teams"; 							

				$this->vars["active"] = "teams";

				$this->vars["rosters"] = $roster;
				
				$this->view = "teams";
				
				return true;				
			
		}
		
		else if ($params[2]=="unassigned") {

				$roster = $this->registration->getUnassignedPlayers();
						
				$this->vars["teamName"] = "Unassigned Players"; 							

				$this->vars["active"] = "teams";

				$this->vars["rosters"] = $roster;
				
				$this->view = "teams";
				
				return true;			
		}
		
		else {				
				if ($params[2]) {
			
					$roster = $this->registration->getRosterWithBalances($params[2]);
					
					$team = $this->registration->getTeam($params[2]);

					$this->vars["teamName"] = $team["name"]; 
				
				}
				
				else {
			
					$roster = $this->registration->getRosterWithBalances(null);
				
				}
							
				$this->vars["rosters"] = $roster;
			
				$this->vars["active"] = "teams";
				
				$this->view = "teams";
				
				return true;
				
				
		}
	 }
	 else {
		 return false;
	 }
	}


	public function reports($params) {
		
	if ($this->auth->validate($this->userID)) {
	
	
		if ($params[2]=="teams") {
		

			if ($params[3]) {
				
				$teamID = $params[4];
	
				$team = $this->registration->getTeam($params[3]);
				
				$roster = $this->registration->getRosterWithBalancesAndContacts($params[3]);
						
				$this->vars["teamName"] = $team["teamName"];			
	
				$this->vars["active"] = "teams";
	
				$this->vars["rosters"] = $roster;
				
				$this->view = "reports-team";
				
				return true;				
			}
			
			else {
				
				$team = "All Teams";
				
				$roster = $this->registration->getRosterWithBalancesAndContacts(null);
						
				$this->vars["teamName"] = "All Teams"; 							
	
				$this->vars["active"] = "teams";
	
				$this->vars["rosters"] = $roster;
				
				$this->view = "reports-team";
				
				return true;				
				
			}

		}
		
		else {
		
			$players = $this->registration->getAllPayments();
			
			$this->vars["players"] = $players;
		
			$this->vars["active"] = "reports";
			
			$this->view = "reports";
			
			return true;		
		}
	 }
	 else {
		 return false;
	 }
	}
	
	public function calendars($params) {
		
	if ($this->auth->validate($this->userID)) {
		
		if ($params[2]>0) {
	
			$players = $this->registration->getCalendarPaymentsByEvent($params[2]);
			
			$this->vars["players"] = $players;
			
			include_once("/var/www/html/portal/custom/calendar.class.php");
			$calendar = new \calendar($this->db);
			$this->vars["eventTitle"] = $calendar->getEventTitle($params[2]);
			
			$this->vars["active"] = "reports";

			$this->view = "calendar-payments";			
			
		
			return true;
		}
		else {
			return false;
		}
	 }
	 else {
		 return false;
	 }
	}	

	
	public function data() {
		
		return $this->vars;
	}


	public function view() {
	
		if ($_GET["mobile"] == "yes") {
			return "mobile";
		}
		else {			
			return $this->view;
		}
	}
	
	public function transfer() {
		
		return $this->transfer;
	}

}
?>
