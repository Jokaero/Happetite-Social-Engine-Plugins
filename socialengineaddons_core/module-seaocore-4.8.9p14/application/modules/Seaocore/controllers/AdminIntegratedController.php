<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminIntegratedController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_AdminIntegratedController extends Core_Controller_Action_Admin {

  protected $_integratedtype;
  protected $_enabledModuleNames;
  public function init()
  {
    $integratedtypeTable = Engine_Api::_()->getDbtable('integratedtype', 'seaocore');
    $integratedtypesSelect = $integratedtypeTable->select();
    $this->view->integratedtype = $this->_integratedtype = $integratedtypeTable->fetchAll($integratedtypesSelect);
    $this->_enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
  }

  public function indexAction() {

    $this->view->type = $type = $this->_getParam('type', 0);

    $integratedtypes = $this->_integratedtype;

    $this->view->selectedIntegratedType = $selectedIntegratedType = $integratedtypes->getRowMatching('type', $type);
    $this->view->moduleType = $moduleType = $this->_getParam('moduleType', 'siteevent');

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation($moduleType. '_admin_main', array(), $moduleType.'_admin_main_integrated');

    $this->view->integrated = $integrated = Engine_Api::_()->getDbtable('integratedtype', 'seaocore')->getIntegratedModules($type, 1);

    $integratedtypes = Engine_Api::_()->getDbtable('integratedtype', 'seaocore')->getIntegratedModules($type, 0);

    $title='';
    $integratedtypeList = array();
    foreach( $integratedtypes as $integratedtype ) {
			if(isset($integratedtype->integratedparams)) {
				$decodedIntegratedParams = Zend_Json_Decoder::decode($integratedtype->integratedparams);
				if(isset($decodedIntegratedParams['title']))  {
					$title = $decodedIntegratedParams['title'];
				}
			}
			$integratedtypeList[$integratedtype->integratedtype] = $title;
    }
    $this->view->integratedtypeList = $integratedtypeList;
  }

  public function addAction() {

    $this->view->moduleType = $moduleType = $this->_getParam('moduleType', 'siteevent');

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation($moduleType.'_admin_main', array(), $moduleType.'_admin_main_integrated');

    $this->view->form = $form = new Seaocore_Form_Admin_Module_Add();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
				$integratedTable = Engine_Api::_()->getDbtable('integrated', 'seaocore');
				$resourceTypeTable = Engine_Api::_()->getItemTable($values['item_type']);
				$primaryId = current($resourceTypeTable->info("primary"));
				if (!empty($primaryId))
					$values['item_id'] = $primaryId;
				$row = $integratedTable->createRow();
				$row->setFromArray($values);
				$row->save();

				if($values['item_type'] == 'sitepage_page' || $values['item_type'] == 'sitegroup_group' || $values['item_type'] == 'sitebusiness_business') {
          $item_type = $values['item_type'];
					
          $item_module = $values['item_module'];
					$type = $item_module . 'event_event';
          $explodedArray = explode('_', $values['item_id']);
          $itemShortType = 'getWidgetized' . ucfirst($explodedArray[0]);
					//GET PAGE PROFILE PAGE INFO
					if (Engine_Api::_()->$item_module()->$itemShortType()) {
						$page_id = Engine_Api::_()->$item_module()->$itemShortType()->page_id;
					}
					Engine_Api::_()->getDbtable('admincontent', $item_module)->setAdminDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"columnHeight":"328","eventInfo":["categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","endDate","venueName","location","directionLink","likeCount","memberCount"],"titlePosition":"1","itemCount":"10","truncation":"25","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","descriptionPosition":0,"name":"siteevent.contenttype-events"}' );
					Engine_Api::_()->getApi('layoutcore', $item_module)->setContentDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"columnHeight":"328","eventInfo":["categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","endDate","venueName","location","directionLink","likeCount","memberCount"],"titlePosition":"1",,"descriptionPosition":0,"itemCount":"10","truncation":"25","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","name":"siteevent.contenttype-events"}');

          $db = Engine_Db_Table::getDefaultAdapter();
          $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$item_type' as `type`,
    'auth_secreate' as `name`,
    5 as `value`,
    \"['registered','owner_network','owner_member_member','owner_member','owner', 'member', 'like_member']\" as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$item_type' as `type`,
    'secreate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'invite' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");


$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'invite' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', '$type', 'view', '1', NULL);");


        }

				$db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_redirect('admin/seaocore/integrated?type='. $values['type']);
		}
  }

  public function editAction() {

    $this->view->moduleType = $moduleType = $this->_getParam('moduleType', 'siteevent');

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation($moduleType.'_admin_main', array(), $moduleType.'_admin_main_integrated');
    
    $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
    $type = Zend_Controller_Front::getInstance()->getRequest()->getParam('type', null);
    $item_module = Zend_Controller_Front::getInstance()->getRequest()->getParam('item_module', null);
    $integratedTable = Engine_Api::_()->getDbTable('integrated', 'seaocore');
		$integratedTableResult = $integratedTable->fetchRow(array('id = ?' => $id, 'type =?' => $type));
    $integratedRowValues = $integratedTableResult->toArray();
    $this->view->form = $form = new Seaocore_Form_Admin_Module_Edit();

    $form->populate($integratedRowValues);
    $form->item_module->setAttrib('disable', true);
    $form->item_type->setAttrib('disable', true);
		
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$values = $form->getValues();

			$item_type = $integratedTableResult->item_type;
			$item_module = $integratedTableResult->item_module;
			$item_id = $integratedTableResult->item_id;
			$type = $item_module . 'event_event';
			if($item_type == 'sitepage_page' || $item_type == 'sitegroup_group' || $item_type == 'sitebusiness_business') {
				$explodedArray = explode('_', $item_id);
				$itemShortType = 'getWidgetized' . ucfirst($explodedArray[0]);
				//GET PAGE PROFILE PAGE INFO
				if (Engine_Api::_()->$item_module()->$itemShortType()) {
					$page_id = Engine_Api::_()->$item_module()->$itemShortType()->page_id;
				}
				Engine_Api::_()->getDbtable('admincontent', $item_module)->setAdminDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"columnHeight":"328","eventInfo":["categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","endDate","venueName","location","directionLink","likeCount","memberCount"],"titlePosition":"1","itemCount":"10","truncation":"25","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","descriptionPosition":0,"name":"siteevent.contenttype-events"}' );
				Engine_Api::_()->getApi('layoutcore', $item_module)->setContentDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"columnHeight":"328","eventInfo":["categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","endDate","venueName","location","directionLink","likeCount","memberCount"],"titlePosition":"1",,"descriptionPosition":0,"itemCount":"10","truncation":"25","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","name":"siteevent.contenttype-events"}');

          $db = Engine_Db_Table::getDefaultAdapter();
          $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$item_type' as `type`,
    'auth_secreate' as `name`,
    5 as `value`,
    \"['registered','owner_network','owner_member_member','owner_member','owner', 'member', 'like_member']\" as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$item_type' as `type`,
    'secreate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'invite' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$type' as `type`,
    'invite' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', '$type', 'view', '1', NULL);");


        }
			unset($values['item_module']);
			unset($values['item_type']);
     
      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
				$integratedTableResult->setFromArray($values);
				$integratedTableResult->save();
			} catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_redirect('admin/seaocore/integrated?type='. $values['type']);
    }
  }

  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->type = $type = $this->_getParam('type');
    $this->view->id = $id = $this->_getParam('id');
    $integratedTable = Engine_Api::_()->getDbtable('integrated', 'seaocore');
    $integratedTableSelect = $integratedTable->fetchRow(array('id = ?' => $id, 'type =?' => $type));
    $this->view->module = $integratedTableSelect->item_module;
    if ($this->getRequest()->isPost()) {
      $integratedTableSelect->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

  public function enabledDisabledAction() {
    $id = $this->_getParam('id');
    $type = $this->_getParam('type');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $table  = Engine_Api::_()->getDbtable('integrated', 'seaocore');
    $select = $table->select()->where('id =?', $id);
    $content = $table->fetchRow($select);
    $enabled = $content->enabled;
		$integrated = $content->integrated;
		if($content->enabled == 0 && $content->integrated == 0) {
			$item_type = $content->item_type;
			$item_module = $content->item_module;
			$item_id = $content->item_id;
			$type = $item_module . 'event_event';
			if($item_type == 'sitepage_page' || $item_type == 'sitegroup_group' || $item_type == 'sitebusiness_business') {
				$explodedArray = explode('_', $item_id);
				$itemShortType = 'getWidgetized' . ucfirst($explodedArray[0]);
				//GET PAGE PROFILE PAGE INFO
				if (Engine_Api::_()->$item_module()->$itemShortType()) {
					$page_id = Engine_Api::_()->$item_module()->$itemShortType()->page_id;
				}
				Engine_Api::_()->getDbtable('admincontent', $item_module)->setAdminDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"columnHeight":"328","eventInfo":["categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","endDate","venueName","location","directionLink","likeCount","memberCount"],"titlePosition":"1","itemCount":"10","truncation":"25","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","descriptionPosition":0,"name":"siteevent.contenttype-events"}' );
				Engine_Api::_()->getApi('layoutcore', $item_module)->setContentDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"columnHeight":"328","eventInfo":["categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","endDate","venueName","location","directionLink","likeCount","memberCount"],"titlePosition":"1",,"descriptionPosition":0,"itemCount":"10","truncation":"25","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","name":"siteevent.contenttype-events"}');

          $db = Engine_Db_Table::getDefaultAdapter();
          $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    '$item_type' as `type`,
    'auth_secreate' as `name`,
    5 as `value`,
    \"['registered','owner_network','owner_member_member','owner_member','owner', 'member', 'like_member']\" as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$item_type' as `type`,
						'secreate' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels`;");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', '$type', 'view', '1', NULL);");

				$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', '$type', 'view', '1', NULL);");
        $feedType = $item_module. 'event_new';
				$db->query("UPDATE `engine4_activity_actiontypes` SET  `module` =  '$type' WHERE  `engine4_activity_actiontypes`.`type` =  '$feedType' LIMIT 1;");
        }
		}

    try {
      $content->enabled = !$content->enabled;
			$content->integrated = 1;
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

		if($enabled == 0 && $integrated == 0) {
			$this->_redirect('admin/siteevent/importevent');
		}
  }
}