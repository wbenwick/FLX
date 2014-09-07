<?php

/**
 * FILELOGIX VIEW CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle the rendering of templated views  
 *
 */ 
 
class view
{  
    // Will store database connection here
	private $db;
	private $pageID;
	private $page;
	private $template;
	
	public function __construct($db, $page, $base) {

		  $this->page = $page;

		  $this->template = new template($db, $page, $base);
	
		
	}
	
	public function assign($params = array()) {
		
		$this->template->assign($params);
	}
	

	public function toHTML($params = array()) {
		$this->assign($params);
		if( !$this->template->exists("db:".$this->page) ){
		
			$result=$this->template->display("debug.tpl");

		}
		else {			
		
			$result=$this->template->display("db:".$this->page);

		}

//		echo $this->template->process($result, $params);
		
	}
	
	public function setHeader() {
		
	}
	
	public function setFooter() {
		
	}
	
	
}

?>