<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Main.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Form_Photo_Main extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Upload Profile Picture')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('id', 'cover_photo_form')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'Upload a Profile Picture');

    $this->addElement('File', 'Filedata', array(
        'label' => 'Choose a profile picture.',
        'destination' => APPLICATION_PATH . '/public/temporary/',
        'validators' => array(
            array('Extension', false, 'jpg,jpeg,png,gif'),
        ),
      'onchange' => 'javascript:uploadPhoto();'
    ));

  }

}