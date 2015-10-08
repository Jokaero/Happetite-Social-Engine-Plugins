<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Api_Core extends Core_Api_Abstract {

  public function getSpecialAlbum($subject, $type, $moduleName) {
    $primaryTableKey = Engine_Api::_()->getItemtable($subject->getType())->info('primary');
    $tablePrimaryFieldName = $primaryTableKey[1];

    if ($moduleName != 'album') {
      $tableAlbum = Engine_Api::_()->getItemTable($moduleName . "_album");
    } else {
      $tableAlbum = Engine_Api::_()->getItemtable("album");
    }


    $primaryKeyId = $subject->getIdentity();
    $select = $tableAlbum->select()
            ->where("$tablePrimaryFieldName = ?", $primaryKeyId)
            ->order('album_id ASC')
            ->limit(1);

    if ($this->checkConditionsForAlbum($moduleName)) {
      $select->where('type = ?', $type);
    }

    $album = $tableAlbum->fetchRow($select);

    //CREATE PHOTOS ALBUM IF IT DOESN't EXIST YET
    if (null === $album) {
      $album = $tableAlbum->createRow();
      if (isset($album->default_value))
        $album->default_value = 0;
      $album->$tablePrimaryFieldName = $primaryKeyId;
      if (isset($album->owner_id) && $subject->owner_id) {
        $album->owner_id = $subject->owner_id;
      } elseif (isset($album->user_id) && $subject->user_id) {
        $album->user_id = $subject->user_id;
      }
      $album->title = Zend_Registry::get('Zend_Translate')->_(ucfirst($type) . ' Photos');
      $album->type = $type;
      $album->search = 1;
      $album->save();
    }
    return $album;
  }

  public function checkConditionsForAlbum($moduleName) {

    //CHECK CONDITIONS FOR IN WHICH ALBUMS ARE GENERETED
    $checkConditionsForAlbum = 0;
    $moduleArray = array('sitepage', 'sitebusiness', 'sitegroup', 'sitestore');
    if (in_array($moduleName, $moduleArray)) {
      $checkConditionsForAlbum = 1;
    }

    return $checkConditionsForAlbum;
  }

  public function showContentOptions($key) {

    $showContent_timeline = array("mainPhoto" => "Content Profile Photo", "title" => "Content Title", "likeCount" => "Total Likes", "optionsButton" => "Options Button (Edit Details, Delete, etc.)");
    switch ($key) {
      case 'album':
        $showContent_timeline = array("mainPhoto" => "Album Profile Photo", "title" => "Album Title", "owner" => "Album Owner", 'description' => "Description", "totalPhotos" => "Total Photos", "viewCount" => "Total Views", "likeCount" => "Total Likes","commentCount" => "Total Comments","location" => "Location", "CategoryLink" => "Category", 'creationDate' => 'Creation Date', 'updateDate' => "Update Date","rating" => "Rating", "optionsButton" => "Options Button (Edit, Delete, etc.)", "shareOptions" => "Share Options (Facebook, Linkedin, Twitter, etc.)");
        break;
      case 'sitepage_page':
        $showContent_timeline = array("mainPhoto" => "Page Profile Photo", "title" => "Page Title", "followButton" => "Follow Button", "likeCount" => "Total Likes", "followCount" => "Total Followers", "optionsButton" => "Options Button (Edit Page Details, Edit Page Layout, etc.)", "featured" => "Featured", "sponsored" => "Sponsored", "shareOptions" => "Share Options (Facebook, Linkedin, Twitter, etc.)");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
          $showContent_timeline['memberCount'] = 'Total Members';
          $showContent_timeline['addButton'] = 'Add People Button';
          $showContent_timeline['joinButton'] = 'Join Page Button';
          $showContent_timeline['leaveButton'] = 'Leave Page Button';
        }
        break;
      case 'sitebusiness_business':
        $showContent_timeline = array("mainPhoto" => "Business Profile Photo", "title" => "Business Title", "followButton" => "Follow Button", "likeCount" => "Total Likes", "followCount" => "Total Followers", "optionsButton" => "Options Button (Edit Business Details, Edit Business Layout, etc.)", "featured" => "Featured", "sponsored" => "Sponsored", "shareOptions" => "Share Options (Facebook, Linkedin, Twitter, etc.)");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) {
          $showContent_timeline['memberCount'] = 'Total Members';
          $showContent_timeline['addButton'] = 'Add People Button';
          $showContent_timeline['joinButton'] = 'Join Business Button';
          $showContent_timeline['leaveButton'] = 'Leave Business Button';
        }
        break;
      case 'sitegroup_group':
        $showContent_timeline = array("mainPhoto" => "Group Profile Photo", "title" => "Group Title", "followButton" => "Follow Button", "likeCount" => "Total Likes", "followCount" => "Total Followers", "optionsButton" => "Options Button (Edit Group Details, Edit Group Layout, etc.)", "featured" => "Featured", "sponsored" => "Sponsored", "shareOptions" => "Share Options (Facebook, Linkedin, Twitter, etc.)");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
          $showContent_timeline['memberCount'] = 'Total Members';
          $showContent_timeline['addButton'] = 'Add People Button';
          $showContent_timeline['joinButton'] = 'Join Group Button';
          $showContent_timeline['leaveButton'] = 'Leave Group Button';
        }
        break;
      case 'sitestore_store':
        $showContent_timeline = array("mainPhoto" => "Store Profile Photo", "title" => "Store Title", "followButton" => "Follow Button", "likeCount" => "Total Likes", "followCount" => "Total Followers", "optionsButton" => "Options Button (Edit Store Details, Edit Store Layout, etc.)", "featured" => "Featured", "sponsored" => "Sponsored", "shareOptions" => "Share Options (Facebook, Linkedin, Twitter, etc.)");
        break;
      case 'sitestoreproduct_product':
        $showContent_timeline = array("mainPhoto" => "Store Product Profile Photo", "title" => "Store Product Title", "likeCount" => "Total Likes", "optionsButton" => "Options Button (Edit Details, Add Best Alternatives, etc.)", "featured" => "Featured", "sponsored" => "Sponsored", "newlabel" => "New");
        break;
      case 'sitereview_listing':
        $showContent_timeline = array("mainPhoto" => "Listing Profile Photo", "title" => "Listing Title", "likeCount" => "Total Likes", "optionsButton" => "Options Button (Edit Details, Add Best Alternatives, etc.)", "featured" => "Featured", "sponsored" => "Sponsored", "newlabel" => "New");
        break;
      case 'siteevent_event':
        $showContent_timeline = array("mainPhoto" => "Event Profile Photo", "title" => "Event Title", "followButton" => "Follow Button", "likeCount" => "Total Likes", "followCount" => "Total Followers", "joinButton" => "Join Button", "inviteGuest" => 'Invite Guests', "updateInfoButton" => "Dashboard Button", "inviteRsvpButton" => "RSVP", "optionsButton" => "Options Button (Edit Details, Add to Diary, etc.)", "venue" => "Venue", "startDate" => "Event Start Date and Time", "endDate" => "Event End Date and Time", "location" => "Event Location", "category" => "Category", "price" => "Price", "ledBy" => "Led By", "featured" => "Featured", "sponsored" => "Sponsored", "newlabel" => "New", "hostName" => "Hosted by", "addToMyCalendar" => "Add to My Calendar", "shareOptions" => "Share Options (Facebook, Linkedin, Twitter, etc.)");
        //CHECK IF SITEEVENTREPEAT MODULE IS ENABLED THEN ADD 2 MORE SETITNGS.
        $siteeventrepeat = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
        if ($siteeventrepeat) {
          $siteeventrepeat_settings = array("showrepeatinfo" => "Event Types (Daily, Weekly, Monthly) and Time");
          $showContent_timeline = array_merge($showContent_timeline, $siteeventrepeat_settings);
        }
        break;
    }

    return $showContent_timeline;
  }

  public function showMobileContentOptions($key) {

    $showContent_timeline = array("mainPhoto" => "Content Profile Photo", "title" => "Content Title", "likeCount" => "Total Likes");
    switch ($key) {
        case 'album':
        $showContent_timeline = array("mainPhoto" => "Album Profile Photo", "title" => "Album Title", "owner" => "Album Owner", 'description' => "Description", "totalPhotos" => "Total Photos", "viewCount" => "Total Views", "likeCount" => "Total Likes","commentCount" => "Total Comments","location" => "Location", "CategoryLink" => "Category", 'creationDate' => 'Creation Date', 'updateDate' => "Update Date","rating" => "Rating");
        break;
      case 'sitepage_page':
        $showContent_timeline = array("mainPhoto" => "Page Profile Photo", "title" => "Page Title", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "followButton" => "Follow", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Page Location", "tags" => "Tags", "price" => "Price", "modifiedDate" => "Last Updated Date", "commentCount" => "Comment Count", "viewCount" => "View Count", "likeCount" => "Like count", "followerCount" => "Follower Count", "memberCount" => "Member Count");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
          $showContent_timeline['addButton'] = 'Add People Button';
          $showContent_timeline['joinButton'] = 'Join Page Button';
          $showContent_timeline['leaveButton'] = 'Leave Page Button';
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
          $showContent_timeline['badge'] = 'Badge';
        }
        break;
      case 'sitebusiness_business':
        $showContent_timeline = array("mainPhoto" => "Business Profile Photo", "title" => "Business Title", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "followButton" => "Follow", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Business Location", "tags" => "Tags", "price" => "Price", "modifiedDate" => "Last Updated Date", "commentCount" => "Comment Count", "viewCount" => "View Count", "likeCount" => "Like count", "followerCount" => "Follower Count", "memberCount" => "Member Count");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) {
          $showContent_timeline['addButton'] = 'Add People Button';
          $showContent_timeline['joinButton'] = 'Join Business Button';
          $showContent_timeline['leaveButton'] = 'Leave Business Button';
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessbadge')) {
          $showContent_timeline['badge'] = 'Badge';
        }
        break;
      case 'sitegroup_group':
        $showContent_timeline = array("mainPhoto" => "Group Profile Photo", "title" => "Group Title", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "followButton" => "Follow", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Group Location", "tags" => "Tags", "price" => "Price", "modifiedDate" => "Last Updated Date", "commentCount" => "Comment Count", "viewCount" => "View Count", "likeCount" => "Like count", "followerCount" => "Follower Count", "memberCount" => "Member Count");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
          $showContent_timeline['addButton'] = 'Add People Button';
          $showContent_timeline['joinButton'] = 'Join Group Button';
          $showContent_timeline['leaveButton'] = 'Leave Group Button';
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
          $showContent_timeline['badge'] = 'Badge';
        }
        break;
      case 'sitestore_store':
        $showContent_timeline = array("mainPhoto" => "Store Profile Photo", "title" => "Store Title", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Store Location", "tags" => "Tags", "price" => "Price");
        break;
      case 'sitestoreproduct_product':
        $showContent_timeline = array("mainPhoto" => "Store Product Profile Photo", "title" => "Store Product Title", "newlabel" => "New Label", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Store Product Location", "tags" => "Tags", "price" => "Price");
        break;
      case 'sitereview_listing':
        $showContent_timeline = array("mainPhoto" => "Listing Profile Photo", "title" => "Listing Title", "newlabel" => "New Label", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Listing Location", "tags" => "Tags", "price" => "Price", "reviewCreate" => "Write a review");
        break;
      case 'siteevent_event':

        $showContent_timeline = array("mainPhoto" => "Event Profile Photo", "title" => "Event Title", "joinButton" => "Join Button", "inviteGuest" => 'Invite Guests', "updateInfoButton" => "Edit Button", "inviteRsvpButton" => "Rsvp", "optionsButton" => "Options", "venue" => "Venue", "startDate" => "Event Start Date and Time", "endDate" => "Event End Date and Time", "location" => "Event Location", "followButton" => "Follow Button","likeButton" => "Like Button","featured" => "Featured","sponsored" => "Sponsored","description" => "About / Description","likeCount" => "Total Likes", "followCount" => "Total Followers","ledBy" => "Led By","hostName" => "Hosted by");
        break;

    }

    return $showContent_timeline;
  }

  public function getSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, $postionParams) {

    if (isset($subject->listingtype_id)) {
      $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $subject->listingtype_id);
      $titleSinLc = strtolower($listingType->title_singular);
      $settinsParams = "sitecontentcoverphoto.$moduleName.$titleSinLc.cover.photo.preview.level.$level_id.params";
    } else {
      $settinsParams = "sitecontentcoverphoto.$moduleName.cover.photo.preview.level.$level_id.params";
    }

    return Engine_Api::_()->getApi("settings", "core")->getSetting($settinsParams, $postionParams);
  }

  public function setSiteContentDefaultSettingsParams($subject, $moduleName, $level_id, $postionParams) {

    if (isset($subject->listingtype_id)) {
      $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $subject->listingtype_id);
      $titleSinLc = strtolower($listingType->title_singular);
      $settinsParams = "sitecontentcoverphoto.$moduleName.$titleSinLc.cover.photo.preview.level.$level_id.params";
    } else {
      $settinsParams = "sitecontentcoverphoto.$moduleName.cover.photo.preview.level.$level_id.params";
    }
    return Engine_Api::_()->getApi("settings", "core")->setSetting($settinsParams, $postionParams);
  }

  public function getSiteContentDefaultSettingsIds($subject, $moduleName, $level_id, $postionParams) {

    if (isset($subject->listingtype_id)) {
      $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $subject->listingtype_id);
      $titleSinLc = strtolower($listingType->title_singular);
      $settinsParams = "sitecontentcoverphoto.$moduleName.$titleSinLc.cover.photo.preview.level.$level_id.id";
    } else {
      $settinsParams = "sitecontentcoverphoto.$moduleName.cover.photo.preview.level.$level_id.id";
    }
    return Engine_Api::_()->getApi("settings", "core")->getSetting($settinsParams, $postionParams);
  }

  public function setSiteContentDefaultSettingsIds($subject, $moduleName, $level_id, $postionParams) {

    if (isset($subject->listingtype_id)) {
      $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $subject->listingtype_id);
      $titleSinLc = strtolower($listingType->title_singular);
      $settinsParams = "sitecontentcoverphoto.$moduleName.$titleSinLc.cover.photo.preview.level.$level_id.id";
    } else {
      $settinsParams = "sitecontentcoverphoto.$moduleName.cover.photo.preview.level.$level_id.id";
    }
    return Engine_Api::_()->getApi("settings", "core")->setSetting($settinsParams, $postionParams);
  }

  public function getUploadPermission($subject, $viewer) {

    if (isset($subject->listingtype_id)) {
      return Engine_Api::_()->authorization()->isAllowed('sitecontentcoverphoto_' . $subject->getType() . '_' . $subject->listingtype_id, $viewer, 'upload');
    } else {
      return Engine_Api::_()->authorization()->isAllowed('sitecontentcoverphoto_' . $subject->getType(), $viewer, 'upload');
    }
  }

}

?>