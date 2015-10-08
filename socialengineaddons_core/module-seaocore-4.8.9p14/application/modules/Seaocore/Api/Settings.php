<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_Api_Settings extends Core_Api_Abstract
{
	/**
   *
   * @param $title: String which are require for truncate
   * @return string
   */
	public function getSettings($settings)
  {
		include_once APPLICATION_PATH . '/application/modules/Seaocore/controllers/license/license5.php';
  }
}
?>