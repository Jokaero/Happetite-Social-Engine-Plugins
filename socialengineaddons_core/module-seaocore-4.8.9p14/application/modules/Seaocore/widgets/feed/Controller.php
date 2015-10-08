<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_FeedController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if (!in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event', 'sitebusiness_business', 'sitebusinessevent_event','sitegroup_group', 'sitegroupevent_event','sitestore_store', 'sitestoreevent_event'))) {
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event'))) {
        $pageSubject = $subject;
        if ($subject->getType() == 'sitepageevent_event')
          $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitebusiness_business', 'sitebusinessevent_event'))) {
        $businessSubject = $subject;
        if ($subject->getType() == 'sitebusinessevent_event')
          $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitegroup_group', 'sitegroupevent_event'))) {
        $groupSubject = $subject;
        if ($subject->getType() == 'sitegroupevent_event')
          $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitestore_store', 'sitestoreevent_event'))) {
        $storeSubject = $subject;
        if ($subject->getType() == 'sitestoreevent_event')
          $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      }
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

    // Get some options
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
    $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate = $request->getParam('getUpdate');
    $this->view->checkUpdate = $request->getParam('checkUpdate');
    $this->view->action_id = (int) $request->getParam('action_id');
    $this->view->post_failed = (int) $request->getParam('pf');

    if ($feedOnly) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    if ($length > 50) {
      $this->view->length = $length = 50;
    }

    // Get all activity feed types for custom view?
//    $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'activity');
//    $this->view->groupedActionTypes = $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
//    $actionTypeGroup = $request->getParam('actionFilter');
//    $actionTypeFilters = array();
//    if( $actionTypeGroup && isset($groupedActionTypes[$actionTypeGroup]) ) {
//      $actionTypeFilters = $groupedActionTypes[$actionTypeGroup];
//    }
    // Get config options for activity
    $config = array(
        'action_id' => (int) $request->getParam('action_id'),
        'max_id' => (int) $request->getParam('maxid'),
        'min_id' => (int) $request->getParam('minid'),
        'limit' => (int) $length,
            //'showTypes' => $actionTypeFilters,
    );

    // Pre-process feed items
    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();
    $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    do {
      // Get current batch
      $actions = null;
      if (!empty($subject)) {
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }
      $selectCount++;

      // Are we at the end?
      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      // Pre-process
      if (count($actions) > 0) {
        foreach ($actions as $action) {
          // get next id
          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          // get first id
          if (null === $firstid || $action->action_id > $firstid) {
            $firstid = $action->action_id;
          }
          // skip disabled actions
          if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled)
            continue;
          // skip items with missing items
          if (!$action->getSubject() || !$action->getSubject()->getIdentity())
            continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity())
            continue;
          // track/remove users who do too much (but only in the main feed)
          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()]++;
            }
          }
          // remove duplicate friend requests
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          // remove items with disabled module attachments
          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }

          // add to list
          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
      }

      // Set next tmp max_id
      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }
    } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);

    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;


    // Get some other info
    if (!empty($subject)) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;

    if ($viewer->getIdentity() && !$this->_getParam('action_id')) {
      if (Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
        if (!$subject || $subject->authorization()->isAllowed($viewer, 'comment')) {
          $this->view->enableComposer = true;
        }
      } else {
        if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
          if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
            $this->view->enableComposer = true;
          }
        } else if ($subject) {
          if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
            $this->view->enableComposer = true;
          }
        }
      }

      if (!empty($subject)) {
        // Get subject

        if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event') {
          $pageSubject = $subject;
          if ($subject->getType() == 'sitepageevent_event')
            $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
           if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            if (Engine_Api::_()->sitepage()->isPageOwner($pageSubject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
              $activityFeedType = 'sitepage_post_self';
            elseif ($pageSubject->all_post || Engine_Api::_()->sitepage()->isPageOwner($pageSubject))
              $activityFeedType = 'sitepage_post';
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        }else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event') {
          $businessSubject = $subject;
          if ($subject->getType() == 'sitebusinessevent_event')
            $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
          if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            $activityFeedType = null;
            if (Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
              $activityFeedType = 'sitebusiness_post_self';
            elseif ($businessSubject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject))
              $activityFeedType = 'sitebusiness_post';
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        }else if ($subject->getType() == 'sitegroup_group' || $subject->getType() == 'sitegroupevent_event') {
          $groupSubject = $subject;
          if ($subject->getType() == 'sitegroupevent_event')
            $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
           if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            if (Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
              $activityFeedType = 'sitegroup_post_self';
            elseif ($groupSubject->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject))
              $activityFeedType = 'sitegroup_post';
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        }else if ($subject->getType() == 'sitestore_store' || $subject->getType() == 'sitestoreevent_event') {
          $storeSubject = $subject;
          if ($subject->getType() == 'sitestoreevent_event')
            $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
           if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            if (Engine_Api::_()->sitestore()->isStoreOwner($storeSubject) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
              $activityFeedType = 'sitestore_post_self';
            elseif ($storeSubject->all_post || Engine_Api::_()->sitestore()->isStoreOwner($storeSubject))
              $activityFeedType = 'sitestore_post';
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        }
      }    
    }

    // Assign the composing values
    $composePartials = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $data) {
      if (empty($data['composer'])) {
        continue;
      }
      foreach ($data['composer'] as $type => $config) {
        if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
          continue;
        }
        $composePartials[] = $config['script'];
      }
    }

    $this->view->composePartials = $composePartials;
    /*  Customization Start */
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Seaocore/View/Helper', 'Seaocore_View_Helper');
    if (!Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
      // Form token
      $session = new Zend_Session_Namespace('ActivityFormToken');
      //$session->setExpirationHops(10);
      if (empty($session->token)) {
        $this->view->formToken = $session->token = md5(time() . $viewer->getIdentity() . get_class($this));
      } else {
        $this->view->formToken = $session->token;
      }
    }
  }

}
