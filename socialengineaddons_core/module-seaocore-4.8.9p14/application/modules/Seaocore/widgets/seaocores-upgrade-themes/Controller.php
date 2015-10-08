<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_Widget_SeaocoresUpgradeThemesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();    
    
		if( !empty($_POST['level_id']) ) {
			$show_table = $_POST['level_id'];
		} else {
			$show_table = 1;
		}
    
    $sitereviewListingTypeVersion = null;
		$this->view->show_table = $show_table;
    $rss = Zend_Feed::import('http://www.socialengineaddons.com/themes/feed');
    $channel = array(
      'title'       => $rss->title(),
      'link'        => $rss->link(),
      'description' => $rss->description(),
      'items'       => array()
    );
    // Loop over each channel item and store relevant data
    foreach( $rss as $item )
    {
      $tempReviewListingTypePlugin = array();
      $product_type = $item->ptype();
  
      $modules_info = $this->module_info($product_type);

      $license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting($modules_info['key']);
      $plugin_info['title'] = $item->title();
      $plugin_info['ptype'] = $product_type;
      $plugin_info['product_version'] = $item->version();
      $plugin_info['key'] = $license_key;
      $plugin_info['link'] = $item->link();
      $plugin_info['price'] = $item->price();
      $plugin_info['socialengine_url'] = $item->socialengine_url();
      $plugin_info['running_version'] = !empty($modules_info['status'])? $modules_info['version']: 0;
      $plugin_info['name'] = $modules_info['name'];
      $product_images = explode("::", $item->image());      
      $tempVersion = $item->version();
      $channel['items'][$plugin_info['ptype']] = $plugin_info;
      if( !empty($tempReviewListingTypePlugin) ) {
          $channel['items'][$tempReviewListingTypePlugin['ptype']] = $tempReviewListingTypePlugin;
      }
    }
    $this->view->sitereviewListingTypeVersion = $sitereviewListingTypeVersion;
    $this->view->channel = $channel['items'];
  }

	public function module_info($product_type)
	{
    if($product_type == "sponsoredstories")
      $product_type = 'communityadsponsored';
    
		switch($product_type) {
			case 'userconnection':
				$name = 'userconnection';
				$key_firld = 'user.licensekey';
			break;
			case 'feedbacks':
				$name = 'feedback';
				$key_firld = 'feedback.license_key';
			break;
			case 'suggestion':
				$name = 'suggestion';
				$key_firld = 'suggestion.controllersettings';
			break;
			case 'peopleyoumayknow':
				$name = 'peopleyoumayknow';
				$key_firld = 'pymk.controllersettings';
			break;
			case 'siteslideshow':
				$name = 'siteslideshow';
				$key_firld = 'siteslideshow.controllersettings';
			break;
			case 'mapprofiletypelevel':
				$name = 'mapprofiletypelevel';
				$key_firld = 'mapprofiletypelevel.controllersettings';
			break;
			case 'documentsv4':
				$name = 'document';
				$key_firld = 'document.controllersettings';
			break;
			case 'groupdocumentsv4':
				$name = 'groupdocument';
				$key_firld = 'groupdocument.controllersettings';
			break;
			case 'backup':
				$name = 'dbbackup';
				$key_firld = 'dbbackup.controllersettings';
			break;
			case 'mcard':
				$name = 'mcard';
				$key_firld = 'mcard.controllersettings';
			break;
			case 'like':
				$name = 'sitelike';
				$key_firld = 'sitelike.controllersettings';
			break;
			case 'seaddons-core':
				$name = 'seaocore';
				$key_firld = '';
			break;
			default:
				$name = $product_type;
				$key_firld = $product_type . '.lsettings';
			break;				
		}
		$moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
		$moduleName = $moduleTable->info('name');
		$select = $moduleTable->select()
			->setIntegrityCheck(false)
			->from($moduleName, array('name', 'version'))
			->where('name = ?', $name)
      ->where('enabled = ?', 1)
			->limit(1);
		$module_info = $select->query()->fetchAll();
		if ( !empty($module_info) ) {
			$module_info_array['version'] = $module_info[0]['version'];
			$module_info_array['name'] = $module_info[0]['name'];
			$module_info_array['status'] = 1;
		} else {
			$module_info_array['status'] = 0;
			$module_info_array['name'] = 0;
		}
		$module_info_array['key'] = $key_firld;
    
		return $module_info_array;
	}
}
