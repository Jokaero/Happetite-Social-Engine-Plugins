<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cover.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Form_Photo_Cover extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Upload Cover Photo')
                ->setDescription('Cover photo makes your content look attractive. Choose and upload a cover photo.')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib('id', 'cover_photo_form')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'Upload a Cover Photo');

        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose a cover photo.',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'description' => 'The recommended height for the photo is 300px.',
            'validators' => array(
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'onchange' => 'javascript:uploadPhoto();'
        ));
        $this->Filedata->addDecorator('Description', array('placement' => 'APPEND', 'class' => 'description', 'escape' => false));
    }

}
