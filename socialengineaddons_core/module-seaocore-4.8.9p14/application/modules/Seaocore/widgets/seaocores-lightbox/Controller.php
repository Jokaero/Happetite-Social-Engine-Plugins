<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_SeaocoresLightboxController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $flag = 0;
    
      $front = Zend_Controller_Front::getInstance();
      $module = $front->getRequest()->getModuleName();
      $controllerName = $front->getRequest()->getControllerName();
      $actionName = $front->getRequest()->getActionName();

		if (SEA_DISPLAY_LIGHTBOX && SITEALBUM_ENABLED && (!($controllerName == 'album' && $actionName == 'browse')) && (!($module == 'sitealbum' && $controllerName == 'index' && $actionName == 'index')) && (!($module == 'user' && $controllerName == 'profile' && $actionName == 'index'))) {
      $flag = 1;
    } else {
      if (SITEALBUM_ENABLED && SEA_GROUP_LIGHTBOX && $module == 'group') {
        $flag = 1;
      } elseif (SITEALBUM_ENABLED && SEA_EVENT_LIGHTBOX && $module == "event") {
        $flag = 1;
      } elseif (SITEALBUM_ENABLED && SEA_YNEVENT_LIGHTBOX && $module == "ynevent") {
        $flag = 1;
      } elseif (SITEALBUM_ENABLED && SEA_ADVGROUP_LIGHTBOX && $module == "advgroup") {
        $flag = 1;
      }
    }
    if (SEA_ACTIVITYFEED_LIGHTBOX) {
      $SEA_ACTIVITYFEED_LIGHTBOX = SEA_ACTIVITYFEED_LIGHTBOX;
    } else {
      $SEA_ACTIVITYFEED_LIGHTBOX = 0;
    }
    $this->view->SEA_ACTIVITYFEED_LIGHTBOX = $SEA_ACTIVITYFEED_LIGHTBOX;
    $this->view->flag = $flag;
    $this->view->fixedwindowEnable=Engine_Api::_()->getApi('settings','core')->getSetting('sea.lightbox.fixedwindow', 1);
  

}
}

?>
