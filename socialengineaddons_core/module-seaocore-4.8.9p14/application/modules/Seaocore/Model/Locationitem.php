<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Model_Locationitem extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_searchTriggers = array('title', 'body', 'search');
  protected $_parent_is_owner = true;
}