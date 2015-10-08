<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Login.php 9592 2012-01-11 02:23:38Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Seaocore_Form_getEmail extends Engine_Form
{
  
  public function init()
  {
    $tabindex = 1;
    
    $description = Zend_Registry::get('Zend_Translate')->_("Please enter your email address to proceed further.");
    

    // Init form
    $this->setTitle('Enter Email');
    $this->setDescription($description);
    $this->setAttrib('id', 'Seaocore_form_getemail');
    $this->setAttrib('onsubmit', 'return checkvalidEmail();');
    $this->setAttrib('method', 'get');
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

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
      
      // Fancy stuff
      'tabindex' => $tabindex++,
      'autofocus' => 'autofocus',
      'inputType' => 'email',
      'class' => 'text',
    ));
    $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

     $this->addElement('Hidden', 'type', array(
      'value' => 'linkedin',
      'order' => 700,
    ));
     $this->addElement('Hidden', 'refuser', array(
      'value' => '',
      'order' => 712,
    ));


    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      
      'ignore' => true,
      'tabindex' => $tabindex++,
    ));

  }
}
