<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    // Get level id
    if (null !== ($id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;
    $resource_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);

    $viewer = Engine_Api::_()->user()->getViewer();
    $href = '';
    $primaryhref = '';
    $listingtype_id = 0;

    if (strpos($resource_type, "sitereview") !== false) {
			$exploded = explode('_', $resource_type);
      $listingtype_id = $exploded[2];
    }


    $modules = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getModules();
    
    if(!empty($modules)) {
			$this->addElement('Select', 'contentModule', array(
					'label' => 'Content Module',
					'onchange' => "javascript:fetchModuleName(this.value,  '$listingtype_id');",
					'multiOptions' => Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getModules()
			));

// 			if (strpos($resource_type, "sitereview") !== false) {
// 				$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingtype_id);
// 				$listingTypeTable = Engine_Api::_()->getDbTable('listingtypes', 'sitereview');
// 				$listingTypes = $listingTypeTable->getListingTypesArray(0, 0);
// 				$listingTypeCount = $listingTypeTable->getListingTypeCount();
// 
// 				if ($listingTypeCount > 1) {
// 					$this->addElement('Select', 'listingtype_id', array(
// 							'label' => 'Listing Type',
// 							'description' => '',
// 							'onchange' => "javascript:fetchListingtypeLevelSettings(this.value, '$listingtype_id');",
// 							'multiOptions' => $listingTypes,
// 					));
// 				}
// 			}

			if (empty($resource_type)) {
				$href = $viewer->getHref() . '?cover_photo_preview=1&level_id=' . $id;
			} else {
				if (strpos($resource_type, "sitereview") !== false) {
					$table = Engine_Api::_()->getItemtable('sitereview_listing');
        } else {
					$table = Engine_Api::_()->getItemtable($resource_type);
        }
				$primary_id = current($table->info('primary'));
				$columnArray = $table->info('cols');
				if (in_array('user_id', $columnArray)) {
					$user_id = 'user_id';
				} elseif (in_array('owner_id', $columnArray)) {
					$user_id = 'owner_id';
				}

				if (strpos($resource_type, "sitereview") !== false) {
					$primary_id_value = $table->select()->from($table->info('name'), array($primary_id))->where("$user_id =?", $viewer->getIdentity())->where("listingtype_id =?", $listingtype_id)->query()->fetchColumn();
				} else {
					$primary_id_value = $table->select()->from($table->info('name'), array($primary_id))->where("$user_id =?", $viewer->getIdentity())->query()->fetchColumn();
				}
				if (!$primary_id_value) {
					if (strpos($resource_type, "sitereview") !== false) {
						$primary_id_value = $table->select()->from($table->info('name'), array($primary_id))->where("listingtype_id =?", $listingtype_id)->query()->fetchColumn();
					} else {
						$primary_id_value = $table->select()->from($table->info('name'), array($primary_id))->query()->fetchColumn();
					}
				}
				if ($primary_id_value) {
					if (strpos($resource_type, "sitereview") !== false) {
						$primaryhref = Engine_Api::_()->getItem('sitereview_listing', $primary_id_value)->getHref();
					} else {
						$primaryhref = Engine_Api::_()->getItem($resource_type, $primary_id_value)->getHref();
					}
        } 
				if ($primaryhref) {
					$href = $primaryhref . '?cover_photo_preview=1&level_id=' . $id . '&resource_type=' . $resource_type;
				}
			}

			parent::init();

			// My stuff
			$this
							->setTitle('Member Level Settings')
							->setDescription('Here, you can enable members to upload cover photos for their content. Below, you can also upload default cover photos for content types, which, if enabled, the content owners can then change individually. These settings are applied on a per member level basis. So, start by selecting the member level you want to modify, then adjust the settings for that level below.');

			if (!$this->isPublic()) {

				// Element: create
				$this->addElement('Radio', 'upload', array(
						'label' => 'Allow to Upload Cover Photos?',
						'description' => 'Do you want to let members upload Cover Photos for their Content?',
						'multiOptions' => array(
								1 => 'Yes, allow users to upload Cover Photos.',
								0 => 'No, do not allow users to upload Cover Photos.'
						),
						'value' => 1,
				));
			}

			$checkEnable = 1;
			if (($resource_type === 'sitepage_page') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate', 1))) {
				if (!$primary_id_value) {
				$description = sprintf(Zend_Registry::get('Zend_Translate')->_('<div class="tip" style="margin-top:-9px"><span>Currently, there is no content in this module. Please create some content in this module to upload and set the default cover photo. (Note: You can upload default cover photo only when you have "%1$sDirectory / Pages - Photo Albums Extension%2$s" installed and enabled on your website. To see details, please visit here. %1$shttp://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums%2$s.)</span></div>'), '<a href="http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums" target="_blank">', '</a>');
				} else {
					$description = sprintf(Zend_Registry::get('Zend_Translate')->_('You can not upload a default cover photo for pages on your site as this feature is dependent on "%1$sDirectory / Pages - Photo Albums Extension%2$s". To see details, please visit here: "%3$sDirectory / Pages - Photo Albums Extension%4$s". [Note: Members can also upload the Cover Photo only when you have "%5$sDirectory / Pages - Photo Albums Extension"%6$s installed and enabled on your site.]'), '<a href="http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums" target="_blank">', '</a>');
				}
				$checkEnable = 0;
			} else if (($resource_type === 'sitebusiness_business') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessalbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusinessalbum.isActivate', 1))) {
				if (!$primary_id_value) {
				$description = sprintf(Zend_Registry::get('Zend_Translate')->_('<div class="tip" style="margin-top:-9px"><span>Currently, there is no content in this module. Please create some content in this module to upload and set the default cover photo. (Note: You can upload default cover photo only when you have "%1$sDirectory / Businesses - Photo Albums Extension%2$s" installed and enabled on your website. To see details, please visit here. %1$shttp://www.socialengineaddons.com/businessextensions/socialengine-directory-businesses-photo-albums%2$s.)</span></div>'), '<a href="http://www.socialengineaddons.com/businessextensions/socialengine-directory-businesses-photo-albums" target="_blank">', '</a>');
				} else {
				$description = sprintf(Zend_Registry::get('Zend_Translate')->_('You can not upload a default cover photo for businesses on your site as this feature is dependent on "%1$sDirectory / Businesses - Photo Albums Extension%2$s". To see details, please visit here: "%3$sDirectory / Businesses - Photo Albums Extension%4$s". [Note: Members can also upload the Cover Photo only when you have "%5$sDirectory / Businesses - Photo Albums Extension%6$s" installed and enabled on your site.]') , '<a href="http://www.socialengineaddons.com/businessextensions/socialengine-directory-businesses-photo-albums" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/businessextensions/socialengine-directory-businesses-photo-albums" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/businessextensions/socialengine-directory-businesses-photo-albums" target="_blank">', '</a>');
				}
				$checkEnable = 0;
			}

			if (!empty($resource_type)) {
				if (!$primary_id_value && $checkEnable) {
					$description = Zend_Registry::get('Zend_Translate')->_("<div class='tip' style='margin-top:-9px'><span>Currently, there is no content in this module. Please create some content in this module to upload and set the default cover photo.");
				} elseif ($checkEnable && $primary_id_value) {
					$description = sprintf(Zend_Registry::get('Zend_Translate')->_("%1sClick here%2s to upload and set default cover photo for content of this module. (Note: This photo will be displayed on Content Profile pages as configured by you from the Layout Editor, until members upload Cover Photo for associated content.)"), "<a href='$href' target='_blank'>", "</a>");
				}
			} else {
				$description = Zend_Registry::get('Zend_Translate')->_("<div class='tip' style='margin-top:-9px'><span>You have not selected any content module for which you want to upload default cover photo.</span></div>");
			}

			if ($this->isPublic()) {
				$description = sprintf(Zend_Registry::get('Zend_Translate')->_("%1sClick here%2s to upload and set default cover photo for content of this module."), "<a href='$href' target='_blank'>", "</a>");
			}

			$this->addElement('dummy', 'sitecontentcoverphoto_dummy', array(
					'label' => 'Default Cover Photo',
					'description' => $description));
			$this->sitecontentcoverphoto_dummy->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
			$setting = 0;
			$resource = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getModuleName(array('resource_type' => $resource_type));

			if ($listingtype_id && $resource) {
				$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingtype_id);
				$titleSinLc = strtolower($listingType->title_singular);
				$setting = Engine_Api::_()->getApi("settings", "core")->getSetting("sitecontentcoverphoto.$resource.$titleSinLc.cover.photo.preview.level.$id.id");
			} elseif ($resource) {
				$setting = Engine_Api::_()->getApi("settings", "core")->getSetting("sitecontentcoverphoto.$resource.cover.photo.preview.level.$id.id");
			}

			if ($setting) {
				$image = Engine_Api::_()->storage()->get($setting, 'thumb.cover')->map();
				$description = sprintf(Zend_Registry::get('Zend_Translate')->_("%1sClick to view default cover photo%2s"), "<a onclick='showPreview();'>", "</a><div style='display:none;' id='show_default_preview' class='admin_file_preview'><img src='$image' style='max-height:200px;max-width:650px;min-width:450px;'></div>");
				$this->addElement('dummy', 'sitecontentcoverphoto_preview', array(
						'description' => $description,
				));
				$this->sitecontentcoverphoto_preview->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
			}
    } else {

			// My stuff
			$this
							->setTitle('Member Level Settings')
							->setDescription('Here, you can enable members to upload cover photos for their content. Below, you can also upload default cover photos for content types, which, if enabled, the content owners can then change individually. These settings are applied on a per member level basis. So, start by selecting the member level you want to modify, then adjust the settings for that level below.');
				$description = Zend_Registry::get('Zend_Translate')->_("<div class='tip' style='margin-top:-9px'><span>There are no modules available.</span></div>");
				$this->addElement('dummy', 'sitecontentcoverphoto_no_dummy', array(
				'description' => $description));
				$this->sitecontentcoverphoto_no_dummy->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    }
  }

}