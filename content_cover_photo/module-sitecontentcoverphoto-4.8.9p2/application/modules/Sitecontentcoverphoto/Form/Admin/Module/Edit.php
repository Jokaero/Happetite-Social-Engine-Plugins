<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Moduleedit.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Form_Admin_Module_Edit extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Edit Module')
            ->setDescription('Use the form below to enable users to upload cover photos for their content. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    $moduleId = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_id', null);
    $modulesTable = Engine_Api::_()->getDbTable('modules', 'sitecontentcoverphoto');
    $modulesTableResult = $modulesTable->fetchRow(array('module_id = ?' => $moduleId));
    $moduleame = array();
    $moduleame[] = $modulesTableResult->module;
    $resourceType[] = $modulesTableResult->resource_type;
    $this->addElement('Select', 'module', array(
        'label' => 'Content Module',
        'allowEmpty' => false,
        'disable' => true,
        'multiOptions' => $moduleame,
    ));

    $this->addElement('Select', 'resource_type', array(
        'label' => 'Database Table Item',
        'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
        'disable' => true,
        'multiOptions' => $resourceType,
    ));

    $this->addElement('Checkbox', 'enabled', array(
        'description' => 'Enable Module for Content Profile Cover Photo',
        'label' => 'Please enable module for showing the Content Profile Cover Photo.',
        'value' => 1
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}