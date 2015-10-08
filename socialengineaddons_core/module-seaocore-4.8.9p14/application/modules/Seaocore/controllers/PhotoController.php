<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_PhotoController extends Core_Controller_Action_Standard {

  protected $_module_name;
  protected $_resource_type;

  public function init() {

    $this->_module_name = $this->view->module_name = $this->_getParam('module_name');
    if ($this->_module_name == 'sitealbum')
      $this->_module_name = 'album';
    $resource_type = $this->_getParam('resource_type');
    $this->view->tab = $this->_getParam('tab');

    if (empty($resource_type)) {
      $this->_resource_type = $this->view->resource_type = $this->_module_name . "_photo";
    } else {
      $this->_resource_type = $this->view->resource_type = $this->_getParam('resource_type');
    }

    //SETTING THE PHOTO SUBJECT
    if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
            null !== ($photo = Engine_Api::_()->getItem($this->_resource_type, $photo_id))) {
      Engine_Api::_()->core()->setSubject($photo);
    }
  }

  //ACTION FOR VIEW THE PHOTO
  public function viewAction() {
    $this->_helper->layout->disableLayout();
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    if ($this->_module_name == 'album' && !Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->isLessThan417AlbumModule()) {
      $this->view->album = $album = $photo->getAlbum();
    } else {
      $this->view->album = $album = $photo->getCollection();
    }
    $params = array();
    $this->view->is_ajax_lightbox = $this->_getParam('is_ajax_lightbox', 0);
    $this->view->viewPermission = $viewPermission = $photo->authorization()->isAllowed($viewer, 'view');
    $viewer_id = $viewer->getIdentity();
    switch ($this->_module_name) {
      //GROUP PHOTOS IN THE LIGHTBOX
      case "advgroup":
      case "group":

        //CHECKING THE PRIVACY IF GROUP HAVE PRIVACY THEN PHOTOS WILL BE SHO IN THE LIGHTBOX
//         if (!$this->_helper->requireAuth()->setAuthParams($photo->getGroup(), null, "view")->isValid()) {
//           $this->viewPermission = 0;
//         }
        $this->view->viewPermission = $viewPermission = $photo->getGroup()->authorization()->isAllowed($viewer, 'view');
        //GET TAG,UNTAG,EDIT,DELETE PRIVACY
        $this->view->canTag = $this->view->canDelete = $this->view->canUntagGlobal = $this->view->canEdit = $photo->canEdit(Engine_Api::_()->user()->getViewer());

        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        $this->view->deleteRoute = "group_extended";

        //GET DELETE ACTION
        $this->view->deleteAction = "delete";

        //GET EDIT ACTION
        $this->view->editAction = "edit";

        //GET COMMENT
        $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        break;

      //ALBUM PHOTOS IN THE LIGHTBOX           
      case "album":
      case "advalbum":
        //CHECKING THE PRIVACY IF ALBUM HAVE PRIVACY THEN PHOTOS WILL BE SHO IN THE LIGHTBOX
        if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid()) {
          return;
        }
        //GET EDIT PRIVACY
        $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');

        //GET DELETE PRIVACY
        $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');

        //GET TAG PRIVACY        
        $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');

        //GET UNTAG PRIVACY
        $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);

        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        if($this->_module_name == 'sitealbum' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum')->version >= '4.8.5') {
					$this->view->deleteRoute = $this->view->editRoute = 'sitealbum_extended';
        } else {
					$this->view->deleteRoute = $this->view->editRoute = 'album_extended';
        }

        //GET DELETE ACTION
        $this->view->deleteAction = 'delete';

        //GET EDIT ACTION
        $this->view->editAction = 'edit';

        //GET COMMENT PRIVACY
        $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');

        //GET VIEW PRIVACY AND CHECK PHOTO IS FEATURED OR NOT
        $this->view->allowView = $this->view->canMakeFeatured = false;

        //GET VIEWER ID
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id) && $viewer->level_id == 1) {
          $this->view->canMakeFeatured = true;
          $auth = Engine_Api::_()->authorization()->context;
          $this->view->allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
        }

        if (Engine_Api::_()->hasModuleBootstrap('sitealbum') && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->version >= '4.8.5') {

          // Get albums
          $albumTable = Engine_Api::_()->getItemTable('album');
          $myAlbums = $albumTable->select()
                  ->from($albumTable, array('album_id', 'title', 'type'))
                  ->where('owner_type = ?', 'user')
                  ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                  ->query()
                  ->fetchAll();

          if ($album->type == null) {
            if (count($myAlbums) > 1)
              $this->view->movetotheralbum = 1;
            if ($album->photo_id != $photo->getIdentity())
              $this->view->makeAlbumCover = 1;
          }

          if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) {
            
            $this->view->update_permission = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbumrating.update', 1);
            
            if (!empty($viewer_id)) {
              $level_id = $viewer->level_id;
            } else {
              $level_id = 0;
            }

            $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, 'album', 'rate');
            if (!empty($viewer_id) && !empty($allowRating)) {
              $this->view->canRate = 1;
            } else {
              $this->view->canRate = 0;
            }

            $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitealbum');
            $this->view->rating_count = $ratingTable->ratingCount(array('resource_id' => $photo->getIdentity(), 'resource_type' => 'album_photo'));
            $this->view->rated = $ratingTable->checkRated(array('resource_id' => $photo->getIdentity(), 'resource_type' => 'album_photo'));
          }
        }
        break;

      //EVENT PHOTOS IN THE LIGHTBOX    
      case "ynevent":
      case "event":

        //CHECKING THE PRIVACY IF EVENT HAVE PRIVACY THEN PHOTOS WILL BE SHOWN IN THE LIGHTBOX
//         if (!$this->_helper->requireAuth()->setAuthParams($photo->getEvent(), null, "view")->isValid()) {
//           return;
//         }
        $this->view->viewPermission = $viewPermission = $photo->getEvent()->authorization()->isAllowed($viewer, 'view');
        //GET TAG,UNTAG,EDIT,DELETE PRIVACY
        $this->view->canTag = $this->view->canDelete = $this->view->canUntagGlobal = $this->view->canEdit = $photo->canEdit(Engine_Api::_()->user()->getViewer());

        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        $this->view->deleteRoute = $this->view->editRoute = "event_extended";

        //GET DELETE ACTION
        $this->view->deleteAction = "delete";

        //GET EDIT ACTION
        $this->view->editAction = "edit";

        //GET COMMENT
        $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');

        break;

      //LISTING PHOTOS IN THE LIGHTBOX  
      case "list":

        //CHECKING THE PRIVACY IF LIST HAVE PRIVACY THEN PHOTOS WILL BE SHOW IN THE LIGHTBOX
//         if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'view')->isValid())
//           return;
        $this->view->viewPermission = $viewPermission = $photo->authorization()->isAllowed(null, 'view');
        //GET TAG,UNTAG,EDIT PRIVACY
        $this->view->canTag = $this->view->canEdit = $this->view->canUntagGlobal = $photo->authorization()->isAllowed(null, 'edit');

        //GET DELETE PRIVACY
        $this->view->canDelete = $photo->authorization()->isAllowed(null, 'delete');

        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        $this->view->deleteRoute = $this->view->editRoute = $this->_module_name . '_photo_extended';

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ACTION
        $this->view->editAction = 'edit';

        //GET COMMENT
        $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        break;
      //RECIPE PHOTOS IN THE LIGHTBOX  
      case "recipe":

        //CHECKING THE PRIVACY IF RECIPE HAVE PRIVACY THEN PHOTOS WILL BE SHOW IN THE LIGHTBOX
//         if (!$this->_helper->requireAuth()->setAuthParams('recipe', null, 'view')->isValid())
//           return;
        $this->view->viewPermission = $viewPermission = $photo->authorization()->isAllowed(null, 'view');
        //GET TAG,UNTAG,EDIT PRIVACY
        $this->view->canTag = $this->view->canEdit = $this->view->canUntagGlobal = $photo->authorization()->isAllowed(null, 'edit');

        //GET DELETE PRIVACY
        $this->view->canDelete = $photo->authorization()->isAllowed(null, 'delete');

        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        $this->view->deleteRoute = $this->view->editRoute = $this->_module_name . '_photo_extended';

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ACTION
        $this->view->editAction = 'edit';

        //GET COMMENT
        $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        break;

      //SITEPAGENOTE PHOTOS IN THE LIGHTBOX  
      case "sitepagenote":
        //GET NOTE ITEM
        $this->view->sitepagenote = $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $photo->note_id);

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photoedit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITEPAGEEVEBT PHOTOS IN THE LIGHTBOX  
      case "sitepageevent":
        //GET NOTE ITEM
        $this->view->sitepageevent = $sitepageevent = $photo->getEvent();

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = $sitepageevent->getParentPage();

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITEPAGE PHOTOS IN THE LIGHTBOX    
      case "sitepage":

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $album->page_id);

        $this->view->photo = $photo;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_imagephoto_specific';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_imagephoto_specific';

        //GET VIEWER ID
        $viewer_id = $viewer->getIdentity();

        $this->view->allowFeatured = false;
        if (!empty($viewer_id) && $viewer->level_id == 1) {
          $auth = Engine_Api::_()->authorization()->context;
          $this->view->allowFeatured = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
        }

        //START MANAGE-ADMIN CHECK
        if (!empty($sitepage)) {
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
//           if (empty($isManageAdmin)) {
//             return $this->_forward('requireauth', 'error', 'core');
//           }
          $this->view->viewPermission = $viewPermission = $isManageAdmin;
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
          if (empty($isManageAdmin)) {
            $this->view->canComment = 0;
          } else {
            $this->view->canComment = 1;
          }

          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
          if (empty($isManageAdmin)) {
            $can_edit = 0;
          } else {
            $can_edit = 1;
          }
          if ($can_edit) {
            $this->view->canTag = 1;
            $this->view->canUntagGlobal = 1;
          } else {
            $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag');
            $this->view->canUntagGlobal = $album->isOwner($viewer);
          }

          //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT AND DELETE PHOTO
          if ($viewer->getIdentity() == $photo->user_id || $can_edit == 1) {
            $this->view->canDelete = 1;
            $this->view->canEdit = 1;
          } else {
            $this->view->canDelete = 0;
            $this->view->canEdit = 0;
          }
        }
        if (0 != ($page_id = (int) $this->_getParam('page_id', 0)))
          $params['page_id'] = $sitepage->page_id;
        //END MANAGE-ADMIN CHECK
        break;

      //SITEPAGENOTE PHOTOS IN THE LIGHTBOX  
      case "sitebusinessnote":
        //GET NOTE ITEM
        $this->view->sitebusinessnote = $sitebusinessnote = Engine_Api::_()->getItem('sitebusinessnote_note', $photo->note_id);

        //GET SITEPAGE ITEM
        $this->view->sitebusiness = $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $sitebusinessnote->business_id);

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photoedit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
//         if (empty($isManageAdmin)) {
//           return $this->_forward('requireauth', 'error', 'core');
//         }
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITEBUSINESSEVENT PHOTOS IN THE LIGHTBOX  
      case "sitebusinessevent":
        //GET NOTE ITEM
        $this->view->sitebusinessevent = $sitebusinessevent = $photo->getEvent();

        //GET SITEbusiness ITEM
        $this->view->sitebusiness = $sitebusiness = $sitebusinessevent->getParentPage();

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITEPAGE PHOTOS IN THE LIGHTBOX    
      case "sitebusiness":

        //GET SITEPAGE ITEM
        $this->view->sitebusiness = $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $album->business_id);

        $this->view->photo = $photo;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_imagephoto_specific';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_imagephoto_specific';

        //GET VIEWER ID
        $viewer_id = $viewer->getIdentity();

        $this->view->allowFeatured = false;
        if (!empty($viewer_id) && $viewer->level_id == 1) {
          $auth = Engine_Api::_()->authorization()->context;
          $this->view->allowFeatured = $auth->isAllowed($sitebusiness, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitebusiness, 'registered', 'view') === 1 ? true : false;
        }

        //START MANAGE-ADMIN CHECK
        if (!empty($sitebusiness)) {
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');
          $this->view->viewPermission = $viewPermission = $isManageAdmin;
//           if (empty($isManageAdmin)) {
//             return $this->_forward('requireauth', 'error', 'core');
//           }

          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'comment');
          if (empty($isManageAdmin)) {
            $this->view->canComment = 0;
          } else {
            $this->view->canComment = 1;
          }

          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
          if (empty($isManageAdmin)) {
            $can_edit = 0;
          } else {
            $can_edit = 1;
          }
          if ($can_edit) {
            $this->view->canTag = 1;
            $this->view->canUntagGlobal = 1;
          } else {
            $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag');
            $this->view->canUntagGlobal = $album->isOwner($viewer);
          }

          //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT AND DELETE PHOTO
          if ($viewer->getIdentity() == $photo->user_id || $can_edit == 1) {
            $this->view->canDelete = 1;
            $this->view->canEdit = 1;
          } else {
            $this->view->canDelete = 0;
            $this->view->canEdit = 0;
          }
        }

        if (0 != ($business_id = (int) $this->_getParam('business_id', 0)))
          $params['business_id'] = $sitebusiness->business_id;
        //END MANAGE-ADMIN CHECK
        break;
//SITEGROUPNOTE PHOTOS IN THE LIGHTBOX  
      case "sitegroupnote":
        //GET NOTE ITEM
        $this->view->sitegroupnote = $sitegroupnote = Engine_Api::_()->getItem('sitegroupnote_note', $photo->note_id);

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroupnote->group_id);

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photoedit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITEGROUPEVEBT PHOTOS IN THE LIGHTBOX  
      case "sitegroupevent":
        //GET NOTE ITEM
        $this->view->sitegroupevent = $sitegroupevent = $photo->getEvent();

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = $sitegroupevent->getParentPage();

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITEGROUP PHOTOS IN THE LIGHTBOX    
      case "sitegroup":

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $album->group_id);

        $this->view->photo = $photo;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_imagephoto_specific';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_imagephoto_specific';

        //GET VIEWER ID
        $viewer_id = $viewer->getIdentity();

        $this->view->allowFeatured = false;
        if (!empty($viewer_id) && $viewer->level_id == 1) {
          $auth = Engine_Api::_()->authorization()->context;
          $this->view->allowFeatured = $auth->isAllowed($sitegroup, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitegroup, 'registered', 'view') === 1 ? true : false;
        }

        //START MANAGE-ADMIN CHECK
        if (!empty($sitegroup)) {
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
//           if (empty($isManageAdmin)) {
//             return $this->_forward('requireauth', 'error', 'core');
//           }
          $this->view->viewPermission = $viewPermission = $isManageAdmin;
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'comment');
          if (empty($isManageAdmin)) {
            $this->view->canComment = 0;
          } else {
            $this->view->canComment = 1;
          }

          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
          if (empty($isManageAdmin)) {
            $can_edit = 0;
          } else {
            $can_edit = 1;
          }
          if ($can_edit) {
            $this->view->canTag = 1;
            $this->view->canUntagGlobal = 1;
          } else {
            $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag');
            $this->view->canUntagGlobal = $album->isOwner($viewer);
          }

          //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT AND DELETE PHOTO
          if ($viewer->getIdentity() == $photo->user_id || $can_edit == 1) {
            $this->view->canDelete = 1;
            $this->view->canEdit = 1;
          } else {
            $this->view->canDelete = 0;
            $this->view->canEdit = 0;
          }
        }
        if (0 != ($group_id = (int) $this->_getParam('group_id', 0)))
          $params['group_id'] = $sitegroup->group_id;
        //END MANAGE-ADMIN CHECK
        break;
      //SITESTORENOTE PHOTOS IN THE LIGHTBOX  
      case "sitestorenote":
        //GET NOTE ITEM
        $this->view->sitestorenote = $sitestorenote = Engine_Api::_()->getItem('sitestorenote_note', $photo->note_id);

        //GET SITESTORE ITEM
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestorenote->store_id);

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photoedit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITESTOREEVEBT PHOTOS IN THE LIGHTBOX  
      case "sitestoreevent":
        //GET NOTE ITEM
        $this->view->sitestoreevent = $sitestoreevent = $photo->getEvent();

        //GET SITESTORE ITEM
        $this->view->sitestore = $sitestore = $sitestoreevent->getParentPage();

        //GET MANAGE ADMIN
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
        $this->view->viewPermission = $viewPermission = $isManageAdmin;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = $this->_module_name . '_removeimage';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_photoedit';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_removeimage';

        //StART MANAGE-ADMIN CHECK
        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');
        if (empty($isManageAdmin)) {
          $this->view->canComment = 0;
        } else {
          $this->view->canComment = 1;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 0;
        } else {
          $this->view->canTag = $this->view->canUntagGlobal = $this->view->canDelete = $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        break;
      //SITESTORE PHOTOS IN THE LIGHTBOX    
      case "sitestore":

        //GET SITESTORE ITEM
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $album->store_id);

        $this->view->photo = $photo;

        //GET EDIT ACTION
        $this->view->editAction = 'photo-edit';

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ROUTE
        $this->view->editRoute = $this->_module_name . '_imagephoto_specific';

        //GET DELETE ROUTE
        $this->view->deleteRoute = $this->_module_name . '_imagephoto_specific';

        //GET VIEWER ID
        $viewer_id = $viewer->getIdentity();

        $this->view->allowFeatured = false;
        if (!empty($viewer_id) && $viewer->level_id == 1) {
          $auth = Engine_Api::_()->authorization()->context;
          $this->view->allowFeatured = $auth->isAllowed($sitestore, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitestore, 'registered', 'view') === 1 ? true : false;
        }

        //START MANAGE-ADMIN CHECK
        if (!empty($sitestore)) {
          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
//           if (empty($isManageAdmin)) {
//             return $this->_forward('requireauth', 'error', 'core');
//           }
          $this->view->viewPermission = $viewPermission = $isManageAdmin;
          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');
          if (empty($isManageAdmin)) {
            $this->view->canComment = 0;
          } else {
            $this->view->canComment = 1;
          }

          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
          if (empty($isManageAdmin)) {
            $can_edit = 0;
          } else {
            $can_edit = 1;
          }
          if ($can_edit) {
            $this->view->canTag = 1;
            $this->view->canUntagGlobal = 1;
          } else {
            $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag');
            $this->view->canUntagGlobal = $album->isOwner($viewer);
          }

          //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT AND DELETE PHOTO
          if ($viewer->getIdentity() == $photo->user_id || $can_edit == 1) {
            $this->view->canDelete = 1;
            $this->view->canEdit = 1;
          } else {
            $this->view->canDelete = 0;
            $this->view->canEdit = 0;
          }
        }
        if (0 != ($store_id = (int) $this->_getParam('store_id', 0)))
          $params['store_id'] = $sitestore->store_id;
        //END MANAGE-ADMIN CHECK
        break;
      case "sitereview":
        //GET SITEREVIEW DETAILS
        //GET LISTING TYPE ID
        $listingtype_id = $listingtype_id = Engine_Api::_()->getDbTable('listings', 'sitereview')->getListingTypeId($photo->listing_id); //$this->_getParam('listingtype_id', null);
        Engine_Api::_()->sitereview()->setListingTypeInRegistry($listingtype_id);
        $this->_listingType = Zend_Registry::get('listingtypeArray' . $listingtype_id);

        //GET LISTING TYPE ID
        $this->view->listingtype_id = $listingtype_id = $this->_listingType->listingtype_id;
        $this->view->viewPermission = $viewPermission = $photo->authorization()->isAllowed(null, "view_listtype_$listingtype_id");
        //GET SETTINGS
        $this->view->canEdit = $photo->authorization()->isAllowed(null, "edit_listtype_$listingtype_id");
        if (empty($this->view->canEdit) && $photo->user_id == $viewer_id) {
          $this->view->canEdit = 1;
        }
        if ($this->view->canEdit) {
          $this->view->canTag = 1;
          $this->view->canUntagGlobal = 1;
        }
        $this->view->canDelete = $photo->authorization()->isAllowed(null, "delete_listtype_$listingtype_id");
        if (empty($this->view->canDelete) && $photo->user_id == $viewer_id) {
          $this->view->canDelete = 1;
        }


        //GET DELETE ROUTE
        $this->view->deleteRoute = "sitereview_photo_extended_listtype_$listingtype_id";
        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        $this->view->deleteRoute = $this->view->editRoute = "sitereview_photo_extended_listtype_$listingtype_id";

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ACTION
        $this->view->editAction = 'edit';

        break;
      case "siteevent":
        $this->view->viewPermission = $viewPermission = $photo->authorization()->isAllowed(null, "view");
        //GET SETTINGS
        $this->view->canEdit = $photo->authorization()->isAllowed(null, "edit");
        if (empty($this->view->canEdit) && $photo->user_id == $viewer_id) {
          $this->view->canEdit = 1;
        }
        if ($this->view->canEdit) {
          $this->view->canTag = 1;
          $this->view->canUntagGlobal = 1;
        }
        $this->view->canDelete = $photo->authorization()->isAllowed(null, "delete");
        if (empty($this->view->canDelete) && $photo->user_id == $viewer_id) {
          $this->view->canDelete = 1;
        }


        //GET DELETE ROUTE
        $this->view->deleteRoute = "siteevent_photo_extended";
        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        $this->view->deleteRoute = $this->view->editRoute = "siteevent_photo_extended";

        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';

        //GET EDIT ACTION
        $this->view->editAction = 'edit';

        break;
      case "sitestoreproduct":
        //GET SITEREVIEW DETAILS
        $this->view->viewPermission = $viewPermission = $photo->authorization()->isAllowed(null, "view");
        //GET SETTINGS
        $this->view->canEdit = $photo->authorization()->isAllowed(null, "edit");
        if (empty($this->view->canEdit) && $photo->user_id == $viewer_id) {
          $this->view->canEdit = 1;
        }
        if ($this->view->canEdit) {
          $this->view->canTag = 1;
          $this->view->canUntagGlobal = 1;
        }
        $this->view->canDelete = $photo->authorization()->isAllowed(null, "delete");
        if (empty($this->view->canDelete) && $photo->user_id == $viewer_id) {
          $this->view->canDelete = 1;
        }

        //GET DELETE ROUTE
        $this->view->deleteRoute = "sitestoreproduct_photo_extended";
        //GET EDIT AND DELETE ROUTE FROM THE MANIFEST
        $this->view->deleteRoute = $this->view->editRoute = "sitestoreproduct_photo_extended";
        //GET DELETE ACTION
        $this->view->deleteAction = 'remove';
        //GET EDIT ACTION
        $this->view->editAction = 'edit';

        break;
    }

    if ($this->_module_name == 'album' && !Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->isLessThan417AlbumModule()) {
      $this->view->getPhotoIndex = $photo->getPhotoIndex();
    } else {
      $this->view->getPhotoIndex = $photo->getCollectionIndex();
    }

    if (null !== ($type = $this->_getParam('type', null)))
      $params['type'] = $type;
    if (0 != ($count = (int) $this->_getParam('count', 0)))
      $params['count'] = $count;
    $this->view->type_count = $count;
    $params['offset'] = 0;
    if (0 != ($offset = (int) $this->_getParam('offset', 0)))
      $params['offset'] = $offset;
    if (0 != ($owner_id = (int) $this->_getParam('owner_id', 0)))
      $params['owner_id'] = $owner_id;
    if (null !== ($urlaction = $this->_getParam('urlaction', null)))
      $params['urlaction'] = $urlaction;


    if (!empty($type)) {
      if (empty($offset)) {
        $params['offset'] = $offset = Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsgetCollectibleIndex($photo, $params);
      }
    } else {
      $count = $album->count();
      $offset = $this->view->getPhotoIndex;
      if (!empty($offset))
        $params['offset'] = $offset;
    }

    if ($offset >= $count)
      $params['offset'] -=$count;
    elseif ($offset < 0)
      $params['offset'] +=$count;

    if (($params['offset'] - 1) < 0) {
      $this->view->PrevOffset = $count - 1;
    } else {
      $this->view->PrevOffset = $params['offset'] - 1;
    }
    if (($params['offset'] + 1) >= $count) {
      $this->view->NextOffset = 0;
    } else {
      $this->view->NextOffset = $params['offset'] + 1;
    }

    if ($this->_module_name == 'album' && !Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->isLessThan417AlbumModule()) {
      $params['album'] = 'album';
    }
    $this->view->params = $params;

    $this->view->prevPhoto = Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsPrevPhoto($photo, $params);

    $this->view->nextPhoto = Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsNextPhoto($photo, $params);
    $array_photo = array();
    if (!empty($photo))
      $array_photo = $photo->toarray();

    if ($this->_module_name == 'album' && !Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->isLessThan417AlbumModule()) {
      if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
        $photo->view_count = new Zend_Db_Expr('view_count + 1');
        $photo->save();
      }
    } else {
      if (array_key_exists('user_id', $array_photo) && $this->_module_name != 'sitepagenote' && $this->_module_name != 'sitebusinessnote') {
        if ((!$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity()) && isset($photo->view_count)) {
          $photo->view_count = new Zend_Db_Expr('view_count + 1');
          $photo->save();
        }
      }
    }
    $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sea.lightbox.fixedwindow', 1)) {
      $this->renderScript('photo/light-box-view-without-fix-window.tpl');
    }
  }

  //ACTION FOR EDIT THE DESCRIPTION OF THE PHOTOS
  public function editDescriptionAction() {
    //GET TEXT
    $text = $this->_getParam('text_string');
    $photo = Engine_Api::_()->core()->getSubject();
    //GET DB
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUE
      $value['description'] = $text;
      $photo->setFromArray($value);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    exit();
  }

  //ACTION FOR EDIT THE DESCRIPTION OF THE PHOTOS
  public function editTitleAction() {
    //GET TEXT
    $text = $this->_getParam('text_string');
    $photo = Engine_Api::_()->core()->getSubject();
    //GET DB
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUE
      $value['title'] = $text;
      $photo->setFromArray($value);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    exit();
  }

  //ACTION FOR ROTATE THE PHOTOS
  public function rotateAction() {

    if (!$this->_helper->requireSubject($this->_resource_type)->isValid())
      return;

    $photo = Engine_Api::_()->core()->getSubject();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $angle = (int) $this->_getParam('angle', 90);
    if (!$angle || !($angle % 360)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must not be empty');
      return;
    }
    if (!in_array((int) $angle, array(90, 270))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must be 90 or 270');
      return;
    }

    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();

    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->rotate($angle)
            ->write()
            ->destroy()
    ;

    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR FLIP THE PHOTO
  public function flipAction() {

    if (!$this->_helper->requireSubject($this->_resource_type)->isValid())
      return;

    //GET PHOTO SUBJECT
    $photo = Engine_Api::_()->core()->getSubject();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $direction = $this->_getParam('direction');
    if (!in_array($direction, array('vertical', 'horizontal'))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid direction');
      return;
    }

    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();

    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->flip($direction != 'vertical')
            ->write()
            ->destroy()
    ;

    //SET THE PHOTO
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  public function getAllPhotosAction() {
    $subject_guid = $this->_getParam('subjectguid');
    $album = Engine_Api::_()->getItemByGuid($subject_guid);
    // $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    $hasAlbumVersion416GT = false;
    if ($album->getType() == 'album') {
      $albumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('album');
      $albumModuleVersion = $albumModule->version;

      if (!($albumModuleVersion < '4.1.7')) {
        $hasAlbumVersion416GT = true;
      }
    }

    if ($hasAlbumVersion416GT) {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
          'album' => $album,
      ));
    } else {
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    }

    $paginator->setItemCountPerPage(10000);
  }

}

?>
