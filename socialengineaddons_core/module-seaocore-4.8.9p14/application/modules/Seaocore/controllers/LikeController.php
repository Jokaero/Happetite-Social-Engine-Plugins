<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_LikeController extends Core_Controller_Action_Standard {

  public function likeAction() {

    //GET THE VIEWER.
    $viewer = Engine_Api::_()->user()->getViewer() ;

    //GET THE VALUE OF RESOURCE ID AND RESOURCE TYPE AND LIKE ID.
    $this->view->resource_id = $resource_id = $this->_getParam( 'resource_id' ) ;
    $this->view->resource_type = $resource_type = $this->_getParam( 'resource_type' ) ;
    $like_id = $this->_getParam( 'like_id' ) ;
    $status = $this->_getParam( 'smoothbox' , 1 ) ;
    $this->view->status = true ;

    //GET THE LIKE BUTTON SETTINGS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;

    //GET THE RESOURCE.
    if ($resource_type == 'member') {
    $resource = Engine_Api::_()->getItem( 'user' , $resource_id ) ;
    } else {
    $resource = Engine_Api::_()->getItem( $resource_type , $resource_id ) ;
    }
    
    if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepage' )) {
			$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
    }

    //GET THE CURRENT UESRID AND SETTINGS.
    $this->view->viewer_id = $loggedin_user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    if ( (empty( $loggedin_user_id ))) {
      return ;
    }

    //CHECK THE LIKE ID.
    if ( empty( $like_id ) ) {

      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $like_id_temp = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($resource_type , $resource_id);

      //CHECK THE THE ITEM IS LIKED OR NOT.
      if ( empty( $like_id_temp[0]['like_id'] ) ) {

        $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
        $notify_table = Engine_Api::_()->getDbtable( 'notifications' , 'activity' ) ;
        $db = $likeTable->getAdapter() ;
        $db->beginTransaction() ;
        try {

          //START NOTIFICATION WORK.
          if ( $resource_type == 'forum_topic' ) {
            $getOwnerId = Engine_Api::_()->getItem( $resource_type , $resource_id )->user_id ;
            $label = '{"label":"forum topic"}' ;
            $object_type = $resource_type ;
          }
          else if ( $resource_type == 'user' ) {
            $getOwnerId = $resource_id ;
            $label = '{"label":"profile"}' ;
            $object_type = 'user' ;
          }
          else {
            if ( $resource_type == 'album_photo' ) {
              $label = '{"label":"photo"}' ;
            }
            else if ( $resource_type == 'group_photo' ) {
              $label = '{"label":"group photo"}' ;
            }
            else if ( $resource_type == 'sitepageevent_event' ) {
              $label = '{"label":"page event"}' ;
            }
            else if ( $resource_type == 'sitepage_page' ) {
              $label = '{"label":"page"}' ;
            } else if ( $resource_type == 'sitebusiness_business' ) {
              $label = '{"label":"business"}' ;
            }
            else {
              $label = '{"label":"' . $resource->getShortType() . '"}' ;
            }
            if ( !strstr($resource_type, 'siteestore_product') ) {
							$getOwnerId = Engine_Api::_()->getItem( $resource_type , $resource_id )->getOwner()->user_id ;
            }
            $object_type = $resource_type ;
          }
          
          if($object_type == 'sitestore_store')
            $label = '';
        
          if ( !empty( $resource ) ) {

						//START PAGE MEMBER PLUGIN WORK.
						if ($resource_type == 'sitepage_page' && $sitepageVersion >= '4.3.0p1') {
							if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepagemember' )) {
								Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Join');
							}
							Engine_Api::_()->sitepage()->itemCommentLike($resource, 'sitepage_contentlike');
						} elseif($resource_type == 'sitebusiness_business') {
							Engine_Api::_()->sitebusiness()->itemCommentLike($resource, 'sitebusiness_contentlike');
						}  
            elseif($resource_type == 'sitegroup_group') {
							Engine_Api::_()->sitegroup()->itemCommentLike($resource, 'sitegroup_contentlike');
						} 
            elseif($resource_type == 'sitestore_store') {
							Engine_Api::_()->sitestore()->itemCommentLike($resource, 'sitestore_contentlike');
						}
						elseif($resource_type == 'siteevent_event') {
							Engine_Api::_()->siteevent()->itemCommentLike($resource, 'siteevent_contentlike', '', 'like');
						}  else {
                 if ( !empty( $getOwnerId ) && $getOwnerId != $viewer->getIdentity() ) {
                    $notifyData = $notify_table->createRow() ;
                    $notifyData->user_id = $getOwnerId ;
                    $notifyData->subject_type = $viewer->getType() ;
                    $notifyData->subject_id = $viewer->getIdentity() ;
                    $notifyData->object_type = $object_type ;
                    $notifyData->object_id = $resource_id ;
                    $notifyData->type = 'liked' ;
                    $notifyData->params = $label;
                    $notifyData->date = date( 'Y-m-d h:i:s' , time() ) ;
                    $notifyData->save() ;
                  }
            }
						//END PAGE MEMBER PLUGIN WORK.
					
            $like_id = $likeTable->addLike( $resource , $viewer )->like_id ;
            if ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitelike' ) )
              Engine_Api::_()->sitelike()->setLikeFeed( $viewer , $resource ) ;
          }

          //PASS THE LIKE ID VALUE.
          $this->view->like_id = $like_id ;
          $db->commit() ;
        }
        catch ( Exception $e ) {
          $db->rollBack() ;
          throw $e ;
        }
        $like_msg = Zend_Registry::get( 'Zend_Translate' )->_( 'Successfully Liked.' ) ;
      }
      else {
        $this->view->like_id = $like_id_temp[0]['like_id'] ;
      }
    }
    else {
    
			//START PAGE MEMBER PLUGIN WORK
			if ($resource_type == 'sitepage_page' && $sitepageVersion >= '4.3.0p1') {
				if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepagemember' )) {
					//Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Leave');
				}
				
			}
			//END PAGE MEMBER PLUGIN WORK
			
			//START DELETE NOTIFICATION
      Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?'  => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $resource_type, 'object_id = ?' => $resource_id));
			//END DELETE NOTIFICATION
			
      //START UNLIKE WORK.
      //HERE 'PAGE OR LIST PLUGIN' CHECK WHEN UNLIKE
      if ( !empty( $resource ) && isset( $resource->like_count ) ) {
        $resource->like_count-- ;
        $resource->save() ;
      }
      $contentTable = Engine_Api::_()->getDbTable( 'likes' , 'core' )->delete( array ( 'like_id =?' => $like_id ) ) ;
      //END UNLIKE WORK.
      //REMOVE LIKE ACTIVITY FEED.
      if ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitelike' ) )
      Engine_Api::_()->sitelike()->removeLikeFeed( $viewer , $resource ) ;
      $like_msg = Zend_Registry::get( 'Zend_Translate' )->_( 'Successfully Unliked.' ) ;
    }
    if ( empty( $status ) ) {
      $this->_forward( 'success' , 'utility' , 'core' , array (
        'smoothboxClose' => true ,
        'parentRefresh' => true ,
        'messages' => array ( $like_msg )
          )
      ) ;
    }
    //HERE THE CONTENT TYPE MEANS MODULE NAME
    $num_of_contenttype = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($resource_type , $resource_id);
    $likes_number = $this->view->translate( array ( '%s like' , '%s likes' , $num_of_contenttype ) , $this->view->locale()->toNumber( $num_of_contenttype ) ) ;
    $this->view->num_of_like = "<a href='javascript:void(0);' onclick='showSmoothBox(); return false;' >".$likes_number."</a>";

  }
  
  //ACTION FOR LIKES THE LISTING
  public function likelistAction() {

		//GET SETTINGS
    $like_user_str = 0;
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');

		$this->view->call_status = $call_status = $this->_getParam('call_status');
    $this->view->page = $page = $this->_getParam('page', 1);

    $search = $this->_getParam('search', '');
    $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);

		$this->view->search = $search;
    //if (empty($search)) {
     // $this->view->search = $this->view->translate('Search Members');
    //}

		$likeTableName = Engine_Api::_()->getItemTable('core_like')->info('name');

		$memberTableName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');

		$userTable = Engine_Api::_()->getItemTable('user');
		$userTableName = $userTable->info('name');

    if ($call_status == 'friend') {

      $sub_status_select = $userTable->select()
              ->setIntegrityCheck(false)
              ->from($likeTableName, array('poster_id'))
              ->joinInner($memberTableName, "$memberTableName . user_id = $likeTableName . poster_id", NULL)
              ->joinInner($userTableName, "$userTableName . user_id = $memberTableName . user_id")
              ->where($memberTableName . '.resource_id = ?', $viewer_id)
              ->where($memberTableName . '.active = ?', 1)
              ->where($likeTableName . '.resource_type = ?', $resource_type)
              ->where($likeTableName . '.resource_id = ?', $resource_id)
              ->where($likeTableName . '.poster_id != ?', $viewer_id)
              ->where($likeTableName . '.poster_id != ?', 0)
              ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
              ->order('	like_id DESC');
    } else if ($call_status == 'public') {

      $sub_status_select = $userTable->select()
              ->setIntegrityCheck(false)
              ->from($likeTableName, array('poster_id'))
              ->joinInner($userTableName, "$userTableName . user_id = $likeTableName . poster_id")
              ->where($likeTableName . '.resource_type = ?', $resource_type)
              ->where($likeTableName . '.resource_id = ?', $resource_id)
              ->where($likeTableName . '.poster_id != ?', 0)
              ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
              ->order($likeTableName . '.like_id DESC');
    }

    $fetch_sub = Zend_Paginator::factory($sub_status_select);
    $fetch_sub->setCurrentPageNumber($page);
    $fetch_sub->setItemCountPerPage(10);
    $check_object_result = $fetch_sub->getTotalItemCount();

		$this->view->user_obj = array();
    if (!empty($check_object_result)) {
      $this->view->user_obj = $fetch_sub;
    } else {
      $this->view->no_result_msg = $this->view->translate('No results were found.');
    }

    //TOTAL LIKES
    $this->view->public_count = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($resource_type, $resource_id);

    //NUMBER OF FRIENDS LIKES
    $this->view->friend_count = Engine_Api::_()->getApi('like', 'seaocore')->userFriendNumberOflike($resource_type, $resource_id, 'friendNumberOfLike');

    //GET LIKE TITLE
    if ($resource_type == 'member') {
      $this->view->like_title = Engine_Api::_()->getItem('user', $resource_id)->displayname;
    } else {
      $this->view->like_title = Engine_Api::_()->getItem($resource_type, $resource_id)->title;
    }
  }
}