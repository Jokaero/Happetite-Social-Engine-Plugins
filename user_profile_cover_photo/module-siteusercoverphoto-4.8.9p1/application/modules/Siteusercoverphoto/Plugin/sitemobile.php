<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Plugin_Sitemobile {

  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    $this->addSiteusercoverphotoPages();
  }

  public function addSiteusercoverphotoPages() {
    $this->setDefaultWidgetForSiteusercoverphoto('content', 'pages');
    $this->setDefaultWidgetForSiteusercoverphoto('tabletcontent', 'tabletpages');
  }

  public function setDefaultWidgetForSiteusercoverphoto($content, $pages) {
    $contentTable = Engine_Api::_()->getDbtable($content, 'sitemobile');
    $contentTableName = $contentTable->info('name');
    $pageTable = Engine_Api::_()->getDbtable($pages, 'sitemobile');
    $pageTableName = $pageTable->info('name');
    $selectPage = $pageTable->select()
            ->from($pageTableName, array('page_id'))
            ->where('name =?', 'user_profile_index')
            ->limit(1);
    $fetchPageId = $selectPage->query()->fetchColumn();
    if (!empty($fetchPageId)) {
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
        $contentTable->delete(array('name =?' => 'sitemobile.profile-photo-status', 'page_id =?' => $fetchPageId));
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

}