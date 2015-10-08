<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitecontentcoverphoto',
        'version' => '4.8.9p2',
        'path' => 'application/modules/Sitecontentcoverphoto',
        'title' => 'Content Profiles - Cover Photo, Banner & Site Branding Plugin',
        'description' => 'Content Profiles - Cover Photo, Banner & Site Branding Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Sitecontentcoverphoto/settings/install.php',
            'class' => 'Sitecontentcoverphoto_Installer',
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
            0 => 'application/modules/Sitecontentcoverphoto',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitecontentcoverphoto.csv',
        ),
    ),
    'sitemobile_compatible' => true,
    //Hooks ---------------------------------------------------------------------
    'hooks' => array(

        array(
            'event' => 'onSitereviewListingtypeDeleteBefore',
            'resource' => 'Sitecontentcoverphoto_Plugin_Core',
        ),
        
        array(
            'event' => 'onSitereviewListingtypeCreateAfter',
            'resource' => 'Sitecontentcoverphoto_Plugin_Core',
        ),
    ),
    'routes' => array(
        'sitecontentcoverphoto_profilepage' => array(
            'route' => 'content/profilepage/:action/*',
            'defaults' => array(
                'module' => 'sitecontentcoverphoto',
                'controller' => 'profile',
            ),
            'reqs' => array(
                'action' => '(get-cover-photo|get-main-photo|reset-position-cover-photo|upload-cover-photo|get-albums-photos|remove-cover-photo|edit-font-color)'
            ),
        ),
        'sitecontentcoverphoto_profilepagemobile' => array(
            'route' => 'content/profilepage/:action/*',
            'defaults' => array(
                'module' => 'sitecontentcoverphoto',
                'controller' => 'profile',
            ),
            'reqs' => array(
                'action' => '(upload-cover-photo|get-albums-photos|remove-cover-photo)'
            ),
        ),
    ),
    'items' => array(
        'sitecontentcoverphoto_modules'
    )
);
?>