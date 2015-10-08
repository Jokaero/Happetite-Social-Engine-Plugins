<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode"
  );
    
  public function init() {

    $this
            ->setTitle('Global Settings')
            ->setName('sitecontentcoverphoto_global_settings')
            ->setDescription('These settings affect all members in your community.');

    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('Text', 'sitecontentcoverphoto_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => $coreSettingsApi->getSetting('sitecontentcoverphoto.lsettings'),
    ));

    
    if( APPLICATION_ENV == 'production' ) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->addElement('Radio', 'sitecontentcoverphotoStrachMainPhoto', array(
        'label' => $view->translate('Consistent Profile Picture Blocks'),
        'description' => "Do you want profile pictures to be displayed in consistent blocks of fixed dimension below the cover photo on your site?",
        'multiOptions' => array(
            '1' => 'Yes (Though the dimensions of the profile picture block will be consistent, and the photos with unequal dimension will be shown in the center of the block.)',
            '0' => 'No (The dimension of the profile picture block will not be fixed. In this case block’s dimensions will depend on the dimensions of profile picture.)',
        ),
        'value' => $coreSettingsApi->getSetting('sitecontentcoverphotoStrachMainPhoto', 1),
    ));

    //Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));

    $this->addElement('Button', 'save', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}