<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($viewer) && !empty($viewer->level_id) ) {
    $level_id = $viewer->level_id;
    if (!$this->_helper->requireUser()->isValid())
      return;
    }
    $product_type=$this->_getParam('type');
    if( ($level_id != 1) || (empty($product_type)) ) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $type = $this->_getParam('type', null);
		if( strstr($type, "sitevideoview") || strstr($type, "sitetagcheckin") || strstr($type, "siteestore") || strstr($type, "sitereview") ) {
			$this->_setParam('plugin_title', @base64_decode($this->_getParam('plugin_title', null)));
		}
    include_once APPLICATION_PATH . '/application/modules/Seaocore/controllers/license/license1.php';
  }

  public function uploadcamimageAction() {
      $session = new Zend_Session_Namespace();

      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/webcam';
      
      $profile_photo=$this->_getParam('profile_photo', 0);
      if( @is_dir($path) ) {
				// Delete all before inserted files.
				$this->destroy($path);
      }

      // Create directory if not exist.
      if (!@is_dir($path) && !@mkdir($path, 0777, true)) {
				@mkdir(dirname($path));
				@chmod(dirname($path), 0777);
				@touch($path);
				@chmod($path, 0777);
      }

      $filename = date('YmdHis') . '.png';
      $result = file_put_contents('public/webcam/' . $filename, file_get_contents('php://input'));
      if (!$result) {
				print "ERROR: Failed to write data to $filename, check permissions\n";
				exit();
      }

      if(!$profile_photo){
				$session->tem_file_name = $filename;
      } else {
        $session->tem_file_main_photo_name = $filename;
      }
  }

  private function destroy($dir) {
    $handle=opendir($dir);

    while (($file = readdir($handle))!==false) {
      @chmod($dir.'/'.$file, 0777);
      @unlink($dir.'/'.$file);
    }

    closedir($handle);
    return;
  }
  
  public function upgradeSeaoPluginsAction() {
		if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('is_seaocore_install', 1);
	    $this->view->isPost = TRUE;
		}
  }
  
  //ACTION FOR SHOWING LOCAITON IN MAP WITH GET DIRECTION
  public function viewMapAction() {

    $this->view->resouce_type = $resouce_type = $this->_getParam('resouce_type');
    $this->view->is_mobile = $is_mobile = $this->_getParam('is_mobile');

		if ($resouce_type == 'classified') {
			$table_option =  Engine_Api::_()->fields()->getTable('classified', 'search');
			$table_option_name = $table_option->info('name'); 
			$select_options = $table_option->select()
																		->from($table_option_name)
																		->where($table_option_name. '.item_id =?', $this->_getParam('id'));
			$searchItem = $table_option->fetchRow($select_options);
			if (!empty($searchItem)) {
				$seLocationsTable = Engine_Api::_()->getDbtable('locations', 'seaocore');
				$select = $seLocationsTable->select()->where('location = ?', $searchItem->location);
				$results = $seLocationsTable->fetchRow($select);
				if(empty($results->location_id)) {
					//Accrodeing to event  location entry in the seaocore location table.
					if (!empty($searchItem->location)) {
						$seaoLocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocationId($searchItem->location);
					  $select = $seLocationsTable->select()->where('location = ?', $searchItem->location);
						$item = $seLocationsTable->fetchRow($select);
					}
				} else  {
						$select = $seLocationsTable->select()->where('location = ?', $searchItem->location);
						$item = $seLocationsTable->fetchRow($select);
				}
			}
		}

    if (empty($is_mobile)) {
			$this->_helper->layout->setLayout('default-simple');
    }

    if (!$this->_getParam('id'))
      return $this->_forward('notfound', 'error', 'core');
      
    $userGeoSettings = '';
		switch($resouce_type) { 
			case 'sitepage_page' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'sitepage');
				$id = 'page_id';
			break;
			case 'sitebusiness_business' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'sitebusiness');
				$id = 'business_id';
			break;
		  case 'list_listing' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'list');
				$id = 'listing_id';
			break;
		  case 'sitereview_listing' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'sitereview');
				$id = 'listing_id';
				$userGeoSettings = Engine_Api::_()->seaocore()->geoUserSettings('sitereview');
			break;
			case 'sitegroup_group' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
				$id = 'group_id';
			break;
    	case 'sitestore_store' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'sitestore');
				$id = 'store_id';
			break;
    	case 'sitestoreproduct_product' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct');
				$id = 'product_id';
			break;  
    	case 'siteevent_event' :
				$dbtable = Engine_Api::_()->getDbtable('locations', 'siteevent');
				$id = 'event_id';
			break;			
			case 'recipe' : 
				$dbtable = Engine_Api::_()->getDbtable('locations', 'recipe');
				$id = 'recipe_id';
			break;
		  case 'seaocore' :
				$dbtable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
				$id = 'locationitem_id';
				$userGeoSettings = Engine_Api::_()->seaocore()->geoUserSettings('sitetagcheckin');
			break;
		  case 'page_event':
		  	$dbtable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
				$id = 'locationitem_id';
			break;
      case 'event':
				$dbtable = Engine_Api::_()->getDbtable('locations', 'seaocore');
				$id = 'location_id';
			case 'video':
				$dbtable = Engine_Api::_()->getDbtable('locations', 'seaocore');
				$id = 'location_id';
      case 'group':
				$dbtable = Engine_Api::_()->getDbtable('locations', 'seaocore');
				$id = 'location_id';
			break;
			default:
			exit();
			break;
		}
		
		$this->view->userSettings = $userGeoSettings;
    $location_id = $this->_getParam('location_id');
    $flag = $this->_getParam('flag');
    
    if($id) {
			if ($resouce_type != 'classified') {
				//if ($resouce_type == 'seaocore' || $resouce_type == 'event' || $resouce_type == 'page_event' || $resouce_type == 'recipe') {
				
				if (empty($location_id) && empty($flag)) {
					$select = $dbtable->select()->where($dbtable->info('name') .".$id =?", $this->_getParam('id'));
				} else {
					$select = $dbtable->select()
										->where($dbtable->info('name') .".$id =?", $this->_getParam('id'))
										->where($dbtable->info('name') .".location_id =?", $location_id);
				}
				$item = $dbtable->fetchRow($select);
	// 			} else {
	// 				$item = $dbtable->getLocation($this->_getParam('id'));
	// 			}
			}
    }

    if(empty($item)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $params = (array) $item->toArray();
    if (is_array($params)) {
      $this->view->checkin = $params;
    } else {
      return $this->_forward('notfound', 'error', 'core');
    }
  }
  
  public function tagSuggestAction()
  {
    $tags = Engine_Api::_()->seaocore()->getTagsByText($this->_getParam('text'), $this->_getParam('limit', 40), $this->_getParam('resourceType'));
    $data = array();
    $mode = $this->_getParam('struct');

    if( $mode == 'text' )
    {
      foreach( $tags as $tag )
      {
        $data[] = $tag->text;
      }
    }
    else
    {
      foreach( $tags as $tag )
      {
        $data[] = array(
          'id' => $tag->tag_id,
          'label' => $tag->text
        );
      }
    }

    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }  
}
?>
