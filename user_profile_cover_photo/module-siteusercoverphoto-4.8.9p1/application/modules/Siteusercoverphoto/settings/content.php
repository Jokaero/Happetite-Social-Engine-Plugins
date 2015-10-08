<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$showContent_timeline = array("mainPhoto" => "Profile Picture", "title" => "User's Display Name", "updateInfoButton" => "Update Information Button", "settingsButton" => "My Settings Button", "optionsButton" => "Options Button (Edit My Profile, Add Friend, Send Message, etc.)", "friendShipButton" => "Friendship Setting Button", "composeMessageButton" => "Send Message Button");

if (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitemember')) {
    $showContent_timeline = array_merge($showContent_timeline, array('featured' => 'Featured', 'sponsored' => 'Sponsored', 'rating' => "Rating"));
}

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify')) {
    $showContent_timeline = array_merge($showContent_timeline, array('verify' => 'Verify Icon'));
}

$showContent_option = array("mainPhoto", "title", "updateInfoButton", "settingsButton", "optionsButton", "friendShipButton", "composeMessageButton");

//CHECK IF FACEBOOK PLUGIN IS ENABLE
$fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');

if (!empty($fbmodule) && !empty($fbmodule->enabled)) {
    $show_like_button = array(
        '1' => 'Yes, show SocialEngine Core Like button',
        '2' => 'Yes, show Facebook Like button',
        '0' => 'No',
    );
    $default_value = 2;
} else {
    $show_like_button = array(
        '1' => 'Yes, show SocialEngine Core Like button',
        '0' => 'No',
    );
    $default_value = 1;
}

return array(
    array(
        'title' => 'User Cover Photo and Information',
        'description' => 'Displays the user cover photo with user profile photo and information. You can choose various options from the Edit Settings of this widget.',
        'category' => 'User Cover Photo',
        'type' => 'widget',
        'name' => 'siteusercoverphoto.user-cover-photo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => '',
            'showContent' => $showContent_option
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block. (Note: This setting will only work if this block is placed on Member Profile page and Member Home Page.)',
                        'multiOptions' => $showContent_timeline,
                    ),
                ),
                array(
                    'Radio',
                    'profile_like_button',
                    array(
                        'label' => 'Do you want to enable Like button in this block?',
                        'multiOptions' => $show_like_button,
                        'value' => $default_value,
                    ),
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
                        'value' => '300',
                    )
                ),
                array(
                    'Radio',
                    'editFontColor',
                    array(
                        'label' => "Do you want 'Edit Font Color' option to be available to the members for their cover photo?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'User Profile Fields',
        'description' => 'Displays a user\'s profile field data on their profile.',
        'category' => 'User Cover Photo',
        'type' => 'widget',
        'name' => 'siteusercoverphoto.user-profile-fields',
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'customFields',
                    array(
                        'label' => 'Enter the number of custom profile fields to be displayed in this block.',
                        'value' => '5',
                    )
                ),
            ),
        ),
    ),
);
