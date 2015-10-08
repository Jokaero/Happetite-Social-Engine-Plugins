<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 6590 2014-06-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Geolocation_Add extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Add Location');

        $this->addElement('Text', 'title', array(
            'label' => 'Location Title',
            'required' => true
        ));

        $this->addElement('Text', 'location', array(
            'label' => 'Location Address',
            'required' => true,
            'onkeypress' => 'unsetLatLng()',
        ));
        
        $order = 998;
        $this->addElement('Hidden', 'latitude', array(
            'order' => $order++,
        ));
        
        $this->addElement('Hidden', 'longitude', array(
            'order' => $order++,
        ));     
        
        $this->addElement('Hidden', 'formatted_address', array(
            'order' => $order++,
        ));
        
        $this->addElement('Hidden', 'address', array(
            'order' => $order++,
        ));        
        
        $this->addElement('Hidden', 'city', array(
            'order' => $order++,
        ));   
        
        $this->addElement('Hidden', 'state', array(
            'order' => $order++,
        ));           
        
        $this->addElement('Hidden', 'country', array(
            'order' => $order++,
        ));               
        
        $this->addElement('Hidden', 'zipcode', array(
            'order' => $order++,
        ));               


    $this->addElement('Button', 'submit', array(
        'label' => 'Add',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $buttons[] = 'cancel';


    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    }

}