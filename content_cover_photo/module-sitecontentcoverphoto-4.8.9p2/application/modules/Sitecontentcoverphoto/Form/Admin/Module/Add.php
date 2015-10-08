<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Form_Admin_Module_Add extends Engine_Form {

  public function init() {

    $this->setTitle('Add New Module')
            ->setDescription('Use the form below to enable users to upload cover photos for their content. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    $notInclude = array('activity', 'advancedactivity', 'sitealbum', 'sitecontentcoverphoto', 'sitepageoffer', 'sitepagebadge', 'featuredcontent', 'sitepagediscussion', 'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'socialengineaddon', 'seaocore', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'sitetagcheckin', 'sitereviewlistingtype', 'sitegroupoffer', 'sitepageintegration', 'sitebusinessintegration', 'sitegroupintegration', 'sitepagemember', 'sitebusinessmember', 'sitegroupmember', 'sitemailtemplates', 'sitepageurl', 'sitestoreadmincontact', 'sitestorealbum', 'sitestoreform', 'sitestoreinvite', 'sitestorelikebox','sitestoreoffer', 'sitestoreproduct', 'sitestorereview', 'sitestoreurl', 'sitestorevideo', 'communityad', 'communityadsponsored', 'sitelike', 'sitestorelikebox', 'sitemobile', 'siteusercoverphoto', 'siteevent', 'sitereview', 'eventdocument', 'sitecoupon', 'siteestore', 'sitefaq', 'sitegroupadmincontact', 'sitegroupbadge', 'sitegroupdiscussion', 'sitegroupform', 'sitegroupinvite', 'sitegrouplikebox', 'sitegroupurl', 'sitevideoview', 'sitebusinessurl', 'sitestoreinvite', 'nestedcomment', 'sitemobileapp' );

    $newArray = array('album', 'blog',  'document', 'event', 'forum', 'poll', 'video', 'list', 'group', 'music', 'recipe', 'user', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitegroup', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupevent', 'sitegroupnote', 'sitegrouppoll', 'sitegroupmusic', 'sitegroupvideo', 'sitegroupreview', 'sitestore');

    $finalArray = array_merge($notInclude, $newArray);

    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $select = $module_table->select()
            ->from($module_name, array('name', 'title'))
            ->where($module_name . '.type =?', 'extra')
            ->where($module_name . '.name not in(?)', $finalArray)
            ->where($module_name . '.enabled =?', 1);

    $contentModuloe = $select->query()->fetchAll();
    $contentModuloeArray = array();

    if (!empty($contentModuloe)) {
      $contentModuloeArray[] = '';
      foreach ($contentModuloe as $modules) {
        $contentModuloeArray[$modules['name']] = $modules['title'];
      }
    }

    if (!empty($contentModuloeArray)) {
      $this->addElement('Select', 'module', array(
          'label' => 'Content Module',
          'allowEmpty' => false,
          'onchange' => 'setModuleName(this.value)',
          'multiOptions' => $contentModuloeArray,
      ));
    } else {
      //VALUE FOR LOGO PREVIEW.
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no new modules to be added to ‘Manage Module’ section.") . "</span></div>";
      $this->addElement('Dummy', 'module', array(
          'description' => $description,
      ));
      $this->module->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    $module = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_name', null);
    $contentItem = array();
    if (!empty($module)) {
      $this->module->setValue($module);
      $contentItem = $this->getContentItem($module);
      if (empty($contentItem))
        $this->addElement('Dummy', 'dummy_title', array(
            'description' => 'For this module, there is  no item defined in the manifest file.',
        ));
    }
    if (!empty($contentItem)) {
      $this->addElement('Select', 'resource_type', array(
          'label' => 'Database Table Item',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
          //  'required' => true,
          'multiOptions' => $contentItem,
      ));

      $this->addElement('Checkbox', 'enabled', array(
          'description' => 'Enable Module for Content Profile Cover Photo',
          'label' => 'Please enable module for showing the Content Profile Cover Photo.',
          'value' => 1
      ));

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

  public function getContentItem($moduleName) {

    $modulesTable = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto');
    $modulesTableName = $modulesTable->info('name');
    $moduleArray = $modulesTable->select()
            ->from($modulesTableName, "$modulesTableName.resource_type")
            ->where($modulesTableName . '.module = ?', $moduleName)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
    $contentItem = array();
    if (@file_exists($file_path)) {
      $ret = include $file_path;
      if (isset($ret['items'])) {
        foreach ($ret['items'] as $item)
          if (!in_array($item, $moduleArray))
            $contentItem[$item] = $item . " ";
      }
    }
    return $contentItem;
  }

}