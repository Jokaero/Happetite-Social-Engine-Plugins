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
class Seaocore_Widget_SitemobilePeopleLikeController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

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
		$seaocoreLike = Engine_Api::_()->getApi('like', 'seaocore');
    $this->view->paginator = $paginator = $seaocoreLike->peopleLike($resource_type, $resource_id);

    // Set item count per page and current page number
    $this->view->paginator = $paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page', 1));
    $this->_childCount = $paginator->getTotalItemCount();

    if($paginator->getTotalItemCount() <= 0 ) 
      return $this->setNoRender();

	}

  public function getChildCount() {
    return $this->_childCount;
  }
  
}