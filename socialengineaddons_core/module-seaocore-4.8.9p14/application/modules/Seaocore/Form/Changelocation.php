<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Changelocation.php 6590 2014-06-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Changelocation extends Engine_Form {

    public function init() {

        $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
        $locationsArray = array();
        $locationsArray[0] = '';
        foreach ($locations as $location) {
            $locationsArray[$location->location] = $location->title;
        }

        $locationValue = '';
        $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
        if(isset($getMyLocationDetailsCookie['location']) && !empty($getMyLocationDetailsCookie['location'])) {
        $locationValue = $getMyLocationDetailsCookie['location'];
        }

        $this->addElement('Select', 'seaocore_locationdefaultspecific', array(
            'multioptions' => $locationsArray,
            'value' => $locationValue,
            'onchange' => 'changeSpecificLocation(this.value)',
        ));
    }

}