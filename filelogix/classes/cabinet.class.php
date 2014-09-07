<?php

/**
 * FILELOGIX CABINET CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
class cabinet
{  

	private $db;

	public function __construct($db) {
		$this->db = $db;
	}
}