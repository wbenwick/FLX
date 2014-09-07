<?php

/**
 * FILELOGIX HOME CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
namespace FLX\Controllers;

class admin
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $userID;
	private $view = "admin";
	private $auth;
	private $vars = array();
	private $lists;
	private $registration;

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

		$this->vars["active"] = "admin";
	
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

		$this->vars["players"] = $players;
		$this->vars["playerCount"] = $playerCount;
		$this->vars["familyCount"] = $familyCount;
		$this->vars["sportCount"] = $sportCount;
	
		$this->vars["active"] = "dashboard";
		
		$this->view = "admin";
		
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

	public function editor() {
		
	if ($this->auth->validate($this->userID)) {
			
		$this->vars["players"] = $players;
	
		$this->vars["active"] = "players";
		
		$this->view = "FLX_EDITOR";
		
		return true;
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
			return "admin";
		}
		else {			
			return $this->view;
		}
	}
	
	public function transfer() {
		
		return false;
	}

}
?>
