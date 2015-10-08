<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProfileController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_ProfileController extends Core_Controller_Action_Standard {

    public function getCoverPhotoAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $this->_getParam("user_id");
        $special = $this->_getParam("special", "cover");
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
        $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
        if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
            $this->view->can_edit = $can_edit = 1;
        } else {
            $this->view->can_edit = $can_edit = 0;
        }
        $this->view->contentFullWidth = $contentFullWidth = $this->_getParam('contentFullWidth');
        $this->view->editFontColor = $editFontColor = $this->_getParam("editFontColor");
        
        $this->view->siteusercoverphotoChangeTabPosition = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.change.tab.position', 1);
        $onlyUserWithPhoto = $this->_getParam("onlyUserWithPhoto", 1);
        $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
        if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
            $this->view->photo = $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
        } else {
            $this->view->photo = $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
        }
        $this->view->coverTop = 0;
        $this->view->coverLeft = 0;
        $this->view->cover_params = array('top' => 0, 'left' => 0, 'fontcolor' => '#FFFFFF');
        $this->view->cover_photo_preview = $cover_photo_preview = 0;
        $this->view->level_id = $level_id = 0;
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
            $this->view->level_id = $level_id = $this->_getParam("level_id", 0);
        }
        if ($photo && empty($cover_photo_preview)) {
            $album_id = $photo->album_id;

            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                $album = Engine_Api::_()->getItem('advalbum_album', $album_id);
            } else {
                $album = Engine_Api::_()->getItem('album', $album_id);
            }

            if ($album && $album->cover_params) {
                if (!is_array($album->cover_params)) {
                    $decoded_cover_param = Zend_Json_Decoder::decode($album->cover_params);
                    $this->view->coverTop = $decoded_cover_param['top'];
                } else {
                    $decoded_cover_param = $album->cover_params;
                    $this->view->coverTop = $decoded_cover_param['top'];
                }
            }
            
            
        } else {
            $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
            $decoded_cover_param = Zend_Json_Decoder::decode(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams));
            $this->view->coverTop = $decoded_cover_param['top'];
        }
    }

    public function getMainPhotoAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $this->_getParam("user_id");
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
        $this->view->showContent = $this->_getParam("showContent");
        if (empty($this->view->showContent)) {
            $this->view->showContent = array();
        }
        $this->view->editFontColor = $editFontColor = $this->_getParam("editFontColor");
        $this->view->profile_like_button = $this->_getParam('profile_like_button');
        $this->view->cover_photo_preview = 0;
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
        }
        $this->view->contentFullWidth = $contentFullWidth = $this->_getParam('contentFullWidth');
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteusercoverphoto/View/Helper', 'Siteusercoverphoto_View_Helper');
        $this->view->can_edit = $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
        $this->view->photo = $photo = '';
        if (Engine_Api::_()->getItem("album_photo", $user->user_cover))
            $this->view->photo = $photo = Engine_Api::_()->getItem("album_photo", $user->user_cover);
        $this->view->level_id = $level_id = $this->_getParam("level_id", $user->getOwner()->level_id);
        $this->view->fontcolor = '';
        $this->view->siteusercoverphotoChangeTabPosition = $siteusercoverphotoChangeTabPosition = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.change.tab.position', 1);
        if ($siteusercoverphotoChangeTabPosition) {
            $this->view->fontcolor = '#FFFFFF';

            if ($photo ) {
                $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
                $album = $tableAlbum->getSpecialAlbumCover($user, 'cover');
                $this->view->fontcolor = $fontcolor = '#FFFFFF';
                if ($album->cover_params && !is_array($album->cover_params)) {
                    $this->view->fontcolor = $fontcolor = '#FFFFFF';
                    $decodecolor = Zend_Json_Decoder::decode($album->cover_params);
                    if (isset($decodecolor['fontcolor']))
                        $this->view->fontcolor = $fontcolor = $decodecolor['fontcolor'];
                } elseif (is_array($album->cover_params) && $album->cover_params['fontcolor']) {
                   $this->view->fontcolor = $fontcolor = $album->cover_params['fontcolor'];
                }
            } else {
                $decoded_cover_param = Zend_Json_Decoder::decode(Engine_Api::_()->siteusercoverphoto()->getSiteUserDefaultSettingsParams($level_id, Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF'))));
                if (isset($decoded_cover_param['fontcolor']))
                    $this->view->fontcolor = $decoded_cover_param['fontcolor'];
            }
        }
    }

    public function resetPositionCoverPhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $this->_getParam("user_id");
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
        $this->view->cover_photo_preview = $cover_photo_preview = 0;
        $this->view->level_id = 0;
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
            $this->view->level_id = $level_id = $this->_getParam("level_id", 0);
        }
        if (!$cover_photo_preview) {
            ////START MANAGE-ADMIN CHECK
            $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
            if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
                $this->view->can_edit = $can_edit = 1;
            } else {
                $this->view->can_edit = $can_edit = 0;
            }
            if (empty($can_edit))
                return;
// 			$tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
// 			$album = $tableAlbum->getSpecialAlbumCover($user, 'cover');
// 			$album->cover_params = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
// 			$album->save();

            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                $this->view->photo = $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
            } else {
                $this->view->photo = $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
            }
            if ($photo && empty($cover_photo_preview)) {
                $album_id = $photo->album_id;

                if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                    $album = Engine_Api::_()->getItem('advalbum_album', $album_id);
                } else {
                    $album = Engine_Api::_()->getItem('album', $album_id);
                }

                $album->cover_params = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
                $album->save();
            }
        } else {
            $defaultCover = $this->_getParam('defaultCover', 0);
            $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
            $postionParams = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
            if (!empty($defaultCover)) {
                $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                foreach ($level_ids as $key => $value) {
                    $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
                    if ($public_level_id == $key)
                        continue;
                    $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$key.params", $postionParams);
                }
            } else {
                $postionParams = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
                $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams);
            }
        }
    }

    public function getAlbumsPhotosAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $this->_getParam("user_id");
        $this->view->special = $special = $this->_getParam('special', 'cover');
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
        if ($special == 'cover') {
            $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
            if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
                $this->view->can_edit = $can_edit = 1;
            } else {
                $this->view->can_edit = $can_edit = 0;
            }

            if (!$can_edit) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
        $this->view->album_id = $album_id = $this->_getParam("album_id");
        $paginator = '';
        if ($album_id) {
            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                $this->view->album = $album = Engine_Api::_()->getItem('advalbum_album', $album_id);

                $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto')->getAdvPhotoPaginator(array('album' => $album));
                if ($paginator)
                    $paginator->setItemCountPerPage(10000);
            } else {
                $this->view->album = $album = Engine_Api::_()->getItem('album', $album_id);
                $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('album_photo')->getPhotoPaginator(array('album' => $album));
                if ($paginator)
                    $paginator->setItemCountPerPage(10000);
            }
        } elseif ($recentAdded) {
            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {

                $select = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto')->getAdvAlbumSelect(array('owner' => $user));
            } else {
                $select = Engine_Api::_()->getItemTable('album')->getAlbumSelect(array('owner' => $user));
            }

            $albums = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $paginator = $this->getPhotoPaginator($albums);
            if ($paginator)
                $paginator->setItemCountPerPage(10000);
        } else {
            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                $paginator = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto')->getAdvAlbumPaginator(array('owner' => $user));
            } else {
                $paginator = Engine_Api::_()->getItemTable('album')->getAlbumPaginator(array('owner' => $user));
            }
            if ($paginator)
                $paginator->setItemCountPerPage(10000);
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

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->cover_photo_preview = $cover_photo_preview = 0;
        $user_id = $this->_getParam('user_id');
        $this->view->special = $special = $this->_getParam('special', 'cover');
        $user = Engine_Api::_()->getItem('user', $user_id);
        $this->view->level_id = $level_id = 0;
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
            $this->view->level_id = $level_id = $this->_getParam("level_id", 0);
        }

        //GET FORM
        if ($special == 'cover') {
            if (!$cover_photo_preview) {
                $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
                if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
                    $this->view->can_edit = $can_edit = 1;
                } else {
                    $this->view->can_edit = $can_edit = 0;
                }

                if (!$can_edit) {
                    return $this->_forward('requireauth', 'error', 'core');
                }
                $this->view->form = $form = new Siteusercoverphoto_Form_Photo_Cover();
            } else {
                $this->view->form = $form = new Siteusercoverphoto_Form_Photo_DefaultCover();
            }
        } else {
            $this->view->form = $form = new Siteusercoverphoto_Form_Photo_Main();
        }

        //CHECK FORM VALIDATION

        if (empty($cover_photo_preview)) {
            $file = '';
            $notNeedToCreate = false;
            $photo_id = $this->_getParam('photo_id');
            if ($photo_id) {
                if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                    $photo = Engine_Api::_()->getItem('advalbum_photo', $photo_id);
                    $album = Engine_Api::_()->getItem('advalbum_album', $photo->album_id);
                } else {
                    $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
                    $album = Engine_Api::_()->getItem('album', $photo->album_id);
                }

                if ($album && ($album->type == 'cover' || $album->type == 'profile')) {
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
                    if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'advalbum');
                    } else {
                        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'album');
                    }
                    if (!$notNeedToCreate) {
                        $photo = $tablePhoto->createRow();
                        $photo->setFromArray(array(
                            'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                            'owner_type' => 'user'
                        ));
                        $photo->save();
                        if ($file) {
                            if ($special == 'cover') {
                                $this->setCoverPhoto($file, $photo, $cover_photo_preview);
                            } else {
                                $this->setMainPhoto($file, $photo);
                            }
                        } else {
                            if ($special == 'cover') {
                                $this->setCoverPhoto($form->Filedata, $photo, $cover_photo_preview);
                            } else {
                                $this->setMainPhoto($form->Filedata, $photo);
                            }
                        }

                        if ($special == 'cover') {
                            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
                            $album = $tableAlbum->getSpecialAlbumCover($user, $special);
                        } else {
                            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                                $tableAlbum = Engine_Api::_()->getDbtable('albums', 'advalbum');
                            } else {
                                $tableAlbum = Engine_Api::_()->getDbtable('albums', 'album');
                            }
                            $album = $tableAlbum->getSpecialAlbum($user, 'profile');
                        }
                        $photo->album_id = $album->album_id;
                        $photo->save();
                    }

                    $album->cover_params = Zend_Json_Encoder::encode($this->_getParam('position', array('top' => '0', 'left' => 0)));
                    $album->save();
                    if (!$album->photo_id) {
                        $album->photo_id = $photo->photo_id;
                        $album->save();
                    }
                    if ($special == 'cover') {
                        $user->user_cover = $photo->photo_id;
                    } else {
                        $user->photo_id = $photo->file_id;
                    }

                    $user->save();

                    //ADD ACTIVITY
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                    if ($special == 'cover') {
                        $action = $activityApi->addActivity($viewer, $photo, 'user_cover_update');
                        if ($action) {
                            if ($photo)
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                        }
                    }
                    else {
                        $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

                        //INSERT ACTIVITY
                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

                        //HOOKS TO ENABLE ALBUMS TO WORK
                        if ($action) {
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
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
                $siteusercoverphoto_setdefaultcoverphoto = $values['siteusercoverphoto_setdefaultcoverphoto'];
                $this->setCoverPhoto($form->Filedata, null, $cover_photo_preview, $level_id, $siteusercoverphoto_setdefaultcoverphoto);
                $this->view->status = true;
                $this->view->siteusercoverphoto_setdefaultcoverphoto = $siteusercoverphoto_setdefaultcoverphoto;
            }
        }
    }

    public function removeCoverPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->cover_photo_preview = $cover_photo_preview = 0;
        $this->view->level_id = $level_id = 0;
        $this->view->special = $special = $this->_getParam('special', 'cover');
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $this->_getParam('user_id');
        $user = Engine_Api::_()->getItem('user', $user_id);
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
            $this->view->level_id = $level_id = $this->_getParam("level_id", 0);
        }

        if ($special == 'cover' && empty($cover_photo_preview)) {
            $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
            if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
                $this->view->can_edit = $can_edit = 1;
            } else {
                $this->view->can_edit = $can_edit = 0;
            }
            if (!$can_edit) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
        $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
        $preview_id = $coreSettingsApi->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id");
        $count = 0;
        foreach ($level_ids as $key => $value) {
            $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
            if ($public_level_id == $key)
                continue;
            if ($coreSettingsApi->getSetting("siteusercoverphoto.cover.photo.preview.level.$key.id") == $preview_id) {
                $count++;
            }
        }
        $this->view->count = $count;

        if ($this->getRequest()->isPost()) {
            if ($special == 'cover') {
                if (empty($cover_photo_preview)) {
                    $user->user_cover = 0;
                    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
                    $album = $tableAlbum->getSpecialAlbumCover($user, $special);
                    $album->cover_params = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                    $album->save();
                } else {
                    if ($this->view->count > 1) {
                        $siteusercoverphoto_removedefaultcoverphoto = $_POST['siteusercoverphoto_removedefaultcoverphoto'];
                        if ($siteusercoverphoto_removedefaultcoverphoto) {
                            $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                            foreach ($level_ids as $key => $value) {
                                $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
                                if ($public_level_id == $key)
                                    continue;
                                $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$key.id", 0);
                                $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                                $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$key.params", $postionParams);
                            }
                            $file = Engine_Api::_()->getItem('storage_file', $preview_id);
                            if ($file)
                                $file->delete();
                        } else {
                            $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", 0);
                            $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                            $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams);
                        }
                    } else {
                        $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", 0);
                        $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                        $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams);
                        $file = Engine_Api::_()->getItem('storage_file', $preview_id);
                        if ($file)
                            $file->delete();
                    }
                }
            } else {
                $user->photo_id = 0;
            }
            $user->save();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    public function getPhotoSelect($album_ids) {

        if (empty($album_ids))
            return;

        if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
            $select = Engine_Api::_()->getDbtable('photos', 'advalbum')->select();
        } else {
            $select = Engine_Api::_()->getDbtable('photos', 'album')->select();
        }
        $select->where('album_id in (?)', $album_ids);

        $select->order('order DESC');

        return $select;
    }

    public function getPhotoPaginator($album_ids) {

        if (empty($album_ids))
            return;
        return Zend_Paginator::factory($this->getPhotoSelect($album_ids));
    }

    public function webCamImageAction() {
        $temFileName = null;
        $session = new Zend_Session_Namespace();
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/webcam';
        $siteusercoverphotoWebcamtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.webcamtype', false);
        if (isset($session->tem_file_main_photo_name) && !empty($siteusercoverphotoWebcamtype)) {
            $temFileName = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->seaddonsBaseUrl() . '/public/webcam/' . $session->tem_file_main_photo_name;
            @chmod($temFileName, 0777);
            //MAKE PROFILE PHOTO OF LOGGDEN USER.
            $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
            $viewer->setPhoto('public/webcam/' . $session->tem_file_main_photo_name);
            $this->view->photo_name = $path . DIRECTORY_SEPARATOR . $session->tem_file_main_photo_name;
            $iMain = Engine_Api::_()->getItem('storage_file', $viewer->photo_id);

            //INSERT ACTIVITY
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $viewer, 'profile_photo_update', '{item:$subject} added a new profile photo.');

            //HOOKS TO ENABLE ALBUMS TO WORK
            if ($action) {
                $event = Engine_Hooks_Dispatcher::_()
                        ->callEvent('onUserProfilePhotoUpload', array(
                    'user' => $viewer,
                    'file' => $iMain,
                ));

                $attachment = $event->getResponse();
                if (!$attachment)
                    $attachment = $iMain;

                //WE HAVE TO ATTACH THE USER HIMSELF W/O ALBUM PLUGIN
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
            }
        }
        $this->view->tem_file_main_photo_name = $temFileName;
        unset($session->tem_file_main_photo_name);
    }

    /**
     * Set a photo
     *
     * @param array photo
     * @return photo object
     */
    public function setCoverPhoto($photo, $photoObject, $cover_photo_preview, $level_id = null, $siteusercoverphoto_setdefault = 0) {


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
        $coverPhotoAttempt = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $isCoverAttempt = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.isattempt', false);
        $getCoverType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.get.type', false);
        $coverphotoAttemptLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.attempt.ltype', false);


        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
            $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);

            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
            $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
            if(!empty($hasVersion)) {
            $image->open($file)
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
            } else {
                $image->open($file)
                       ->autoRotate()
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
            }
        
        $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
        $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        
        $image = Engine_Image::factory();
        if(!empty($hasVersion)) {
        $image->open($file)
                ->resize($normalWidth, $normalHeight)
                ->write($normalPath)
                ->destroy();
        } else {
          $image->open($file)
                ->autoRotate()
                ->resize($normalWidth, $normalHeight)
                ->write($normalPath)
                ->destroy();  
        }
            
        $coverPath = $path . DIRECTORY_SEPARATOR . $base . '_c.' . $extension;
        $image = Engine_Image::factory();
        if(!empty($hasVersion)) {
        $image->open($file)
                ->resize(1000, 1000)
                ->write($coverPath)
                ->destroy();
        } else {
            $image->open($file)
                ->autoRotate()
                ->resize(1000, 1000)
                ->write($coverPath)
                ->destroy();
        }

        if (empty($isCoverAttempt) && $getCoverType != $coverphotoAttemptLtype) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.getalbumtype', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.webcamtype', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.elementinfo', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.album.set', 0);
            $requestListType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.request.mod', false);
            $requestListType = !empty($requestListType) ? @unserialize($requestListType) : array();
            $requestListType[] = $coverPhotoAttempt;
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.request.mod', @serialize($requestListType));
        }

        if (empty($cover_photo_preview)) {
            $params = array(
                'parent_type' => $photoObject->getType(),
                'parent_id' => $photoObject->getIdentity(),
                'user_id' => $photoObject->owner_id,
                'name' => basename($fileName),
            );

            try {
                $iMain = $filesTable->createFile($mainPath, $params);
                $iIconNormal = $filesTable->createFile($normalPath, $params);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                $iCover = $filesTable->createFile($coverPath, $params);
                $iMain->bridge($iCover, 'thumb.cover');
            } catch (Exception $e) {
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($coverPath);
// 				if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
// 					throw new Album_Model_Exception($e->getMessage(), $e->getCode());
// 				} else {
// 					throw $e;
// 				}
            }
            @unlink($mainPath);
            @unlink($normalPath);
            @unlink($coverPath);
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
            $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
            if ($siteusercoverphoto_setdefault) {
                $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                foreach ($level_ids as $key => $value) {
                    $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
                    if ($public_level_id == $key)
                        continue;
                    $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$key.id", $iMain->file_id);
                }
            } else {
                $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", $iMain->file_id);
            }
        }
    }

    /**
     * Set a photo
     *
     * @param array photo
     * @return photo object
     */
    public function setMainPhoto($photo, $photoObject) {

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
        $params = array(
            'parent_type' => $photoObject->getType(),
            'parent_id' => $photoObject->getIdentity(),
            'user_id' => $photoObject->owner_id,
            'name' => basename($fileName),
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
            $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);
            $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            if(!empty($hasVersion)) {
            $image->open($file)
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
            } else {
                $image->open($file)
                    ->autoRotate()
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
            }

            $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
            $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
            // Resize image (normal)
            $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

            $image = Engine_Image::factory();
            if(!empty($hasVersion)) {
            $image->open($file)
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();
            } else {
                $image->open($file)
                    ->autoRotate()
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();
            }

            $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
            $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
            // Resize image (normal)
            $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

            $image = Engine_Image::factory();
            if(!empty($hasVersion)) {
            $image->open($file)
                    ->resize($normalLargeWidth, $normalLargeHeight)
                    ->write($normalLargePath)
                    ->destroy();
            } else {
                $image->open($file)
                    ->autoRotate()
                    ->resize($normalLargeWidth, $normalLargeHeight)
                    ->write($normalLargePath)
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
            try {
                $iMain = $filesTable->createFile($mainPath, $params);
                $iIconNormal = $filesTable->createFile($normalPath, $params);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
                $iMain->bridge($iIconNormalLarge, 'thumb.large');
                $iSquare = $filesTable->createFile($squarePath, $params);
                $iMain->bridge($iSquare, 'thumb.icon');
            } catch (Exception $e) {
                // Remove temp files
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($normalLargePath);
                @unlink($squarePath);
                // Throw
                if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                    throw new Album_Model_Exception($e->getMessage(), $e->getCode());
                } else {
                    throw $e;
                }
            }
//        // Resize image (main)
//        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
//        $image = Engine_Image::factory();
//        $image->open($file)
//                ->resize(720, 720)
//                ->write($mainPath)
//                ->destroy();
//
//        // Resize image (profile)
//        $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
//        $image = Engine_Image::factory();
//        $image->open($file)
//                ->resize(200, 400)
//                ->write($profilePath)
//                ->destroy();
//
//        // Resize image (normal)
//        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
//        $image = Engine_Image::factory();
//        $image->open($file)
//                ->resize(140, 160)
//                ->write($normalPath)
//                ->destroy();
//
//        // Resize image (icon)
//        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
//        $image = Engine_Image::factory();
//        $image->open($file);
//
//        $size = min($image->height, $image->width);
//        $x = ($image->width - $size) / 2;
//        $y = ($image->height - $size) / 2;
//
//        $image->resample($x, $y, $size, $size, 48, 48)
//                ->write($squarePath)
//                ->destroy();
//
//        // Store
//        $iMain = $filesTable->createFile($mainPath, $params);
//        $iProfile = $filesTable->createFile($profilePath, $params);
//        $iIconNormal = $filesTable->createFile($normalPath, $params);
//        $iSquare = $filesTable->createFile($squarePath, $params);
//
//        $iMain->bridge($iProfile, 'thumb.profile');
//        $iMain->bridge($iIconNormal, 'thumb.normal');
//        $iMain->bridge($iSquare, 'thumb.icon');
//
//        // Remove temp files
//        @unlink($mainPath);
//        @unlink($profilePath);
//        @unlink($normalPath);
//        @unlink($squarePath);
        
        
        
        
        
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
        $user_id = $this->_getParam("user_id");
        $special = $this->_getParam("special", "cover");
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
        $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
        if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
            $this->view->can_edit = $can_edit = 1;
        } else {
            $this->view->can_edit = $can_edit = 0;
        }

        if (!$can_edit) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->cover_photo_preview = $cover_photo_preview = 0;
        $this->view->level_id = $level_id = 0;
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->cover_photo_preview = $cover_photo_preview = $this->_getParam("cover_photo_preview", 0);
            $this->view->level_id = $level_id = $this->_getParam("level_id", 0);
        }

        if ($special == 'cover' && empty($cover_photo_preview)) {

            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
            $album = $tableAlbum->getSpecialAlbumCover($user, $special);
            $fontcolor = '#FFFFFF';
            if (!is_array($album->cover_params)) {
                $fontcolor = '#FFFFFF';
                $decodecolor = Zend_Json_Decoder::decode($album->cover_params);
                if (isset($decodecolor['fontcolor']))
                    $fontcolor = $decodecolor['fontcolor'];
            } elseif (is_array($album->cover_params) && $album->cover_params['fontcolor']) {
                $fontcolor = $album->cover_params['fontcolor'];
            }

            $this->view->form = $form = new Siteusercoverphoto_Form_Photo_Editfontcolor();
            $form->populate(array('hiddenfontcolor' => $fontcolor));
            if ($this->getRequest()->isPost()) {
                if (is_array($album->cover_params)) {
                    $album->cover_params = Zend_Json_Encoder::encode(array('top' => $album->cover_params['top'], 'left' => $album->cover_params['left'], 'fontcolor' => $_POST['siteusercover_font_color']));
                } else {
                    $decodecolor = Zend_Json_Decoder::decode($album->cover_params);
                    $album->cover_params = Zend_Json_Encoder::encode(array('top' => $decodecolor['top'], 'left' => $decodecolor['left'], 'fontcolor' => $_POST['siteusercover_font_color']));
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
            $preview_id = $coreSettingsApi->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id");
            $count = 0;
            foreach ($level_ids as $key => $value) {
                $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
                if ($public_level_id == $key)
                    continue;
                if ($coreSettingsApi->getSetting("siteusercoverphoto.cover.photo.preview.level.$key.id") == $preview_id) {
                    $count++;
                }
            }
            $this->view->count = $count;
            
            $postionParamss = Engine_Api::_()->siteusercoverphoto()->getSiteUserDefaultSettingsParams($level_id, array('top' => '0', 'left' => 0, 'fontcolor' => '#FFFFFF'));

            if (isset($postionParamss['fontcolor']))
                $fontcolor = $postionParamss['fontcolor'];
            else
                $fontcolor = '#FFFFFF';

            $this->view->form = $form = new Siteusercoverphoto_Form_Photo_Editfontcolor();
            $form->populate(array('hiddenfontcolor' => $fontcolor, 'count' => $count));
            if ($this->getRequest()->isPost()) {
                
                
                if ($this->view->count > 1) {
                    $postionParamss['fontcolor'] = $_POST['siteusercover_font_color'];
                    $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                    
                    foreach ($level_ids as $key => $value) {
                       Engine_Api::_()->siteusercoverphoto()->setSiteUserDefaultSettingsParams($key, Zend_Json_Encoder::encode($postionParamss));
                    }
                } else {
                    Engine_Api::_()->siteusercoverphoto()->setSiteUserDefaultSettingsParams( $level_id, Zend_Json_Encoder::encode($postionParamss));
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