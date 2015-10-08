<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_LayoutWidthController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->layoutWidth = $this->_getParam('layoutWidth', 0);
    if (empty($this->view->layoutWidth)) {
      return $this->setNoRender();
    }
    $this->view->layoutWidth .= $this->_getParam('layoutWidthType', 'px');   
  }

}
