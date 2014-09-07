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
	  $this->userID = $_SESSION["userID"];
	}
	
	/**
  	 * Log a user in based on password
  	 *
  	 * @return bool true for success, false for failure
  	 */
	
	public function login($username, $password) {
	
//	  session_regenerate_id();
	
	  $usernameStr = $this->db->quote($username);

	  $this->users=$this->db->query("select *,md5('$password') as pwd from FLX_USERS where username=$usernameStr and status=''");

	  error_log("Login: " . $this->db->lastQuery());

	  error_log("Authenticating $username...");
	  
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
		  if ($row['password'] == $row['pwd']) {
		  	  $this->create();
			  return true;
		  }
		  else {
			  return false;
		  }
      }
		

		return false;
	}
	
	/**
  	 * Log the current user out
  	 *
  	 * @return bool 
  	 */
		
	public function logout() {
	
		error_log("Logging out..." . $this->sessionID);
		
		session_regenerate_id();
		
		$this->delete();
		return true;

	}

	
	/**
  	 * Create an entry in the FLX_SESSION table to show the session is active and the userID it is assigned to.
  	 *
  	 * @return bool 
  	 */
		
	public function create() {
		
		$_SESSION["userID"] = $this->userID;
	
		$nowStr = date('Y-m-d h:i:s a', time());

	    $this->db->insert("FLX_SESSION", array("sessionID"=>session_id(), "userID"=>$this->userID, "created"=>$nowStr));	
	  	
//	   	error_log("Inserting Session: " . $this->db->lastQuery());	  	
	  	
		return true;

	}

	/**
  	 * Delete an entry in the FLX_SESSION table to inactivate the session.
  	 *
  	 * @return bool 
  	 */
		
	public function delete() {
		
		error_log("Deleting session: " . $this->sessionID);
	    $this->db->delete("FLX_SESSION", "sessionID", $this->sessionID, array("userID"=>$this->userID));	
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

	  error_log("Validating... " . "select * from FLX_SESSION where sessionID = '" . session_id() . "'");
	  $sessions=$this->db->query("select * from FLX_SESSION where sessionID = '" . session_id() . "'");
	  foreach ($sessions as $session) {
		  error_log("Validate: " . $this->userID . "=" . $session['userID']);
//		  if ($userID == $session['userID']) {
		  if (intval(trim($this->userID)) == intval(trim($session['userID']))) {
	   		  error_log("Validated.");

			  return true;
		  }
		  else {
	   		  error_log("Not Validated. (" . $this->userID .") != (" . $session['userID'] . ")");
			  return false;
		  }
      }

  	  error_log("Validation Error.");
      
      return false;
      
    }
    
	/**
  	 * Return the current user's username
  	 *
  	 * @return string
  	 */
	
	public function getUsername() {
	
			return $this->username;

    }

	
	public function getUserID() {
	
			return $this->userID;

    }

	/**
  	 * Return the current user's abbreviated (short) name
  	 *
  	 * @return string
  	 */
	
	public function getShortName($userID) {
	
	   $q = "select * from FLX_USERS where userID= '$userID'";

	   $r=$this->db->query($q);	   

       $results=$r->fetch(PDO::FETCH_ASSOC);

//  	   error_log($this->db->lastQuery());

       return $results["shortName"];
		
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

    public function getAccessByUsername($username, $activity) {
	    
	   $q = "select * from FLX_ACCESS left join FLX_GROUP using (accessID) left join FLX_MEMBERS on (FLX_MEMBERS.groupID=FLX_GROUP.groupID) left join FLX_USERS on (FLX_USERS.userID=FLX_MEMBERS.userID) where FLX_MEMBERS.memberID is not NULL and FLX_ACCESS.activity = '$activity' and FLX_USERS.username = '$username'";

	   $r=$this->db->query($q);	   

       $results=$r->fetchAll();

 	   error_log("Auth: " . $this->db->lastQuery());

       return $results;
       
    }

    public function getAccessByUserID($userID, $activity) {
	    
	   $q = "select * from FLX_ACCESS left join FLX_GROUP using (accessID) left join FLX_MEMBERS on (FLX_MEMBERS.groupID=FLX_GROUP.groupID) where FLX_MEMBERS.memberID is not NULL and FLX_ACCESS.activity = '$activity' and FLX_MEMBERS.userID= '$userID'";

	   $r=$this->db->query($q);	   

       $results=$r->fetchAll();

 	   error_log("Auth: " . $this->db->lastQuery());

       return $results;
       
    }

}
?>