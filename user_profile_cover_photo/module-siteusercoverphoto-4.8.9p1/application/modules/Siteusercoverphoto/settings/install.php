<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Installer extends Engine_Package_Installer_Module {

  
  public function onPreinstall() {
    $db = $this->getDb();  
    
    $isTableExist = $db->query("SHOW TABLES LIKE 'engine4_album_albums'")->fetch();
    if( !empty($isTableExist) ) {
      $PRODUCT_TYPE = 'siteusercoverphoto';
      $PLUGIN_TITLE = 'Siteusercoverphoto';
      $PLUGIN_VERSION = '4.8.8';
      $PLUGIN_CATEGORY = 'plugin';
      $PRODUCT_DESCRIPTION = 'User Cover Photo';
      $_PRODUCT_FINAL_FILE = 0;
      $SocialEngineAddOns_version = '4.8.9p12';
      $PRODUCT_TITLE = 'User Cover Photo';
      $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
      $is_file = @file_exists($file_path);

      if (empty($is_file)) {
        include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
      } else {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
        $is_Mod = $select->query()->fetchObject();
        if (empty($is_Mod)) {
          include_once $file_path;
        }
      }
      parent::onPreinstall();
    }else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the SocialEngine Album Plugin on your site yet. Please install it first before installing the User Profile Cover Photo. If you do not want to install SocialEngine Album Plugin then please file a support ticket from your SocialEngineAddOns <a href='http://www.socialengineaddons.com/user/login' target='_blank'>client area</a></span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }
  
  
  public function onInstall() {
    $db = $this->getDb();
    
    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteusercoverphoto';");
    $user_cover = $db->query("SHOW COLUMNS FROM engine4_users LIKE 'user_cover'")->fetch();
    if (empty($user_cover)) {
      $db->query("ALTER TABLE  `engine4_users` ADD  `user_cover` INT( 11 ) NOT NULL DEFAULT  '0';");
    }

    $album_cover_params = $db->query("SHOW COLUMNS FROM engine4_album_albums LIKE 'cover_params'")->fetch();
    if (empty($album_cover_params)) {
      $db->query("ALTER TABLE  `engine4_album_albums` ADD  `cover_params` VARCHAR( 265 ) NULL;");
    }

    $album_type = $db->query("SHOW COLUMNS FROM engine4_album_albums LIKE 'type'")->fetch();
    if (!empty($album_type)) {
      $db->query("ALTER TABLE `engine4_album_albums` CHANGE  `type`  `type` ENUM(  'wall','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','wall_onlyme','wall_friend','wall_network','profile','message','blog','cover' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
    }

    $user_cover = $db->query("SHOW COLUMNS FROM engine4_user_fields_meta LIKE 'cover'")->fetch();
    if (empty($user_cover)) {
      $db->query("ALTER TABLE  `engine4_user_fields_meta` ADD  `cover` TINYINT( 1 ) NOT NULL DEFAULT  '1';");
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules', array('name'))
            ->where('enabled = ?', 1)
            ->where('name = ?', 'sitemobile');
    $name = $select->query()->fetchColumn();
    if ($name) {
      $sitemobileModuleTable = $db->query('SHOW TABLES LIKE \'engine4_sitemobile_modules\'')->fetch();
      if (!empty($sitemobileModuleTable)) {
        $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('siteusercoverphoto', '1', '1', '1', '1')");
      }
    }

		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings', array('value'))
            ->where('name = ?', 'siteusercoverphoto.change.tab.position');
    $value = $select->query()->fetchColumn();   

    if(!empty($value) || $value == '') {
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages', array('page_id'))
							->where('name = ?', 'user_profile_index');
			$page_id = $select->query()->fetchColumn();

			if(!empty($page_id)) {

				$select = new Zend_Db_Select($db);
				$select
								->from('engine4_core_content', array('content_id'))
								->where('name = ?', 'main')
								->where('page_id = ?', $page_id)
								->where('type = ?', 'container');
				$content_id = $select->query()->fetchColumn();

				if(!empty($content_id)) {
					$select = new Zend_Db_Select($db);
					$select
									->from('engine4_core_content', array('content_id'))
									->where('name = ?', 'middle')
									->where('page_id = ?', $page_id)
									->where('parent_content_id = ?', $content_id)
									->where('type = ?', 'container');
					$parent_content_id = $select->query()->fetchColumn();
					if(!empty($parent_content_id)) {
						$select = new Zend_Db_Select($db);
						$select
										->from('engine4_core_content', array('params'))
										->where('name = ?', 'core.container-tabs')
										->where('page_id = ?', $page_id)
										->where('parent_content_id = ?', $parent_content_id)
										->where('type = ?', 'widget');
						$params = $select->query()->fetchColumn();
            if(empty($params)) {
              $params = '{"max" : 5}';
            }
						$params = Zend_Json_Decoder::decode($params);
						$params['max'] = 5;
						$params = Zend_Json_Encoder::encode($params);
						$db->query("UPDATE `engine4_core_content` SET `params` = '$params' WHERE `engine4_core_content`.`name` = 'core.container-tabs' AND `engine4_core_content`.`page_id` = '" . $page_id . "' AND `engine4_core_content`.`parent_content_id` = '" . $parent_content_id . "' AND `engine4_core_content`.`type` = 'widget';");
					}
			  }
			}
    }
    
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules', array('name'))
            ->where('enabled = ?', 1)
            ->where('name = ?', 'sitemember');
    $sitememberName = $select->query()->fetchColumn();
    if ($sitememberName) {    
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content', array('params'))
                ->where('name = ?', 'siteusercoverphoto.user-cover-photo');
        $params = $select->query()->fetchColumn();    
        if(!empty($params)) {
            $params = Zend_Json::decode($params);
            if(!isset($params['showContent']['rating'])) {
                $params['showContent'][] = 'rating';
                $params = Zend_Json::encode($params);
                $db->query("UPDATE `engine4_core_content` SET `params` = '$params' WHERE `engine4_core_content`.`name` = 'siteusercoverphoto.user-cover-photo'");
            }
        }
    }
    
    $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'SEAO - User Profiles-Cover Photo, Banner & Site Branding' WHERE `engine4_core_menuitems`.`name` = 'core_admin_main_plugins_siteusercoverphoto' AND `engine4_core_menuitems`.`module` = 'siteusercoverphoto'");
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
    if(!empty($is_sitemobile_object)) {
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('siteusercoverphoto','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'siteusercoverphoto')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/siteusercoverphoto/integrated/0/redirect/install');
				} 
      }
    }
  }

}
