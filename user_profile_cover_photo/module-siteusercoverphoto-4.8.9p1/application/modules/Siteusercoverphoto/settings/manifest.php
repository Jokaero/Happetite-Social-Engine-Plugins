<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteusercoverphoto',
        'version' => '4.8.9p1',
        'path' => 'application/modules/Siteusercoverphoto',
        'title' => 'User Profiles - Cover Photo, Banner & Site Branding Plugin',
        'description' => 'User Profiles - Cover Photo, Banner & Site Branding Plugin',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' => array(
            'path' => 'application/modules/Siteusercoverphoto/settings/install.php',
            'class' => 'Siteusercoverphoto_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Siteusercoverphoto',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/siteusercoverphoto.csv',
        ),
    ),
    'routes' => array(
        'siteusercoverphoto_profilepage' => array(
            'route' => 'user/profilepage/:action/*',
            'defaults' => array(
                'module' => 'siteusercoverphoto',
                'controller' => 'profile',
            ),
            'reqs' => array(
                'action' => '(get-cover-photo|get-main-photo|reset-position-cover-photo|upload-cover-photo|get-albums-photos|remove-cover-photo|web-cam-image|edit-font-color)'
            ),
        ),
        'siteusercoverphoto_profilepagemobile' => array(
            'route' => 'user/profilepage/:action/*',
            'defaults' => array(
                'module' => 'siteusercoverphoto',
                'controller' => 'profile',
            ),
            'reqs' => array(
                'action' => '(upload-cover-photo|get-albums-photos|remove-cover-photo)'
            ),
        ),
    ),
);
?>
