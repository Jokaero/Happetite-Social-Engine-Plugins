<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_AdminLevelController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecontentcoverphoto_admin_main', array(), 'sitecontentcoverphoto_admin_main_level');

    // Get level id
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $this->view->level_id = $id = $level->level_id;
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type', 0);

    // Make form
    $this->view->form = $form = new Sitecontentcoverphoto_Form_Admin_Settings_Level(array(
                'public' => ( in_array($level->type, array('public')) ),
                'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
            ));

    if (isset($form->level_id)) 
    $form->level_id->setValue($id);
    if (isset($form->contentModule)) 
    $form->contentModule->setValue($resource_type);

    $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
    if($public_level_id) {
			if (isset($form->level_id)) 
		  $form->level_id->removeMultiOption($public_level_id);
    }

		$permissionType = 'sitecontentcoverphoto_'.$resource_type;
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed($permissionType, $id, array_keys($form->getValues())));

    // Check post
    if (!$this->getRequest()->isPost()) {
      return;
    }

    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();
    $contentModule = $values['contentModule'];
    if (array_key_exists('sitecontentcoverphoto_dummy', $values)) {
      unset($values['sitecontentcoverphoto_dummy']);
    }
    if (array_key_exists('sitecontentcoverphoto_preview', $values)) {
      unset($values['sitecontentcoverphoto_preview']);
    }
    if (array_key_exists('contentModule', $values)) {
      unset($values['contentModule']);
    }
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/controllers/license/license2.php';

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

}