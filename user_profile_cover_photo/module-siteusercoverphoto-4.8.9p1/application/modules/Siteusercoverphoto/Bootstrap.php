<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
    include APPLICATION_PATH . '/application/modules/Siteusercoverphoto/controllers/license/license.php';
  }

  protected function _initFrontController() {
    $this->initActionHelperPath();

    //INITIALIZE GROUPPOLLS HELPER
    Zend_Controller_Action_HelperBroker::addHelper(new Siteusercoverphoto_Controller_Action_Helper_Usercoverfield());
  }

}
