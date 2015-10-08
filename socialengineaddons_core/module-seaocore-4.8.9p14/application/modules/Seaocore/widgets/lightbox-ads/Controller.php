<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_LightboxAdsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.lightboxads', 1);
    if (!$enable_ads) {
      return $this->setNoRender();
    }

    $limit = $this->_getParam('limit', null);
    $this->view->viewer_object = $viewer_object = Engine_Api::_()->user()->getViewer();
    $this->view->user_id = $viewer_object->getIdentity();
    $params = array();
    $params['lim'] = $limit;
    $ad_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.adtype', 3);

    switch ($ad_type) {
      case 0:
        $params['sponsored'] = 1;
        $params['featured'] = 1;
        break;
      case 1:
        $params['featured'] = 1;
        break;
      case 2:
        $params['sponsored'] = 1;
        break;
      case 3:
        break;
    }
    
    $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);

    if (!empty($fetch_community_ads)) {
      $this->view->communityads_array = $fetch_community_ads;
    } else {
      return $this->setNoRender();
    }
  }

}