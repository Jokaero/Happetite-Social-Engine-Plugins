<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PinBoardCommentController.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_PinBoardCommentController extends Core_Controller_Action_Standard {

  public function init() {
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    if ($type && $identity) {
      $item = Engine_Api::_()->getItem($type, $identity);
      if ($item instanceof Core_Model_Item_Abstract &&
              (method_exists($item, 'comments') || method_exists($item, 'likes'))) {
        if (!Engine_Api::_()->core()->hasSubject()) {
          Engine_Api::_()->core()->setSubject($item);
        }
      }
    }
  }

  public function listAction() {
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->subject = $subject = $this->getSubjectItem();

    // Perms
    $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
    $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

    // Likes
    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();

    // Comments
    // If has a page, display oldest to newes
    if (null !== ( $page = $this->_getParam('page'))) {
      $commentSelect = $subject->comments()->getCommentSelect('ASC');
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(5);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }
    // If not has a page, show the
    else {
      $commentSelect = $subject->comments()->getCommentSelect('DESC');
      $commentSelect->order('comment_id DESC');

      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }
    
    $this->view->widget_id = $this->_getParam('widget_id', 0);
    $this->view->element_id = $subject->getGuid() . '_' . $this->_getParam('widget_id', 0);
    if ($viewer->getIdentity() && $canComment) {
      $this->view->form = $form = new Seaocore_Form_Comment_Create();
      $form->populate(array(
          'identity' => $subject->getIdentity(),
          'type' => $subject->getType(),
      ));

      $this->view->show_bottom_post = $this->_getParam('show_bottom_post', false);
      $this->view->submit_post = $this->_getParam('submit_post', false);
    }
  }

  public function createAction() {
    
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $this->getSubjectItem();
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      return;
    }

    $this->view->form = $form = new Seaocore_Form_Comment_Create();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
      return;
    }

    // Process
    // Filter HTML
    $filter = new Zend_Filter();
    $filter->addFilter(new Engine_Filter_Censor());
    $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

    $body = $form->getValue('body');
    $body = $filter->filter($body);


    $db = $subject->comments()->getCommentTable()->getAdapter();
    $db->beginTransaction();

    try {

      //For comment work owner or second user

      $subject->comments()->addComment($viewer, $body);

      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $subjectOwner = $subject->getOwner('user');

      // Activity
      $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
          'owner' => $subjectOwner->getGuid(),
          'body' => $body,
          'listingtype' => $subject->getShortType(),
              ));

//      if (!empty($action)) {
//        $activityApi->attachActivity($action, $subject);
//      }

      // Notifications
      // Add notification for owner (if user and not viewer)
      $this->view->subject = $subject->getGuid();
      $this->view->owner = $subjectOwner->getGuid();
      if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {

        $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
            'label' => $subject->getShortType()
        ));
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      $commentedUserNotifications = array();
      foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
        if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
          continue;

        // Don't send a notification if the user both commented and liked this
        $commentedUserNotifications[] = $notifyUser->getIdentity();


        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
            'label' => $subject->getShortType()
        ));
      }

      // Add a notification for all users that liked
      // @todo we should probably limit this
      foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
        // Skip viewer and owner
        if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
          continue;

        // Don't send a notification if the user both commented and liked this
        if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
          continue;


        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
            'label' => $subject->getShortType()
        ));
      }
      //Send notification to Page admins
      // Increment comment count
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = 'Comment added';
    $this->view->body = $this->view->action('list', 'pin-board-comment', 'seaocore', array(
        'type' => $this->_getParam('type'),
        'id' => $this->_getParam('id'),
        'widget_id' => $this->_getParam('widget_id'),
        'format' => 'html',
            ));
    $this->_helper->contextSwitch->initContext();
  }

  public function likeAction() {
    
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $this->getSubjectItem();
    $comment_id = $this->_getParam('comment_id');

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if ($comment_id) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }

    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try {

      $commentedItem->likes()->addLike($viewer);

      // Add notification
      $owner = $commentedItem->getOwner();
      $this->view->owner = $owner->getGuid();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
            'label' => $commentedItem->getShortType()
        ));
      }

      //Send notification to Page admins
			$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
			if (strpos($subject->getType(), "sitepage") !== false && $sitepageVersion >= '4.2.9p3') {
        Engine_Api::_()->sitepage()->itemCommentLike($subject, 'sitepage_contentlike', '');
      }elseif (strpos($subject->getType(), "sitegroup") !== false) {
        Engine_Api::_()->sitegroup()->itemCommentLike($subject, 'sitegroup_contentlike', '');
      }elseif (strpos($subject->getType(), "sitestore") !== false) {
        Engine_Api::_()->sitestore()->itemCommentLike($subject, 'sitestore_contentlike', '');
      } elseif (strpos($subject->getType(), "siteevent") !== false) {
        Engine_Api::_()->siteevent()->itemCommentLike($subject, 'siteevent_contentlike', '', 'like');
      }
      //Send notification to Page admins

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // For comments, render the resource
    if ($subject->getType() == 'core_comment') {
      $type = $subject->resource_type;
      $id = $subject->resource_id;
      Engine_Api::_()->core()->clearSubject();
    } else {
      $type = $subject->getType();
      $id = $subject->getIdentity();
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
    $this->view->body = $this->view->action('list', 'pin-board-comment', 'seaocore', array(
        'type' => $type,
        'id' => $id,
        'widget_id' => $this->_getParam('widget_id'),
        'format' => 'html',
            ));
    
    $this->_helper->contextSwitch->initContext();
  }

  public function unlikeAction() {
    
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $this->getSubjectItem();
    $comment_id = $this->_getParam('comment_id');

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if ($comment_id) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }

    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try {
      $commentedItem->likes()->removeLike($viewer);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // For comments, render the resource
    if ($subject->getType() == 'core_comment') {
      $type = $subject->resource_type;
      $id = $subject->resource_id;
      Engine_Api::_()->core()->clearSubject();
    } else {
      $type = $subject->getType();
      $id = $subject->getIdentity();
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
    $this->view->body = $this->view->action('list', 'pin-board-comment', 'seaocore', array(
        'type' => $type,
        'id' => $id,
        'widget_id' => $this->_getParam('widget_id'),
        'format' => 'html',
            ));
    $this->_helper->contextSwitch->initContext();
  }

  public function getSubjectItem() {
    
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    if ($type && $identity)
      return $subject = Engine_Api::_()->getItem($type, $identity);
  }

}