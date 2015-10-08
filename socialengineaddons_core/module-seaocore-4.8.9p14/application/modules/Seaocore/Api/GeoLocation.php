<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GeoLocation.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_GeoLocation extends Core_Api_Abstract {

  public function getMaxmindCurrentLocation() {

     $license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.maxmind.key'); 
    if (empty($license_key))
      return;
    $ipaddress =  '113.193.239.124'; //$_SERVER["REMOTE_ADDR"];
    $query = "http://geoip3.maxmind.com/f?l=" . $license_key . "&i=" . $ipaddress;
    $url = parse_url($query);
    $host = $url["host"];
    $path = $url["path"] . "?" . $url["query"];
    $timeout = 1;
    $fp = fsockopen($host, 80, $errno, $errstr, $timeout)
        or die('Can not open connection to server.');
    if ($fp) {
      $buf = null;
      fputs($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
      while (!feof($fp)) {
        $buf .= fgets($fp, 128);
      }
      $lines = explode("\n", $buf);
      $data = $lines[count($lines) - 1];
      fclose($fp);
    } else {
      # enter error handing code here
    }
    $data = explode(',', $data);
    $location = array();
		$session = new Zend_Session_Namespace('Current_location');
		$session->country=$location['country'] = $data['0'];
    $session->city=$location['city'] = $data['2'];
    $session->latitude=$location['latitude'] = $data['4'];
    $session->longitude=$location['longitude'] = $data['5'];
    return $location;
  }

  public function getMaxmindGeoLiteCountry() {
    $ipaddress = $_SERVER["REMOTE_ADDR"];
    $ipaddress_digites = explode('.', $ipaddress);
    $ipnum = (16777216 * $ipaddress_digites[0]) + (65536 * $ipaddress_digites[1]) + (256 * $ipaddress_digites[2]) + ($ipaddress_digites[3]);

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    $select
        ->from('engine4_seaocore_geolitecity_blocks')
        ->where('ip_start <= ?', $ipnum)
        ->where('ip_end >= ?', $ipnum)
        ->limit(1);
    $result = $select->query()->fetchObject();

    if (empty($result))
      return;
    $select = new Zend_Db_Select($db);

    $select
        ->from('engine4_seaocore_geolitecity_location')
        ->where('locId = ?', $result->location_id)
        ->limit(1);
    $resultlocation = $select->query()->fetchObject();

    if (empty($resultlocation))
      return;
    $location = array();
		$session = new Zend_Session_Namespace('Current_location');
		$session->country=$location['country'] =  $resultlocation->country;
    $session->city=$location['city'] =  $resultlocation->city;
    $session->latitude=$location['latitude'] = $resultlocation->latitude;
    $session->longitude=$location['longitude'] = $resultlocation->longitude;

    return $location;
  }
  
  public function getLatLong($params = array()) {
      
    $getSEAOLocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation(array('location' => $params['location']));
    if (empty($getSEAOLocation)) {    
        $urladdress = str_replace(" ", "+", $params['location']);

        $key = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
        if(!empty($key)) {
            $request_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$urladdress&sensor=true&key=$key";
        }
        else {
            $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";
        }

        $ch = @curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
              
				$get_value = ob_get_contents();
				if (empty($get_value)) {
				$get_value = @file_get_contents($request_url);
				}
				$json_resopnse = Zend_Json::decode($get_value);
              
        ob_end_clean();
        $status = $json_resopnse['status'];
        if (strcmp($status, "OK") == 0) {
            $result = $json_resopnse['results'];
            $details['latitude'] = (float) $result[0]['geometry']['location']['lat'];
            $details['longitude'] = (float) $result[0]['geometry']['location']['lng'];
        }
        else {
            $params['status'] = $json_resopnse;
            if(is_array($params['status']) && isset($params['status']['error_message'])) {
                $params['status'] = $params['status']['error_message'];
            }            
            $this->writeErrorLog($params);
            $details['latitude'] = $details['longitude'] = 0;     
            $details['over_query_limit'] = 1;
        }
    }
    else {
        $details['latitude'] = (float) $getSEAOLocation->latitude;
        $details['longitude'] = (float) $getSEAOLocation->longitude;
    }
    
    return $details;
  }
  
  public function writeErrorLog($params = array()) {

    //START CODE FOR CREATING THE LocationGeocodingErrors.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/LocationGeocodingErrors.log')) {
        $log = new Zend_Log();
        try {
            $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/LocationGeocodingErrors.log'));
        } catch (Exception $e) {
            //CHECK DIRECTORY
            if (!@is_dir(APPLICATION_PATH . '/temporary/log') && @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
                $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/LocationGeocodingErrors.log'));
            } else {
                //Silence ...
                if (APPLICATION_ENV !== 'production') {
                    $log->log($e->__toString(), Zend_Log::CRIT);
                } else {
                    //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
                    $log->addWriter(new Zend_Log_Writer_Null());
                }
            }
        }
    }

    //GIVE WRITE PERMISSION IF FILE EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/LocationGeocodingErrors.log')) {
        @chmod(APPLICATION_PATH . '/temporary/log/LocationGeocodingErrors.log', 0777);
    }    
    $stringData='';
    //CREATE LOG ENTRY IN LOG FILE
    if (file_exists(APPLICATION_PATH . '/temporary/log/LocationGeocodingErrors.log')) {
        $myFile = APPLICATION_PATH . '/temporary/log/LocationGeocodingErrors.log';
        $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
        $fh = fopen($myFile, 'a') or die($error);
        $current_time = date('D, d M Y H:i:s T');
        $location = $params['location'];
        $view = new Zend_View();
        $stringData .= "\n----------------------------------------------------------------------------------------------------------------\n";
        $stringData .= $view->translate('Google returned the error while detacting the "%1s" location information for "%2s" module at "%3s". Please see the error: %4s', $location, $params['module'], $current_time, $params['status']);
        $stringData .= "\n----------------------------------------------------------------------------------------------------------------\n";
        fwrite($fh, $stringData);
        fclose($fh);
    }        
        
  }

}