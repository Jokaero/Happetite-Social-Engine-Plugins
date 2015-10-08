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
        'description' => 'Displays the content cover photo with content profile photo and information. You can choose various options from the Edit Settings of this widget.',
        'category' => 'Content Cover Photo',
        'type' => 'widget',
        'name' => 'sitecontentcoverphoto.content-cover-photo',
        'autoEdit' => true,
        'adminForm' => 'Sitecontentcoverphoto_Form_Admin_Widget_Content'
    ),
);