<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobileContent.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Form_Admin_Widget_MobileContent extends Engine_Form {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $modulesItems = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getMobileModules();

    $this->addElement('Select', 'modulename', array(
        'label' => 'Module Name',
        'onchange' => 'javascript:fetchModuleName(this.value, 1);',
        'multiOptions' => $modulesItems
    ));

    foreach ($modulesItems as $key => $value) {
      $this->addElement('MultiCheckbox', "showContent_$key", array(
          'label' => $view->translate('Select the information options that you want to be available in this block.'),
          'multiOptions' => Engine_Api::_()->sitecontentcoverphoto()->showMobileContentOptions($key)
      ));
    }

    $this->addElement('Radio', 'showMemberLevelBasedPhoto', array(
       // 'label' => $view->translate('Tab Placement'),
        'label' => "Select the default cover photo type you want to show in this widget.",
        'multiOptions' => array(
            '1' => 'Default Cover Photo Based On Member Level (Admin will be able to upload the cover photo from the Member Level Settings of Content Cover Photo Plugin.)',
            '0' => 'Profile Cover Photo of Content Owner.',
        ),
        'value' => 1,
    ));

//     $this->addElement('Radio', 'siteusercoverphotoStrachMainPhoto', array(
//         'label' => $view->translate('Consistent Profile Picture Blocks'),
//         'description' => "Do you want profile pictures to be displayed in consistent blocks of fixed dimension below the cover photo on your site?",
//         'multiOptions' => array(
//             '1' => 'Yes (Though the dimensions of the profile picture block will be consistent, and the photos with unequal dimension will be shown in the center of the block.)',
//             '0' => 'No (The dimension of the profile picture block will not be fixed. In this case block’s dimensions will depend on the dimensions of profile picture.)',
//         ),
//         'value' => 1,
//     ));
  }

}
?>

<script type="text/javascript">
<?php $modulesItems = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getMobileModules(); ?>

  window.addEvent('domready', function() {
<?php foreach ($modulesItems as $key => $value): ?>
      fetchModuleName($('modulename').value, 0);
<?php endforeach; ?>
  });


  function fetchModuleName(value, load) {
		
<?php foreach ($modulesItems as $key => $value): ?>
      $('showContent_'+'<?php echo $key; ?>'+'-wrapper').style.display = 'none';
<?php endforeach; ?>
    if(value == 0 && load == 0) {
      $('showContent_'+value+'-wrapper').style.display = 'none';
    } else if(value == 0 && load ==1) {
      $('showContent_'+value+'-wrapper').style.display = 'none';
    } else if(value != '' && load == 0) {
      $('showContent_'+value+'-wrapper').style.display = 'block';
    } else if(value != '' && load == 1) {
      $('showContent_'+value+'-wrapper').style.display = 'block';
    }
  }

</script>