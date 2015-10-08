<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Main.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Form_Photo_Editfontcolor extends Engine_Form {

    public function init() {

        $this->setTitle("Edit Font Color");

        //COLOR VALUE FOR FEATURED
        $this->addElement('Text', 'siteusercover_font_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowFontColor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Hidden', 'hiddenfontcolor', array('order' => 4));
        $this->addElement('Hidden', 'count', array('order' => 5));

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'onclick' => 'parent.Smoothbox.close()',
            'link' => true,
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'execute',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}