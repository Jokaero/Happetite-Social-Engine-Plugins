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
class Seaocore_Widget_SeaocoresNestedcommentsController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {

    //GET SUBJECT
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    } else if( ($subject = $this->_getParam('subject')) ) {
      list($type, $id) = explode('_', $subject);
      $this->view->subject = $subject = Engine_Api::_()->getItem($type, $id);
    } else if( ($type = $this->_getParam('type')) &&
        ($id = $this->_getParam('id')) ) {
      $this->view->subject = $subject = Engine_Api::_()->getItem($type, $id);
    }

    if(!$subject)
      return $this->setNoRender();
    
   if(($subject->getType() != 'sitereview_listing' && $subject->getType() != 'sitereview_review') && ($subject->getType() != 'siteevent_event' && $subject->getType() != 'siteevent_review')) {
      return $this->setNoRender();
    }

    if( !($subject instanceof Core_Model_Item_Abstract) ||
        !$subject->getIdentity() ||
        (!method_exists($subject, 'comments') && !method_exists($subject, 'likes')) ) {
      return $this->setNoRender();
    }
   
  }

}