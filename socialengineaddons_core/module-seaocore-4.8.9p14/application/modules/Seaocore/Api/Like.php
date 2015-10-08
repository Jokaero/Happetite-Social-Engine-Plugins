<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_Like extends Core_Api_Abstract {

  /**
   * check the item is like or not.
   *
   * @param Stirng $resource_type
   * @param Int $resource_id
   * @return results
   */
  public function hasLike( $resource_type , $resource_id ) {

    //GET THE VIEWER.
    $viewer = Engine_Api::_()->user()->getViewer() ;
    $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $likeTableName = $likeTable->info( 'name' ) ;
    $sub_status_select = $likeTable->select()
            ->from( $likeTableName , array ( 'like_id' ) )
            ->where( 'resource_type = ?' , $resource_type )
            ->where( 'resource_id = ?' , $resource_id )
            ->where( 'poster_type =?' , $viewer->getType() )
            ->where( 'poster_id =?' , $viewer->getIdentity() )
            ->limit( 1 ) ;
    return $sub_status_select->query()->fetchAll() ;
  }


  /**
   * Function for showing 'Number of Likes'.
   *
   * @param Stirng $resource_type
   * @param Int $resource_id
   * @return number of likes
   */
  public function likeCount( $resource_type , $resource_id ) {

    //GET THE VIEWER (POSTER) AND RESOURCE.
    $poster = Engine_Api::_()->user()->getViewer() ;
    $resource = Engine_Api::_()->getItem( $resource_type , $resource_id ) ;
    return Engine_Api::_()->getDbtable( 'likes' , 'core' )->getLikeCount( $resource , $poster ) ;
  }
  
  /**
   * THIS FUNCTION SHOW PEOPLE LIKES.
   *
   * @param String $resource_type
   * @param Int $resource_id
   * @param int $limit
   * @return array of result
   */
  public function peopleLike($resource_type, $resource_id, $limit = null) {

    $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $likeTableName = $likeTable->info( 'name' ) ;
    $select = $likeTable->select()
            ->from( $likeTableName , array ( 'poster_id' ) )
            ->where( 'resource_type = ?' , $resource_type )
            ->where( 'resource_id = ?' , $resource_id )
            ->order( 'like_id DESC' );

    if($limit)
       $select->limit( $limit ) ;

		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }

    return $select->query()->fetchAll() ;
  }

  /**
   * THIS FUNCTION SHOW PEOPLE LIKES OR FRIEND LIKES.
   *
   * @param String $call_status
   * @param String $resource_type
   * @param int $resource_id
   * @param Int $user_id
   * @param Int $search
   * @return results
   */
  public function friendPublicLike($call_status, $resource_type, $resource_id, $user_id, $search) {

    $likeTableName = Engine_Api::_()->getItemTable('core_like')->info( 'name' ) ;
    $membershipTableName = Engine_Api::_()->getDbtable( 'membership' , 'user' )->info( 'name' ) ;
    
    $userTable = Engine_Api::_()->getItemTable( 'user' ) ;
    $userTableName = $userTable->info( 'name' ) ;

    $select = $userTable->select()
            ->setIntegrityCheck( false )
            ->from( $likeTableName , array ( 'poster_id' ) )
            ->where( $likeTableName . '.resource_type = ?' , $resource_type )
            ->where( $likeTableName . '.resource_id = ?' , $resource_id )
            ->where( $likeTableName . '.poster_id != ?' , 0 )
            ->where( $userTableName . '.displayname LIKE ?' , '%' . $search . '%' )
            ->order( 'like_id DESC' ) ;

    if ( $call_status == 'friend' || $call_status == 'myfriendlikes' ) {
      $select->joinInner( $membershipTableName , "$membershipTableName . resource_id = $likeTableName . poster_id" , NULL )
          ->joinInner( $userTableName , "$userTableName . user_id = $membershipTableName . resource_id" )
          ->where( $membershipTableName . '.user_id = ?' , $user_id )
          ->where( $membershipTableName . '.active = ?' , 1 )
          ->where( $likeTableName . '.poster_id != ?' , $user_id ) ;
    }
    else if ( $call_status == 'public' ) {
      $select->joinInner( $userTableName , "$userTableName . user_id = $likeTableName . poster_id" ) ;
    }
    return $select;
  }

  /**
   * THIS FUNCTION USE FOR USER OR FRIEND NUMBER OF LIKES.
   *
   * @param String $resource_type
   * @param Int $resource_id
   * @param String $params
   * @param Int $limit
   * @return count results
   */
  public function userFriendNumberOflike($resource_type, $resource_id, $params, $limit = null) {

    //GET THE USER ID.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    
    $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $likeTableName = $likeTable->info( 'name' ) ;

    $memberTableName = Engine_Api::_()->getDbtable( 'membership' , 'user' )->info( 'name' ) ;

    $select = $likeTable->select();
    
    if ( $params == 'friendNumberOfLike' ) {
      $select->from($likeTableName , array ( 'COUNT(' . $likeTableName . '.like_id) AS like_count'));
    }
    elseif ( $params == 'userFriendLikes' ) {
      $select->from( $likeTableName , array ( 'poster_id' ) ) ;
    }
    
    $select->joinInner($memberTableName, "$memberTableName . resource_id = $likeTableName . poster_id" , NULL)
					->where( $memberTableName . '.user_id = ?' , $viewer_id )
					->where( $memberTableName . '.active = ?' , 1 )
					->where( $likeTableName . '.resource_type = ?' , $resource_type )
					->where( $likeTableName . '.resource_id = ?' , $resource_id )
					->where( $likeTableName . '.poster_id != ?' , $viewer_id )
					->where( $likeTableName . '.poster_id != ?' , 0 ) ;

    if ( $params == 'friendNumberOfLike' ) {
      $select->group( $likeTableName . '.resource_id' ) ;
    }
    elseif ( $params == 'userFriendLikes' ) {
      $select->order( $likeTableName . '.like_id DESC' )->limit( $limit ) ;
    }
    //$fetch_count = $select->query()->fetchAll() ;
    $fetch_count = $select ->query()->fetchColumn();
    
    if (!empty($fetch_count)) {
      return $fetch_count;
    } 
    else {
      return 0;
    }
  }

}