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

// Load classes

    include_once 'TwitterOAuth.php';

class Seaocore_Api_Twitter_Api  { 
  
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
    return ( !empty($_SESSION['twitter_token2']) && !empty($_SESSION['twitter_secret2']) );
  }

  protected function _initializeApi()
  {

    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    $twitterTableName = $twitterTable->info('name');
    // Load settings
    $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter');
    if( empty($settings['key']) ||
        empty($settings['secret'])
         ) {

      $this->_api = null;
      Zend_Registry::set('Twitter_Api', $this->_api); return;
    }

    // Try to log viewer in?
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( !isset($_SESSION['twitter_uid']) || empty($_SESSION['twitter_uid']) ||
        @$_SESSION['twitter_lock'] !== $viewer->getIdentity() ) {
      $_SESSION['twitter_lock'] = $viewer->getIdentity();
      if( $viewer && $viewer->getIdentity() ) {
        // Try to get from db
        $info = $twitterTable->select()
            ->from($twitterTableName)
            ->where('user_id = ?', $viewer->getIdentity())
            ->query()
            ->fetch();
        
        if( is_array($info) &&
            !empty($info['twitter_secret']) &&
            !empty($info['twitter_token']) ) {
          $_SESSION['twitter_uid'] = $info['twitter_uid'];
          $_SESSION['twitter_secret2'] = $info['twitter_secret'];
          $_SESSION['twitter_token2'] = $info['twitter_token'];
        } else {
          $_SESSION['twitter_uid'] = false; // @todo make sure this gets cleared properly
        }
      } else {
        // Could not get
        //$_SESSION['twitter_uid'] = false;
      }
    }
    
    $this->_api = new TwitterOAuth(
					$settings['key'],	// Consumer Key
					$settings['secret']   	// Consumer secret					
					);
   
    // Get oauth
    if( isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2']) ) { 
      
      $this->_api->token = new OAuthConsumer($_SESSION['twitter_token2'], $_SESSION['twitter_secret2']);
    
    
    } else if( isset($_SESSION['twitter_token'], $_SESSION['twitter_secret']) ) { 
      $this->_api->token = new OAuthConsumer($_SESSION['twitter_token'], $_SESSION['twitter_secret']);     
    }

  }
  
  
  
  
  
   /**
	 *  Retrive the contacts from the xml response from linkedin server.
	 * 
	 */  
public function retriveContacts ($moduletype = '') {
  
  try {
       
        $twitterOauth = $twitter = $this->getApi();       
        if ($twitter && $this->isConnected()) { 
          //get all the followers ids who are following the currently authenticated user.
          //$twitter->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
          $logged_TwitterUserfollowersIds = (array)$twitterOauth->get(
            'followers/ids'

          );
          //$logged_TwitterUserfollowersIds = $twitter->followers_ids();
          $i = 0;
          $followerids_String = '';
          $count = count($logged_TwitterUserfollowersIds['ids']);
          $result = array();
          $logged_TwitterUserfollowers = array();
           $contactInfo = array ();
                $key_temp = 0;
          foreach ($logged_TwitterUserfollowersIds['ids'] as $key => $followerId) {
            $followerids_String = $followerids_String. $followerId . ',';
            $i++;
            if ($i == 100 || ($count == $i)) { 
              //GET THE USER INFORMATIONS FOR 100 USERS AT A TIME.
              $logged_TwitterUserfollowers = (array)$twitterOauth->get(
                'users/lookup',
                array('user_id' => trim($followerids_String, ','))      

              );
              //$logged_TwitterUserfollowers = $twitter->users_lookup(array('user_id' => trim($followerids_String, ',')));
              
              if(count($logged_TwitterUserfollowers) > 0) {
               
                foreach($logged_TwitterUserfollowers as $follower) { 
                   $contactInfo[$key_temp]['name'] = $follower->screen_name ;
                   $contactInfo[$key_temp]['id'] = $follower->id ;
                   $contactInfo[$key_temp]['picture'] = $follower->profile_image_url ;
                   $key_temp++;
                }

                
               }

              
              $i = 0;
              $followerids_String = '';
            }          
            
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
      } catch (Exception $e) { 
        $this->view->TwitterLoginURL = $TwitterloginURL;
        // Silence
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
public function parseUserContacts ($contactInfo) {
  
  $viewer =  Engine_Api::_()->user()->getViewer();
	$user_id = $viewer->getIdentity();
	$table_user = Engine_Api::_()->getitemtable('user');
	$tableName_user = $table_user->info('name');
	
	$inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
  $inviteTableName = $inviteTable->info('name');
  
	$table_user_memberships = Engine_Api::_()->getDbtable('membership' , 'user');
	$tableName_user_memberships = $table_user_memberships->info('name');
	$SiteNonSiteFriends[] = '';
  $i = 0;
	foreach ($contactInfo as $values) {   
    if (empty($values['id']))      continue;
		//FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
		$select = $table_user->select()
		->setIntegrityCheck(false)
		->from($tableName_user, array('user_id', 'displayname', 'photo_id'))
		->join($inviteTableName, "$inviteTableName.new_user_id = $tableName_user.user_id", null)
		->where($inviteTableName . '.new_user_id <>?', 0)
		->limit(1)
		->where($inviteTableName . '.social_profileid = ?', $values['id']);

		$is_site_members = $table_user->fetchRow($select);
    if (empty($user_id)) {
			if ($is_site_members && !empty($is_site_members->user_id)) {
				continue;
      }

    }
		//NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
		if ($is_site_members && !empty($is_site_members->user_id) && $is_site_members->user_id != $user_id) { 
		  
			$contact =  Engine_Api::_()->user()->getUser($is_site_members->user_id);
      
			// Get data
      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (!$direction) {
        //one way
        $friendship_status = $viewer->membership()->getRow($contact);
      }
      else
        $friendship_status = $contact->membership()->getRow($viewer);
       
      if (!$friendship_status || $friendship_status->active == 0) {
        $SiteNonSiteFriends[0][] = $is_site_members->toArray();;
      }
     
		}
		//IF USER IS NOT SITE MEMBER .
		else if (!$is_site_members) {
		  
			$SiteNonSiteFriends[1][] = $values;
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

public function sendInvite ($friendsToJoin, $user_data = null, $pageinvite_id = null, $moduletype = '', $resource_type ='') { 
  $viewer = Engine_Api::_()->user()->getViewer();
  if (!$viewer->getIdentity()) {
   $viewer = $user_data; 
  } 
  $appkey = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.apikey');
	$appsecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.secretkey');
  //Now make the body of invite messge:
  //CHECK IF WWW EXIST IN THE HOST NAME.
  $removeWWW = explode("www.", $_SERVER['HTTP_HOST']);
  if (count($removeWWW) == 2) {
    $HOST = $removeWWW[1];
  }
  else {
    $HOST = $removeWWW[0];
  }
  $callbackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $HOST . Zend_Controller_Front::getInstance()->getBaseUrl(). '/seaocore/auth/social-Signup/?type=twitter&refuser=' . $viewer->getIdentity();
  
	if (!empty($appkey) && !empty($appsecret))
		$callbackURL = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url($callbackURL, $appkey, $appsecret, $format = 'txt');
  
  $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');  
  $twitterOauth = $twitter = $Api_twitter->getApi();
  $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
 if (!empty($moduletype)) { 
   if ($pageinvite_id) {
      $sitepage = Engine_Api::_()->getItem($resource_type, $pageinvite_id);
      
    }
     
    $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            .$sitepage->getHref();
    if (!empty($appkey) && !empty($appsecret))
		$inviteUrl = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url($inviteUrl, $appkey, $appsecret, $format = 'txt');
    
    $resource_type = explode('_', $resource_type);
     if (isset($resource_type[1]))
       $page = ucfirst($resource_type[1]);
     else
       $page = ucfirst($resource_type[0]);
       
    if ($moduletype == 'siteevent')
      $visit = 'join';
    else
      $visit = 'visit';
     
     if (strlen(sprintf(Zend_Registry::get('Zend_Translate')->_('You are being invited to '.$visit.' my %s.'), $page)) > 139) { 
		 $message = substr(sprintf(Zend_Registry::get('Zend_Translate')->_('You are being invited to '.$visit.' my %s.'), $page), 0, 136) ;
		 $message .= '...'; 
		 $bodyTextTemplate = $message. ' ' . $inviteUrl;
	 }
	 else 
		$bodyTextTemplate = sprintf(Zend_Registry::get('Zend_Translate')->_('You are being invited to '.$visit.' my %s.'), $page). ' ' . $inviteUrl;
   
 }
  else { 
	    $sitename = 'My Community';
      $sitename = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
      
      if (strlen(sprintf(Zend_Registry::get('Zend_Translate')->_('You are being invited to join me at %s.'), $sitename)) > 139) { 
		 $message = substr(sprintf(Zend_Registry::get('Zend_Translate')->_('You are being invited to join me at %s.'), $sitename), 0, 136) ; 
		 $message .= '...';
		 $bodyTextTemplate = $message. ' ' . $callbackURL; 
		 
     } else   
        $bodyTextTemplate = sprintf(Zend_Registry::get('Zend_Translate')->_('You are being invited to join me at %s.'), $sitename). ' ' . $callbackURL;
  }
  try {
    if ($twitter && $Api_twitter->isConnected()) { 
     
      foreach ($friendsToJoin as $follower => $follower_name) { 
        
         $response = $twitterOauth->post(
          'direct_messages/new',
          array('text' => $bodyTextTemplate, 'user_id' => $follower, 'wrap_links' => true)       

        );
        
      }
    }
        
  } catch (Exception $e) {
    
      echo  Zend_Registry::get('Zend_Translate')->_("Some problem occured while sending invitation to your followers. Please try again later.");die;
  }
  //SAVING THE INFO INTO DATABASE.
  Seaocore_Api_Facebook_Facebookinvite::seacoreInvite($friendsToJoin, 'twitter', $viewer);
  
}
  
  
}
