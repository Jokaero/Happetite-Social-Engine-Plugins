<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cover.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Form_Photo_DefaultCover extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Upload Default Cover Photo')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('id', 'cover_photo_form')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'Upload A Default Cover Photo');

    $this->addElement('File', 'Filedata', array(
        'label' => 'Choose a dafault cover photo.',
        'destination' => APPLICATION_PATH . '/public/temporary/',
        'validators' => array(
            array('Extension', false, 'jpg,jpeg,png,gif'),
        ),
      //'onchange' => 'javascript:uploadPhoto();'
    ));

    $this->addElement('Checkbox', 'siteusercoverphoto_setdefaultcoverphoto', array(
          'label' => 'Set this cover photo as default cover photo for the various pages on your site for all Member Levels.',
          'value' => 0,
    ));

    // Element: execute
    $this->addElement('Button', 'submit', array(
        'label' => 'Submit',
        'type' => 'submit',
        'onclick' => 'javascript:uploadPhoto();',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' =>"javascript:parent.Smoothbox.close()",
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));

  }

}