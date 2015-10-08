<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ListItems.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Seaocore_Model_DbTable_Tabs extends Engine_Db_Table {

  protected $_rowClass = 'Seaocore_Model_Tab';
  protected $_location;

  // Get the tabs
  public function getTabs($params=array()) {
    $select = $this->select()
                    ->where('module = ?', $params['module'])
                    ->where('type = ?', $params['type'])
                    ->order('order');
    if (isset($params['enabled']))
      $select->where('enabled = ?', $params['enabled']);
    if (isset($params['name']))
      $select->where('name = ?', $params['name']);
    return $this->fetchAll($select);
  }

}
