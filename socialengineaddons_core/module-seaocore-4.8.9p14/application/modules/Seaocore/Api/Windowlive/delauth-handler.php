<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delauth-handler.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

/**
 * This page handles the 'delauth' Delegated Authentication action.
 * When you create a Windows Live application, you must specify the URL 
 * of this handler page.
 */

// Load common settings.  For more information, see settings.php.


// Initialize the WindowsLiveLogin module.
$wll = WindowsLiveLogin::initFromXml($KEYFILE);
$wll->setDebug($DEBUG);

// Extract the 'action' parameter, if any, from the request.
$action = @$_REQUEST['action'];

if ($action == 'delauth') {
  $consent = $wll->processConsent($_REQUEST);

// If a consent token is found, store it in the cookie that is 
// configured in the settings.php file and then redirect to 
// the main page.
  if ($consent) {
    setcookie($COOKIE, $consent->getToken(), $COOKIETTL);
  }
  else {
    setcookie($COOKIE);
  }
  $session->redirect = 0;
  header("Location: $INDEX");
}


?>
