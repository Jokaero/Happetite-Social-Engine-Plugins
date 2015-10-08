<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();
    // Get level id
    if( null !== ($id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null)) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    $viewer = Engine_Api::_()->user()->getViewer();
    $href = $viewer->getHref() . '?cover_photo_preview=1&level_id='.$id;
    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'upload', array(
        'label' => 'Allow to Upload Cover Photos?',
        'description' => 'Do you want to let members upload cover photos?',
        'multiOptions' => array(
          1 => 'Yes, allow user to upload cover photos',
          0 => 'No, do not allow users to upload cover photos.'
        ),
        'value' => 1,
      ));

			$description = sprintf(Zend_Registry::get('Zend_Translate')->_("%1sClick here%2s to upload and set default user cover photo on your site. (Note: This photo will be displayed on various pages as configured by you from the Layout Editor, until members upload a cover photo.)"), "<a href='$href' target='_blank'>", "</a>");
			$this->addElement('dummy', 'siteusercoverphoto_dummy', array(
				'label' => 'Default User Cover Photo', 
				'description'=> $description));
			$this->siteusercoverphoto_dummy->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
      if(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$id.id")) {
				$image = Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$id.id"), 'thumb.cover')->map();
				$description = sprintf(Zend_Registry::get('Zend_Translate')->_("%1sClick to view default cover photo%2s"), "<a onclick='showPreview();'>", "</a><div style='display:none;' id='show_default_preview' class='admin_file_preview'><img src='$image' style='max-height:200px;max-width:650px;min-width:450px;'></div>");
				$this->addElement('dummy', 'siteusercoverphoto_preview', array(
					'description' => $description,
				));

				$this->siteusercoverphoto_preview->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
      }
    }
  }

}