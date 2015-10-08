<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Form_Admin_Widget_Content extends Engine_Form {

    public function init() {
        $this
                ->setAttrib('id', 'form-upload');
        $inside_info = 'Inside the "Content Cover Photo and Information" widget.';
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $outside_info = 'Outside the "Content Cover Photo and Information" widget.';
        $modulesItems = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getModules();
        //CHECK IF FACEBOOK PLUGIN IS ENABLE
        $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');

        if (!empty($fbmodule) && !empty($fbmodule->enabled)) {
            $show_like_button = array(
                '1' => $view->translate('Yes, show SocialEngine Core Like button'),
                '2' => $view->translate('Yes, show Facebook Like button'),
                '0' => $view->translate('No'),
            );
        } else {
            $show_like_button = array(
                '1' => $view->translate('Yes, show SocialEngine Core Like button'),
                '0' => $view->translate('No'),
            );
        }

        $this->addElement('Select', 'modulename', array(
            'label' => 'Module Name',
            'onchange' => 'javascript:fetchModuleName(this.value, 1);',
            'multiOptions' => $modulesItems
        ));

        foreach ($modulesItems as $key => $value) {
            $this->addElement('MultiCheckbox', "showContent_$key", array(
                'label' => $view->translate('Select the information options that you want to be available in this block.'),
                'multiOptions' => Engine_Api::_()->sitecontentcoverphoto()->showContentOptions($key)
            ));
        }

        $this->addElement('Radio', 'profile_like_button', array(
            'label' => $view->translate('Do you want to enable Like button in this block?'),
            'multiOptions' => $show_like_button,
            'value' => 1,
        ));

        $this->addElement('Text', 'columnHeight', array(
            'label' => $view->translate('Enter the cover photo height (in px). (Minimum 150 px required.)'),
            'value' => 300,
        ));

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $this->addElement('Radio', 'showMember', array(
                'label' => $view->translate('Do you want to show members in this block?'),
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => 1,
            ));

            $this->addElement('Select', 'memberCount', array(
                'label' => $view->translate('Select members to be displayed in a row.'),
                'multiOptions' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'),
                'value' => '8',
            ));

            $this->addElement('Radio', 'onlyMemberWithPhoto', array(
                'label' => $view->translate('Do you want to show only those members who have uploaded their profile pictures?'),
                'multiOptions' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'),
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => 1,
            ));
        }

        $this->addElement('Radio', 'contentFullWidth', array(
            'label' => "Display Cover Photo in full width Yes / No?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => '0',
            'onclick' => 'covercontentFullWidth();'
        ));


        $this->addElement('Radio', 'sitecontentcoverphotoChangeTabPosition', array(
            // 'label' => $view->translate('Tab Placement'),
            'label' => "Select the position of the tabs to be placed on Content Profile page. (Note: This setting will only work for the widget placed on Content Profile page.)",
            'multiOptions' => array(
                '1' => $inside_info,
                '0' => $outside_info,
            ),
            'value' => 0,
        ));

        $this->addElement('MultiCheckbox', 'contacts', array(
            'label' => $view->translate('Select the contact details you want to display.'),
            'multiOptions' => array("1" => "Phone", "2" => "Email", "3" => "Website"),
                //'value' => array("0" => "1", "1" => "2", "2" => "3")
        ));

        $this->addElement('Radio', 'showMemberLevelBasedPhoto', array(
            // 'label' => $view->translate('Tab Placement'),
            'label' => "Select the default cover photo type you want to show in this widget.",
            'multiOptions' => array(
                '1' => 'Default Cover Photo Based On Member Level (Admin will be able to upload the cover photo from the Member Level Settings of Content Cover Photo Plugin.)',
                '0' => 'Profile Cover Photo of Content Owner.',
            ),
            'value' => 1,
        ));

        $this->addElement('Radio', 'emailme', array(
            'label' => "Do you want users to send emails to Pages via a customized pop up when they click on 'Email Me' link?",
            'multiOptions' => array(
                1 => 'Yes, open customized pop up',
                0 => 'No, open browser`s default pop up'
            ),
            'value' => '1'
        ));

        $this->addElement('Radio', 'editFontColor', array(
            'label' => "Do you want 'Edit Font Color' option to be available to the members for their content cover photo?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => '0'
        ));
    }

}
?>

<script type="text/javascript">
<?php $modulesItems = Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->getModules(); ?>
    var form = document.getElementById("form-upload");
    window.addEvent('domready', function () {
<?php foreach ($modulesItems as $key => $value): ?>
            fetchModuleName($('modulename').value, 0);
<?php endforeach; ?>
        covercontentFullWidth();
    });

    function covercontentFullWidth() {

        if (form.elements["contentFullWidth"].value == 0) {
            $('sitecontentcoverphotoChangeTabPosition-wrapper').style.display = 'block';
        } else {
            $('sitecontentcoverphotoChangeTabPosition-wrapper').style.display = 'none';
        }
    }

    function fetchModuleName(value, load) {
        if ($('memberCount-wrapper'))
            $('memberCount-wrapper').style.display = 'none';
        if ($('onlyMemberWithPhoto-wrapper'))
            $('onlyMemberWithPhoto-wrapper').style.display = 'none';
        if ($('showMember-wrapper'))
            $('showMember-wrapper').style.display = 'none';
<?php foreach ($modulesItems as $key => $value): ?>
            $('showContent_' + '<?php echo $key; ?>' + '-wrapper').style.display = 'none';
<?php endforeach; ?>
        if (value == 0 && load == 0) {
            $('showContent_' + value + '-wrapper').style.display = 'none';
            $('emailme-wrapper').style.display = 'none';
            $('contacts-wrapper').style.display = 'none';
        } else if (value == 0 && load == 1) {
            $('showContent_' + value + '-wrapper').style.display = 'none';
            $('emailme-wrapper').style.display = 'none';
            $('contacts-wrapper').style.display = 'none';
        } else if (value != '' && load == 0) {
            $('emailme-wrapper').style.display = 'none';

            if (value == 'album')
                $('contacts-wrapper').style.display = 'none';
            else
                $('contacts-wrapper').style.display = 'block';
            if (value == 'sitepage_page' || value == 'sitebusiness_business' || value == 'sitegroup_group') {
                if ($('memberCount-wrapper'))
                    $('memberCount-wrapper').style.display = 'block';
                if ($('onlyMemberWithPhoto-wrapper'))
                    $('onlyMemberWithPhoto-wrapper').style.display = 'block';
                if ($('showMember-wrapper'))
                    $('showMember-wrapper').style.display = 'block';
                $('emailme-wrapper').style.display = 'block';
            }
            $('showContent_' + value + '-wrapper').style.display = 'block';
        } else if (value != '' && load == 1) {
            if (value == 'album')
                $('contacts-wrapper').style.display = 'none';
            else
                $('contacts-wrapper').style.display = 'block';
            $('emailme-wrapper').style.display = 'none';
            if (value == 'sitepage_page' || value == 'sitebusiness_business' || value == 'sitegroup_group') {
                if ($('memberCount-wrapper'))
                    $('memberCount-wrapper').style.display = 'block';
                if ($('onlyMemberWithPhoto-wrapper'))
                    $('onlyMemberWithPhoto-wrapper').style.display = 'block';
                if ($('showMember-wrapper'))
                    $('showMember-wrapper').style.display = 'block';
                $('emailme-wrapper').style.display = 'block';
            }
            $('showContent_' + value + '-wrapper').style.display = 'block';
        }
    }

</script>