<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Seaocores.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_Model_DbTable_Seaocores extends Engine_Db_Table
{
  protected $_name = 'seaocores';
	protected $_rowClass = 'Seaocore_Model_Seaocore';

	public function getModules()
	{
			$tableName = $this->info('name');
			$select = $this->select()->from($tableName, array('module_name'));
			$modArray = $select->query()->fetchAll();
			return $modArray;
	}

	public function modActivate($modName) {
		$this->update(array("is_activate" => 1), array('module_name =?' => $modName));
	}
}