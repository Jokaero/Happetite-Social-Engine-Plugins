<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' => array(
        'type' => 'module',
        'name' => 'seaocore',
        'version' => '4.8.9p14',
        'path' => 'application/modules/Seaocore',
        'repository' => 'null',
        'title' => 'SocialEngineAddOns Core Plugin',
        'description' => 'SocialEngineAddOns Core Plugin',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thu, 18 Nov 2010 18:33:08 +0000',
        'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Seaocore/settings/install.php',
            'class' => 'Seaocore_Installer',
        ),
        'directories' => array(
            'application/modules/Seaocore',
	    'externals/tinymce/plugins/jbimages'	
        ),
        'files' => array(
            'application/languages/en/seaocore.csv',
            'application/libraries/PEAR/Services/Twitter.php',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'seaocore',
        'seaocore_tab',
        'seaocore_locationitems',
        'seaocore_reply',
        'seaocore_follow',
        'seaocore_locationcontent'
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            // 'event' => 'addActivity',
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Seaocore_Plugin_Core'
        ),
//         array(
//             'event' => 'onItemDeleteBefore',
//             'resource' => 'Seaocore_Plugin_Core',
//         ),
//         array(
//             'event' => 'onCoreCommentCreateAfter',
//             'resource' => 'Seaocore_Plugin_Core',
//         ),
        array(
            'event' => 'onCoreCommentDeleteBefore',
            'resource' => 'Seaocore_Plugin_Core',
        ),
        array(
            'event' => 'onActivityCommentDeleteBefore',
            'resource' => 'Seaocore_Plugin_Core',
    ),
    ),
    'routes' => array(
			'seaocore_image_specific' => array(
					'route' => 'seaocore/photo/view/*',
					'defaults' => array(
							'module' => 'seaocore',
							'controller' => 'photo',
							'action' => 'view'
					),
					'reqs' => array(
							'action' => '(view)',
					),
			),
			'seaocore_viewmap' => array(
					'route' => 'seaocore/index/view-map/:id/*',
					'defaults' => array(
							'module' => 'seaocore',
							'controller' => 'index',
							'action' => 'view-map'
					)
			),
			'seaocore_like' => array(
				'route' => 'seaocore/like/:action/*',
				'defaults' => array(
						'module' => 'seaocore',
						'controller' => 'like',
						//'action' => 'index',
				),
				'reqs' => array(
						'action' => '(global-likes)',
				),
			),
      'seaocore_resend_invite' => array(
          'route' => 'seaocore/invite/resendinvite',
          'defaults' => array(
              'module' => 'seaocore',
              'controller' => 'invite',
              'action' => 'resendinvite'
          )
      ),
        
        'seaocore_fb_invite' => array(
          'route' => '/invite-code/:code/:id/:type/*',
          'defaults' => array(
              'module' => 'seaocore',
              'controller' => 'auth',
              'action' => 'invite-code',
              'type' => '',
              'id' => 0
          )
      ),
			'seaocore_integrated_module' => array(
				'route' => "admin/seaocore/integrated/index/:moduletype/*",
				'defaults' => array(
					'module' => 'seaocore',
					'controller' => 'admin-integrated',
					'action' => 'index',
					'moduletype' => 'seaocore',
				),
				'reqs' => array(
							'moduletype' => 'seaocore',
					),
			),
      'seaocore_insta_auth_url' => array(
        'route' => '/instagram/auth/instagram/',
        'defaults' => array(
            'module' => 'seaocore',
            'controller' => 'auth',
            'action' => 'instagram',
        )
    ),
    )
);
?>
