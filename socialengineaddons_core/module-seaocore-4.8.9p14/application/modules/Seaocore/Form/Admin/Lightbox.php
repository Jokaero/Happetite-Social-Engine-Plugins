<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Lightbox.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Lightbox extends Engine_Form {

  public function init() {

    $this->loadDefaultDecorators();

    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.isActivate', 0)) {
      $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here, you can customize the Advanced Lightbox Viewer for displaying the photos on your website. You can also integrate any 3rd party / SocialEngine Core plugin installed on your site to display photos belonging to it in the Advanced Lightbox Viewer. (Note: This integration feature is dependent on the '%1sAdvanced Photo Albums Plugin%2s' and requires it to be installed and enabled on your site. Please install this plugin after downloading it from your Client Area on SocialEngineAddOns. You may purchase this plugin over %3shere%4s.)"), "<a
	href='http://www.socialengineaddons.com/socialengine-advanced-photo-albums-plugin' target='_blank'>", "</a>", "<a href='http://www.socialengineaddons.com/socialengine-advanced-photo-albums-
	plugin' target='_blank'>", "</a>");
    } else {
      $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here, you can customize the Advanced Lightbox Viewer for displaying the photos on your website. You can also integrate any 3rd party / SocialEngine Core plugin installed on your site to display photos belonging to it in the Advanced Lightbox Viewer."));
    }

    $this
            ->setTitle('Advanced Lightbox Viewer Settings')
            ->setDescription("$description");

    $this->getDecorator('Description')->setOption('escape', false);


    $this->addElement('Radio', 'seaocore_display_lightbox', array(
        'label' => 'Advanced Lightbox Display',
        'description' => "Do you want users of your site to be able to view all photos in the Advanced Lighbox Viewer after clicking on their thumbnails? (Selecting 'Yes' will enable the lightbox display for all photo thumbnails on your site which have SocialEngine's core 'thumbs_photo' CSS class applied on them. Plugins that follow SocialEngine's standards use this CSS class on photo thumbnails. If you select 'No' over here, then you will be able to choose below the modules / plugins for which you want the photos to be displayed in the lightbox viewer; for photo thumbnails in the selected modules also the 'thumbs_photo' CSS class should be coming on the photo thumbnails.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showModuleName(this.value)',
        'value' => SEA_DISPLAY_LIGHTBOX,
    ));

    $this->seaocore_display_lightbox->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $includeThirdPartyModules = array();

    if (SITEALBUM_ENABLED) {
      $includeThirdPartyModules = array('group' => 'Group', 'event' => 'Event', 'advgroup' => 'Group', 'ynevent' => 'Event');
    }

    $enableSubModules = array();
    $includeOtherModules = array("activity" => "Activity Feed", "sitepagealbum"
        => "Directory / Pages - Photo Albums Extension", "sitepagenote" => "Directory /
Pages - Notes Extension ", "list" => "Listing", "recipe" => "Recipe", "sitelike"
        => "Likes Plugin and Widgets", "sitealbum" => "Advanced Photo Albums", "sitebusinessalbum"
        => "Directory / Businesses - Photo Albums Extension", "sitebusinessnote" => "Directory /
Businesses - Notes Extension", "sitepageevent" => "Directory /
Pages - Events Extension ", "sitebusinessevent" => "Directory /
Businesses - Events Extension ", "sitegroupalbum"
        => "Groups / Communities - Photo Albums Extension", "sitegroupnote" => "Directory /
Groups - Notes Extension ", "sitegroupevent" => "Directory /
Groups - Events Extension ", "sitestorealbum"
        => "Stores / Communities - Photo Albums Extension", "sitestorenote" => "Directory /
Stores - Notes Extension ", "sitestoreevent" => "Directory /
Stores - Events Extension ", "siteevent" => 'SEAO - Advanced Events ');

    $includeModules = array_merge($includeThirdPartyModules, $includeOtherModules);

    $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
    foreach ($enableModules as $module) {
      $enableSubModules[$module] = $includeModules[$module];
    }
    $coreApiSetting = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('MultiCheckbox', 'seaocore_lightbox_option_display', array(
        'label' => "Modules / Plugins",
        'description' => "Select the modules / plugins for which you want the photos belonging to them to be displayed in the Advanced Lighbox Viewer after clicking of their thumbnails (For the selected modules, the CSS class: 'thumbs_photo' should be coming on the photo thumbnails. If you do not see a desired plugin listed below, then please follow the Guidelines at the link mentioned at the top of this page.).",
        'multiOptions' => $enableSubModules,
        'value' => $coreApiSetting->getSetting('seaocore.lightbox.option.display', array_keys($enableSubModules)),));

    $this->addElement('Radio', 'sea_lightbox_fixedwindow', array(
        'label' => 'Advanced Lightbox View Mode',
        'description' => 'Select a view mode for the Photo Lightbox Viewer.',
        'multiOptions' => array(
            1 => 'Theater Mode [This is the latest advanced viewer for photos.]',
            0 => 'Lightbox Mode [This is the old viewer for photos.]'
        ),
        'onclick' => 'showOptions("seaocore_photolightbox_fontcolor",this.value)',
        'value' => $coreApiSetting->getSetting('sea.lightbox.fixedwindow', 1),
    ));

    //COLOR VALUE FOR BACKGROUND COLOR
    $this->addElement('Text', 'seaocore_photolightbox_bgcolor', array(
        'decorators' => array(
            array('ViewScript', array(
                    'viewScript' => '_formImagerainbowLightBoxBg.tpl',
                    'class' => 'form element'
                )))
    ));

    //COLOR VALUE FOR FONT COLOR
    $this->addElement('Text', 'seaocore_photolightbox_fontcolor', array(
        'decorators' => array(
            array('ViewScript', array(
                    'viewScript' => '_formImagerainbowLightBoxFont.tpl',
                    'class' => 'form element'
                )))
    ));

    //ENABLE COMMUNITYAD PLUGIN
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Radio', 'seaocore_lightboxads', array(
          'label' => 'Community Ads in Photos Lightbox',
          'description' => 'Do you want Community Ads to be shown in the Advanced Photos Lightbox?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showlightboxads(this.value)',
          'value' => $coreApiSetting->getSetting('seaocore.lightboxads', 1),
      ));

      $this->addElement('Radio', 'seaocore_adtype', array(
          'label' => 'Type of Community Ads in Photos Lightbox',
          'description' => 'What type of Community Ads do you want to be shown in the Advanced Photos Lightbox?',
          'multiOptions' => array(
              3 => 'All',
              2 => 'Sponsored Ads',
              1 => 'Featured Ads',
              0 => 'Both Sponsored and Featured Ads'
          ),
          'value' => $coreApiSetting->getSetting('seaocore.adtype', 3),
      ));
    }


    $this->addElement('Radio', 'seaocore_photo_makeprofile', array(
        'label' => 'Make Profile Photo',
        'description' => 'Do you want the "Make Profile Photo" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable users to make the photos that they can view as their profile photo.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreApiSetting->getSetting('seaocore.photo.makeprofile', 1),
    ));


    $this->addElement('Radio', 'seaocore_photo_download', array(
        'label' => 'Download',
        'description' => 'Do you want the "Download" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable users to download the photos that they can view.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreApiSetting->getSetting('seaocore.photo.download', 1),
    ));


    //VALUE FOR ENABLE /DISABLE SHARE
    $this->addElement('Radio', 'seaocore_photo_share', array(
        'label' => 'Share',
        'description' => 'Do you want the "Share" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable users to share the photos that they can view.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.share', 1),
    ));

    //VALUE FOR ENABLE/DISABLE REPORT
    $this->addElement('Radio', 'seaocore_photo_report', array(
        'label' => 'Report as Inappropriate',
        'description' => 'Do you want the "Report" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable logged-in users to report as inappropriate the photos that they can view (Members will also be able to mention the reason why they find the photo inappropriate.).',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreApiSetting->getSetting('seaocore.photo.report', 1),
    ));

    if (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->version >= '4.8.5') {

      $this->addElement('Radio', 'seaocore_photo_editlocation', array(
          'label' => 'Edit Location',
          'description' => 'Do you want the "Edit Location" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable logged-in users to edit location of owned photos. [ This setting is affected only in Advanced Photo Albums Plugin.]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $coreApiSetting->getSetting('seaocore.photo.editlocation', 1),
      ));

      //VALUE FOR ENABLE/DISABLE GETLINK
      $this->addElement('Radio', 'seaocore_photo_getlink', array(
          'label' => 'Get Link',
          'description' => 'Do you want the "Get Link" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable logged-in users to get link of owned photos and send the owned photos to friends by messeges. [ This setting is affected only in Advanced Photo Albums Plugin.]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $coreApiSetting->getSetting('seaocore.photo.getlink', 1),
      ));

      $this->addElement('Radio', 'seaocore_photo_sendmail', array(
          'label' => 'Tell a Friend',
          'description' => 'Do you want the "Tell a Friend" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable logged-in users to send the owned photos by email . [ This setting is affected only in Advanced Photo Albums Plugin.]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $coreApiSetting->getSetting('seaocore.photo.sendmail', 1),
      ));

      $this->addElement('Radio', 'seaocore_photo_makealbumcover', array(
          'label' => 'Make Album Main Photo',
          'description' => 'Do you want the "Make Album Main Photo" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable logged-in users to make the owned photos main photo of album that belongs to viewing photo . [ This setting is affected only in Advanced Photo Albums Plugin.]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $coreApiSetting->getSetting('seaocore.photo.makealbumcover', 1),
      ));

      $this->addElement('Radio', 'seaocore_photo_movetootheralbum', array(
          'label' => 'Move To other Album',
          'description' => 'Do you want the "Move To other Album" link to be shown in the Photo Lightbox Viewer below the photos? This link will enable logged-in users to move owned photos in another album .  [ This setting is affected only in Advanced Photo Albums Plugin.]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $coreApiSetting->getSetting('seaocore.photo.movetootheralbum', 1),
      ));
    }

    //VALUE FOR ENABLE/DISABLE REPORT
    $this->addElement('Radio', 'seaocore_photo_title', array(
        'label' => 'Photo Title',
        'description' => 'Do you want the Title of the photos to be shown in the Photo Lightbox Viewer?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreApiSetting->getSetting('seaocore.photo.title', 0),
    ));

    $this->addElement('Radio', 'seaocore_photo_pinit', array(
        'label' => '"Pin it" Button',
        'description' => 'Do you want the button of "Pin it" to be shown in the Photo Lightbox Viewer?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreApiSetting->getSetting('seaocore.photo.pinit', 0),
    ));
    
    $this->addElement('Radio', 'seaocore_gotophoto', array(
        'label' => '"Go to Photo" Button',
        'description' => 'Do you want the button of "Go to Photo" to be shown in the Photo Lightbox Viewer?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreApiSetting->getSetting('seaocore.gotophoto', 1),
    ));    

    $this->addElement('Radio', 'seaocore_photo_code_share', array(
        'label' => 'Social Share Buttons',
        'description' => 'Do you want the buttons of "Social Share" to be shown in the Photo Lightbox Viewer? ',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreApiSetting->getSetting('seaocore.photo.code.share', 0),
    ));
    $this->seaocore_photo_code_share->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>