<?php
/**
 * FILELOGIX CALENDAR TICKET CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
namespace CAL;
  
class ticket
{  
    // Will store database connection here
	private $db;
	private $sessionID;
	private $ticketID;

	
	public function __construct($db) {
	  $this->db = $db;
	  $this->sessionID = session_id();
		
	}

	public function hold($ticketsID, $memberID) {
		error_log("Holding Ticket...");
		if (!$this->lookup($ticketsID, $memberID)) {
			$this->ticketID = $this->db->replace("CAL_TICKET", array("kTicketID"=>$ticketsID, "kMemberID"=>$memberID,"sStatus"=>"Hold","sSessionID"=>session_id()));
			return $this->ticketID;
		}
		else {
			return -1;
		}
	}
	
	public function reserve($ticketsID, $memberID) {
		error_log("Reserving Ticket...");
		if (!$this->lookup($ticketsID, $memberID)) {
			$this->ticketID = $this->db->replace("CAL_TICKET", array("kTicketID"=>$ticketsID, "kMemberID"=>$memberID,"sStatus"=>"Pending","sSessionID"=>session_id()));
			return $this->ticketID;
		}
		else {
			return -1;
		}
	}
	
	public function lookup($ticketsID, $memberID) {
		
	  $r=$this->db->query("select * from CAL_TICKET where kMemberID='$memberID' and kTicketID='$ticketsID'");
      $results=$r->fetch(\PDO::FETCH_ASSOC);
      $ticketID= $results['id'];

      if ($ticketID>0) {
	      return $ticketID;
      }				
      else {
	      return 0;
      }
	}
	
	public function getAttendeesBySession() {
		
	  $r=$this->db->query("select * from CAL_TICKET left join REG_MEMBERS on (CAL_TICKET.kMemberID=REG_MEMBERS.id) where CAL_TICKET.sSessionID='$this->sessionID'");
      $results=$r->fetchAll();

	  error_log($this->db->lastQuery());
		
	  return $results;
	}
	
	public function getAttendeesByEvent($eventID) {
		
	  $r=$this->db->query("select *,CAL_TICKET.id as attendeeID, CAL_TICKET.sStatus as paidStr, REG_MEMBERS.firstName as firstName, REG_MEMBERS.lastName as lastName, REG_MEMBERS.emailAddress as emailAddress, REG_MEMBERS.company as companyName from CAL_TICKET left join CAL_TICKETS on (CAL_TICKET.kTicketID=CAL_TICKETS.id) left join REG_MEMBERS on (CAL_TICKET.kMemberID=REG_MEMBERS.id) where kEventID='$eventID'");    
      $results=$r->fetchAll();

	  error_log("Attendee Qry" . $this->db->lastQuery());
		
	  return $results;
	}	

	public function getAllEvents() {
		
	  $r=$this->db->query("select *, CAL_EVENT.sTitle as title, CAL_CATEGORIES.sName as category, CAL_EVENT.tStart as startDateStr, CAL_EVENT.tEnd as endDateStr, CAL_LOCATION.sShortName as location from CAL_EVENT left join CAL_LOCATION on (CAL_EVENT.kLocationID=CAL_LOCATION.id) left join CAL_CATEGORIES on (CAL_CATEGORIES.id=CAL_EVENT.kCategoryID) order by tStart desc");    
      $results=$r->fetchAll();
		
	  return $results;
	}
	
	public function getAttendee($attendeeID) {
		$attendeeIDStr=$this->db->quote($attendeeID);
		$r=$this->db->query("select * from CAL_TICKET left join REG_MEMBERS on (REG_MEMBERS.id=CAL_TICKET.kMemberID) where CAL_TICKET.id=$attendeeIDStr");    
        $results=$r->fetch(\PDO::FETCH_ASSOC);

	    error_log("getAttendee: " . $this->db->lastQuery());

        return $results;
    }	
	
	public function getTotalAttendeesBySession() {

	  $r=$this->db->query("select count(1) as attendeeCount from CAL_TICKET where sSessionID='$this->sessionID'");
  	  $results = $r->fetch(\PDO::FETCH_ASSOC);
  
	  error_log($this->db->lastQuery());

      return $results["attendeeCount"];		
	
	}
	
	public function getTotalCostBySession() {
		
	  $r=$this->db->query("select sum(CAL_TICKETS.fPrice) as totalCost from CAL_TICKET left join CAL_TICKETS on (CAL_TICKET.kTicketID=CAL_TICKETS.id) where CAL_TICKET.sSessionID='$this->sessionID'");    
	  $results = $r->fetch(\PDO::FETCH_ASSOC);
	  error_log($this->db->lastQuery());

      return $results["totalCost"];
	}
	
	public function getEventAttendeesByDay($eventID) {
		
	  $r=$this->db->query("select count(1) as count, month(tCreated) as month, year(tCreated) as year, day(tCreated) as day from CAL_TICKET left join CAL_TICKETS on (CAL_TICKET.kTicketID=CAL_TICKETS.id) where kEventID='$eventID' and tCreated>0 group by date(tCreated) asc");    
      $results=$r->fetchAll();

      return $results;
	}	
	
	public function getPaymentDataBySessionID($sessionID) {
		
	  $r=$this->db->query("select REG_PAYMENTS.response from REG_PAYMENTS left join CAL_TICKET on (CAL_TICKET.kPaymentID=REG_PAYMENTS.id) where CAL_TICKET.sSessionID='$sessionID'");    
	  $results = $r->fetch(\PDO::FETCH_ASSOC);
	  error_log($this->db->lastQuery());

      return $results["response"];
	}
		
	public function markAsPaid($sessionID, $token, $amount, $charge) {
		
	  $paymentID = $this->db->insert("REG_PAYMENTS", array("transactionID"=>$token, "source"=>"Stripe","amount"=>"$amount", "response"=>$charge));
		
	  $rows=$this->db->query("update CAL_TICKET set kPaymentID='$paymentID', sStatus='Paid' where sSessionID='$sessionID'");
		
	  return $paymentID;		
	}
}
?>