<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: googleapi.php 2010-08-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//CHECKING FOR AUTHENTICATION.
function GoogleContactsAuth($token) {
        
  include_once('Zend/Loader.php');
  $GoogleContactsService = 'cp';
  try {
  $GoogleContactsClient   =  Zend_Gdata_AuthSub::getHttpClient(trim($token));
	//Zend_Gdata_ClientLogin::getHttpClient($GoogleContactsEmail,$GoogleContactsPass, $GoogleContactsService);
	return $GoogleContactsClient;
  }catch (Exception $e) {
	echo Zend_Registry::get('Zend_Translate')->_('You are not authorize to access this location.');
  }

}

//GETTING ALL GOOGLE CONTACTS.
function GoogleContactsAll($GoogleContactsClient, $token) {  
  ini_set('memory_limit', '2048M');
  set_time_limit(0);
  ini_set('upload_max_filesize', '100M');
  ini_set('post_max_size', '100M');
	ini_set('max_input_time', 600);
	ini_set('max_execution_time', 600);
  $scope          = "https://www.google.com/m8/feeds/contacts/default/";
 try {
	$gdata          = new Zend_Gdata($GoogleContactsClient);
	
	$google_Apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey', '');
	if (!empty($google_Apikey))
	   $query          = new Zend_Gdata_Query($scope.'full?oauth_token=' . $token);
	else 
	  $query          = new Zend_Gdata_Query($scope.'full'); 
	$query->setMaxResults(10000);
	
	$feed           = $gdata->retrieveAllEntriesForFeed($gdata->getFeed($query));
	$contactMail = '';
	$contactName = '';
	$arrContactsData = array();
  if (!empty($feed)) { 
		foreach ($feed as $entry) {
      $contactMail = '';
      $contactName = '';
			$contactName = $entry->title->text;

			$ext = $entry->getExtensionElements();

			foreach($ext as $extension) {

			if($extension->rootElement == "email") {

				$attr=$extension->getExtensionAttributes();

				$contactMail = $attr['address']['value'];

			}
			if($contactName=="") {
				$contactName = $contactMail;
			}

			}
      $email_temp = explode('@', $contactMail);
      if (!empty($email_temp['0'])) {
        $arrContactsData['contactMail'] = $contactMail;

        $arrContactsData['contactName'] = $contactName;
        if (!empty($contactMail)) {
          $arrContacts[] = $arrContactsData;
        }
      }
		}
  }
  
  if (!empty($arrContacts)) {
		sort($arrContacts);
	}

	return $arrContacts;
  }
  catch (Exception $e) {
   echo Zend_Registry::get('Zend_Translate')->_('Your contacts could not be retrive right now .Please try again after some time..');die;
 }
}

?>