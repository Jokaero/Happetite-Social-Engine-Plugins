<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Seaocore_Widget_SitemobileFollowersController extends Engine_Content_Widget_Abstract {
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

		//GET VALUES
		$follow_user_str = 0 ;
		$this->view->page = $page = $this->_getParam('page' , 1 );
		$this->view->call_status = $call_status = $this->_getParam('call_status' , 'public' );
    $this->view->isajax = $isajax = $this->_getParam('isajax' , 0);
		//GET VIEWER ID
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//GET FOLLOW TABLE
		$followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
		$subject = Engine_Api::_()->core()->getSubject();
		$this->view->resource_type = $resource_type = $subject->getType(); 
		$this->view->resource_id = $resource_id = $subject->getIdentity();
    
		//HERE FUNCTION CALL FROM THE CORE.PHP FILE OR THIS IS SHOW NO OF FRIEND
		$this->view->paginator = $paginator = $followTable->getFollowDetails($call_status, $resource_type, $resource_id, $viewer_id, '');
		$this->view->paginator->setCurrentPageNumber($page);
		$this->view->paginator->setItemCountPerPage(10);

    if (!empty($isajax)) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    // Add count to title if configured
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    if($paginator->getTotalItemCount() < 0 ) 
      return $this->setNoRender();
	}

  public function getChildCount() {
    return $this->_childCount;
  }

}

?>