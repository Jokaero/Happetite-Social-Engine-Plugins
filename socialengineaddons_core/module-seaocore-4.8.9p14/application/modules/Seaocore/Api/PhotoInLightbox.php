<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoInLightbox.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_PhotoInLightbox extends Core_Api_Abstract {

  /**
   * Get View Link
   *
   * @param string $photo
   * @param array $params
   * @return link
   */
  public function getSocialEngineAddOnsLightBoxPhotoHref($photo, $params = array()) {
    if (!$this->isLessThan417AlbumModule() && isset($params['album'])) {
      $params = array_merge(array(
          'route' => 'seaocore_image_specific',
          'reset' => true,
          'album_id' => $photo->album_id,
          'photo_id' => $photo->photo_id,
              ), $params);
    } else {
      $params = array_merge(array(
          'route' => 'seaocore_image_specific',
          'reset' => true,
          'album_id' => $photo->collection_id,
          'photo_id' => $photo->photo_id,
              ), $params);
    }
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * get the previous photo
   */
  public function getSocialEngineAddOnsPrevPhoto($current_photo, $params) {

    if (!isset($params['type'])) {
      if (!$this->isLessThan417AlbumModule() && isset($params['album'])) {
        return $current_photo->getPreviousPhoto();
      } else {
        return $current_photo->getPrevCollectible();
      }
    } else {
      return $this->getSocialEngineAddOnsPhoto($current_photo, $params, -1);
    }
  }

  /**
   * get the next photo
   */
  public function getSocialEngineAddOnsNextPhoto($current_photo, $params) {
    if (!isset($params['type'])) {
      if (!$this->isLessThan417AlbumModule() && isset($params['album'])) {
        return $current_photo->getNextPhoto();
      } else {
        return $current_photo->getNextCollectible();
      }
    } else {
      return $this->getSocialEngineAddOnsPhoto($current_photo, $params, 1);
    }
  }

  /**
   * get the photo next/ previoues
   */
  public function getSocialEngineAddOnsPhoto($collectible, $params=array(), $direction) {

    if (!isset($params['offset']) || empty($params['offset']))
      $index = $this->getSocialEngineAddOnsgetCollectibleIndex($collectible, $params);
    else
      $index = $params['offset'];


    $index = $index + (int) $direction;

    $select = $this->getSocialEngineAddOnsCollectibleSql($collectible, $params);

    // Check index bounds
    $count = $params['count'];
    if ($index >= $count) {
      $index -= $count;
    } else if ($index < 0) {
      $index += $count;
    }

    $select->limit(1, (int) $index);

    $rowset = $this->_table->fetchAll($select);
    if (null === $rowset) {
      // @todo throw?
      return null;
    }
    $row = $rowset->current();
    return Engine_Api::_()->getItem($collectible->getType(), $row->photo_id);
  }

  /**
   * get the current photo index
   */
  public function getSocialEngineAddOnsgetCollectibleIndex($collectible, $params=array()) {
    $select = $this->getSocialEngineAddOnsCollectibleSql($collectible, $params);

    $i = 0;
    $index = 0;
    if (isset($params['count']) && !empty($params['count'])) {
      $select->limit($params['count']);
    }

    $rows = $this->_table->fetchAll($select);

    foreach ($rows as $row) {
      if ($row->getIdentity() == $collectible->getIdentity()) {
        $index = $i;
        break;
      }
      $i++;
    }
    return $index;
  }

  /**
   * Get sql
   *
   * @param string $collectible
   * @param array $params
   * @return sql
   */
  public function getSocialEngineAddOnsCollectibleSql($collectible, $params=array()) {

    //GET PARENT NAME
    $parentName = Engine_Api::_()->getItemTable($collectible->getParent()->getType())->info('name');

    //CHANGE PARENT NAME FOR SITEPAGE
    if($parentName == 'engine4_sitepage_pages') {
			$parentName = 'engine4_sitepage_albums';
    } elseif($parentName == 'engine4_sitebusiness_businesses') {
      $parentName = 'engine4_sitebusiness_albums';
    }elseif($parentName == 'engine4_sitegroup_groups') {
      $parentName = 'engine4_sitegroup_albums';
    }elseif($parentName == 'engine4_sitestore_stores') {
      $parentName = 'engine4_sitestore_albums';
    }

    //GET RESOURCE TYPE    
    $resourceType = $collectible->getType();

    //GET TABLE INFO
    $this->_table = $table = Engine_Api::_()->getItemTable($resourceType);

    //GET TABLE NAME
    $tableName = $table->info('name');

    //GET COLUMN
    $col = current($table->info("primary"));

    //SELECT 
    $select = $table->select()->from($tableName, $col);

    if ($resourceType == 'album_photo' && !$this->isLessThan417AlbumModule()) {
      $select->join($parentName, $parentName . '.album_id=' . $tableName . '.album_id', null);
    } else {
      $select->join($parentName, $parentName . '.album_id=' . $tableName . '.collection_id', null);
    }
    
    //GET TYPE
    $type = $params['type'];

    $urlaction = '';
    if (isset($params['urlaction']))
      $urlaction = $params['urlaction'];

    if($urlaction != 'likes' && $urlaction != 'myfriendslike' && $urlaction != 'mycontent' && $urlaction != 'mylikes' && $urlaction != 'recent' && $urlaction != 'popular') {
      if (isset($params['page_id']) && !empty($params['page_id'])) {
        $select->
                where($tableName . '.page_id = ?', $params['page_id']);
      } elseif(isset($params['business_id']) && !empty($params['business_id'])) {
        $select->
                where($tableName . '.business_id = ?', $params['business_id']);
      } elseif(isset($params['group_id']) && !empty($params['group_id'])) {
        $select->
                where($tableName . '.group_id = ?', $params['group_id']);
      }elseif(isset($params['store_id']) && !empty($params['store_id'])) {
        $select->
                where($tableName . '.store_id = ?', $params['store_id']);
      }
    }

    switch ($type) {
      case 'like_count':
          case 'likesphotos':
        $likeTableName = 'engine4_core_likes';
				if ($resourceType == 'ynevent_photo')
							$resourceType = "event_photo";
				if ($resourceType == 'advgroup_photo')
					$resourceType = "group_photo";
        if ($resourceType == "list_photo" || $resourceType == "recipe_photo" || $resourceType == "sitepagenote_photo" || $resourceType == "sitepage_photo" || $resourceType == "album_photo" || $resourceType == "group_photo" || $resourceType == "event_photo" || $resourceType == "sitebusinessnote_photo" || $resourceType == "sitebusiness_photo" || $resourceType == "sitegroupnote_photo" || $resourceType == "sitegroup_photo" || $resourceType == "sitestorenote_photo" || $resourceType == "sitestore_photo") {
          $tableName = Engine_Api::_()->getItemTable($resourceType)->info('name');
          $select->join($likeTableName, $likeTableName . '.resource_id 	=' . $tableName . '.photo_id', null);
          $select->where($likeTableName . '.resource_type = ?', $resourceType);
          if (!empty($urlaction) && $urlaction == 'myfriendslike') {
            //GET THE RESOURCE TYPE AND RESOURCE ID AND VIEWER.
            $user = Engine_Api::_()->user()->getViewer();
            //GET THE FRIEND OF LOGIN USER.
            $user_id = $user->membership()->getMembershipsOfIds();
            $select->where($likeTableName . '.poster_id IN (?)', (array) $user_id);
          } elseif (!empty($urlaction) && $urlaction == 'mylikes') {
            $select->where($likeTableName . '.poster_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
          } elseif (!empty($urlaction) && $urlaction == 'mycontent') {
            if ($resourceType == 'album_photo') {
              $select->where($tableName . ".owner_id = ?", Engine_Api::_()->user()->getViewer()->getIdentity());
            } else {
              $select->where($tableName . ".user_id = ?", Engine_Api::_()->user()->getViewer()->getIdentity());
            }
          }
          if ($resourceType == 'sitepage_photo' || $resourceType == 'sitebusiness_photo' || $resourceType == 'sitegroup_photo' || $resourceType == 'sitestore_photo') {
            $select
                    ->where($tableName . '.like_count > ?', 0)
                    ->order($tableName . '.like_count DESC');
          }
          $select->group($tableName . '.photo_id');
        }
        break;
      case 'comment_count':
        $select
                ->where($tableName . '.comment_count > ?', 0)
                ->order($tableName . '.comment_count DESC');
        break;
      case 'view_count':
        $select
                ->where($tableName . '.view_count > ?', 0)
                ->order($tableName . '.view_count DESC');
        break;
      case 'creation_date':
        if(isset($params['owner_id']))
        $select->
                  where($tableName . '.user_id <> ?', $params['owner_id']);
        $select
                ->order($tableName . '.creation_date DESC');
        break;
      case 'strip_creation_date':
        $select->
                where($tableName . '.photo_hide = ?', 0);

        $select
                ->order($tableName . '.creation_date DESC');
        break;
      default :
        $select->order($tableName . ".$type DESC");
        break;
    }

    return $select;
  }

  /**
   * Check lightbox is enable or not for photos show
   * @return bool
   */
  public function showSocialEngineAddOnsLightBoxPhoto() {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.show', 1);
  }

  /**
   * Checking the album version
   * @return bool
   */
  public function isLessThan417AlbumModule() {
    $albumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('album');
    if($albumModule) {
			$albumModuleVersion = $albumModule->version;
			if ($albumModuleVersion < '4.1.7') {
				return true;
			} else {
				return false;
			}
   }
   return false;
  }

}

?>