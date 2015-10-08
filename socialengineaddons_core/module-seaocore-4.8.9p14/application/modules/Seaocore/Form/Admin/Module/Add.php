<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Module_Add extends Engine_Form {

    public function init() {

    $this->setTitle('Add New Module')
        ->setDescription('Use the form below to enable users to event for their content. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $select = $module_table->select()
            ->from($module_name, array('name', 'title'))
            ->where($module_name . '.type =?', 'extra')
            ->where($module_name . '.enabled =?', 1);

    $contentModuloe = $select->query()->fetchAll(); 
    $contentModuloeArray = array();

		if( !empty($contentModuloe) ) {
			$contentModuloeArray[] = '';
			foreach ($contentModuloe as $modules) {
				$contentModuloeArray[$modules['name']] = $modules['title'];
			}
		}

    $type = Zend_Controller_Front::getInstance()->getRequest()->getParam('type', null);
		if( !empty($contentModuloeArray) ) {
				$this->addElement('Select', 'item_module', array(
						'label' => 'Content Module',
						'allowEmpty' => false,
						'onchange' => "setModuleName(this.value, '$type')",
						'multiOptions' => $contentModuloeArray,
				));
		} else {
			//VALUE FOR LOGO PREVIEW.
			$description = "<div class='tip'><span>" . Zend_Registry::get( 'Zend_Translate' )->_( "There are currently no new modules to be added to ‘Manage Module’ section." ) . "</span></div>" ;
					$this->addElement( 'Dummy' , 'item_module' , array (
						'description' => $description ,
					)) ;
			$this->item_module->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;
		}

    $module = Zend_Controller_Front::getInstance()->getRequest()->getParam('item_module', null);
    
    $contentItem = array();
    if (!empty($module)) {
      $this->item_module->setValue($module);
      $contentItem = $this->getContentItem($module, $type);
      if (empty($contentItem))
        $this->addElement('Dummy', 'dummy_title', array(
            'description' => 'For this module, there is  no item defined in the manifest file.',

        ));
    }
    if (!empty ($contentItem)) {
      $this->addElement('Select', 'item_type', array(
          'label' => 'Database Table Item',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
        //  'required' => true,
          'multiOptions' => $contentItem,
      ));

			$this->addElement('Checkbox', 'enabled', array(
					'description' => 'Enable Module for Event in the Content',
					'label' => 'Enable Module for Event in the Content.',
					'value' => 1
			));

      $this->addElement('Hidden', 'type', array('order'=> 3, 'value' => $type));

			// Element: execute
			$this->addElement('Button', 'execute', array(
					'label' => 'Save Settings',
					'type' => 'submit',
					'ignore' => true,
					'decorators' => array('ViewHelper'),
			));

			// Element: cancel
			$this->addElement('Cancel', 'cancel', array(
					'label' => 'cancel',
					'prependText' => ' or ',
					'ignore' => true,
					'link' => true,
					'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
					'decorators' => array('ViewHelper'),
			));
    } 
  }

  public function getContentItem($moduleName, $type) {
    $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
		$mixSettingsTable = Engine_Api::_()->getDbtable( 'integrated' , 'seaocore' );
		$mixSettingsTableName = $mixSettingsTable->info('name');
		$moduleArray = $mixSettingsTable->select()
                    ->from($mixSettingsTableName, "$mixSettingsTableName.item_type")
                    ->where($mixSettingsTableName . '.item_module = ?', $moduleName)
										->where($mixSettingsTableName . '.type = ?', $type)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);

    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
    $contentItem = array();
    if (@file_exists($file_path)) {
      $ret = include $file_path;
      if (isset($ret['items'])) {
       foreach ($ret['items'] as $item) {
					if($id) {
						$contentItem[$item] = $item . " ";
					} else {
						if(!in_array($item , $moduleArray))
							$contentItem[$item] = $item . " ";
					}
        }
      }
    } 
    return $contentItem;
  }
}