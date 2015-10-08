<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Follows.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Seaocore_Model_DbTable_Follows extends Engine_Db_Table
{
  protected $_rowClass = 'Seaocore_Model_Follow';

  protected $_custom = false;

  public function __construct($config = array())
  {
    if( get_class($this) !== 'Seaocore_Model_DbTable_Follows') {
      $this->_custom = true;
    }

    parent::__construct($config);
  }

  public function getFollowTable()
  {
    return $this;
  }

  public function addFollow(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    $row = $this->getFollow($resource, $poster);
    if( null !== $row )
    {
      throw new Core_Model_Exception('Already Followd');
    }

    $table = $this->getFollowTable();
    $row = $table->createRow();

    if( isset($row->resource_type) )
    {
      $row->resource_type = $resource->getType();
    }

    $row->resource_id = $resource->getIdentity();
    $row->poster_type = $poster->getType();
    $row->poster_id = $poster->getIdentity();
    $row->save();

    if( isset($resource->follow_count) )
    {
      $resource->follow_count++;
      $resource->save();
    }

    return $row;
  }

  public function removeFollow(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    $row = $this->getFollow($resource, $poster);
    if( null === $row )
    {
      throw new Core_Model_Exception('No follow to remove');
    }

    $row->delete();

    if( isset($resource->follow_count) )
    {
      $resource->follow_count--;
      $resource->save();
    }

    return $this;
  }

  public function isFollow(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    return ( null !== $this->getFollow($resource, $poster) );
  }

  public function getFollow(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster)
  {
    $table = $this->getFollowTable();
    $select = $this->getFollowSelect($resource)
      ->where('poster_type = ?', $poster->getType())
      ->where('poster_id = ?', $poster->getIdentity())
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getFollowSelect(Core_Model_Item_Abstract $resource)
  {
    $select = $this->getFollowTable()->select();

    if( !$this->_custom )
    {
      $select->where('resource_type = ?', $resource->getType());
    }

    $select
      ->where('resource_id = ?', $resource->getIdentity())
      ->order('follow_id ASC');

    return $select;
  }

  public function getFollowPaginator(Core_Model_Item_Abstract $resource)
  {
    $paginator = Zend_Paginator::factory($this->getFollowSelect($resource));
    $paginator->setItemCountPerPage(3);
    $paginator->count();
    $pages = $paginator->getPageRange();
    $paginator->setCurrentPageNumber($pages);
    return $paginator;
  }

  public function getFollowCount(Core_Model_Item_Abstract $resource)
  {
    if( isset($resource->follow_count) )
    {
      return $resource->follow_count;
    }

    $select = new Zend_Db_Select($this->getFollowTable()->getAdapter());
    $select
      ->from($this->getFollowTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

    if( !$this->_custom )
    {
      $select->where('resource_type = ?', $resource->getType());
    }

    $select->where('resource_id = ?', $resource->getIdentity());

    $data = $select->query()->fetchAll();
    return (int) $data[0]['count'];
  }

  public function getAllFollows(Core_Model_Item_Abstract $resource)
  {
    return $this->getFollowTable()->fetchAll($this->getFollowSelect($resource));
  }

  public function getAllFollowsUsers(Core_Model_Item_Abstract $resource)
  {
    $table = $this->getFollowTable();
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), array('poster_type', 'poster_id'));

    if( !$this->_custom )
    {
      $select->where('resource_type = ?', $resource->getType());
    }

    $select->where('resource_id = ?', $resource->getIdentity());

    $users = array();
    foreach($select->query()->fetchAll() as $data )
    {
      if($data['poster_type'] == 'user')
      {
        $users[] = $data['poster_id'];
      }
    }
    $users = array_values(array_unique($users));

    return Engine_Api::_()->getItemMulti('user', $users);
  }
  
  public function getFollowers($resource_type, $resource_id, $owner_id) {
    
    $followTableName = $this->info('name');
    $select = $this->select() 
                    ->from($followTableName, 'poster_id')
                    ->where('poster_id != ?', $owner_id)
                    ->where('poster_type = ?', 'user')
                    ->where('resource_type = ?', $resource_type)
                    ->where('resource_id = ?', $resource_id)
                    ;
    return $this->fetchAll($select);
  }

  /**
   * THIS FUNCTION SHOW PEOPLE FOLLOWS OR FRIEND FOLLOWS.
   *
   * @param String $call_status
   * @param String $resource_type
   * @param int $resource_id
   * @param Int $user_id
   * @param Int $search
   * @return results
   */
  public function getFollowDetails($call_status, $resource_type, $resource_id, $user_id, $search) {

    //GET FOLLOW TABLE
    $followTable = Engine_Api::_()->getItemTable('seaocore_follow') ;
    $followTableName = $followTable->info('name') ;
    
    //GET MEMBERSHIP TABLE
    $membershipTable = Engine_Api::_()->getDbtable('membership' , 'user') ;
    $memberName = $membershipTable->info('name') ;
    
    //GET USER TABLE
    $userTable = Engine_Api::_()->getItemTable('user') ;
    $userTableName = $userTable->info('name') ;

    //MAKE QUERY
    $select = $userTable->select()
            ->setIntegrityCheck( false )
            ->from($followTableName , array ('poster_id') )
            ->where($followTableName . '.resource_type = ?' , $resource_type )
            ->where($followTableName . '.resource_id = ?' , $resource_id)
            ->where($followTableName . '.poster_id != ?' , 0 )
            ->where($userTableName . '.displayname LIKE ?' , '%' . $search . '%')
            ->order('follow_id DESC') ;

    if ($call_status == 'friend' || $call_status == 'myfriendfollows') {
      $select->joinInner($memberName , "$memberName . resource_id = $followTableName . poster_id" , NULL )
          ->joinInner($userTableName , "$userTableName . user_id = $memberName . resource_id")
          ->where($memberName . '.user_id = ?' , $user_id)
          ->where($memberName . '.active = ?' , 1 )
          ->where($followTableName . '.poster_id != ?' , $user_id) ;
    }
    else if ($call_status == 'public') {
      $select->joinInner($userTableName , "$userTableName . user_id = $followTableName . poster_id") ;
    }

    return Zend_Paginator::factory($select);
  }

  /**
   * Function for showing 'Number of Follows'.
   *
   * @param Stirng $resource_type
   * @param Int $resource_id
   * @return number of follows
   */
  public function numberOfFollow($resource_type , $resource_id) {

    //RETURN FOLLOW COUNT
    return Engine_Api::_()->getItem($resource_type , $resource_id)->follow_count ;
  }

  /**
   * Function for showing 'Friend number of Follows'.
   *
   * @param Stirng $resource_type
   * @param Int $resource_id
   * @return friend number of follows
   */
  public function numberOfFriendsFollow($resource_type, $resource_id) {

    $fetch_count = $this->userFriendNumberOffollow($resource_type, $resource_id, 'friendNumberOfFollow') ;
    if (!empty($fetch_count )) {
      return $fetch_count[0]['follow_count'] ;
    }
    
    return 0 ;
  }

  /**
   * THIS FUNCTION USE FOR USER OR FRIEND NUMBER OF FOLLOWS.
   *
   * @param String $resource_type
   * @param Int $resource_id
   * @param String $functioName
   * @param Int $limit
   * @return count results
   */
  public function userFriendNumberOffollow($resource_type, $resource_id, $functioName, $limit = null ) {

    //GET VIEWER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    
    //GET FOLLOW TABLE
    $followTable = Engine_Api::_()->getItemTable('seaocore_follow') ;
    $followTableName = $followTable->info('name');
    
    //GET MEMBERSHIP TABLE
    $membershipTable = Engine_Api::_()->getDbtable('membership' , 'user') ;
    $membershipTableName = $membershipTable->info('name') ;

    //MAKE QUERY
    $select = $followTable->select() ;
    if ($functioName == 'friendNumberOfFollow') {
      $select->from($followTableName , array('COUNT(' . $followTableName . '.follow_id) AS follow_count')) ;
    }
    elseif ($functioName == 'userFriendFollows') {
      $select->from($followTableName , array('poster_id') ) ;
    }
    
    $select->joinInner($membershipTableName , "$membershipTableName . resource_id = $followTableName . poster_id" , NULL)
        ->where($membershipTableName . '.user_id = ?' , $user_id)
        ->where($membershipTableName . '.active = ?' , 1 )
        ->where($followTableName . '.resource_type = ?' , $resource_type )
        ->where($followTableName . '.resource_id = ?' , $resource_id)
        ->where($followTableName . '.poster_id != ?' , $user_id)
        ->where($followTableName . '.poster_id != ?' , 0 ) ;

    if ($functioName == 'friendNumberOfFollow') {
      $select->group($followTableName . '.resource_id') ;
    }
    elseif ($functioName == 'userFriendFollows') {
      $select->order($followTableName . '.follow_id DESC')
          ->limit($limit) ;
    }
    
    return $select->query()->fetchAll() ;
  }    
}
