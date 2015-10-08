<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contacts_fn.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

		include 'settings.php';
		include 'windowslivelogin.php';
	//A class to create an object for contact info attributes
	//You may extend this if you want more information, but you will then need to
	//"fill in" the info when parsing the return array and expand the consctructor
	class Person {
		  public $first_name, $last_name, $email_address;
		
		  public function __construct($first, $last, $em) {
			$this->first_name = $first;
			$this->last_name = $last;
			$this->email_address = $em;
		  }
		
		  public function __toString() {
			return $this->first_name. ' ' . $this->$last_name . ' : ' . $this->$email_address . ' <BR>';
		  }
	}
	
	function get_people_array($token)
	{
		if ($token) {
			// Convert Unix epoch time stamp to user-friendly format.
				$expiry = $token->getExpiry();
				$expiry = date(DATE_RFC2822, $expiry);
		
		
		//*******************CONVERT HEX TO DOUBLE LONG INT ***************************************
				$hexIn = $token->getLocationID();
				include "hex.php";		
				$longint=$output;		//here's the magic long integer to be sent to the Windows Live service
				
		//*******************CURL THE REQUEST ***************************************
				$uri = "https://livecontacts.services.live.com/users/@C@".$output."/LiveContacts";
				$session = curl_init($uri);
				
				curl_setopt($session, CURLOPT_HEADER, true);
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				curl_setopt ($session, CURLOPT_USERAGENT, "Windows Live Data Interactive SDK");
				curl_setopt($session, CURLOPT_HTTPHEADER, array("Authorization: DelegatedToken dt=\"".$token->getDelegationToken()."\""));
				curl_setopt($session, CURLOPT_VERBOSE, 1);
				curl_setopt ($session, CURLOPT_HTTPPROXYTUNNEL, TRUE);
				curl_setopt ($session, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
				//curl_setopt ($session, CURLOPT_PROXY,$PROXY_SVR);
				curl_setopt ($session, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt ($session, CURLOPT_TIMEOUT, 120);
				$response_h = curl_exec($session);
				curl_close($session);	
				
		//*******************PARSING THE RESPONSE ****************************************************
				$response=strstr($response_h,"<?xml version");
		  
				try {
				$xml = new SimpleXMLElement($response);
				}
				catch (Exception $e) {
					echo $response_h."<br>".$uri;
					die;
				}		
				$lengthArray=sizeof($xml->Contacts->Contact);
				for ($i=0;$i<$lengthArray;$i++)
				{
					//There can be more fields, depending on how you configure.  Here's
					//where you should access the fields and send them to the constructor
					
					$fn = $xml->Contacts->Contact[$i]->Profiles->Personal->FirstName;			
					$ln = $xml->Contacts->Contact[$i]->Profiles->Personal->LastName;
					if (is_object($xml->Contacts->Contact[$i]->Emails->Email)) {				
						$em = (string)$xml->Contacts->Contact[$i]->Emails->Email->Address;
					  $email_temp = explode('@', $em); 
            if (!empty($email_temp['0'])) {
              //instantiate an object and add it to the array
              $person_array[$i]['contactName']=$fn . ' ' . $ln;
              $person_array[$i]['contactMail']=$em;
            }
					}
				}
			}
		  
			//return the entire array of Person objects
			return $person_array;
	}
?>
