<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
        'title' => 'Content Cover Photo and Information',
        'description' => 'Displays the cover photo of a Content. From the Edit Settings section of this widget, you can also choose to display content member’s profile photos, if Content Admin has not selected a cover photo. It is recommended to place this widget on the Page Profile at the top.',
        'category' => 'Content Cover Photo',
        'type' => 'widget',
        'name' => 'sitecontentcoverphoto.content-cover-mobile-photo',
        'autoEdit' => true,
        'adminForm' => 'Sitecontentcoverphoto_Form_Admin_Widget_MobileContent'
    ),
);