<?php

/**
 * FILELOGIX MAIL CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle the composing, sending and downloading of multi-part MIME messages with plain text and Smarty template based content, plus attachments
 *
 */ 

namespace FLX;

//include_once("/var/www/html/lib/filelogix/plugins/CSssToInlineStyles.php");
 
class mail
{  
    // Will store database connection here
	private $db;
	private $template;
	private $to = array();
	private $cc = array();
	private $bcc = array();
	private $from;
	private $replyto;
	private $subject;
	private $messages = array(array());
	private $randomHash;
	private $didSend;
	private $vars = array();
	private $inliner;
	
	public function __construct($db) {

		  $this->db = $db;

		  $this->randomHash = md5(date('r', time()));

		  $this->messages[0]["type"] = "text/plain";
		  $this->messages[0]["charset"] = "iso=8859-1";
		  $this->messages[0]["encoding"] = "7bit";
		  $this->messages[0]["content"] = "";
		  $this->messages[1]["type"] = "text/html";
		  $this->messages[1]["charset"] = "utf-8";
		  $this->messages[1]["encoding"] = "7bit";
		  $this->messages[1]["content"] = "<html><head></head><body></body></html>";
		  
		  $this->from = "no-reply@filelogix.com";
		  		  
		  $this->vars["boundary"] = "_NextPart-" . $this->randomHash;
     	  $this->template = new \template($this->db, "MAIL_DEFAULT", BASE);
     	  
 //    	  $this->inliner = new \TijsVerkoyen\CssToInlineStyles\CSSToInlineStyles(null,null);
		  
	}
	
	public function send($mailQueue) {
	
		$to = $this->to[0];
		$this->vars["messages"] = $this->messages;
		$subject = $this->subject;

//		error_log("Messages:" . print_r($this->messages));


		$message = $this->template->fetch($this->vars);
		$headers = "";
		$params = null;
		$ccStr = implode(",", $this->cc);
		$bccStr = implode(",", $this->bcc);

		$headers = 'MIME-Version: 1.0' . "\r\n";

		$headers .= "From: " . $this->from . "\r\n";
		if (strlen($ccStr)>0) {
			$headers .= "cc: " . $ccStr . "\r\n";
		}
		if (strlen($bccStr)>0) {
			$headers .= "bcc: " . $bccStr . "\r\n";			
		}
		$headers .= 'Content-type: multipart/mixed; ' . "boundary=\"----=" . $this->vars["boundary"] . "\"\r\n";
	
		$this->didSend = mail($to, $subject, wordwrap($message,70,"\n",FALSE), $headers);
		
	}

	public function text($content) {

		return $this->content(0,$content);			
	
	}

	public function textByTemplate($templateName, $content = array()) {

		$template = new \template($this->db, $templateName, BASE);
		if ($template->exists("db:" . $templateName)) {
			return $this->content(0,$template->fetch($content));
		}
		else {
			return false;			
		}	
	}		
	
	public function html($content) {
	
		return $this->content(1,$content);			
	}
	
	public function htmlByTemplate($templateName, $content = array()) {
	
		$template = new \template($this->db, $templateName, BASE);
		if ($template->exists("db:" . $templateName)) {
			return $this->content(1,$template->fetch($content));
		}
		else {
			return false;			
		}
	}
	
	private function content($index, $content) {
		
		if (!is_null($content)) {
			$this->messages[$index]["content"]= $content;
		}
		
		return $this->messages[$index]["content"];			
	}

	public function template($template) {
	
		if (strlen($template)>0) {
			$this->template = $template;
			if ($this->template->exists("db:" . $template)) {
		   	    $this->template = new \template($this->db, $template, BASE);
		   	}
		}
		
		return $this->template;
		
	}
	

	public function subject($subject) {
	
		if (strlen($subject)>0) {
			$this->subject = $subject;
		}
		
		return $this->subject;
		
	}
	
	public function to($to) {
	
		if (strlen($to)>0) {
			array_push($this->to, $to);
		}
		
		return $this->to;
		
	}

	public function bcc($bcc) {
	
		if (strlen($bcc)>0) {
			array_push($this->bcc, $bcc);
		}
		
		return $this->bcc;
		
	}

	public function cc($cc) {
	
		if (strlen($cc)>0) {
			array_push($this->cc, $cc);
		}
		
		return $this->cc;
		
	}
			
	public function from($from) {
		if (strlen($from)>0) {
			$this->from = $from;
		}
		
		return $this->from;		
	}
	
	public function addCC($cc) {
		
	}
	
	public function addBCC($bcc) {
		
	}
	
	public function addTo($to) {
	
		array_push($this->to, $to);
		
	}

	public function addContent($contentType, $content, $charset = "utf-8", $encoding = "7bit") {

		$message = array();
		
		if (!is_null($content)) {			
		  $message["type"] = $contentType;
		  $message["charset"] = $charset;
		  $message["encoding"] = $encoding;
		  $message["content"] = $content;
		}

		array_push($this->messages, $message);		
	}

	public function addFileByURL($fileurl, $filename) {
		    $file = file_get_contents($fileurl);
			
			return $this->addContent("application/octet-stream; name=\"$filename\"",base64_encode($file), "utf-8", "base64\r\nContent-Disposition: attachment");  	
	}

	public function addFileByPath($filepath, $filename) {

		
	}
		
	public function open() {
		
	}

	public function finish() {
		
	}	
	
	public function retrieve() {
		
	}
		
}

?>