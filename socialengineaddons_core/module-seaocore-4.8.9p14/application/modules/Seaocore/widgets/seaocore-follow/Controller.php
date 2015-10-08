<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_SeaocoreFollowController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

//    //HERE WE CAN FOUND THE MODULE NAME AND MODULE IS ENABLE.
//    $moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
//    $modulesEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($moduleName);
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();

    if (empty($viewer_id) || $viewer_id == $subject->getOwner()->getIdentity()) {
      return $this->setNoRender();
    }

    $this->view->resource_id = $resource_id = $subject->getIdentity();
    $this->view->resource_type = $resource_type = $subject->getType();
    $this->view->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($resource_type, $resource_id);
  }

}