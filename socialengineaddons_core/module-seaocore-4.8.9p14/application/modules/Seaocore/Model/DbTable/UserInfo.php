<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Comment.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Seaocore_Model_DbTable_UserInfo extends Engine_Db_Table
{
  protected $_rowClass = 'Seaocore_Model_UserInfo';
  
public function getColumnValue($user_id, $column_name) {

    $select = $this->select()
            ->from($this->info('name'), array("$column_name"));

    $select->where('user_id = ?', $user_id);
    
    if($select->limit(1)->query()->fetchColumn()) {
        return $select->limit(1)->query()->fetchColumn();
    } else {
        return '0';
    }
    
  }  

}