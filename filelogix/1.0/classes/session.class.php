<?php

/**
 * FILELOGIX SESSION CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 * derived in part from Stephen McIntyre's implementation (http://stephenmcintyre.net)
 * @description This class will handle all session management
 *
 */ 
class session
{  

  private $alive = true;
  private $db = NULL;
  private $sessionID = "";
 
  function __construct($db)
  {
    $this->db=$db;
  
	if (!isset($_SERVER["HTTP_COOKIE"])) {
	    ini_set("session.use_cookies",0);
		ini_set("session.use_only_cookies",0);
		ini_set("session.use_trans_sid",1); 
		error_log("Cookies are NOT SET." .  $_COOKIE["PHPSESSID"] . " " . $_POST["PHPSESSID"]);
    
    }
    else {
	    ini_set("session.use_cookies",1);
		ini_set("session.use_only_cookies",1);
		ini_set("session.use_trans_sid",1);		    
		error_log("Cookies are SET. " . $_COOKIE["PHPSESSID"]);
    }
    
    $browser = get_browser(null, true);
    $cookies = $browser["cookies"];
    error_log("Cookies ok? $cookies");
    
    session_set_save_handler(
      array(&$this, 'open'),
      array(&$this, 'close'),
      array(&$this, 'read'),
      array(&$this, 'write'),
      array(&$this, 'destroy'),
      array(&$this, 'clean'));
 
      session_start();

    if (0) {
	    session_regenerate_id();
    }
 
  


  }
 
  function __destruct()
  {
    if($this->alive)
    {
      session_write_close();
      $this->alive = false;
    }
  }
 
  function delete()
  {
    if(ini_get('session.use_cookies'))
    {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
      );
    }
 
    session_destroy();
 
    $this->alive = false;
  }
 
  public function open()
  {    
    $this->db->status()
      OR die('Could not connect to database.');
      
    error_log("Opening Session");
 
    return true;
  }
 
  public function close()
  {
    return true;
  }
 
  public function read($sid)
  {
  
    
    $q = "SELECT `data` FROM `FLX_SESSIONS` WHERE `sessionID` = ". $this->db->real_escape_string($sid) . " LIMIT 1";
    $u = "UPDATE FLX_SESSIONS` set `modified` = now() where `sessionID` = " . $this->db->real_escape_string($sid);

    error_log("Reading Session");

    $this->db->exec($u);
    
    $r = $this->db->query($q);
    if($this->db->rows() == 1)
    { 
      error_log($q);
      $results=$r->fetch(PDO::FETCH_ASSOC);
      error_log($results['data']);
      return $results['data'];
    }
    else
    {
      error_log($q);

      return '';
    }
  }
 
  public function write($sid, $data)
  {
    $sidStr = $this->db->real_escape_string($sid);
    $dataStr = $this->db->real_escape_string($data);

    error_log("Writing Session");

    $this->sessionID = $sidStr;
    
    $q = "REPLACE INTO `FLX_SESSIONS` (`sessionID`, `data`, `modified`) VALUES (".$this->db->real_escape_string($sid).", $dataStr, now())";
  
    error_log($q);
    return $this->db->exec($q);
    
  }
 
  public function destroy($sid)
  {
    $q = "DELETE FROM `FLX_SESSIONS` WHERE `sessionID` = " . $this->db->real_escape_string($sid); 
    $rows = $this->db->exec($q);
 
    $_SESSION = array();
  
    error_log("Destroying Session");

    return $rows;
  }
 
  public function clean($expire)
  {
    $q = "DELETE FROM `FLX_SESSIONS` WHERE DATE_ADD(`modified`, INTERVAL ".(int) $expire." SECOND) < NOW()"; 
 
    error_log("Cleaning Session");

    return $this->db->exec($q);
  }
	
  public function isValid($sid) {
      $sidStr = $this->db->real_escape_string($sid);
      $q = "SELECT * FROM `FLX_SESSIONS` WHERE `sessionID` = ". $sidStr . " LIMIT 1";

      error_log("Checking Session " . $sidStr );
	 	    
	  $r = $this->db->query($q);
	  
      if($this->db->rows() == 1)
	  { 
	      error_log("TRUE");
	      $results=$r->fetch(PDO::FETCH_ASSOC);
	      error_log($results['sessionID']);
	      return true;
      }
      else
	  {
	      error_log("FALSE");	
	      return false;
      }

  }
	
	
}

?>