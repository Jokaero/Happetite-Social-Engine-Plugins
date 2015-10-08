<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2014-06-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Geolocation_Edit extends Seaocore_Form_Admin_Geolocation_Add {
    
    protected $_item;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }    

    public function init() {
        
        parent::init();

        $this->setTitle('Edit Location');

				$this->submit->setLabel("Save Changes");
    }

}