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
class Seaocore_Model_DbTable_Integratedtype extends Engine_Db_Table
{
	protected $_serializedColumns = array('params');

  public function getIntegratedModules($type, $withIntegratedType = null, $enabled = 0) {
    $integratedTable = Engine_Api::_()->getDbtable('integrated', 'seaocore');
    $integratedTableName = $integratedTable->info('name');
    $integratedTypeTableName = $this->info('name');
    $coreModuleTable = Engine_Api::_()->getDbtable('modules', 'core');
    $coreModuleTableName = $coreModuleTable->info('name');
		$select = $this->select()->setIntegrityCheck(false)
																	->from($integratedTypeTableName, array('type as integratedtype', 'params as integratedparams'))
																	->join($coreModuleTableName, $coreModuleTableName . '.name = ' . $integratedTypeTableName . '.module', array())
																	->where($coreModuleTableName.'.enabled =?', 1)
																	->where($integratedTypeTableName.'.type = ?', $type);

     if(!empty($withIntegratedType)) {
      $select->join($integratedTableName, $integratedTableName . '.type = ' . $integratedTypeTableName . '.type', array('*'));
		 }

     if($enabled) {
      $select->where($integratedTableName.'.enabled =?', 1);
     }
     return $this->fetchAll($select);
  }

  public function checkIntegratedModule($type = null, $item_module = null, $item_type = null, $onlyCheckInSeaocore = null) {
    $integratedTable = Engine_Api::_()->getDbtable('integrated', 'seaocore');
    $integratedTableName = $integratedTable->info('name');
    $integratedTypeTableName = $this->info('name');
    $coreModuleTable = Engine_Api::_()->getDbtable('modules', 'core');
    $coreModuleTableName = $coreModuleTable->info('name');
		$checkColumn = $this->select()->setIntegrityCheck(false)
																	->from($integratedTypeTableName, array())
																	->join($integratedTableName, $integratedTableName . '.type = ' . $integratedTypeTableName . '.type', array())
																	->join($coreModuleTableName, $coreModuleTableName . '.name = ' . $integratedTypeTableName . '.module', array())
																	->where($coreModuleTableName.'.enabled =?', 1)
																	->where($integratedTableName.'.item_module = ?', $item_module)
																	->where($integratedTableName.'.item_type = ?', $item_type)
																	->where($integratedTableName.'.enabled = ?', 1)
																	->query()	
																	->fetchColumn();

     if($onlyCheckInSeaocore && !$checkColumn) {
       return false;
     }
     if($checkColumn || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
       return true;
     } elseif($checkColumn || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessevent')) {
				return true;
     } elseif($checkColumn || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
				return true;
     } elseif($checkColumn || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) {
				return true;
     }

		
  }
  
}