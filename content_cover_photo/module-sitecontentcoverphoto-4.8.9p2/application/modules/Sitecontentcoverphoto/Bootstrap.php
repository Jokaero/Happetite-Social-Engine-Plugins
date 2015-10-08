<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Bootstrap extends Engine_Application_Bootstrap_Abstract {
    
  public function __construct($application) {
    parent::__construct($application);
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/controllers/license/license.php';
  }
  
}