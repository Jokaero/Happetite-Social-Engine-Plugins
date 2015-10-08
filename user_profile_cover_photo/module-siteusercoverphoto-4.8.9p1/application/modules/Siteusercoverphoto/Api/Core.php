<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Api_Core extends Core_Api_Abstract {

  public function setDefaultUserProfileWidgets($contentTableName, $pagesTableName) {
    $contentTable = Engine_Api::_()->getDbtable($contentTableName, 'sitemobile');
    $contentTableName = $contentTable->info('name');
    $pageTable = Engine_Api::_()->getDbtable($pagesTableName, 'sitemobile');
    $pageTableName = $pageTable->info('name');
    $selectPage = $pageTable->select()
            ->from($pageTableName, array('page_id'))
            ->where('name =?', 'user_profile_index')
            ->limit(1);
    $fetchPageId = $selectPage->query()->fetchColumn();
    if (!empty($fetchPageId)) {
      $contentTable->delete(array('name =?' => 'sitemobile.profile-photo-status', 'page_id =?' => $fetchPageId));
      $content_main_id = $contentTable->select()
              ->from($contentTableName, array('content_id'))
              ->where('page_id =?', $fetchPageId)
              ->where('name =?', 'main')
              ->query()
              ->fetchColumn();
      if (!empty($content_main_id)) {
        $content_middle_id = $contentTable->select()
                ->from($contentTableName, array('content_id'))
                ->where('page_id =?', $fetchPageId)
                ->where('name =?', 'middle')
                ->where('parent_content_id =?', $content_main_id)
                ->query()
                ->fetchColumn();
        if (!empty($content_middle_id)) {
          $content_id = $contentTable->select()
                  ->from($contentTableName, array('content_id'))
                  ->where('page_id =?', $fetchPageId)
                  ->where('name =?', 'siteusercoverphoto.user-cover-mobile-photo')
                  ->query()
                  ->fetchColumn();
          if (empty($content_id)) {
            $contentCreate = $contentTable->createRow();
            $contentCreate->page_id = $fetchPageId;
            $contentCreate->name = 'siteusercoverphoto.user-cover-mobile-photo';
            $contentCreate->type = 'widget';
            $contentCreate->parent_content_id = $content_middle_id;
            $contentCreate->params = '{"title":"","titleCount":"true"}';
            $contentCreate->order = 1;
            $contentCreate->save();
          }
        }
      }
    }
  }

  public function getCount() {
		$corePagesTable = Engine_Api::_()->getDbtable('pages', 'core');
		$corePagesTableName = $corePagesTable->info('name');
		$coreContentPageID = $corePagesTable->select()->from($corePagesTableName, array('page_id'))->where('name =?', 'user_profile_index')->query()->fetchColumn();
   	$coreContentTable = Engine_Api::_()->getDbtable('content', 'core');
		$coreContentTableName = $coreContentTable->info('name');
		$coreContentName = $coreContentTable->select()->from($coreContentTableName, array('name'))->where('page_id =?', $coreContentPageID)->where('name in (?)', array('left', 'right'))->query()->fetchAll();
    $count = count($coreContentName);
    return $count;
  }
  
  public function getSiteUserDefaultSettingsIds($level_id, $postionParams) {
    return Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", $postionParams);
  }

  public function setSiteUserDefaultSettingsIds($level_id, $postionParams) {
    return Engine_Api::_()->getApi("settings", "core")->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", $postionParams);
  }  
  
  public function getSiteUserDefaultSettingsParams($level_id, $postionParams) {

    return Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams);
  }

  public function setSiteUserDefaultSettingsParams($level_id, $postionParams) {

    return Engine_Api::_()->getApi("settings", "core")->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams);
  }  
}