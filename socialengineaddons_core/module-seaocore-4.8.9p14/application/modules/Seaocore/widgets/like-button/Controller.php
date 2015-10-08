<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Seaocore
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Seaocore_Widget_LikeButtonController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
		//DONT RENDER IF SUBJECT IS NOT SET
		if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject();
    $this->view->resource_id = $subject->getIdentity();
    $this->view->resource_type = $subject->getType(); 

		//GET VIEWER
		$this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//DONT RENDER IF NOT AUTHORIZED
		if (empty($viewer_id)) {
			return $this->setNoRender();
    }
  }
}