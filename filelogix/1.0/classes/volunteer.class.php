<?php

/**
 * FILELOGIX VARIABLE CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle all storing/retrieval/cleanup of session based variables
 *
 */ 
class vars
{  
    // Will store database connection here
	private $db;
	private $sessionID;
	private $volunteerID;
	
	public function __construct($db, $sessionID) {
	  $this->db = $db;
	  $this->sessionID = $sessionID;
		
	}
	
	// Will save a new volunteer record
	public function save() {
		
	}
	
	// Will retrieve a stored value based on key from the table FLX_VARS based on the session ID
	public function get($key) {
	  $keyStr = $this->db->quote($key);
	  $this->users=$this->db->query("select varValue from FLX_VARS where username=\"$this->sessionID\" and varKey=$keyStr");
	  foreach ($this->users as $row) {
		  return $row['varValue'];
      }
		
	}
	
	// Will return all stored keys and value pairs for a given session
//	public function list() {
		
//	}
	
	// Will delete a key entry for a given session
	public function delete($key) {
		$keyStr = $this->db->quote($key);
		$this->db->exec("delete from FLX_VARS where sessionID=\"$this->sessionID\" and varKey=$keyStr");
	}
	
	// Will delete all key entries for a given session
	public function clear() {
		$keyStr = $this->db->quote($key);
		$this->db->exec("delete from FLX_VARS where sessionID=\"$this->sessionID\"");		
	}
	
}

?>