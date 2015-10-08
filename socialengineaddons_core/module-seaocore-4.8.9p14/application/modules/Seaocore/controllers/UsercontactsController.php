<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UsercontactsController.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_UsercontactsController extends Core_Controller_Action_Standard
{
	
 
	//FUNCTION FOR GETTING GOOGLE CONTACTS OF THE REQUESTED USER.
   public function getgooglecontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$this->_helper->layout->disableLayout();
		include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Gmail/googleapi.php';
		$session = new Zend_Session_Namespace();
		$session->windowlivemsnredirect = 0;		
	  $session->yahooredirect = 0;
	  $session->googleredirect =1;	
		$session->aolredirect = 0;
		$session->facebookredirect =0;
		$session->linkedinredirect = 0;
		$session->twitterredirect =0;
		$viewer =  Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
    $gmail_RedirectURL_Confirm = Engine_Api::_()->getApi('settings', 'core')->getSetting('gmail.redirecturl.confirm', 0);
    // the domain that you entered when registering your application for redirecting from google site.
    $keep_original_url = $this->_getParam('redirect_uri', null);
    
    if ($gmail_RedirectURL_Confirm)
      $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl().'/seaocore/usercontacts/getgooglecontacts';
    else
      $callback = $keep_original_url;
    
    $front = Zend_Controller_Front::getInstance();
//    $currenturl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
    if (!empty($keep_original_url)) {
      $session->keep_original_url = $keep_original_url;	
    }

    $this->view->moduletype = '';
	
	//HERE WE ARE CHECKING IF REQUEST IS NOT AN AJAX REQUEST THEN WE WILL REDIRECT TO GOOGLE SITE.FOR GETTING TOKEN.
    if (empty($_POST['task']) && !empty($keep_original_url)) {
      //CHECK IF ADMIN HAS SET THE THE GOOGLE API KEYS THERE:
      $google_Apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey', '');
      if (!empty($google_Apikey)) {
        $google_redirect_URL = 'https://accounts.google.com/o/oauth2/auth?client_id=' . $google_Apikey . '&redirect_uri=' . urlencode($callback) . '&scope=' . urlencode('https://www.google.com/m8/feeds') . '&response_type=token';
      }
      else {
        $scope  = "http://www.google.com/m8/feeds/contacts/default/";
        $google_redirect_URL = Zend_Gdata_AuthSub::getAuthSubTokenUri($callback, urlencode($scope), 0, 1);
      }
      header('location: ' . $google_redirect_URL);
    }
    
		//IF THE TASK IS TO SHOWING THE LIST OF FRIENDS.
		if (!empty($_POST['task']) && $_POST['task'] == 'get_googlecontacts') {
			//IF WE GET THE TOKEN MEANS GOOGLE HAS RESPOND SUCCESSFULLY.THIS IS ONE TIME USES TOKEN
			if (!empty($_POST['token'])) { 
			   //CHECK IF ADMIN HAS SET THE THE GOOGLE API KEYS THERE:
         $google_Apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey', '');
         if (empty($google_Apikey))
				   $token = urldecode($_POST['token']);
				 else
				   $token = $_POST['token'];   
       
				//CHECKING THE AUTHENTICITY OF REQUESTED USER EITHER THIS TOKEN IS VALID OR NOT FOR GETTING CONTACTS.
				$result = GoogleContactsAuth ($token); 
				//$result = 1;
				if (!empty($result)) {
						$session->googleredirect = 0;
					//FETCHING THE ALL GOOGLE CONTACTS OF THIS USER.
					$GoogleContacts =  GoogleContactsAll($result, $token);
				
					//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
					if (!empty($GoogleContacts)) { 
					  if (isset($_POST['moduletype']) && !empty($_POST['moduletype'])) { 
					    $this->view->moduletype = str_replace("site", "", $_POST['moduletype']);
					    $SiteNonSiteFriends = array();
					    $result = array();
					    $SiteNonSiteFriends[1] = $GoogleContacts;
					    
                $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
                $SiteNonSiteFriends[1] = $result[1];
                
					  }
					  else {
              $SiteNonSiteFriends = $this->parseUserContacts ($GoogleContacts);
					  }
						 
						if (!empty($SiteNonSiteFriends[0]))  {
							$this->view->task = 'show_sitefriend';
							$this->view->addtofriend = $SiteNonSiteFriends[0];
						}
						if (!empty($SiteNonSiteFriends[1])) { 
							$this->view->addtononfriend = $SiteNonSiteFriends[1];
						}
					}
					if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  	$this->view->errormessage = true;
					}
				}
			}
		}
    else {       
        $this->view->redirectToOrigine = $session->keep_original_url;
    }
  }
  
  
  //FUNCTION FOR GETTING FACEBOOK CONTACTS OF THE REQUESTED USER.
  public function getfacebookcontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$this->_helper->layout->disableLayout();
		$session = new Zend_Session_Namespace();
		$session->windowlivemsnredirect = 0;		
	  $session->yahooredirect = 0;
	  $session->googleredirect =0;
	  $session->facebookredirect =1;	
		$session->aolredirect = 0;
		$session->linkedinredirect = 0;
		$session->twitterredirect =0;
		$viewer =  Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
   
	 $callback = $this->_getParam('redirect_uri', null) . '?redirect_fbinvite=1';
	//HERE WE ARE CHECKING IF REQUEST IS NOT AN AJAX REQUEST THEN WE WILL REDIRECT TO FACEBOOK SITE.FOR GETTING TOKEN.
    if (empty($_POST['task'])) {  
      
      $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
  	  $facebook = $facebookInvite->getFBInstance();
    	$FBloginURL = $facebook->getLoginUrl(array(
          'redirect_uri' => $callback,
          'scope' => join(',', array(
            'user_friends'
           
            //'offline_access',
         )),
    ));
     
 
      header('location: ' . $FBloginURL);
    }
    
		//IF THE TASK IS TO SHOWING THE LIST OF FRIENDS.
		if (!empty($_POST['task']) && $_POST['task'] == 'get_facebookcontacts') { 
		  $session->facebookredirect =0;
		  $facebookapi = new Seaocore_Api_Facebook_Facebookinvite();
      $facebook = $facebookapi->getFBInstance();
		  if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
		    
		    $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url(array(), 'user_signup', true);
		  }
		  else {
		     $callback = $_POST['redirect_uri'] . '?redirect_fbinvite=1';
		  }
		  
			$friends_invite = $facebookapi->facebook_invitefriend($callback, $_POST['invitepage_id'], $_POST['moduletype'], $_POST['resource_type']);
      $fbUserInfo = $facebook->api('/' . $facebook->getUser());
      $displayname = $fbUserInfo['name'];
      if (!empty($_POST['invitepage_id']))
      	$link = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/invite-code/' . Seaocore_Api_Facebook_Facebookinvite::getInviteCode($facebook->getUser(), $displayname) . '/' . $_POST['invitepage_id'] . '/' . $_POST['resource_type'];
      
      else 
      	$link = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/invite-code/' . Seaocore_Api_Facebook_Facebookinvite::getInviteCode($facebook->getUser(), $displayname) ;
 
       //ADD A RANDOM QUERY PARAMETER.
      $link = $link . '?c=' . rand(0, 999);	
			echo Zend_Json::encode(array('link' => $link, 'message' => $friends_invite['message'], 'exclude_ids' => $friends_invite['exclude_ids'])); 
			exit();
		}
      exit();          
  }

  //FUNCTION FOR GETTING YAHOO CONTACTS OF THE REQUESTED USER.
  public function getyahoocontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
   	$session = new Zend_Session_Namespace();
		$this->_helper->layout->disableLayout();
		// the domain that you entered when registering your application for redirecting from google site.
	 	$callback = $this->_getParam('redirect_uri', null);
		include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Yahoo/getreqtok.php';
		//STEP:1 FIRST WE WILL GET REQUEST VALID OAUTH TOKEN OAUTH TOKEN SECRET FROM YAHOO.
		if (empty($_POST['oauth_verifier'])) {
			$session->windowlivemsnredirect = 0;		
			$session->yahooredirect = 1;
			$session->googleredirect = 0;	
			$session->aolredirect = 0;
			$session->facebookredirect =0;
			$session->linkedinredirect = 0;
			$session->twitterredirect =0;
			// Get the request token using HTTP GET and HMAC-SHA1 signature
			$retarr = get_request_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
																	$callback, false, true, true);
			
			if (! empty($retarr) ) { 
				unset($session->oauth_token_secret);	
				unset($session->oauth_token);				
				$session->oauth_token_secret = $retarr[3]['oauth_token_secret'];
				$session->oauth_token = $retarr[3]['oauth_token'];
				$redirecturl = urldecode($retarr[3]['xoauth_request_auth_url']);
				header('location: ' . $redirecturl);
			}
		}
    //STEP:2 AFTER GETTING REQUESTED OAUTH TOKE AND OAUTH TOKEN SECRET WE WILL GET OAUTH VERIFIER BY GRANTING ACCESS TO THIRD PARTY FOR FATCHING YAHOO CONTACTS.
		else if (!empty($_POST['oauth_verifier'])) {
			$session->redirect = 0;
			$request_token=$session->oauth_token;
			$request_token_secret=$session->oauth_token_secret;
			$oauth_verifier= $_POST['oauth_verifier'];
			//STEP:3 AFTER GETTING OAUTH VERIFIER AND OTHER TOKENS WE WILL AGAIN CALL YAHOO API TO GET OAUTH VERIFY SUCCESS TOKEN.
			$retarr = get_access_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                           $request_token, $request_token_secret,
                           $oauth_verifier, false, true, true);	
			
       if (!empty($retarr)) {
					$guid=$retarr[3]['xoauth_yahoo_guid'];
					$access_token=urldecode($retarr[3]['oauth_token']);
					$access_token_secret=$retarr[3]['oauth_token_secret'];
          // Call Contact API
					$YahooContacts = callcontact(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                      $guid, $access_token, $access_token_secret,
                      false, true);
           $session->yahooredirect = 0;      		
					//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
					if (!empty($YahooContacts)) { 
					  
					  if (isset($_POST['moduletype']) && !empty($_POST['moduletype'])) {
					    $this->view->moduletype = str_replace("site", "", $_POST['moduletype']);
					    $SiteNonSiteFriends = array();
					    $result = array();
					    $SiteNonSiteFriends[1] = $YahooContacts;
					    
                $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
                $SiteNonSiteFriends[1] = $result[1];
                
					  }
					  else 
                $SiteNonSiteFriends = $this->parseUserContacts ($YahooContacts);
						   
						if (!empty($SiteNonSiteFriends[0]))  {
							$this->view->task = 'show_sitefriend';
							$this->view->addtofriend = $SiteNonSiteFriends[0];
						}
						if (!empty($SiteNonSiteFriends[1])) { 
							$this->view->addtononfriend = $SiteNonSiteFriends[1];
						}
					}
					if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  	$this->view->errormessage = true;
					}
				}
			}
	}
	
	//FUNCTION FOR GETTING YAHOO CONTACTS OF THE REQUESTED USER.
  public function getlinkedincontactsAction () { 
    include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Linkedin/Linkedin.php';
	  ini_set('display_errors', FALSE);
		error_reporting(0);
   	$session = new Zend_Session_Namespace();
		$this->_helper->layout->disableLayout();
		
		// the domain that you entered when registering your application for redirecting from google site.
	 	$callback = $this->_getParam('redirect_uri', null);
		
		$API_CONFIG = array(
    'appKey'       => Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'),
	  'appSecret'    =>  Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'),
	  'callbackUrl'  => $callback 
  );
      $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
			$OBJ_linkedin = $Api_linkedin->getApi();
      $OBJ_linkedin->setCallbackUrl($callback);
		//STEP:1 FIRST WE WILL GET REQUEST VALID OAUTH TOKEN OAUTH TOKEN SECRET FROM YAHOO.
		if (empty($_POST['oauth_verifier'])) {
			$session->windowlivemsnredirect = 0;		
			$session->linkedinredirect = 1;
			$session->googleredirect = 0;	
			$session->aolredirect = 0;
			$session->facebookredirect =0;
			$session->yahooredirect =0;
			$session->twitterredirect =0;
			// Get the request token using HTTP GET and HMAC-SHA1 signature
		  
		  $OBJ_linkedin->setToken(NULL);
			$response = $OBJ_linkedin->retrieveTokenRequest();
			
			if($response['success'] === TRUE) {
        // store the request token
        $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
      
        // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
        header('Location: ' . Linkedin::_URL_AUTH . $response['linkedin']['oauth_token']);
      } else  { 
       echo Zend_Registry::get('Zend_Translate')->_("There are some problem in processing your request. Please try again after some time.");die;
     }
		}
    //STEP:2 AFTER GETTING REQUESTED OAUTH TOKE AND OAUTH TOKEN SECRET WE WILL GET OAUTH VERIFIER BY GRANTING ACCESS TO THIRD PARTY FOR FATCHING YAHOO CONTACTS.
		else if (!empty($_POST['oauth_verifier'])) {
			$session->redirect = 0;
			$response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['oauth']['linkedin']['request']['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_POST['oauth_verifier']);
		
			
			if($response['success'] === TRUE) { 
			    $session->linkedinredirect = 0;
          // the request went through without an error, gather user's 'access' tokens
          $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
          
          // set the user as authorized for future quick reference
          $_SESSION['oauth']['linkedin']['authorized'] = TRUE;            
          
          $LinkedinContacts = $Api_linkedin->retriveContacts ($OBJ_linkedin, $_POST['moduletype']);
          $this->view->moduletype = str_replace("site", "", $_POST['moduletype']);
          
          if (!empty($LinkedinContacts[0]))  {
						$this->view->task = 'show_sitefriend';
						$this->view->addtofriend = $LinkedinContacts[0];
					}
					if (!empty($LinkedinContacts[1])) { 
						$this->view->addtononfriend = $LinkedinContacts[1];
					}
					
					if (empty($LinkedinContacts[0]) && empty($LinkedinContacts[1])) {
				  	$this->view->errormessage = true;
					}
        
         
        } else {
          // bad token access
          echo  Zend_Registry::get('Zend_Translate')->_("There are some problem in retriving your contacts. Please try again after some time.");die;
        }
			}
	}
	
	
	//FUNCTION FOR GETTING WINDOW LIVE  CONTACTS OF THE REQUESTED USER.
	public function getwindowlivecontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$session = new Zend_Session_Namespace();
		$session->windowlivemsnredirect = 1;		
		$session->yahooredirect = 0;
		$session->googleredirect = 0;
		$session->aolredirect = 0;
		$session->facebookredirect =0;
		$session->linkedinredirect = 0;
		$session->twitterredirect =0;		
		$this->_helper->layout->disableLayout();
		$callback = $this->_getParam('redirect_uri', null);
		if($callback)
		 $session->windowslivecallback =$callback;	
    $Api_windowlive = Engine_Api::_()->getApi('windowlive_Api', 'seaocore');
    $scope = 'wl.basic,wl.contacts_emails';
    $returnURL = urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl()."/seaocore/auth/windowslive");
    $Api_windowlive->init($scope, $returnURL); //INITIALIZING THE APP FOR FURTHER PROCESS
  		if (!isset($_POST['task'])) {
        if (isset($_GET['code'])) {  
          $access_Token = $Api_windowlive->getAccessToken($_GET['code']);
          $callback = $callback . '?oauth_verifier=' . $access_Token;
          header("Location: $callback ");         
        }
        else {
          $session->redirect = 1;        
          $url = $Api_windowlive->getAuthorizeURL('code');  	
          header( 'Location:'.$url);
        }
	  }
		
		//IF REQUEST IS AJAX FOR SHOWING WINDOW LIVE CONTACTS.
		if ( isset ($_POST['task']) && isset ($_POST['oauth_verifier'])) { 
		  
			$WindowLiveContacts = $Api_windowlive->getContacts($_POST['oauth_verifier']);
      $this->view->moduletype = '';
      if (isset($_POST['moduletype']) && !empty($_POST['moduletype'])) {
        $this->view->moduletype = str_replace("site", "", $_POST['moduletype']);
      }
			$session->windowlivemsnredirect = 0;		
			//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
			if (!empty($WindowLiveContacts)) {
				$SiteNonSiteFriends = $this->parseUserContacts ($WindowLiveContacts);
				if (!empty($SiteNonSiteFriends[0]))  {
					$this->view->task = 'show_sitefriend';
					$this->view->addtofriend = $SiteNonSiteFriends[0];
				}
				if (!empty($SiteNonSiteFriends[1])) { 
					$this->view->addtononfriend = $SiteNonSiteFriends[1];
				}
			}
			if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  $this->view->errormessage = true;
			}
		}
	}

	//FUNCTION FOR GETTING WINDOW LIVE  CONTACTS OF THE REQUESTED USER.
	public function getaolcontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$this->_helper->layout->disableLayout();
		$session = new Zend_Session_Namespace();
    $session->windowlivemsnredirect = 0;		
	  $session->yahooredirect = 0;
	  $session->googleredirect =0;	
		$session->aolredirect = 1;
		$session->facebookredirect =0;
		$session->linkedinredirect = 0;
		$session->twitterredirect =0;	
		include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Aol/aolapi.php';
		if (!empty($session->aol_email)) {
			$AolContacts = getaolcontacts ($session->aol_email, $session->aol_password, false);
			//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
			if (!empty($AolContacts)) { 
			  if (!empty ($_POST['task'])) {
    		  $session->aolredirect = 0;
    		}
				 if (isset($_POST['moduletype']) && !empty($_POST['moduletype'])) { 
  			    $this->view->moduletype = str_replace("site", "", $_POST['moduletype']);
  			    $SiteNonSiteFriends = array();
  			    $result = array();
  			    $SiteNonSiteFriends[1] = $AolContacts;
			      $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
            $SiteNonSiteFriends[1] = $result[1];
 			  }
 			  else 
				    $SiteNonSiteFriends = $this->parseUserContacts ($AolContacts);
				if (!empty($SiteNonSiteFriends[0]))  {
					$this->view->task = 'show_sitefriend';
					$this->view->addtofriend = $SiteNonSiteFriends[0];
				}
				if (!empty($SiteNonSiteFriends[1])) { 
					$this->view->addtononfriend = $SiteNonSiteFriends[1];
				}
			}
			if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  $this->view->errormessage = true;
			}
		}

  }


  //FUNTION FOR GETTING USERNAME AND PASSWORD OF AOL MAIL.
  public function aolloginAction() {
		$this->_helper->layout->disableLayout();
		$this->view->form = $form = new Seaocore_Form_Aollogin();
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
			include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Aol/aolapi.php';
	    $values = $form->getValues();
			$session = new Zend_Session_Namespace();
			$session->windowlivemsnredirect = 0;		
			$session->yahooredirect = 0;
			$session->googleredirect =0;	
			$session->aolredirect = 1;
			$session->facebookredirect =0;
			$session->linkedinredirect = 0;	
			$session->twitterredirect =0;
			$loginsuccess = getaolcontacts($values['email'], $values['password'], true);
			if ($loginsuccess) {
				$session->aol_email = $values['email'];
				$session->aol_password = $values['password'];		
				// the domain that you entered when registering your application for redirecting from google site.
				$callback = $this->_getParam('redirect_uri', null) . '?redirect_aol=1';
				header('location:'. $callback);
			}
			else {
        $this->view->error = Zend_Registry::get('Zend_Translate')->_("Incorrect Username or Password");
			}
		}
  }
	
  //FUNCTION FOR GETTING YAHOO CONTACTS OF THE REQUESTED USER.
  public function gettwittercontactsAction () {
		ini_set('display_errors', False);
		error_reporting(0);
   	$session = new Zend_Session_Namespace();
		$this->_helper->layout->disableLayout();
		
		// the domain that you entered when registering your application for redirecting from google site.
	 	$callback = $this->_getParam('redirect_uri', null);

		//STEP:1 FIRST WE WILL GET REQUEST VALID OAUTH TOKEN OAUTH TOKEN SECRET FROM TWITTER.
		if (!isset($_POST['task'])) {
			$session->windowlivemsnredirect = 0;		
			$session->linkedinredirect = 0;
			$session->googleredirect = 0;	
			$session->aolredirect = 0;
			$session->facebookredirect =0;
			$session->yahooredirect =0;
			$session->twitterredirect =1;
			// Get the request token using HTTP GET and HMAC-SHA1 signature
	    $TwitterloginURL = Zend_Controller_Front::getInstance()->getRouter()
                      ->assemble(array('module' => 'seaocore', 'controller' => 'auth',
                          'action' => 'twitter'), 'default', true) . '?return_url=' . $callback;
      return $this->_helper->redirector->gotoUrl($TwitterloginURL, array('prependBase' => false));
		}
    //STEP:2 AFTER GETTING REQUESTED OAUTH TOKE AND OAUTH TOKEN SECRET WE WILL GET OAUTH VERIFIER BY GRANTING ACCESS TO THIRD PARTY FOR FATCHING YAHOO CONTACTS.
		else if (isset($_POST['task']) && $_POST['task'] == 'get_twiitercontacts') {
		 $Api_twitter = new Seaocore_Api_Twitter_Api();
     $TwitterContacts = $Api_twitter->retriveContacts ($_POST['moduletype']);
     $this->view->moduletype = str_replace("site", "", $_POST['moduletype']); 
			//if($response['success'] === TRUE) { 
			   $session->twitterredirect =0;
          
          if (!empty($TwitterContacts[0]))  {
						$this->view->task = 'show_sitefriend';
						$this->view->addtofriend = $TwitterContacts[0];
					}
					if (!empty($TwitterContacts[1])) { 
						$this->view->addtononfriend = $TwitterContacts[1];
					}
					
					if (empty($TwitterContacts[0]) && empty($TwitterContacts[1])) {
				  	$this->view->errormessage = true;
					}
        
         
//        } else {
//          // bad token access
//          echo  Zend_Registry::get('Zend_Translate')->_("There are some problem in retriving your contacts. Please try again after some time.");die;
//        }
			}
	}
	
 //FUNCTION FOR PARSING USER CONTACTS IN 2 PARTS SITE MEMBERS AND NONSITE MEMBERS.
  public function parseUserContacts ($UserContacts) { 
    $userFriendsDirection = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
    
    $viewer =  Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
		$table_user = Engine_Api::_()->getitemtable('user');
		$tableName_user = $table_user->info('name');
		$table_user_memberships = Engine_Api::_()->getDbtable('membership' , 'user');
		$tableName_user_memberships = $table_user_memberships->info('name');
		$SiteNonSiteFriends[] = '';
		foreach ($UserContacts as $values) {     
			//FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
      if (empty($values['contactMail']))
		     $values['contactMail'] = $values['contactName']; 
			$select = $table_user->select()
			->setIntegrityCheck(false)
			->from($tableName_user, array('user_id', 'displayname', 'photo_id'))
			->where('email = ?', $values['contactMail']);

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
					// The contact should not be my friend, and neither of us should have sent a friend request to the other.
					$select = $table_user_memberships->select()
					->setIntegrityCheck(false)
					->from($tableName_user_memberships, array('user_id'));
          if ($userFriendsDirection == 1) { 
            //TWO WAY FRIENDSHIP
            $select->where($tableName_user_memberships . '.resource_id = ' . $user_id .' AND ' . $tableName_user_memberships . '.user_id = ' . $is_site_members->user_id )
            ->orwhere($tableName_user_memberships . '.resource_id = ' . $is_site_members->user_id .' AND ' . $tableName_user_memberships . '.user_id = ' .$user_id );
          }
          else {
            //ONE WAY FRIENDSHIP
            $select->where($tableName_user_memberships . '.resource_id = ' . $is_site_members->user_id .' AND ' . $tableName_user_memberships . '.user_id = ' . $user_id .' AND ' . $tableName_user_memberships . '.user_approved = 1');
          }
					$already_friend = $table_user->fetchRow($select);
					
					//IF THIS QUERY RETURNS EMPTY RESULT MEANS THIS USER IS SITE MEMBER BUT NOT FRIEND OF CURRENTLY LOGGEDIN USER SO WE WILL SEND HIM TO FRIENDSHIP REQUEST.
					if (empty($already_friend->user_id)) { 
						$SiteNonSiteFriends[0][] = $is_site_members->toArray();
					}
				}
			}
			//IF USER IS NOT SITE MEMBER .
			else if (empty($is_site_members->user_id)) {
			  
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

	//FUNCTION FOR GETTING CSV FILE CONTACTS OF THE REQUESTED USER.
	function getcsvcontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
    $this->_helper->layout->disableLayout();
    $session = new Zend_Session_Namespace();
    $filebaseurl = APPLICATION_PATH.'/public/seaocore/csvfiles/';
		//$filebaseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/suggestion/csvfiles/'; 
    $validator = new Zend_Validate_EmailAddress();
    $validator->getHostnameValidator()->setValidateTld(false);
		//READING THE CSV FILE AND FINDING THE EMAIL FOR CORROSPONDING ROW.
	  //WE ARE READING THE FILE FOR VERIOUS DELIMITERS TYPE. 
		$probable_delimiters = array(",", ";", "|", " ");
    foreach ($probable_delimiters as $delimiter) {
    	$fp = fopen($filebaseurl . $session->filename,'r') or die("can't open file");
			$k = 0;	

			while($csv_line = fgetcsv($fp,4096, $delimiter)) { 
        
				for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
					try {  
            //CHECK IF THE SPECE IS IN STRING THEN REPLACE MULTIPLE SPACE INTO SINGLE SPACE.
            $output = preg_replace('!\s+!', ' ', $csv_line[$i]);
             $tempstring = explode(" ", $output);
            $csv_line[$i] = $tempstring[0]; 
  					if ($validator->isValid(trim($csv_line[$i]))) {
  						$usercontacs_csv[$k]['contactMail'] =  $csv_line[$i];
  						$usercontacs_csv[$k]['contactName'] =  $csv_line[$i];
  						$k++;
  						break;
  					}
				  }
				  catch (Exception $e) {
				    continue;
				  }
				}
        
			}
      
			if (!empty($usercontacs_csv[0]['contactMail'])) {
				break;	
			}
		
			//CLOSING THE FILE AFTER READING. 
			fclose($fp) or die("can't close file");
		}
		//AFTER READING THE FILE WE ARE UNLINKING THE FILE.
		$filebaseurl = APPLICATION_PATH.'/public/seaocore/csvfiles/'.$session->filename;
		@unlink($filebaseurl);
		unset($session->filename);
		if (!empty($usercontacs_csv)) {
		  sort($usercontacs_csv);
			 if (isset($_POST['moduletype']) && !empty($_POST['moduletype'])) {
  			    $this->view->moduletype = str_replace("site", "", $_POST['moduletype']);
  			    $SiteNonSiteFriends = array();
  			    $result = array();
  			    $SiteNonSiteFriends[1] = $usercontacs_csv;
			      $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
            $SiteNonSiteFriends[1] = $result[1];
 			  }
 			  else 
				    $SiteNonSiteFriends = $this->parseUserContacts ($usercontacs_csv);
			if (!empty($SiteNonSiteFriends[0]))  {
				$this->view->task = 'show_sitefriend';
				$this->view->addtofriend = $SiteNonSiteFriends[0];
			}
			if (!empty($SiteNonSiteFriends[1])) { 
				$this->view->addtononfriend = $SiteNonSiteFriends[1];
			}
		}
		if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
			$this->view->errormessage = true;
		}
	}

	public function uploadsAction() { 
    // Prepare
    if( empty($_FILES['Filedata']) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('File failed to upload. Check your server settings (such as php.ini max_upload_filesize).');
      return;
    }
    $session = new Zend_Session_Namespace();
    $session->filename = $this->view->filename = $_FILES['Filedata']['name'];		
     $file_path = APPLICATION_PATH . '/public/seaocore/csvfiles';
		if( !is_dir($file_path) && !mkdir($file_path, 0777, true) ) {
        //$filename = APPLICATION_PATH . "/application/languages/$localeCode/custom.csv";
        mkdir(dirname($file_path));
        chmod(dirname($file_path), 0777);
        touch($file_path);
        chmod($file_path, 0777);
      }	
    
    // Prevent evil files from being uploaded
    $disallowedExtensions = array('php');
    if (in_array(end(explode(".", $_FILES['Filedata']['name'])), $disallowedExtensions)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('File type or extension forbidden.');
      return;
    }
		
    $info = $_FILES['Filedata'];
    $targetFile = $file_path . '/' . $info['name'];
    $vals = array();

    if( file_exists($targetFile) ) {
      $deleteUrl = $this->view->url(array('action' => 'delete')) . '?path=' . $file_path . '/' . $info['name'];
      $deleteUrlLink = '<a href="'.$this->view->escape($deleteUrl) . '">' . Zend_Registry::get('Zend_Translate')->_("delete") . '</a>';
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("File already exists. Please %s before trying to upload.", $deleteUrlLink);
      return;
    }

    if( !is_writable($file_path) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Path is not writeable. Please CHMOD 0777 the public/admin directory.');
      return;
    }
    
    // Try to move uploaded file
    if( !move_uploaded_file($info['tmp_name'], $targetFile) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      return;
    }
    
    $this->view->status = 1;

//     if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
//       return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
//     }
  }
  
  

  //IF THE TASK IS TO JOIN THIS SITE.
  function invitetositeAction() { 
	   //$invite_friend = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.ltsug');
	   //if (!empty($invite_friend)) {
	      $friendsToJoin = $_POST['nonsitemembers']; 
	      foreach ($friendsToJoin as $friend) {
    	    //SPLIT THIS IN FORM OF NAME AND EMAIL.
//          if ($_POST['socialtype'] == 'linkedin' || $_POST['socialtype'] == 'twitter') {
//             $friend_info = explode("#", $friend);
//          }
//          else
            $friend_info = explode("#", $friend);
          
          if (isset($friend_info[1]))
            $recepients[$friend_info[0]] = (string)$friend_info[1];
          else
            $recepients[$friend] = (string)$friend;
        }
        
	      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    	  if (empty($user_id)) {
      		$session = new Zend_Session_Namespace();
      		$invite_sessionids = array();
      		$invite_sessionids[$_POST['socialtype']] = $friendsToJoin;
      		
      		 if (!isset($session->suggestion_invites[$_POST['socialtype']])) { 
  				    $session->suggestion_invites[$_POST['socialtype']] = $friendsToJoin;
  				  }
  				  else {
  				    $session->suggestion_invites[$_POST['socialtype']] = array_merge($session->suggestion_invites[$_POST['socialtype']], $friendsToJoin);
  				    $session->suggestion_invites[$_POST['socialtype']] = array_unique($session->suggestion_invites[$_POST['socialtype']]);
  				    
  				  }
      		return;
    	  }
	  
    	  //THIS IS AN SPECIAL CASE BECAUSE IN CASE OF LINKEDIN WE DO NOT HAVE EMAIL IDS:
    	  if ($_POST['socialtype'] == 'linkedin') { 
    	   
    	   
    	    $Api_linkedin = Seaocore_Api_Linkedin_Api::sendInvite ($recepients, null, $_POST['invitepage_id'], $_POST['moduletype'], $_POST['resource_type']);
          //$Api_linkedin->sendInvite ($friendsToJoin);
    	  }
    	  else if ($_POST['socialtype'] == 'twitter') { 
    	    
    	    $Api_linkedin = Seaocore_Api_Twitter_Api::sendInvite ($recepients, '', $_POST['invitepage_id'], $_POST['moduletype'], $_POST['resource_type']);
          //$Api_linkedin->sendInvite ($friendsToJoin);
    	  }
    	  else { 
    	      if (isset($_POST['moduletype']) && !empty($_POST['moduletype'])) {
    	        Engine_Api::_()->getApi('Invite', 'Seaocore')->sendPageInvites($friendsToJoin, $_POST['invitepage_id'], $_POST['moduletype'], '', $_POST['resource_type']);
    	      }
    	      else { 
    	         Engine_Api::_()->getApi('Invite', 'Seaocore')->sendInvites($recepients, $_POST['socialtype'], @$_POST['moduletype'], @$_POST['custom_message']);
    	      }
    	     
    	  }
	   //}
	   return;
  }
  
  //IF THE TASK IS TO SEND FRIENDSHIP REQUESTS.
  function addtofriendAction() { 
    if (!$this->_helper->requireUser()->isValid())
  	  return;
  
  	// Disable Layout.
  	$this->_helper->layout->disableLayout(true);
  	//$add_friend_sugg = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.qatsug');
  	//if (!empty($add_friend_sugg)) {
  	  $friendsToAdd = $_POST['sitemembers'];
  	  foreach ($friendsToAdd as $friend) {
  		  Engine_Api::_()->getApi('Invite', 'Seaocore')->addAction($friend);
  	  }
  	//}
  }
  
  //IF THE SITE ADMIN HAS CONFIRMED THE GOOGLE REDIRECT URL TO BE THE NEW ONE:
  
  function gmailredirecturlconfirmAction () {
    $this->_helper->layout->disableLayout(true);
    $settings = Engine_Api::_()->getApi('settings', 'core')->setSetting('gmail_redirecturl_confirm', 1);
    exit();
    
  }
}

?>