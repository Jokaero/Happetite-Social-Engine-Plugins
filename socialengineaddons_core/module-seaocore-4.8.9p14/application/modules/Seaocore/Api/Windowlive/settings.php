<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: settings.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

// Specify true to log messages to Web server logs.
$DEBUG = false;

// Comma-delimited list of offers to be used.
$OFFERS = "Contacts.View";

// Application key file: store in an area that cannot be
// accessed from the Web.
$KEYFILE = 'config.xml';

// Name of cookie to use to cache the consent token. 
$COOKIE = 'delauthtoken';
$COOKIETTL = time() + (60);

// Default handler for Delegated Authentication.
$HANDLER = 'delauth-handler.php';

//A Proxy Server for PHP CURL to handle Go Daddy server access.  You may not need this or 
//need to use a different address depending on your ISP
$PROXY_SVR = "";

?>
