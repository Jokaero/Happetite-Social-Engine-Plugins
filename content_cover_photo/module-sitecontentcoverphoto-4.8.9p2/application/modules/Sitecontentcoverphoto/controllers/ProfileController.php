<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProfileController.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_ProfileController extends Core_Controller_Action_Standard {

    public function getCoverPhotoAction() {

        if (!Engine_Api::_()->core()->hasSubject())
            return;
        $this->view->editFontColor = $editFontColor = $this->_getParam("editFontColor");
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $special = $this->_getParam("special", "cover");
        $this->view->moduleName = $moduleName = $this->_getParam("moduleName");
        $this->view->fieldName = $fieldName = $this->_getParam("fieldName");
        $this->view->can_edit = $can_edit = 0;
        $this->view->contentFullWidth = $contentFullWidth = $this->_getParam('contentFullWidth');
        $this->view->sitecontentcoverphotoChangeTabPosition = $sitecontentcoverphotoChangeTabPosition = $this->_getParam("sitecontentcoverphotoChangeTabPosition");
        $primaryTableKey = Engine_Api::_()->getItemtable($subject->getType())->info('primary');
        $this->view->tablePrimaryFieldName = $tablePrimaryFieldName = $primaryTableKey[1];
        $db = Engine_Db_Table::getDefaultAdapter();
        if ($moduleName != 'album') {
            $tableName = Engine_Api::_()->getItemtable("$moduleName" . "_album")->info('name');
        } else {
            $tableName = Engine_Api::_()->getItemtable("album")->info('name');
        }
        $field = $db->query("SHOW COLUMNS FROM $tableName LIKE 'cover_params'")->fetch();
        if (empty($field)) {
            $db->query("ALTER TABLE `$tableName` ADD `cover_params` VARCHAR( 255 ) NULL");
        }
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

        $this->view->photo = $photo = '';

        if ($subject->getType() != 'sitereview_listing') {
            $this->view->photo = $photo = Engine_Api::_()->getItem("$moduleName" . "_photo", $subject->$fieldName);
        } else {

            $fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($subject->listing_id, $fieldName);
            if ($fieldNameValue) {
                $this->view->photo = $photo = Engine_Api::_()->getItem($moduleName . "_photo", $fieldNameValue);
            }
        }

        $this->view->coverTop = 0;
        $this->view->coverLeft = 0;
        $this->view->cover_params = array('top' => 0, 'left' => 0, 'fontcolor' => '#FFFFFF');
        $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
        $this->view->level_id = $level_id = $subject->getOwner()->level_id;

        if (!empty($cover_photo_preview)) {
            $this->view->level_id = $level_id = $this->_getParam("level_id", $level_id);
        }

        if ($photo && empty($cover_photo_preview)) {

            if ($moduleName != 'album') {
                $album = Engine_Api::_()->getItem("$moduleName" . "_album", $photo->album_id);
            } else {
                $album = Engine_Api::_()->getItem("album", $photo->album_id);
            }

            if ($album && $album->cover_params && is_array($album->cover_params) && isset($album->cover_params['top'])) {
                $this->view->coverTop = $album->cover_params['top'];
            } else if (!is_array($album->cover_params) && $album->cover_params) {
                $decodedArray = Zend_Json_Decoder::decode($album->cover_params);
                $this->view->coverTop = $decodedArray['top'];
            }
        } else {
            $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF'));
            $decoded_cover_param = Zend_Json_Decoder::decode(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, $postionParams));
            $this->view->coverTop = $decoded_cover_param['top'];
        }
        $this->view->showMember = $showMember = $this->_getParam("showMember", 0);
        $this->view->membersCountView = $this->_getParam("memberCount", 8);

        $this->view->showMemberLevelBasedPhoto = $this->_getParam('showMemberLevelBasedPhoto', 1);

        if ($moduleName == 'sitepage' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            if ($showMember) {
                $this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($subject->page_id, null, null, $this->_getParam("onlyMemberWithPhoto", 1));
                $this->view->membersCount = $members->getTotalItemCount();
            }
        } elseif ($moduleName == 'sitebusiness' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) {
            if ($showMember) {
                $this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitebusiness')->getJoinMembers($subject->business_id, null, null, $this->_getParam("onlyMemberWithPhoto", 1));
                $this->view->membersCount = $members->getTotalItemCount();
            }
        } elseif ($moduleName == 'sitegroup' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            if ($showMember) {
                $this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($subject->group_id, null, null, $this->_getParam("onlyMemberWithPhoto", 1));
                $this->view->membersCount = $members->getTotalItemCount();
            }
        }
    }

    public function getMainPhotoAction() {

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->moduleName = $moduleName = $this->_getParam("moduleName");
        $this->view->fieldName = $fieldName = $this->_getParam("fieldName");
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->showContent = $this->_getParam("showContent");
        //if occurrence id is coming then set it in zend registry
        Zend_Registry::set('occurrence_id', $this->_getParam("occurrence_id", ''));
        $primaryTableKey = Engine_Api::_()->getItemtable($subject->getType())->info('primary');
        $this->view->tablePrimaryFieldName = $tablePrimaryFieldName = $primaryTableKey[1];
        $this->view->editFontColor = $editFontColor = $this->_getParam("editFontColor");
        $this->view->sitecontentcoverphotoStrachMainPhoto = $this->_getParam("sitecontentcoverphotoStrachMainPhoto");
        $this->view->emailme = $this->_getParam('emailme');
        $this->view->show_phone = $this->_getParam('show_phone');
        $this->view->level_id = $level_id = $this->_getParam("level_id", $subject->getOwner()->level_id);
        $this->view->photo = $photo = '';

        $this->view->contentFullWidth = $contentFullWidth = $this->_getParam('contentFullWidth');
        if ($subject->getType() != 'sitereview_listing') {
            $this->view->photo = $photo = Engine_Api::_()->getItem("$moduleName" . "_photo", $subject->$fieldName);
        } else {

            $fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($subject->listing_id, $fieldName);
            if ($fieldNameValue) {
                $this->view->photo = $photo = Engine_Api::_()->getItem($moduleName . "_photo", $fieldNameValue);
            }
        }

        $this->view->fontcolor = '';

        $this->view->sitecontentcoverphotoChangeTabPosition = $sitecontentcoverphotoChangeTabPosition = $this->_getParam("sitecontentcoverphotoChangeTabPosition");
        if ($sitecontentcoverphotoChangeTabPosition) {
            $this->view->fontcolor = '#FFFFFF';
            if ($photo) {

                $album = Engine_Api::_()->sitecontentcoverphoto()->getSpecialAlbum($subject, 'cover', $moduleName);
                $this->view->fontcolor = '#FFFFFF';
                if (!is_array($album->cover_params) && $album->cover_params) {
                    $this->view->fontcolor = '#FFFFFF';
                    $decodecolor = Zend_Json_Decoder::decode($album->cover_params);
                    if (isset($decodecolor['fontcolor']))
                        $this->view->fontcolor = $decodecolor['fontcolor'];
                } elseif (is_array($album->cover_params) && isset($album->cover_params['fontcolor']) && $album->cover_params['fontcolor']) {
                    $this->view->fontcolor = $album->cover_params['fontcolor'];
                }
            } else {
                $decoded_cover_param = Zend_Json_Decoder::decode(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF'))));
                if (isset($decoded_cover_param['fontcolor']))
                    $this->view->fontcolor = $decoded_cover_param['fontcolor'];
            }
        }

        $this->view->show_email = $this->_getParam('show_email');
        $this->view->show_website = $this->_getParam('show_website');
        if (empty($this->view->showContent)) {
            $this->view->showContent = array();
        }
        $this->view->profile_like_button = $this->_getParam('profile_like_button');

        $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
        $this->view->level_id = $level_id = $subject->getOwner()->level_id;

        $this->view->can_edit = $can_edit = 0;
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

        if (($subject->getType() === 'sitepage_page') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate', 1))) {
            $this->view->can_edit = $can_edit = 0;
        } else if (($subject->getType() === 'sitebusiness_business') && (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessalbum') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusinessalbum.isActivate', 1))) {
            $this->view->can_edit = $can_edit = 0;
        }

        $onlyUserWithPhoto = $this->_getParam("onlyUserWithPhoto", 1);
    }

    public function resetPositionCoverPhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->moduleName = $moduleName = $this->_getParam("moduleName");
        $this->view->fieldName = $fieldName = $this->_getParam("fieldName");
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->cover_photo_preview = $cover_photo_preview = 0;
        $this->view->level_id = $level_id = $subject->getOwner()->level_id;
        $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
        if ($cover_photo_preview) {
            $this->view->level_id = $level_id = $this->_getParam("level_id", $level_id);
        }
        if (!$cover_photo_preview) {
            $this->view->can_edit = $can_edit = 0;
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

            if (empty($can_edit))
                return;

            $this->view->photo = $photo = '';

            if ($subject->getType() != 'sitereview_listing') {
                $this->view->photo = $photo = Engine_Api::_()->getItem("$moduleName" . "_photo", $subject->$fieldName);
            } else {

                $fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($subject->listing_id, $fieldName);
                if ($fieldNameValue) {
                    $this->view->photo = $photo = Engine_Api::_()->getItem($moduleName . "_photo", $fieldNameValue);
                }
            }
            if ($photo && empty($cover_photo_preview)) {

                if ($moduleName != 'album') {
                    $album = Engine_Api::_()->getItem("$moduleName" . "_album", $photo->album_id);
                } else {
                    $album = Engine_Api::_()->getItem("album", $photo->album_id);
                }


                if (is_array($album->cover_params) && !isset($album->cover_params['fontcolor'])) {
                    $fontcolor = '#FFFFFF';
                } elseif (is_array($album->cover_params) && isset($album->cover_params['fontcolor'])) {
                    $fontcolor = $album->cover_params['fontcolor'];
                } elseif (!is_array($album->cover_params) && $album->cover_params) {
                    $decodeParams = Zend_Json_Decoder::decode($album->cover_params);

                    if (!isset($decodeParams['fontcolor'])) {
                        $fontcolor = '#FFFFFF';
                    } else {
                        $fontcolor = $decodeParams['fontcolor'];
                    }
                } else {
                    $fontcolor = $album->cover_params['fontcolor'];
                }
                $coverParams = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
                $coverParams = Zend_Json_Decoder::decode($coverParams);
                $coverParams['fontcolor'] = $fontcolor;
                $coverParams = Zend_Json_Encoder::encode($coverParams);
                $album->cover_params = $coverParams;
                $album->save();
            }
        } else {
            $defaultCover = $this->_getParam('defaultCover', 0);
            $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
            $defaultParams = array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF');
            if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF'))) {
                $defaultParams = Zend_Json_Decoder::decode(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF')));
                if (isset($defaultParams) && $defaultParams['fontcolor']) {
                    $fontcolor = $defaultParams['fontcolor'];
                } else {
                    $fontcolor = '#FFFFFF';
                }
            }

            if (!empty($defaultCover)) {
                $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                foreach ($level_ids as $key => $value) {
                    Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsParams($subject, $moduleName, $key, Zend_Json_Encoder::encode($defaultParams));
                }
            } else {
                $postionParams = $this->_getParam('position', array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF'));
                $defaultParams['top'] = $postionParams['top'];
                $defaultParams['left'] = $postionParams['left'];
                $defaultParams['fontcolor'] = $fontcolor;
                Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, Zend_Json_Encoder::encode($defaultParams));
            }
        }
    }

    public function getAlbumsPhotosAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $contentCoverPhotoDisplayable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecontentcoverphoto.displayable', null);
        $contentPhoto_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecontentcoverphoto.photo.type', null);
        $photoSettings = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $this->view->moduleName = $moduleName = $this->_getParam("moduleName");
        $this->view->fieldName = $fieldName = $this->_getParam("fieldName");
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->special = $special = $this->_getParam('special', 'cover');
        $primaryTableKey = Engine_Api::_()->getItemtable($subject->getType())->info('primary');
        $tablePrimaryFieldName = $primaryTableKey[1];
        $photoSettings = !empty($photoSettings) ? @base64_encode($photoSettings) : null;

        $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
        $this->view->album_id = $album_id = $this->_getParam("album_id");

        if (empty($contentCoverPhotoDisplayable) && ($photoSettings != $contentPhoto_type))
            return;

        if ($album_id) {

            if ($moduleName != 'album') {
                $this->view->album = $album = Engine_Api::_()->getItem($moduleName . '_album', $album_id);
                $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
                $paginator->setItemCountPerPage(10000);
            } else {
                $this->view->album = $album = Engine_Api::_()->getItem('album', $album_id);
                $photoTable = Engine_Api::_()->getItemTable('album_photo');
                $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
                    'album' => $album,
                ));
                $paginator->setItemCountPerPage(10000);
            }
        } elseif ($recentAdded) {
            if (Engine_Api::_()->sitecontentcoverphoto()->checkConditionsForAlbum($moduleName)) {
                $paginator = Engine_Api::_()->getItemTable($moduleName . '_photo')->getPhotos(array($tablePrimaryFieldName => $subject->getIdentity(), 'orderby' => 'photo_id DESC'));
            } else {

                if ($moduleName != 'album') {
                    $this->view->album = $album = $subject->getSingletonAlbum();
                    $paginator = $album->getCollectiblesPaginator();
                } else {

                    $this->view->album = $album = $subject;
                    $photoTable = Engine_Api::_()->getItemTable('album_photo');
                    $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
                        'album' => $album,
                    ));
                    $paginator->setItemCountPerPage(10000);
                }
            }
        } else {
            $paramsAlbum[$tablePrimaryFieldName] = $subject->getIdentity();
            if ($moduleName != 'album') {
                $paginator = Engine_Api::_()->getItemTable($moduleName . '_album')->getAlbums($paramsAlbum);
            } else {
                $this->view->album = $album = Engine_Api::_()->getItem('album', $album_id);
                $photoTable = Engine_Api::_()->getItemTable('album_photo');
                $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
                    'album' => $album,
                ));
                $paginator->setItemCountPerPage(10000);
            }
        }

        if (empty($contentCoverPhotoDisplayable) && ($photoSettings != $contentPhoto_type)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitecontentcoverphoto.view.type', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitecontentcoverphoto.photo.setting', 0);
        }

        $this->view->paginator = $paginator;
    }

    public function uploadCoverPhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->setLayout('default-simple');
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->moduleName = $moduleName = $this->_getParam("moduleName");
        $this->view->fieldName = $fieldName = $this->_getParam("fieldName");
        $primaryTableKey = Engine_Api::_()->getItemtable($subject->getType())->info('primary');
        $photoSettings = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $tablePrimaryFieldName = $primaryTableKey[1];

        $coverphotoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecontentcoverphoto.lsettings', null);
        $coverphotoGetType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecontentcoverphoto.get.type', null);
        $contentCoverPhotoDisplayable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecontentcoverphoto.displayable', null);
        $this->view->cover_photo_preview = $cover_photo_preview = 0;
        $this->view->can_edit = $can_edit = 0;
        $getPhotoSettingsStr = @md5($photoSettings . $coverphotoSettings);
        //START MANAGE-ADMIN CHECK
        if (Engine_Api::_()->sitecontentcoverphoto()->checkConditionsForAlbum($moduleName)) {
            $this->view->can_edit = $can_edit = $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');
            if (empty($isManageAdmin)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        } else {
            if ($moduleName == 'sitereview') {
                $this->view->can_edit = $can_edit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");
            } else {
                $this->view->can_edit = $can_edit = $subject->authorization()->isAllowed($viewer, 'edit');
            }
        }

        if (empty($contentCoverPhotoDisplayable) && ($getPhotoSettingsStr != $coverphotoGetType))
            return $this->_forward('requireauth', 'error', 'core');

        $this->view->special = $special = $this->_getParam('special', 'cover');
        $this->view->level_id = $level_id = $subject->getOwner()->level_id;
        $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
        if ($cover_photo_preview) {
            $this->view->level_id = $level_id = $this->_getParam("level_id", $level_id);
        }

        if (empty($contentCoverPhotoDisplayable) && ($getPhotoSettingsStr != $coverphotoGetType)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitecontentcoverphoto.view.type', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitecontentcoverphoto.photo.setting', 0);
        }

        //GET FORM
        if ($special == 'cover') {
            if (!$cover_photo_preview) {
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

                if (!$can_edit) {
                    return $this->_forward('requireauth', 'error', 'core');
                }


                $this->view->form = $form = new Sitecontentcoverphoto_Form_Photo_Cover();
            } else {
                $this->view->form = $form = new Sitecontentcoverphoto_Form_Photo_DefaultCover();
            }
        } else {
            $this->view->form = $form = new Sitecontentcoverphoto_Form_Photo_Main();
        }

        //CHECK FORM VALIDATION

        if (empty($cover_photo_preview)) {
            $file = '';
            $notNeedToCreate = false;
            $photo_id = $this->_getParam('photo_id');

            if ($photo_id) {
                $photo = Engine_Api::_()->getItem("$moduleName" . "_photo", $photo_id);

                if ($moduleName != 'album') {
                    $album = Engine_Api::_()->getItem("$moduleName" . "_album", $photo->album_id);
                } else {
                    $album = Engine_Api::_()->getItem("album", $photo->album_id);
                }

                if (Engine_Api::_()->sitecontentcoverphoto()->checkConditionsForAlbum($moduleName)) {
                    if ($special == 'cover' && $album->default_value == 1 && $album->type != 'cover' && $album->photo_id == $subject->photo_id && $subject->$fieldName == 0) {
                        $notNeedToCreate = false;
                    } elseif ($special == 'cover' && $album->default_value == 1 && $album->type != 'cover' && $album->photo_id == $subject->photo_id && $subject->$fieldName != 0) {
                        $notNeedToCreate = true;
                    } elseif ($special == 'cover' && $album->default_value == 0 && $album->type == 'cover' && $album->photo_id != $subject->photo_id && $subject->$fieldName != 0) {
                        $notNeedToCreate = true;
                    }
                }

                if ($special == 'profile') {
                    $notNeedToCreate = true;
                }

                if ($moduleName == 'album' || $moduleName == 'siteevent' || $moduleName == 'sitestoreproduct' || $moduleName == 'sitereview') {
                    $notNeedToCreate = true;
                }
                if ($photo->file_id && !$notNeedToCreate)
                    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
            }

            if (empty($photo_id) || empty($photo)) {
                if (!$this->getRequest()->isPost()) {
                    return;
                }

                //CHECK FORM VALIDATION
                if (!$form->isValid($this->getRequest()->getPost())) {
                    return;
                }
            }

            //UPLOAD PHOTO
            if ($form->Filedata->getValue() !== null || $photo || ($notNeedToCreate && $file)) {

                //PROCESS
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    //CREATE PHOTO
                    $tablePhoto = Engine_Api::_()->getItemTable($moduleName . "_photo");
                    $getShortType = ucfirst($subject->getShortType());

                    $primaryTableKey = Engine_Api::_()->getItemtable($subject->getType())->info('primary');
                    $tablePrimaryFieldName = $primaryTableKey[1];
                    if (!$notNeedToCreate) {
                        $photo = $tablePhoto->createRow();

                        if (isset($photo->user_id)) {
                            $user_id = 'user_id';
                        } elseif (isset($photo->owner_id)) {
                            $user_id = 'owner_id';
                        }
                        $photo->setFromArray(array(
                            $user_id => Engine_Api::_()->user()->getViewer()->getIdentity(),
                            $tablePrimaryFieldName => $subject->getIdentity()
                        ));
                        $photo->save();

                        if ($file) {
                            if ($special == 'cover') {
                                $this->setCoverPhoto($file, $photo, $cover_photo_preview, null, null, $moduleName);
                            } else {
                                $this->setMainPhoto($file, $photo, $moduleName);
                            }
                        } else {
                            if ($special == 'cover') {
                                $this->setCoverPhoto($form->Filedata, $photo, $cover_photo_preview, null, null, $moduleName);
                            } else {
                                $this->setMainPhoto($form->Filedata, $photo, $moduleName);
                            }
                        }

                        if ($special == 'cover') {
                            $album = Engine_Api::_()->sitecontentcoverphoto()->getSpecialAlbum($subject, $special, $moduleName);
                            $tablePhotoName = $tablePhoto->info('name');

                            if (isset($tablePhoto->order)) {
                                $photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
                                $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
                                $order = 0;
                                if (!empty($photo_rowinfo)) {
                                    $order = $photo_rowinfo->order + 1;
                                }
                                $photo->order = $order;
                            }

                            if (isset($photo->collection_id))
                                $photo->collection_id = $album->album_id;
                            $photo->album_id = $album->album_id;
                            $photo->save();


                            if (!isset($album->cover_params['fontcolor'])) {
                                $fontcolor = '#FFFFFF';
                            } else {
                                $fontcolor = $album->cover_params['fontcolor'];
                            }
                            $coverParams = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
                            $coverParams = Zend_Json_Decoder::decode($coverParams);
                            $coverParams['fontcolor'] = $fontcolor;
                            $coverParams = Zend_Json_Encoder::encode($coverParams);
                            $album->cover_params = $coverParams;

                            $album->save();
                            if (!$album->photo_id) {
                                if ($moduleName != 'album') {
                                    $album->photo_id = $photo->file_id;
                                } else {
                                    $album->photo_id = $photo->getIdentity();
                                }
                                $album->save();
                            }
                        }
                    }

                    if ($special == 'cover') {
                        if ($moduleName != 'sitereview') {
                            $subject->$fieldName = $photo->photo_id;
                        } else {
                            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitereview');
                            $row = $tableOtherinfo->getOtherinfo($subject->listing_id);
                            if (empty($row)) {
                                Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->insert(array(
                                    'listing_id' => $subject->listing_id,
                                    $fieldName => $photo->photo_id
                                )); //COMMIT  
                            } else {
                                $tableOtherinfo->update(array($fieldName => $photo->photo_id), array('listing_id = ?' => $subject->listing_id));
                            }
                        }
                    } else {
                        if ($moduleName != 'album') {
                            $subject->photo_id = $photo->file_id;
                        } else {
                            $subject->photo_id = $photo->getIdentity();
                        }
                        if (Engine_Api::_()->sitecontentcoverphoto()->checkConditionsForAlbum($moduleName)) {
                            if ($moduleName != 'album') {
                                $photo->album_id = $photo->collection_id = Engine_Api::_()->getItemTable($moduleName . "_album")->getDefaultAlbum($subject->getIdentity())->album_id;
                            } else {
                                $photo->album_id = $photo->collection_id = Engine_Api::_()->getItemTable("album")->getDefaultAlbum($subject->getIdentity())->album_id;
                            }

                            $photo->save();
                        } else {

                            if ($moduleName != 'album') {
                                $album_id = Engine_Api::_()->getItemTable($moduleName . "_album")->select()
                                        ->from(Engine_Api::_()->getItemTable($moduleName . "_album")->info('name'), array('album_id'))
                                        ->where("$tablePrimaryFieldName = ?", $subject->getIdentity())
                                        ->query()
                                        ->fetchColumn();
                            } else {
                                $album_id = Engine_Api::_()->getItemTable("album")->select()
                                        ->from(Engine_Api::_()->getItemTable("album")->info('name'), array('album_id'))
                                        ->where("$tablePrimaryFieldName = ?", $subject->getIdentity())
                                        ->query()
                                        ->fetchColumn();
                            }
                            $photo->album_id = $album_id;
                            if (isset($photo->collection_id)) {
                                $photo->collection_id = $album_id;
                            }
                            $photo->save();
                        }
                    }

                    $subject->save();
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                    //ADD ACTIVITY
                    if (Engine_Api::_()->sitecontentcoverphoto()->checkConditionsForAlbum($moduleName)) {

                        $activityFeedType = null;
                        $ownerFunction = 'is' . $getShortType . 'Owner';
                        $feedTypeFunction = 'isFeedType' . $getShortType . 'Enable';
                        if (Engine_Api::_()->$moduleName()->$ownerFunction($subject) && Engine_Api::_()->$moduleName()->$feedTypeFunction()) {
                            if ($special == 'cover')
                                $activityFeedType = $moduleName . '_admin_cover_update';
                            elseif ($special == 'profile')
                                $activityFeedType = $moduleName . '_admin_profile_photo';
                        }
                        elseif ($subject->all_post || Engine_Api::_()->$moduleName()->$ownerFunction($subject)) {
                            if ($special == 'cover')
                                $activityFeedType = $moduleName . '_cover_update';
                            elseif ($special == 'profile')
                                $activityFeedType = $moduleName . '_profile_photo_update';
                        }

                        if ($activityFeedType) {
                            $action = $activityApi->addActivity($viewer, $subject, $activityFeedType);
                        }

                        if ($action) {
                            Engine_Api::_()->getApi('subCore', $moduleName)->deleteFeedStream($action);
                            if ($photo)
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                        }
                    }
                    else {
                        if ($moduleName == 'siteevent') {
                            if ($special == 'cover')
                                $activityFeedType = $moduleName . '_cover_update';
                            elseif ($special == 'profile')
                                $activityFeedType = $moduleName . '_change_photo';
                            $action = $activityApi->addActivity($viewer, $subject, Engine_Api::_()->siteevent()->getActivtyFeedType($subject, $activityFeedType));
                            if ($action) {
                                if ($photo)
                                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                            }
                        } else if ($moduleName == 'album') {
                            $activityFeedType = $moduleName . '_cover_update';
                            $action = $activityApi->addActivity($viewer, $subject, $activityFeedType);
                            if ($action) {
                                if ($photo)
                                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                            }
                        }
                    }

                    $this->view->status = true;
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->view->status = false;
                    $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
                    return;
                }
            }
        } else {
            //CHECK FORM VALIDATION
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
            if ($form->Filedata->getValue() !== null) {
                $values = $form->getValues();
                $sitecontentcoverphoto_setdefaultcoverphoto = $values['sitecontentcoverphoto_setdefaultcoverphoto'];
                $this->setCoverPhoto($form->Filedata, null, $cover_photo_preview, $level_id, $sitecontentcoverphoto_setdefaultcoverphoto, $moduleName, $subject);
                $this->view->status = true;
                $this->view->sitecontentcoverphoto_setdefaultcoverphoto = $sitecontentcoverphoto_setdefaultcoverphoto;
            }
        }
    }

    public function removeCoverPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->moduleName = $moduleName = $this->_getParam("moduleName");
        $this->view->fieldName = $fieldName = $this->_getParam("fieldName");
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->cover_photo_preview = $cover_photo_preview = 0;

        $this->view->special = $special = $this->_getParam('special', 'cover');

        $this->view->level_id = $level_id = $subject->getOwner()->level_id;
        $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
        if ($cover_photo_preview) {
            $this->view->level_id = $level_id = $this->_getParam("level_id", $level_id);
        }

        if ($special == 'cover' && empty($cover_photo_preview)) {
            $this->view->can_edit = $can_edit = 0;
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
            if (!$can_edit) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
        $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
        $postionParams = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
        $preview_id = Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($subject, $moduleName, $level_id, $postionParams);
        $count = 0;
        foreach ($level_ids as $key => $value) {
            if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($subject, $moduleName, $key, $postionParams) == $preview_id) {
                $count++;
            }
        }
        $this->view->count = $count;

        if ($this->getRequest()->isPost()) {

            if ($special == 'cover') {
                if (empty($cover_photo_preview)) {

                    if ($subject->getType() == 'sitereview_listing') {
                        $tableName = 'engine4_sitereview_otherinfo';
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $field = $db->query("SHOW COLUMNS FROM $tableName LIKE '$fieldName'")->fetch();
                        if (empty($field)) {
                            $db->query("ALTER TABLE `$tableName` ADD `$fieldName` INT( 11 ) NOT NULL DEFAULT '0'");
                        }

                        $fieldNameValue = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($subject->listing_id, $fieldName);
                        if ($fieldNameValue) {

                            if (Engine_Api::_()->getItem('sitereview_photo', $fieldNameValue))
                                Engine_Api::_()->getItem('sitereview_photo', $fieldNameValue)->delete();
                        }

                        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitereview');
                        $tableOtherinfo->update(array($fieldName => 0), array('listing_id = ?' => $subject->listing_id));
                    } else
                        $subject->$fieldName = 0;

                    $album = Engine_Api::_()->sitecontentcoverphoto()->getSpecialAlbum($subject, $special, $moduleName);
                    $album->cover_params = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF'));
                    $album->save();
                } else {
                    if ($this->view->count > 1) {
                        if (isset($_POST['sitecontentcoverphoto_removedefaultcoverphoto']) && $_POST['sitecontentcoverphoto_removedefaultcoverphoto']) {
                            $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                            foreach ($level_ids as $key => $value) {
                                if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($subject, $moduleName, $key, $postionParams) == $preview_id) {
                                    Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsIds($subject, $moduleName, $key, 0);
                                    $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                                    Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsParams($subject, $moduleName, $key, $postionParams);
                                }
                            }
                            $file = Engine_Api::_()->getItem('storage_file', $preview_id);
                            if ($file)
                                $file->delete();
                        } else {
                            Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsIds($subject, $moduleName, $level_id, 0);
                            $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                            Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, $postionParams);
                        }
                    } else {
                        Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsIds($subject, $moduleName, $level_id, 0);
                        $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                        Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, $postionParams);
                        $file = Engine_Api::_()->getItem('storage_file', $preview_id);
                        if ($file)
                            $file->delete();
                    }
                }
            } else {
//                /Engine_Api::_()->getItem($moduleName . "_photo", $subject->photo_id);
                $subject->photo_id = 0;
            }
            $subject->save();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    /**
     * Set a photo
     *
     * @param array photo
     * @return photo object
     */
    public function setCoverPhoto($photo, $photoObject, $cover_photo_preview, $level_id = null, $sitecontentcoverphoto_setdefault = 0, $moduleName = null, $subject = null) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if ($moduleName != 'album') {
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();

            if (!empty($hasVersion)) {
                $image->open($file)
                        ->resize(720, 720)
                        ->write($mainPath)
                        ->destroy();

                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(140, 160)
                        ->write($normalPath)
                        ->destroy();
            } else {
                $image->open($file)
                        ->autoRotate()
                        ->resize(720, 720)
                        ->write($mainPath)
                        ->destroy();

                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize(140, 160)
                        ->write($normalPath)
                        ->destroy();
            }
        } else {
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
            $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);

            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            if (!empty($hasVersion)) {
                $image->open($file)
                        ->resize($mainWidth, $mainHeight)
                        ->write($mainPath)
                        ->destroy();

                $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
                $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($normalWidth, $normalHeight)
                        ->write($normalPath)
                        ->destroy();

                $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
                $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
                // Resize image (normal)
                $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($normalLargeWidth, $normalLargeHeight)
                        ->write($normalLargePath)
                        ->destroy();

                $coverPath = $path . DIRECTORY_SEPARATOR . $base . '_c.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(1000, 1000)
                        ->write($coverPath)
                        ->destroy();
            } else {
                $image->open($file)->autoRotate()
                        ->resize($mainWidth, $mainHeight)
                        ->write($mainPath)
                        ->destroy();

                $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
                $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize($normalWidth, $normalHeight)
                        ->write($normalPath)
                        ->destroy();

                $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
                $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
                // Resize image (normal)
                $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize($normalLargeWidth, $normalLargeHeight)
                        ->write($normalLargePath)
                        ->destroy();
                $coverPath = $path . DIRECTORY_SEPARATOR . $base . '_c.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize(1000, 1000)
                        ->write($coverPath)
                        ->destroy();
            }
        }

        if (empty($cover_photo_preview)) {
            if (isset($photoObject->user_id)) {
                $user_id = 'user_id';
                $user_id_value = $photoObject->user_id;
            } elseif (isset($photoObject->owner_id)) {
                $user_id = 'owner_id';
                $user_id_value = $photoObject->owner_id;
            }
            $params = array(
                'parent_type' => $photoObject->getType(),
                'parent_id' => $photoObject->getIdentity(),
                $user_id => $user_id_value,
                'name' => basename($fileName),
            );

            try {
                $iMain = $filesTable->createFile($mainPath, $params);
                $iIconNormal = $filesTable->createFile($normalPath, $params);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                if ($moduleName == 'album') {
                    $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
                    $iMain->bridge($iIconNormalLarge, 'thumb.large');
                }
                $iCover = $filesTable->createFile($coverPath, $params);
                $iMain->bridge($iCover, 'thumb.cover');
            } catch (Exception $e) {
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($coverPath);
                if ($moduleName == 'album') {
                    @unlink($normalLargePath);
                }
            }
            @unlink($mainPath);
            @unlink($normalPath);
            @unlink($coverPath);
            if ($moduleName == 'album') {
                @unlink($normalLargePath);
            }
            $photoObject->modified_date = date('Y-m-d H:i:s');
            $photoObject->file_id = $iMain->file_id;
            $photoObject->save();
            if (!empty($tmpRow)) {
                $tmpRow->delete();
            }

            return $photoObject;
        } else {
            try {
                $iMain = $filesTable->createSystemFile($mainPath);
                $iIconNormal = $filesTable->createSystemFile($normalPath);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                $iCover = $filesTable->createSystemFile($coverPath);
                $iMain->bridge($iCover, 'thumb.cover');
            } catch (Exception $e) {
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($coverPath);
                if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                    throw new Album_Model_Exception($e->getMessage(), $e->getCode());
                } else {
                    throw $e;
                }
            }

            if ($sitecontentcoverphoto_setdefault) {
                $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                foreach ($level_ids as $key => $value) {
                    Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsIds($subject, $moduleName, $key, $iMain->file_id);
                }
            } else {
                Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsIds($subject, $moduleName, $level_id, $iMain->file_id);
            }
        }
    }

    /**
     * Set a photo
     *
     * @param array photo
     * @return photo object
     */
    public function setMainPhoto($photo, $photoObject, $moduleName) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        if (isset($photoObject->user_id)) {
            $user_id = 'user_id';
            $user_id_value = $photoObject->user_id;
        } elseif (isset($photoObject->owner_id)) {
            $user_id = 'owner_id';
            $user_id_value = $photoObject->owner_id;
        }
        $params = array(
            'parent_type' => $photoObject->getType(),
            'parent_id' => $photoObject->getIdentity(),
            $user_id => $user_id_value,
            'name' => basename($fileName),
        );
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        if ($moduleName != 'album') {

            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;

            $image = Engine_Image::factory();
            if (!empty($hasVersion)) {
                $image->open($file)
                        ->resize(720, 720)
                        ->write($mainPath)
                        ->destroy();

                // Resize image (profile)
                $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(200, 400)
                        ->write($profilePath)
                        ->destroy();

                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(140, 160)
                        ->write($normalPath)
                        ->destroy();
            } else {
                $image->open($file)->autoRotate()
                        ->resize(720, 720)
                        ->write($mainPath)
                        ->destroy();

                // Resize image (profile)
                $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize(200, 400)
                        ->write($profilePath)
                        ->destroy();

                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize(140, 160)
                        ->write($normalPath)
                        ->destroy();
            }
            // Resize image (icon)
            $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
            $image = Engine_Image::factory();
            $image->open($file);

            $size = min($image->height, $image->width);
            $x = ($image->width - $size) / 2;
            $y = ($image->height - $size) / 2;

            $image->resample($x, $y, $size, $size, 48, 48)
                    ->write($squarePath)
                    ->destroy();

            // Store
            $iMain = $filesTable->createFile($mainPath, $params);
            $iProfile = $filesTable->createFile($profilePath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);
            $iSquare = $filesTable->createFile($squarePath, $params);

            $iMain->bridge($iProfile, 'thumb.profile');
            $iMain->bridge($iIconNormal, 'thumb.normal');
            $iMain->bridge($iSquare, 'thumb.icon');

            // Remove temp files
            @unlink($mainPath);
            @unlink($profilePath);
            @unlink($normalPath);
            @unlink($squarePath);
        } else {
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
            $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);

            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            if (!empty($hasVersion)) {
                $image->open($file)
                        ->resize($mainWidth, $mainHeight)
                        ->write($mainPath)
                        ->destroy();

                $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
                $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($normalWidth, $normalHeight)
                        ->write($normalPath)
                        ->destroy();

                $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
                $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
                // Resize image (normal)
                $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($normalLargeWidth, $normalLargeHeight)
                        ->write($normalLargePath)
                        ->destroy();
            } else {
                $image->open($file)->autoRotate()
                        ->resize($mainWidth, $mainHeight)
                        ->write($mainPath)
                        ->destroy();

                $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
                $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
                // Resize image (normal)
                $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize($normalWidth, $normalHeight)
                        ->write($normalPath)
                        ->destroy();

                $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
                $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
                // Resize image (normal)
                $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

                $image = Engine_Image::factory();
                $image->open($file)->autoRotate()
                        ->resize($normalLargeWidth, $normalLargeHeight)
                        ->write($normalLargePath)
                        ->destroy();
            }
            // Store
            try {
                $iMain = $filesTable->createFile($mainPath, $params);
                $iIconNormal = $filesTable->createFile($normalPath, $params);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
                $iMain->bridge($iIconNormalLarge, 'thumb.large');
            } catch (Exception $e) {
                // Remove temp files
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($normalLargePath);
                // Throw
                if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                    throw new Album_Model_Exception($e->getMessage(), $e->getCode());
                } else {
                    throw $e;
                }
            }
        }

        $photoObject->modified_date = date('Y-m-d H:i:s');
        $photoObject->file_id = $iMain->file_id;
        $photoObject->save();
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }


        return $photoObject;
    }

    public function editFontColorAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->moduleName = $moduleName = $this->_getParam("moduleName");

        $this->view->fieldName = $fieldName = $this->_getParam("fieldName");
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->cover_photo_preview = $cover_photo_preview = 0;

        $this->view->special = $special = $this->_getParam('special', 'cover');

        $this->view->level_id = $level_id = $subject->getOwner()->level_id;
        $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
        if ($cover_photo_preview) {
            $this->view->level_id = $level_id = $this->_getParam("level_id", $level_id);
        }

        if ($special == 'cover' && empty($cover_photo_preview)) {
            $this->view->can_edit = $can_edit = 0;
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

            if (!$can_edit) {
                return $this->_forward('requireauth', 'error', 'core');
            }
            $album = Engine_Api::_()->sitecontentcoverphoto()->getSpecialAlbum($subject, 'cover', $moduleName);
            $fontcolor = '#FFFFFF';
            if (!is_array($album->cover_params)) {
                $fontcolor = '#FFFFFF';
                $decodecolor = Zend_Json_Decoder::decode($album->cover_params);
                if (isset($decodecolor['fontcolor']))
                    $fontcolor = $decodecolor['fontcolor'];
            } elseif (is_array($album->cover_params) && $album->cover_params['fontcolor']) {
                $fontcolor = $album->cover_params['fontcolor'];
            }

            $this->view->form = $form = new Sitecontentcoverphoto_Form_Photo_Editfontcolor();
            $form->populate(array('hiddenfontcolor' => $fontcolor));
            if ($this->getRequest()->isPost()) {
                if (is_array($album->cover_params)) {
                    $album->cover_params = Zend_Json_Encoder::encode(array('top' => $album->cover_params['top'], 'left' => $album->cover_params['left'], 'fontcolor' => $_POST['sitecontentcover_font_color']));
                } else {
                    $decodecolor = Zend_Json_Decoder::decode($album->cover_params);
                    $album->cover_params = Zend_Json_Encoder::encode(array('top' => $decodecolor['top'], 'left' => $decodecolor['left'], 'fontcolor' => $_POST['sitecontentcover_font_color']));
                }
                $album->save();
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully !'))
                ));
            }
        } else {

            $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
            $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();

            $preview_id = Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($subject, $moduleName, $level_id, Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF')));
            $count = 0;
            foreach ($level_ids as $key => $value) {
                if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($subject, $moduleName, $key, array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF')) == $preview_id) {
                    $count++;
                }
            }
            $this->view->count = $count;

            $postionParamss = Zend_Json_Decoder::decode(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF')));

            if (isset($postionParamss['fontcolor']))
                $fontcolor = $postionParamss['fontcolor'];
            else
                $fontcolor = '#FFFFFF';

            $this->view->form = $form = new Sitecontentcoverphoto_Form_Photo_Editfontcolor();
            $form->populate(array('hiddenfontcolor' => $fontcolor, 'count' => $count));

            if ($this->getRequest()->isPost()) {
                if ($this->view->count > 1) {
                    $postionParamss['fontcolor'] = $_POST['sitecontentcover_font_color'];
                    $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                    foreach ($level_ids as $key => $value) {
                        if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($subject, $moduleName, $key, $postionParamss) == $preview_id) {
                            Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsParams($subject, $moduleName, $key, Zend_Json_Encoder::encode($postionParamss));
                        }
                    }
                } else {
                    Engine_Api::_()->sitecontentcoverphoto()->setSiteContentDefaultSettingsParams($subject, $moduleName, $key, Zend_Json_Encoder::encode($postionParamss));
                }

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully !'))
                ));
            }
        }
    }

}
