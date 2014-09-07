<?php

/**
 * FILELOGIX USER CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
class user
{  
    // Will store database connection here
	private $db;
	private $userID;
	private $username;
	private $emailAddress;
	private $password;
	private $status;
	private $users;
	
	public function __construct($db, $username) {
	  $this->db = $db;
	  $this->users=$this->db->query("select * from FLX_USERS where username=\"$username\"");
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
		  $this->status = $row['status'];
      }
      
      if ($this->userID < 1) {
	      return false;
      }
  
	  return true;
	}
 	
	public function getUsername() {
			return $this->username;
    }

	public function getStatus() {
			return $this->status;
    }

	public function getEmailAddress() {
			return $this->emailAddress;
    }
    
    public function getUserID() {
			return $this->userID;
    }     
   
   	public function setStatus($status) {
	   		$userID=$this->db->update("FLX_USERS", "userID", $this->userID, array("status"=>$status));	
	   		$this->status = $status;

	   		error_log("Set Status: " . $this->db->lastQuery());

	   		return $status;
   	} 
    
    public function activate($password) {
    	
    	if ($this->status == "new") {
	    			
	    	if ($this->setPassword($password)) {
				$this->setStatus("");
				return $this->userID;
			}
			else {
				return false;
			}
			
		}
		else {
			return false;
		}
		
		return false;
    
    }
    
    public function forgotPassword() {
	    
	    return false;
    }
    
    public function setPassword($password) {
	    
	    if (($password != "") and (strlen($password)>=6)) {

			   $result = $this->db->updateMD5("FLX_USERS", "userID", $this->userID, "password", $password);
		   	   if ($result != "") {
			   	   $this->password = $result;
			   	   return true;
		   	   }	 
		   	   else {
			   	   return false;
		   	   }
		    
	    }
	    
	    return false;
    }
    
    
    public function tempPassword($password) {
	    
	    return true;
    }
}
?>