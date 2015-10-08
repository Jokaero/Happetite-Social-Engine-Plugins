<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Settings extends Engine_Form {

    public function init() {

        $this->setTitle('General Settings')
                ->setDescription('These settings affect all members in your community.');

        $this->addElement('Radio', 'seaocore_advancedcalendar', array(
            'label' => 'Advanced Calendar',
            'description' => 'Do you want to use "Advanced Calendar" on your site ? (If set to yes, an Advanced Calendar will replace default socialengine core calendar at all the places on your site.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.advancedcalendar', 1),
        ));

        $this->addElement('Radio', 'seaocore_calendar_daystart', array(
            'label' => 'Calendar Format',
            'description' => "Select a format for the calendar.",
            'multiOptions' => array(
                1 => 'First day of week as Sunday, and last as Saturday',
                2 => 'First day of week as Monday, and last as Sunday',
                3 => 'First day of week as Saturday, and last as Friday'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.calendar.daystart', 1),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'order' => 500,
        ));
    }

}
