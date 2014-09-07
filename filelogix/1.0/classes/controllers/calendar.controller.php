<?php

/**
 * FILELOGIX CALENDAR CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
namespace FLX\Controllers;

class calendar
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $userID;
#	private $view = "CAL_SEARCH";
	private $view = "CAL_LOGIN";
	private $auth;
	private $vars = array();
	private $lists;
	private $registration;
	private $transfer = false;

	/**
  	 * Create instance, load current info based on session info
  	 *
  	 * @return bool
  	 */
	
	public function __construct($db, $sessionID, $userID) {
	  $this->db = $db;
	  $this->sessionID = $sessionID;
	  $this->auth = new \auth($db);
	  $this->userID = $this->auth->getUserID();

	  $this->lists = new \lists($this->db);
	  	 
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

		$this->vars["loginUsername"]="wbenwick@filelogix.net";


		$this->vars["dropdowns"] = $this->lists->retrieveByID("1");
		
	}
	
	public function add($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_NEW_EVENT_V2";
		$this->vars["navCreateActive"]=true;
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}

	public function results($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_RESULTS";
		$this->vars["navHomeActive"]=true;
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_RESULTS";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }
	}

	public function manage($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_MANAGE";
		$this->vars["navManageActive"]=true;
		
		$ticket = new \CAL\ticket($this->db);

		$this->vars["events"]=$ticket->getAllEvents(); 
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}


	public function attendee($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_ATTENDEE";
		$this->vars["navManageActive"]=true;
		
		$ticket = new \CAL\ticket($this->db);
				
		$this->vars["attendee"]=$ticket->getAttendee($params[2]);
		$this->vars["events"]=$ticket->getAllEvents(); 
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}

	public function support($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_HOME";
		$this->vars["navSupportActive"]=true;
		

		
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}

	public function home($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_HOME";
		$this->vars["navHomeActive"]=true;
		
		
		$ticket = new \CAL\ticket($this->db);
		$member = new \registration($this->db);

		$this->vars["chartLeft"] = $ticket->getEventAttendeesByDay(1);
		$this->vars["chartRight"] = $member->getTotalMembersByDay();
		$this->vars["attendees"] = $ticket->getAttendeesByEvent(1);

		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }
	}

	public function reports($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_REPORTS";
		$this->vars["navReportsActive"]=true;
		
		
		$ticket = new \CAL\ticket($this->db);
		$member = new \registration($this->db);

		$this->vars["chartLeft"] = $ticket->getEventAttendeesByDay(1);
		$this->vars["chartRight"] = $member->getTotalMembersByDay();
		$this->vars["attendees"] = $ticket->getAttendeesByEvent(1);

		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navReportsActive"]=false;
		
		return true;		  
		  
	  }
	}	
	


	public function login($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_HOME";
		$this->vars["navHomeActive"]=true;
		
		return true;
	  }
	  
	  else {
	  
	  	error_log("Logging Calendar User In... " . $_POST["username"]);

		if (($_POST["username"] != "") and ($_POST["password"] != "")) {
			if ($this->auth->login($_POST["username"], $_POST["password"])) {

				$this->view="CAL_HOME";
				$this->vars["navHomeActive"]=true;
				return true;
			}
			else {
				$this->view="CAL_LOGIN";
				$this->vars["alertError"]=true;
				return true;
			}
		}
		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }
	}	


	public function logout($params) {

		$this->auth->logout();
			
		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	}	
	
	public function lookup($params) {
	  if ($this->auth->validate($this->userID)) {
//	    $this->vars["username"] = "@" . $this->auth->getShortName($this->userID);

	    $access["payments"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Payments");
		$access["player"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Player");	
		$access["confcode"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Conf Code");	
						
		if ($access["payments"][0]["create"]) {
			$this->vars["acceptPayments"]=true;
		}

		if ($access["confcode"][0]["view"]) {
			$this->vars["confCode"]=true;
		}
		else {
			$this->vars["confCode"]=false;			
		}
		
		if ($access["player"][0]["delete"]) {
			$this->vars["deletePlayer"]=true;
		}
		else {
			$this->vars["deletePlayer"]=false;
		}
		if ($this->registration->isPlayerActive($params[2])) {
			$this->vars["playerIsActive"]=true;
		}
		else {
			$this->vars["playerIsActive"]=false;			
		}

		$player = $this->registration->getPlayer($params[2]);
		$payments = $this->registration->getPaymentHistory($params[2]);
		$invoices = $this->registration->getInvoicesByPlayer($params[2]);
		$parents = $this->registration->getParents();
		$sport = $this->registration->getSport($player["id"]);
		$sports =  $this->registration->getSports();
		$divisions =  $this->registration->getDivisions($sport["sportID"],$player["childID"]);
		$team = $this->registration->getTeam($player["teamID"]);
		
		
		
		$this->vars["parents"] = array();
		foreach ($parents as $parent) {
			array_push($this->vars["parents"], $this->registration->getParent($parent));
			

		}
		
//		error_log("Player: " . print_r($player));
		
		$this->vars["player"] = $player;
		$this->vars["payments"] = $payments;
		$this->vars["invoices"] = $invoices;
		$this->vars["sport"] = $sport;
		$this->vars["team"] = $team;
		$this->vars["sports"] = $sports;
		$this->vars["divisions"] = $divisions;
		$this->vars["userID"] = $_SESSION["userID"];
	
		$this->vars["playersActive"] = "class=active";
		
		$this->view = "player";
				
		$this->vars["editPlayer"] = $access["player"][0];
		
		return true;
	  }
	  else {
		  return false;
	  }
	}


	public function pay($params) {
	  if ($this->auth->validate($this->userID)) {

		  $access["payments"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Payments");
		
			  if (($this->auth->validate($this->userID)) and ($access["payments"][0]["create"])) {

				  if ($_POST["action"]=="Save") {
				  		$this->registration->paymentByPlayer($_POST["source"], $_POST["invoice"], $_POST["refnum"], $_POST["amount"], 0, $this->userID);
				  		
				  		$this->view = "";
				  		$this->transfer = "/portal/player/lookup/" . $params[2];
				  		return true;
					  
				  }

				  else if ($_POST["action"]=="Cancel") {
				  		
				  		$this->view = "";
				  		$this->transfer = "/portal/player/lookup/" . $params[2];
				  		return true;
					  
				  }
				  
				  else {
				  
			
				    $this->vars["username"] = "@" . $this->auth->getShortName($this->userID);
							
					$player = $this->registration->getPlayer($params[2]);
					$payments = $this->registration->getPaymentHistory($params[2]);
					$invoices = $this->registration->getInvoicesByPlayer($params[2]);
					$openInvoices = $this->registration->getOpenInvoicesByPlayer($params[2]);
					$parents = $this->registration->getParents();
					$sport = $this->registration->getSport($player["id"]);
					$sports =  $this->registration->getSports();
					$divisions =  $this->registration->getDivisions($sport["sportID"],$player["childID"]);
					$team = $this->registration->getTeam($player["teamID"]);
							
					$invoiceNames = array();
					$invoiceIDs = array();
					


					foreach ($openInvoices as $invoice) {
						array_push($invoiceNames, $invoice["id"] . " - " . $invoice["title"] . " (" . $invoice["balance"] .")");
						array_push($invoiceIDs, $invoice["id"]);
					}
						
					if (count($invoiceIDs)<1) {
						$this->vars["acceptPaymentError"] = "There are no open invoices to pay at this time.";
					}

					$this->vars["parents"] = array();
					foreach ($parents as $parent) {
						array_push($this->vars["parents"], $this->registration->getParent($parent));
			
					}
					
			//		error_log("Player: " . print_r($player));
					
					$this->vars["player"] = $player;
					$this->vars["payments"] = $payments;
					$this->vars["invoices"] = $invoices;
					$this->vars["invoiceIDs"] = $invoiceIDs;
					$this->vars["invoiceNames"] = $invoiceNames;
					$this->vars["sport"] = $sport;
					$this->vars["team"] = $team;
					$this->vars["sports"] = $sports;
					$this->vars["divisions"] = $divisions;
					$this->vars["userID"] = $_SESSION["userID"];
				
					$this->vars["playersActive"] = "class=active";
					
					$this->view = "player-payment";
					
					$access["player"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Player");
					
//					$this->vars["editPlayer"] = $access["editPlayer"][0];
					$this->vars["editPlayer"] = false;
					
					return true;
				}
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
		
		return $this->view;
	}
	
	public function transfer() {
		
		return $this->transfer;
	}

}
?>
