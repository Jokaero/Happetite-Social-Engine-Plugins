<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Form_Admin_Global extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Global Settings')
                ->setName('siteusercoverphoto_global_settings')
                ->setDescription('These settings affect all members in your community.')
                ->setAttrib('id', 'form-upload');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $faqHref = $view->url(array('module' => 'siteusercoverphoto', 'controller' => 'settings', 'action' => 'faq', 'faq_id' => 'faq_1'), 'admin_default', true);

        $this->addElement('Dummy', 'siteusercoverphoto_faq_tip', array(
            'description' => "<a target='_blank' class='buttonlink icon_help' href='$faqHref'>I want to configure custom profile fields to be shown on various pages on my site. How can I do this in Mobile, Tablet and Full site?</a>",
            'decorators' => array(
                'ViewHelper', array(
                    'description', array('placement' => 'APPEND', 'escape' => false)
                ))
        ));

        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $this->addElement('Text', 'siteusercoverphoto_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => $coreSettingsApi->getSetting('siteusercoverphoto.lsettings'),
        ));

        if (APPLICATION_ENV == 'production') {
            $this->addElement('Checkbox', 'environment_mode', array(
                'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
                'description' => 'System Mode',
                'value' => 1,
            ));
        } else {
            $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
        }

        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true,
            'order' => 500,
        ));

        $this->addElement('radio', 'siteusercoverphoto_setlayout', array(
            'label' => 'Member Profile Page Layout',
            'description' => "You have enabled 3 column layout for the Member Profile page on your site. Do you want to change the layout of this page to 2 column and then place the “User Cover Photo and Information” widget? (If you select ‘No’, then the user cover photo will be placed in the middle column of the Member Profile page.)",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => $coreSettingsApi->getSetting('siteusercoverphoto.setlayout', 0),
            'onclick' => 'showLayoutOptions(this.value);'
        ));

        $this->addElement('Radio', 'siteusercoverphoto_layout', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formRadioButtonStructureCover.tpl',
                        'class' => 'form element'
        )))));

        $inside_info = 'Inside the "User Cover Photo and Information" widget.'
                . '<a href="https://lh6.googleusercontent.com/-jmu-UoWkjbc/UbXZg6D1NSI/AAAAAAAAAYQ/pECMvwdlhig/s1124/ss1.png" title="View Screenshot" class="buttonlink sm_icon_view mleft5" target="_blank"></a>';

        $outside_info = 'Outside the "User Cover Photo and Information" widget.'
                . '<a href="https://lh6.googleusercontent.com/-Ngm4dlS59LQ/UbXZhVBnbuI/AAAAAAAAAYU/OyIZJmScuPU/s1124/ss1b.png" title="View Screenshot" class="buttonlink sm_icon_view mleft5" target="_blank"></a>';

        $this->addElement('Radio', 'siteusercoverphoto_content_full_width', array(
            'label' => "Display Cover Photo in full width",
            'description' => "Do you want to show cover photo in full width?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettingsApi->getSetting('siteusercoverphoto.content.full.width', 0),
            'onclick' => 'covercontentFullWidth();'
        ));

        $this->addElement('radio', 'siteusercoverphoto_change_tab_position', array(
            'label' => 'Tab Placement',
            'description' => "Select the position of the tabs to be placed on Member Profile page. (Note: This setting will only work for the widget placed on Member Profile page.)",
            'multiOptions' => array(
                '1' => $inside_info,
                '0' => $outside_info,
            ),
            'value' => $coreSettingsApi->getSetting('siteusercoverphoto.change.tab.position', 1),
            'escape' => false,
        ));

        $this->addElement('radio', 'siteusercoverphoto_strach_main_photo', array(
            'label' => 'Consistent User Profile Picture Blocks',
            'description' => "Do you want user profile pictures to be displayed in consistent blocks of fixed dimension below the cover photo on your site?",
            'multiOptions' => array(
                '1' => 'Yes (Though the dimensions of the user profile picture block will be consistent, and the photos with unequal dimension will be shown in the center of the block.)',
                '0' => 'No (The dimension of the user profile picture block will not be fixed. In this case block’s dimensions will depend on the dimensions of user profile picture.)',
            ),
            'value' => $coreSettingsApi->getSetting('siteusercoverphoto.strach.main.photo', 1)
        ));

        $this->addElement('Hidden', 'is_remove_note', array('value' => 0, 'order' => 999));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
