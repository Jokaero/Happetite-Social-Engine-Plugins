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
class Seaocore_Widget_SocialShareButtonsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->position = $this->_getParam('position', 'left');
    $this->view->buttons = $this->_getParam('show_buttons', array('facebook', 'twitter', 'linkedin', 'plusgoogle', 'share'));
    if (count($this->view->buttons) == 0) {
      return $this->setNoRender();
    }
  }

}
