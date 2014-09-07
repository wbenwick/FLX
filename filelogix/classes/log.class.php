<?php

/**
 * FILELOGIX LOGGING CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle all logging for the system
 *
 */ 
class log
{  
    // Will store database connection here
	private $db;
	private $sessionID;
	
	public function __construct($db, $sessionID) {
	  $this->db = $db;
	  $this->sessionID = $sessionID;
		
	}
	
	public function insert($messageType, $message) {
		
	}
	
	public function error($message) {
		
	}
	
	public function info($message) {
		
	}
	
	public function success($message) {
		
	}
	
	public function show() {
		
	}
	
}

?>