<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.seaocores.com/license/
 * @version    $Id: AuthController.php 6590 2012-26-01 00:00:00Z seaocores $
 * @author     seaocores
 */
class Seaocore_AuthController extends Core_Controller_Action_Standard {

    public function facebookAction() {
        // Clear
        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['facebook_lock']);
            unset($_SESSION['facebook_uid']);
        }
        $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
        $session = new Zend_Session_Namespace();
        $viewer = Engine_Api::_()->user()->getViewer();
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $manage_pages = $this->_getParam('manage_pages', false);
        $facebookInviteApi = Engine_Api::_()->getApi('Facebook_Facebookinvite', 'seaocore');

        //CHECKING PREMISSION FOR READ STREAM THAT IS IT IS A NEW APP OR OLD APP
        if (!empty($facebookInviteApi) && $facebookInviteApi->checkAppReadPermission()) {
            $read_stream = 'read_stream';
        } else {
            $read_stream = null;
        }

        if ($manage_pages || isset($_SESSION['manage_pages'])) {
            $_SESSION['manage_pages'] = true;
            $permissions_array = array(
                'email',
                'publish_actions',
                'manage_pages',
                'publish_pages',
                'user_friends',
                $read_stream
            );
        } else {
            $permissions_array = array(
                'email',
                'publish_actions',
                'user_friends',
                $read_stream
            );
        }
        $db = Engine_Db_Table::getDefaultAdapter();

        $URL_Home = $this->view->url(array('action' => 'home'), 'user_general', true);
        // Enabled?
//    if (!$facebook || 'none' == $settings->core_facebook_enable) {
//      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
//    }
        $fbfeedpublisher = $this->_getParam('fbfeedpublisher', '');
        $redirect_uri = $this->_getParam('redirect_urimain');
        if (!empty($redirect_uri)) {
            $session->aaf_redirect_uri = urldecode($this->_getParam('redirect_urimain'));
            if (!empty($fbfeedpublisher))
                $_SESSION['fbfeedpublisher'] = 1;
        }
        // Already connected

        if ($facebook && $facebook->getUser() && empty($_GET['redirect_urimain'])) {

            try {
                if (!isset($_GET['redirect_fbback'])) {
                    $permissions = $facebook->api("/me/permissions");

                    //CHECK IF SITE IS IN MOBILE MODE THEN WE WILL ONLY ASK ABOUT PUBLISH STREAM PERMISSION.
                    if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
                        if (!$facebookInviteApi->checkPermission('publish_actions', $permissions)) {
                            $url = $facebook->getLoginUrl(array(
                                'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_fbback=1',
                                'scope' => join(',', array(
                                    'publish_stream',
                                )),
                            ));


                            return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
                        }
                    } else {
                        if (!empty($read_stream) && !$facebookInviteApi->checkPermission('read_stream', $permissions)) {
                            $url = $facebook->getLoginUrl(array(
                                'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_fbback=1',
                                'scope' => join(',', $permissions_array),
                            ));


                            return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
                        } else {
                            $session->fb_canread = true;
                            if (!$facebookInviteApi->checkPermission('manage_pages', $permissions)) {
                                $session->fb_can_managepages = false;
                            } else
                                $session->fb_can_managepages = true;
                        }
                    }
                }
            } catch (Exception $e) {
                //continue;
            }
            $signedRequest = $facebook->getSignedRequest();
            if (isset($signedRequest['code']))
                $code = $signedRequest['code'];
            if (!empty($_GET['code'])) {
                $code = $_GET['code'];
            }


            //GETTING THE NEW ACCESS TOKEN FOR THIS REQUEST.
            $result = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB($code);

            $result = explode("&expires=", $result);
            //CLEARING THE FACEBOOK OLD PERSISTENTDATA AND SETTING THEM TO NEW.

            $facebook->setPersistentData('code', $code);
            $response_temp = array();
            if (!empty($result)) {
                $response_temp = explode("access_token=", $result[0]);
                if (!empty($response_temp[1])) {
                    $facebook->setAccessToken($response_temp[1]);
                    $facebook->setPersistentData('access_token', $facebook->getAccessToken());
                } else {
                    $response_temp[1] = $facebook->getAccessToken();
                }
            }
            if (empty($response_temp[1])) {
                $response_temp[1] = $facebook->getAccessToken();
            }


            if ($viewer->getIdentity() && (!$enable_sitemobile || !Engine_API::_()->sitemobile()->checkMode('mobile-mode'))) {

                // Attempt to connect account
                $info = $facebookTable->select()
                        ->from($facebookTable)
                        ->where('user_id = ?', $viewer->getIdentity())
                        ->orwhere('facebook_uid = ?', $facebook->getUser())
                        ->limit(1)
                        ->query()
                        ->fetch();

                $core_fbenable = Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable;
                $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
                if (empty($info) && ('publish' == $core_fbenable || 'login' == $core_fbenable || $enable_socialdnamodule)) {
                    //CHECKING FOR WHAT ADMIN HAS SET REGARDING THE REGISTRATION USING FACEBOOK.

                    $facebookTable->insert(array(
                        'user_id' => $viewer->getIdentity(),
                        'facebook_uid' => $facebook->getUser(),
                        'access_token' => $response_temp[1],
                        'code' => $code,
                        'expires' => 0, // @todo make sure this is correct
                    ));
                } else if (!empty($info)) {

                    // Save info to db
                    if ($info['facebook_uid'] == $facebook->getUser() && $info['user_id'] == $viewer->getIdentity()) {
                        $facebookTable->update(array(
                            'facebook_uid' => $facebook->getUser(),
                            'access_token' => $response_temp[1],
                            'code' => $code,
                            'expires' => 0, // @todo make sure this is correct
                                ), array(
                            'user_id = ?' => $viewer->getIdentity(),
                        ));
                    }
                }
                //}
            }
            $_SESSION['facebook_uid'] = $facebook->getUser();
            $session->aaf_fbaccess_token = $response_temp[1];
            if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
                echo "<script type='text/javascript'>
           
           $(document).ready(function() { 
             if (window.opener) {  
                if (window.opener.sm4.socialactivity.activeTab == true) {
                  window.opener.sm4.socialactivity.getTabBaseContentFeed();
                }
                else {
                  window.opener.fb_loginURL = '';
                  window.opener.sm4.socialService.initialize('facebook');
                }
                close();
              }
            }); 
        </script>";
                return;
            } else {
                if (isset($_SESSION['manage_pages']))
                    unset($_SESSION['manage_pages']);
                if (!empty($session->aaf_redirect_uri)) {
                    $redirect_uri = $session->aaf_redirect_uri;
                    unset($session->aaf_redirect_uri);
                    if (isset($_SESSION['fbfeedpublisher']) && !empty($_SESSION['fbfeedpublisher'])) {
                        unset($_SESSION['fbfeedpublisher']);
                        echo "<script type='text/javascript'>
           
               window.addEvent('domready', function() { 
                    if (window.opener) {  
                       window.opener.location.reload();
                       close();
                     }
                   }); 
               </script>";
                        return;
                    }
                    return $this->_helper->redirector->gotoUrl(urldecode($redirect_uri), array('prependBase' => false));
                } else
                // Redirect to home
                    return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
            }
        }

        // Not connected
        else {
            // Okay
            if (!empty($_GET['code'])) {
                // This doesn't seem to be necessary anymore, it's probably
                // being handled in the api initialization
                if (isset($_SESSION['manage_pages']))
                    unset($_SESSION['manage_pages']);
                return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
            }

            // Error
            else if (!empty($_GET['error'])) {
                if (isset($_SESSION['manage_pages']))
                    unset($_SESSION['manage_pages']);
                // @todo maybe display a message?
                return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
            }
            else if (isset($_GET['redirect_fbback'])) {
                if (isset($_SESSION['manage_pages']))
                    unset($_SESSION['manage_pages']);
                if (!empty($session->aaf_redirect_uri))
                    return $this->_helper->redirector->gotoUrl($session->aaf_redirect_uri, array('prependBase' => false));
            }

            // Redirect to auth page
            else {
                if (!empty($_GET['redirect_urimain'])) {
                    $session = new Zend_Session_Namespace();
                    $session->aaf_redirect_uri = urldecode($_GET['redirect_urimain']);
                }

                //CHECK IF THE SITE IS IN MOBILE MODE. THEN WE WILL ONLY ASK FOR PUBLISH STREAM.
                if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
                    $scope = array('publish_stream');
                } else {
                    $scope = join(',', $permissions_array);
                }

                $url = $facebook->getLoginUrl(array(
                    'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(),
                    'scope' => $scope,
                    'display' => 'popup',
                ));


                return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
            }
        }
    }

    public function twitterAction() {
        $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
        // Clear
        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['twitter_lock']);
            unset($_SESSION['twitter_token']);
            unset($_SESSION['twitter_secret']);
            unset($_SESSION['twitter_token2']);
            unset($_SESSION['twitter_secret2']);
        }

        if ($this->_getParam('denied')) {
            $this->view->error = 'Access Denied!';
            return;
        }

        // Setup
        $viewer = Engine_Api::_()->user()->getViewer();
        $Api_twitter = new Seaocore_Api_Twitter_Api();
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitterOauth = $twitter = $Api_twitter->getApi();
        $db = Engine_Db_Table::getDefaultAdapter();

        // Check
        if (!$twitter) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        // Connect
        try {

            $accountInfo = null;
            if (isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2'])) {
                // Try to login?
                if (!$viewer->getIdentity()) {
                    // Get account info
                    try {

                        $accountInfo = $twitterOauth->get(
                                'account/verify_credentials'
                        );
                    } catch (Exception $e) {
                        // This usually happens when the application is modified after connecting
                        unset($_SESSION['twitter_token']);
                        unset($_SESSION['twitter_secret']);
                        unset($_SESSION['twitter_token2']);
                        unset($_SESSION['twitter_secret2']);
                    }
                }
            }

            if (isset($_SESSION['twitter_token'], $_SESSION['twitter_secret'], $_GET['oauth_verifier'])) {


                $token = $twitterOauth->getAccessToken($_GET['oauth_verifier']);
                $_SESSION['twitter_token2'] = $twitter_token = $token['oauth_token'];
                $_SESSION['twitter_secret2'] = $twitter_secret = $token['oauth_token_secret'];
                $_SESSION['oauth_verified'] = true;
                // Reload api?
                $Api_twitter->clearApi();
                $twitterOauth = $Api_twitter->getApi();
                // Get account info
                $accountInfo = (array) $twitterOauth->get(
                                'account/verify_credentials'
                );

                $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
                // Save to settings table (if logged in)
                if ($viewer->getIdentity() && isset($accountInfo['id']) && (!$enable_sitemobile || !Engine_API::_()->sitemobile()->checkMode('mobile-mode'))) {
                    $info = $twitterTable->select()
                            ->from($twitterTable)
                            ->where('user_id = ?', $viewer->getIdentity())
                            ->orwhere('twitter_uid = ?', $accountInfo['id'])
                            ->query()
                            ->fetch();

                    if (!empty($info)) {
                        if (!empty($info['twitter_uid']) && $info['twitter_uid'] != $accountInfo['id']) {
//                $error_msg = Zend_Registry::get('Zend_Translate')->_('The Twitter account that you are trying to login with seems to be already used by some other user. Please logout from this Twitter account and try again.');
//                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
//                die;
                        } else if ($info['user_id'] == $viewer->getIdentity() && $info['twitter_uid'] == $accountInfo['id']) {
                            $twitterTable->update(array(
                                'twitter_uid' => $accountInfo['id'],
                                'twitter_token' => $twitter_token,
                                'twitter_secret' => $twitter_secret,
                                    ), array(
                                'user_id = ?' => $viewer->getIdentity(),
                            ));
                        }
                    } else if ('publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable || 'login' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable || $enable_socialdnamodule) {
                        $twitterTable->insert(array(
                            'user_id' => $viewer->getIdentity(),
                            'twitter_uid' => $accountInfo['id'],
                            'twitter_token' => $twitter_token,
                            'twitter_secret' => $twitter_secret,
                        ));
                    }
                    // Redirect
                    if (!empty($_GET['return_url'])) {
                        //CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
                        $queryString = explode("?", urldecode($_GET['return_url']));
                        if (isset($queryString[1])) {
                            $returnUrl = urldecode($_GET['return_url']) . '&redirect_tweet=1';
                        } else {
                            $returnUrl = $_GET['return_url'] . '?redirect_tweet=1';
                        }

                        return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
                    } else {
                        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                    }
                } else { // Otherwise try to login?
                    if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
                        echo "<script type='text/javascript'>

               $(document).ready(function() { 
             if (window.opener) {  
                if (window.opener.sm4.socialactivity.activeTab == true) {
                  window.opener.sm4.socialactivity.getTabBaseContentFeed();
                }
                else {
                   window.opener.twitter_loginURL = '';
                    window.opener.sm4.socialService.initialize('twitter');
                }
                close();
              }
            }); 
               
            </script>";
                        return;
                    }

                    if (!empty($_GET['return_url'])) {
                        //CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
                        $queryString = explode("?", urldecode($_GET['return_url']));
                        if (isset($queryString[1])) {
                            $returnUrl = urldecode($_GET['return_url']) . '&redirect_tweet=1';
                        } else {
                            $returnUrl = $_GET['return_url'] . '?redirect_tweet=1';
                        }
                        return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
                    } else {

                        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                    }
                }
            } else {

                unset($_SESSION['twitter_token']);
                unset($_SESSION['twitter_secret']);
                unset($_SESSION['twitter_token2']);
                unset($_SESSION['twitter_secret2']);

                // Reload api?
                $Api_twitter->clearApi();
                $twitterOauth = $twitter = $Api_twitter->getApi();

                // Connect account
                if (!empty($_GET['return_url']))
                    $token = $twitterOauth->getRequestToken((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?return_url=' . urlencode($_GET['return_url']));
                else
                    $token = $twitterOauth->getRequestToken('https://twitter.com/oauth/request_token', (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url());

                $_SESSION['twitter_token'] = $token['oauth_token'];
                $_SESSION['twitter_secret'] = $token['oauth_token_secret'];

                $url = $twitterOauth->getAuthorizeURL($token);

                return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
            }
        } catch (Exception $e) {
            if (in_array($e->getCode(), array(500, 502, 503))) {
                $error_msg = Zend_Registry::get('Zend_Translate')->_('Twitter is currently experiencing technical issues, please try again later.');
                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
                die;
                return;
            } else {
                $error_msg = Zend_Registry::get('Zend_Translate')->_('Twitter is currently experiencing technical issues, please try again later.');
                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
                die;
            }
        } catch (Exception $e) {
            $error_msg = Zend_Registry::get('Zend_Translate')->_('Twitter is currently experiencing technical issues, please try again later.');
            echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
            die;
        }
    }

    public function linkedinAction() {
        include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Linkedin/Linkedin.php';
        $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
        // Clear
        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['linkedin_lock']);
            unset($_SESSION['linkedin_token']);
            unset($_SESSION['linkedin_secret']);
            unset($_SESSION['linkedin_token2']);
            unset($_SESSION['linkedin_secret2']);
        }
        if ($this->_getParam('denied')) {
            $this->view->error = 'Access Denied!';
            return;
        }

        // Setup
        $viewer = Engine_Api::_()->user()->getViewer();
        $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
        $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'advancedactivity');
        $OBJ_linkedin = $Api_linkedin->getApi();
        if (empty($OBJ_linkedin))
            return;
        //$linkedinOauth = $linkedinTable->getOauth();

        $db = Engine_Db_Table::getDefaultAdapter();

        $URL_Home = $this->view->url(array('action' => 'home'), 'user_general', true);

        $redirect_uri = $this->_getParam('redirect_urimain', '');

        if (!empty($redirect_uri)) {
            $session->aaf_redirect_uri = urldecode($this->_getParam('redirect_urimain'));
        }

        if (isset($_SESSION['linkedin_token'], $_SESSION['linkedin_secret'], $_GET['oauth_verifier'])) {
            $OBJ_linkedin->setCallbackUrl((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_linkedin=' . $redirect_uri);
            $response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['linkedin_token'], $_SESSION['linkedin_secret'], $_GET['oauth_verifier']);

            if ($response['success'] == TRUE) {
                $_SESSION['linkedin_obj'] = $OBJ_linkedin;
                $_SESSION['linkedin_token2'] = $linkedin_token = $response['linkedin']['oauth_token'];
                $_SESSION['linkedin_secret2'] = $linkedin_secret = $response['linkedin']['oauth_token_secret'];

                // Reload api?
                $Api_linkedin->clearApi();
                $OBJ_linkedin = $Api_linkedin->getApi();

                // Get account info
                $getUserinfo = $OBJ_linkedin->profile('~:(id)');

                $getUserinfo = json_decode(json_encode((array) simplexml_load_string($getUserinfo['linkedin'])), 1);

                $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
                // Save to settings table (if logged in)
                if ($viewer->getIdentity() && isset($getUserinfo['id']) && (!$enable_sitemobile || !Engine_API::_()->sitemobile()->checkMode('mobile-mode'))) {
                    $info = $linkedinTable->select()
                            ->from($linkedinTable)
                            ->where('user_id = ?', $viewer->getIdentity())
                            ->orwhere('linkedin_uid = ?', $getUserinfo['id'])
                            ->query()
                            ->fetch();

                    if (!empty($info)) {
                        if (!empty($info['linkedin_uid']) && $info['linkedin_uid'] != $getUserinfo['id']) {
                            //                $error_msg = Zend_Registry::get('Zend_Translate')->_('The Twitter account that you are trying to login with seems to be already used by some other user. Please logout from this Twitter account and try again.');
                            //                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
                            //                die;
                        } else if ($info['user_id'] == $viewer->getIdentity() && $info['linkedin_uid'] == $getUserinfo['id']) {
                            $linkedinTable->update(array(
                                'linkedin_uid' => $getUserinfo['id'],
                                'linkedin_token' => $linkedin_token,
                                'linkedin_secret' => $linkedin_secret,
                                    ), array(
                                'user_id = ?' => $viewer->getIdentity(),
                            ));
                        }
                    } else {
                        $linkedinTable->insert(array(
                            'user_id' => $viewer->getIdentity(),
                            'linkedin_uid' => $getUserinfo['id'],
                            'linkedin_token' => $linkedin_token,
                            'linkedin_secret' => $linkedin_secret,
                        ));
                    }

                    // Redirect
                    if (!empty($_GET['redirect_urimain'])) {
                        //CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
                        $queryString = explode("?", urldecode($_GET['redirect_urimain']));
                        if (isset($queryString[1])) {
                            $returnUrl = urldecode($_GET['redirect_urimain']) . '&redirect_linkedin=1';
                        } else {
                            $returnUrl = $_GET['redirect_urimain'] . '?redirect_linkedin=1';
                        }

                        return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
                    } else {
                        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                    }
                } else { // Otherwise try to login?
                    if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
                        echo "<script type='text/javascript'>

                $(document).ready(function() { 
             if (window.opener) {  
                if (window.opener.sm4.socialactivity.activeTab == true) {
                  window.opener.sm4.socialactivity.getTabBaseContentFeed();
                }
                else {
                   window.opener.linkedin_loginURL = '';
                    window.opener.sm4.socialService.initialize('linkedin');
                }
                close();
              }
            }); 
               
            </script>";
                        return;
                    }

                    if (!empty($_GET['redirect_urimain'])) {
                        //CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
                        $queryString = explode("?", urldecode($_GET['redirect_urimain']));
                        if (isset($queryString[1])) {
                            $returnUrl = urldecode($_GET['redirect_urimain']) . '&redirect_linkedin=1';
                        } else {
                            $returnUrl = $_GET['redirect_urimain'] . '?redirect_linkedin=1';
                        }
                        return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
                    } else {

                        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                    }
                }
            } else {
                $OBJ_linkedin->setCallbackUrl((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_urimain=' . $redirect_uri);
                $OBJ_linkedin->setToken(NULL);
                $response = $OBJ_linkedin->retrieveTokenRequest();

                if ($response['success'] === TRUE) {
                    // store the request token						
                    $_SESSION['linkedin_token'] = $response['linkedin']['oauth_token'];
                    $_SESSION['linkedin_secret'] = $response['linkedin']['oauth_token_secret'];
                    // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
                    header('Location: ' . Linkedin::_URL_AUTH . $response['linkedin']['oauth_token']);
                    die;
                }
            }
        }

        if (!isset($_GET['redirect_linkedin'])) {

            $OBJ_linkedin->setCallbackUrl((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_urimain=' . $redirect_uri);
            $OBJ_linkedin->setToken(NULL);
            $response = $OBJ_linkedin->retrieveTokenRequest();
            if ($response['success'] === TRUE) {
                // store the request token
                //$_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
                $_SESSION['linkedin_token'] = $response['linkedin']['oauth_token'];
                $_SESSION['linkedin_secret'] = $response['linkedin']['oauth_token_secret'];

                // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
                header('Location: ' . Linkedin::_URL_AUTH . $response['linkedin']['oauth_token']);
                die;
            } else {
                echo Zend_Registry::get('Zend_Translate')->_("There are some problem in processing your request. Please try again after some time.");
                die;
            }
        }
    }

    public function instagramAction() {
        include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Instagram/Instagram.php';
        $scope = array('basic', 'likes', 'comments', 'relationships');
        $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
        // Clear
        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['instagram_lock']);
            unset($_SESSION['instagram_token']);
            unset($_SESSION['instagram_secret']);
            unset($_SESSION['instagram_token2']);
            unset($_SESSION['instagram_secret2']);
        }

        if ($this->_getParam('denied')) {
            $this->view->error = 'Access Denied!';
            return;
        }

        // Setup
        $viewer = Engine_Api::_()->user()->getViewer();
        $Api_instagram = new Seaocore_Api_Instagram_Api();
        $instagramTable = Engine_Api::_()->getDbtable('instagram', 'advancedactivity');
        $instagramOauth = $instagram = $Api_instagram->getApi();
        $db = Engine_Db_Table::getDefaultAdapter();

        // Check
        if (!$instagram) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        // Connect
        try {

            $accountInfo = null;
            if (isset($_SESSION['instagram_token2'])) {
                // Try to login?
                if (!$viewer->getIdentity()) {
                    // Get account info
                    try {

                        $accountInfo = $instagramOauth->getUser();
                    } catch (Exception $e) {
                        // This usually happens when the application is modified after connecting
                        unset($_SESSION['instagram_token']);
                        unset($_SESSION['instagram_secret']);
                        unset($_SESSION['instagram_token2']);
                        unset($_SESSION['instagram_secret2']);
                    }
                }
            }

            if (isset($_GET['code'])) {

                $token = $instagramOauth->getOAuthToken($_GET['code'], true);
                $_SESSION['instagram_token2'] = $instagram_token = $token;
                $_SESSION['instagram_secret2'] = true;
                $_SESSION['code'] = true;
                // Reload api?
                $Api_instagram->clearApi();
                $instagramOauth = $Api_instagram->getApi();
                // Get account info
                $accountInfo = $instagramOauth->getUser();

                $accountInfo = $accountInfo->data;
                $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
                // Save to settings table (if logged in)
                if ($viewer->getIdentity() && isset($accountInfo->id) && (!$enable_sitemobile || !Engine_API::_()->sitemobile()->checkMode('mobile-mode'))) {
                    $info = $instagramTable->select()
                            ->from($instagramTable)
                            ->where('user_id = ?', $viewer->getIdentity())
                            ->orwhere('instagram_uid = ?', $accountInfo->id)
                            ->query()
                            ->fetch();

                    if (!empty($info)) {
                        if (!empty($info['instagram_uid']) && $info['instagram_uid'] != $accountInfo->id) {
//                $error_msg = Zend_Registry::get('Zend_Translate')->_('The Twitter account that you are trying to login with seems to be already used by some other user. Please logout from this Twitter account and try again.');
//                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
//                die;
                        } else if ($info['user_id'] == $viewer->getIdentity() && $info['instagram_uid'] == $accountInfo->id) {
                            $instagramTable->update(array(
                                'instagram_uid' => $accountInfo->id,
                                'instagram_token' => $instagram_token,
                                'instagram_secret' => $instagram_secret,
                                    ), array(
                                'user_id = ?' => $viewer->getIdentity(),
                            ));
                        }
                    } else if (Engine_Api::_()->getApi('settings', 'core')->instagram_enable) {
                        $instagramTable->insert(array(
                            'user_id' => $viewer->getIdentity(),
                            'instagram_uid' => $accountInfo->id,
                            'instagram_token' => $instagram_token,
                            'instagram_secret' => $instagram_secret,
                        ));
                    }
                    // Redirect
                    $instasession = new Zend_Session_Namespace('instagram_redirect_session');
                    $returnUrl = $instasession->instagram_redirect_url;

                    if (!empty($returnUrl)) {
                        $returnUrl = $returnUrl;
                    } else {
                        $returnUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'home'), 'user_general', true) . '?redirect_instagram=1';
                    }
                    Zend_Session::namespaceUnset('instagram_redirect_session');
                    return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
                } else { // Otherwise try to login?
                    if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
                        echo "<script type='text/javascript'>

               $(document).ready(function() { 
             if (window.opener) {  
                if (window.opener.sm4.socialactivity.activeTab == true) {
                  window.opener.sm4.socialactivity.getTabBaseContentFeed();
                }
                else {
                   window.opener.instagram_loginURL = '';
                    window.opener.sm4.socialService.initialize('instagram');
                }
                close();
              }
            }); 
               
            </script>";
                        return;
                    }

                    //CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
                    $instasession = new Zend_Session_Namespace('instagram_redirect_session');
                    $returnUrl = $instasession->instagram_redirect_url;

                    if (!empty($returnUrl)) {
                        $returnUrl = $returnUrl . '?redirect_instagram=1';
                    } else {
                        $returnUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'home'), 'user_general', true) . '?redirect_instagram=1';
                    }
                    Zend_Session::namespaceUnset('instagram_redirect_session');

                    return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
                }
            } else {

                unset($_SESSION['instagram_token']);
                unset($_SESSION['instagram_secret']);
                unset($_SESSION['instagram_token2']);
                unset($_SESSION['instagram_secret2']);

                // Reload api?
                $Api_instagram->clearApi();
                $instagramOauth = $instagram = $Api_instagram->getApi();

                // Connect account
                if (!empty($_GET['redirect_urimain']))
                    $token = $instagramOauth->getAccessToken();
                else
                    $token = $instagramOauth->getAccessToken();

                $_SESSION['instagram_token'] = $token;
                $_SESSION['instagram_secret'] = true;


                $instagramOauth->setApiCallback((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                            'module' => 'seaocore',
                            'controller' => 'auth',
                            'action' => 'instagram'
                                ), 'seaocore_insta_auth_url', true) . '/');
                $url = $instagramOauth->getLoginUrl($scope);

                return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
            }
        } catch (Exception $e) {
            if (in_array($e->getCode(), array(500, 502, 503))) {
                $error_msg = Zend_Registry::get('Zend_Translate')->_('Instagram is currently experiencing technical issues, please try again later.');
                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
                die;
                return;
            } else {
                $error_msg = Zend_Registry::get('Zend_Translate')->_('Instagram is currently experiencing technical issues, please try again later.');
                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
                die;
            }
        } catch (Exception $e) {
            $error_msg = Zend_Registry::get('Zend_Translate')->_('Instagram is currently experiencing technical issues, please try again later.');
            echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
            die;
        }
    }

    public function instagramCheckAction() {

        $session = new Zend_Session_Namespace('instagram_redirect_session');
        $redirect_url = $this->_getParam('redirect_urimain');
        $session->instagram_redirect_url = $redirect_url;
        $Api_instagram = new Seaocore_Api_Instagram_Api();
        $instagramOauth = $Api_instagram->getApi();
        $scope = array('basic', 'likes', 'comments', 'relationships');
        $url = $instagramOauth->getLoginUrl($scope);
        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
    }

    public function logoutAction() {

        if ($this->_getParam('logout_service') == 'facebook') {
            try {

                $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();

                if ($facebook->getUser())
                    $fb_userid = $facebook->getUser();
                else
                    $fb_userid = $_SESSION['facebook_uid'];
                if ($facebook && $fb_userid) {


                    $accessToken = $facebook->getAccessToken();
                    $statusUpdate = $facebook->api('/' . $fb_userid . '/permissions/', 'DELETE', array('access_token' => $accessToken));
                }
                //unset($_SESSION['facebook_lock']);
                //unset($_SESSION['facebook_uid']);
                if (isset($session->aaf_redirect_uri))
                    unset($session->aaf_redirect_uri);
                if (isset($session->aaf_fbaccess_token))
                    unset($session->aaf_fbaccess_token);
                if (isset($session->fb_canread))
                    unset($session->fb_canread);
                if (isset($session->fb_can_managepages))
                    unset($session->fb_can_managepages);

                if (isset($session->fb_checkconnection))
                    unset($session->fb_checkconnection);
            } catch (Exception $e) {
                
            }
        } else if ($this->_getParam('logout_service') == 'twitter') {
            try {
                $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');
                $twitterOauth = $twitter = $Api_twitter->getApi();

                unset($_SESSION['twitter_token']);
                unset($_SESSION['twitter_secret']);
                unset($_SESSION['twitter_token2']);
                unset($_SESSION['twitter_secret2']);

                // Reload api?
                $Api_twitter->clearApi();
            } catch (Exception $e) {
                
            }
        } else if ($this->_getParam('logout_service') == 'linkedin') {
            try {

                unset($_SESSION['linkedin_token']);
                unset($_SESSION['linkedin_secret']);
                unset($_SESSION['linkedin_token2']);
                unset($_SESSION['linkedin_secret2']);
                $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');


                // Reload api?
                $Api_linkedin->clearApi();
                $OBJ_linkedin = $Api_linkedin->getApi();
            } catch (Exception $e) {
                
            }
        } else if ($this->_getParam('logout_service') == 'instagram') {
            try {

                unset($_SESSION['instagram_token']);
                unset($_SESSION['instagram_secret']);
                unset($_SESSION['instagram_token2']);
                unset($_SESSION['instagram_secret2']);
                $Api_instagram = Engine_Api::_()->getApi('instagram_Api', 'seaocore');


                // Reload api?
                $Api_instagram->clearApi();
                $OBJ_instagram = $Api_instagram->getApi();
            } catch (Exception $e) {
                
            }
        }

        echo Zend_Json::encode(array('success' => 1));
        exit();
    }

    //INTERMEDIATE SIGNUP PROCESS WHEN INVITED USERS ARE COMING FROM THE OTHER SOCIAL INVITER SERVICES:

    public function socialSignupAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer && $viewer->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $callbackURL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url()
                . '?type=' . $this->_getParam('type', null) . '&refuser=' . $this->_getParam('refuser', null);

        if (1) {
            if ($this->_getParam('type', null) == 'facebook') {
                $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();

                if ($this->_getParam('code', '') && !$facebook->getUser()) {

                    $result = $tempresult = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB($this->_getParam('code'), $callbackURL);
                    $tempresult = Zend_Json::decode($tempresult);
                    if (!isset($tempresult['error']) && !empty($result)) {
                        $result = explode("&expires=", $result);
                        $response_temp = explode("access_token=", $result[0]);
                        $facebook->setAccessToken($response_temp[1]);
                        $facebook->setPersistentData('access_token', $facebook->getAccessToken());
                    }
                }

                try {
                    $userFBInfo = $facebook->api('/me');
                    $recipientId = $userFBInfo['id'];
                    $recipientEmail = $userFBInfo['email'];
                } catch (Exception $e) {

                    //CHECK IF ALREADY EMAIL ADDRESS EXIST OF THIS USER
                    if ($facebook->getUser()) {
                        $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
                        $userInvited = $inviteTable->fetchRow(array('social_profileid = ?' => $facebook->getUser()));
                        $validator = new Zend_Validate_EmailAddress();
                        $validator->getHostnameValidator()->setValidateTld(false);
                        if ($validator->isValid($userInvited->recipient)) {
                            $recipientId = $facebook->getUser();
                            $recipientEmail = $userInvited->recipient;
                        }
                    }
                    if (!isset($recipientId)) {

                        $url = $facebook->getLoginUrl(array(
                            'redirect_uri' => $callbackURL,
                            'scope' => join(',', array(
                                'email',
                                'user_birthday'
                            )),
                        ));

                        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
                    }
                }
//        	}
//        }
            } else if ($this->_getParam('type', null) == 'linkedin') {
                $API_CONFIG = array(
                    'appKey' => Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'),
                    'appSecret' => Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'),
                    'callbackUrl' => $callbackURL
                );
                $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
                $OBJ_linkedin = $Api_linkedin->getApi();
                $OBJ_linkedin->setApplicationKey($API_CONFIG['appKey']);
                $OBJ_linkedin->setApplicationSecret($API_CONFIG['appSecret']);
                $OBJ_linkedin->setCallbackUrl($API_CONFIG['callbackUrl']);
                //$OBJ_linkedin = new Seaocore_Api_Linkedin_Linkedin($API_CONFIG); 
                if (!isset($_GET['oauth_verifier']) && !isset($_GET['email'])) {
                    $OBJ_linkedin->setToken(NULL);
                    $response = $OBJ_linkedin->retrieveTokenRequest();

                    if ($response['success'] === TRUE) {
                        // store the request token
                        $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];

                        // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
                        header('Location: ' . Linkedin::_URL_AUTH . $response['linkedin']['oauth_token']);
                        die;
                    }
                } else {
                    if (!isset($_GET['email'])) {
                        $response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['oauth']['linkedin']['request']['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']);
                        if ($response['success'] === TRUE) {
                            // the request went through without an error, gather user's 'access' tokens
                            $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];

                            // set the user as authorized for future quick reference
                            $_SESSION['oauth']['linkedin']['authorized'] = TRUE;

                            //FETCHING THE PROFILE INFO OF THE CURRENT USER.
                            $response = $OBJ_linkedin->profile('~:(id)');

                            if ($response['success'] === TRUE) {
                                if ($settings->getSetting('user.signup.checkemail')) {
                                    $response['linkedin'] = new SimpleXMLElement($response['linkedin']);
                                    $recipientId = (string) $response['linkedin']->id;
                                    //CHECK IF THE USER HAS ALREADY REGISTERED TO SITE OR NOT.
                                    $this->validateUser($recipientId, '');
                                    $this->view->form = $form = new Seaocore_Form_getEmail();
                                    $form->refuser->setValue($this->_getParam('refuser', null));
                                } else {
                                    $response['linkedin'] = new SimpleXMLElement($response['linkedin']);
                                    $recipientId = (string) $response['linkedin']->id;
                                    $recipientEmail = '';
                                }
                            }
                        }
                    } else {
                        $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
                        $response = $OBJ_linkedin->profile('~:(id)');
                        $response['linkedin'] = new SimpleXMLElement($response['linkedin']);
                        $recipientId = (string) $response['linkedin']->id;
                        $recipientEmail = $_GET['email'];
                    }
                }
            } else if ($this->_getParam('type', null) == 'twitter') {
                if (!isset($_GET['redirect_tweet']) && !isset($_GET['email'])) {
                    $TwitterloginURL = Zend_Controller_Front::getInstance()->getRouter()
                                    ->assemble(array('module' => 'seaocore', 'controller' => 'auth',
                                        'action' => 'twitter'), 'default', true) . '?return_url=' . urlencode($callbackURL);
                    return $this->_helper->redirector->gotoUrl($TwitterloginURL, array('prependBase' => false));
                }
                try {
                    $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    $twitterOauth = $twitter = $Api_twitter->getApi();
                    if (!isset($_GET['email'])) {
                        if ($twitter && $Api_twitter->isConnected()) {
                            $accountInfo = (array) $twitterOauth->get(
                                            'account/verify_credentials'
                            );
                            $recipientId = (string) $accountInfo['id'];

                            if ($settings->getSetting('user.signup.checkemail')) {

                                $recipientEmail = '';
                                //CHECK IF THE USER HAS ALREADY REGISTERED TO SITE OR NOT.


                                $this->validateUser($recipientId, '');
                                $this->view->form = $form = new Seaocore_Form_getEmail();
                                $form->refuser->setValue($this->_getParam('refuser', null));
                                $form->type->setValue($this->_getParam('type', null));
                            } else {

                                $recipientEmail = '';
                            }
                        }
                    } else {
                        if (isset($_GET['email'])) {
                            $recipientEmail = $_GET['email'];
                        }
                        $accountInfo = (array) $twitterOauth->get(
                                        'account/verify_credentials'
                        );
                        $recipientId = (string) $accountInfo['id'];
                    }
                } catch (Exception $e) {
                    
                }
            }
            if ($this->_getParam('type', null) !== null && $this->_getParam('refuser', null) !== null && isset($recipientId)) {
                $this->validateUser($recipientId, $recipientEmail);
            }
        } else {
            return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
        }

        if (!empty($recipientId) && !empty($recipientEmail)) {
            return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
        }
    }

    //SAVING FACEBOOK INVITED USERS.....

    public function saveFbInviterAction() {
        if ($this->getRequest()->isPost()) {
            $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
            $fbUser_ids = $this->getRequest()->get('ids');
            if (empty($fbUser_ids))
                exit();
            $fb_User = array();
            //GETTING THE DISPLAY NAME OF THE USERS ALSO.
            $session = new Zend_Session_Namespace();
            $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
            try {
                if ($facebook) {
                    foreach ($fbUser_ids as $key => $id) :

                        $displayname = $facebook->api('/' . $id);

                        $fb_User[$id] = $displayname['name'];
                    endforeach;
                }
            } catch (Exception $e) {
                
            }
            if (empty($fb_User)) :
                foreach ($fbUser_ids as $key => $id) :
                    $fb_User[$id] = '';
                endforeach;
            endif;

            $facebookInvite->seacoreInvite($fb_User, 'facebook');
            echo Zend_Json::encode(array('status' => true));
            exit();
        }
    }

    //CHECK FOR VALID USER AND THEN REDIRECT
    public function validateUser($recipientId, $recipientEmail) {

        $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');

        if (empty($recipientEmail)) {
            $userInvited = $inviteTable->fetchRow(array('social_profileid = ?' => $recipientId));

            if ($userInvited) {
                if (!empty($userInvited->recipient) && $userInvited->recipient != $this->_getParam('type') . '-' . $recipientId) {
                    $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                                'module' => 'invite',
                                'controller' => 'signup',
                                    ), 'default', true)
                            . '?code=' . $userInvited->code . '&email=' . $userInvited->recipient;
                    return $this->_helper->redirector->gotoUrl($inviteUrl, array('prependBase' => false));
                }
            } else {
                return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
            }
        } else {
            //CHECK EITHER THIS USER IS VALID USER OR NOT:
            $userInvited = $inviteTable->fetchRow(array('recipient = ?' => $this->_getParam('type') . '-' . $recipientId));
            if ($userInvited) {

                $inviteTable->update(array(
                    'recipient' => $recipientEmail,
                        ), array(
                    'new_user_id = ?' => 0,
                    'recipient = ?' => $this->_getParam('type') . '-' . $recipientId
                ));

                $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                            'module' => 'invite',
                            'controller' => 'signup',
                                ), 'default', true)
                        . '?code=' . $userInvited->code . '&email=' . $recipientEmail;


                return $this->_helper->redirector->gotoUrl($inviteUrl, array('prependBase' => false));
            } else {
                $userInvited = $inviteTable->fetchRow(array('recipient = ?' => $recipientEmail, 'social_profileid = ?' => $recipientId));
                if ($userInvited) {

                    $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                                'module' => 'invite',
                                'controller' => 'signup',
                                    ), 'default', true)
                            . '?code=' . $userInvited->code . '&email=' . $recipientEmail;
                    return $this->_helper->redirector->gotoUrl($inviteUrl, array('prependBase' => false));
                }
            }
        }
    }

    //THIS IS THE MESSAGE WHICH WILL BE SHOWN TO THE FACEBOOK INIVTED USERS.
    public function fbInviteAction() {
        $refuser = 0;
        $this->_helper->layout->disableLayout(true);
        $callBackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/seaocore/auth/fb-Invite';
        if (isset($_GET['fbredirect']) && isset($_GET['type']))
            $callBackURL = $callBackURL . '?fbredirect=' . $_GET['fbredirect'] . '&type=' . $_GET['type'];
        $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
        $tempresult = array();
        if ($this->_getParam('code') && (!$facebook || !$facebook->getUser())) {
            $result = $tempresult = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB($this->_getParam('code'), $callBackURL);

            $tempresult = Zend_Json::decode($tempresult);
            if (!isset($tempresult['error']) && !empty($result)) {
                $result = explode("&expires=", $result);
                $response_temp = explode("access_token=", $result[0]);

                $facebook->setAccessToken($response_temp[1]);
                $facebook->setPersistentData('access_token', $facebook->getAccessToken());
            }
        }

        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        if (!isset($tempresult['error']) && $facebook && $facebook->getUser()) {
            //NOW CHECK IF THE FACEBOOK USER IS FRIEND OF THE INVITED USER IF SO THEN MAKE AN ENTRY FOR THIS USER.
            $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
            //CHECK IF THIS USER'S ENTRY IS ALREADY THERE
            if (isset($_GET['fbredirect']))
                $row = $inviteTable->fetchRow(array('code = ?' => $_GET['fbredirect'], 'recipient = ?' => ''));
            else
                $row = $inviteTable->fetchRow(array('social_profileid = ?' => $facebook->getUser()));
            //
            if (empty($row)) {
                return $this->_helper->redirector->gotoUrl(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl(), array('prependBase' => false));
            }
            $fbcurrentuid = $facebook->getUser();
            $fql_areFriends = "SELECT uid1, uid2 FROM friend WHERE uid1=$row->social_profileid AND uid2=$fbcurrentuid";
            try {
                $friends = $facebook->api(array(
                    'method' => 'fql.query',
                    'access_token' => $facebook->getAccessToken(),
                    'query' => $fql_areFriends,
                ));
            } catch (Exception $e) {
                //continue
            }
            if (($friends == null || empty($friends)) && false) {
                return $this->_helper->redirector->gotoUrl(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl(), array('prependBase' => false));
            } else {
                $userInvited = $inviteTable->fetchRow(array('social_profileid = ?' => $facebook->getUser()));
                if (!$userInvited) :
                    $displayname = $facebook->api('/' . $facebook->getUser());
                    do {
                        $inviteCode = substr(md5(rand(0, 999) . 'facebook-' . $facebook->getUser()), 10, 7);
                    } while (null !== $inviteTable->fetchRow(array('code = ?' => $inviteCode)));
                    //CHECK THE INVITE TYPE EITHER USER INVITE OR PAGE INVITE
                    if (!empty($_GET['type'])) {
                        $module = explode("_", $_GET['type']);
                        $invite_type = $module[0] . '_invite';
                    } else
                        $invite_type = 'user_invite';
                    $newrow = $inviteTable->createRow();
                    $newrow->user_id = $row->user_id;
                    $newrow->recipient = 'facebook-' . $facebook->getUser();
                    $newrow->code = $inviteCode;
                    $newrow->timestamp = new Zend_Db_Expr('NOW()');
                    $newrow->message = '';
                    $newrow->social_profileid = $facebook->getUser();
                    if (isset($newrow->service))
                        $newrow->service = 'facebook';
                    if (isset($newrow->invite_type))
                        $newrow->invite_type = $invite_type;
                    if (isset($newrow->displayname))
                        $newrow->displayname = $displayname;
                    $newrow->save();
                endif;
            }

            //IS JOINED THE WEBSITE USING THEIR FACEBOOK ACCOUNT INTEGRATION.
            $info = $facebookTable->select()
                    ->from($facebookTable)
                    ->where('facebook_uid = ?', $facebook->getUser())
                    ->limit(1)
                    ->query()
                    ->fetch();

            // IS INVITED AND SIGNUPED FOR WEBSITE OR NOT.              

            $userInvited = $inviteTable->fetchRow(array('social_profileid = ?' => $facebook->getUser(), 'recipient != ?' => ''));

            //CASE: IS INVITED AND YET NOT JOINED THIS WEBSITE:
            if ($userInvited && empty($userInvited->new_user_id) && empty($info)) {

                $this->view->callBackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/seaocore/auth/social-Signup/?type=facebook&refuser=' . $userInvited->user_id;
            } else {

                $this->view->callBackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
            }
        } else if (isset($_GET['fbredirect'])) {
            $this->view->callBackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
        } else {

            $FBloginURL = $facebook->getLoginUrl(array(
                'redirect_uri' => $callBackURL,
                'scope' => join(',', array(
                    'email',
                    'user_birthday',
                    'user_status'
                )),
            ));
            $this->view->callBackURL = $FBloginURL;
        }
    }

    public function inviteCodeAction() {
        $this->_helper->layout->disableLayout(true);
        $isFacebookModule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
        //CHECK EITHER FACEBOOK IS ACCESSING THIS FROM THE BACKEND OR IT'S RUNNING VIA NORMAL BROWSER WINDOW.    
        if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            $isFacebook = strstr($_SERVER['HTTP_USER_AGENT'], "facebook") ? true : false;
        } else {
            $isFacebook = false;
        }

        $invite_code = $this->_getParam('code', '');
        $invite_type = $this->_getParam('type', '');
        $this->view->page_id = $page_id = $this->_getParam('id', '0');
        $this->view->resource_type = $resource_type = $this->_getParam('type', '');


        if (empty($invite_code)) {
            if ($isFacebook)
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            else
                return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
        }

        //IF IT'S AN VALID INVITE CODE THEN FIND THE INTER FACEBOOK ID FROM THE INVITE TABLE:
        $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
        //CHECK IF THIS USER'S ENTRY IS ALREADY THERE
        $row = $inviteTable->fetchRow(array('code = ?' => $invite_code, 'recipient = ?' => ''));
        if (!empty($row)) {
            $this->view->fbuid = $row->social_profileid;
            $this->view->user_id = $row->user_id;
        } else {
            if ($isFacebook)
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            else
                return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
        }


        $this->view->invitecode = $invite_code;
        $this->view->isFacebook = $isFacebook;
        $this->view->picture = '';

        if ($isFacebook && $isFacebookModule) {
            $metainfos = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo('home');
            $admin_meta_photo_id = $metainfos->photo_id;
            if (!empty($admin_meta_photo_id)) {
                if (!Engine_Api::_()->seaocore()->isCdn()) {
                    $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.profile');
                } else {
                    $fbmeta_imageUrl = $metainfos->getPhotoUrl('thumb.profile');
                }
            } else {
                $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . 'Facebookse' . '/externals/images/nophoto_site_logo_profile.png';
            }
//       $imageUrl = Engine_Api::_()->user()->getUser($row->user_id)->getPhotoUrl('thumb.normal'); 
//       if (empty($imageUrl))
//							  	$imageUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
            $this->view->picture = $fbmeta_imageUrl;
        } else {
            $this->view->isFacebook = 0;
            if (!$isFacebook) {
                $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
                $FBloginURL = $facebook->getLoginUrl(array(
                    'redirect_uri' => ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/seaocore/auth/fb-Invite?fbredirect=' . $invite_code . '&type=' . $invite_type,
                    'scope' => join(',', array(
                        'email',
                        'user_birthday',
                        'user_status'
                    )),
                ));
                return $this->_helper->redirector->gotoUrl($FBloginURL, array('prependBase' => false));
            } else if ($isFacebook) {
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }
        }
    }

    public function windowsliveAction() {
        $session = new Zend_Session_Namespace();
        $callback = $session->windowslivecallback;
        $windowliveURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . Zend_Controller_Front::getInstance()->getRequest()->getModuleName() . '/usercontacts/getwindowlivecontacts?redirect_uri=' . $callback . '&code=' . $_GET['code'];

        return $this->_helper->redirector->gotoUrl($windowliveURL, array('prependBase' => false));
    }

}
