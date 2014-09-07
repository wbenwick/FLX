<?php

/**
 * FILELOGIX REQUEST CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handles the parsing of requests from incoming connections
 *
 */ 
 
class request
{  
    // Will store database connection here
    static private $_instance; 
	private $db;
	private $request;
	private $baseURL;
	private $page;
	private $action;
	private $result;
	private $args;
	
	public function __construct($db, $request, $base) {

		  $this->db = $db;
		  $this->request = $request;
		  $requestNoBase = str_replace('$base', '', $request);
		  $result = explode("/",$requestNoBase);	
		  $this->result = $result;
		  $page = explode("?",$result[2]);
		  $this->page = $page[0];
		  $action = explode("?",$result[3]);
		  $this->action = $action[0];
		  $resultNoBase = explode("?",$requestNoBase);
		  $this->args = explode("/", $resultNoBase[0]);
		  
		  error_log("Request: base is $base, page is $page[0] - $result[1] - $result[2] from $requestNoBase($request).");
		  error_log("Request: args is " . $this->args[1]);
		  
	}


	
	public function getPage() {
		
		  return $this->page;
	}
	
	public function getAction() {
		
		  return $this->action;
	}
	
	public function getArg($num) {
			
		   return $this->result[$num+2];	
	}

	public function getArgs() {
			
		   return $this->args;	
	}	

	public function getRequest() {

		   return $this->request;	
	}	
	
}

?>