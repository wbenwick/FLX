<?php

/**
 * FILELOGIX RANDOM NUMBER and STRING CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle random number and string generation 
 *
 */

class random
{  
	private $randomStr;
	
	
	public function __construct($min, $max) {
	  $this->randomStr = mt_rand($min,$max);
	}
	
	public function toString() {
		return md5($this->randomStr);
	}
	
	public function toNumber() {
		return $this->randomStr;
	}
	
	public function toDigits() {
		$digitStr = "";
		$digits = str_split($this->randomStr);
		foreach($digits as $digit) {
			$digitStr .= $digit . " ";
		}
		return $digitStr;
	}

	
	public function generate($min, $max) {
		$this->randomStr = mt_rand($min,$max);
		return $this->randomStr;
	}
	
	public function setFooter() {
		
	}
	
	public function password($length=9, $strength=4) {
		
			$vowels = 'aeuy';
			$consonants = 'bdghjmnpqrstvz';

			if ($strength & 1) {
				$consonants .= 'BDGHJLMNPQRSTVWXZ';
			}
			if ($strength & 2) {
				$vowels .= "AEUY";
			}
			if ($strength & 4) {
				$consonants .= '23456789';
			}
			if ($strength & 8) {
				$consonants .= '.$!';
			}
 
			$password = '';
			$alt = time() % 2;
			for ($i = 0; $i < $length; $i++) {
				if ($alt == 1) {
					$password .= $consonants[(mt_rand() % strlen($consonants))];
					$alt = 0;
				} 
				else {
					$password .= $vowels[(mt_rand() % strlen($vowels))];
					$alt = 1;
				}
			}
			
			return $password;
	}
	
	public function token($length=9) {
		
			$vowels = 'aeuy';
			$consonants = 'bdghjmnpqrstvz';

			$strength=4;

			if ($strength & 1) {
				$consonants .= 'BDGHJLMNPQRSTVWXZ';
			}
			if ($strength & 2) {
				$vowels .= "AEUY";
			}
			if ($strength & 4) {
				$consonants .= '23456789';
			}

 
			$token = '';
			$alt = time() % 2;
			for ($i = 0; $i < $length; $i++) {
				if ($alt == 1) {
					$token .= $consonants[(mt_rand() % strlen($consonants))];
					$alt = 0;
				} 
				else {
					$token .= $vowels[(mt_rand() % strlen($vowels))];
					$alt = 1;
				}
			}
			
			return strtoupper($token);
	}
	

	
}

?>