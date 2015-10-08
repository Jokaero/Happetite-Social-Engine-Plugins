<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ((empty($this->showContent) || !in_array('mainPhoto', $this->showContent))): ?>
    <style type="text/css">
        div.tabs_alt,
        .seaocore_profile_cover_head_section_inner{
            margin-left: 0 !important;
        }
        [dir="rtl"] div.tabs_alt,
        [dir="rtl"] .seaocore_profile_cover_head_section_inner{
            margin-right: 0 !important;
            margin-left: auto !important;
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
$tablePrimaryFieldName = $this->tablePrimaryFieldName;

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecontentcoverphoto/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/follow.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')): ?>

    <?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');
    ?>
    <script>
        var call_advfbjs = 1;
    </script>
<?php endif ?>
<?php if ($this->sitecontentcoverphotoChangeTabPosition): ?>
    <?php
    $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_tabs.css');
    ?>
<?php endif ?>


<?php $minHeight = 200; ?>
<?php $level_id = $this->level_id; ?>
<?php if (!$this->cover_photo_preview && $this->memberCount && $this->membersCount && $this->showMember && !Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $this->moduleName, $level_id, 0) && !$this->photo && $this->showMemberLevelBasedPhoto): ?>
     <?php $this->columnHeight = 120;
    $minHeight = 120;
    ?>
<?php endif; ?>
    
<?php if(!$this->showMemberLevelBasedPhoto):?>
    <?php 
	$user = Engine_Api::_()->getItem('user', $this->subject()->getOwner()->getIdentity());
  $photo='';
	if (Engine_Api::_()->hasModuleBootstrap('advalbum') && isset($user->user_cover) && $user->user_cover) {
		$photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
	} elseif(isset($user->user_cover) && $user->user_cover) {
		$photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
	}
?> 
 <?php
$minHeight = $this->columnHeight;
?>
<?php endif;?>
    
<?php if (!empty($this->cover_photo_preview)): ?>
    <?php $href = $this->subject()->getHref(); ?>
    <div class="tip">
        <span> 
            <?php echo $this->translate('Here, you can upload and set default cover photo for this module. (Note: This photo will be displayed on Content Profile pages of this module as configured by you from the Layout Editor, until members upload a cover photo.). Please %s to go to the Content Profile page of this module.', "<a href='$href'>click here</a>"); ?>
        </span>
    </div><br />
<?php endif; ?>


<?php $photo_preview_id = Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $this->moduleName, $level_id, 0); ?>
<div class="seaocore_profile_cover_wrapper <?php if (!$this->checkTabContainerExists): ?>seaocore_notabs<?php endif; ?>">

    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting($this->moduleName . '.fs.markers', 1)): ?>
        <div class="prelative">
            <?php if (is_array($this->showContent) && in_array('newlabel', $this->showContent) && isset($this->subject()->newlabel) && $this->subject()->newlabel): ?>
                <i class="seaocore_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
            <?php endif; ?>	
            <?php if (is_array($this->showContent) && in_array('featured', $this->showContent) && $this->subject()->featured): ?>
                <span title="<?php echo $this->translate('Featured') ?>" class="seaocore_list_featured_label"></span>
            <?php endif; ?>	
            <?php if (is_array($this->showContent) && in_array('sponsored', $this->showContent) && $this->subject()->sponsored): ?>
                <div class="seaocore_coverphoto_profile_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting($this->moduleName . '.sponsored.color', '#fc0505'); ?>;'>
                    <?php echo $this->translate('SPONSORED'); ?>     				
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    
    <div class="seaocore_profile_cover_photo_wrapper <?php if ($this->sitecontentcoverphotoChangeTabPosition): ?>seaocore_profile_cover_has_tabs<?php endif; ?> <?php if($this->contentFullWidth):?><?php echo 'seaocore_profile_fullwidth_cover_photo';?><?php endif;?>" id="sitecontent_cover_photo" style='min-height:<?php echo $minHeight; ?>px; height:<?php echo (!empty($this->fieldName) || !empty($this->can_edit) || $photo_preview_id) ? $this->columnHeight : $minHeight; ?>px;'>
    </div>
    <?php if (!empty($this->showContent) || $this->sitecontentcoverphotoChangeTabPosition): ?>
        <div class="seaocore_profile_cover_head_section b_medium <?php if ($this->sitecontentcoverphotoChangeTabPosition): ?>seaocore_profile_cover_has_tabs<?php endif; ?> <?php if ($this->profile_like_button == 2) : ?>seaocore_profile_cover_has_fblike<?php endif; ?> " id="sitecontent_main_photo"></div>
    <?php endif; ?>
</div>

<div class="clr"></div>
<script type="text/javascript">
    var noProfilePhoto = '<?php echo $this->noProfilePhoto; ?>';
    document.sitecontentCoverPhoto = new Sitecontentcoverphoto({
        block: $('sitecontent_cover_photo'),
        photoUrl: '<?php echo $this->url(array('action' => 'get-cover-photo', $tablePrimaryFieldName => $this->subject()->getIdentity(), 'special' => 'cover', 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id, 'moduleName' => $this->moduleName, "fieldName" => $this->fieldName), 'sitecontentcoverphoto_profilepage', true); ?>',
        buttons: 'sitecontentcoverphoto_cover_options',
        positionUrl: '<?php echo $this->url(array('action' => 'reset-position-cover-photo', $tablePrimaryFieldName => $this->subject()->getIdentity(), 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id, 'moduleName' => $this->moduleName, "fieldName" => $this->fieldName, 'subject' => $this->subject()->getGuid()), 'sitecontentcoverphoto_profilepage', true); ?>',
        position:<?php echo json_encode(array('top' => 0, 'left' => 0)); ?>,
        cover_photo_preview: '<?php echo $this->cover_photo_preview; ?>',
        showMember: '<?php echo $this->showMember; ?>',
        memberCount: '<?php echo $this->memberCount; ?>',
        onlyMemberWithPhoto: '<?php echo $this->onlyMemberWithPhoto; ?>',
        sitecontentcoverphotoChangeTabPosition: '<?php echo $this->sitecontentcoverphotoChangeTabPosition; ?>',
        editFontColor: '<?php echo $this->editFontColor; ?>',
        showMemberLevelBasedPhoto: '<?php echo $this->showMemberLevelBasedPhoto; ?>',
        contentFullWidth: '<?php echo $this->contentFullWidth; ?>'
    });

    document.sitecontentMainPhoto = new Sitecontentmainphoto({
        block: $('sitecontent_main_photo'),
        photoUrl: '<?php echo $this->url(array('action' => 'get-main-photo', $tablePrimaryFieldName => $this->subject()->getIdentity(), 'special' => 'profile', 'profile_like_button' => $this->profile_like_button, 'cover_photo_preview' => $this->cover_photo_preview, 'moduleName' => $this->moduleName, 'level_id' => $this->level_id, "fieldName" => $this->fieldName, 'subject' => $this->subject()->getGuid()), 'sitecontentcoverphoto_profilepage', true); ?>',
        buttons: 'sitecontentcoverphoto_main_options',
        positionUrl: '<?php echo $this->url(array('action' => 'reset-position-cover-photo', $tablePrimaryFieldName => $this->subject()->getIdentity(), 'moduleName' => $this->moduleName, 'cover_photo_preview' => $this->cover_photo_preview, "fieldName" => $this->fieldName), 'sitecontentcoverphoto_profilepage', true); ?>',
        position:<?php echo json_encode(array('top' => 0, 'left' => 0)); ?>,
        showContent:<?php echo json_encode($this->showContent) ?>,
        sitecontentcoverphotoStrachMainPhoto:<?php echo $this->sitecontentcoverphotoStrachMainPhoto ?>,
        occurrence_id: <?php echo Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : 0; ?>,
        emailme: '<?php echo $this->emailme; ?>',
        show_phone: '<?php echo $this->show_phone; ?>',
        show_email: '<?php echo $this->show_email; ?>',
        show_website: '<?php echo $this->show_website; ?>',
        sitecontentcoverphotoChangeTabPosition: '<?php echo $this->sitecontentcoverphotoChangeTabPosition; ?>',
        editFontColor: '<?php echo $this->editFontColor; ?>',
        showMemberLevelBasedPhoto: '<?php echo $this->showMemberLevelBasedPhoto; ?>',
        contentFullWidth: '<?php echo $this->contentFullWidth; ?>',
    });

    function showSmoothBox(url) {
        Smoothbox.open(url);
    }

<?php if ($this->sitecontentcoverphotoChangeTabPosition): ?>
        en4.core.runonce.add(function () {
            if ($('global_content').getElement('div.layout_core_container_tabs')) {
                if ($('global_content').getElement('div.layout_core_container_tabs').hasClass('generic_layout_container layout_core_container_tabs')) {
                    if (noProfilePhoto == 1) {
                        $('global_content').getElement('div.layout_core_container_tabs').removeClass('generic_layout_container layout_core_container_tabs').addClass('generic_layout_container layout_core_container_tabs seaocore_profile_cover_has_tabs seaocore_profile_cover_no_profile_photo');
                    } else {
                        $('global_content').getElement('div.layout_core_container_tabs').removeClass('generic_layout_container layout_core_container_tabs').addClass('generic_layout_container layout_core_container_tabs seaocore_profile_cover_has_tabs');
                    }
                }
            }
        });
<?php endif; ?>

    <?php if($this->contentFullWidth):?>
        if ($$('.layout_sitecontentcoverphoto_content_cover_photo').length > 0) {
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
    .seaocore_profile_fullwidth_cover_photo .cover_photo_wap, 
		.seaocore_profile_fullwidth_cover_photo .seaocore_profile_cover_head_section_inner{
        height: <?php echo $this->columnHeight?>px;
    }
    /*.seaocore_profile_fullwidth_cover_photo .seaocore_profile_cover_gradient {
        bottom: 40px;
    }*/
		#sitecontent_cover_photo #sitecontentcover_middle_content {
			 height: <?php echo $this->columnHeight?>px;
    	 margin-top: -<?php echo $this->columnHeight?>px;
		}
</style>
