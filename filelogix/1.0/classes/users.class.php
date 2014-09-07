<?php

/**
 * FILELOGIX USERS CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
class users
{  
    // Will store database connection here
	private $db;
	private $user;

	
	public function __construct($db) {
	  $this->db = $db;
	}
 	
	public function getUsername() {
			return $this->username;
    }

	public function getEmailAddress() {
			return $this->emailAddress;
    }
    
    public function getUserID() {
			return $this->userID;
    }    
   
    public function lookupByEmail($emailAddress) {
	  
	  	$r = $this->db->query("select * from FLX_USERS where emailAddress='$emailAddress' and `isActive` is TRUE");
        $results = $r->fetch(\PDO::FETCH_ASSOC);
        $username = $results['username'];
	  
	    if ($username != "") {	
		  	$this->user = new \user($this->db, $username);
		  	return $this->user;
	  	}
	  	
	  	else {
		  	return false;
	  	}
    }

    public function userExists($username) {


		$usernameStr = $this->db->quote($username);
	  	$r = $this->db->query("select * from FLX_USERS where username=$usernameStr and `isActive` is TRUE");  
	    $results = $r->fetch(\PDO::FETCH_ASSOC);
        $username = $results['username'];
	  
	    if ($username != "") {	
			return true;
	  	}
	  	
	  	else {
		  	return false;
	  	}
	  		    
	    return false;
    }
 
    public function emailExists($emailAddressStr) {

		$emailAddressStr = $this->db->quote($emailAddressStr);
		
	  	$r = $this->db->query("select * from FLX_USERS where emailAddress=$emailAddressStr");
      
        $results = $r->fetch(\PDO::FETCH_ASSOC);
      
        $username = $results['username'];
        $userID = $results['userID'];
        $isActive = $results['isActive'];
	  
	    if ($username != "") {	
			if ($isActive) {
				return $userID;
			}
			else {
				return -$userID;
			}
	  	}
	  	
	  	else {
		  	return false;
	  	}
	  		    
	    return false;
    }
       
	public function signup($emailAddress, $firstName, $lastName) {

	    $userExists = $this->emailExists($emailAddress);
		
		if (!$userExists) {
		
			if ($userExists < 0) {

				return $userExists;
			}
			
			else {

				$name = $firstName . " " . $lastName;
			
				$newUserID = $this->db->insert("FLX_USERS", array("emailAddress"=>$emailAddress, "name"=>$name, "firstName"=>$firstName, "lastName"=>$lastName, "status"=>"pending"));
			
				return $newUserID;
			}
		}
		
		else {
			return 0;
		}
	}
   
    public function activate($emailAddress, $password) {
	  
	  $user = $this->lookupByEmail($emailAddress);
	  
	  if ($user) {
		  // if email exists and account is provisioned as new, set password and activate
		    
		  if ($this->userID=$this->user->activate($password)) {
		  	  return $this->user->getUsername();
		  }
		  
		  else {
			  return false;
		  }
	  }
	  
	  else {
	  
		  return false;
	  }
	
	  return -1;	    

	}    
    
    public function forgotPassword($username) {
	    
	    return false;
    }
}
?>