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
class Sitecontentcoverphoto_Api_Setwidgetparam extends Core_Api_Abstract {

    public function setDefaultParamsForSitepage() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableNameContentName = $tableNameContent->info('name');
        $parameters = array();
        $parameters['modulename'] = 'sitepage_page';
        $parameters['sitecontentcoverphotoChangeTabPosition'] = 1;
        $parameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
        $parameters['profile_like_button'] = 1;
        $parameters['showMember'] = 0;
        $parameters['memberCount'] = 8;
        $parameters['onlyMemberWithPhoto'] = 1;

        if (1) {
            $columnHeight = 400;
            $contentFullWidth = $parameters['contentFullWidth'] = 1;
            $parameters['columnHeight'] = $columnHeight;
        } else {
            $columnHeight = 300;
            $contentFullWidth = $parameters['contentFullWidth'] = 0;
            $parameters['columnHeight'] = $columnHeight;
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('enabled = ?', 1);
        $is_sitepage_object = $select->query()->fetchObject();

        if (!empty($is_sitepage_object)) {


            $db->delete('engine4_core_content', array('name =?' => 'sitepage.contactdetails-sitepage'));
            $db->delete('engine4_sitepage_content', array('name =?' => 'sitepage.contactdetails-sitepage'));
            $db->delete('engine4_sitepage_admincontent', array('name =?' => 'sitepage.contactdetails-sitepage'));
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
			SELECT
				level_id as `level_id`,
				'sitecontentcoverphoto_sitepage_page' as `type`,
				'upload' as `name`,
				1 as `value`,
				NULL as `params`
			FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");

            $select = new Zend_Db_Select($db);
            $pageIndexView = $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitepage_index_view')
                    ->query()
                    ->fetchObject();

            if (!empty($pageIndexView)) {
                $page_id = $pageIndexView->page_id;
                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();

                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_core_content', array('params'))
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitepage.page-cover-information-sitepage')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitepage_page'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitepage_page","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }
                if (empty($pageContent)) {
                    $db->update('engine4_core_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitepage.page-cover-information-sitepage'));
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitepage_content')
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_sitepage_admincontent', array('params'))
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitepage.page-cover-information-sitepage')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitepage_page'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitepage_page","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"0","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }
                if (empty($pageContent)) {
                    $db->update('engine4_sitepage_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitepage.page-cover-information-sitepage'));
                    if (1) {
                        $top_content_id = $tableNameContent->select()
                                ->from($tableNameContentName, 'content_id')
                                ->where('page_id =?', $page_id)
                                ->where('name =?', 'top')
                                ->query()
                                ->fetchColumn();
                        if (empty($top_content_id)) {
                            $db->insert('engine4_core_content', array(
                                'type' => 'container',
                                'name' => 'top',
                                'page_id' => $page_id,
                                'parent_content_id' => null,
                                'order' => 1,
                                'params' => ''
                            ));
                            $content_id = $db->lastInsertId('engine4_core_content');
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (empty($middle_content_id)) {
                                $db->insert('engine4_core_content', array(
                                    'type' => 'container',
                                    'name' => 'middle',
                                    'page_id' => $page_id,
                                    'parent_content_id' => $content_id,
                                    'order' => 2,
                                    'params' => ''
                                ));

                                $content_id = $db->lastInsertId('engine4_core_content');
                                if ($content_id) {
                                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                                }
                            }
                        } else {
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $top_content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($middle_content_id)) {
                                $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                            }
                        }
                    }
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitepage_admincontent')
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                if (empty($pageContent)) {
                    $db->update('engine4_sitepage_admincontent', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitepage.page-cover-information-sitepage'));
                }
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitemobile')
                    ->where('enabled = ?', 1);
            $is_sitemobile_object = $select->query()->fetchObject();

            if (!empty($is_sitemobile_object)) {
                $select = new Zend_Db_Select($db);
                $pageIndexView = $select
                        ->from('engine4_sitemobile_pages')
                        ->where('name = ?', 'sitepage_index_view')
                        ->query()
                        ->fetchObject();
                if (!empty($pageIndexView)) {
                    $page_id = $pageIndexView->page_id;
                    $select = new Zend_Db_Select($db);
                    $pageContent = $select
                            ->from('engine4_sitemobile_content')
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                            ->query()
                            ->fetchObject();
                    $select = new Zend_Db_Select($db);
                    $params = $select
                            ->from('engine4_sitemobile_content', array('params'))
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitepage.sitemobile-pagecover-photo-information')
                            ->query()
                            ->fetchColumn();

                    if (!empty($params)) {
                        $params = Zend_Json_Decoder::decode($params);
                        $params['showContent_sitepage_page'] = $params['showContent'];
                        $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                        unset($params['showContent']);
                        $mparameters = array();
                        $mparameters['modulename'] = 'sitepage_page';
                        $mparameters['profile_like_button'] = 1;
                        $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
                        $params = array_merge($params, $mparameters);
                        $params = Zend_Json_Encoder::encode($params);
                    } else {
                        $params = '{"modulename":"sitepage_page","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                    }
                    if (empty($pageContent)) {
                        $db->update('engine4_sitemobile_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitepage.sitemobile-pagecover-photo-information'));
                    }

                    $engine4_sitepage_mobileadmincontent = $db->query('SHOW TABLES LIKE \'engine4_sitepage_mobileadmincontent\'')->fetch();
                    if (!empty($engine4_sitepage_mobileadmincontent)) {
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitepage_mobileadmincontent', array('params'))
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitepage.sitemobile-pagecover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitepage_page'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitepage_page';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitepage_page","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitepage_mobileadmincontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitepage.sitemobile-pagecover-photo-information'));
                        }
                    }

                    $engine4_sitepage_mobilecontent = $db->query('SHOW TABLES LIKE \'engine4_sitepage_mobilecontent\'')->fetch();
                    if (!empty($engine4_sitepage_mobilecontent)) {
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitepage_mobilecontent')
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        if (empty($pageContent)) {
                            $db->update('engine4_sitepage_mobilecontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitepage.sitemobile-pagecover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $pageIndexView = $select
                            ->from('engine4_sitemobile_tablet_pages')
                            ->where('name = ?', 'sitepage_index_view')
                            ->query()
                            ->fetchObject();
                    if (!empty($pageIndexView)) {
                        $page_id = $pageIndexView->page_id;
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitemobile_tablet_content')
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitemobile_tablet_content', array('params'))
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitepage.sitemobile-pagecover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitepage_page'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitepage_page';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitepage_page","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitemobile_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitepage.sitemobile-pagecover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitemobileapp')
                            ->where('enabled = ?', 1);
                    $is_sitemobileapp_object = $select->query()->fetchObject();

                    if (!empty($is_sitemobileapp_object)) {
                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_pages')
                                ->where('name = ?', 'sitepage_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitepage.sitemobile-pagecover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitepage_page'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitepage_page';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitepage_page","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price","modifiedDate","commentCount", "viewCount","likeCount","followerCount","memberCount"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitepage.sitemobile-pagecover-photo-information'));
                            }
                        }

                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_tablet_pages')
                                ->where('name = ?', 'sitepage_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_tablet_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_tablet_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitepage.sitemobile-pagecover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitepage_page'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitepage_page';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitepage_page","showContent_0":"","showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price","modifiedDate","commentCount", "viewCount","likeCount","followerCount","memberCount"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitepage.sitemobile-pagecover-photo-information'));
                            }
                        }
                    }
                }
            }
        }
    }

    public function setDefaultParamsForSitebusiness() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableNameContentName = $tableNameContent->info('name');
        $parameters = array();
        $parameters['modulename'] = 'sitebusiness_business';
        $parameters['sitecontentcoverphotoChangeTabPosition'] = 1;
        $parameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
        $parameters['showMember'] = 0;
        $parameters['memberCount'] = 8;
        $parameters['onlyMemberWithPhoto'] = 1;
        if (1) {
            $columnHeight = 400;
            $contentFullWidth = $parameters['contentFullWidth'] = 1;
            $parameters['columnHeight'] = $columnHeight;
        } else {
            $columnHeight = 300;
            $contentFullWidth = $parameters['contentFullWidth'] = 0;
            $parameters['columnHeight'] = $columnHeight;
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitebusiness')
                ->where('enabled = ?', 1);
        $is_sitebusiness_object = $select->query()->fetchObject();

        if (!empty($is_sitebusiness_object)) {

            $db->delete('engine4_core_content', array('name =?' => 'sitebusiness.contactdetails-sitebusiness'));
            $db->delete('engine4_sitebusiness_content', array('name =?' => 'sitebusiness.contactdetails-sitebusiness'));
            $db->delete('engine4_sitebusiness_admincontent', array('name =?' => 'sitebusiness.contactdetails-sitebusiness'));

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
			SELECT
				level_id as `level_id`,
				'sitecontentcoverphoto_sitebusiness_business' as `type`,
				'upload' as `name`,
				1 as `value`,
				NULL as `params`
			FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");

            $select = new Zend_Db_Select($db);
            $pageIndexView = $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitebusiness_index_view')
                    ->query()
                    ->fetchObject();

            if (!empty($pageIndexView)) {
                $page_id = $pageIndexView->page_id;
                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();

                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_core_content', array('params'))
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitebusiness.business-cover-information-sitebusiness')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitebusiness_business'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitebusiness_business","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitebusiness_business":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"0","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }
                if (empty($pageContent)) {
                    $db->update('engine4_core_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitebusiness.business-cover-information-sitebusiness'));
                    if (1) {
                        $top_content_id = $tableNameContent->select()
                                ->from($tableNameContentName, 'content_id')
                                ->where('page_id =?', $page_id)
                                ->where('name =?', 'top')
                                ->query()
                                ->fetchColumn();
                        if (empty($top_content_id)) {
                            $db->insert('engine4_core_content', array(
                                'type' => 'container',
                                'name' => 'top',
                                'page_id' => $page_id,
                                'parent_content_id' => null,
                                'order' => 1,
                                'params' => ''
                            ));
                            $content_id = $db->lastInsertId('engine4_core_content');
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (empty($middle_content_id)) {
                                $db->insert('engine4_core_content', array(
                                    'type' => 'container',
                                    'name' => 'middle',
                                    'page_id' => $page_id,
                                    'parent_content_id' => $content_id,
                                    'order' => 2,
                                    'params' => ''
                                ));

                                $content_id = $db->lastInsertId('engine4_core_content');
                                if ($content_id) {
                                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                                }
                            }
                        } else {
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $top_content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($middle_content_id)) {
                                $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                            }
                        }
                    }
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitebusiness_content')
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_sitebusiness_admincontent', array('params'))
                        ->where('business_id = ?', $page_id)
                        ->where('name = ?', 'sitebusiness.business-cover-information-sitebusiness')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitebusiness_business'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitebusiness_business","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitebusiness_business":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"0","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"0","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }
                if (empty($pageContent)) {
                    $db->update('engine4_sitebusiness_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitebusiness.business-cover-information-sitebusiness'));
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitebusiness_admincontent')
                        ->where('business_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                if (empty($pageContent)) {
                    $db->update('engine4_sitebusiness_admincontent', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitebusiness.business-cover-information-sitebusiness'));
                }
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitemobile')
                    ->where('enabled = ?', 1);
            $is_sitemobile_object = $select->query()->fetchObject();

            if (!empty($is_sitemobile_object)) {
                $select = new Zend_Db_Select($db);
                $pageIndexView = $select
                        ->from('engine4_sitemobile_pages')
                        ->where('name = ?', 'sitebusiness_index_view')
                        ->query()
                        ->fetchObject();
                if (!empty($pageIndexView)) {
                    $page_id = $pageIndexView->page_id;
                    $select = new Zend_Db_Select($db);
                    $pageContent = $select
                            ->from('engine4_sitemobile_content')
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                            ->query()
                            ->fetchObject();
                    $select = new Zend_Db_Select($db);
                    $params = $select
                            ->from('engine4_sitemobile_content', array('params'))
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitebusiness.sitemobile-businesscover-photo-information')
                            ->query()
                            ->fetchColumn();

                    if (!empty($params)) {
                        $params = Zend_Json_Decoder::decode($params);
                        $params['showContent_sitebusiness_business'] = $params['showContent'];
                        $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                        unset($params['showContent']);
                        $mparameters = array();
                        $mparameters['modulename'] = 'sitebusiness_business';
                        $mparameters['profile_like_button'] = 1;
                        $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                        $params = array_merge($params, $mparameters);
                        $params = Zend_Json_Encoder::encode($params);
                    } else {
                        $params = '{"modulename":"sitebusiness_business","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitebusiness_business":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                    }
                    if (empty($pageContent)) {
                        $db->update('engine4_sitemobile_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitebusiness.sitemobile-businesscover-photo-information'));
                    }

                    $engine4_sitebusiness_mobileadmincontent = $db->query('SHOW TABLES LIKE \'engine4_sitebusiness_mobileadmincontent\'')->fetch();
                    if (!empty($engine4_sitebusiness_mobileadmincontent)) {
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitebusiness_mobileadmincontent', array('params'))
                                ->where('business_id = ?', $page_id)
                                ->where('name = ?', 'sitebusiness.sitemobile-businesscover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitebusiness_business'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitebusiness_business';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitebusiness_business","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitebusiness_business":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitebusiness_mobileadmincontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitebusiness.sitemobile-businesscover-photo-information'));
                        }
                    }

                    $engine4_sitebusiness_mobilecontent = $db->query('SHOW TABLES LIKE \'engine4_sitebusiness_mobilecontent\'')->fetch();
                    if (!empty($engine4_sitebusiness_mobilecontent)) {
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitebusiness_mobilecontent')
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        if (empty($pageContent)) {
                            $db->update('engine4_sitebusiness_mobilecontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitebusiness.sitemobile-businesscover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $pageIndexView = $select
                            ->from('engine4_sitemobile_tablet_pages')
                            ->where('name = ?', 'sitebusiness_index_view')
                            ->query()
                            ->fetchObject();
                    if (!empty($pageIndexView)) {
                        $page_id = $pageIndexView->page_id;
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitemobile_tablet_content')
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitemobile_tablet_content', array('params'))
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitebusiness.sitemobile-businesscover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitebusiness_business'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitebusiness_business';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitebusiness_business","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitebusiness_business":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitemobile_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitebusiness.sitemobile-businesscover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitemobileapp')
                            ->where('enabled = ?', 1);
                    $is_sitemobileapp_object = $select->query()->fetchObject();

                    if (!empty($is_sitemobileapp_object)) {
                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_pages')
                                ->where('name = ?', 'sitebusiness_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitebusiness.sitemobile-businesscover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitebusiness_business'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitebusiness_business';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitebusiness_business","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitebusiness_business":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price","modifiedDate","commentCount", "viewCount","likeCount","followerCount","memberCount"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitebusiness.sitemobile-businesscover-photo-information'));
                            }
                        }

                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_tablet_pages')
                                ->where('name = ?', 'sitebusiness_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_tablet_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_tablet_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitebusiness.sitemobile-businesscover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitebusiness_business'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitebusiness_business';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitebusiness_business","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitebusiness_business":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price","modifiedDate","commentCount", "viewCount","likeCount","followerCount","memberCount"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitebusiness.sitemobile-businesscover-photo-information'));
                            }
                        }
                    }
                }
            }
        }
    }

    public function setDefaultParamsForSitegroup() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableNameContentName = $tableNameContent->info('name');
        $parameters = array();
        $parameters['modulename'] = 'sitegroup_group';
        $parameters['sitecontentcoverphotoChangeTabPosition'] = 1;
        $parameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
        $parameters['showMember'] = 1;
        $parameters['memberCount'] = 8;
        $parameters['onlyMemberWithPhoto'] = 1;
        if (1) {
            $columnHeight = 400;
            $contentFullWidth = $parameters['contentFullWidth'] = 1;
            $parameters['columnHeight'] = $columnHeight;
        } else {
            $columnHeight = 300;
            $contentFullWidth = $parameters['contentFullWidth'] = 0;
            $parameters['columnHeight'] = $columnHeight;
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('enabled = ?', 1);
        $is_sitegroup_object = $select->query()->fetchObject();

        if (!empty($is_sitegroup_object)) {

            $db->delete('engine4_core_content', array('name =?' => 'sitegroup.contactdetails-sitegroup'));
            $db->delete('engine4_sitegroup_content', array('name =?' => 'sitegroup.contactdetails-sitegroup'));
            $db->delete('engine4_sitegroup_admincontent', array('name =?' => 'sitegroup.contactdetails-sitegroup'));

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
			SELECT
				level_id as `level_id`,
				'sitecontentcoverphoto_sitegroup_group' as `type`,
				'upload' as `name`,
				1 as `value`,
				NULL as `params`
			FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");

            $select = new Zend_Db_Select($db);
            $pageIndexView = $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitegroup_index_view')
                    ->query()
                    ->fetchObject();

            if (!empty($pageIndexView)) {
                $page_id = $pageIndexView->page_id;
                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();

                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_core_content', array('params'))
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitegroup.group-cover-information-sitegroup')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitegroup_group'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }

                if (empty($pageContent)) {
                    $db->update('engine4_core_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitegroup.group-cover-information-sitegroup'));
                    if (1) {
                        $top_content_id = $tableNameContent->select()
                                ->from($tableNameContentName, 'content_id')
                                ->where('page_id =?', $page_id)
                                ->where('name =?', 'top')
                                ->query()
                                ->fetchColumn();
                        if (empty($top_content_id)) {
                            $db->insert('engine4_core_content', array(
                                'type' => 'container',
                                'name' => 'top',
                                'page_id' => $page_id,
                                'parent_content_id' => null,
                                'order' => 1,
                                'params' => ''
                            ));
                            $content_id = $db->lastInsertId('engine4_core_content');
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (empty($middle_content_id)) {
                                $db->insert('engine4_core_content', array(
                                    'type' => 'container',
                                    'name' => 'middle',
                                    'page_id' => $page_id,
                                    'parent_content_id' => $content_id,
                                    'order' => 2,
                                    'params' => ''
                                ));

                                $content_id = $db->lastInsertId('engine4_core_content');
                                if ($content_id) {
                                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                                }
                            }
                        } else {
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $top_content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($middle_content_id)) {
                                $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                            }
                        }
                    }
                }

                $select = new Zend_Db_Select($db);
                $paramss = $select
                        ->from('engine4_core_content', array('params'))
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitegroupmember.groupcover-photo-sitegroupmembers')
                        ->query()
                        ->fetchColumn();
                if (!empty($paramss)) {
                    $params = Zend_Json_Decoder::decode($paramss);
                    $params['showContent'][] = "optionsButton";
                    $showContent = $params['showContent'];
                    $statistics = array();
                    if (isset($params['statistics']))
                        $statistics = $params['statistics'];
                    $parArray = array_merge($showContent, $statistics);
                    $params['showContent_sitegroup_group'] = $parArray;
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    unset($params['statistics']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }

                if (empty($pageContent)) {
                    $db->update('engine4_core_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitegroupmember.groupcover-photo-sitegroupmembers'));
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitegroup_content')
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_sitegroup_admincontent', array('params'))
                        ->where('group_id = ?', $page_id)
                        ->where('name = ?', 'sitegroup.group-cover-information-sitegroup')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitegroup_group'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }
                if (empty($pageContent)) {
                    $db->update('engine4_sitegroup_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitegroup.group-cover-information-sitegroup'));
                }

                $select = new Zend_Db_Select($db);
                $paramss = $select
                        ->from('engine4_sitegroup_admincontent', array('params'))
                        ->where('group_id = ?', $page_id)
                        ->where('name = ?', 'sitegroupmember.groupcover-photo-sitegroupmembers')
                        ->query()
                        ->fetchColumn();
                if (!empty($paramss)) {
                    $params = Zend_Json_Decoder::decode($paramss);
                    $params['showContent'][] = "optionsButton";
                    $showContent = $params['showContent'];
                    $statistics = array();
                    if (isset($params['statistics']))
                        $statistics = $params['statistics'];
                    $parArray = array_merge($showContent, $statistics);
                    $params['showContent_sitegroup_group'] = $parArray;
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    unset($params['statistics']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }

                if (empty($pageContent)) {
                    $db->update('engine4_sitegroup_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitegroupmember.groupcover-photo-sitegroupmembers'));
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitegroup_admincontent')
                        ->where('group_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                if (empty($pageContent)) {
                    $db->update('engine4_sitegroup_admincontent', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitegroup.group-cover-information-sitegroup'));

                    $db->update('engine4_sitegroup_admincontent', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitegroupmember.groupcover-photo-sitegroupmembers'));
                }
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitemobile')
                    ->where('enabled = ?', 1);
            $is_sitemobile_object = $select->query()->fetchObject();

            if (!empty($is_sitemobile_object)) {
                $select = new Zend_Db_Select($db);
                $pageIndexView = $select
                        ->from('engine4_sitemobile_pages')
                        ->where('name = ?', 'sitegroup_index_view')
                        ->query()
                        ->fetchObject();
                if (!empty($pageIndexView)) {
                    $page_id = $pageIndexView->page_id;
                    $select = new Zend_Db_Select($db);
                    $pageContent = $select
                            ->from('engine4_sitemobile_content')
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                            ->query()
                            ->fetchObject();
                    $select = new Zend_Db_Select($db);
                    $params = $select
                            ->from('engine4_sitemobile_content', array('params'))
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitegroup.sitemobile-groupcover-photo-information')
                            ->query()
                            ->fetchColumn();

                    if (!empty($params)) {
                        $params = Zend_Json_Decoder::decode($params);
                        $params['showContent_sitegroup_group'] = $params['showContent'];
                        $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                        unset($params['showContent']);
                        $mparameters = array();
                        $mparameters['modulename'] = 'sitegroup_group';
                        $mparameters['profile_like_button'] = 1;
                        $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                        $params = array_merge($params, $mparameters);
                        $params = Zend_Json_Encoder::encode($params);
                    } else {
                        $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                    }
                    if (empty($pageContent)) {
                        $db->update('engine4_sitemobile_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitegroup.sitemobile-groupcover-photo-information'));
                    }

                    $engine4_sitegroup_mobileadmincontent = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_mobileadmincontent\'')->fetch();
                    if (!empty($engine4_sitegroup_mobileadmincontent)) {
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitegroup_mobileadmincontent', array('params'))
                                ->where('group_id = ?', $page_id)
                                ->where('name = ?', 'sitegroup.sitemobile-groupcover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitegroup_group'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitegroup_group';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitegroup_mobileadmincontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitegroup.sitemobile-groupcover-photo-information'));
                        }
                    }

                    $engine4_sitegroup_mobilecontent = $db->query('SHOW TABLES LIKE \'engine4_sitegroup_mobilecontent\'')->fetch();
                    if (!empty($engine4_sitegroup_mobilecontent)) {
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitegroup_mobilecontent')
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        if (empty($pageContent)) {
                            $db->update('engine4_sitegroup_mobilecontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitegroup.sitemobile-groupcover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $pageIndexView = $select
                            ->from('engine4_sitemobile_tablet_pages')
                            ->where('name = ?', 'sitegroup_index_view')
                            ->query()
                            ->fetchObject();
                    if (!empty($pageIndexView)) {
                        $page_id = $pageIndexView->page_id;
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitemobile_tablet_content')
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitemobile_tablet_content', array('params'))
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitegroup.sitemobile-groupcover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitegroup_group'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitegroup_group';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitemobile_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitegroup.sitemobile-groupcover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitemobileapp')
                            ->where('enabled = ?', 1);
                    $is_sitemobileapp_object = $select->query()->fetchObject();

                    if (!empty($is_sitemobileapp_object)) {
                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_pages')
                                ->where('name = ?', 'sitegroup_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitegroup.sitemobile-groupcover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitegroup_group'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitegroup_group';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price","modifiedDate","commentCount", "viewCount","likeCount","followerCount","memberCount"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitegroup.sitemobile-groupcover-photo-information'));
                            }
                        }

                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_tablet_pages')
                                ->where('name = ?', 'sitegroup_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_tablet_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_tablet_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitegroup.sitemobile-groupcover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitegroup_group'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitegroup_group';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitegroup_group","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitegroup_group":["title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price","modifiedDate","commentCount", "viewCount","likeCount","followerCount","memberCount"],"showContent_sitereview_listing":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitegroup.sitemobile-groupcover-photo-information'));
                            }
                        }
                    }
                }
            }
        }
    }

    public function setDefaultParamsForSitestore() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableNameContentName = $tableNameContent->info('name');
        $parameters = array();
        $parameters['modulename'] = 'sitestore_store';
        $parameters['sitecontentcoverphotoChangeTabPosition'] = 1;
        $parameters['sitecontentcoverphotoStrachMainPhoto'] = 1;
        $parameters['showMember'] = 0;
        $parameters['memberCount'] = 8;
        $parameters['onlyMemberWithPhoto'] = 1;
        $parameters['profile_like_button'] = 1;
        if (1) {
            $columnHeight = 400;
            $contentFullWidth = $parameters['contentFullWidth'] = 1;
            $parameters['columnHeight'] = $columnHeight;
        } else {
            $columnHeight = 300;
            $contentFullWidth = $parameters['contentFullWidth'] = 0;
            $parameters['columnHeight'] = $columnHeight;
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitestore')
                ->where('enabled = ?', 1);
        $is_sitestore_object = $select->query()->fetchObject();

        if (!empty($is_sitestore_object)) {
            $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES
    ("sitestore", "sitestore_store", "store_id", 1)');
            $db->query('INSERT IGNORE INTO `engine4_sitecontentcoverphoto_modules` (`module`, `resource_type`, `resource_id`, `enabled`) VALUES
    ("sitestoreproduct", "sitestoreproduct_product", "product_id", 1)');
            $db->update('engine4_sitecontentcoverphoto_modules', array('enabled' => 1), array('module = ?' => 'sitestore'));
            $db->update('engine4_sitecontentcoverphoto_modules', array('enabled' => 1), array('module = ?' => 'sitestoreproduct'));
            $db->delete('engine4_core_content', array('name =?' => 'sitestore.contactdetails-sitestore'));
            $db->delete('engine4_sitestore_content', array('name =?' => 'sitestore.contactdetails-sitestore'));
            $db->delete('engine4_sitestore_admincontent', array('name =?' => 'sitestore.contactdetails-sitestore'));

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
			SELECT
				level_id as `level_id`,
				'sitecontentcoverphoto_sitestore_store' as `type`,
				'upload' as `name`,
				1 as `value`,
				NULL as `params`
			FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');");

            $select = new Zend_Db_Select($db);
            $pageIndexView = $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitestore_index_view')
                    ->query()
                    ->fetchObject();

            if (!empty($pageIndexView)) {
                $page_id = $pageIndexView->page_id;
                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();

                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_core_content', array('params'))
                        ->where('page_id = ?', $page_id)
                        ->where('name = ?', 'sitestore.store-cover-information-sitestore')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitestore_store'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitestore_store","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitestore_store":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitegroup_group":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"0","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }
                if (empty($pageContent)) {
                    $db->update('engine4_core_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitestore.store-cover-information-sitestore'));
                    if (1) {
                        $top_content_id = $tableNameContent->select()
                                ->from($tableNameContentName, 'content_id')
                                ->where('page_id =?', $page_id)
                                ->where('name =?', 'top')
                                ->query()
                                ->fetchColumn();
                        if (empty($top_content_id)) {
                            $db->insert('engine4_core_content', array(
                                'type' => 'container',
                                'name' => 'top',
                                'page_id' => $page_id,
                                'parent_content_id' => null,
                                'order' => 1,
                                'params' => ''
                            ));
                            $content_id = $db->lastInsertId('engine4_core_content');
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (empty($middle_content_id)) {
                                $db->insert('engine4_core_content', array(
                                    'type' => 'container',
                                    'name' => 'middle',
                                    'page_id' => $page_id,
                                    'parent_content_id' => $content_id,
                                    'order' => 2,
                                    'params' => ''
                                ));

                                $content_id = $db->lastInsertId('engine4_core_content');
                                if ($content_id) {
                                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                                }
                            }
                        } else {
                            $middle_content_id = $tableNameContent->select()
                                    ->from($tableNameContentName, 'content_id')
                                    ->where('page_id =?', $page_id)
                                    ->where('parent_content_id =?', $top_content_id)
                                    ->where('name =?', 'middle')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($middle_content_id)) {
                                $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                            }
                        }
                    }
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitestore_content')
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                $select = new Zend_Db_Select($db);
                $params = $select
                        ->from('engine4_sitestore_admincontent', array('params'))
                        ->where('store_id = ?', $page_id)
                        ->where('name = ?', 'sitestore.store-cover-information-sitestore')
                        ->query()
                        ->fetchColumn();
                if (!empty($params)) {
                    $params = Zend_Json_Decoder::decode($params);
                    $params['showContent'][] = "optionsButton";
                    $params['showContent_sitestore_store'] = $params['showContent'];
                    $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                    unset($params['showContent']);
                    $params = array_merge($params, $parameters);
                    $params = Zend_Json_Encoder::encode($params);
                } else {
                    $params = '{"modulename":"sitestore_store","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitestore_store":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","memberCount","addButton","joinButton"],"showContent_sitereview_listing":"","showContent_sitegroup_group":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoChangeTabPosition":"1","sitecontentcoverphotoStrachMainPhoto":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo","showMember":"0","memberCount":"8","onlyMemberWithPhoto":"1", "contentFullWidth":"' . $contentFullWidth . '"}';
                }
                if (empty($pageContent)) {
                    $db->update('engine4_sitestore_content', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitestore.store-cover-information-sitestore'));
                }

                $select = new Zend_Db_Select($db);
                $pageContent = $select
                        ->from('engine4_sitestore_admincontent')
                        ->where('store_id = ?', $page_id)
                        ->where('name = ?', 'sitecontentcoverphoto.content-cover-photo')
                        ->query()
                        ->fetchObject();
                if (empty($pageContent)) {
                    $db->update('engine4_sitestore_admincontent', array('name' => 'sitecontentcoverphoto.content-cover-photo', 'params' => $params), array('name = ?' => 'sitestore.store-cover-information-sitestore'));
                }
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitemobile')
                    ->where('enabled = ?', 1);
            $is_sitemobile_object = $select->query()->fetchObject();

            if (!empty($is_sitemobile_object)) {
                $select = new Zend_Db_Select($db);
                $pageIndexView = $select
                        ->from('engine4_sitemobile_pages')
                        ->where('name = ?', 'sitestore_index_view')
                        ->query()
                        ->fetchObject();
                if (!empty($pageIndexView)) {
                    $page_id = $pageIndexView->page_id;
                    $select = new Zend_Db_Select($db);
                    $pageContent = $select
                            ->from('engine4_sitemobile_content')
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                            ->query()
                            ->fetchObject();
                    $select = new Zend_Db_Select($db);
                    $params = $select
                            ->from('engine4_sitemobile_content', array('params'))
                            ->where('page_id = ?', $page_id)
                            ->where('name = ?', 'sitestore.sitemobile-storecover-photo-information')
                            ->query()
                            ->fetchColumn();

                    if (!empty($params)) {
                        $params = Zend_Json_Decoder::decode($params);
                        $params['showContent_sitestore_store'] = $params['showContent'];
                        $params['name'] = 'sitecontentcoverphoto.content-cover-photo';
                        unset($params['showContent']);
                        $mparameters = array();
                        $mparameters['modulename'] = 'sitestore_store';
                        $mparameters['profile_like_button'] = 1;
                        $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                        $params = array_merge($params, $mparameters);
                        $params = Zend_Json_Encoder::encode($params);
                    } else {
                        $params = '{"modulename":"sitestore_store","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitestore_store":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitegroup_group":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                    }
                    if (empty($pageContent)) {
                        $db->update('engine4_sitemobile_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitestore.sitemobile-storecover-photo-information'));
                    }

                    $engine4_sitestore_mobileadmincontent = $db->query('SHOW TABLES LIKE \'engine4_sitestore_mobileadmincontent\'')->fetch();
                    if (!empty($engine4_sitestore_mobileadmincontent)) {
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitestore_mobileadmincontent', array('params'))
                                ->where('store_id = ?', $page_id)
                                ->where('name = ?', 'sitestore.sitemobile-storecover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitestore_store'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitestore_store';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitestore_store","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitestore_store":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitegroup_group":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitestore_mobileadmincontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitestore.sitemobile-storecover-photo-information'));
                        }
                    }

                    $engine4_sitestore_mobilecontent = $db->query('SHOW TABLES LIKE \'engine4_sitestore_mobilecontent\'')->fetch();
                    if (!empty($engine4_sitestore_mobilecontent)) {
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitestore_mobilecontent')
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        if (empty($pageContent)) {
                            $db->update('engine4_sitestore_mobilecontent', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitestore.sitemobile-storecover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $pageIndexView = $select
                            ->from('engine4_sitemobile_tablet_pages')
                            ->where('name = ?', 'sitestore_index_view')
                            ->query()
                            ->fetchObject();
                    if (!empty($pageIndexView)) {
                        $page_id = $pageIndexView->page_id;
                        $select = new Zend_Db_Select($db);
                        $pageContent = $select
                                ->from('engine4_sitemobile_tablet_content')
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                ->query()
                                ->fetchObject();
                        $select = new Zend_Db_Select($db);
                        $params = $select
                                ->from('engine4_sitemobile_tablet_content', array('params'))
                                ->where('page_id = ?', $page_id)
                                ->where('name = ?', 'sitestore.sitemobile-storecover-photo-information')
                                ->query()
                                ->fetchColumn();

                        if (!empty($params)) {
                            $params = Zend_Json_Decoder::decode($params);
                            $params['showContent_sitestore_store'] = $params['showContent'];
                            $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                            unset($params['showContent']);
                            $mparameters = array();
                            $mparameters['modulename'] = 'sitestore_store';
                            $mparameters['profile_like_button'] = 1;
                            $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                            $params = array_merge($params, $mparameters);
                            $params = Zend_Json_Encoder::encode($params);
                        } else {
                            $params = '{"modulename":"sitestore_store","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitestore_store":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitegroup_group":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                        }
                        if (empty($pageContent)) {
                            $db->update('engine4_sitemobile_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitestore.sitemobile-storecover-photo-information'));
                        }
                    }

                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitemobileapp')
                            ->where('enabled = ?', 1);
                    $is_sitemobileapp_object = $select->query()->fetchObject();

                    if (!empty($is_sitemobileapp_object)) {
                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_pages')
                                ->where('name = ?', 'sitestore_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitestore.sitemobile-storecover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitestore_store'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitestore_store';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitestore_store","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitestore_store":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitegroup_group":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitestore.sitemobile-storecover-photo-information'));
                            }
                        }

                        $select = new Zend_Db_Select($db);
                        $pageIndexView = $select
                                ->from('engine4_sitemobileapp_tablet_pages')
                                ->where('name = ?', 'sitestore_index_view')
                                ->query()
                                ->fetchObject();
                        if (!empty($pageIndexView)) {
                            $page_id = $pageIndexView->page_id;
                            $select = new Zend_Db_Select($db);
                            $pageContent = $select
                                    ->from('engine4_sitemobileapp_tablet_content')
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitecontentcoverphoto.content-cover-mobile-photo')
                                    ->query()
                                    ->fetchObject();
                            $select = new Zend_Db_Select($db);
                            $params = $select
                                    ->from('engine4_sitemobileapp_tablet_content', array('params'))
                                    ->where('page_id = ?', $page_id)
                                    ->where('name = ?', 'sitestore.sitemobile-storecover-photo-information')
                                    ->query()
                                    ->fetchColumn();

                            if (!empty($params)) {
                                $params = Zend_Json_Decoder::decode($params);
                                $params['showContent_sitestore_store'] = $params['showContent'];
                                $params['name'] = 'sitecontentcoverphoto.content-cover-mobile-photo';
                                unset($params['showContent']);
                                $mparameters = array();
                                $mparameters['modulename'] = 'sitestore_store';
                                $mparameters['profile_like_button'] = 1;
                                $mparameters['sitecontentcoverphotoStrachMainPhoto'] = 0;
                                $params = array_merge($params, $mparameters);
                                $params = Zend_Json_Encoder::encode($params);
                            } else {
                                $params = '{"modulename":"sitestore_store","showContent_0":"","showContent_sitepage_page":"","showContent_siteevent_event":"","showContent_sitebusiness_business":"","showContent_sitestore_store":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"showContent_sitereview_listing":"","showContent_sitegroup_group":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"' . $columnHeight . '","sitecontentcoverphotoStrachMainPhoto":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-mobile-photo", "contentFullWidth":"' . $contentFullWidth . '"}';
                            }
                            if (empty($pageContent)) {
                                $db->update('engine4_sitemobileapp_tablet_content', array('name' => 'sitecontentcoverphoto.content-cover-mobile-photo', 'params' => $params), array('name = ?' => 'sitestore.sitemobile-storecover-photo-information'));
                            }
                        }
                    }
                }
            }
        }
    }

}
