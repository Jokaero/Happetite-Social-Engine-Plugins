<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.Seaocores.com/license/
 * @version    $Id: Core.php 2010-11-18 9:40:21Z Seaocores $
 * @author     Seaocores
 */
class Seaocore_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

        $lightbox_type = $request->getParam('lightbox_type', null);
        if (!empty($lightbox_type) && $lightbox_type == 'photo') {
            $module_name = $request->getModuleName();
            $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
            $request->setModuleName('seaocore');
            $request->setControllerName('photo');
            $request->setActionName('view');
            if ($module_name == 'sitealbum') {
                $module_name = 'album';
            }
            $request->setParam("module_name", $module_name);
            $request->setParam("tab", $tab_id);
        } else if (substr($request->getPathInfo(), 1, 5) == "admin") {
            $requestURL = str_replace("www.", "", strtolower($_SERVER['HTTP_HOST']));
            $unpackValuesArray = @unpack('H*', $requestURL);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('core_adminmenutype', $unpackValuesArray[1]);
        }
        
        //Start work for responsive theme/media query
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $theme = '';
        $themeArray = $view->layout()->themes;
        if (isset($themeArray[0])) {
            $theme = $view->layout()->themes[0];
        }

        if ($theme == 'shoppinghub' || $theme == 'clear' || $theme == 'demotheme' || $theme == 'luminous' || $theme == 'spectacular') {
            $view->headMeta()->setName('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
        }
        //End work for responsive theme/media query
    }

    public function onRenderLayoutDefault($event) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
				
				$view->headLink()->appendStylesheet('application/modules/Seaocore/externals/styles/fonts.css');
        
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu'))
            $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
                    . 'application/modules/Sitemenu/externals/styles/style_sitemenu.css');
         
        if (!Engine_Api::_()->seaocore()->hasAddedWidgetOnPage("header", "seaocore.seaocores-lightbox"))
            return;
        $view->headScript()
                ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
                ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
                ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
                ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');
        
        $view->headTranslate(array(
            'Save', 'Cancel', 'delete',
        ));
        
        $fixWindowEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sea.lightbox.fixedwindow', 1);
        if ($fixWindowEnable) {
            $view->headScript()
                    ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/tagger/tagger.js');
            $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/lightbox/fixWidthLightBox.js');
            $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
                    . 'application/modules/Seaocore/externals/styles/style_advanced_photolightbox.css');
        } else {
            $view->headScript()
                    ->appendFile($view->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
                    ->appendFile($view->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
                    ->appendFile($view->layout()->staticBaseUrl . 'externals/tagger/tagger.js')
                    ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/lightBox.js');
            $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
                    . 'application/modules/Seaocore/externals/styles/style_photolightbox.css');
        }
    }

    public function onCoreCommentDeleteBefore($event) {
        $payload = $event->getPayload();
        if (isset($payload->parent_comment_id)) {
            if ((isset($payload->resource_type) && !empty($payload->resource_type)) && (isset($payload->resource_id) && !empty($payload->resource_id))) {
                if (Engine_Api::_()->hasItemType($payload->resource_type) && Engine_Api::_()->getItem($payload->resource_type, $payload->resource_id)) {
                    $resource = Engine_Api::_()->getItem($payload->resource_type, $payload->resource_id);
                    if($resource->getType() != 'core_link') {
                        $replyTable = Engine_Api::_()->getDbtable('comments', 'core');
                        $replySelect = $replyTable->select()
                                ->where('parent_comment_id = ?', $payload->getIdentity());
                        foreach ($replyTable->fetchAll($replySelect) as $reply) {
                            $resource->comments()->removeComment($reply->comment_id);
                        }
                    }
                }
            }
        }
    }

    public function onActivityCommentDeleteBefore($event) {
        $payload = $event->getPayload();
        if (isset($payload->parent_comment_id)) {
            if ((isset($payload->comment_id) && !empty($payload->comment_id))) {
                $replyTable = Engine_Api::_()->getDbtable('comments', 'activity');
                $replySelect = $replyTable->select()
                        ->where('parent_comment_id = ?', $payload->comment_id);
                foreach ($replyTable->fetchAll($replySelect) as $reply) {
                    if (Engine_Api::_()->hasItemType('activity_comment') && Engine_Api::_()->getItem('activity_comment', $reply->comment_id)) {
                        $resource = Engine_Api::_()->getItem('activity_comment', $reply->comment_id);
                        $resource->delete();
                    }
                }
            }
        }
    }

}