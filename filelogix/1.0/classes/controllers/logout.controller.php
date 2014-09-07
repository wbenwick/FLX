<?php


/**
 * FILELOGIX LOGIN CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 * Notes: This class creates the form for any login box or login page either as Javascript or HTML output
 * 
 */ 

namespace FLX\Controllers;
 
class logout
{  
  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $username;
	private $view;
	private $auth;
	private $vars = array();
	private $lists;
	private $action;

	/**
  	 * Create instance, load current info based on session info
  	 *
  	 * @return bool
  	 */
	
	public function __construct($db) {
	  $this->db = $db;

	  
	  $this->sessionID = $sessionID;
	  $this->username = $username;
	  
	  $this->auth = new \auth($this->db);
	  
	  $this->lists = new \lists($this->db);
	  
	  $this->auth->logout();
	 
	  session_regenerate_id(true);

	  		
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
	
	
	public function go() {

		if ($this->auth->login($_POST["username"], $_POST["password"])) {	
//			session_regenerate_id(true);
			error_log("Authenticated. " . $_POST["username"]);		
			$this->action = "go";
			return true;
		}
		
		else {
			error_log("Not Authenticated. " . $_POST["username"]);		
			$this->action = "";
			return true;
		}
	}
	
	/**
  	 * Loads the controller, handles any templating and pre-display logic for the requested view
  	 *
  	 * @return bool
  	 */
	
	public function load($view) {


	}

	public function view() {
		
		return "login";
	}
	
	public function transfer() {
	
		if ($this->action == "go") {
			if ($_SESSION["returnURL"]) {
				$returnURL = $_SESSION["returnURL"];
				error_log("Login ReturnURL: " . $returnURL);
				unset($_SESSION["returnURL"]);
				return $returnURL;
			}
			else {
				error_log("Transferring to /portal/home/");
				return "/portal/home";
			}
		}
		else {
			return false;
		}
		
	}
	
	
	public function data() {
		
		return $this->vars;
	}
}
?>