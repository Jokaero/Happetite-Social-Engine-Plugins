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
class Seaocore_Widget_SeaocoresInvitestatisticsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    
    $task = $this->view->invite_statistics = $this->_getParam('invite_statistics', 1);
    $service_type = $this->view->service_type = $this->_getParam('invite_service', 'all');
    $this->view->isajax = $this->_getParam('isajax', false);
    if ($this->view->isajax) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->search = $search = $this->_getParam('search', '');
    $this->view->form = new Seaocore_Form_Invite_Search ();    
    $params = $this->_getAllParams();
    if(!isset($params['page']))
      $params['page'] = 1;
    $params['invite_type'] = 'user_invite';
     $this->view->invite_Info =  $invite_Info = Engine_Api::_()->getApi('Invite', 'Seaocore')->getInviteStatisticSearchInfo ($params);
    //Fetching the friends invite info for this user:
//   if (!empty($search)) { 
//     $this->view->invite_Info =  $invite_Info = Engine_Api::_()->getApi('Invite', 'Seaocore')->getInviteStatisticSearchInfo ($params);
//   }
//   else {
//      $this->view->invite_Info =  $invite_Info = Engine_Api::_()->getApi('Invite', 'Seaocore')->getInviteStatisticInfo ($service_type, $task, 'user_invite', $page);
//   }

  }
}