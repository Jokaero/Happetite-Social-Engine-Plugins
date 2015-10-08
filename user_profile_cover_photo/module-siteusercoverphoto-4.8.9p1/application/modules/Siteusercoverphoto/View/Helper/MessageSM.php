<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Message.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_View_Helper_MessageSM extends Zend_View_Helper_Abstract {

  public function MessageSM($user, $viewer = null) {

    if (null === $viewer) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if (!$viewer->getIdentity() || $viewer->getGuid(false) === $user->getGuid(false)) {
      return '';
    }

    // Get setting?
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
      return '';
    }
    $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if ($messageAuth == 'none') {
      return false;
    } else if ($messageAuth == 'friends') {
      // Get data
      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (!$direction) {
        //one way
        $friendship_status = $viewer->membership()->getRow($user);
      }
      else
        $friendship_status = $user->membership()->getRow($viewer);

      if (!$friendship_status || $friendship_status->active == 0) {
        return '';
      }
    }

    return $this->view->htmlLink(array('route' => 'messages_general', 'action' => 'compose', 'to' => $user->getIdentity()), $this->view->translate('Send Message'), array(
                'data-role' => "button", 'data-icon' => "false", "data-inset" => 'false', 'data-inline' => 'false', 'data-mini' => "true", 'data-corners' => "false", 'data-shadow' => "true", "data-ajax" => 'true'
            ));
  }

}