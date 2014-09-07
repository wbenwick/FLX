<?php

/**
 * FILELOGIX PAGE CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class acts as the controller or presentation layer  
 *
 */ 
class page
{  
  
    // Will store database connection here
	private $db;
	private $pageID;
	private $connID;
	private $sessionID;
	private $URL;

	/**
  	 * Create instance, load current info based on session info
  	 *
  	 */
	
	public function __construct($db, $cID) {
	  $this->db = $db;
	  $this->connID = $cID;
	  $this->sessionID = session_id();	
	}
	
	/**
  	 * Return route details for a matching url
  	 *
  	 * @return string
  	 */
	
	public function lookup($url) {
	
		$this->URL = $url;
		
		$lookup = array();
		
		$lookup["controller"] = "login";
		
		return $lookup;
	}

	/**
  	 * Check if route is secure
  	 *
  	 * @return bool
  	 */	

	public function isSecure() {
		
		
	}

	/**
  	 * Get the model
  	 *
  	 * @return model String
  	 */
  	 		
	public function controller($request) {
		return $request->getPage();
	}	

	public function view($request) {
		return "CAL_SEARCH";
	}	
	
}

?>