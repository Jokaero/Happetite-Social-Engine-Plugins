<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function onSitereviewListingtypeDeleteBefore($event) {
		$payload = $event->getPayload();
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->query("DELETE FROM engine4_sitecontentcoverphoto_modules WHERE resource_type = 'sitereview_listing_$payload->listingtype_id'");
		$db->query("DELETE FROM engine4_authorization_permissions WHERE type = 'sitecontentcoverphoto_sitereview_listing_$payload->listingtype_id'");
  }

  public function onSitereviewListingtypeCreateAfter($event) {
		$payload = $event->getPayload();
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->query("INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES ('sitereview', 'sitereview_listing_$payload->listingtype_id', 'review_id', '0')");
		$contentType = 'sitecontentcoverphoto_sitereview_listing_'. $payload->listingtype_id;
		$db->query("
		INSERT IGNORE INTO `engine4_authorization_permissions` 
		SELECT 
					level_id as `level_id`, 
					'$contentType' as `type`, 
					'upload' as `name`, 
					1 as `value`, 
					NULL as `params` 
		FROM `engine4_authorization_levels` WHERE `type` IN('moderator','admin','user');
		");
  }

}