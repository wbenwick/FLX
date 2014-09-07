<?php

/**
 * FILELOGIX EDITOR CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
class editor
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $username;
	private $view;
	private $auth;
	private $vars = array();
	private $lists;

	/**
  	 * Create instance, load current info based on session info
  	 *
  	 * @return bool
  	 */
	
	public function __construct($db) {
	  $this->db = $db;
	}
	
	public function getTemplate($name) {
	
	  $r=$this->db->query("select * from FLX_TEMPLATE where name=\"$name\"");
	  $template=$r->fetch(\PDO::FETCH_ASSOC);

	  return $template["source"];
		
	}

	public function getTemplates($name) {
	
	  $r=$this->db->query("select *,name as id, editWith as editor, modified as lastUpdated from FLX_TEMPLATE");
      $results=$r->fetchAll();

	  return $results;
		
	}


}
?>
