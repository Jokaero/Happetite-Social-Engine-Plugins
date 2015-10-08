<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ActivityLoopSea.php 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_ActivityLoopSea extends Activity_View_Helper_Activity {

  public function activityLoopSea($actions = null, array $data = array()) {
    if (null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract))) {
      return '';
    }

    $form = new Activity_Form_Comment();
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = "";
    if ($viewer->getIdentity()) {
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }
    $data = array_merge($data, array(
                'actions' => $actions,
                'commentForm' => $form,
                'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
                'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
                'activity_moderate' => $activity_moderate,
            ));

    return $this->view->partial(
            '_activityText.tpl',
            /*  Customization Start */
            'seaocore',
            /*  Customization End */
            $data
    );
  }

}