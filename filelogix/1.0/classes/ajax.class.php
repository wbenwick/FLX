<?php

/**
 * FILELOGIX AUTHENTICATION CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
class auth
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $username;
	private $emailAddress;
	private $authType;

	/**
  	 * Create instance, load current info based on session info
  	 *
  	 * @return bool
  	 */
	
	public function __construct($db) {
	  $this->db = $db;
	  $this->sessionID = session_id();		
		
//	  $this->db->insert("FLX_CONNECTIONS", array("type"=>$this->type, "sessionID"=>$this->sessionID, "httpHost"=>$this->httpHost, "ipAddress"=>$this->ipAddress, "userAgent"=>$this->userAgent, "fingerprint"=>$this->fingerprint, "requestURI"=>$this->requestURI, "_server"=>print_r($_SERVER,1), "_get"=>print_r($_GET,1), "_post"=>print_r($_POST,1)));	

//	  error_log($this->db->lastQuery());
	}
	
	/**
  	 * Log a user in based on password
  	 *
  	 * @return bool true for success, false for failure
  	 */
	
	public function login($username, $password) {
/*
	  $this->users=$this->db->query("select * from connections");
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
      }
*/		

		return true;
	}
	
	/**
  	 * Log the current user out
  	 *
  	 * @return bool 
  	 */
		
	public function logout() {
		
		return true;

	}

	/**
  	 * Log a user out based on sessionID
  	 *
  	 * @return bool 
  	 */
		
	public function logoutSID($sID) {
		
	}
	
	/**
  	 * Validate the current session
  	 *
  	 * @return bool 
  	 */
	 	
	public function validate() {
	
			return false;

    }
    
	/**
  	 * Return the current user's username
  	 *
  	 * @return string
  	 */
	
	public function getUsername() {
	
			return true;

    }
 
 	/**
  	 * Return the username of a logged in user from an existing session
  	 *
  	 * @return string
  	 */
	
	public function getUsernameSID($sID) {
	
			return true;


    }
       
    /**
  	 * Change the current user's password
  	 *
  	 * @return bool 
  	 */
	
	public function changePassword() {
	
			return true;


    }

	/**
  	 * change a specific user's password
  	 *
  	 * @return bool 
  	 */
	
	public function changeUsername($username) {
	
			return true;

    }    

}
?>