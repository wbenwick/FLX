<?php

/**
 * FILELOGIX TOKENS CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
class token
{  
    // Will store database connection here
	private $db;
	private $tokenID;
	private $sessionID;
	private $tokenStr;
	private $isOneTime;
	
	public function __construct($db, $sID, $isOneTime) {
	  $this->db = $db;
	  $this->sessionID = $sID;
	  $this->tokenStr = md5($sID . $_SERVER["REMOTE_PORT"]);
	  $this->isOneTime = $isOneTime;
	  
      $sidStr = $this->db->real_escape_string($sID);

      error_log("Saving Token");
    
      $q = "INSERT INTO `FLX_TOKENS` (`tokenStr`, `refID`, `source`, `tokenStamp`, `isOneTime`) VALUES ('$this->tokenStr', $sidStr, 'FLX_SESSIONS', now(), '$this->isOneTime')";
  
      error_log($q);
      $this->db->exec($q);
	  $this->tokenID = $this->db->lastInsertID(null);

	  error_log("Token ID: $this->tokenID");
	}

	
	public function getToken() {
		
		return $this->tokenStr;
	}
	
	public function useOnce() {
	     $u = "UPDATE `FLX_TOKENS` set `isOneTime` = true where `id` = '$this->tokenID'";
			   	 
  	     error_log("Updating Token to Single Use");
	     error_log($u);
	   	  
		 $this->db->exec($u);
		 
		 $this->isOneTime = true;
		 
		 return true;
	}
	
	public function useMany() {
	     $u = "UPDATE `FLX_TOKENS` set `isOneTime` = false where `id` = '$this->tokenID'";
			   	 
  	     error_log("Updating Token to Use Many");
	     error_log($u);
   	  
		 $this->db->exec($u);
		 
		 $this->isOneTime = false;
		 
		 return true;
	}
	
	public function isOneTime() {
	  $q = "SELECT * FROM `FLX_TOKENS` WHERE `tokenStr` = '$this->tokenStr' LIMIT 1";
	 	    
 	 	    
	  $r = $this->db->query($q);
	  
      if($this->db->rows() == 1) { 

	       $results=$r->fetch(PDO::FETCH_ASSOC);
	       $id = $results['id'];
	       $isOneTime = $results['isOneTime'];

	       if ($isOneTime) {	
		       	return true;
  	 	   }
		   else {
			   return false;
		   }
	  }
      else
	  {
	      return false;
      }

	}
	
	public function useToken($tID, $refID) {
		
	  $refIDStr = $this->db->real_escape_string($refID);
      $tIDStr = $this->db->real_escape_string($tID);

	  $q = "SELECT * FROM `FLX_TOKENS` WHERE `tokenStr` = $tIDStr and `status`='' and `refID`=$refIDStr LIMIT 1";
	 	    
      error_log("Using Token");
      error_log($q);
	 	    
	  $r = $this->db->query($q);
	  
      if($this->db->rows() == 1) { 
	       error_log("TRUE");

	       $results=$r->fetch(PDO::FETCH_ASSOC);
	       $id = $results['id'];
	       $isOneTime = $results['isOneTime'];

	       if ($isOneTime) {	
			   $u = "UPDATE `FLX_TOKENS` set `status` = 'used' where `id` = $id";
			   
			   error_log("Updating Token");
			   error_log($u);

			   $this->db->exec($u);
		  }
		  
	      return true;
      }
      else
	  {
	      error_log("FALSE");	
	      return false;
      }

		
		

	}
 	    

}
?>