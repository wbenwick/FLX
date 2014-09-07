<?php

/**
 * FILELOGIX TEMPLATE CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle the configuration and generation of templated views
 *
 */ 

 
require_once(SMARTY_DIR . 'Smarty.class.php');
require_once('plugins/resource.db.php');
//require_once(SMARTY_DIR . "sysplugins/smarty_internal_debug.php");
 

 
class template
{  
    // Will store database connection here
	private $db;
	private $smarty;
	private $template;
	
	public function __construct($db, $template) {

		  $this->smarty = new Smarty();
		  $this->template = $template;
		  $this->db = $db;
		
		  if (defined('BASEDIR')) {
		  	  error_log("Setting Smarty to use " . BASEDIR);
		  	  
			  $this->smarty->setTemplateDir(BASEDIR . 'templates/');
			  $this->smarty->setCompileDir(BASEDIR . 'smarty/templates_c/');
			  $this->smarty->setConfigDir('../smarty/configs/');
			  $this->smarty->setCacheDir(BASEDIR . 'smarty/cache/');
		  }
		  else {			  
		  	  error_log("Setting Smarty to use default config.");

			  $this->smarty->setTemplateDir('templates/');
			  $this->smarty->setCompileDir('../smarty/templates_c/');
			  $this->smarty->setConfigDir('../smarty/configs/');
			  $this->smarty->setCacheDir('../smarty/cache/');		  }
	  
//		  $this->smarty->debugging = true;
	  
	      $this->smarty->registerResource('db', new smarty_resource_db($db));
	      
		
			//** un-comment the following line to show the debug console
		  //$this->smarty->debugging = true;
			
			
			//$smarty->display('index.tpl');
			
			// using resource from php script
			//$smarty->display("db:index.tpl");

		
	}
	
	public function debug() {
	
	}
		
	public function exists($template) {
		
		return $this->smarty->templateExists($template);
	}	

		
	public function assign($params = array()) {
			
		foreach($params as $key => $value) {
		 	 $this->smarty->assign($key,$value);
		 	 error_log("template: assigning $key - $value");

		}

/*
		  $this->smarty->assign('Name','Ned');
		  $this->smarty->assign('test','This is a TEST');
		  $this->smarty->assign('loginAlert', $loginAlert);
		  $this->smarty->assign('showLogin','true');
		  $this->smarty->assign('loginUsername','wbenwick@filelogix.net');
*/
	}
	

	public function display($params = array()) {
		$this->assign($params);
//		$this->smarty->display($this->template . ".tpl");
		if( !$this->smarty->templateExists("db:".$this->template) ){
//			$result=$this->smarty->display("debug.tpl");
			$result=$this->smarty->display("db:error");

		}
		else {			
			$result=$this->smarty->display("db:".$this->template);
		}
//		$this->smarty->display("db:home.tpl");
	}
	
	public function fetch($params = array()) {
		$this->assign($params);
//		$this->smarty->display($this->template . ".tpl");
		if( !$this->smarty->templateExists("db:".$this->template) ){
//			$result=$this->smarty->fetch("debug.tpl");
			$result=$this->smarty->display("db:error");

		}
		else {			
		
			error_log("template: fetching from db " . $this->template);
			$result=$this->smarty->fetch("db:".$this->template);
		}
//		$this->smarty->display("db:home.tpl");
	
		return $result;
	}
	
	public function process($template, $params = array()) {
		$this->assign($params);		
		
		$result=$this->smarty->fetch($template);
			
		return $result;		
	}
	
	public function toJSON() {
		
	}

	
}

?>