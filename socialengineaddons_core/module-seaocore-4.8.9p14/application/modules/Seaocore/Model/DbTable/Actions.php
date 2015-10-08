<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Actions.php 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Model_DbTable_Actions extends Activity_Model_DbTable_Actions {

  protected $_rowClass = 'Activity_Model_Action';
  protected $_serializedColumns = array('params');
  protected $_name = 'activity_actions';
  protected $_actionTypes;

  public function addActivity(Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $type, $body = null, array $params = null) {
    // Disabled or missing type
    $typeInfo = $this->getActionType($type);
    if (!$typeInfo || !$typeInfo->enabled) {
      return;
    }

    // User disabled publishing of this type
    $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
    if ($subject->getType() == "user") {
      if (!$actionSettingsTable->checkEnabledAction($subject, $type)) {
        return;
      }
    } else {
      if (!$actionSettingsTable->checkEnabledAction($object, $type)) {
        return;
      }
    }


    $socialDNApublish = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdnapublisher');

    $activityPoints = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('activitypoints');
    if ($socialDNApublish || $activityPoints) {
      // To make compatebile with "Social DNA Publisher" Plugin
      $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onSemodsAddActivity', array(
          'subject' => $subject,
          'object' => $object,
          'type' => $type,
          'body' => $body,
          'params' => $params,
      ));
    }

    // Create action
    $action = $this->createRow();
    $action->setFromArray(array(
        'type' => $type,
        'subject_type' => $subject->getType(),
        'subject_id' => $subject->getIdentity(),
        'object_type' => $object->getType(),
        'object_id' => $object->getIdentity(),
        'body' => (string) $body,
        'params' => (array) $params,
        'date' => date('Y-m-d H:i:s')
    ));
    $action->save();

    // Add bindings
    $this->addActivityBindings($action, $type, $subject, $object);

    // We want to update the subject
    if (isset($subject->modified_date)) {
      $subject->modified_date = date('Y-m-d H:i:s');
      $subject->save();
    }
    if ($socialDNApublish || $activityPoints) {
      // To make compatebile with "Social DNA Publisher" Plugin
      $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onSemodsAddActivityAfter', array(
          'subject' => $subject,
          'object' => $object,
          'type' => $type,
          'body' => $body,
          'params' => $params,
          'action' => $action,
      ));
    }
    return $action;
  }

  public function getActivity(User_Model_User $user, array $params = array()) {
    // Proc args
    extract($this->_getInfo($params)); // action_id, limit, min_id, max_id
    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    $mainActionTypes = array();

    // Filter out types set as not displayable
    foreach ($masterActionTypes as $type) {
      if ($type->displayable & 4) {
        $mainActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $mainActionTypes = array_intersect($mainActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $mainActionTypes = array_diff($mainActionTypes, $hideTypes);
    }

    // Nothing to show
    if (empty($mainActionTypes)) {
      return null;
    }
    // Show everything
    else if (count($mainActionTypes) == count($masterActionTypes)) {
      $mainActionTypes = true;
    }
    // Build where clause
    else {
      $mainActionTypes = "'" . join("', '", $mainActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
        'for' => $user,
    ));
    $responses = (array) $event->getResponses();

    if (empty($responses)) {
      return null;
    }

    foreach ($responses as $response) {
      if (empty($response))
        continue;

      $select = $streamTable->select()
              ->from($streamTable->info('name'), 'action_id')
              ->where('target_type = ?', $response['type'])
      ;

      if (empty($response['data'])) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        // Single
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        // Array
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if (null !== $action_id) {
        $select->where('action_id = ?', $action_id);
      } else {
        if (null !== $min_id) {
          $select->where('action_id >= ?', $min_id);
        } else if (null !== $max_id) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      if ($mainActionTypes !== true) {
        $select->where('type IN(' . $mainActionTypes . ')');
      }

      // Add order/limit
      $select
              ->order('action_id DESC')
              ->limit($limit);

      // Add to main query
      $union->union(array('(' . $select->__toString() . ')')); // (string) not work before PHP 5.2.0
    }

    // Finish main query
    $union
            ->order('action_id DESC')
            ->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // Process ids
    $ids = array();
    if (Engine_Api::_()->hasItemType('sitebusiness_business')) {
      $ids = Engine_Api::_()->sitebusiness()->getFeedActionLikedBusinesses($user, $this->_getInfo($params));
    }
    // No visible actions and ids
    if (empty($actions) && empty($ids)) {
      return null;
    }

    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $this->fetchAll(
                    $this->select()
                            ->where('action_id IN(' . join(',', $ids) . ')')
                            ->order('action_id DESC')
                            ->limit($limit)
    );
  }

  public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user, array $params = array()) {
    // Proc args
    extract($this->_getInfo($params)); // action_id, limit, min_id, max_id
    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    $subjectActionTypes = array();
    $objectActionTypes = array();

    // Filter types based on displayable
    foreach ($masterActionTypes as $type) {
      if ($type->displayable & 1) {
        $subjectActionTypes[] = $type->type;
      }
      if ($type->displayable & 2) {
        $objectActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $subjectActionTypes = array_intersect($subjectActionTypes, $showTypes);
      $objectActionTypes = array_intersect($objectActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $subjectActionTypes = array_diff($subjectActionTypes, $hideTypes);
      $objectActionTypes = array_diff($objectActionTypes, $hideTypes);
    }

    // Nothing to show
    if (empty($subjectActionTypes) && empty($objectActionTypes)) {
      return null;
    }

    if (empty($subjectActionTypes)) {
      $subjectActionTypes = null;
    } else if (count($subjectActionTypes) == count($masterActionTypes)) {
      $subjectActionTypes = true;
    } else {
      $subjectActionTypes = "'" . join("', '", $subjectActionTypes) . "'";
    }

    if (empty($objectActionTypes)) {
      $objectActionTypes = null;
    } else if (count($objectActionTypes) == count($masterActionTypes)) {
      $objectActionTypes = true;
    } else {
      $objectActionTypes = "'" . join("', '", $objectActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
        'for' => $user,
        'about' => $about,
    ));
    $responses = (array) $event->getResponses();

    if (empty($responses)) {
      return null;
    }

    foreach ($responses as $response) {
      if (empty($response))
        continue;

      // Target info
      $select = $streamTable->select()
              ->from($streamTable->info('name'), 'action_id')
              ->where('target_type = ?', $response['type'])
      ;

      if (empty($response['data'])) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        // Single
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        // Array
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if (null !== $action_id) {
        $select->where('action_id = ?', $action_id);
      } else {
        if (null !== $min_id) {
          $select->where('action_id >= ?', $min_id);
        } else if (null !== $max_id) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      // Add order/limit
      $select
              ->order('action_id DESC')
              ->limit($limit);


      // Add subject to main query
      $selectSubject = clone $select;
      if ($subjectActionTypes !== null) {
        if ($subjectActionTypes !== true) {
          $selectSubject->where('type IN(' . $subjectActionTypes . ')');
        }
        $selectSubject
                ->where('subject_type = ?', $about->getType())
                ->where('subject_id = ?', $about->getIdentity());
        $union->union(array('(' . $selectSubject->__toString() . ')')); // (string) not work before PHP 5.2.0
      }

      // Add object to main query
      $selectObject = clone $select;
      if ($objectActionTypes !== null) {
        if ($objectActionTypes !== true) {
          $selectObject->where('type IN(' . $objectActionTypes . ')');
        }
        $selectObject
                ->where('object_type = ?', $about->getType())
                ->where('object_id = ?', $about->getIdentity());
        $union->union(array('(' . $selectObject->__toString() . ')')); // (string) not work before PHP 5.2.0
      }
    }

    // Finish main query
    $union
            ->order('action_id DESC')
            ->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // Process ids
    $ids = array();
    if ($about->getType() == 'sitebusiness_business' || $about->getType() == 'sitebusinessevent_event') {
      $ids = Engine_Api::_()->getApi('subCore', 'sitebusiness')->getEveryoneBusinessProfileFeeds($about, $this->_getInfo($params));
    }
    // No visible actions and ids
    if (empty($actions) && empty($ids)) {
      return null;
    }

    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $this->fetchAll(
                    $this->select()
                            ->where('action_id IN(' . join(',', $ids) . ')')
                            ->order('action_id DESC')
                            ->limit($limit)
    );
  }

  // Utility

  /**
   * Add an action-privacy binding
   *
   * @param int $action_id
   * @param string $type
   * @param Core_Model_Item_Abstract $subject
   * @param Core_Model_Item_Abstract $object
   * @return int The insert id
   */
  public function addActivityBindings($action) {
    // Get privacy bindings
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('addActivity', array(
        'subject' => $action->getSubject(),
        'object' => $action->getObject(),
        'type' => $action->type,
    ));

    // Add privacy bindings

    $privacyNetworkIds = array();
    $item = $object = $action->getObject();
    if (!($object instanceof User_Model_User) && !isset($object->networks_privacy)) {
      $item = $object->getParent();
    }

    $hasAddNetworkPrivacy = false;
    $viewNetworkPricavyEnable = (isset($item->networks_privacy) && $item->networks_privacy) ? Engine_Api::_()->getApi('settings', 'core')->getSetting(strtolower($item->getModuleName()) . '.networkprofile.privacy', 0) : 0;
    if ($viewNetworkPricavyEnable) {
      $privacyNetworkIds = is_array($item->networks_privacy)? $item->networks_privacy :explode(",", $item->networks_privacy);
    }
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    foreach ((array) $event->getResponses() as $response) {
      if (isset($response['target'])) {
        $target_type = $response['target'];
        $target_id = 0;
      } else if (isset($response['type']) && isset($response['identity'])) {
        $target_type = $response['type'];
        $target_id = $response['identity'];
      } else {
        continue;
      }

      if (count($privacyNetworkIds) && in_array($target_type, array('everyone', 'registered', 'network', 'members'))) {
        $hasAddNetworkPrivacy = true;
        continue;
      }

      $streamTable->insert(array(
          'action_id' => $action->action_id,
          'type' => $action->type,
          'target_type' => (string) $target_type,
          'target_id' => (int) $target_id,
          'subject_type' => $action->subject_type,
          'subject_id' => $action->subject_id,
          'object_type' => $action->object_type,
          'object_id' => $action->object_id,
      ));
    }

    if ($hasAddNetworkPrivacy) {
      $target_type = 'network';
      foreach ($privacyNetworkIds as $target_id) {
        $streamTable->insert(array(
            'action_id' => $action->action_id,
            'type' => $action->type,
            'target_type' => 'network',
            'target_id' => (int) $target_id,
            'subject_type' => $action->subject_type,
            'subject_id' => $action->subject_id,
            'object_type' => $action->object_type,
            'object_id' => $action->object_id,
        ));
      }
    }
    return $this;
  }

}