<?php
/**
 * FILELOGIX FORMS CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
  
class forms
{  
    // Will store database connection here
	private $db;
	private $sessionID;

	
	public function __construct($db) {
	  $this->db = $db;
	  $this->sessionID = session_id();
		
	}
	
	// Create a new template in the FLX_TEMPLATES table
	
	public function create($name) {
		
	}
	
	
	// Save a new template version in the FLX_TEMPLATE table
	
	public function save($templateID, $source) {
		
		$this->db->insert("FLX_TEMPLATE", array("templateID"=>$templateID, "source"=>$source));
		
	}
	
	public function delete($id) {
		
	}
	
	public function update($id, $params = array()) {
		
	}
	
	// Maps form input fields to database fields both in array format by corresponding field name.
	
	public function map($fields = array(), $values = array()) {
	
		$results = array();
	
		foreach ($fields as $fieldName => $columnName) {
			$results[$columnName] = $values[$fieldName];
		}
		
		return $results;
		
	}
	
	// Maps database fields to form input fields both in array format by corresponding field name.
	
	public function unmap($fields = array(), $data = array()) {
	
		$results = array();
	
		foreach ($fields as $fieldName => $columnName) {
			$results[$fieldName] = $data[$columnName];
		}
		
		return $results;
		
	}
	
}
?>