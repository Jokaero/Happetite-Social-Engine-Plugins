<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-15 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_BrowseMenuMainController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $coreApi = Engine_Api::_()
                ->getApi('menus', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->hasModuleBootstrap('sitemenu')) {
            $isCacheEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.cache.enable', true);
            if (!empty($isCacheEnabled)) {
                $cache = Zend_Registry::get('Zend_Cache');

                $viewer_id = $viewer->getIdentity();
                if (!empty($viewer_id)) {
                    $viewer_level_id = Engine_Api::_()->user()->getViewer()->level_id;
                } else {
                    $viewer_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
                }

                $cacheName = 'browse_main_menu_cache_level_' . $viewer_level_id;
                $data = $cache->load($cacheName);
                if (!empty($data)) {
                    $this->view->browsenavigation = $navigation = $data;
                } else {
                    $this->view->browsenavigation = $navigation = $data = $coreApi->getNavigation('core_main');
                    $cache->setLifetime(Engine_Api::_()->sitemenu()->cacheLifeInSec());
                    $cache->save($data, $cacheName);
                }
            } else {
                $this->view->browsenavigation = $navigation = $coreApi
                        ->getNavigation('core_main');
            }
        } else {
            $this->view->browsenavigation = $navigation = $coreApi
                    ->getNavigation('core_main');
        }

        $this->view->max = $this->_getParam('max', 20);

        $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
        if (!$require_check && !$viewer->getIdentity()) {
            $navigation->removePage($navigation->findOneBy('route', 'user_general'));
        }
    }

}
