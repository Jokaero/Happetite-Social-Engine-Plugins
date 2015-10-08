<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Seaocore
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Seaocore_Widget_PeopleLikeController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
		//DONT RENDER IF VEWER IS EMPTY
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if(empty($viewer_id)) {
			return $this->setNoRender();
    }

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

		//GET LIST SUBJECT
		$subject = Engine_Api::_()->core()->getSubject();
		$this->view->resource_type = $resource_type = $subject->getType(); 
		$this->view->resource_id = $resource_id = $subject->getIdentity();
		
		if ($resource_type == 'list_listing') {
			$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.listinglike.view', 3);
		} elseif ($resource_type == 'recipe') {
			$limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('recipe.recipelike.view', 3);
		}
		elseif ($resource_type == 'sitepage_page' || $resource_type == 'sitereview_listing' || 'sitebusiness_business') {
			$limit = $this->_getParam('itemCount', 3);
		} 

    $peopleLikeResults = Engine_Api::_()->getApi('like', 'seaocore')->peopleLike($resource_type, $resource_id, $limit);

    $this->view->like_count = $like_count = Engine_Api::_()->getApi('like', 'seaocore')->likeCount( $resource_type , $resource_id);

		$this->view->detail = 0;
		$this->view->results = array();

		if(Count($peopleLikeResults) > 0)	{
		
			foreach( $peopleLikeResults as $peopleLikeResult )	{
				$like_user_object[] = Engine_Api::_()->getItem('user', $peopleLikeResult['poster_id']);
			}
			$this->view->results = $like_user_object;
			
			if( !empty($like_count) && $like_count > $limit)	{
				$this->view->detail = 1;
			}
		}
		else {
			return $this->setNoRender();
		}
	}
}