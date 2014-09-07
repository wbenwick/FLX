<?php

/**
 * FILELOGIX CONNECTIONS CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
class connection
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $fingerprint;
	private $ipAddress;
	private $tokenID;
	private $userAgent;
	private $userCert;
	private $sequence;
	private $httpHost;
	private $requestURI;
	private $isEncrypted;
	private $isTrusted;
	private $isValid;
	private $parentID;
	private $type;
	
	public function __construct($db) {
	  $this->db = $db;
	  $this->id = 0;
	  $this->sessionID = session_id();
	  $this->fingerprint = md5($_SERVER["HTTP_USER_AGENT"] . $_SERVER["REMOTE_ADDR"]);
	  $this->ipAddress = $_SERVER["REMOTE_ADDR"];
//	  $this->tokenID = getTokenID($_COOKIE["token"]);
	  $this->userAgent = $_SERVER["HTTP_USER_AGENT"];
	  $this->httpHost = $_SERVER["HTTP_HOST"];
	  $this->requestURI = $_SERVER["REQUEST_URI"];
	  $this->type = $_SERVER["SERVER_PROTOCOL"];
  	  $this->sequence = $_COOKIE["seq"];
		
		
	  $this->id = $this->db->insert("FLX_CONNECTIONS", array("type"=>$this->type, "sessionID"=>$this->sessionID, "httpHost"=>$this->httpHost, "ipAddress"=>$this->ipAddress, "userAgent"=>$this->userAgent, "fingerprint"=>$this->fingerprint, "requestURI"=>$this->requestURI, "_server"=>print_r($_SERVER,1), "_get"=>print_r($_GET,1), "_post"=>print_r($_POST,1)));	

	  error_log($this->db->lastQuery());
	}
	
	public function findPrevious() {
	  $this->users=$this->db->query("select * from connections");
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
      }
		
	}
	
	public function uaHash() {
		
		return $this->fingerprint;
	}
 	
	public function getIPAddress() {

    }
    

	public function isMatching() {

    }
    
    public function id() {
	    return $this->id;
    }
    
    public function uri() {
	    return $this->requestURI;
    }
    

}
?>