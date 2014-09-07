<?php

/**
 * FILELOGIX PLAYER CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
namespace FLX\Controllers;

class player
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $userID;
	private $view = "player";
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
	  $this->userID = $userID;
	  $this->registration = new \registration($this->db);
	  
	  $this->auth = new \auth($this->db);

	  if (!$this->auth->validate($this->userID)) {
		  $this->view = "login";
		  
		  return;
	  }
	  	  
	  $this->lists = new \lists($this->db);
	  
	  $this->vars["playersActive"] = "class=active";

	  $this->vars["username"] = "@" . $this->auth->getShortName($this->userID);

	  		
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
	
	
	public function edit($params) {
	
	  if ($_POST["action"]=="Save") {
	  		$this->registration->updateTeam(intval($_POST["playerID"]), intval($_POST["team"]));
	  		
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
		
			  $access["player"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Player");
		
			  if (($this->auth->validate($this->userID)) and ($access["player"][0]["edit"])) {
						
				$player = $this->registration->getPlayer($params[2]);
				$parents = $this->registration->getParents();
				$sport = $this->registration->getSport($player["playerID"]);
				$sports =  $this->registration->getAllSports();
				$divisions =  $this->registration->getDivisions($sport["sportID"],$player["childID"]);
				$teams = $this->registration->getTeamsByDivision($sport["sportID"]);
		
				$teamNames = array();
				$teamIDs = array();
				
				
				$this->vars["parents"] = array();
				foreach ($parents as $parent) {
					array_push($this->vars["parents"], $this->registration->getParent($parent));
		
				}
		
				foreach ($teams as $team) {
					array_push($teamNames, $team["name"]);
					array_push($teamIDs, $team["teamID"]);
				}
				
				array_push($teamNames, "Unassigned");
				array_push($teamIDs, "0");
		
				
		//		error_log("Player: " . print_r($player));
				
				$this->vars["teamNames"]=$teamNames;
				$this->vars["teamIDs"]=$teamIDs;
				$this->vars["player"] = $player;
				$this->vars["sport"] = $sport;
				$this->vars["sports"] = $sports;
				$this->vars["divisions"] = $divisions;
				$this->vars["username"] = $_SESSION["username"];
				$this->vars["userID"] = $_SESSION["userID"];
			
				$this->vars["playersActive"] = "class=active";
				
				$this->view = "player-edit";
				
				
				$this->vars["editPlayer"] = $access["player"][0];
				
				return true;
			  }
			  else {
			  	  $this->lookup($params);
			  	  $this->vars["errorMsg"] = "You do not have permission to edit this player.";
			  	  
				  return true;
			  }
	  }
	}


	
	public function delete($params) {
		  
		
			  $access["player"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Player");
		
			  if (($this->auth->validate($this->userID)) and ($access["player"][0]["delete"])) {

				if ($this->registration->deletePlayer($params[2])) {
					$this->transfer="/portal/home/players";
					$this->view="";
					$this->vars["confirmMsg"] = "The player has been removed from the system.";
					return true;
				}
				else {
					$this->lookup($params);
					$this->vars["errorMsg"] = "This player can not be deleted because they are active.";
					return true;
				}
			  }
			  else {
			  	  $this->lookup($params);
			  	  $this->vars["errorMsg"] = "You do not have permission to delete this player.";
			  	  
				  return true;
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
