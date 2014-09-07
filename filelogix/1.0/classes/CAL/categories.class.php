<?php
/**
 * FILELOGIX CALENDAR CATEGORIES CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
  
namespace CAL;
  
class categories
{  
    // Will store database connection here
	private $db;
	private $sessionID;

	
	public function __construct($db) {
	  $this->db = $db;
	  $this->sessionID = session_id();
		
	}
	
	public function get($id) {
	
	}
	
	// Create a new location in the CAL_LOCATIONS table, return the location ID
	
	public function create($params = array()) {
	
	}
	
	
	public function delete($id) {
		
	}
	
	public function update($id, $params = array()) {
		
	}
	
//	Retrieve a list of all categories	
	
	public function retrieve() {
		
	}
	
}
?>