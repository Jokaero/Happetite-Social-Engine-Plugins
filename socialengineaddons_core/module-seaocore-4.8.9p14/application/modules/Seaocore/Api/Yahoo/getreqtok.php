<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getreqtok.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

require 'globals.php';
require 'oauth_helper.php';

/**
 * Get a request token.
 * @param string $consumer_key obtained when you registered your app
 * @param string $consumer_secret obtained when you registered your app
 * @param string $callback callback url can be the string 'oob'
 * @param bool $usePost use HTTP POST instead of GET
 * @param bool $useHmacSha1Sig use HMAC-SHA1 signature
 * @param bool $passOAuthInHeader pass OAuth credentials in HTTP header
 * @return array of response parameters or empty array on error
 */
function get_request_token($consumer_key, $consumer_secret, $callback, $usePost=false, $useHmacSha1Sig=true, $passOAuthInHeader=false)
{
 
  $retarr = array();  // return value
  $response = array();
  $consumer_key = trim($consumer_key);
  $consumer_secret = trim($consumer_secret);
  $url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
  $params['oauth_version'] = '1.0';
  $params['oauth_nonce'] = mt_rand();
  $params['oauth_timestamp'] = time();
  $params['oauth_consumer_key'] = $consumer_key;
  $params['oauth_callback'] = $callback;

  // compute signature and add it to the params list
  if ($useHmacSha1Sig) {
    $params['oauth_signature_method'] = 'HMAC-SHA1';
    $params['oauth_signature'] =
      oauth_compute_hmac_sig($usePost? 'POST' : 'GET', $url, $params,
                             $consumer_secret, null);
  } else {
    $params['oauth_signature_method'] = 'PLAINTEXT';
    $params['oauth_signature'] =
      oauth_compute_plaintext_sig($consumer_secret, null);
  }

  // Pass OAuth credentials in a separate header or in the query string
  if ($passOAuthInHeader) {
    $query_parameter_string = oauth_http_build_query($params, true);
    $header = build_oauth_header($params, "yahooapis.com");
    $headers[] = $header;
  } else {
    $query_parameter_string = oauth_http_build_query($params);
  }

  // POST or GET the request
  if ($usePost) {
    $request_url = $url;
    logit("getreqtok:INFO:request_url:$request_url");
    logit("getreqtok:INFO:post_body:$query_parameter_string");
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $response = do_post($request_url, $query_parameter_string, 443, $headers);
  } else {
    $request_url = $url . ($query_parameter_string ?
                           ('?' . $query_parameter_string) : '' );
    logit("getreqtok:INFO:request_url:$request_url");
    $response = do_get($request_url, 443, $headers);
  }

  // extract successful response
  if (! empty($response)) {
    list($info, $header, $body) = $response;
    $body_parsed = oauth_parse_str($body);
    
    $retarr = $response;
    $retarr[] = $body_parsed;
  }
   if (empty($retarr[3]['xoauth_request_auth_url']) || empty($retarr[3]['oauth_token']) || empty($retarr[3]['oauth_token_secret'])) {
		echo Zend_Registry::get('Zend_Translate')->_("There are some problem in processing your request. Please try again after some time.");die;
	}
  return $retarr;
}


/**
 * Get an access token using a request token and OAuth Verifier.
 * @param string $consumer_key obtained when you registered your app
 * @param string $consumer_secret obtained when you registered your app
 * @param string $request_token obtained from getreqtok
 * @param string $request_token_secret obtained from getreqtok
 * @param string $oauth_verifier obtained from step 3
 * @param bool $usePost use HTTP POST instead of GET
 * @param bool $useHmacSha1Sig use HMAC-SHA1 signature
 * @param bool $passOAuthInHeader pass OAuth credentials in HTTP header
 * @return array of response parameters or empty array on error
 */
function get_access_token($consumer_key, $consumer_secret, $request_token, $request_token_secret, $oauth_verifier, $usePost=false, $useHmacSha1Sig=true, $passOAuthInHeader=true)
{
  $retarr = array();  // return value
  $response = array();

  $url = 'https://api.login.yahoo.com/oauth/v2/get_token';
  $params['oauth_version'] = '1.0';
  $params['oauth_nonce'] = mt_rand();
  $params['oauth_timestamp'] = time();
  $params['oauth_consumer_key'] = $consumer_key;
  $params['oauth_token']= $request_token;
  $params['oauth_verifier'] = $oauth_verifier;

  // compute signature and add it to the params list
  if ($useHmacSha1Sig) {
    $params['oauth_signature_method'] = 'HMAC-SHA1';
    $params['oauth_signature'] =
      oauth_compute_hmac_sig($usePost? 'POST' : 'GET', $url, $params,
                             $consumer_secret, $request_token_secret);
  } else {
    $params['oauth_signature_method'] = 'PLAINTEXT';
    $params['oauth_signature'] =
      oauth_compute_plaintext_sig($consumer_secret, $request_token_secret);
  }

  // Pass OAuth credentials in a separate header or in the query string
  if ($passOAuthInHeader) {
    $query_parameter_string = oauth_http_build_query($params, true);
    $header = build_oauth_header($params, "yahooapis.com");
    $headers[] = $header;
  } else {
    $query_parameter_string = oauth_http_build_query($params);
  }

  // POST or GET the request
  if ($usePost) {
    $request_url = $url;
    logit("getacctok:INFO:request_url:$request_url");
    logit("getacctok:INFO:post_body:$query_parameter_string");
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $response = do_post($request_url, $query_parameter_string, 443, $headers);
  } else {
    $request_url = $url . ($query_parameter_string ?
                           ('?' . $query_parameter_string) : '' );
    logit("getacctok:INFO:request_url:$request_url");
    $response = do_get($request_url, 443, $headers);
  }

  // extract successful response
  if (! empty($response)) {
    list($info, $header, $body) = $response;
    $body_parsed = oauth_parse_str($body);
    $retarr = $response;
    $retarr[] = $body_parsed;
  }

  return $retarr;
}



/**
 * Call the Yahoo Contact API
 * @param string $consumer_key obtained when you registered your app
 * @param string $consumer_secret obtained when you registered your app
 * @param string $guid obtained from getacctok
 * @param string $access_token obtained from getacctok
 * @param string $access_token_secret obtained from getacctok
 * @param bool $usePost use HTTP POST instead of GET
 * @param bool $passOAuthInHeader pass the OAuth credentials in HTTP header
 * @return response string with token or empty array on error
 */
function callcontact($consumer_key, $consumer_secret, $guid, $access_token, $access_token_secret, $usePost=false, $passOAuthInHeader=true)
{
  $retarr = array();  // return value
  $response = array();

  $url = 'https://social.yahooapis.com/v1/user/' . $guid . '/contacts;count=1000';
  $params['format'] = 'xml';
  $params['view'] = 'compact';
  $params['oauth_version'] = '1.0';
  $params['oauth_nonce'] = mt_rand();
  $params['oauth_timestamp'] = time();
  $params['oauth_consumer_key'] = $consumer_key;
  $params['oauth_token'] = $access_token;

  // compute hmac-sha1 signature and add it to the params list
  $params['oauth_signature_method'] = 'HMAC-SHA1';
  $params['oauth_signature'] =
      oauth_compute_hmac_sig($usePost? 'POST' : 'GET', $url, $params,
                             $consumer_secret, $access_token_secret);

  // Pass OAuth credentials in a separate header or in the query string
  if ($passOAuthInHeader) {
    $query_parameter_string = oauth_http_build_query($params, true);
    $header = build_oauth_header($params, "yahooapis.com");
    $headers[] = $header;
  } else {
    $query_parameter_string = oauth_http_build_query($params);
  }

  // POST or GET the request
  if ($usePost) {
    $request_url = $url;
    logit("callcontact:INFO:request_url:$request_url");
    logit("callcontact:INFO:post_body:$query_parameter_string");
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $response = do_post($request_url, $query_parameter_string, 443, $headers);
  } else {
    $request_url = $url . ($query_parameter_string ?
                           ('?' . $query_parameter_string) : '' );
    logit("callcontact:INFO:request_url:$request_url");
    $response = do_get($request_url, 443, $headers);
  }

 // extract successful response
  try {
		if (!empty($response[2])) { 
			$xml = new SimpleXMLElement($response[2]);
			$i = 0;
			
			foreach ($xml->contact as $contact) {
				if (!empty($contact->fields[0]) && !empty($contact->fields[1])) { 
					$emails[$i]['contactMail'] = (string)$contact->fields[0]->value;
						$email_temp = explode('@', $emails[$i]['contactMail']);
					if (empty($email_temp[1])) {
						$emails[$i]['contactMail'] = $emails[$i]['contactMail'] . '@yahoo.com';
					}
					$type = 	(string)$contact->fields[1]->type;
					$givenName = 		(string)$contact->fields[1]->value->givenName;		
																	
					if ($type == 'name') {
						if (!empty($givenName))	{
							$emails[$i]['contactName'] = (string)$contact->fields[1]->value->givenName ;
							//if (!is_object($contact->fields[1]->value->familyName)) {
								$emails[$i]['contactName'] = (string)$contact->fields[1]->value->givenName . ' ' . (string)$contact->fields[1]->value->familyName;
							//}
						}
						else {
							$emails[$i]['contactName'] = (string)$contact->fields[0]->value;	
						}
					}
					else if ($type == 'nickname') {
						if (!empty($contact->fields[1]->value))	{
							$emails[$i]['contactName'] = (string)$contact->fields[1]->value;
						}
						else {
							$emails[$i]['contactName'] = $emails[$i]['contactMail'];	
						}
					}
					else {		
						$emails[$i]['contactName'] = $emails[$i]['contactMail'];
					}
          
          if (empty($email_temp[0])) {
             unset($emails[$i]);
          }
				}
				else if (is_object($contact->fields)){
					$emails[$i]['contactMail'] = (string)$contact->fields->value;
					$email_temp = explode('@', $emails[$i]['contactMail']);
					if (empty($email_temp[1])) {
						$emails[$i]['contactMail'] = $emails[$i]['contactMail'] . '@yahoo.com';
					}
										
					$emails[$i]['contactName'] = $emails[$i]['contactMail'];
          if (empty($email_temp[0])) {
              unset($emails[$i]);
          }
				}
				$i++;
			}
			//$emails = array_unique($emails);
			if (!empty($emails)) {
				sort($emails);
			}
		}
		return $emails;
	}
	catch (Exception $e) {
		echo Zend_Registry::get('Zend_Translate')->_("Please try again.");die;
	}
}
?>