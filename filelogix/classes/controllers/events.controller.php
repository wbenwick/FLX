<?php

/**
 * FILELOGIX CALENDAR CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 */ 
 
namespace FLX\Controllers;

require_once("/var/www/html/lib/stripe-php-1.8.3/lib/Stripe.php");

class events
{  
    // Will store database connection here
	private $db;
	private $connID;
	private $sessionID;
	private $userID;
	private $view = "EVENTS";
	private $auth;
	private $vars = array();
	private $lists;
	private $registration;
	private $transferTo = false;

	/**
  	 * Create instance, load current info based on session info
  	 *
  	 * @return bool
  	 */
	
	public function __construct($db, $sessionID, $userID) {
	  $this->db = $db;
	  $this->sessionID = $sessionID;
	  $this->auth = new \auth($db);
	  $this->userID = $this->auth->getUserID();
	  
	  $this->lists = new \lists($this->db);

	}
	
	/**
  	 * Opens the controller - responsible for authentication and loading defaults
  	 *
  	 * @return bool true if success, false if failure
  	 */
	
	public function open() {
/*
	  $this->users=$this->db->query("select * from connections");
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
      }
*/		
	}
	
	
	/**
  	 * Loads the controller, handles any templating and pre-display logic for the requested view
  	 *
  	 * @return bool
  	 */
	
	public function load($view) {
/*
	  $this->users=$this->db->query("select * from connections");
	  foreach ($this->users as $row) {
		  $this->userID = $row['userID'];
		  $this->username = $row['username'];
		  $this->emailAddress = $row['emailAddress'];
      }
      
*/
//        $this->vars["loginAlert"]="<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" style=\"text-indent: 0px; height: 100%;\" data-dismiss=\"alert\">&times;</button> <strong>Warning!</strong> Invalid username or password.</div>";

		$this->vars["loginUsername"]="wbenwick@filelogix.net";


		$this->vars["dropdowns"] = $this->lists->retrieveByID("1");
		
	}
	
	public function add($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_NEW_EVENT";
		$this->vars["navCreateActive"]=true;
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}

	public function reserve($params) {
	
		$firstName = trim($_POST["firstName"]);
		$lastName = trim($_POST["lastName"]);
		$emailAddress = trim($_POST["emailAddress"]);
		$company = trim($_POST["company"]);

		$this->vars["firstName"] = trim($firstName);
		$this->vars["lastName"] = trim($lastName);
		$this->vars["emailAddress"] = trim($emailAddress);
		$this->vars["company"] = trim($company);

		$this->view = "EVENTS_ADD";
		
		if (($_POST["submit"] == "Add Another Attendee") or ($_POST["submit"] == "Add Attendee")) {

			if (strlen(trim($firstName))<2) {
				$this->vars["errorMsg"]="Please enter your first name.";
		
				return true;
			}
	
			if (strlen(trim($lastName))<2) {
				$this->vars["errorMsg"]="Please enter your last name.";
		
				return true;
			}
	
			if (strlen(trim($emailAddress))<3) {
				$this->vars["errorMsg"]="Please enter your email address.";
				
				return true;
			}
			
			if (strlen(trim($company))<3) {
				$this->vars["errorMsg"]="Please enter your Company or Organization's name.";
				
				return true;
			}		
		}
		
		$ticket = new \CAL\ticket($this->db);
		$member = new \registration($this->db);
		
		switch ($_POST["submit"]) {
			case "Add Another Attendee":
			case "Add Attendee":
					$memberID = $member->addMember($firstName,$lastName,$emailAddress, $company);
					if ($memberID) {
						$ticketID=$ticket->hold("1", $memberID);				
						$this->view = "EVENTS_ADD";
						$this->vars["successMsg"] = "Attendee has been saved, please continue.";
					}
					else if ($memberID<0) {
						$this->vars["errorMsg"] = "This member already exists.";
						$this->view = "EVENTS_ADD";						
					}
					else {
						$this->vars["errorMsg"] = "An error occurred while saving this record.";
						$this->view = "EVENTS_ADD";
					}
					break;
			case "Finish & Pay":
			case "Cancel":
			case "Next":
					$memberID = $member->addMember($firstName,$lastName,$emailAddress, $company);
					if ($memberID) {
						$ticketID=$ticket->reserve("1", $memberID);
						$this->vars["successMsg"] = "Attendee has been saved, please pay to confirm your seat.";
					}
					else {
						$this->vars["errorMsg"] = "An error occurred while saving this record.";
						$this->view = "EVENTS_ADD";
					}
					$this->vars["attendees"] = $ticket->getAttendeesBySession();
					$this->vars["totalCost"] = $ticket->getTotalCostBySession();
					$this->vars["totalAttendees"] = $ticket->getTotalAttendeesBySession();
									
					$this->view = "EVENTS_PAY";

					break;
			case "Pay By Credit Card";
					$this->vars["attendees"] = $ticket->getAttendeesBySession();
					$this->vars["totalCost"] = $ticket->getTotalCostBySession();
					$this->vars["totalAttendees"] = $ticket->getTotalAttendeesBySession();	
					$this->view = "EVENTS_CCPAY";
					break;
		}

		if ($ticketID<0) {
			$this->vars["errorMsg"] = "The attendee has already been reserved a seat.";	
			$this->view = "EVENTS";							
		}
		else {
			$this->vars["firstName"] = "";
			$this->vars["lastName"] = "";
			$this->vars["emailAddress"] = "";
			$this->vars["company"] = trim($company);
		}

		return true;
	}
	
	public function pay($params) {
	
		
		if ($_POST["stripeToken"] != "") {

			$ticket = new \CAL\ticket($this->db);

			$this->vars["totalCost"] = $ticket->getTotalCostBySession();

			$totalCostStr = intval($this->vars["totalCost"]) . "00";
			
			// Set your secret key: remember to change this to your live secret key in production
			// See your keys here https://manage.stripe.com/account
//			\Stripe::setApiKey("sk_test_qhl2uuZscissfXHTxBePkOLB");
			\Stripe::setApiKey("sk_live_X77SoeG6bpc87RzNdFNBspIg");

			// Get the credit card details submitted by the form
			$token = $_POST['stripeToken'];
			
			// Create the charge on Stripe's servers - this will charge the user's card
			try {
			$charge = \Stripe_Charge::create(array(
			  "amount" => $totalCostStr, // amount in cents, again
			  "currency" => "usd",
			  "card" => $token,
			  "description" => "CSCMP Jax Events - October 15th, 2013 (" . session_id() . ")")
			);

			$ticket->markAsPaid(session_id(), $token, $totalCost, serialize($charge));
			$this->vars["successMsg"] = "Your have been charged $" . $this->vars["totalCost"] ." for the following attendees:";
			$this->vars["attendees"] = $ticket->getAttendeesBySession();
			
			
			session_regenerate_id();
			$this->view = "EVENTS_PAID";
			
			
			return true;
			
			} catch(Stripe_CardError $e) {

						$this->vars["errorMsg"] = "An error occurred while attempting to charge the card you provided.";
						$this->view = "EVENTS_CCPAY";
						return true;
			}
			

		}


		$_POST["submit"] = "Cancel";
		

		return $this->reserve($params);
		
	}

	public function manage($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_MANAGE";
		$this->vars["navManageActive"]=true;
		
		$ticket = new \CAL\ticket($this->db);

		$this->vars["events"]=$ticket->getAllEvents(); 
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}


	public function attendee($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_ATTENDEE";
		$this->vars["navManageActive"]=true;
		
		$ticket = new \CAL\ticket($this->db);
				
		$this->vars["attendee"]=$ticket->getAttendee($params[2]);
		$this->vars["events"]=$ticket->getAllEvents(); 
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}


	public function support($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_HOME";
		$this->vars["navSupportActive"]=true;
		
		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }

	}

	public function home($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_HOME";
		$this->vars["navHomeActive"]=true;
		
		
		$ticket = new \CAL\ticket($this->db);
		$member = new \registration($this->db);

		$this->vars["chartLeft"] = $ticket->getEventAttendeesByDay(1);
		$this->vars["chartRight"] = $member->getTotalMembersByDay();
		$this->vars["attendees"] = $ticket->getAttendeesByEvent(1);

		return true;
	  }
	  
	  else {

		return $this->login($params);		  
		  
	  }
	}

	public function reports($params) {

	  if ($this->auth->validate($this->userID)) {
		
		$this->view="CAL_REPORTS";
		$this->vars["navReportsActive"]=true;
		
		
		$ticket = new \CAL\ticket($this->db);
		$member = new \registration($this->db);

		$this->vars["chartLeft"] = $ticket->getEventAttendeesByDay(1);
		$this->vars["chartRight"] = $member->getTotalMembersByDay();
		$this->vars["attendees"] = $ticket->getAttendeesByEvent(1);

		return true;
	  }
	  
	  else {

		$this->view="CAL_LOGIN";
		$this->vars["navReportsActive"]=false;
		
		return true;		  
		  
	  }
	}	
	

	public function login($params) {

	  if ($this->auth->validate($this->userID)) {

		return $this->home($params);
		  
	  }
	  
	  else {
	  	  	error_log("Logging Calendar User In... " . $_POST["username"]);

		    if (($_POST["username"] != "") and ($_POST["password"] != "")) {
			  if ($this->auth->login($_POST["username"], $_POST["password"])) {

				return $this->home($params);

			  }
			  else {
				$this->view="CAL_LOGIN";
				$this->vars["alertError"]=true;
				return true;
			  }
		}



		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }
	}	


	public function forgot($params) {

	  if ($this->auth->validate($this->userID)) {

		return $this->home($params);
		  
	  }
	  
	  else {
	  
/*	  	error_log("Logging Calendar User In... " . $_POST["username"]);

		if (($_POST["username"] != "") and ($_POST["password"] != "")) {
			if ($this->auth->login($_POST["username"], $_POST["password"])) {

				return $this->home($params);

			}
			else {
				$this->view="CAL_LOGIN";
				$this->vars["alertError"]=true;
				return true;
			}
		}
*/

	    require_once('/var/www/html/lib/recaptchalib.php');
	    $publickey = "6Lc6tecSAAAAALevgROIwALgd4yT01iTrEDfqEuy"; 
	    $this->vars["captcha"] = recaptcha_get_html($publickey);

		$this->view="CAL_FORGOT";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }
	}	


	public function signup($params) {

	  if ($this->auth->validate($this->userID)) {

		return $this->home($params);
		  
	  }
	  
	  else {
	  

	  	error_log("Signing up new user... " . $_POST["username"]);

		if (($_POST["emailAddress"] != "") and ($_POST["firstName"] != "") and ($_POST["lastName"] != "")) {
			if ($user->activate($_POST["emailAddress"])) {

				

				return $this->home($params);

			}
			else {
				$this->view="CAL_SIGNUP";
				$this->vars["alertError"]=true;
				$this->vars["errorMsg"]="Thank You! Your request is being reviewed.";
				
				return true;
			}
		}

		$this->view="CAL_SIGNUP";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	  }
	}	

	public function activate($params) {

	  if ($this->auth->validate($this->userID)) {

		return $this->home($params);
		  
	  }
	  
	  else {
	  
	  	error_log("Activating new user... " . $_POST["emailAddress"]);

	  	$users = new \users($this->db);

		if ($_POST["emailAddress"] != "") {
			$this->auth->logout();
			
			$username = $users->activate($_POST["emailAddress"], $_POST["password"]);
			if ($username) {

				$_POST["username"] = $username;

				error_log("User Activated.  Logging In Automatically...");

				return $this->login($params);
	
				return true;
			}
			else {
				$this->view="CAL_ACTIVATE";
				$this->vars["alertError"]=true;
				$this->vars["errorMsg"]="An error occurred. Please Try Again.";
				if (($_POST["emailAddress"] != "") and (strlen($_POST["password"]) < 6)) {
					$this->vars["email"] = $_POST["emailAddress"];
					$this->vars["errorMsg"]="Your password is too short.";				
				}
				$this->vars["emailAddress"]=$emailAddress;
				
				return true;
			}
		}

		$this->view="CAL_ACTIVATE";
		$this->vars["navHomeActive"]=false;
		$this->vars["emailAddress"]=$_POST["emailAddress"];
		
		return true;		  
		  
	  }
	}	



	public function logout($params) {

		$this->auth->logout();
			
		$this->view="CAL_LOGIN";
		$this->vars["navHomeActive"]=false;
		
		return true;		  
		  
	}	
		

	public function data() {
		
		return $this->vars;
	}


	public function view() {
		
		return $this->view;
	}
	
	public function transfer() {
		
		return $this->transferTo;
	}

}
?>
