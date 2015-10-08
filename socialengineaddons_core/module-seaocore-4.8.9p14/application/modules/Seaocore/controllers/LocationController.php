<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_LocationController extends Core_Controller_Action_Standard {

    public function changeMyLocationAction() {
        
        $location = $_POST['changeMyLocationValue'];
        $location_privacy = $_POST['location_privacy'];
        $updateUserLocation = $_POST['updateUserLocation'];

        $latitude = (isset($_POST['latitude']) && !empty($_POST['latitude'])) ? $_POST['latitude'] : 0;
        $longitude = (isset($_POST['longitude']) && !empty($_POST['longitude'])) ? $_POST['longitude'] : 0;
        $locationResults = array();
        if (!empty($location) && $location !== "world" && $location !== "World" && (empty($latitude) || empty($longitude))) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $location, 'module' => 'SocialEngineAddOns Core'));
            $latitude = $locationResults['latitude'];
            $longitude = $locationResults['longitude'];
        }

        if (!empty($locationResults['over_query_limit'])) {
            $this->view->error = 2;
        } else {
            $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $this->view->location = $getMyLocationDetailsCookie['location'] = $location;
            $this->view->latitude = $getMyLocationDetailsCookie['latitude'] = $latitude;
            $this->view->longitude = $getMyLocationDetailsCookie['longitude'] = $longitude;
            $this->view->error = 0;
        }

        if (Engine_Api::_()->hasModuleBootstrap('sitemember') && $updateUserLocation) {
            Engine_Api::_()->seaocore()->setUserLocation($location, $location_privacy);
        }
        
        //CLEAR THE MENUS CACHE
        Engine_Api::_()->seaocore()->clearMenuCache();        
    }

    public function setSpecificLocationAction() {
        
        $location = $_POST['specificLocation'];
        
        if(empty($location)) {
            if($_POST['current_url']) {
                return $this->_redirect($_POST['current_url'], array('prependBase' => false));
            }
            else {
                return $this->_redirectCustom($this->view->url(array('action' => 'index'), 'home'));
            }
        }
        
        $specificLocationsDetails = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getSpecificLocationRow($location);
        $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
        $getMyLocationDetailsCookie['location'] = $location;
        $getMyLocationDetailsCookie['latitude'] = $specificLocationsDetails->latitude;
        $getMyLocationDetailsCookie['longitude'] = $specificLocationsDetails->longitude;
        $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
        Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);
        
        //CLEAR THE MENUS CACHE
        Engine_Api::_()->seaocore()->clearMenuCache();        

        return $this->_redirect($_POST['current_url'], array('prependBase' => false));
    }
    
    public function setSpecificLocationDatasAction() {

        $location = $_POST['specificLocation'];
        $this->view->specificLocationDatas = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getSpecificLocationRow($location)->toArray();
    }    

    public function getSpecificLocationSettingAction() {

        $locationspecific = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);
        $currentCookiesValues = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(1);
        $this->view->saveCookies = 0;
        if (!$locationspecific && empty($currentCookiesValues['location'])) {
            $this->view->saveCookies = 1;
        }
        if (Engine_Api::_()->hasModuleBootstrap('sitemember') && isset($currentCookiesValues['location']) && !empty($currentCookiesValues['location']) && $this->_getParam('updateUserLocation')) {
            Engine_Api::_()->seaocore()->setUserLocation($currentCookiesValues['location']);
        } elseif (Engine_Api::_()->hasModuleBootstrap('sitemember')) {
            if ($this->_getParam('location') && $this->_getParam('updateUserLocation'))
                Engine_Api::_()->seaocore()->setUserLocation($this->_getParam('location'), 'everyone');
        }
    }

}