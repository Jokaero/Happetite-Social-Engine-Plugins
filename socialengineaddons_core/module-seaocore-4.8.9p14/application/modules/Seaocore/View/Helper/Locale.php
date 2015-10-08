<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locale.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_Locale extends Engine_View_Helper_Locale {

    //GET Date TIME Format.
    public function useDateLocaleFormat() {

        $localeObject = Zend_Registry::get('Locale');
        $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
        $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
        $dateLocaleString = strtolower($dateLocaleString);
        $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
        $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
        return $dateLocaleString;
    }

}
