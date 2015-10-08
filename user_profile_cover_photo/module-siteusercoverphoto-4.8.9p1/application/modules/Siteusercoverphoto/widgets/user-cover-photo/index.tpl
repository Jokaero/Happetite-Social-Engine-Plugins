<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headTranslate(array('Cancel Friend Request', 'Add Friend', 'Remove Friend', 'Cancel Follow Request', 'Follow', 'Unfollow', 'Approve Follow Request', 'Unfollow', 'Approve Friend Request', 'Leave Event', 'Join Event', 'Request Invite'));
?>

<?php if ((empty($this->showContent) || !in_array('mainPhoto', $this->showContent))): ?>
    <style type="text/css">
        div.tabs_alt {
            margin-left: 0 ;
        }
        .seaocore_profile_cover_head_section_inner{
            margin-left: 0 !important;
        }
    </style>
<?php endif; ?>

<?php
if ($this->profile_like_button == 1) {
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
}
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteusercoverphoto/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteusercoverphoto/externals/scripts/friends.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php if ($this->change_tab_position): ?>
    <?php
    $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_tabs.css');
    ?>
<?php endif ?>

<?php $minHeight = 200; ?>

<?php if (!empty($this->cover_photo_preview)): ?>
    <?php $href = $this->viewer()->getHref(); ?>
    <div class="tip">
        <span> 
            <?php echo $this->translate('Here, you can upload and set default user cover photo for various pages on your site (Note: This photo will be displayed on various pages as configured by you from the Layout Editor, until members upload a cover photo.). Please %s to go to your profile.', "<a href='$href'>click here</a>"); ?>
        </span>
    </div><br />
<?php endif; ?>
<?php if (isset($this->user->level_id)): ?>
    <?php $level_id = $this->user->level_id; ?>
<?php else: ?>
    <?php $level_id = 0; ?>
<?php endif; ?>
<?php $photo_preview_id = Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id"); ?>
<div class="seaocore_profile_cover_wrapper">

    <?php if ($this->sitememberEnabled): ?>
        <div class="prelative">
            <?php if (is_array($this->showContent) && in_array('featured', $this->showContent) && $this->featured): ?>
                <span title="<?php echo $this->translate('Featured') ?>" class="seaocore_list_featured_label"></span>
            <?php endif; ?>	
            <?php if (is_array($this->showContent) && in_array('sponsored', $this->showContent) && $this->sponsored): ?>
                <div class="seaocore_coverphoto_profile_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;'>
                    <?php echo $this->translate('SPONSORED'); ?>
                </div>
            <?php endif; ?> 
        </div>
    <?php endif; ?>
    <div class="seaocore_profile_cover_photo_wrapper <?php if($this->contentFullWidth):?><?php echo 'seaocore_profile_fullwidth_cover_photo';?><?php endif;?> <?php if ($this->change_tab_position): ?>seaocore_profile_cover_has_tabs<?php endif; ?>" id="siteuser_cover_photo" style='min-height:<?php echo $minHeight; ?>px; height:<?php echo (!empty($this->user->user_cover) || !empty($this->can_edit) || $photo_preview_id) ? $this->columnHeight : $minHeight; ?>px;'>
    </div>
    <?php if (!empty($this->showContent) || $this->change_tab_position): ?>
        <div class="seaocore_profile_cover_head_section b_medium <?php if ($this->change_tab_position): ?>seaocore_profile_cover_has_tabs<?php endif; ?> <?php if ($this->profile_like_button == 2) : ?>seaocore_profile_cover_has_fblike<?php endif; ?> " id="siteuser_main_photo"></div>
    <?php endif; ?>
</div>

<div class="clr"></div>
<?php if (isset($this->user->user_id)) : ?>
    <?php $user_id = $this->user->user_id; ?>
<?php else : ?>
    <?php $user_id = 0; ?>
<?php endif; ?>

<script type="text/javascript">
    var noProfilePhoto = '<?php echo $this->noProfilePhoto; ?>';
    document.seaoCoverPhoto = new Siteusercoverphoto({
        block: $('siteuser_cover_photo'),
        photoUrl: '<?php echo $this->url(array('action' => 'get-cover-photo', 'user_id' => $user_id, 'special' => 'cover', 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id), 'siteusercoverphoto_profilepage', true); ?>',
        buttons: 'siteusercoverphoto_cover_options',
        positionUrl: '<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'user_id' => $user_id, 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id), 'siteusercoverphoto_profilepage', true); ?>',
        position:<?php echo Zend_Json_Encoder::encode(array('top' => 0, 'left' => 0)); ?>,
        cover_photo_preview: '<?php echo $this->cover_photo_preview; ?>',
        editFontColor: '<?php echo $this->editFontColor; ?>',
        contentFullWidth: '<?php echo $this->contentFullWidth; ?>'
    });

    document.seaoMainPhoto = new Siteusermainphoto({
        block: $('siteuser_main_photo'),
        photoUrl: '<?php echo $this->url(array('action' => 'get-main-photo', 'user_id' => $user_id, 'special' => 'profile', 'profile_like_button' => $this->profile_like_button, 'cover_photo_preview' => $this->cover_photo_preview), 'siteusercoverphoto_profilepage', true); ?>',
        buttons: 'siteusercoverphoto_main_options',
        positionUrl: '<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'user_id' => $user_id), 'siteusercoverphoto_profilepage', true); ?>',
        position:<?php echo Zend_Json_Encoder::encode(array('top' => 0, 'left' => 0)); ?>,
        showContent:<?php echo Zend_Json_Encoder::encode($this->showContent) ?>,
        editFontColor: '<?php echo $this->editFontColor; ?>',
        contentFullWidth: '<?php echo $this->contentFullWidth; ?>'
    });

    function showSmoothBox(url) {
        Smoothbox.open(url);
    }

<?php if ($this->change_tab_position): ?>
        en4.core.runonce.add(function () {
            setTimeout("setTabInsideLayout()", 500);
        });
<?php endif; ?>

    function setTabInsideLayout() {
        if (document.getElementById('global_content').getElement('div.layout_core_container_tabs')) {
            if (document.getElementById('global_content').getElement('div.layout_core_container_tabs').hasClass('generic_layout_container layout_core_container_tabs')) {
                if (noProfilePhoto == 1) {
                    $('global_content').getElement('div.layout_core_container_tabs').removeClass('generic_layout_container layout_core_container_tabs').addClass('generic_layout_container layout_core_container_tabs seaocore_profile_cover_has_tabs seaocore_profile_cover_no_profile_photo');
                } else {
                    document.getElementById('global_content').getElement('div.layout_core_container_tabs').removeClass('generic_layout_container layout_core_container_tabs').addClass('generic_layout_container layout_core_container_tabs seaocore_profile_cover_has_tabs');
                }

            }
        }
    }
    <?php if($this->contentFullWidth):?>
    if ($$('.layout_siteusercoverphoto_user_cover_photo').length > 0) {
        $('global_content').setStyles({
            'width': '100%',
            'margin-top': '-15px'
        });
    }
    <?php endif;?>  
</script>

<?php $resetcolumnHeight = $this->columnHeight - 40;?>
<style type="text/css">  
    .seaocore_profile_cover_has_tabs{
			/*display: table;*/
			width:100%;
			box-sizing: border-box;
    }   
    .seaocore_profile_cover_photo{
        height: <?php echo $this->columnHeight?>px;
    }
    .seaocore_profile_fullwidth_cover_photo .cover_photo_wap, .seaocore_profile_fullwidth_cover_photo .seaocore_profile_cover_head_section_inner{
        height: <?php echo $this->columnHeight?>px;
    }
   /* .seaocore_profile_fullwidth_cover_photo .seaocore_profile_cover_gradient {
        bottom: 40px;
    }*/
    #siteuser_cover_photo #siteusercover_middle_content {
             height: <?php echo $this->columnHeight?>px;
             margin-top: -<?php echo $this->columnHeight?>px;
    }
</style>