<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FriendsController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_FriendsController extends Core_Controller_Action_Standard {

  /**
   * Handles HTTP request to like an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/add-friend
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function addAction() {
    // Make sure user exists
    if (!$this->_helper->requireUser()->isValid())
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = (int) $this->_getParam('user_id');
    $user = Engine_Api::_()->user()->getUser($user_id);
    $message = '';
    if (!$viewer->isSelf($user) && !$user->membership()->isMember($viewer) && !$viewer->isBlocked($user)) {

      // Start transaction
      $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
      $db->beginTransaction();
      try {

        $user->membership()->addMember($viewer)->setUserApproved($viewer);
        // if one way friendship and verification not required
        if (!$user->membership()->isUserApprovalRequired() && !$user->membership()->isReciprocal()) {
          // Add activity
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends_follow', '{item:$subject} is now following {item:$object}.');

          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');

          $message = "You are now following this member.";
        }

        // if two way friendship and verification not required
        else if (!$user->membership()->isUserApprovalRequired() && $user->membership()->isReciprocal()) {
          // Add activity
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
        }

        // if one way friendship and verification required
        else if (!$user->membership()->isReciprocal()) {
          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
        }

        // if two way friendship and verification required
        else if ($user->membership()->isReciprocal()) {
          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
        }
        $db->commit();
        $this->view->status = true;
      } catch (Exception $e) {
        $db->rollBack();
        $this->view->status = false;
        $this->view->exception = $e->__toString();
      }
    }
    // Success

    $this->view->message = ($message) ? Zend_Registry::get('Zend_Translate')->_($message) : '';


    // Redirect if not json context
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
    }
  }

  /**
   * Handles HTTP request to like an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/add-friend
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function cancelAction() {
    // Make sure user exists
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$this->_helper->requireUser()->isValid())
      return;

    $user_id = (int) $this->_getParam('user_id');
    $user = Engine_Api::_()->user()->getUser($user_id);
    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $user->membership()->removeMember($viewer);

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
              ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
              ->getNotificationBySubjectAndType($user, $viewer, 'friend_follow_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.');
    // Collect params
//     $action_id = $this->_getParam('action_id');
//     $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
    // Redirect if not json context
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
    }
  }

  public function confirmAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if( null == ($user_id = $this->_getParam('user_id')) ||
        null == ($user = Engine_Api::_()->getItem('user', $user_id)) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }
    
    $friendship = $viewer->membership()->getRow($user);
    if( $friendship->active ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already friends');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer->membership()->setResourceApproved($user);

      // Add activity
      if( !$user->membership()->isReciprocal() ) {
        Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
      } else {
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
      }
      
      // Add notification
      if( !$user->membership()->isReciprocal() ) {
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $user, 'friend_follow_accepted');
      } else {
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $user, 'friend_accepted');
      }

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
          ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
      if( $notification ) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
          ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
      if( $notification ) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      // Increment friends counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
      $message = sprintf($message, $user->__toString());

    } catch( Exception $e ) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }

    $this->view->status = true;
    $this->view->message = $message;
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
    }
  }

  public function removeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if( null == ($user_id = $this->_getParam('user_id')) ||
        null == ($user = Engine_Api::_()->getItem('user', $user_id)) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      if( $this->_getParam('rev') ) {
        $viewer->membership()->removeMember($user);
      } else {
        $user->membership()->removeMember($viewer);
      }

      // Remove from lists?
      // @todo make sure this works with one-way friendships
      if($user->lists()->removeFriendFromLists($viewer))
        $user->lists()->removeFriendFromLists($viewer);
      
      if($viewer->lists()->removeFriendFromLists($user))
        $viewer->lists()->removeFriendFromLists($user);

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
      if( $notification ) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
          ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
      if( $notification ) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.');
      
    } catch( Exception $e ) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }

		$this->view->status = true;
		$this->view->message = $message;
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
//       $helper = 'advancedActivity';
//       $this->view->body = $this->view->$helper($action, array('noList' => true));
    }
  }
  
}