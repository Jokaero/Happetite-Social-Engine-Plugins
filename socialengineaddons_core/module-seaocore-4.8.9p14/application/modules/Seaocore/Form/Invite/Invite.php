<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Invite.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Invite_Invite extends Engine_Form
{
  public function init() 
  {
    $this->setTitle('Invite Friend')
      ->setDescription('Would you like to invite this member again?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction($_SERVER['REQUEST_URI'])
      ;

    //$this->addElement('Hash', 'token');

    $this->addElement('Button', 'submit', array(
      'label' => 'Invite Friend',      
      'ignore' => true,
      'onclick' => 'resendInvite();',
      'decorators' => array('ViewHelper')
    ));

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
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}