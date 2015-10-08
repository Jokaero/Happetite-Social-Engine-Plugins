<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_AdminModuleController extends Core_Controller_Action_Admin {
  public function indexAction()
  {ini_set('max_execution_time', 300);
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($viewer) && !empty($viewer->level_id) ) {
    $level_id = $viewer->level_id;
    if (!$this->_helper->requireUser()->isValid())
      return;
    }
    $product_type=$this->_getParam('type');
    if( ($level_id != 1) || (empty($product_type)) ) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $type = $this->_getParam('type', null);
    $this->_setParam('plugin_title', @base64_decode($this->_getParam('plugin_title', null)));
    include_once APPLICATION_PATH . '/application/modules/Seaocore/controllers/license/license1.php';
  }
}