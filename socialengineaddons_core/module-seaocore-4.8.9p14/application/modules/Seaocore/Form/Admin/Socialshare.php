<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Socialshare extends Engine_Form {
    
  protected $_field;

  public function getField() {
    return $this->_field;
  }

  public function setField($field) {
    $this->_field = $field;
    return $this;
  }    

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $fieldName = $this->getField();
    $tableFieldValue = str_replace('_', ".", $fieldName);

    $social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_preferred_5"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
		</div>
		<script type="text/javascript">
		var addthis_config = {
							services_compact: "facebook, twitter, linkedin, google, digg, more",
							services_exclude: "print, email"
		}
		</script>
		<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';

    $this->addElement('Textarea', "$fieldName", array(
        'value' => $settings->getSetting("$tableFieldValue", $social_share_default_code),
    ));
    $this->$fieldName->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),        
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => "javascript:parent.Smoothbox.close();",
        'href' => "javascript:void(0);",
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $this->getDisplayGroup('buttons');    
    
  }

}
