<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobile_content.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$showContent_timeline = array("mainPhoto" => "Profile Picture", "title" => "User's Display Name", "friendShipButton" => "Friendship Setting Button", "composeMessageButton" => "Send Message Button", "customFields" => "Custom Fields");
$showContent_option = array("mainPhoto", "title", "friendShipButton", "composeMessageButton", "customFields");

return array(
    array(
        'title' => 'User Cover Photo, Information and Profile Fields',
        'description' => 'Displays the user cover photo with user profile photo and profile fields. You can choose various options from the Edit Settings of this widget.',
        'category' => 'User Cover Photo',
        'type' => 'widget',
        'name' => 'siteusercoverphoto.user-cover-mobile-photo',
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
                        'label' => 'Select the information options that you want to be available in this block. (Note: This setting will only work if this block is placed on Member Profile page and Member Home page.)',
                        'multiOptions' => $showContent_timeline,
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