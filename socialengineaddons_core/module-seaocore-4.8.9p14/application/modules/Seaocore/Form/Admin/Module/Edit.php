<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Module_Edit extends Seaocore_Form_Admin_Module_Add {

  public function init() {
 
    parent::init();
    $this
      ->setTitle('Edit Module')
      ->setDescription('Use the form below to enable users to event for their content. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

  }
}