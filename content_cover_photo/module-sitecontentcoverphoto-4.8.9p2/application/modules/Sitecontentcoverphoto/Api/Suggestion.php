<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Suggestion.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Api_Suggestion extends Core_Api_Abstract {


	public function isModulesSupport() {
		$modArray = array(
			'facebookse' => '4.6.0p1',
			'facebooksefeed' => '4.6.0p1',
		);
		$finalModules = array();
		foreach( $modArray as $key => $value ) {
			$isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
			if( !empty($isModEnabled) ) {
				$getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
				$isModSupport = strcasecmp($getModVersion->version, $value);
				if( $isModSupport < 0 ) {
					$finalModules[] = $getModVersion->title;
				}
			}
		}
		return $finalModules;
	}
}

?>