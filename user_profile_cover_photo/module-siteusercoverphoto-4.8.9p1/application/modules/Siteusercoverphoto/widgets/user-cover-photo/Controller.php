<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Widget_UserCoverPhotoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject('user')) {
            $this->view->user = $user = $viewer;
        } else {
            $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
        }

        if (!$this->view->user->getIdentity())
            return $this->setNoRender();

        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        $this->view->sitememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember');
        $this->view->siteverifyEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify');
        if ($this->view->sitememberEnabled && isset($user->user_id)) {
            $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
            $this->view->featured = $tableUserInfo->getColumnValue($this->view->user->user_id, 'featured');
            $this->view->sponsored = $tableUserInfo->getColumnValue($this->view->user->user_id, 'sponsored');
        }

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advalbum')) {
            return $this->setNoRender();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.elementinfo', false)) {
            return $this->setNoRender();
        }

        $this->view->photo = '';
        if (isset($user->user_cover)) {
            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                $this->view->photo = $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
            } else {
                $this->view->photo = $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
            }
        }
        $this->view->columnHeight = $this->_getParam('columnHeight', '300');
        $this->view->cover_photo_preview = 0;
        $this->view->level_id = 0;

        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->cover_photo_preview = $cover_photo_preview = isset($_GET['cover_photo_preview']) ? $_GET['cover_photo_preview'] : 0;
            $this->view->level_id = $level_id = isset($_GET['level_id']) ? $_GET['level_id'] : 0;
        }

        $this->view->showContent = $showContent = $this->_getParam('showContent', array("mainPhoto", "title", "updateInfoButton", "settingsButton", "optionsButton", "friendShipButton", "composeMessageButton", "featured", "sponsored", "verify"));
        if (($p['module'] == 'user' && $p['controller'] == 'index' && $p['action'] == 'home') || ($p['module'] == 'user' && $p['controller'] == 'profile' && $p['action'] == 'index')) {
            if (($p['module'] == 'user' && $p['controller'] == 'index' && $p['action'] == 'home')) {
                $this->view->change_tab_position = 0;
            } else {
                $this->view->change_tab_position = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.change.tab.position', 1);
            }
        } else {
            if ($showContent) {
                $this->view->showContent = $showContent = array_diff($showContent, array("updateInfoButton", "settingsButton", "optionsButton", "friendShipButton", "composeMessageButton"));
            }
            $this->view->change_tab_position = 0;
        }

        $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
        if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
            $this->view->can_edit = 1;
        } else {
            $this->view->can_edit = 0;
        }

        $this->view->cover_params = array('top' => 0, 'left' => 0);
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteusercoverphoto/View/Helper', 'Siteusercoverphoto_View_Helper');
        $siteuserCoverPhotoInfo = Zend_Registry::isRegistered('siteuserCoverPhotoInfo') ? Zend_Registry::get('siteuserCoverPhotoInfo') : null;
        //IF FACEBOOK PLUGIN IS THERE THEN WE WILL SHOW DEFAULT FACEBOOK LIKE BUTTON.
        $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        $default_like = 1;
        if (!empty($fbmodule) && !empty($fbmodule->enabled)) {
            $default_like = 2;
        }
        $this->view->profile_like_button = $this->_getParam('profile_like_button', $default_like);

        $this->view->noProfilePhoto = 0;
        if ($this->view->change_tab_position && !empty($showContent) && !in_array('mainPhoto', $showContent)) {
            $this->view->noProfilePhoto = 1;
        }

        if (empty($siteuserCoverPhotoInfo))
            return $this->setNoRender();

        $this->view->editFontColor = $this->_getParam('editFontColor', 0);

        $this->view->contentFullWidth = $contentFullWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.content.full.width', 0);
    }

}
