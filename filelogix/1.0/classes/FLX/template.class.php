<?php
/**
 * FILELOGIX TEMPLATE OBJECT CLASS (FLX_TEMPLATE and FLX_TEMPLATES)
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
namespace FLX;
  
class template
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
	
}
?>