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
class Siteusercoverphoto_Form_Photo_Cover extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Upload User Cover Photo')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib('id', 'cover_photo_form')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'Upload a Cover Photo');

        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose a cover photo.',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'description' => 'The recommended height for the photo is 300px.',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'validators' => array(
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'onchange' => 'javascript:uploadPhoto();'
        ));
    }

}
