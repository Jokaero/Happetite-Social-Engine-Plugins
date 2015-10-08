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
class Seaocore_Widget_ScrollTopController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET SETTING
		$this->view->mouseOverText = $this->_getParam('mouseOverText', 'Scroll to Top');
	}
}
