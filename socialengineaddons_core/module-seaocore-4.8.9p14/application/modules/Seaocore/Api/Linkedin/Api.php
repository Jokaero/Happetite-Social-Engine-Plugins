<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Api.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once 'Linkedin.php';
class Seaocore_Api_Linkedin_Api { 
  
  protected $_api;

  protected $_oauth;

  public function getApi()
  {
    if( null === $this->_api ) {
      $this->_initializeApi();
    }
    
    return $this->_api;
  }

  public function getOauth()
  {
    if( null === $this->_oauth ) {
      $this->_initializeApi();
    }
    
    return $this->_oauth;
  }

  public function clearApi()
  {
    $this->_api = null;
    $this->_oauth = null;
    return $this;
  }

  public function isConnected()
  {
    // @todo make sure that info is validated
    return ( !empty($_SESSION['linkedin_token2']) && !empty($_SESSION['linkedin_secret2']) );
  }

  protected function _initializeApi()
  {
    $settings['key'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
    $settings['secret'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
    if (class_exists('Advancedactivity_Model_DbTable_Linkedin')) {
      $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'advancedactivity');
      $linkedinTableName = $linkedinTable->info('name');
      // Load settings
       $settings['key'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
       $settings['secret'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
      if( empty($settings['key']) ||
          empty($settings['secret']) ||
          empty($settings['enable']) ||
          $settings['enable'] == 'none' ) {

        $this->_api = null;
        Zend_Registry::set('Linkedin_Api', $this->_api);
      }
      $this->_api = new Linkedin(array(
          'appKey'       => $settings['key'],
          'appSecret'    => $settings['secret'],
          'callbackUrl'  => '' 
          ));
      // Try to log viewer in?
      $viewer = Engine_Api::_()->user()->getViewer();
      if( !isset($_SESSION['linkedin_uid']) || empty($_SESSION['linkedin_uid']) ||
          @$_SESSION['linkedin_lock'] !== $viewer->getIdentity() ) { 
        $_SESSION['linkedin_lock'] = $viewer->getIdentity();
        if( $viewer && $viewer->getIdentity() ) {
          // Try to get from db
          $info = $linkedinTable->select()
              ->from($linkedinTable)
              ->where('user_id = ?', $viewer->getIdentity())
              ->query()
              ->fetch();
          if( is_array($info) &&
              !empty($info['linkedin_secret']) &&
              !empty($info['linkedin_token']) ) {
            $_SESSION['linkedin_uid'] = $info['linkedin_uid'];
            $_SESSION['linkedin_secret2'] = $info['linkedin_secret'];
            $_SESSION['linkedin_token2'] = $info['linkedin_token'];

            $this->_api->setToken(array('oauth_token' => $info['linkedin_token'], 'oauth_token_secret' => $info['linkedin_secret']));
          } else {
            $_SESSION['linkedin_uid'] = false; // @todo make sure this gets cleared properly
          }
        } else {
          // Could not get
          //$_SESSION['linkedin_uid'] = false;
        }
      }
       if (isset($_SESSION['linkedin_secret2'], $_SESSION['linkedin_token2'])){ 
        $this->_api->setToken(array('oauth_token' => $_SESSION['linkedin_token2'], 'oauth_token_secret' => $_SESSION['linkedin_secret2']));

      }
    }
    else {
      $this->_api = new Linkedin(array(
          'appKey'       => $settings['key'],
          'appSecret'    => $settings['secret'],
          'callbackUrl'  => '' 
          ));
    }

  }
  
   /**
	 *  Retrive the contacts from the xml response from linkedin server.
	 * 
	 */  
public function retriveContacts ($OBJ_linkedin, $moduletype = '') {
  
  $response = $OBJ_linkedin->connections('~/connections:(id,first-name,last-name,picture-url)?start=0&count=' . CONNECTION_COUNT);
  if($response['success'] === TRUE) { 
     $connections = new SimpleXMLElement($response['linkedin']); 
     
     if((int)$connections['total'] > 0) {
        $contactInfo = array ();
        $key = 0;
        foreach($connections->person as $connection) { 
           $contactInfo[(string)$connection->{'id'}]['name'] = $connection->{'first-name'} . ' ' . $connection->{'last-name'} ;
           $contactInfo[(string)$connection->{'id'}]['id'] = (string)$connection->{'id'};
           $contactInfo[(string)$connection->{'id'}]['picture'] = (string)$connection->{'picture-url'};
           $key++;
        }
       if (!empty($moduletype)) { 
			    $SiteNonSiteFriends = array();
			    $result = array();
			    $SiteNonSiteFriends[1] = $contactInfo;
			    
            $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
            $contactInfo = $result;
            
			  }
			  else 
          $contactInfo = $this->parseUserContacts ($contactInfo);
     }
     
   }
   else {
     echo "Error retrieving connections:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response) . "</pre>";die;
   }
  return $contactInfo;
}
  
  
  
  /**
	 *  Parse the contacts in two parts :
	 * 
	 * 1) Contacts which are already at site
	 * 
	 * 2) Contacts which are not on this site
	 * 
	 */  
public function parseUserContacts ($contactInfo, $moduletype = null, $Subject = null) {
  
  $viewer =  Engine_Api::_()->user()->getViewer();
	$user_id = $viewer->getIdentity();
	$table_user = Engine_Api::_()->getitemtable('user');
	$tableName_user = $table_user->info('name');
	
	$inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
  $inviteTableName = $inviteTable->info('name');
  
	$table_user_memberships = Engine_Api::_()->getDbtable('membership' , 'user');
	$tableName_user_memberships = $table_user_memberships->info('name');
	$SiteNonSiteFriends[] = '';
	
	foreach ($contactInfo as $userid => $name) {

		//FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
		$select = $table_user->select()
		->setIntegrityCheck(false)
		->from($tableName_user, array('user_id', 'displayname', 'photo_id'))
		->join($inviteTableName, "$inviteTableName.new_user_id = $tableName_user.user_id", null)
		->where($inviteTableName . '.new_user_id <>?', 0)
		->limit(1)
		->where($inviteTableName . '.social_profileid = ?', $userid);

		$is_site_members = $table_user->fetchRow($select);
			
    if (empty($user_id)) {
			if (!empty($is_site_members->user_id)) {
				continue;
      }

    }
    
    
		//NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
		if (!empty($is_site_members->user_id) && $is_site_members->user_id != $user_id) {
			$contact =  Engine_Api::_()->user()->getUser($is_site_members->user_id);
			// check that user has not blocked the member
			if(!$viewer->isBlocked($contact)) { 
			  
			  //SENDING PAGE JOIN SUGGESTION IF THE USER IS SITE MEMBER.
			  if (!empty($moduletype)) {
           $is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

            // IF SUGGESTION PLUGIN IS INSTALLED, A SUGGESTION IS SEND
            if ($is_suggenabled) {
              Engine_Api::_()->sitepageinvite()->sendSuggestion($is_site_members, $viewer, $Subject->page_id);
            }
            // IF SUGGESTION PLUGIN IS NOT INSTALLED, A NOTIFICATION IS SEND
            else {
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($is_site_members, $viewer, $Subject, $moduletype . '_suggested');
            }
          return;
        }
				// The contact should not be my friend, and neither of us should have sent a friend request to the other.
				$select = $table_user_memberships->select()
				->setIntegrityCheck(false)
				->from($tableName_user_memberships, array('user_id'))
				->where($tableName_user_memberships . '.resource_id = ' . $user_id .' AND ' . $tableName_user_memberships . '.user_id = ' . $is_site_members->user_id )
				->orwhere($tableName_user_memberships . '.resource_id = ' . $is_site_members->user_id .' AND ' . $tableName_user_memberships . '.user_id = ' .$user_id );
				$already_friend = $table_user->fetchRow($select);
				
				//IF THIS QUERY RETURNS EMPTY RESULT MEANS THIS USER IS SITE MEMBER BUT NOT FRIEND OF CURRENTLY LOGGEDIN USER SO WE WILL SEND HIM TO FRIENDSHIP REQUEST.
				if (empty($already_friend->user_id)) { 
					$SiteNonSiteFriends[0][] = $is_site_members->toArray();
				}
			}
		}
		//IF USER IS NOT SITE MEMBER .
		else if (empty($is_site_members->user_id)) {
		  
			$SiteNonSiteFriends[1][] = $name;
		}
	}
	
	$result[0] = '';
	$result[1] = '';
	if (!empty($SiteNonSiteFriends[1]))
	   $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));

	if (!empty($SiteNonSiteFriends[0]))    
	   $result[0] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[0])));
  return $result;
  
   
}

public function sendInvite ($friendsToJoin, $user_data = null, $pageinvite_id = null, $moduletype = null, $resource_type = null) { 
  $viewer = Engine_Api::_()->user()->getViewer();
  $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
  if (!$viewer->getIdentity()) {
   $viewer = $user_data; 
  }
  $translate = Zend_Registry::get('Zend_Translate');
  $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
  $coreversion = $coremodule->version;
  if ($coreversion < '4.1.8') {
  //Now make the body of invite messge:
  $callbackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl(). '/seaocore/auth/social-Signup/?type=linkedin&refuser=' . $viewer->getIdentity();
  }
  else 
    $callbackURL = Zend_Controller_Front::getInstance()->getBaseUrl(). '/seaocore/auth/social-Signup/?type=linkedin&refuser=' . $viewer->getIdentity();
  
   $message = $translate->_(Engine_Api::_()->getApi('settings', 'core')->invite_message);
   $message = trim($message);
   
   $API_CONFIG = array(
    'appKey'       => Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'),
	  'appSecret'    =>  Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'),
	  'callbackUrl'  => null
  );
  $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
	$OBJ_linkedin = $Api_linkedin->getApi();
  $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']); 
  
  //MAKING THE BODY TEMPLATE:
  if (!empty($moduletype)) {
  
    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    $inviter_name = $viewer->getTitle();
    $sitepageModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    if ($pageinvite_id) {
      $subject = Engine_Api::_()->getItem($resource_type, $pageinvite_id);
//      if ($subject) {
//        Engine_Api::_()->core()->setSubject($subject);
//      }
    }
    //$subject = Engine_Api::_()->core()->getSubject($moduletype . '_page');
    $host = $_SERVER['HTTP_HOST'];
    $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
    $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . $subject->getHref();

   
    $recepients_array = array();

    $site_title_linked = '<a href="' . $base_url . '" target="_blank" >' . $site_title . '</a>';
    $page_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $subject->title . '</a>';
    $page_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';
    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduletype . 'invite.set.type', 0);
    if (empty($isModType)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting($moduletype . 'invite.utility.type', convert_uuencode($sitepageModHostName));
    }

    
    $body = '';
    if (!empty($invite_message)) {
      $body .= $invite_message . "<br />";
    }
    $link = '<a href="' . $base_url . '">' . $base_url . '</a>';
    
   // $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);
    if (is_array($friendsToJoin) && !empty($friendsToJoin) && !empty($pageinvite_id) ) {
   
      $table_user = Engine_Api::_()->getitemtable('user');
      $tableName_user = $table_user->info('name');

       
      //SENDING SUGGESTION IF THE USERS ARE SITE MEMBER.
      Seaocore_Api_Linkedin_Api::parseUserContacts ($friendsToJoin, $moduletype, $subject);
      $resource_type = explode ("_", $resource_type);
      if (isset($resource_type[1]) && !empty($resource_type[1]))
        $page = $resource_type[1];
      else
        $page = $resource_type[0];

      foreach ($friendsToJoin as $recipient => $recipient_name) { 
        $recipient_join = array();
        // perform tests on each recipient before sending invite
        $recipient = trim($recipient);

        // watch out for poorly formatted emails
        if (!empty($recipient)) {
          //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
          $select = $table_user->select()
                  ->setIntegrityCheck(false)
                  ->from($tableName_user, array('user_id'))
                  ->where('email = ?', $recipient);
          $is_site_members = $table_user->fetchAll($select);

          //NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
          // IF THE PERSON IS NOT THE SITE MEMBER
          if (!isset($is_site_members[0]) || empty($is_site_members[0]->user_id)) { 
            
            if ($moduletype == 'siteevent') {
              $body = Engine_Api::_()->siteeventinvite()->getMailBody($subject, $inviter_name, $site_title_linked, $site_title );
            }
            $mailParams = array(
                  'template_header' => '',
                  'template_footer' => '',
                  'inviter_name' => $inviter_name,
                  'site_title_linked' => $site_title_linked,
                  $page . '_title_linked' => $page_title_linked,
                  $page . '_link' => $page_link,
                  'site_title' => $site_title,
                  'body' => $body,
                  $page . '_title' => $subject->title,
                  'link' => $callbackURL,
                  'host' => $host,
                  'email' => $viewer->email,
                  'queue' => true
            );
            $response = Seaocore_Api_Linkedin_Api::getBodyText (ucfirst($moduletype) . 'invite_User_Invite', $mailParams);
           
            $recipient_join[$recipient] = $recipient_name;
            $response_linkedin =  $OBJ_linkedin->message(array('0' => $recipient), $response['subject'], $response['body'], FALSE);
           
            if($response_linkedin['success'] === TRUE) {
              // message has been sent
              //saving the users info in database.
              Seaocore_Api_Facebook_Facebookinvite::seacoreInvite($recipient_join, 'linkedin', $viewer);
            } 
          
          }
          // IF THE PERSON IS A SITE MEMBER
          else { 
            
            $mailParams = array(
                'template_header' => '',
                'template_footer' => '',
                'inviter_name' => $inviter_name,
                $page . '_title_linked' => $page_title_linked,
                $page . '_link' => $page_link,
                'site_title' => $site_title,
                'body' => $body,
                $page . '_title' => $subject->title,
                'link' => $callbackURL,
                'host' => $host,
                'email' => $viewer->email,
                'queue' => true
            );
            
            $response = Seaocore_Api_Linkedin_Api::getBodyText (strtoupper($moduletype) . 'INVITE_MEMBER_INVITE', $mailParams);
            //$recipient_join[$recipient] = $recipient_name;
            $response_linkedin =  $OBJ_linkedin->message(array('0' => $recipient), $response['subject'], $response['body'], FALSE);         
          }
          
          
          
        }
      }}
   
  }
  else { 
      
      $mailParams = array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'date' => time(),
                    'sender_email' => $viewer->email,
                    'sender_title' => $viewer->getTitle(),
                    'sender_link' => $viewer->getHref(),
                    'sender_photo' => $viewer->getPhotoUrl('thumb.icon'),
                    'message' => $message,
                    'object_link' => $callbackURL,
                    
     );
     
      $response = Seaocore_Api_Linkedin_Api::getBodyText ('invite', $mailParams);
      $recipients = array();
      foreach ($friendsToJoin as $recipient => $recipient_name) {
        $recipients[] = $recipient;
      }
      $response_linkedin =  $OBJ_linkedin->message($recipients, $response['subject'], $response['body'], FALSE);
      if($response_linkedin['success'] === TRUE ) {
        // message has been sent
        //saving the users info in database.
        Seaocore_Api_Facebook_Facebookinvite::seacoreInvite($friendsToJoin, 'linkedin', $viewer);
      } 
    
  } 
  
}

public function getBodyText ($mailTemplateType, $mailParams = array(), $moduletype = '', $subject = '') { 
  $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
  $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', $mailTemplateType));
  if( null === $mailTemplate ) {
    return;
  }
  $translate = Zend_Registry::get('Zend_Translate'); 
  

  
  // Build subject/body
    

    $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
  
      $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
    $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');
     
    // Detect language
    $recipientLanguage = $translate->getLocale();
    
    if( !Zend_Locale::isLocale($recipientLanguage) ||
        $recipientLanguage == 'auto' ||
        !in_array($recipientLanguage, $translate->getList()) ) {
      $recipientLanguage = $translate->getLocale();
    }
    
    // Get subject and body
      $subjectTemplate  = (string) $translate->_($subjectKey,  $recipientLanguage);
      $bodyTextTemplate = (string) $translate->_($bodyTextKey, $recipientLanguage);
      $bodyHtmlTemplate = (string) $translate->_($bodyHtmlKey, $recipientLanguage);
      
    $isMember = false;
   // Get headers and footers
  $headerPrefix = '_EMAIL_HEADER_' . ( $isMember ? 'MEMBER_' : '' );
  $footerPrefix = '_EMAIL_FOOTER_' . ( $isMember ? 'MEMBER_' : '' );     
  
  $subjectHeader  = (string) $translate->_($headerPrefix . 'SUBJECT',   $recipientLanguage);
  $subjectFooter  = (string) $translate->_($footerPrefix . 'SUBJECT',   $recipientLanguage);
  $bodyTextHeader = (string) $translate->_($headerPrefix . 'BODY',      $recipientLanguage);
  $bodyTextFooter = (string) $translate->_($footerPrefix . 'BODY',      $recipientLanguage);
  $bodyHtmlHeader = (string) $translate->_($headerPrefix . 'BODYHTML',  $recipientLanguage);
  $bodyHtmlFooter = (string) $translate->_($footerPrefix . 'BODYHTML',  $recipientLanguage);
  
  // Do replacements
  foreach( $mailParams as $var => $val ) {
    $raw = trim($var, '[]');
    $var = '[' . $var . ']';
    //if( !$val ) {
    //  $val = $var;
    //}
    // Fix nbsp
    $val = str_replace('&amp;nbsp;', ' ', $val);
    $val = str_replace('&nbsp;', ' ', $val);
    // Replace
    $subjectTemplate  = str_replace($var, $val, $subjectTemplate);
    $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
    $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
    $subjectHeader    = str_replace($var, $val, $subjectHeader);
    $subjectFooter    = str_replace($var, $val, $subjectFooter);
    $bodyTextHeader   = str_replace($var, $val, $bodyTextHeader);
    $bodyTextFooter   = str_replace($var, $val, $bodyTextFooter);
    $bodyHtmlHeader   = str_replace($var, $val, $bodyHtmlHeader);
    $bodyHtmlFooter   = str_replace($var, $val, $bodyHtmlFooter);
  }

  // Do header/footer replacements
  $subjectTemplate  = str_replace('[header]', $subjectHeader, $subjectTemplate);
  $subjectTemplate  = str_replace('[footer]', $subjectFooter, $subjectTemplate);
  $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
  $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
  $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
  $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

  // Check for missing text or html
  if( !$bodyHtmlTemplate ) {
    $bodyHtmlTemplate = nl2br($bodyTextTemplate);
  } else if( !$bodyTextTemplate ) {
    $bodyTextTemplate = strip_tags($bodyHtmlTemplate);
  }
  
  $return_data['subject'] = $subjectTemplate;
  
  $return_data['body'] = $bodyTextTemplate;
  
  return $return_data;  
  
}

	public function getNetworkUpdates ($OBJ_linkedin, $options) { 
	    try { 
				$response = $OBJ_linkedin->updates($options); 
				
				if($response['success'] === TRUE) {  
					$updates = json_decode(json_encode((array) simplexml_load_string($response['linkedin'])), 1);			
					
				}
				else { return ;
					//echo "Error retrieving connections:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response) . "</pre>";die;
				}
			} catch (Exception $e) { return ;}
			return $updates;

	}
	
	public function getContent ($limit, $callbackURL, $from = '', $timestemp = 0) { 
    
	
		 $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
	   $LinkedinObj = $OBJ_linkedin = $Api_linkedin->getApi();
	  $LinkedinObj->setCallbackUrl($callbackURL);   
	   $LinkedinFeeds_1 = array();
	   //WE WILL FIRST FINDOUT THE SELF FEED IF THERE ARE ANY AND THEN WE WILL FIND THE OTHER FEEDS:
	   if (empty($from))
				$options = '?count=10&scope=self';
     try { 
        $total_feedcount = 0;
       
				if (!empty($from) && $from == 'after') {
							$options = '?count=10&scope=self' . '&' . $from . '=' . $timestemp;
							
							}
				
				if (!empty($options)) { 
		        $LinkedinFeeds = $this->getNetworkUpdates ($LinkedinObj, $options);
						if (isset($LinkedinFeeds['update'], $LinkedinFeeds['update'][0])) {
						$Linkedin_FeedCount = $total_feedcount = count(@$LinkedinFeeds['update']);
						$LinkedinFeeds_1 = $LinkedinFeeds;
						}
						else {
						$Linkedin_FeedCount = $total_feedcount= isset($LinkedinFeeds['update']) ? 1 : 0;
						if ($Linkedin_FeedCount) { 
							$feed_1 = $LinkedinFeeds['update'];
							unset($LinkedinFeeds['update']);
							$LinkedinFeeds_1['update'][0] = $feed_1;
							}
						}
				}
				
	
	  $LinkedinFeeds_2 = array();
	  if ($total_feedcount < (int)$limit) { 
		  $options = '?count='. (int)($limit-$total_feedcount).'&type=SHAR&type=CONN&type=PRFU&type=PICT&type=JGRP';
		   if (!empty($from)) {
					$options = $options . '&' . $from . '=' . $timestemp;
			 
			 }
		  $LinkedinFeeds_2 = $this->getNetworkUpdates ($LinkedinObj, $options);
		  
		  if (isset($LinkedinFeeds_2['update'], $LinkedinFeeds_2['update'][0])) {
		   $Linkedin_FeedCount = $total_feedcount = $total_feedcount + count(@$LinkedinFeeds_2['update']);
		   
		  }
		  else {
			$Linkedin_FeedCount = $total_feedcount = $total_feedcount + isset($LinkedinFeeds_2['update']) ? 1 : 0;
			if ($Linkedin_FeedCount && isset($LinkedinFeeds_2['update'])) {  
			   $feed_2 = $LinkedinFeeds_2['update'];
			   unset($LinkedinFeeds_2['update']);
				$LinkedinFeeds_2['update'][0] = $feed_2;
			}
		  }
		  
	  } 
    
	    if (isset($LinkedinFeeds_1['update'], $LinkedinFeeds_2['update'])) {
					foreach ($LinkedinFeeds_2['update'] as $array) {
					array_push($LinkedinFeeds_1['update'], $array); 
				}
			}
			
			
			if (isset($LinkedinFeeds_1['update'])) {
			
				foreach ($LinkedinFeeds_1['update'] as $key => $array) { 
					$timestemp_array[] =  $array['timestamp'];
				}
				
				array_multisort($timestemp_array, SORT_DESC, $LinkedinFeeds_1['update']);
			}
			else if (isset($LinkedinFeeds_2['update'])) {
			   $LinkedinFeeds_1 = $LinkedinFeeds_2;
			}
			
			
			
  } catch (Exception $e) {
	  $Linkedin_FeedCount = 0;
	  $LinkedinFeeds_1['update'] = '';
  }	  
	  
	  return array('feedcount' => $Linkedin_FeedCount, 'feedcontent' => $LinkedinFeeds_1);  
  }

}
