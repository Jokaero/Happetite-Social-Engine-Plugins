<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ActivityLoopSea.php 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_SeaddonsBaseUrl extends Zend_View_Helper_Abstract {

  public function seaddonsBaseUrl() {
    $coreModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreModuleVersion = $coreModule->version;
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if ($coreModuleVersion < '4.1.8') {
      return $view->baseUrl();
    } else {
      return rtrim($view->layout()->staticBaseUrl, '/');
    }
  }

}