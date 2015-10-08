<?php

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitecontentcoverphoto_admin_main_level", "sitecontentcoverphoto", "Member Level Settings", NULL, \'{"route":"admin_default","module":"sitecontentcoverphoto","controller":"level"}\', "sitecontentcoverphoto_admin_main", NULL, 1, 0, 2),
( "sitecontentcoverphoto_admin_manage_modules", "sitecontentcoverphoto", "Manage Modules", NULL, \'{"route":"admin_default","module":"sitecontentcoverphoto","controller":"module"}\', "sitecontentcoverphoto_admin_main", NULL, 1, 0, 3);');

Engine_Api::_()->getApi('setwidgetparam', 'sitecontentcoverphoto')->setDefaultParamsForSitepage();
Engine_Api::_()->getApi('setwidgetparam', 'sitecontentcoverphoto')->setDefaultParamsForSitebusiness();
Engine_Api::_()->getApi('setwidgetparam', 'sitecontentcoverphoto')->setDefaultParamsForSitegroup();
Engine_Api::_()->getApi('setwidgetparam', 'sitecontentcoverphoto')->setDefaultParamsForSitestore();

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitestoreproduct')
        ->where('enabled = ?', 1);
$is_sitestoreproduct_object = $select->query()->fetchObject();

if (!empty($is_sitestoreproduct_object)) {
    $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
				SELECT
					level_id as `level_id`,
					'sitecontentcoverphoto_sitestoreproduct_product' as `type`,
					'upload' as `name`,
					1 as `value`,
					NULL as `params`
				FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'siteevent')
        ->where('enabled = ?', 1);
$is_siteevent_object = $select->query()->fetchObject();

if (!empty($is_siteevent_object)) {
    $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitecontentcoverphoto_siteevent_event' as `type`,
    'upload' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");

    $select = new Zend_Db_Select($db);
    $eventIndexView = $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'siteevent_index_view')
            ->query()
            ->fetchObject();
    if (!empty($eventIndexView)) {
        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'Save to Diary' WHERE  `engine4_core_menuitems`.`name` = 'siteevent_gutter_diary' LIMIT 1 ;");
        $event_id = $eventIndexView->page_id;
        $select = new Zend_Db_Select($db);
        $main_content_id = $select
                ->from('engine4_core_content', array('content_id'))
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->where('page_id = ?', $event_id)
                ->query()
                ->fetchColumn();

        Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'siteevent.list-information-profile', 'page_id =?' => $event_id));
        if (!empty($main_content_id)) {
            $select = new Zend_Db_Select($db);
            $middle_content_id = $select
                    ->from('engine4_core_content', array('content_id'))
                    ->where('parent_content_id = ?', $main_content_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where('page_id = ?', $event_id)
                    ->query()
                    ->fetchColumn();
            if (!empty($middle_content_id)) {
                $select = new Zend_Db_Select($db);
                $widget_content_id = $select
                        ->from('engine4_core_content', array('content_id'))
                        ->where('parent_content_id = ?', $middle_content_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchColumn();
                if (empty($widget_content_id)) {
                    $db->insert('engine4_core_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":["title","joinButton","inviteGuest","updateInfoButton","inviteRsvpButton","optionsButton","venue","startDate", "endDate"],"showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"300","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}', 'parent_content_id' => $middle_content_id, 'type' => 'widget', 'order' => 6, 'page_id' => $event_id));
                }
            }
        }
    }

    if (1) {
        $select = new Zend_Db_Select($db);
        $page_id = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'siteevent_index_view')
                ->query()
                ->fetchColumn();
        if ($page_id) {
            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteGuest","updateInfoButton","inviteRsvpButton","optionsButton","venue","startDate","endDate","location","hostName", "addToMyCalendar","shareOptions"],"profile_like_button":"0","columnHeight":"400","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","contentFullWidth":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.add-to-my-calendar-siteevent" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.list-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

            $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'Save to Diary' WHERE  `engine4_core_menuitems`.`name` = 'siteevent_gutter_diary' LIMIT 1 ;");

            $select = new Zend_Db_Select($db);
            $content_id = $select
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'top')
                    ->where('page_id = ?', $page_id)
                    ->query()
                    ->fetchColumn();

            if ($content_id) {
                $select = new Zend_Db_Select($db);
                $content_id = $select
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'middle')
                        ->where('parent_content_id = ?', $content_id)
                        ->where('page_id = ?', $page_id)
                        ->query()
                        ->fetchColumn();
                if ($content_id) {
                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                }
            }
        }
    }
}


$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitereview')
        ->where('enabled = ?', 1);
$is_sitereview_object = $select->query()->fetchObject();

if (!empty($is_sitereview_object)) {

    $listingTypes = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypesArray(0, 0);

    foreach ($listingTypes as $listingtype_id => $plural_title) {
        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` SELECT
			level_id as `level_id`, 'sitecontentcoverphoto_sitereview_listing_$listingtype_id' as `type`, 'upload' as `name`, 1 as `value`,
			NULL as `params` FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");
    }
}

//START ADVANCED ALBUM PLUGIN IS INSTALL THEN CHANGED LAYOUT.
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

//END ADVANCED ALBUM PLUGIN IS INSTALL THEN CHANGED LAYOUT.
if (1) {
    $select = new Zend_Db_Select($db);
    $page_id = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'sitealbum_album_view')
            ->query()
            ->fetchColumn();
    if ($page_id) {
        $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"album","showContent_0":"","showContent_album":["mainPhoto","title","owner","description","totalPhotos","viewCount","likeCount","commentCount","location","CategoryLink","updateDate","optionsButton","shareOptions"],"showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"400","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "sitealbum.profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
    }
}