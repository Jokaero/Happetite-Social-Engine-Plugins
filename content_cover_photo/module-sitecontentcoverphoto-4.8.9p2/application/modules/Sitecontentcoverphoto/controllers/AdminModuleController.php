<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_AdminModuleController extends Core_Controller_Action_Admin {

  public function indexAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecontentcoverphoto_admin_main', array(), 'sitecontentcoverphoto_admin_manage_modules');

		$db = Engine_Db_Table::getDefaultAdapter();
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', 'sitereview')
						->where('enabled = ?', 1);
		$is_sitereview_object = $select->query()->fetchObject();
		if(!empty($is_sitereview_object)) {
			$select = new Zend_Db_Select($db);
			$listingtypeObject = $select
							->from('engine4_sitereview_listingtypes', 'listingtype_id')
							->query()
							->fetchAll();
			foreach($listingtypeObject as $values) {
				$listingtype_id = $values['listingtype_id'];
				$db->query("INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES ('sitereview', 'sitereview_listing_$listingtype_id', 'review_id', '0')");
				$contentType = 'sitecontentcoverphoto_sitereview_listing_'. $listingtype_id;
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
    $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    $page = $this->_getParam('page', 1);
    $pagesettingsTable = Engine_Api::_()->getItemTable('sitecontentcoverphoto_modules');
    $pagesettingsTableName = $pagesettingsTable->info('name');
    $pagesettingsSelect = $pagesettingsTable->select();

    $this->view->paginator = Zend_Paginator::factory($pagesettingsSelect);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator->setCurrentPageNumber($page);

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $obj = Engine_Api::_()->getItem('sitecontentcoverphoto_modules', $value);
          if (empty($obj->is_delete)) {
            $obj->delete();
          }
        }
      }
    }
  }

  // Function: Manage Module - Creation Tab.
  public function addAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecontentcoverphoto_admin_main', array(), 'sitecontentcoverphoto_admin_manage_modules');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $module_table = Engine_Api::_()->getDbTable('modules', 'sitecontentcoverphoto');
    $module_name = $module_table->info('name');
    $this->view->form = $form = new Sitecontentcoverphoto_Form_Admin_Module_Add();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $resource_type = $values['resource_type'];
      $module = $values['module'];
      $customCheck = $module_table->fetchRow(array('resource_type = ?' => $resource_type, 'module = ?' => $module));
      if (!empty($customCheck)) {
        $itemError = Zend_Registry::get('Zend_Translate')->_("This ‘Content Module’ already exist.");
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($itemError);
        return;
      }

      $resourceTypeTable = Engine_Api::_()->getItemTable($resource_type);
      $primaryId = current($resourceTypeTable->info("primary"));
      if (!empty($primaryId))
        $values['resource_id'] = $primaryId;

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/controllers/license/license2.php';
        $contentType = 'sitecontentcoverphoto_'. $resource_type;
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
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

        //end
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  // Function: Manage Module - Creation Tab.
  public function editAction() {

    $manageModules = Engine_Api::_()->getItem('sitecontentcoverphoto_modules', $this->_getParam('module_id'));
    $modules = Engine_Api::_()->getItem('sitecontentcoverphoto_modules', $manageModules->module_id);

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecontentcoverphoto_admin_main', array(), 'sitecontentcoverphoto_admin_manage_modules');

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $this->view->form = $form = new Sitecontentcoverphoto_Form_Admin_Module_Edit();
    //SHOW PRE-FIELD FORM
    $form->populate($manageModules->toArray());

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($manageModules->toArray());
      return;
    }

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();
    unset($values['module']);
    unset($values['resource_type']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $manageModules->setFromArray($values);
      $manageModules->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('controller' => 'module', 'action' => 'index'));
  }

  public function disabledAction() {
    $value = $this->_getParam('module_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $content = Engine_Api::_()->getItemTable('sitecontentcoverphoto_modules')->fetchRow(array('module_id = ?' =>
        $value));
    try {
      $content->enabled = !$content->enabled;
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitecontentcoverphoto/module');
  }


  public function enabledAction() {
		$this->_helper->layout->setLayout('admin-simple');
		$this->view->module_id = $this->_getParam('module_id');   
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
    $modulestable = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto');
    $sub_status_select = $modulestable->fetchRow(array('resource_type = ?' => $resource_type));
    $this->view->moduleName = $sub_status_select->module;

    if ($this->getRequest()->isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			$content = Engine_Api::_()->getItemTable('sitecontentcoverphoto_modules')->fetchRow(array('module_id = ?' =>
					$this->view->module_id));
			try {
				$content->enabled = !$content->enabled;
				$content->save();
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				throw $e;
			}

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('')),
					'parentRefresh' => 10,
      ));
		}
  }

  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
    $modulestable = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto');
    $sub_status_select = $modulestable->fetchRow(array('resource_type = ?' => $resource_type));
    $this->view->module = $sub_status_select->module;

    if ($this->getRequest()->isPost()) {
      $custom = Engine_Api::_()->getItemTable('sitecontentcoverphoto_modules')->fetchRow(array('resource_type = ?' =>
          $resource_type));
      $custom->delete();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

}

?>