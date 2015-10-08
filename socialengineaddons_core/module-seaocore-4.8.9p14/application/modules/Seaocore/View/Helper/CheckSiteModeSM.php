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
class Seaocore_View_Helper_CheckSiteModeSM extends Zend_View_Helper_Abstract {

  public function checkSiteModeSM($mode=null) {
 //   $session = new Zend_Session_Namespace('siteViewModeSM');
    if($mode)
      return Engine_Api::_()->seaocore()->checkSitemobileMode($mode."-mode");
    else
      return Engine_Api::_()->seaocore()->isSiteMobileModeEnabled();
//    if ($mode) {
//      if (isset($session->siteViewModeSM) && $session->siteViewModeSM == $mode) {
//        return true;
//      }
//    } elseif (isset($session->siteViewModeSM) && in_array($session->siteViewModeSM, array("mobile", "tablet"))) {
//      return true;
//    }
  }

}