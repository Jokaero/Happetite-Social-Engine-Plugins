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
class Siteusercoverphoto_SitemobileProfileController extends Seaocore_Controller_Action_Standard {

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
        $this->view->level_id = $level_id = 0;
        $user_id = $this->_getParam('user_id');
        $this->view->special = $special = $this->_getParam('special', 'cover');
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $this->view->level_id = $level_id = $this->_getParam("level_id", 0);
        }

        //CHECK FORM VALIDATION
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

        //PROCESS
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
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
                        $this->setCoverPhoto($file, $photo);
                    }
                } else {
                    if ($special == 'cover') {
                        $fileElement = $_FILES['Filedata'];
                        Engine_Api::_()->sitemobile()->autoRotationImage($fileElement);
                        $this->setCoverPhoto($_FILES['Filedata'], $photo);
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

//			if ($this->_getParam('photo_id'))
            return $this->_redirectCustom($user->getHref());
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            return;
        }
    }

    public function removeCoverPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->special = $special = $this->_getParam('special', 'cover');
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->user_id = $user_id = $this->_getParam('user_id');
        if ($this->getRequest()->isPost()) {
            $user = Engine_Api::_()->getItem('user', $user_id);
            if ($special == 'cover') {
                $user->user_cover = 0;
                $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
                $album = $tableAlbum->getSpecialAlbumCover($user, $special);
                $album->cover_params = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                $album->save();
            } else {
                $user->photo_id = 0;
            }
            $user->save();
            return $this->_redirectCustom($viewer->getHref());
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

    /**
     * Set a photo
     *
     * @param array photo
     * @return photo object
     */
    public function setCoverPhoto($photo, $photoObject) {

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
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize(720, 720)
                    ->write($mainPath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize(720, 720)
                    ->write($mainPath)
                    ->destroy();
        }
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize(140, 160)
                    ->write($normalPath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize(140, 160)
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
            $image->open($file)->autoRotate()
                ->resize(1000, 1000)
                ->write($coverPath)
                ->destroy();
        }

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
            if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                throw new Album_Model_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
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
    }

}
