<?php

/**
 * FILELOGIX CALENDAR CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
  
namespace CAL;

class calendar
{  
    // Will store database connection here
	private $db;
	private $sessionID;

	
	public function __construct($db) {
	  $this->db = $db;
	  $this->sessionID = session_id();
		
	}

	public function getCalendars() {
	  
	  $calendars=$this->db->query("select * from CAL_MASTER where bActive is TRUE");

	  error_log($this->db->lastQuery());
		
	  return $calendars;

		
	}	

	public function getAllEvents() {
	  
	  $calendars=$this->db->query("select * from CAL_EVENT where bActive is TRUE");

	  error_log($this->db->lastQuery());
		
	  return $calendars;

		
	}	
	
	public function getAllEventsByDate($from, $to) {
		
		
	}
	
	public function getAllEventsByLocation($location, $from, $to) {
		
		
	}
	
	public function getAllEventsByCalendar($calendar, $from, $to) {
		
	}
	
// OLD FUNCTIONS BELOW

	public function getEventsByAccount($accountID) {
	  
	  $events=$this->db->query("select *,CAL_EVENTS.title as eventTitle, CAL_EVENT.name as eventName, concat(CAL_EVENTS.title, ' ' , CAL_EVENT.name) as eventDescription, concat(REG_CHILD.firstName, ' ', REG_CHILD.lastName) as childName, CAL_EVENT.startStamp as eventDateTime from CAL_EVENT left join CAL_EVENTS on (CAL_EVENT.eventID=CAL_EVENTS.id) left join REG_PLAYERS on (CAL_EVENT.refID=REG_PLAYERS.id) left join REG_CHILD on (REG_CHILD.id=REG_PLAYERS.childID) where CAL_EVENT.source=\"REG_PLAYERS\" and REG_CHILD.accountID='$accountID'");

//	  $results=$events->fetchAll();
	  
	  error_log($this->db->lastQuery());
		
//	  return $results;
	  
	  return $events;
		
	}	

	public function getEventsByChild($childID) {
	  
	  $events=$this->db->query("select *,CAL_EVENTS.title as eventTitle, CAL_EVENT.name as eventName, concat(CAL_EVENTS.title, ' ' , CAL_EVENT.name) as eventDescription, concat(REG_CHILD.firstName, ' ', REG_CHILD.lastName) as childName, CAL_EVENT.startStamp as eventDateTime from CAL_EVENT left join CAL_EVENTS on (CAL_EVENT.eventID=CAL_EVENTS.id) left join REG_PLAYERS on (CAL_EVENT.refID=REG_PLAYERS.id) left join REG_CHILD on (REG_CHILD.id=REG_PLAYERS.childID) where CAL_EVENT.source=\"REG_PLAYERS\" and REG_CHILD.id='$childID'");

//	  $results=$events->fetchAll();
	  
	  error_log($this->db->lastQuery());
		
//	  return $results;
	  
	  return $events;
		
	}
		
	public function getEventTitle($eventID) {
	  
	  $r=$this->db->query("select * from CAL_EVENTS where id='$eventID'");

	  $results=$r->fetch(PDO::FETCH_ASSOC);

	  error_log($this->db->lastQuery());
		
	  error_log("Title: " . $results["title"]);
	  	
	  return $results["title"];

		
	}

	public function getPaymentMessage($eventID) {
	  
	  $r=$this->db->query("select payMessage from CAL_EVENTS where id='$eventID'");

	  $results=$r->fetch(PDO::FETCH_ASSOC);

	  error_log($this->db->lastQuery());
			  	
	  return $results["payMessage"];
		
	}
	
	public function getSlotName($slotID) {
	  
	  $r=$this->db->query("select * from CAL_EVENT where id='$slotID'");

	  $results=$r->fetch(PDO::FETCH_ASSOC);

	  error_log($this->db->lastQuery());
			  	
	  return $results["name"];

		
	}
	
	public function getEventSourceBySlotID($slotID) {
	  
	  $r=$this->db->query("select CAL_EVENTS.source from CAL_EVENTS left join CAL_EVENT on (CAL_EVENT.eventID=CAL_EVENTS.id) where CAL_EVENT.id='$slotID'");

	  $results=$r->fetch(PDO::FETCH_ASSOC);

	  error_log($this->db->lastQuery());
		
	  error_log("Source: " . $results["source"]);
	  	
	  return $results["source"];

		
	}

	public function getEventBySlot($slotID) {
	  
	  $r=$this->db->query("select CAL_EVENT.eventID from CAL_EVENTS left join CAL_EVENT on (CAL_EVENT.eventID=CAL_EVENTS.id) where CAL_EVENT.id='$slotID'");

	  $results=$r->fetch(PDO::FETCH_ASSOC);

	  error_log($this->db->lastQuery());
			  	
	  return $results["eventID"];

		
	}

	public function getAmountBySlot($slotID) {
	  
	  $r=$this->db->query("select CAL_EVENT.cost from CAL_EVENTS left join CAL_EVENT on (CAL_EVENT.eventID=CAL_EVENTS.id) where CAL_EVENT.id='$slotID'");

	  $results=$r->fetch(PDO::FETCH_ASSOC);

	  error_log($this->db->lastQuery());
		
	  	
	  return $results["cost"];

		
	}

	public function getSourceBySlot($slotID) {
	  
	  $r=$this->db->query("select CAL_EVENT.source from CAL_EVENT where CAL_EVENT.id='$slotID'");

	  $results=$r->fetch(PDO::FETCH_ASSOC);

	  error_log($this->db->lastQuery());
			  	
	  return $results["source"];

		
	}
			
	public function getSlots($eventID) {
	  
	  $calendars=$this->db->query("select *,count(if(CAL_EVENT.status = '', CAL_EVENT.status,NULL)) as openCnt, count(if(CAL_EVENT.status != '', CAL_EVENT.status,NULL)) as usedCnt from CAL_EVENT left join CAL_EVENTS on (CAL_EVENT.eventID=CAL_EVENTS.id) where CAL_EVENT.eventID='$eventID' group by CAL_EVENT.name order by groupOrder asc, slotOrder asc, CAL_EVENT.startStamp asc");

	  error_log($this->db->lastQuery());
		
	  return $calendars;
	
	}

	public function getSlotByEvent($source, $refID, $eventID) {
	  
	  $slots=$this->db->query("select * from CAL_EVENT left join CAL_EVENTS on (CAL_EVENT.eventID=CAL_EVENTS.id) where CAL_EVENT.eventID='$eventID' and CAL_EVENT.source='$source' and CAL_EVENT.refID='$refID'");

	  error_log($this->db->lastQuery());
		
	  return $slots;
	
	}

	public function getSlotsByGroupName($eventID, $groupName) {
	  
	  $calendars=$this->db->query("select *,CAL_EVENT.id as SID, count(if(CAL_EVENT.status = '', CAL_EVENT.status,NULL)) as openCnt, count(if(CAL_EVENT.status != '', CAL_EVENT.status,NULL)) as usedCnt from CAL_EVENT left join CAL_EVENTS on (CAL_EVENT.eventID=CAL_EVENTS.id) where CAL_EVENT.eventID='$eventID' and CAL_EVENT.groupName='$groupName' group by CAL_EVENT.name order by slotOrder asc, CAL_EVENT.startStamp asc");

	  error_log($this->db->lastQuery());
		
	  return $calendars;
	
	}

		
	public function getSlotGroups($eventID) {
	  
	  $calendars=$this->db->query("select *,count(if(CAL_EVENT.status = '', CAL_EVENT.status,NULL)) as openCnt, count(if(CAL_EVENT.status != '', CAL_EVENT.status,NULL)) as usedCnt from CAL_EVENT left join CAL_EVENTS on (CAL_EVENT.eventID=CAL_EVENTS.id) where CAL_EVENT.eventID='$eventID' group by groupName order by groupOrder asc");

	  error_log($this->db->lastQuery());
		
	  return $calendars;

		
	}	
	
	public function reserve($slotID, $source, $sourceKey, $refID) {
								
	  if($refID>0) {							
								
			  $result = $this->db->updateWhere("CAL_EVENT", "id", $slotID, array("source"=>$source, "refID"=>$refID, "status"=>"open", "sourceKey"=>$sourceKey), " and refID='0'");	
		
			  error_log($this->db->lastQuery());
			  
			  $r=$this->db->query("select * from CAL_EVENT where id=\"$slotID\" and source=\"$source\" and sourceKey=\"$sourceKey\" and refID=\"$refID\" limit 1");
		
			  $results=$r->fetch(PDO::FETCH_ASSOC);
		
			  error_log($this->db->lastQuery());
					  	
			  if ($results["refID"]==$refID) {
			  		if ($results["invoiceID"]>0) {
				  		error_log("Reserve: Inv Found");		  	
				  		return $results["invoiceID"];
			  		}
			  		else {
				  		error_log("Reserve: Inv Not Found");		  	
						return 0;	  
					}
			  }
	  }
	  
	  error_log("Reserve: Error");		  	
	 
	  return -1;
	  

	}

	public function addInvoice($slotID, $invoiceID) {
								
	  $result = $this->db->updateWhere("CAL_EVENT", "id", $slotID, array("invoiceID"=>$invoiceID), " and invoiceID=\"0\"");	

	  error_log($this->db->lastQuery());
	  
	  return $result;
	}
	
}
?>