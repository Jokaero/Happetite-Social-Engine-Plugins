<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ListItems.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Seaocore_Model_DbTable_Locations extends Engine_Db_Table {

  protected $_rowClass = 'Seaocore_Model_Location';
  protected $_location;

 // Set the location
  public function setLocation($location=array()) {
    $select = $this->select()
            ->where('location = ?', $location['location']);
    $row = $this->fetchRow($select);
    if ($row == null) {
      $row = $this->createRow();
      $row->setFromArray($location);
    }
    if (null !== $row) {
      $row->setFromArray($location);
    }
    $row->save();

    return $row->location_id;
  }
 // Get the location
  public function getLocation($location=array()) {
    $select = $this->select();
    foreach ($location as $key => $value) {
      $select->where(" $key = ?", $value);
    }
    return $this->fetchRow($select);
  }
 // Check the location
  public function hasLocation($location=array()) {
    $flage = 0;

    $result = $this->getLocation($location);
    if (!empty($result)) {
      $flage = 1;
    }

    return $flage;
  }
 // Delete the location
  public function clearLocation() {
    $result = $this->getLocation($location);
     if (!empty($result)) {
      $result->delete();
    }

  }

  /**
   * Return location_id 
   *
   * @param int $location
   */
  public function getLocationId($location, $contentProfile = null) {
    $addlocation = array();
    $addlocation['location'] = $location;
    $locationTable = Engine_Api::_()->getDbtable('locations', 'seaocore');
    $flag = $locationTable->hasLocation($addlocation);
    if (!empty($flag)) {
      $locationRow = $locationTable->getLocation($addlocation);
      $location_id = $locationRow->location_id;
      $addlocation['location_id'] = $location_id;
			$addlocation['latitude'] = $locationRow->latitude;
			$addlocation['longitude'] = $locationRow->longitude;
			$addlocation['formatted_address'] = $locationRow->formatted_address;
			$addlocation['country'] = $locationRow->country;
			$addlocation['state'] = $locationRow->state;
			$addlocation['zipcode'] = $locationRow->zipcode;
			$addlocation['city'] = $locationRow->city;
			$addlocation['address'] = $locationRow->address;
			$addlocation['zoom'] = $locationRow->zoom;
    } else {
      $urladdress = urlencode($location);
      $delay = 0;

      //Iterate through the rows, geocoding each address
      $geocode_pending = true;
      while ($geocode_pending) {
        $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";
        $ch = curl_init();
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
          //Successful geocode
          $geocode_pending = false;
          $result = $json_resopnse['results'];
          //Format: Longitude, Latitude, Altitude
          $latitude = $result[0]['geometry']['location']['lat'];
          $longitude = $result[0]['geometry']['location']['lng'];
          $formatted_address = $result[0]['formatted_address'];
          $len_add = count($result[0]['address_components']);
          $address = '';
          $country = '';
          $state = '';
          $zip_code = '';
          $city = '';
          for ($i = 0; $i < $len_add; $i++) {
            $types_location = $result[0]['address_components'][$i]['types'][0];

            if ($types_location == 'country') {
              $country = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'administrative_area_level_1') {
              $state = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'administrative_area_level_2') {
              $city = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'postal_code' || $types_location == 'zip_code') {
              $zip_code = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'street_address') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'locality') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            }else if ($types_location == 'route') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            }else if ($types_location == 'sublocality') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            }
          }
          $addlocation['location'] = $location;
          $addlocation['latitude'] = $latitude;
          $addlocation['longitude'] = $longitude;
          $addlocation['formatted_address'] = $formatted_address;
          $addlocation['country'] = $country;
          $addlocation['state'] = $state;
          $addlocation['zipcode'] = $zip_code;
          $addlocation['city'] = $city;
          $addlocation['address'] = $address;
          $addlocation['zoom'] = 16;
        } else if (strcmp($status, "620") == 0) {
          //sent geocodes too fast
          $delay += 100000;
        } else {
          //failure to geocode
          $geocode_pending = false;
          echo "Address " . $location . " failed to geocoded. ";
          echo "Received status " . $status . "\n";
        }
        usleep($delay);
      }
      $location_id = $locationTable->setLocation($addlocation);
    }

		if($contentProfile){
     return $addlocation;
		}		 

    return $location_id;
  }
}