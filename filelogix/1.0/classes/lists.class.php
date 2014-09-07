<?php

/**
 * FILELOGIX LIST CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle all list (key=value pairs) management
 *
 */ 
class lists
{  

  private $db = NULL;
  private $listArray;
 
  function __construct($db)
  {
    $this->db=$db;
  }
 
  function __destruct()
  {

  }
 
  function delete()
  {
  }

  function create() 
  {
	  
  }
 
  public function retrieveByID($id)
  {
  	$this->listArray = array();
    
    $q = "SELECT `key`, `value` FROM `FLX_LIST` WHERE `listID` = '". $id . "'";
    
    $rows = $this->db->query($q);

    foreach ($rows as $row) {
	  $key = $row['key'];
	  $value = $row['value'];
	  $this->listArray[$key] = $value;
    }

    return $this->listArray;

  }
 
  public function retrieveByName($name)
  {
  	$this->listArray = array();
    
    $q = "SELECT id FROM `FLX_LISTS` WHERE `name` = '". $name . "' limit 1";
    
    $rows = $this->db->query($q);

    foreach ($rows as $row) {
	  $listID = $row['id'];
	  $this->retrieveByID($listID);
    }

    return $this->listArray;

  }

	
	
}

?>
