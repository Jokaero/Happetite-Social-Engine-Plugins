<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Usercoverfield.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Controller_Action_Helper_Usercoverfield extends Zend_Controller_Action_Helper_Abstract
{

	function postDispatch()
	{
		//GET NAME OF MODULE, CONTROLLER AND ACTION
		$front = Zend_Controller_Front::getInstance();
		$module = $front->getRequest()->getModuleName();
		$controller = $front->getRequest()->getControllerName();
		$action = $front->getRequest()->getActionName();
		$view = $this->getActionController()->view;

		//ADD USER PRIVACY FIELDS AT FIELD CREATION AND EDITION 
		if(($module == 'user') && ($action == 'field-create' || $action == 'heading-edit' || $action == 'field-edit') && ($controller == 'admin-fields'))
		{
			$new_element =  $view->form;
      if(!$this->getRequest()->isPost() || (isset($view->form) && (!$view->form->isValid($this->getRequest()->getPost())))) {
				$new_element->addElement('Select', 'cover', array(
					'label' => 'SHOW IN WIDGETS DISPLAYING USER PROFILE FIELDS IN FULLSITE, MOBILE AND TABLET?',
					'multiOptions' => array(
						1 => 'Show in such Widgets',
						0 => 'Hide in such Widgets'
					)
				));
        if($front->getRequest()->getParam('field_id')) {
				  $field = Engine_Api::_()->fields()->getField($front->getRequest()->getParam('field_id'), 'user'); 
          $new_element->cover->setValue($field->cover);
        }
        $new_element->buttons->setOrder(999);
     } else {
       $db = Engine_Db_Table::getDefaultAdapter();
       $db->update('engine4_user_fields_meta', array('cover' => $_POST['cover']), array('field_id = ?' => $view->field['field_id']));
     }

		} 

	}

}