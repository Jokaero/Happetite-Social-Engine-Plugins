<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: aolapi.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

function getaolcontacts($username,$password, $login=true) {
	$cookie_file_path = "cookies/aol";
	$username = trim($username);
	$password = trim($password);
	$login_url = 'https://my.screenname.aol.com/_cqr/login/login.psp?sitedomain=sns.webmail.aol.com&lang=en&locale=us&authLev=0&siteState=ver%3a3|rt%3aSTANDARD|ac%3aWS|at%3aSNS|ld%3awebmail.aol.com|uv%3aAOL|lc%3aen-us|mt%3aAOL|snt%3aScreenName&offerId=webmail-en-us&seamless=novl';
 
    $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 firefox/3.1';
  
  //SETP:1:-GETTING AOL LOGIN PAGE.
	$ch	= curl_init();
	curl_setopt($ch, CURLOPT_URL, $login_url);
	curl_setopt($ch,CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file_path);
	curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file_path);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$loginhtmlpage 	= curl_exec( $ch );
	
	//GETTING THE REQUIRED HIDDEN FILEDS FROM AOL LOGIN FORM.
	preg_match_all("/name=\"usrd\" value=\"(.*?)\"/", $loginhtmlpage, $arr_user_usrd);
	preg_match_all("/name=\"siteState\" value=\"(.*?)\"/", $loginhtmlpage, $arr_user_sitestate);
	$user_usrd = $arr_user_usrd[1][0];
	$user_sitestate = $arr_user_sitestate[1][0];
	$postvars = "method=POST=action=https://my.screenname.aol.com/_cqr/login/login.psp?sitedomain=sns.webmail.aol.com&siteState=" . $user_sitestate . "&usrd=" . $user_usrd . "&loginId=".urlencode($username)."&password=".urlencode($password) ; 
	
	
  $login_confirmurl = 'https://my.screenname.aol.com/_cqr/login/login.psp';
	//AUTHENTICATING THE USER AT AOL SITE.
	curl_setopt($ch, CURLOPT_URL, $login_confirmurl);
	//curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
	$loginconfirmpage = curl_exec( $ch );
  
  
	//GETTING THE CONTACT INBOX OF USER.
  $url="http://webmail1.mail.aol.com/32360-211/aol-6/en-us/Lite/ContactList.aspx?folder=Inbox&showUserFolders=False";
  curl_setopt($ch, CURLOPT_URL,$url);
	//curl_setopt($ch, CURLOPT_POST, 0);
	
	$usercontactlist = curl_exec( $ch );
 
  
  //FETCHING THE USER CONTACT LIST IN PRINTABLE FORM
		preg_match_all("/name=\"user\" value=\"(.*?)\"/", $usercontactlist, $arr_user_user);
		if ($login) {
		 if (!empty($arr_user_user[1][0])) {
			return true;	
		}
		else {
			return false;
		}	
  }
		$url="http://webmail1.mail.aol.com/32244-111/aim/en-us/Lite/addresslist-print.aspx?command=all&user=".urlencode($arr_user_user[1][0]);
	
		curl_setopt($ch, CURLOPT_URL,$url);
		$result = curl_exec( $ch );
    
    //FINALLY EXTRACTIN ADDRESS BOOK FROM PRINTABLE FORM.
		preg_match_all('/(<span class=\"fullName\">)(.*)(<i>(.*)<\/i><\/span>)/',$result,$contactname); 
		preg_match_all('/(<span>Email 1:<\/span> <span>)(.*)(<\/span>)/',$result,$emails);

		$UserContactList 		= array();
    if (!empty($emails[2])) {
			foreach( $emails[2] as $key => $value ) {
        $email_temp = explode('@', $value);
        if (!empty($email_temp['0'])) {
          $UserContactList[$key]['contactMail'] = $value;
          if (!empty($contactname[2][$key])) {
            $UserContactList[$key]['contactName'] = $contactname[2][$key];
          }
          else {
            $UserContactList[$key]['contactName'] = $value;
          }
        }
				
			}
			if (!empty($UserContactList)) {
				sort($UserContactList);
			}
		}
		return $UserContactList;
	}
?>