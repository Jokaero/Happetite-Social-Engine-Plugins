<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Model_DbTable_Locationitems extends Engine_Db_Table {

  protected $_rowClass = 'Seaocore_Model_Locationitem';
  protected $_location;

 // Set the location
  public function setLocation($location=array(), $resource_type = null, $resource_id = null) {
    $select = $this->select()
						->where('resource_id = ?', $resource_id)
						->where('resource_type = ?', $resource_type)
            ->where('location = ?', $location['location']);
    $row = $this->fetchRow($select);
    $location['resource_type'] = $resource_type;
    $location['resource_id'] = $resource_id;
    if ($row == null) {
      $row = $this->createRow();
      $row->setFromArray($location);
    }
    if (null !== $row) {
      $row->setFromArray($location);
    }
    $row->save();

    return $row->locationitem_id;
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
   * Return locationitem_id 
   *
   * @param int $location
   */
  public function getLocationItemId($location, $contentProfile = null, $resource_type = null, $resource_id = null) {
    $addlocation = array();
    $addlocation['location'] = $location;
    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
//     $flag = $locationTable->hasLocation($addlocation);
//     if (!empty($flag)) {
//       $locationRow = $locationTable->getLocation($addlocation);
//       $locationitem_id = $locationRow->locationitem_id;
//       $addlocation['locationitem_id'] = $locationitem_id;
// 			$addlocation['latitude'] = $locationRow->latitude;
// 			$addlocation['longitude'] = $locationRow->longitude;
// 			$addlocation['formatted_address'] = $locationRow->formatted_address;
// 			$addlocation['country'] = $locationRow->country;
// 			$addlocation['state'] = $locationRow->state;
// 			$addlocation['zipcode'] = $locationRow->zipcode;
// 			$addlocation['city'] = $locationRow->city;
// 			$addlocation['address'] = $locationRow->address;
// 			$addlocation['zoom'] = $locationRow->zoom;
//     } else {

      //start location autosuggestion work
			$locationRow = $locationTable->getLocation(array('location' => $location));
			if (isset($_POST['locationParams']) && $_POST['locationParams']) {
				if (is_string($_POST['locationParams']))
					$_POST['locationParams'] = Zend_Json_Decoder::decode($_POST['locationParams']);
				if ($_POST['locationParams']['location'] === $location) {
					try {
						$loctionV = $_POST['locationParams'];
						$loctionV['resource_id'] = $resource_id;
						$loctionV['resource_type'] = $resource_type;
						$loctionV['zoom'] = 16;
						if (empty($locationRow))
						$locationRow = $locationTable->createRow();
						$locationRow->setFromArray($loctionV);
						$locationRow->save();
					} catch (Exception $e) {
						throw $e;
					}
				}
				$locationitem_id = $locationTable->getLocationItemIds(array('resource_id' => $resource_id, 'resource_type' => $resource_type));
				return $locationitem_id;
			}
      //end location autosuggestion work

      $urladdress = urlencode($location);
      $delay = 0;

      //Iterate through the rows, geocoding each address
      $geocode_pending = true;
      while ($geocode_pending) {
          
        $key = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
        if(!empty($key)) {
            $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true&key=$key";
        }      
        
        $getModule = $request_url; 
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
        
        if (strcmp($status, "OK") != 0) {
            $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";
            $getModule = $request_url; 
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
        }        
        
        if (strcmp($status, "OK") == 0) {
          //Successful geocode
          $geocode_pending = false;
          $result = $json_resopnse['results'];
          //Format: Longitude, Latitude, Altitude
          $latitude = $result[0]['geometry']['location']['lat'];
          $longitude = $result[0]['geometry']['location']['lng'];
          $formatted_address = $result[0]['formatted_address'];
          $len_add = 0;
          if($result[0]['address_components']) {
            $len_add = count($result[0]['address_components']);
          }
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
					try {
						$addlocation['resource_type'] = $resource_type;
						$addlocation['resource_id'] = $resource_id;
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
					} catch (Exception $e) {
						// $db->rollBack();
						throw $e;
					}
        } else if (strcmp($status, "620") == 0) {
          //sent geocodes too fast
          $delay += 100000;
        }  else {
          //failure to geocode
          $geocode_pending = false;
          echo "Google Error: <br />";
          echo "Address " . $location . " failed to geocoded. <br />";
          echo "Received status " . $status . "\n";
        }
        usleep($delay);
      }
      
      if(!empty($addlocation['latitude']) && !empty($addlocation['longitude']) && $addlocation['latitude'] != 0 && $addlocation['longitude'] != 0) {
				$locationitem_id = $locationTable->setLocation($addlocation, $resource_type, $resource_id);
      } else {
				$locationitem_id = 0;
      }

		if($contentProfile){
     return $addlocation;
		}		 

    return $locationitem_id;
  }

  /**
   * Get location
   *
   * @param array $params
   * @return object
   */
  public function getLocations($params=array()) {
 
		$locationName = $this->info('name');

		$select = $this->select();
		if (isset($params['id'])) {
			$select->where('locationitem_id = ?', $params['id']);
			return $this->fetchRow($select);
		}
		
		if (isset($params['user_ids'])) {
			$select->where('locationitem_id IN (?)', (array) $params['user_ids'])
			->where('resource_type = ?', 'user'); 
			return $this->fetchAll($select);
		}
  }
  
  /**
   * Get location
   *
   * @param array $params
   * @return object
   */
  public function getLocationItemIds($params = array()) {
 
		$locationName = $this->info('name');

		$locationitem_id = $this->select()
		  ->from($locationName, array('locationitem_id'))
			->where('resource_id = ?', $params['resource_id'])
			->where('resource_type = ?', $params['resource_type'])
			->query()
			->fetchColumn();
			return $locationitem_id;
  }
}
