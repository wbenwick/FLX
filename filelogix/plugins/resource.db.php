<?php

class smarty_resource_db extends smarty_resource_custom {
 // PDO instance
 protected $db;
 // prepared fetch() statement
 protected $fetch;
 // prepared fetchTimestamp() statement
 protected $mtime;

 public function __construct($db) {
  	 $this->db = $db;
     $this->fetch = $this->db->prepare('SELECT modified, source FROM FLX_TEMPLATE WHERE name = :name');
     $this->mtime = $this->db->prepare('SELECT modified FROM FLX_TEMPLATE WHERE name = :name');

 }
 
 /**
  * Fetch a template and its modification time from database
  *
  * @param string $name template name
  * @param string $source template source
  * @param integer $mtime template modification timestamp (epoch)
  * @return void
  */
 protected function fetch($name, &$source, &$mtime)
 {
     $this->fetch->execute(array('name' => $name));
     $row = $this->fetch->fetch();
     $this->fetch->closeCursor();
     if ($row) {
         $source = $row['source'];
         $mtime = strtotime($row['modified']);
     } else {
         $source = null;
         $mtime = null;
     }
 }
 
 /**
  * Fetch a template's modification time from database
  *
  * @note implementing this method is optional. Only implement it if modification times can be accessed faster than loading the comple template source.
  * @param string $name template name
  * @return integer timestamp (epoch) the template was modified
  */
 protected function fetchTimestamp($name) {
     $this->mtime->execute(array('name' => $name));
     $mtime = $this->mtime->fetchColumn();
     $this->mtime->closeCursor();
     return strtotime($mtime);
 }
}

?>