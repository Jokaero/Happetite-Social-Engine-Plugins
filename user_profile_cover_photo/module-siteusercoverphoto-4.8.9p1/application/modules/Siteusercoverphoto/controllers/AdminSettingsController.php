<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    $pluginName = 'siteusercoverphoto';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
    
    $beforeActivateRemoveElement = array('save');
    $afterActivateRemoveElement = array('environment_mode', 'submit_lsetting');    
    include APPLICATION_PATH . '/application/modules/Siteusercoverphoto/controllers/license/license1.php';    
  }

  public function readmeAction(){}
  
  public function faqAction() {
		//GET NAVIGATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
						->getNavigation('siteusercoverphoto_admin_main', array(), 'siteusercoverphoto_admin_main_faq');
    $this->view->faq_id = $faq_id = $this->_getParam('faq_id', 'faq_1');
  }

}