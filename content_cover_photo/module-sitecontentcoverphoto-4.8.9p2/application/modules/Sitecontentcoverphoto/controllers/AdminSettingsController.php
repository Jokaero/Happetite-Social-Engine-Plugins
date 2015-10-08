<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_AdminSettingsController extends Core_Controller_Action_Admin {

	public function __call($method, $params) {
		/*
			* YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
			* YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
			* REMEMBER:
			*    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
			*    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
			*/
		if (!empty($method) && $method == 'Sitecontentcoverphoto_Form_Admin_Global') {

		}
		return true;
	}
    
  public function indexAction() {
    $pluginName = 'sitecontentcoverphoto';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
    
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/controllers/license/license1.php';
		$this->view->isModsSupport = Engine_Api::_()->getApi('suggestion', 'sitecontentcoverphoto')->isModulesSupport();
  }

  public function readmeAction() {
    
  }

  public function faqAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecontentcoverphoto_admin_main', array(), 'sitecontentcoverphoto_admin_main_faq');
    $this->view->faq_id = $faq_id = $this->_getParam('faq_id', 'faq_1');
  }

}