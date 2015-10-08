<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Seaocore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Integrated.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Seaocore_Model_DbTable_Integrated extends Engine_Db_Table
{

  public function getModuleTitle($name) {

		$coreTable = Engine_Api::_()->getDbtable('modules', 'core');
		$coreTableName = $coreTable->info('name');
		$select = $coreTable->select()
						->from($coreTableName, array('title'))
						->where('name =?', $name);
	  $title = $select->query()->fetchColumn();
		return $title;
  }

}