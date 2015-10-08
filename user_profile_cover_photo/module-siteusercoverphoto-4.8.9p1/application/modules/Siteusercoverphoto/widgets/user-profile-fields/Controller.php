<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Widget_UserProfileFieldsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    $user = Engine_Api::_()->core()->getSubject('user');
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteusercoverphoto/View/Helper', 'Siteusercoverphoto_View_Helper');
    $siteuserCoverPhotoFields = Zend_Registry::isRegistered('siteuserCoverPhotoFields') ?  Zend_Registry::get('siteuserCoverPhotoFields') : null;
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
    $this->view->fieldValues = $this->view->userFieldValueLoop($this->view->subject(), $fieldStructure, $this->_getParam('customFields', 5));
    if(empty($this->view->fieldValues) || empty($siteuserCoverPhotoFields)) 
     return $this->setNoRender();
  }

}