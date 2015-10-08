<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Installer extends Engine_Package_Installer_Module {

    public function onPreinstall() {
        $db = $this->getDb();
        $PRODUCT_TYPE = 'sitecontentcoverphoto';
        $PLUGIN_TITLE = 'Sitecontentcoverphoto';
        $PLUGIN_VERSION = '4.8.8';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Content Profiles - Cover Photo, Banner & Site Branding Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.9p12';
        $PRODUCT_TITLE = 'Content Profiles - Cover Photo, Banner & Site Branding Plugin';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = @file_exists($file_path);

        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
        } else {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }
        parent::onPreinstall();
    }

    function onInstall() {
        $db = $this->getDb();

        $db->update('engine4_core_modules', array('title' => 'Content Profiles - Cover Photo, Banner & Site Branding Plugin', 'description' => 'Content Profiles - Cover Photo, Banner & Site Branding Plugin'), array('name = ?' => 'sitecontentcoverphoto'));

        $db->update('engine4_core_menuitems', array('label' => 'SEAO - Content Profiles-Cover Photo, Banner & Site Branding'), array('name = ?' => 'core_admin_main_plugins_sitecontentcoverphoto', 'module =?' => 'sitecontentcoverphoto'));


        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitecontentcoverphoto';");

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if (!empty($is_siteevent_object)) {
            $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES
		("siteevent", "siteevent_event", "event_id", 1)');
        }

        // WORK FOR ADVANCED ALBUM PLUGIN
        $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` ( `module`, `resource_type`, `resource_id`, `enabled`) VALUES ( "sitealbum", "album", "album_id", "0");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("album_cover_update", "sitealbum", \'{item:$subject} updated cover photo of the album {item:$object}:\', 1, 3, 2, 1, 1, 1)');

        $db->query("
        INSERT IGNORE INTO `engine4_authorization_permissions` 
        SELECT 
              level_id as `level_id`, 
              'sitecontentcoverphoto_album' as `type`, 
              'upload' as `name`, 
              1 as `value`, 
              NULL as `params` 
        FROM `engine4_authorization_levels` WHERE `type` IN('moderator','admin','user');
      ");

        //START ADVANCED ALBUM PLUGIN IS INSTALL THEN CHANGED LAYOUT.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitealbum')
                ->where('version >= ?', '4.8.5')
                ->where('enabled = ?', 1);
        $is_sitealbum_object = $select->query()->fetchObject();
        if (false) {

            $select = new Zend_Db_Select($db);
            $albumIndexView = $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitealbum_album_view')
                    ->query()
                    ->fetchObject();
            if (!empty($albumIndexView)) {

                $page_id = $albumIndexView->page_id;
                $db->delete('engine4_core_content', array('page_id =?' => $page_id));
                if ($page_id) {
                    $containerCount = 0;
                    $widgetCount = 0;
                    //TOP CONTAINER
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'top',
                        'page_id' => $page_id,
                        'order' => $containerCount++,
                    ));
                    $top_container_id = $db->lastInsertId();

                    //MAIN CONTAINER
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'main',
                        'page_id' => $page_id,
                        'order' => $containerCount++,
                    ));
                    $main_container_id = $db->lastInsertId();

                    //INSERT TOP-MIDDLE
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'middle',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_container_id,
                        'order' => $containerCount++,
                    ));
                    $top_middle_id = $db->lastInsertId();

                    //LEFT CONTAINER
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'right',
                        'page_id' => $page_id,
                        'parent_content_id' => $main_container_id,
                        'order' => $containerCount++,
                    ));
                    $right_container_id = $db->lastInsertId();

                    //MAIN-MIDDLE CONTAINER
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'middle',
                        'page_id' => $page_id,
                        'parent_content_id' => $main_container_id,
                        'order' => $containerCount++,
                    ));
                    $main_middle_id = $db->lastInsertId();

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'core.container-tabs',
                        'parent_content_id' => $main_middle_id,
                        'order' => $containerCount++,
                        'params' => '{"max":"5","title":"","nomobile":"0","name":"core.container-tabs"}',
                    ));
                    $tab_id = $db->lastInsertId('engine4_core_content');


                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.profile-breadcrumb',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitecontentcoverphoto.content-cover-photo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"modulename":"album","showContent_0":"","showContent_album":["mainPhoto","title","owner","description","totalPhotos","viewCount","likeCount","commentCount","directionLink","updateDate","optionsButton"],"showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"235","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                    ));


                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.album-view',
                        'parent_content_id' => $tab_id,
                        'order' => $widgetCount++,
                        'params' => '{"titleCount":true,"itemCountPerPage":"12","margin_photo":"2","photoHeight":"200","photoWidth":"210","columnHeight":"200","photoInfo":["likeCommentStrip"],"show_content":"2","title":"Photos","nomobile":"0","name":"sitealbum.album-view"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.profile-photos',
                        'parent_content_id' => $tab_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"View all albums","titleCount":true,"itemCountPerPage":"24","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","margin_photo":"2","albumPhotoHeight":"195","albumPhotoWidth":"208","photoHeight":"205","photoWidth":"212","albumColumnHeight":"240","photoColumnHeight":"250","selectDispalyTabs":["albums"],"albumInfo":["albumTitle","totalPhotos"],"photoInfo":"","truncationLocation":"50","titleTruncation":"50","nomobile":"0","name":"sitealbum.profile-photos"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.profile-photos',
                        'parent_content_id' => $tab_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"Photos of Owner","titleCount":true,"itemCountPerPage":"24","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","margin_photo":"2","albumPhotoHeight":"195","albumPhotoWidth":"208","photoHeight":"205","photoWidth":"210","albumColumnHeight":"240","photoColumnHeight":"250","selectDispalyTabs":["photosofyou"],"albumInfo":"","photoInfo":["likeCommentStrip"],"truncationLocation":"50","titleTruncation":"50","nomobile":"0","name":"sitealbum.profile-photos"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.profile-photos',
                        'parent_content_id' => $tab_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"Owner\'s Photos","titleCount":true,"itemCountPerPage":"24","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","margin_photo":"2","albumPhotoHeight":"195","albumPhotoWidth":"208","photoHeight":"200","photoWidth":"210","albumColumnHeight":"240","photoColumnHeight":"200","selectDispalyTabs":["yourphotos"],"albumInfo":"","photoInfo":["likeCommentStrip"],"truncationLocation":"50","titleTruncation":"50","nomobile":"0","name":"sitealbum.profile-photos"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.user-ratings',
                        'parent_content_id' => $right_container_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"User Ratings","titleCount":true}',
                    ));
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.make-featured-link',
                        'parent_content_id' => $right_container_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"User Ratings","titleCount":true}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.specification-sitealbum',
                        'parent_content_id' => $right_container_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"Additional Information","titleCount":true,"name":"sitealbum.specification-sitealbum"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.friends-photos',
                        'parent_content_id' => $right_container_id,
                        'order' => $widgetCount++,
                        'params' => '{"itemCountPhoto":"2","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"1","photoHeight":"200","photoWidth":"200","photoInfo":["ownerName","likeCount","commentCount"],"truncationLocation":"50","photoTitleTruncation":"100","title":"Friends\' Photos","nomobile":"0","name":"sitealbum.friends-photos"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.friends-photo-albums',
                        'parent_content_id' => $right_container_id,
                        'order' => $widgetCount++,
                        'params' => '{"itemCountAlbum":"2","itemCountPhoto":"2","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"1","albumInfo":["ownerName","likeCount","commentCount"],"truncationLocation":"50","albumTitleTruncation":"100","title":"Friends\' Albums Photos","nomobile":"0","name":"sitealbum.friends-photo-albums"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'seaocore.social-share-buttons',
                        'parent_content_id' => $right_container_id,
                        'order' => $widgetCount++,
                        'params' => '{"show_buttons":["facebook","twitter","linkedin","plusgoogle","share"],"title":"","nomobile":"0","name":"seaocore.social-share-buttons"}',
                    ));

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitealbum.information-sitealbum',
                        'parent_content_id' => $right_container_id,
                        'order' => $widgetCount++,
                        'params' => '{"title":"Information","titleCount":true,"showContent":["totalPhotos","categoryLink","creationDate","updateDate","viewCount","likeCount","commentCount","location","directionLink","tags","socialShare"],"nomobile":"0","name":"sitealbum.information-sitealbum"}',
                    ));
                }
                $db->query("UPDATE `engine4_sitecontentcoverphoto_modules` SET `enabled` = '1' WHERE `engine4_sitecontentcoverphoto_modules`.`module` ='sitealbum' LIMIT 1 ;");
            }
        }
        //END ADVANCED ALBUM PLUGIN IS INSTALL THEN CHANGED LAYOUT.

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitereview')
                ->where('version <= ?', '4.8.8')
                ->where('enabled = ?', 1);
        $is_sitereview_object = $select->query()->fetchObject();
        if (!empty($is_sitereview_object)) { 
            $sitereview_cover_column = $db->query("SHOW COLUMNS FROM engine4_sitereview_listings LIKE '%_cover'")->fetchAll();
            foreach ($sitereview_cover_column as $sitereview_cover) {
                $columnName = $sitereview_cover['Field'];
                $checkExistColumn = $db->query("SHOW COLUMNS FROM engine4_sitereview_otherinfo LIKE '$columnName'")->fetch();
                if (empty($checkExistColumn)) {
                    $db->query("ALTER TABLE engine4_sitereview_otherinfo add `$columnName` int (11);");
                    $db->query("UPDATE engine4_sitereview_otherinfo eso join engine4_sitereview_listings esl on eso.listing_id = esl.listing_id set eso.`$columnName` = esl.`$columnName`");
                    $db->query("ALTER TABLE engine4_sitereview_otherinfo modify `$columnName` int (11);");
                }
                $checkExistColumnName = $db->query("SHOW COLUMNS FROM engine4_sitereview_listings LIKE '$columnName'")->fetch();
                if (!empty($checkExistColumnName)) {
                    $db->query("ALTER TABLE `engine4_sitereview_listings` DROP `$columnName`");
                }
            }
            
            $select = new Zend_Db_Select($db);
            $select
            ->from('engine4_sitereview_albums', array('album_id', 'listing_id'))
            ->query()
            ->fetchAll();

            $db = Engine_Db_Table::getDefaultAdapter();
            foreach($results as $key => $value) {
               $album_id = $value['album_id'];
               $listing_id = $value['listing_id'];
              $db->query("UPDATE `engine4_sitereview_photos` SET `album_id` = $album_id, `collection_id` = $album_id WHERE `engine4_sitereview_photos`.`listing_id` = $listing_id;") ;
            }
           
        }
        
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('version <= ?', '4.8.8')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if (!empty($is_siteevent_object)) { 
            $select = new Zend_Db_Select($db);
                $select
                ->from('engine4_siteevent_albums', array('album_id', 'event_id'))
                ->query()
                ->fetchAll();
            $db = Engine_Db_Table::getDefaultAdapter();
            foreach($results as $key => $value) {
               $album_id = $value['album_id'];
               $event_id = $value['event_id'];
              $db->query("UPDATE `engine4_siteevent_photos` SET `album_id` = $album_id, `collection_id` = $album_id WHERE `engine4_siteevent_photos`.`event_id` = $event_id;") ;
            }
        }
        
        parent::onInstall();
    }

    public function onPostInstall() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('sitecontentcoverphoto', '1', '1', '1', '1')");
        }
    }

}