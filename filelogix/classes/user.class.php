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
	private $isActive;
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

   	public function setActive($active) {
	   		$userID=$this->db->update("FLX_USERS", "userID", $this->userID, array("isActive"=>$active));	
	   		$this->isActive = $isActive;

	   		error_log("Set IsActive: " . $this->db->lastQuery());

	   		return $this->isActive;
   	} 

   	public function setUsername($username) {
	   		$userID=$this->db->update("FLX_USERS", "userID", $this->userID, array("username"=>$username));	
	   		$this->username = $username;

	   		error_log("Set Username: " . $this->db->lastQuery());

	   		return $this->username;
   	}

   	public function setActivationCode($code) {
	   		$userID=$this->db->update("FLX_USERS", "userID", $this->userID, array("activationCode"=>$code));	

	   		return true;
   	} 

   	public function setUserKey($code) {
	   		$userID=$this->db->update("FLX_USERS", "userID", $this->userID, array("userKey"=>md5($code)));	

	   		return true;
   	} 
   	   	    
    public function activate($password) {
    
    	
    	if ($this->status == "new") {
	    			
	    	if ($this->setPassword($password)) {
				$this->setStatus("");
				$this->setActivationCode("");
				return $this->userID;
			}
			else {
				return false;
			}
			
		}
    	if ($this->status == "pending") {
	     	error_log("UserActivate " . $this->userID);
   			
	    	if ($this->setPassword($password)) {
				$this->setStatus("new");
				$this->setActivationCode($password);
				$this->setUserKey($password);
				$this->setActive(true);
				$this->setUsername($this->emailAddress);
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
    
    public function resetPassword($code) {
	   	$this->db->update("FLX_USERS", "userID", $this->userID, array("isReset"=>true, "activationCode"=>$code));	

	    return true;
    }
    
    public function clearReset() {
	   	$this->db->update("FLX_USERS", "userID", $this->userID, array("isReset"=>false));	

	    return true;
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