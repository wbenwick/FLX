<?php

/**
 * FILELOGIX STRIPE CLASS
 *  
 * @author Wes Benwick
 * @link http://www.filelogix.com
 * @license Part of Filelogix usage license
 *
 *
 * @description This class will handle the integration with the Stripe.com API
 *
 */ 

 
//require_once(SMARTY_DIR . 'Smarty.class.php');
//require_once(BASEDIR . 'plugins/resource.db.php');
//require_once(SMARTY_DIR . "sysplugins/smarty_internal_debug.php");
 
require_once("/var/www/html/lib/stripe/1.17.1.2/lib/Stripe.php");
 
class stripe
{  
    // Will store database connection here
	private $db;
	private $stripe;
	private $mode
	private $errorMsg;
	private $customerID;
	private $cardID;
	private $stripeID;
	
	public function __construct($db, $mode) {

		  $this->smarty = new Smarty();
		  $this->mode = $mode;
		  $this->db = $db;
				
	}
		
	public function createCustomer($token, $customer) {

				  if($token!= "") {
						// Set your secret key: remember to change this to your live secret key in production
						// See your keys here https://manage.stripe.com/account
						\Stripe::setApiKey("sk_test_cyBQVqa3iN5HVaotOmVlsSuj");
			//			\Stripe::setApiKey("sk_live_EVDf2nJsqkUWMHxZEzGSF7Dz");
										

						// Create the customer in Stripe
						
						try {
							if ($this->stripeID == "") {
								$customer = \Stripe_Customer::create(array(
										"description" => $customer["name"],
										"email"=> $customer["emailAddress"],
										"metadata"=>array("customerID" => $customer["id"]),
										"card" => $token // obtained with Stripe.js
									));
								error_log("Stripe Create Customer: " . $customer["id"]);
								$this->customerID = $customer["id"];
								$this->stripeID = $this->db->insert("FLX_STRIPE", array("customer"=>$customer["id"]));
							}
							else {
								$customer = \Stripe_Customer::retrieve($stripeID);
								$customer->card = $token; // obtained with Stripe.js
								$customer->save();
								error_log("Stripe Update Customer: " . $stripeID);					
							}
						} catch(Stripe_CardError $e) {
			
									$this->errorMsg = "An error occurred while attempting to save credit card.";
						}
						
/*
						
						// Create the charge on Stripe's servers - this will charge the user's card
						try {
							$charge = \Stripe_Charge::create(array(
							  "amount" => $totalCostStr, // amount in cents, again
							  "currency" => "usd",
							  "customer"=> $customer["id"],
							  "description" => "$eventName on $eventDate (" . session_id() . ")")
						);
			
						$ticket->markAsPaid(session_id(), $token, $totalCost, serialize($charge));
						$this->vars["successMsg"] = "You have been charged $" . $this->vars["totalCost"] ." for the following:";
						$this->vars["attendees"] = $ticket->getAttendeesBySession();
						
						
						session_regenerate_id();
						$this->view = "EVENTS_PAID";
						
						
						return true;
						
						} catch(Stripe_CardError $e) {
			
									error_log("Stripe Charge Error: " . $e);
									$this->vars["errorMsg"] = "An error occurred while attempting to charge the card you provided.";
									$this->view = "EVENTS_CCPAY";
									return true;
						}
*/
				return $this->stripeID;	
			}
					
	}
	
	public function getCustomer($customerID) {
		
	}
	
	public function getCard($cardID) {
		
	}

	public function chargeCustomer($customerID, $cardID, $amount) {
		
	}
	
}

?>