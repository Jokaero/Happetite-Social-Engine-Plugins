<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Invite_Search extends Engine_Form
{
  public function init()
  {
    $this->loadDefaultDecorators();
    $this->getDecorator('HtmlTag')->setOption('class', 'invite_members_criteria');
    $this->setAttrib('id',      'search_friends');
    $this->addElement('Text', 'displayname', array(
    'label' => 'Name',
    'order' => -1000000,
    ));
    
    $email = Zend_Registry::get('Zend_Translate')->_('Email Address');
    // Init email
    $this->addElement('Text', 'email', array(
      'label' => $email,
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        'EmailAddress'
      ),      
    ));
    $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Date");
    $start->setAllowEmpty(false);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Date");
    $end->setAllowEmpty(false);
    $this->addElement($end);
    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'onclick' => 'javascript:searchMembers()',
      'ignore' => true,
    ));
  }

  
}