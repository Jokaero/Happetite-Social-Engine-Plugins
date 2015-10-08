<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetContent.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_SocialShareButton extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function socialShareButton() {

    $isenable = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.code.share', 0);
  
    if (!$isenable)
      return;
    $data = array();
    return $this->view->partial(
                    'application/modules/Seaocore/views/scripts/social-share-button/_socialShareButton.tpl',
                    /*  Customization End */ $data
    );
  }

}