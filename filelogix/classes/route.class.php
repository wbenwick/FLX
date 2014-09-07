<?php

/**
 * FILELOGIX ROUTE CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle the routing of requests
 *
 */ 
 
class route
{  



    // Will store database connection here
	private $db;
	private $controller;
	private $status;
	private $sessionID;
	private $request;
	private $controller;
	private $page;
	private $returnURL;
	private $auth;
	private $config;

	public function __construct($globals) {

		  $this->db = $globals["db"];
		  $this->sessionID = $globals["sessionID"];
		  $this->request = $globals["request"];
		  $this->page = $globals["page"];
		  $this->config = $globals["config"];
		  $this->controller = $globals["controller"];
		  
		  $this->returnURL = $this->request->getRequest();
		  $this->auth = new auth($this->db);

	}
	
	public function getController() {
		
		$hostname = $this->request->getHostName();
		$params = $this->request->getParams();
		
		$hostnameStr = str_replace('*','%',$hostname);

		  $matches=$this->db->query("select * from FLX_ROUTES where replace(hostname,'*','') like '$hostnameStr'");
		  foreach ($matches as $match) {
			  $routeID = $match['id'];

			  if (1) {

				  return true;
			  }
			  else {
				  return false;
			  }
	      }
		

	}
	
	public function getView() {
		
	}
	
	public function getAction() {
		
	}
	
}

?>