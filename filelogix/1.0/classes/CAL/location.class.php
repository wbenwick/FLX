<?php
/**
 * FILELOGIX CALENDAR LOCATION CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 

namespace CAL;
  
class location
{  
    // Will store database connection here
	private $db;
	private $sessionID;

	
	public function __construct($db) {
	  $this->db = $db;
	  $this->sessionID = session_id();
		
	}
	
	public function openByID($id) {
	
	}
	
	// Create a new location in the CAL_LOCATIONS table, return the location ID
	
	public function create($params = array()) {
	
	}
	
	// Create a new record in the CAL_LOCATION table that is a many-to-one relationshiop with the CAL_LOCATIONS table
	
	public function add($id, $params = array() ) {
		
		
	}
	
	public function delete($id) {
		
	}
	
	public function update($id, $params = array()) {
		
	}
	
}
?>