<?php

class Seaocore_AdminInfotooltipController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_main_infotooltip');
    $this->view->form = $form = new Seaocore_Form_Admin_Infotip();
    if (!$this->getRequest()->isPost()) { return; }
    if (!$form->isValid($this->getRequest()->getPost())) {  return; }
    // Process
    $values = $form->getValues();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Save settings
    foreach ($values as $key => $value) {
      if($settings->hasSetting($key))
      $settings->removeSetting($key);
      $settings->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }
}
