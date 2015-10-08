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
class Seaocore_Api_Windowlive_Api {

  protected $_authorizedURL = 'https://login.live.com/oauth20_authorize.srf';
  protected $_key;
  protected $_secret;
  protected $_scope;
  protected $_callback;

  public function accessTokenURL() {
    return 'https://login.live.com/oauth20_token.srf';
  }

  public function init($scope, $callback) {
    $this->_key = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey');
    $this->_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey');
    $this->_scope = $scope;
    $this->_callback = $callback;
  }

  public function getAuthorizeURL($responseType = 'code') {
    $authorizedUrl = $this->_authorizedURL . '?client_id=' . $this->_key . '&response_type=' . $responseType . '&scope=' . $this->_scope . '&redirect_uri=' . $this->_callback;

    return $authorizedUrl;
  }

  public function getAccessToken($oauth_verifier) {

    $postFields = 'client_id=' . $this->_key . '&redirect_uri=' . $this->_callback . '&client_secret=' . $this->_secret . '&code=' . $oauth_verifier . '&grant_type=authorization_code';

    $request = (array) $this->http($this->accessTokenURL(), 'POST', $postFields);
    if (!isset($request['access_token'])) {
      echo Zend_Registry::get('Zend_Translate')->_("There are some problem in processing your request. Please try again after some time.");die;
    }
      
    $token = $request['access_token'];
    return $token;
  }

  public function getContacts($access_token) {

    $url = 'https://apis.live.net/v5.0/me/contacts?access_token=' . $access_token;
    $response = (array) $this->http($url, '', '');
    $contacts = array();
    if (isset($response['data'])) {
      foreach ($response['data'] as $key => $contact) { 
        if ($contact->emails->preferred) {
          $contacts[$key]['contactMail'] = $contact->emails->preferred;
          $contacts[$key]['contactName'] = $contact->name;
        }
      }
    }

    return $contacts;
  }

  public function http($url, $method, $postfields = NULL) {
    $http_info = array();
    if (!empty($method))
      $header[] = 'Content-type: application/x-www-form-urlencoded';
    $ci = curl_init();
    /* Curl settings */
    //curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
    //curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    if (!empty($method))
      curl_setopt($ci, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ci, CURLOPT_HEADER, FALSE);

    switch ($method) {
      case 'POST':
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
    }

    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    curl_close($ci);
    return json_decode($response);
  }

}
