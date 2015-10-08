<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Follow.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Seaocore_Model_Follow extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getOwner($type = null)
  {
    $poster = $this->getPoster();
    if( null === $type && $type !== $poster->getType() ) {
      return $poster->getOwner($type);
    }
    return $poster;
  }

  public function getPoster()
  {
    return Engine_Api::_()->getItem($this->poster_type, $this->poster_id);
  }

  public function __toString()
  {
    return $this->getPoster()->__toString();
  }
}
