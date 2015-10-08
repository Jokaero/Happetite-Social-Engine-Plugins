<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Widget_ContentCoverMobilePhotoController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    $this->_mobileAppFile = true;
    $this->view->user = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    if (Engine_Api::_()->core()->hasSubject('user') || !Engine_Api::_()->core()->getSubject()) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $resource_type = $subject->getType();

    if(isset($subject->listingtype_id)) {
			$params = array('resource_type' => $resource_type. '_' . $subject->listingtype_id);
			$this->view->showContent = $showContent = $this->_getParam("showContent_" . $resource_type. '_' . $subject->listingtype_id);
    }
    else {
		 $params = array('resource_type' => $resource_type);
			$this->view->showContent = $showContent = $this->_getParam("showContent_" . $resource_type);
    }

    if (!Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule($params))
      return $this->setNoRender();
    
    $sitecontentcoverphoto_photo_type = Zend_Registry::isRegistered('sitecontentcoverphoto_photo_type') ? Zend_Registry::get('sitecontentcoverphoto_photo_type') : null;
    if( empty($sitecontentcoverphoto_photo_type) )
        return $this->setNoRender();
   
    if (empty($showContent))
      $this->view->showContent = array();
    $front = Zend_Controller_Front::getInstance()->getRequest();
    $p = $front->getParams();
    $this->view->photo = '';
    $primaryTableKey = Engine_Api::_()->getItemtable($resource_type)->info('primary');

    $this->view->tablePrimaryFieldName = $tablePrimaryFieldName = $primaryTableKey[1];
    $this->view->fieldName = $fieldName = strtolower($subject->getShortType()) . '_cover';
    $this->view->moduleName = $moduleName = $front->getModuleName();
    if ($moduleName == 'sitealbum') {
        $this->view->moduleName = $moduleName = 'album';
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    if($resource_type != 'sitereview_listing') {
        $tableName = Engine_Api::_()->getItemtable($resource_type)->info('name');
        $field = $db->query("SHOW COLUMNS FROM $tableName LIKE '$fieldName'")->fetch();
        if (empty($field)) {
          $db->query("ALTER TABLE `$tableName` ADD `$fieldName` INT( 11 ) NOT NULL DEFAULT '0'");
        }

        if (isset($subject->$fieldName)) {
          $this->view->photo = $photo = Engine_Api::_()->getItem($moduleName. "_photo", $subject->$fieldName);
        }

    } else {
        $tableName = 'engine4_sitereview_otherinfo';
        $field = $db->query("SHOW COLUMNS FROM $tableName LIKE '$fieldName'")->fetch();
        if (empty($field)) {
          $db->query("ALTER TABLE `$tableName` ADD `$fieldName` INT( 11 ) NOT NULL DEFAULT '0'");
        }
        $fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($subject->listing_id, $fieldName);
        if ($fieldNameValue) {
          $this->view->photo = $photo = Engine_Api::_()->getItem($moduleName. "_photo", $fieldNameValue);
        }
    }

    $this->view->columnHeight = $this->_getParam('columnHeight', '300');
		$this->view->sitecontentcoverphotoStrachMainPhoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecontentcoverphotoStrachMainPhoto', 1);
    $this->view->showMemberLevelBasedPhoto = $this->_getParam('showMemberLevelBasedPhoto', 1);
    $this->view->showMember = $showMember = $this->_getParam('showMember', 0);
		$this->view->memberCount = $membersCount = $this->_getParam('memberCount', 8);
    $this->view->onlyMemberWithPhoto = $this->_getParam('onlyMemberWithPhoto', 1);
    $this->view->sitecontentcoverphotoChangeTabPosition = $this->_getParam('sitecontentcoverphotoChangeTabPosition', 0);
    $this->view->showContactDetails = $this->_getParam('showContactDetails', 1);
    $this->view->cover_photo_preview = $cover_photo_preview = isset($_GET['cover_photo_preview']) ? $_GET['cover_photo_preview'] : 0;
    $this->view->level_id = 0;
		$this->view->contacts = $contacts = $this->_getParam('contacts', array("0" => "1", "1" => "2", "2" => "3"));
		$this->view->emailme = $this->_getParam('emailme', 1);

		//INITIALIZATION
		$this->view->show_phone = $this->view->show_email = $this->view->show_website = 0;
		if($contacts && in_array(1, $contacts)) {
			$this->view->show_phone = 1;
		}
		if($contacts && in_array(2, $contacts)) {
			$this->view->show_email = 1;
		}
		if($contacts && in_array(3, $contacts)) {
			$this->view->show_website = 1;
		}
		$this->view->level_id = $level_id = $subject->getOwner()->level_id;

    if ($cover_photo_preview) {
      $this->view->level_id = $level_id = isset($_GET['level_id']) ? $_GET['level_id'] : $level_id;
    }

    $this->view->change_tab_position = 0;

    $this->view->can_edit = $can_edit = 0;
    $this->view->showMemberLevelBasedPhoto = $this->_getParam('showMemberLevelBasedPhoto', 1);
    //START MANAGE-ADMIN CHECK
    if (Engine_Api::_()->sitecontentcoverphoto()->checkConditionsForAlbum($moduleName)) {
      $this->view->can_edit = $can_edit = $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');
      if (empty($isManageAdmin)) {
        $this->view->can_edit = $can_edit = 0;
      }
    } else {
      if ($moduleName == 'sitereview') {
        $this->view->can_edit = $can_edit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");
      } else {
        $this->view->can_edit = $can_edit = $subject->authorization()->isAllowed($viewer, 'edit');
      }
    }

    if ($can_edit && Engine_Api::_()->sitecontentcoverphoto()->getUploadPermission($subject, $viewer)) {
      $this->view->can_edit = $can_edit = 1;
    } else {
      $this->view->can_edit = $can_edit = 0;
    }

    if (($subject->getType() === 'sitepage_page') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate', 1))) {
      $this->view->can_edit = $can_edit = 0;
    } else if (($subject->getType() === 'sitebusiness_business') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessalbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusinessalbum.isActivate', 1))) {
      $this->view->can_edit = $can_edit = 0;
    }
    $this->view->cover_params = array('top' => 0, 'left' => 0);
    //IF FACEBOOK PLUGIN IS THERE THEN WE WILL SHOW DEFAULT FACEBOOK LIKE BUTTON.
    $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
    $default_like = 1;
    if (!empty($fbmodule) && !empty($fbmodule->enabled)) {
      $default_like = 2;
    }
    $this->view->profile_like_button = $this->_getParam('profile_like_button');
    
    
    $this->view->noProfilePhoto = 0;
    if ($this->view->sitecontentcoverphotoChangeTabPosition && !empty($showContent) && !in_array('mainPhoto', $showContent)) {
      $this->view->noProfilePhoto = 1;
    }
    $this->view->membersCount =0;
    if($moduleName == 'sitepage' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			if ($showMember) {
				$this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($subject->page_id, null,null,$this->_getParam("onlyMemberWithPhoto", 1));
				$this->view->membersCount = $members->getTotalItemCount();
			}
    } elseif($moduleName == 'sitebusiness' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) {
			if ($showMember) {
				$this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitebusiness')->getJoinMembers($subject->business_id, null,null,$this->_getParam("onlyMemberWithPhoto", 1));
				$this->view->membersCount = $members->getTotalItemCount();
			}
    } elseif($moduleName == 'sitegroup' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			if ($showMember) {
				$this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($subject->group_id, null,null,$this->_getParam("onlyMemberWithPhoto", 1));
        $this->view->membersCount = $members->getTotalItemCount();
			}
    }

    //PARAMS
    $this->view->can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.share', 1);
    $this->view->showMessageOwner = Engine_Api::_()->authorization()->getPermission($level_id, 'messages', 'auth');
  }

}
