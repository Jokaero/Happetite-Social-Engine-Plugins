<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Installer extends Engine_Package_Installer_Module {

    function onPreInstall() {

        $db = $this->getDb();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'socialengineaddon');
        $check_socialengineaddons = $select->query()->fetchAll();

        if (empty($_GET['flag']) && !empty($check_socialengineaddons)) {

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'seaocore');
            $check_seaocore = $select->query()->fetchAll();

            if (empty($check_seaocore)) {
                $REQUEST_URI = $_SERVER['REQUEST_URI'];
                $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();

// 			$explode_base_url = explode('/', $baseUrl);
// 			foreach($explode_base_url as $url_key) {
// 				if($url_key != 'install') {
// 				$core_final_url .= $url_key . '/';
// 				}
// 			}
// 			
// 	    if (strstr ($baseUrl, '/query')) {
// 				$url1 = $REQUEST_URI . '?flag=1'; //'http://' . $baseUrl . '/manage/install?flag=1';
// 			} else {
// 				$url1 = $REQUEST_URI . '&flag=1';
// 			}
                $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
                if (strstr($url_string, 'manage/install')) {
                    $url1 = $REQUEST_URI . '&flag=1';
                } else if (strstr($url_string, 'manage/query')) {
                    $url1 = $REQUEST_URI . '?flag=1';
                }
                $url2 = 'http://' . $baseUrl . '/manage';
                $blog_post1 = "http://www.socialengineaddons.com/content/upgrades-enhancements-released-many-plugins";
                $blog_post2 = "http://www.socialengineaddons.com/content/socialengineaddons-plugins-upgraded-their-version-423";
                return $this->_error('<div class="global_form"><div style="float:none;max-width:none;"><div>We have renamed our “SocialEngineAddOns Core Plugin” directory from "/application/modules/Socialengineaddon” to "/application/modules/Seaocore". We recommend you to install the latest version of SocialEngineAddOns Core Plugin (v 4.2.3) after downloading it from your SocialEngineAddOns Client Area. After installing this plugin, you should upgrade all the SocialEngineAddOns plugins installed on your site to their latest versions.<br /><br />
			We have released many feature enhancements in our plugins. To know the significant feature enhancements made in our plugins, please read our blog post over here: <a href="' . $blog_post1 . '" target="_blank">http://www.socialengineaddons.com/content/upgrades-enhancements-released-many-plugins</a>.<br /><br />
			To view the guidelines for installing the new SocialEngineAddOns Core plugin and upgrading the other plugins from SocialEngineAddOns on your site, please read this article: <a href="' . $blog_post2 . '" target="_blank">http://www.socialengineaddons.com/content/socialengineaddons-plugins-upgraded-their-version-423</a>.<br /><br />
      If you have read both the above articles and wish to proceed to install this plugin, then click on <a href="' . $url1 . '" class="">Confirm</a>, else <a href="' . $url2 . '" class="">Cancel</a> to return to “Manage Packages” section.</div></div></div>');
            }
        }
        parent::onPreInstall();
    }

    function onInstall() {

        $db = $this->getDb();
        $db->query("UPDATE  `engine4_core_menuitems` SET  `order` =  '0' WHERE  `engine4_core_menuitems`.`name` ='core_admin_plugins_Seaocore' LIMIT 1 ");
        include_once APPLICATION_PATH . "/application/modules/Seaocore/settings/upgradeQuries.php";
        
        $results = $db->query('SELECT * FROM  `engine4_activity_notificationtypes` WHERE  `body` LIKE  \'%{var:$eventname}%\'')->fetchAll();
        foreach($results as $value) {
            $body = str_replace('var:$eventname', 'item:$object', $value['body']);
            $type = $value['type'];
            $db->query("UPDATE `engine4_activity_notificationtypes` SET  `body` = '$body'  WHERE  `engine4_activity_notificationtypes`.`type` =  '$type' LIMIT 1 ;"); 
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_attachments LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_attachments` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'handler'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notificationtypes` CHANGE `handler` `handler` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type <= 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notificationtypes` CHANGE `type` `type` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationsettings LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type <= 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notificationsettings` CHANGE `type` `type` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'subject_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `subject_type` `subject_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'object_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `object_type` `object_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_core_menuitems's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'menu'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type <= 64) {
                $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `menu` `menu` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_core_menuitems's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'label'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type <= 64) {
                $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `label` `label` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $db->query("ALTER TABLE `engine4_activity_actiontypes` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actionsettings LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $db->query("ALTER TABLE `engine4_activity_actionsettings` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'name'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `name` `name` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_core_modules FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_modules LIKE 'title'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 128) {
                $db->query("ALTER TABLE `engine4_core_modules` CHANGE `title` `title` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CHANGE IN CORE SETTING TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_core_settings'")->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM engine4_core_settings LIKE 'value'")->fetch();
            if (!empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_core_settings` CHANGE `value` `value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
            }
        }

        // check for ajax enabling column in table
        $table_seaocore_exist = $db->query("SHOW TABLES LIKE 'engine4_seaocores'")->fetch();
        if (!empty($table_seaocore_exist)) {
            $column_env_exist = $db->query("SHOW COLUMNS FROM engine4_seaocores LIKE 'enviroment'")->fetch();
            if (empty($column_env_exist)) {
                $db->query("ALTER TABLE `engine4_seaocores` ADD `enviroment` VARCHAR( 50 ) NOT NULL DEFAULT 'development'");
            }
        }

        //CHANGE IN SEARCH FORM SETTING TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_seaocore_searchformsetting'")->fetch();
        if (!empty($table_exist)) {

            //DELETE DUPLICATE ENTRIES BEFORE CONVERTING INDEXING TO UNIQUE KEY
            $select = new Zend_Db_Select($db);
            $ids = $select
                    ->from('engine4_seaocore_searchformsetting', array('MIN( searchformsetting_id ) AS searchformsetting_id'))
                    ->group('module')
                    ->group('name')
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);

            if (!empty($ids)) {
                $idsString = '';
                foreach ($ids as $id) {
                    $idsString .= "'$id',";
                }

                $idsString = rtrim($idsString, ',');

                if (!empty($idsString)) {
                    $db->query("DELETE FROM `engine4_seaocore_searchformsetting` WHERE searchformsetting_id NOT IN ($idsString)");
                }
            }

            $db->query("ALTER TABLE `engine4_seaocore_searchformsetting` DROP INDEX `PLUGIN_NAME` , ADD UNIQUE `PLUGIN_NAME` ( `module` , `name` );");
        }

        //Delete widget if install seaocore plugin.
        $select = new Zend_Db_Select($db);
        $content_id = $select
                ->from('engine4_core_content')
                ->where('name =?', 'socialengineaddon.socialengineaddones-lightbox')
                ->query()
                ->fetchColumn();
        if (!empty($content_id)) {
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'socialengineaddon.socialengineaddones-lightbox' LIMIT 1;");
        }

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_tabs` (
			`tab_id` int(11) NOT NULL AUTO_INCREMENT,
			`module` varchar(64) NOT NULL,
			`type` varchar(64) NOT NULL,
			`name` varchar(64) NOT NULL,
			`title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`order` int(3) NOT NULL DEFAULT '0',
			`limit` int(3) NOT NULL,
			`show` tinyint(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (`tab_id`),
			UNIQUE KEY `name` (`name`)
		) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_searchformsetting` (
			`searchformsetting_id` int(11) NOT NULL AUTO_INCREMENT,
			`module` varchar(64) NOT NULL,
			`name` varchar(64) NOT NULL,
			`display` tinyint(1) NOT NULL DEFAULT '1',
			`order` int(11) NOT NULL DEFAULT '0',
			`label` varchar(100) NOT NULL,
			PRIMARY KEY (`searchformsetting_id`),
			UNIQUE KEY `PLUGIN_NAME` (`module`,`name`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocores` (
			`seaocores_id` int(11) NOT NULL AUTO_INCREMENT,
			`module_name` varchar(64) NOT NULL,
			`title` varchar(64) NOT NULL,
			`description` text NOT NULL,
			`version` varchar(32) NOT NULL,
			`is_installed` tinyint(1) NOT NULL,
			`category` varchar(64) NOT NULL,
			`ptype` varchar(20) NOT NULL,
			`is_activate` int(11) NOT NULL DEFAULT '0',
			`enviroment` varchar(50) NOT NULL DEFAULT 'development',
			PRIMARY KEY (`seaocores_id`)
		) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_locations` (
			`location_id` int(11) NOT NULL AUTO_INCREMENT,
			`location` varchar(255) NOT NULL,
			`latitude` double NOT NULL,
			`longitude` double NOT NULL,
			`formatted_address` text,
			`country` varchar(255) DEFAULT NULL,
			`state` varchar(255) DEFAULT NULL,
			`zipcode` varchar(255) DEFAULT NULL,
			`city` varchar(255) DEFAULT NULL,
			`address` varchar(255) DEFAULT NULL,
			`zoom` int(11) NOT NULL,
			PRIMARY KEY (`location_id`)
		) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_user_settings` (
			`user_id` int(10) unsigned NOT NULL,
			`name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
			`value` varchar(255) NOT NULL,
			PRIMARY KEY (`user_id`, `name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'socialengineaddon');
        $check_socialengineaddons = $select->query()->fetchAll();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'seaocore');
        $seaocore_install = $select->query()->fetchAll();

        if (!empty($check_socialengineaddons) && empty($seaocore_install)) {

            $seaocore_tab_column_exist = TRUE;
            $tabs_table_exist = $db->query("SHOW TABLES LIKE 'engine4_socialengineaddon_tabs'")->fetch();
            if (!empty($tabs_table_exist)) {
                $seaocore_tab_column_exist = $db->query("SHOW COLUMNS FROM engine4_socialengineaddon_tabs LIKE 'show'")->fetch();
                if (empty($seaocore_tab_column_exist)) {
                    $db->query("ALTER TABLE `engine4_socialengineaddon_tabs` ADD `show` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `limit`");
                }


                $seaocore_tabs = $db->query("SELECT * FROM `engine4_socialengineaddon_tabs`")->fetchAll();
                foreach ($seaocore_tabs as $seaocore) {
                    $db->query("INSERT IGNORE INTO `engine4_seaocore_tabs` ( `module`, `type`, `name`, `title`, `enabled`, `order`, `limit`, `show` ) VALUES ('" . $seaocore['module'] . "', '" . $seaocore['type'] . "', '" . $seaocore['name'] . "', '" . $seaocore['title'] . "', '" . $seaocore['enabled'] . "', '" . $seaocore['order'] . "', '" . $seaocore['limit'] . "','" . $seaocore['show'] . "');");
                }
            }


            $location_table_exist = $db->query("SHOW TABLES LIKE 'engine4_socialengineaddon_locations'")->fetch();
            if (!empty($location_table_exist)) {
                $db->query('TRUNCATE TABLE `engine4_seaocore_locations`;');
                $db->query('INSERT INTO `engine4_seaocore_locations` (`location`, `latitude`, `longitude`, `formatted_address`, `country`, `state`, `zipcode`, `city`, `address`, `zoom`) select `location`, `latitude`, `longitude`, `formatted_address`, `country`, `state`, `zipcode`, `city`, `address`, `zoom` from `engine4_socialengineaddon_locations`;');

// 			$seaocore_locations = $db->query("SELECT * FROM `engine4_socialengineaddon_locations`")->fetchAll();
// 			foreach( $seaocore_locations as $seaocore ) {
// 			  $db->query('INSERT IGNORE INTO `engine4_seaocore_locations` ( `location`, `latitude`, `longitude`, `formatted_address`, `country`, `state`, `zipcode`, `city`, `address`, `zoom` ) VALUES ("' . $seaocore['location'] . '", "' . $seaocore['latitude'] . '", "' . $seaocore['longitude'] . '", "' . $seaocore['formatted_address'] . '", "' . $seaocore['country'] . '", "' . $seaocore['state'] . '", "' . $seaocore['zipcode'] . '","' . $seaocore['city'] . '", "' . $seaocore['address'] . '", "' . $seaocore['zoom'] . '");');
// 			}
            }

            $seaocore_table_exist = $db->query("SHOW TABLES LIKE 'engine4_socialengineaddons'")->fetch();
            if (!empty($seaocore_table_exist)) {
                $seaocore_column_exist = $db->query("SHOW COLUMNS FROM engine4_socialengineaddons LIKE 'is_activate'")->fetch();
                if (empty($seaocore_column_exist)) {
                    $db->query("ALTER TABLE `engine4_socialengineaddons` ADD `is_activate` int( 11 ) NOT NULL DEFAULT '0' AFTER `ptype`");
                }

                $seaocore_cores = $db->query("SELECT * FROM `engine4_socialengineaddons`")->fetchAll();
                foreach ($seaocore_cores as $seaocore) {
                    $db->query("INSERT IGNORE INTO engine4_seaocores ( `module_name`, `title`, `description`, `version`, `is_installed`, `category`, `ptype`, `is_activate` ) VALUES ('" . $seaocore['module_name'] . "', '" . $seaocore['title'] . "', '" . $seaocore['description'] . "', '" . $seaocore['version'] . "', " . $seaocore['is_installed'] . ", '" . $seaocore['category'] . "', '" . $seaocore['ptype'] . "'," . $seaocore['is_activate'] . ");");
                }
            }


            //update info tool tip values.
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings', 'name')
                    ->where('name like ?', 'socialengineaddon.information.link%');
            $check_socialengineaddons = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            if (!empty($check_socialengineaddons)) {
                foreach ($check_socialengineaddons as $check) {
                    $replace = str_replace("socialengineaddon", "seaocore", $check);
                    $db->query('UPDATE `engine4_core_settings` SET `name` = \'' . $replace . '\' WHERE `engine4_core_settings`.`name` = \'' . $check . '\';');
                }
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings', 'name')
                    ->where('name like ?', 'socialengineaddon.action.link%');
            $check_socialengineaddon = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            if (!empty($check_socialengineaddon)) {
                foreach ($check_socialengineaddon as $check_action) {
                    $replace = str_replace("socialengineaddon", "seaocore", $check_action);
                    $db->query('UPDATE `engine4_core_settings` SET `name` = \'' . $replace . '\' WHERE `engine4_core_settings`.`name` = \'' . $check_action . '\';');
                }
            }


            $db->query('UPDATE `engine4_core_content` SET `name` = "seaocore.feed" WHERE `engine4_core_content`.`name` = "socialengineaddon.feed";');


            $siteapgeTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_content\'')->fetch();
            if (!empty($siteapgeTable)) {
                $db->query('UPDATE `engine4_sitepage_content` SET `name` = "seaocore.feed" WHERE `engine4_sitepage_content`.`name` = "socialengineaddon.feed";');
                $db->query('UPDATE `engine4_sitepage_admincontent` SET `name` = "seaocore.feed" WHERE `engine4_sitepage_admincontent`.`name` = "socialengineaddon.feed";');
            }
            $sitebusinessTable = $db->query('SHOW TABLES LIKE \'engine4_sitebusiness_content\'')->fetch();
            if (!empty($sitebusinessTable)) {
                $db->query('UPDATE `engine4_sitebusiness_content` SET `name` = "seaocore.feed" WHERE `engine4_sitebusiness_content`.`name` = "socialengineaddon.feed";');
                $db->query('UPDATE `engine4_sitebusiness_admincontent` SET `name` = "seaocore.feed" WHERE `engine4_sitebusiness_admincontent`.`name` = "socialengineaddon.feed";');
            }




            //delete socialengine entries from core menuitems. table.
            $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "core_admin_plugins_Socialengineaddon";');
            $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "socialengineaddon_admin_upgrade";');
            $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "socialengineaddon_admin_news";');
            $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "socialengineaddon_admin_info";');
            $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "socialengineaddon_admin_main_infotooltip";');
            $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "socialengineaddon_admin_main_lightbox";');


            //here we chnage the socialengineaddon to seaocore.
            $this->updateSettingName("socialengineaddon.tag.type", "seaocore.tag.type");
            $this->updateSettingName("socialengineaddon.lightbox.option.display", "seaocore.lightbox.option.display");
            $this->updateSettingName("socialengineaddon.photo.title", "seaocore.photo.title");
            $this->updateSettingName("socialengineaddon.photo.report", "seaocore.photo.report");
            $this->updateSettingName("socialengineaddon.photo.share", "seaocore.photo.share");




// 			$select = new Zend_Db_Select($db);
// 			$value = $select
// 				->from('engine4_core_settings', 'value')
// 				->where('name = ?', 'socialengineaddon.display.lightbox')
// 				->query()
// 				->fetchColumn();
// 			if (!empty($value)) {
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('seaocore.display.lightbox', $value);");
// 			}
// 			
// 			$select = new Zend_Db_Select($db);
// 			$tag_type_value = $select
// 				->from('engine4_core_settings', 'value')
// 				->where('name = ?', 'socialengineaddon.tag.type')
// 				->query()
// 				->fetchColumn();
// 			if (!empty($tag_type_value)) {
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('seaocore.tag.type', $tag_type_value);");
// 			}
// 			
// 			$select = new Zend_Db_Select($db);
// 			$option_display_value = $select
// 				->from('engine4_core_settings', 'value')
// 				->where('name = ?', 'socialengineaddon.lightbox.option.display')
// 				->query()
// 				->fetchColumn();
// 			if (!empty($option_display_value)) {
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('seaocore.lightbox.option.display', $option_display_value);");
// 			}
        }

        //START CODE FOR INSERTING THE LIGHBOX WIDGET IN THE HEADER
        $select = new Zend_Db_Select($db);
        $page_id = $select
                ->from('engine4_core_pages', array('page_id'))
                ->where('name = ?', 'header')
                ->query()
                ->fetchColumn();

        if (!empty($page_id)) {
            $select = new Zend_Db_Select($db);
            $parent_content_id = $select
                    ->from('engine4_core_content', array('content_id'))
                    ->where('page_id =?', $page_id)
                    ->where('type =?', 'container')
                    ->where('name =?', 'main')
                    ->query()
                    ->fetchColumn();

            $select = new Zend_Db_Select($db);
            $content_id = $select
                    ->from('engine4_core_content', array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name =?', 'seaocore.seaocores-lightbox')
                    ->query()
                    ->fetchColumn();
            if (empty($content_id)) {
                $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				  ($page_id, 'widget', 'seaocore.seaocores-lightbox', '999', $parent_content_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
            }
        }
        //END CODE FOR INSERTING THE LIGHBOX WIDGET IN THE HEADER    
        //SCRIPT FOR INSERTING AND UPDATING THE BACKGOUND COLOR, FONT COLOR OF THE LIGHTBOX
        $select = new Zend_Db_Select($db);
        $version = 0;
        $version = $select
                ->from('engine4_core_modules', 'version')
                ->where('name = ?', 'socialengineaddon')
                ->where('version <= ?', '4.2.0')
                ->query()
                ->fetchColumn();
        $name = 0;
        $value = 0;
        if (!empty($version)) {

            $select = new Zend_Db_Select($db);
            $value = $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitealbum.photolightbox.fontcolor')
                    ->orWhere('name = ?', 'sitepagealbum.photolightbox.fontcolor')
                    ->orWhere('name = ?', 'sitepagenote.photolightbox.fontcolor');

            $lightboxDisplayFontSettings = $select->query()->fetchObject();
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'seaocore.photolightbox.fontcolor');
            $seaocore_photolightbox_fontcolor = $select->query()->fetchObject();
            if (!empty($lightboxDisplayFontSettings) && !empty($seaocore_photolightbox_fontcolor)) {
                $db->update('engine4_core_settings', array('value' => $lightboxDisplayFontSettings->value), array('name = ?' => 'seaocore.photolightbox.fontcolor'));
            } elseif (!empty($lightboxDisplayFontSettings) && empty($seaocore_photolightbox_fontcolor)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photolightbox.fontcolor',
                    'value' => $lightboxDisplayFontSettings->value
                ));
            } elseif (empty($lightboxDisplayFontSettings) && empty($seaocore_photolightbox_fontcolor)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photolightbox.fontcolor',
                    'value' => '#FFFFFF'
                ));
            } elseif (empty($lightboxDisplayFontSettings) && !empty($seaocore_photolightbox_fontcolor)) {
                $db->update('engine4_core_settings', array('value' => '#FFFFFF'), array('name = ?' => 'seaocore.photolightbox.fontcolor'));
            }

            $select = new Zend_Db_Select($db);
            $value = $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitealbum.photolightbox.bgcolor')
                    ->orWhere('name = ?', 'sitepagealbum.photolightbox.bgcolor')
                    ->orWhere('name = ?', 'sitepagenote.photolightbox.bgcolor');

            $lightboxDisplayBgSettings = $select->query()->fetchObject();
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'seaocore.photolightbox.bgcolor');
            $seaocore_photolightbox_bgcolor = $select->query()->fetchObject();
            if (!empty($lightboxDisplayBgSettings) && !empty($seaocore_photolightbox_bgcolor)) {
                $db->update('engine4_core_settings', array('value' => $lightboxDisplayBgSettings->value), array('name = ?' => 'seaocore.photolightbox.bgcolor'));
            } elseif (!empty($lightboxDisplayBgSettings) && empty($seaocore_photolightbox_bgcolor)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photolightbox.bgcolor',
                    'value' => $lightboxDisplayBgSettings->value
                ));
            } elseif (empty($lightboxDisplayBgSettings) && empty($seaocore_photolightbox_bgcolor)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photolightbox.bgcolor',
                    'value' => '#000000'
                ));
            } elseif (empty($lightboxDisplayBgSettings) && !empty($seaocore_photolightbox_bgcolor)) {
                $db->update('engine4_core_settings', array('value' => '#000000'), array('name = ?' => 'seaocore.photolightbox.bgcolor'));
            }

            $select = new Zend_Db_Select($db);
            $value = $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitealbum.photolightbox.show')
                    ->orWhere('name = ?', 'sitepagealbum.photolightbox.show')
                    ->orWhere('name = ?', 'sitepagenote.photolightbox.show');

            $lightboxDisplaySettings = $select->query()->fetchObject();
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'seaocore.display.lightbox');
            $seaocore_display_lightbox = $select->query()->fetchObject();

            if (!empty($lightboxDisplaySettings) && !empty($seaocore_display_lightbox)) {
                $db->update('engine4_core_settings', array('value' => $lightboxDisplaySettings->value), array('name = ?' => 'seaocore.display.lightbox'));
            } elseif (!empty($lightboxDisplaySettings) && empty($seaocore_display_lightbox)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.display.lightbox',
                    'value' => $lightboxDisplaySettings->value
                ));
            } elseif (empty($lightboxDisplaySettings) && empty($seaocore_display_lightbox)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.display.lightbox',
                    'value' => 1
                ));
            } elseif (empty($lightboxDisplaySettings) && !empty($seaocore_display_lightbox)) {
                $db->update('engine4_core_settings', array('value' => 1), array('name = ?' => 'seaocore.display.lightbox'));
            }

            $select = new Zend_Db_Select($db);
            $value = $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitealbum.photo.download');

            $downloadSettings = $select->query()->fetchObject();
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'seaocore.photo.download');
            $seaocore_photo_download = $select->query()->fetchObject();

            if (!empty($downloadSettings) && !empty($seaocore_photo_download)) {
                $db->update('engine4_core_settings', array('value' => $downloadSettings->value), array('name = ?' => 'seaocore.photo.download'));
            } elseif (!empty($downloadSettings) && empty($seaocore_photo_download)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photo.download',
                    'value' => $downloadSettings->value
                ));
            } elseif (empty($downloadSettings) && empty($seaocore_photo_download)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photo.download',
                    'value' => 1
                ));
            } elseif (empty($downloadSettings) && !empty($seaocore_photo_download)) {
                $db->update('engine4_core_settings', array('value' => 1), array('name = ?' => 'seaocore.photo.download'));
            }

            $select = new Zend_Db_Select($db);
            $value = $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitealbum.photo.makeprofile');
            $makeProfilePhotoSettings = $select->query()->fetchObject();
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'seaocore.photo.makeprofile');
            $seaocore_photo_makeprofile = $select->query()->fetchObject();

            if (!empty($makeProfilePhotoSettings) && !empty($seaocore_photo_makeprofile)) {
                $db->update('engine4_core_settings', array('value' => $makeProfilePhotoSettings->value), array('name = ?' => 'seaocore.photo.makeprofile'));
            } elseif (!empty($makeProfilePhotoSettings) && empty($seaocore_photo_makeprofile)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photo.makeprofile',
                    'value' => $makeProfilePhotoSettings->value
                ));
            } elseif (empty($makeProfilePhotoSettings) && empty($seaocore_photo_makeprofile)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.photo.makeprofile',
                    'value' => 1
                ));
            } elseif (empty($makeProfilePhotoSettings) && !empty($seaocore_photo_makeprofile)) {
                $db->update('engine4_core_settings', array('value' => 1), array('name = ?' => 'seaocore.photo.makeprofile'));
            }

            $select = new Zend_Db_Select($db);
            $value = $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitealbum.lightboxads');
            $lightboxadsSettings = $select->query()->fetchObject();
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'seaocore.lightboxads');
            $seaocore_photo_lightboxads = $select->query()->fetchObject();

            if (!empty($lightboxadsSettings) && !empty($seaocore_photo_lightboxads)) {
                $db->update('engine4_core_settings', array('value' => $lightboxadsSettings->value), array('name = ?' => 'seaocore.lightboxads'));
            } elseif (!empty($lightboxadsSettings) && empty($seaocore_photo_lightboxads)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.lightboxads',
                    'value' => $lightboxadsSettings->value
                ));
            } elseif (empty($lightboxadsSettings) && empty($seaocore_photo_lightboxads)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.lightboxads',
                    'value' => 1
                ));
            } elseif (empty($lightboxadsSettings) && !empty($seaocore_photo_lightboxads)) {
                $db->update('engine4_core_settings', array('value' => 1), array('name = ?' => 'seaocore.lightboxads'));
            }

            $select = new Zend_Db_Select($db);
            $value = $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitealbum.adtype');
            $adtypeSettings = $select->query()->fetchObject();
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'seaocore.adtype');
            $seaocore_photo_adtype = $select->query()->fetchObject();

            if (!empty($adtypeSettings) && !empty($seaocore_photo_adtype)) {
                $db->update('engine4_core_settings', array('value' => $adtypeSettings->value), array('name = ?' => 'seaocore.adtype'));
            } elseif (!empty($adtypeSettings) && empty($seaocore_photo_adtype)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.adtype',
                    'value' => $adtypeSettings->value
                ));
            } elseif (empty($adtypeSettings) && empty($seaocore_photo_adtype)) {
                $db->insert('engine4_core_settings', array(
                    'name' => 'seaocore.adtype',
                    'value' => 3
                ));
            } elseif (empty($adtypeSettings) && !empty($seaocore_photo_adtype)) {
                $db->update('engine4_core_settings', array('value' => 3), array('name = ?' => 'seaocore.adtype'));
            }
        } else {
// 				$this->updateSettingName("socialengineaddon.photo.download", "seaocore.photo.download");
// 				$this->updateSettingName("socialengineaddon.photo.makeprofile", "seaocore.photo.makeprofile");
// 				$this->updateSettingName("socialengineaddon.adtype", "seaocore.adtype");
// 				$this->updateSettingName("socialengineaddon.lightboxads", "seaocore.lightboxads");
// 				$this->updateSettingName("socialengineaddon.photolightbox.fontcolor", "seaocore.photolightbox.fontcolor");
// 				$this->updateSettingName("socialengineaddon.photolightbox.bgcolor", "seaocore.photolightbox.bgcolor");
            //$this->updateSettingName("socialengineaddon.display.lightbox", "seaocore.display.lightbox");
        }

        //START FOR UPDATE SEAO IN THE MENU OF THE ADMIN PANL.
        $modArray = array('advancedactivity', 'birthday', 'list', 'sitelike', 'advancedslideshow', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'sitealbum', 'siteslideshow', 'sitetagcheckin', 'sitereviewlistingtype', 'document', 'recipe', 'sitemailtemplates', 'sitemobile', 'sitereview', 'sitevideoview', 'eventdocument', 'peopleyoumayknow', 'sitefaq', 'sitetutorial', 'userconnection', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitepagemember', 'sitepageurl', 'sitepageoffer', 'sitepagebadge', 'sitepagelikebox', 'sitepageinvite', 'sitepageadmincontact', 'sitepageform', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', '
sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitebusinessmember', 'sitebusinessurl');

        foreach ($modArray as $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$value")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_menuitems', array('label'))
                        ->where('name = ?', "core_admin_main_plugins_$value")
                        ->where('label NOT LIKE "%SEAO - %"')
                        ->where('enabled = ?', 1);
                $getLabel = $select->query()->fetchObject();
                if (!empty($getLabel)) {
                    $label = $getLabel->label;
                    $db->query("UPDATE  `engine4_core_menuitems` SET  `label` =  'SEAO - $label' WHERE  `engine4_core_menuitems`.`name` ='core_admin_main_plugins_$value';");
                }
            }
        }

        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Discount Coupons' WHERE  `engine4_core_menuitems`.`name` ='core_admin_plugins_sitecoupon';");

        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Userconnection' WHERE  `engine4_core_menuitems`.`name` ='core_admin_main_plugins_Userconn';");


        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - E-commerce Store (Magento Integration)' WHERE  `engine4_core_menuitems`.`name` ='core_siteestore_api';");

        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Pokes' WHERE  `engine4_core_menuitems`.`name` ='core_admin_main_plugins_pokesettings';");

        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - Suggestions' WHERE  `engine4_core_menuitems`.`name` ='module_suggestion';");

        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'SEAO - SocialEngineAddOns Core' WHERE  `engine4_core_menuitems`.`name` ='core_admin_plugins_Seaocore';");

        //END FOR UPDATE SEAO IN THE MENU OF THE ADMIN PANL.
        $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'seaocore_admin_main_integrated'");

        $seocoreBannedUrlTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
        if (empty($seocoreBannedUrlTable)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_bannedpageurls` (
							`bannedpageurl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							`word` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
							PRIMARY KEY (`bannedpageurl_id`),
							UNIQUE KEY `word` (`word`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
        }

        $db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES
									('sitestaticpage'),('static'),('music'),('polls'),('blogs'),('videos'),	('classifieds'),('albums'),('events'),	('groups'),('group'),
									('forums'),('invite'),('recipeitems'),('ads'),	('likes'),('documents'),('sitepage'),
									('sitepagepoll'),('sitepageoffer'),('sitepagevideo'),('sitepagedocument'),('sitepagenote'),
									('sitepageevent'),('sitepagemusic'),('sitepageinvite'),('sitepagereview'),('sitepagebadge'),
									('sitepageform'),('sitepagealbum'),('sitepagediscussion'),('sitebusiness'),
									('sitebusinesspoll'),('sitebusinessoffer'),('sitebusinessvideo'),('sitebusinessdocument'),('sitebusinessnote'),
									('sitebusinessevent'),('sitebusinessmusic'),('sitebusinessinvite'),('sitebusinessreview'),('sitebusinessbadge'),
									('sitebusinessform'),('sitebusinessalbum'),('sitebusinessdiscussion'),('sitegroup'),
									('sitegrouppoll'),('sitegroupoffer'),('sitegroupvideo'),('sitegroupdocument'),('sitegroupnote'),
									('sitegroupevent'),('sitegroupmusic'),('sitegroupinvite'),('sitegroupreview'),('sitegroupbadge'),
									('sitegroupform'),('sitegroupalbum'),('sitegroupdiscussion'),('sitestore'),
									('sitestorepoll'),('sitestoreoffer'),('sitestorevideo'),('sitestoredocument'),('sitestorenote'),
									('sitestoreevent'),('sitestoremusic'),('sitestoreinvite'),('sitestorereview'),('sitestorebadge'),
									('sitestoreform'),('sitestorealbum'),('sitestorediscussion'),('recipe'),('sitelike'),('suggestion'),('advanceslideshow'),('feedback'),('grouppoll'),('groupdocumnet'),('sitealbum'),('siteslideshow'),('userconnection'),('communityad'),('list'),('article'),
									('listing'),('store'),('page-videos'),('pageitem'),('pageitems'),('page-events'),('page-documents'),('page-offers'),('page-notes'),('page-invites'),('page-form'),('page-music'),
									('page-reviews'),('businessitem'),('businessitems'),('business-events'),('business-documents'),('business-offers'),('business-notes'),('business-invites'),('business-form'),('business-music'),
									('business-reviews'),('group-videos'),('groupitem'),('groupitems'),('group-events'),('group-documents'),('group-offers'),('group-notes'),('group-invites'),('group-form'),('group-music'),('group-reviews'),('store-videos'),('storeitem'),('storeitems'),('store-events'),
									('store-documents'),('store-offers'),('store-notes'),('store-invites'),('store-form'),('store-music'),('store-reviews'),('listingitems'),('market'),('document'),('pdf'),('pokes'),('facebook'),('album'),('photo'),('files'),('file'),('page'),
									('store'),('backup'),('question'),('answer'),('questions'),('answers'),('newsfeed'),('birthday'),('wall'),('profiletype'),('memberlevel'),('members'),('member'),('memberlevel'),
									('level'),('slideshow'),('seo'),('xml'),('cmspages'),('favoritepages'),('help'),('rss'),
									('stories'),('story'),('visits'),('points'),('vote'),('advanced'),('listingitem');");

        $searchFormTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_searchformsetting\'')->fetch();
        if (!empty($searchFormTable)) {
            $moduleIndex = $db->query("SHOW INDEX FROM `engine4_seaocore_searchformsetting` WHERE Key_name = 'module'")->fetch();
            if (empty($moduleIndex)) {
                $db->query("ALTER TABLE `engine4_seaocore_searchformsetting` ADD INDEX ( `module` )");
            }
        }

        // ADD SETTINGS TAB IN ADMIN PANEL FOR ADVANCED CALENDAR SETTING
        $isSettingsTab = $db->query("SELECT * FROM `engine4_core_menuitems` WHERE `name` = 'seaocore_admin_settings' LIMIT 1")->fetch();
        if (empty($isSettingsTab)) {
            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('seaocore_admin_settings', 'seaocore', 'General Settings', NULL , '{\"route\":\"admin_default\",\"module\":\"seaocore\",\"controller\":\"settings\"}', 'seaocore_admin_main', NULL , '1', '0', '8');");
        }

        $locationTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_locationcontents\'')->fetch();
        if (empty($locationTable)) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `engine4_seaocore_locationcontents` (
                  `locationcontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `latitude` double NOT NULL,
                  `longitude` double NOT NULL,              
                  `status` tinyint(1) NOT NULL DEFAULT '1',
                    `formatted_address` text COLLATE utf8_unicode_ci,
                    `country` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `state` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `zipcode` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `city` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,                  
                  PRIMARY KEY (`locationcontent_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;      
            ");
        } else {

            $latitude = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'latitude'")->fetch();
            if (empty($latitude)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `latitude` double NOT NULL");
            }

            $longitude = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'longitude'")->fetch();
            if (empty($longitude)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `longitude` double NOT NULL");
            }

            $formattedAddress = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'formatted_address'")->fetch();
            if (empty($formattedAddress)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `formatted_address` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
            }

            $country = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'country'")->fetch();
            if (empty($country)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `country` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL");
            }

            $state = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'state'")->fetch();
            if (empty($state)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `state` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL");
            }

            $zipCode = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'zipcode'")->fetch();
            if (empty($zipCode)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `zipcode` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL");
            }

            $city = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'city'")->fetch();
            if (empty($city)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `city` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL");
            }

            $address = $db->query("SHOW COLUMNS FROM engine4_seaocore_locationcontents LIKE 'address'")->fetch();
            if (empty($address)) {
                $db->query("ALTER TABLE `engine4_seaocore_locationcontents` ADD `address` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL");
            }
        }

        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Locations & Maps' WHERE `engine4_core_menuitems`.`name` = 'seaocore_admin_map';");
        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'General Settings' WHERE `engine4_core_menuitems`.`name` = 'seaocore_admin_settings';");

        $userInfoTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_userinfo\'')->fetch();
        if (empty($userInfoTable)) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `engine4_seaocore_userinfo` (
                `userinfo_id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(32) NOT NULL,
                `rating_avg` float NOT NULL,
                `rating_users` float NOT NULL,
                `review_count` int(11) NOT NULL,
                PRIMARY KEY (`userinfo_id`),
                UNIQUE KEY `user_id` (`user_id`)
              ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;      
            ");
        }

        parent::onInstall();
    }

    public function updateSettingName($oldSettingName, $newSettingName) {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings')
                ->where('name = ?', $oldSettingName);
        $info = $select->query()->fetch();
        if (!empty($info)) {
            $db->query('UPDATE `engine4_core_settings` SET `name` = \'' . $newSettingName . '\' WHERE `engine4_core_settings`.`name` = \'' . $oldSettingName . '\';');
        }
    }

}

?>
