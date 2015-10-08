<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_ChangeMyLocationController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->locationSpecific = $locationSpecific = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);
        $this->view->widgetContentId = ($this->_getParam('widgetContentId', null) ? $this->_getParam('widgetContentId', null) : $this->view->identity);

        $locationContentTable = Engine_Api::_()->getDbTable('locationcontents', 'seaocore');

        //DELETE COOKIES IF EXISTING SPECIFIC LOCATIONS IS NOT MATCHED WITH SAVED COOKIES VALUE
        $currentLocationCookies = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(1);
        $setCookies = 1;
        if ($locationSpecific && !empty($currentLocationCookies) && !empty($currentLocationCookies['location'])) {
            $locationcontent_id = $locationContentTable->getSpecificLocationColumn(array('location' => $currentLocationCookies['location'], 'columnName' => 'locationcontent_id', 'status' => 1));
            if (empty($locationcontent_id)) {
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                setcookie('seaocore_myLocationDetails', '', time() - 3600, $view->url(array(), 'default', true));

                if (($locationDefault = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefault'))) {
                    $seaocore_myLocationDetails['latitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlatitude');
                    $seaocore_myLocationDetails['longitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlongitude');
                    $seaocore_myLocationDetails['location'] = $locationDefault;
                    $seaocore_myLocationDetails['changeLocationWidget'] = 1;
                    $seaocore_myLocationDetails['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles');
                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                    $setCookies = 0;
                }
            }
        }

        //SET DEFAULT COOKIES VALUE IF NOT SET 
        $locationCookies = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(1);
        if ($setCookies && ((empty($locationCookies) || empty($locationCookies['location'])) && ($locationDefault = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefault')))) {
            $seaocore_myLocationDetails['latitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlatitude');
            $seaocore_myLocationDetails['longitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlongitude');
            $seaocore_myLocationDetails['location'] = $locationDefault;
            $seaocore_myLocationDetails['changeLocationWidget'] = 1;
            $seaocore_myLocationDetails['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles');
            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
        }

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $user = '';
        if ($viewer->getIdentity())
            $user = Engine_Api::_()->getItem('user', $viewer->getIdentity());
        $this->view->getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
        $this->view->params = array();
        $this->view->detactLocation = $this->view->params['detactLocation'] = $this->_getParam('detactLocation', 0);

        if ($locationSpecific) {

            $this->view->locationValue = '';
            $this->view->locationValueTitle = '';
            $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            if (isset($getMyLocationDetailsCookie['location']) && !empty($getMyLocationDetailsCookie['location'])) {
                $this->view->locationValue = $getMyLocationDetailsCookie['location'];
            }

            $locations = $locationContentTable->getLocations(array('status' => 1));
            $locationsArray = array();
            foreach ($locations as $location) {
                $locationsArray[$location->location] = $location->title;
                if ($this->view->locationValue == $location->location) {
                    $this->view->locationValueTitle = $location->title;
                }
            }

            $this->view->locationsArray = $locationsArray;
        } 

        if (!$locationSpecific && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if (Engine_Api::_()->hasModuleBootstrap('sitemember')) {
                $this->view->showLocationPrivacy = $this->_getParam('showLocationPrivacy', 0);
                $this->view->updateUserLocation = $this->_getParam('updateUserLocation', 0);
                $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(1);

                if ($user && $user->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.change.user.location', 0)) {
                    $locationRow = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocation(array('location' => $user->location));
                    $getMyLocationDetailsCookie['location'] = $user->location;
                    $getMyLocationDetailsCookie['latitude'] = $locationRow->latitude;
                    $getMyLocationDetailsCookie['longitude'] = $locationRow->longitude;
                    $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);
                    $this->view->getMyLocationDetailsCookie = $getMyLocationDetailsCookie;
                } elseif ($user && !$user->location && $this->view->updateUserLocation) {
                    if (!empty($getMyLocationDetailsCookie)) {
                        Engine_Api::_()->seaocore()->setUserLocation($getMyLocationDetailsCookie['location']);
                    }
                }
            }

            if ($user && $user->getIdentity() && Engine_Api::_()->hasModuleBootstrap('sitemember')) {
                $this->view->privacyOptions = $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();
                $fields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
                $field_id = '';

                $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
                $topLevelId = $aliasedFields['profile_type']->field_id;
                $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
                $profilemapsTablename = $profilemapsTable->info('name');
                $select = $profilemapsTable->select()->from($profilemapsTablename, array('profile_type'));
                $select->where($profilemapsTablename . '.option_id = ?', $topLevelId);

                $profile_type = $select->query()->fetchColumn();

                foreach ($fields as $value) {
                    if (isset($value['type']) && $value['type'] == 'location' && $profile_type == $value['field_id']) {
                        $field_id = $value['field_id'];
                    } elseif (isset($value['type']) && $value['type'] == 'city' && $profile_type == $value['field_id']) {
                        $field_id = $value['field_id'];
                    }
                }
                if ($field_id) {
                    $values = Engine_Api::_()->fields()->getFieldsValues($user);
                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $user->getIdentity()
                    ));
                    foreach ($valueRows as $valueRow) {
                        $this->view->prevPrivacy = $valueRow->privacy;
                    }
                }
            }
        }
    }

}
