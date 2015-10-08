<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Settings.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Seaocore_Model_Dbtable_UserSettings extends User_Model_DbTable_Settings {

  protected $_name = 'user_settings';

  public function setSetting(User_Model_User $user, $key, $value) {
    if (null === $value) {
      $this->delete(array(
          'user_id = ?' => $user->getIdentity(),
          'name = ?' => $key,
      ));
    } else if (false === ($prev = $this->getSetting($user, $key))) {
      $this->insert(array(
          'user_id' => $user->getIdentity(),
          'name' => $key,
          'value' => $value,
      ));
    } else {
      $this->update(array(
          'value' => $value,
              ), array(
          'user_id = ?' => $user->getIdentity(),
          'name = ?' => $key,
      ));
    }
    return $this;
  }

}