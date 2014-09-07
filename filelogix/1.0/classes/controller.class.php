<?php

/**
 * FILELOGIX CONTROLLER CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle the management and instantiation of controllers on the fly.  All controllers should be found in the controllers/ folder or stored in the database.
 *
 */ 
 
class controller
{  



    // Will store database connection here
	private $db;
	private $controller;
	private $status;
	private $sessionID;
	private $username;
	private $request;
	private $returnURL;
	private $auth;

	public function __construct($db, $request) {

		  $this->db = $db;
		  $this->sessionID = $sessionID;
		  $this->username = $username;
		  $this->request = $request;
		  $this->returnURL = $this->request->getRequest();
		  $this->auth = new auth($this->db);

	}
	
	public function open($controller) {
		
		error_log("Loading $controller...");
		
//		if (file_exists("../controllers/" . $controller . ".php")) {
			
				try {			
//					$this->controller = new $controller($this->db);
//					$this->controller = new Controllers\$controller($this->db);

					if ($controller == "") {
							$controller="home";
					}
					$controller = "FLX\Controllers" . "\\" . $controller;
					$this->controller = new $controller($this->db, session_id(), $_SESSION["userID"], $this->request);
					
					return $this->controller;
				}
				catch (Exception $e) {
					$this->status = $e->getMessage();
					return false;
				}
//		}
	
	}
		
	public function exists($function) {
		
		return method_exists($this->controller, $function);
	}	

	public function go($function, $params = array()) {
		  if ($this->exists($function)) {

			if (!$params) {
				return $this->controller->$function($this->request->getArgs());
			} 	
			else {		
				return $this->controller->$function($params);
			}
		  }
		  else {
			  return false;
		  }
	}

	public function status() {
		
		return $this->status;
	}

	public function getRequest() {
		
		return $this->returnURL;
	}
	
	public function data() {
		
		if ($this->controller) {

//		    $access["admin"] = $this->auth->getAccessByUserID($_SESSION["userID"],"Administrator");

			$globalArray = array();
//			$globalArray["isAdmin"] = $access["admin"][0]["view"];
				
			return array_merge($this->controller->data(), $globalArray);
		}
		else {
			return null;
		}
	}
	
	
	public function transfer() {
		
		if($this->controller) {
			
			return $this->controller->transfer();
		}
		
		else {
			
			return false;
		}
	}
	
	public function view() {

		if($this->controller) {
			
			return $this->controller->view();
		}
		
		else {
			
			return false;
		}
	}
		
	public function close() {
		
	}
	
	
}

?>