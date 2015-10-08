<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserFriendshipAjax.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_View_Helper_UserFriendshipAjax extends Zend_View_Helper_Abstract {

  public function UserFriendshipAjax($user, $viewer = null) {
//     $user = $user->getSubject();
    if (null === $viewer) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if (!$viewer || !$viewer->getIdentity() || $user->isSelf($viewer) || $user->getType() !== 'user') {
      return '';
    }

    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if (!$direction) {
      $row = $user->membership()->getRow($viewer);
    }
    else
      $row = $viewer->membership()->getRow($user);

     $varifiedRequried = $user->membership()->isUserApprovalRequired();
     if(empty($varifiedRequried))
      $varifiedRequried = 0;
    // Render
    // Check if friendship is allowed in the network
    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if ($eligible == 0) {
      return '';
    }

    // check admin level setting if you can befriend people in your network
    else if ($eligible == 1) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
              ->from($networkMembershipName, 'user_id')
              ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
              ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
              ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity())
      ;

      $data = $select->query()->fetch();

      if (empty($data)) {
        return '';
      }
    }

		// One-way mode
    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
    if( !$direction ) {
      $viewerRow = $viewer->membership()->getRow($user);
      $subjectRow = $user->membership()->getRow($viewer);
      $params = array();
      // Viewer?
      if( null === $subjectRow ) {
        // Follow
        $params[] =  "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
 onclick='en4.siteusercoverphoto.follow.add(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                 $this->view->translate('Follow') . "</span></a>";
      } else if( $subjectRow->resource_approved == 0 ) {
        // Cancel follow request
        $params[] = "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
 onclick='en4.siteusercoverphoto.follow.cancel(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                 $this->view->translate('Cancel Follow Request') . "</span></a>";
      } else {
        // Unfollow
        $params[] = "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
 onclick='en4.siteusercoverphoto.follow.remove(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                 $this->view->translate('Unfollow') . "</span></a>";
      }

     // Subject?
      if( null === $viewerRow ) {
        // Do nothing
      } else if( $viewerRow->resource_approved == 0 ) {
        // Approve follow request
        $params[] ="<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
 onclick='en4.siteusercoverphoto.follow.confirm(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                 $this->view->translate('Approve Follow Request') . "</span></a>";
      } else {
        // Remove as follower?
        $params[] = "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
 onclick='en4.siteusercoverphoto.follow.remove(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                 $this->view->translate('Unfollow') . "</span></a>";
      }


			if( count($params) == 1 ||  count($params) == 2) {
        return $params[0];
      } else if( count($params) == 0 ) {
        return false;
      } 

//       // Viewer?
//       if( null === $subjectRow ) {
// 				return "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
// onclick='en4.siteusercoverphoto.follow.add(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
//                 $this->view->translate('Follow') . "</span></a>";
//       } else if( $subjectRow->resource_approved == 0 && $varifiedRequried) {
// 				return "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
// onclick='en4.siteusercoverphoto.follow.cancel(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
//                 $this->view->translate('Cancel Follow Request') . "</span></a>";
//       } else if( $viewerRow->resource_approved == 0 && $varifiedRequried) {
// 				return "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
// onclick='en4.siteusercoverphoto.follow.confirm(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
//                 $this->view->translate('Approve Follow Request') . "</span></a>";
//       } else {
// 				return "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
// onclick='en4.siteusercoverphoto.follow.remove(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
//                 $this->view->translate('Unfollow') . "</span></a>";
//       }
      
    }

    // Two-way mode
    else {
      $row = $viewer->membership()->getRow($user);
      if( null === $row ) {
				return "<a  id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
onclick='en4.siteusercoverphoto.friend.add(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                $this->view->translate('Add Friend') . "</span></a>";
      } else if( $row->user_approved == 0 ) {
				return "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
onclick='en4.siteusercoverphoto.friend.cancel(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                $this->view->translate('Cancel Friend Request') . "</span></a>";
      } else if( $row->resource_approved == 0 ) {
				return "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
onclick='en4.siteusercoverphoto.friend.confirm(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                $this->view->translate('Approve Friend Request') . "</span></a>";
      } else {
				return "<a id='aaf_addfriend_" . $user->getIdentity() . "' href='javascript:void(0);'
onclick='en4.siteusercoverphoto.friend.remove(" . $user->getIdentity() . ", " . $varifiedRequried. ")'><span>" .
                $this->view->translate('Remove Friend') . "</span></a>";
      }
    }

    return '';
  }

}