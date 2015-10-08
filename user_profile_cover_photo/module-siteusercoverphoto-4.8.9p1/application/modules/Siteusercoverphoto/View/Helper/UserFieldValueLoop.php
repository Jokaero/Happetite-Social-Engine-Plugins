<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserFieldValueLoop.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_View_Helper_UserFieldValueLoop extends Fields_View_Helper_FieldAbstract
{
  public function userFieldValueLoop($subject, $partialStructure, $customFields)
  {
    if( empty($partialStructure) ) {
      return '';
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() ) {
      return '';
    }
    
    // Calculate viewer-subject relationship
    $usePrivacy = ($subject instanceof User_Model_User);
    if( $usePrivacy ) {
      $relationship = 'everyone';
      if( $viewer && $viewer->getIdentity() ) {
        if( $viewer->getIdentity() == $subject->getIdentity() ) {
          $relationship = 'self';
        } else if( $viewer->membership()->isMember($subject, true) ) {
          $relationship = 'friends';
        } else {
          $relationship = 'registered';
        }
      }
    }

    // Generate
    $content = '';
    $lastContents = '';
    $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");
    $show_hidden = $viewer->getIdentity()
                 ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type)
                 : false;
    $count = 0;
    foreach( $partialStructure as $map ) {

       if($count == $customFields)
         break;

      // Get field meta object
      $field = $map->getChild();
      $value = $field->getValue($subject);
      if( !$field || $field->type == 'profile_type' ) continue;
    
      if(!$field->cover) continue;

      if( !$field->display && !$show_hidden) continue;

      $isHidden = !$field->display;
      
      // Get first value object for reference
      $firstValue = $value;
      if( is_array($value) && isset($value[0])) {
        $firstValue = $value[0];
      }
      
      // Evaluate privacy
      if( $usePrivacy && !empty($firstValue->privacy) && $relationship != 'self' ) {
        if( $firstValue->privacy == 'self' && $relationship != 'self' ) {
          $isHidden = true; //continue;
        } else if( $firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self') ) {
          $isHidden = true; //continue;
        } else if( $firstValue->privacy == 'registered' && $relationship == 'everyone' ) {
          $isHidden = true; //continue;
        }
      }
      
      // Render
      if( $field->type == 'heading' ) {
        // Heading
        if( !empty($lastContents) ) {
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = $this->view->translate($field->label);
      } else {
        // Normal fields
        $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);
        if( !empty($firstValue->value) && !empty($tmp) ) {

          $notice = $isHidden && $show_hidden
                  ? sprintf('<div class="tip"><span>%s</span></div>',
                      $this->view->translate('This field is hidden and only visible to you and admins:'))
                  : '';
          if( !$isHidden || $show_hidden ) {
            $label = $this->view->translate($field->label);
            $lastContents .= <<<EOF
  <li data-field-id={$field->field_id}>
    {$notice}
    <span>{$label}:</span>
    <span>
      {$tmp}
    </span>
  </li>
EOF;
$count++;
          }
        }
      }
    }
    if( !empty($lastContents) ) {
      $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
    }

    return $content;
  }

  public function getFieldValueString($field, $value, $subject, $map = null,
      $partialStructure = null)
  {
    if( (!is_object($value) || !isset($value->value)) && !is_array($value) ) {
      return null;
    }
    
    // @todo This is not good practice:
    // if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
    if( !$helperName ) {
      return null;
    }

    $helper = $this->view->getHelper($helperName);


    if( !$helper ) {
      return null;
    }

    $helper->structure = $partialStructure;
    $helper->map = $map;
    $helper->field = $field;
    $helper->subject = $subject;

    if($field->type == 'birthdate') {
      if(!$value->value) {
        $tmp = $this->view->userFieldBirthdate($subject, $field, $value);
      } else {
        $tmp = $helper->$helperName($subject, $field, $value);
      }
    }else {
      $tmp = $helper->$helperName($subject, $field, $value);
    }
    unset($helper->structure);
    unset($helper->map);
    unset($helper->field);
    unset($helper->subject);
    
    return $tmp;
  }

  protected function _buildLastContents($content, $title)
  {

    if( !$title ) {
      return '<div class="siteuser_cover_profile_fields"><ul>' . $content . '</ul></div>';
    }
    return <<<EOF
          <div class="siteuser_cover_profile_fields">
          <h4>
            <span>{$title}</span>
          </h4>
          <ul>
            {$content}
          </ul></div>
EOF;
  }

}