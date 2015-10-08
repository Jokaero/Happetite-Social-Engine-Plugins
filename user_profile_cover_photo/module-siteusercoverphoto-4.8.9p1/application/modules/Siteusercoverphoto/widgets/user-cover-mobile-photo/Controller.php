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
class Siteusercoverphoto_Widget_UserCoverMobilePhotoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('user')) {
      $this->view->user = $user = $viewer;
    } else {
      $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
    }

    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advalbum')) {
      return $this->setNoRender();
    }

    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $this->view->cover_photo_preview = 0;
    if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
      $this->view->cover_photo_preview = $cover_photo_preview = isset($_GET['cover_photo_preview']) ? $_GET['cover_photo_preview'] : 0;
    }

    $this->view->showContent = $showContent = $this->_getParam('showContent', array("mainPhoto", "title", "friendShipButton", "composeMessageButton"));
    if (($p['module'] == 'user' && $p['controller'] == 'index' && $p['action'] == 'home') || ($p['module'] == 'user' && $p['controller'] == 'profile' && $p['action'] == 'index')) {
      if (($p['module'] == 'user' && $p['controller'] == 'index' && $p['action'] == 'home')) {
        $this->view->change_tab_position = 0;
      } else {
        $this->view->change_tab_position = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.change.tab.position', 1);
      }
    } else {
      if ($showContent) {
        $this->view->showContent = $showContent = array_diff($showContent, array("friendShipButton", "composeMessageButton"));
      }
      $this->view->change_tab_position = 0;
    }
    $this->view->photo = '';
    if(isset($user->user_cover)) {
			if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
				$this->view->photo = $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
			} else {
				$this->view->photo = $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
			}
    }
    $this->view->columnHeight = $this->_getParam('columnHeight', '300');
    $this->view->customFields = $this->_getParam('customFields', 5);
    $this->view->can_edit = $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
    if ($this->view->can_edit) {
      $this->view->can_edit = Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload');
    }
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteusercoverphoto/View/Helper', 'Siteusercoverphoto_View_Helper');
    $siteuserCoverPhotoMobile = Zend_Registry::isRegistered('siteuserCoverPhotoMobile') ? Zend_Registry::get('siteuserCoverPhotoMobile') : null;
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
    if (empty($siteuserCoverPhotoMobile))
      return $this->setNoRender();
  }

}