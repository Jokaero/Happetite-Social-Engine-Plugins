<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FollowController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_FollowController extends Core_Controller_Action_Standard {

	  public function getFollowersAction() {

    //GET VALUES
    $follow_user_str = 0 ;
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
    $this->view->page = $page = $this->_getParam('page' , 1 );
    $this->view->search = $search = $this->_getParam('search' , '' );
    $this->view->is_ajax = $this->_getParam('is_ajax' , 0 );
    $this->view->call_status = $call_status = $this->_getParam('call_status');
    
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET FOLLOW TABLE
    $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
    
    //HERE FUNCTION CALL FROM THE CORE.PHP FILE OR THIS IS SHOW NO OF FRIEND
    $this->view->paginator = $followTable->getFollowDetails($call_status, $resource_type, $resource_id, $viewer_id, $search);
    $this->view->paginator->setCurrentPageNumber($page);
    $this->view->paginator->setItemCountPerPage(10);

    //NUMBER OF FRIEND WHICH FOLLOWD THIS CONTENT.
    $this->view->totalFollowCount = $followTable->numberOfFollow($resource_type, $resource_id);

    //NUMBER OF MY FRIEND WHICH FOLLOWD THIS CONTENT.
    $this->view->totalFriendsFollow = $followTable->numberOfFriendsFollow($resource_type, $resource_id);

    //FIND OUT THE TITLE OF FOLLOWS.
    $this->view->resourceTitle = Engine_Api::_()->getItem($resource_type, $resource_id)->getTitle();
  }

  //ACTION FOR GLOBALLY FOLLOW THE LISTING
  public function globalFollowsAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET THE VALUE OF RESOURCE ID AND TYPE 
    $resource_id = $this->_getParam('resource_id');
    $resource_type = $this->_getParam('resource_type');
    $follow_id = $this->_getParam('follow_id');
    $status = $this->_getParam('smoothbox', 1);
    $this->view->status = true;

		if ($resource_type == 'sitepage_page') {
			$manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($resource_id, $viewer_id);
			$tableName = 'engine4_sitepage_membership';
			$ExtensionModuleName = 'sitepagemember';
		} else	if ($resource_type == 'sitebusiness_business') {
			$manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitebusiness')->getManageAdmin($resource_id, $viewer_id);
		} else	if ($resource_type == 'sitestore_store') {
			$manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($resource_id, $viewer_id);
		}else	if ($resource_type == 'sitegroup_group') {
			$manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($resource_id, $viewer_id);
		}

    //GET FOLLOW TABLE
    $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
    $follow_name = $followTable->info('name');

    //GET OBJECT
    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
    if (empty($follow_id)) {

      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $follow_id_temp = $resource->follows()->isFollow($viewer);
      if (empty($follow_id_temp)) {

        if (!empty($resource)) {
          $follow_id = $followTable->addFollow($resource, $viewer);
          if($viewer_id != $resource->getOwner()->getIdentity()) {

						if ($resource_type == 'sitepage_page' || $resource_type == 'sitebusiness_business' || $resource_type == 'sitegroup_group' || $resource_type == 'sitestore_store') {
							if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($ExtensionModuleName)) {
								$object_type = $resource->getType();
								$object_id = $resource->getIdentity();
								$subject_type = $viewer->getType();
								$subject_id = $viewer->getIdentity();
								$notificationType = 'follow_' . $resource_type;
								$notificationcreated = '%"notificationfollow":"1"%';
								$notificationFriendCreated = '%"notificationfollow":"2"%';
								$db = Zend_Db_Table_Abstract::getDefaultAdapter();
								
								$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
								
								if($friendId) {
									$db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `".$tableName."`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `".$tableName."` WHERE ($tableName.page_id = " . $resource->page_id . ") AND ($tableName.user_id <> " . $viewer->getIdentity() . ") AND ($tableName.notification = 1) AND ($tableName.action_notification LIKE '".$notificationcreated."' or ($tableName.action_notification LIKE '".$notificationFriendCreated."' and ($tableName .user_id IN (".join(",",$friendId)."))))");
								} else {
									$db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `".$tableName."`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `".$tableName."` WHERE ($tableName.page_id = " . $resource->page_id . ") AND ($tableName.user_id <> " . $viewer->getIdentity() . ") AND ($tableName.notification = 1) AND ($tableName.action_notification LIKE '".$notificationcreated."')");
								}
							} else {
								foreach ($manageAdminsIds as $value) {
									$action_notification = unserialize($value['action_notification']);
									$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
									//ADD NOTIFICATION
									if (!empty($value['notification']) && in_array('follow', $action_notification)) {
										Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $resource, 'follow_' . $resource_type, array());
									}
								}
							}
						} else if($resource_type == 'siteevent_event') {
							$actionTable = Engine_Api::_()->getDbtable('actions', 'seaocore');
							$action = $actionTable->addActivity($viewer, $resource, 'follow_siteevent_event');
							if ($action != null) {
									$actionTable->attachActivity($action, $resource);

									//START NOTIFICATION AND EMAIL WORK
									Engine_Api::_()->siteevent()->sendNotificationEmail($resource, $action, 'follow_siteevent_event', 'SITEEVENT_FOLLOW_CREATENOTIFICATION_EMAIL', null, null, 'follow', $viewer);
									//END NOTIFICATION AND EMAIL WORK
							}
						}
						elseif($resource_type == 'sitereview_wishlist') {
							//ADD NOTIFICATION
							Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($resource->getOwner(), $viewer, $resource, 'follow_' . $resource_type, array());
						}

						if($resource_type != 'siteevent_event') {
							//ADD ACTIVITY FEED
							$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							if ($resource_type != 'sitepage_page' || $resource_type != 'sitebusiness_business' || $resource_type != 'sitegroup_group'  || $resource_type != 'sitestore_store') {
							$action = $activityApi->addActivity($viewer, $resource, 'follow_' . $resource_type, '', array(
								'owner' => $resource->getOwner()->getGuid(),
							));
							} else {
							$action = $activityApi->addActivity($viewer, $resource, 'follow_' . $resource_type);
							}

              if(!empty($action))
                $activityApi->attachActivity($action, $resource);
						}
					}
        }

        $this->view->follow_id = $follow_id;

        $follow_msg = $this->view->translate('Successfully Followd.');
      }
    } else {
      if (!empty($resource)) {
        $followTable->removeFollow($resource, $viewer);

				if($viewer_id != $resource->getOwner()->getIdentity()) {

					if ($resource_type == 'sitepage_page' || $resource_type == 'sitebusiness_business' || $resource_type == 'sitegroup_group' || $resource_type == 'sitestore_store') {
						Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_type = ?' => "$resource_type", 'object_id = ?' => $resource_id, 'subject_id = ?' => $resource_id, 'subject_type = ?' => "$resource_type", 'user_id = ?' => $viewer_id));
						foreach ($manageAdminsIds as $value) {
							$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
							//DELETE NOTIFICATION
							$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($user_subject, $resource, 'follow_' . $resource_type);
							if($notification) {
								$notification->delete();
							}
						}
					}
					elseif($resource_type == 'sitereview_wishlist') {
						//DELETE NOTIFICATION
						$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($resource->getOwner(), $resource, 'follow_' . $resource_type);
						if($notification) {
							$notification->delete();
						}
					}

					//DELETE ACTIVITY FEED
					$action_id = Engine_Api::_()->getDbtable('actions', 'activity')
										->select()
										->from('engine4_activity_actions', 'action_id')
										->where('type = ?', "follow_$resource_type")
										->where('subject_id = ?', $viewer_id)
										->where('subject_type = ?', 'user')
										->where('object_type = ?', $resource_type)
										->where('object_id = ?', $resource->getIdentity())    
										->query()
										->fetchColumn();

					if(!empty($action_id)) {
						$activity = Engine_Api::_()->getItem('activity_action', $action_id);
						if(!empty($activity)) {
							$activity->delete();
						}
				  }	
				}
      }
      $follow_msg = $this->view->translate('Successfully Unfollowd.');
    }

    if (empty($status)) {
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array($follow_msg))
      );
    }
    
    //HERE THE CONTENT TYPE MEANS MODULE NAME
    $follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow( $resource_type , $resource_id);
    
    $followers = $this->view->translate(array('%s follower', '%s followers', $follow_count),$this->view->locale()->toNumber($follow_count));

    $this->view->follow_count = "<a href='javascript:void(0);' onclick='showSmoothBox(); return false;' >".$followers."</a>";
  }
  
  
}
