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
	$this->headTranslate(array('Cancel Friend Request', 'Add Friend', 'Remove Friend', 'Cancel Follow Request', 'Follow', 'Unfollow', 'Approve Follow Request', 'Unfollow', 'Approve Friend Request'));
?>

<?php
$id = null;
$this->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('user_settings', ( $id ? array('params' => array('id' => $id)) : array()));
$strach_main_photo = Engine_Api::_()->getApi("settings", "core")->getSetting('siteusercoverphoto.strach.main.photo', 1);
?>

<?php
$this->headScriptSM()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteusercoverphoto/externals/scripts/friends_mobile.js');
?>

<?php $minHeight = 60; ?>
<?php $user_level_id = $this->user->level_id;?>
<?php $randId=rand(10,999999999);?>
<div class="seaocore_profile_cover_wrapper">
  <?php if($this->can_edit):?>
		<a href="#icons_options_cover_photo<?php echo $randId?>" data-rel="popup" data-transition="pop">
   <?php endif;?>
	 <div class='seaocore_profile_cover_photo_wrapper' id="siteuser_cover_photo" style='min-height:<?php echo $minHeight; ?>px;'>
			<?php if (Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $this->user, 'upload') && $this->photo) : ?>
				<div class="seaocore_profile_cover_photo">
				<?php if (empty($this->can_edit)): ?>
						<a data-linktype='photo-gallery' href="<?php echo $this->photo->getHref(); ?>">
					<?php endif; ?>
						<?php echo $this->itemPhoto($this->photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo')); ?>
					<?php if (empty($this->can_edit)) : ?>
						</a>
				<?php endif; ?>
					</div>
			<?php elseif(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id")):?>
				<div class="seaocore_profile_cover_photo">
					<img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id"), 'thumb.cover')->map()?>" align="left" class="cover_photo" style="top:<?php echo $this->coverTop?>px" />
				</div>
			<?php else: ?>
				<div class="seaocore_profile_cover_photo_empty ui-bar-c"></div>
			<?php endif; ?>
			<?php if($this->can_edit):?>  
				<div class="seaocore_profile_cover_upload_op"><span></span></div>
			<?php endif;?>
	 </div>
	<?php if($this->can_edit):?>
		</a>
  <?php endif;?>
  <div class="seaocore_profile_cover_head_section" id="siteuser_main_photo">
    <?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent)):?>
			<div class="seaocore_profile_cover_head">
        <?php if (in_array('mainPhoto', $this->showContent)):?>
					<div class="seaocore_profile_main_photo_wrapper">
						<div class='seaocore_profile_main_photo'>
              <div class="item_photo <?php if($strach_main_photo):?> show_photo_box <?php endif; ?>">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr valign="middle">
										<td>
											<?php 			
												$href = Engine_Api::_()->seaocore()->getUserPhotoHref($this->user);
											?>
											<?php if (empty($this->can_edit) && $href) : ?>
												<a href="<?php echo $href; ?>" class="thumbs_photo" data-linktype="photo-gallery">
											<?php endif; ?>
                     <?php if ($this->can_edit) : ?> 
												<?php echo $this->htmlLink($this->url(array("module"=>"user","controller"=>"edit","action"=>"photo", 'id' => $this->user->user_id),'user_extended', true), $this->itemPhoto($this->user, 'thumb.profile', '', array('align' => 'left', 'id' => 'user_profile_photo'))) ?>
                     <?php else: ?>
												<?php echo $this->itemPhoto($this->user, 'thumb.profile', '', array('align' => 'left', 'id' => 'user_profile_photo')); ?>
											<?php endif;?>
											<?php if (empty($this->can_edit) && $href) : ?></a><?php endif; ?>
										</td>
									</tr>
								</table>
              </div>
						</div>
					</div>	
        <?php endif;?>
        <?php if (in_array('title', $this->showContent)):?>
					<div class="seaocore_profile_cover_title">
						<a href="<?php echo $this->user->getHref(); ?>"><h2><?php echo $this->user->getTitle(); ?></h2></a>
					</div>
        <?php endif;?>
			</div>
    <?php endif;?>

    <?php if ((in_array('friendShipButton', $this->showContent) || in_array('composeMessageButton', $this->showContent) && (($this->user->getType() == 'user') && ($this->user->authorization()->isAllowed(null, 'view')))) && !$this->viewer()->isSelf($this->user)):?>
			<div class="seaocore_profile_cover_buttons">
				<table cellpadding="2" cellspacing="0">
					<tr>
						<?php if ((in_array('friendShipButton', $this->showContent) && ($this->UserFriendshipAjaxSM($this->user)))): ?>
							<td id="friendship_user">
								<?php echo $this->UserFriendshipAjaxSM($this->user) ?>
							</td>
						<?php endif; ?>
						<?php if ((in_array('composeMessageButton', $this->showContent) && $this->MessageSM($this->user))): ?>   
							<td>
								<?php echo $this->MessageSM($this->user) ?>
							</td>
						<?php endif; ?>
					</tr>
				</table>  
			</div>
     <?php endif; ?>

     <?php if ((in_array('customFields', $this->showContent) && $this->user->getType() == 'user') && ($this->user->authorization()->isAllowed(null, 'view'))): ?>
			<div class="ui-page-content">
				<?php echo $this->userFieldValueLoop($this->user, $this->fieldStructure, $this->customFields) ?>
			</div>
		 <?php endif; ?>

    <?php if (($this->user->getType() == 'user') && (!$this->user->authorization()->isAllowed(null, 'view'))): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('This profile is private - only friends of this member may view it.'); ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
</div>

<div data-role="popup" id="icons_options_cover_photo<?php echo $randId?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15" data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window" >
	<div data-inset="true" style="min-width:150px;" class="sm-options-popup change-cover-photo-options">
    <?php if ($this->photo): ?>
      <a href="<?php echo $this->photo->getHref();?>" data-linktype='photo-gallery' class="ui-btn-default ui-btn-corner-all"><?php echo $this->translate("View Photo");?></a>
    <?php endif;?>
      <form id="upload_cover_photo_mobile<?php echo $randId?>" class="upload_cover_photo_mobile ui-btn-default" enctype="multipart/form-data" method="post" action="<?php echo $this->url(array('action' => 'upload-cover-photo', 'user_id' => $this->user->user_id, 'special' => 'cover', 'cover_photo_preview' => $this->cover_photo_preview), 'siteusercoverphoto_profilepagemobile', true); ?>">
      <label for="Filedata" data-mini="true" class="ui-btn-default"><?php echo $this->translate("Upload Cover Photo");?></label>
			<input id="MAX_FILE_SIZE" type="hidden" value="1073741824" name="MAX_FILE_SIZE">
			<input id="Filedata" type="file" onchange="uploadCoverPhoto('<?php echo $randId?>');" name="Filedata">
      </form>
	<?php if (Engine_Api::_()->sitemobile()->isApp()) : 
	 echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', 'user_id' => $this->user->user_id, 'recent' => 1, 'format' => 'smoothbox'), 'siteusercoverphoto_profilepagemobile', true), $this->translate('Choose from Album Photos'), array(' class' => 'ui-btn-default ui-btn-corner-all')); 
        else:
             echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', 'user_id' => $this->user->user_id, 'recent' => 1), 'siteusercoverphoto_profilepagemobile', true), $this->translate('Choose from Album Photos'), array(' class' => 'ui-btn-default ui-btn-corner-all')); 
        endif;
?>
		<?php if (!empty($this->user->user_cover) && $this->photo) : ?>
			<?php echo $this->htmlLink(array('route' => 'siteusercoverphoto_profilepagemobile', 'action' => 'remove-cover-photo', 'user_id' => $this->user->user_id), $this->translate('Remove Cover Photo'), array(' class' => 'smoothbox ui-btn-corner-all ui-btn-default')); ?>
		<?php endif; ?>
      <?php if (!Engine_Api::_()->sitemobile()->isApp()) : ?>
		<a href="#" data-rel="back" class="ui-btn-default ui-btn-corner-all">
			<?php echo $this->translate('Cancel'); ?>
		</a>
      <?php endif; ?>
	</div>
</div>

<script type="text/javascript">
  
  function uploadCoverPhoto(id) {
//    $('#upload_cover_photo_mobile'+id).attr("action", "<?php //echo $this->url(array('action' => 'upload-cover-photo', 'user_id' => $this->user->user_id, 'special' => 'cover', 'cover_photo_preview' => $this->cover_photo_preview), 'siteusercoverphoto_profilepagemobile', true); ?>");
    $('#upload_cover_photo_mobile'+id).submit();
  }

 	sm4.core.runonce.add(function() {
    setTimeout(function(){
        var imageMaxHeight =$('body').width();
        if(imageMaxHeight > 500){
          imageMaxHeight = 600;
        }
       var imageHeight= $.mobile.activePage.find('.seaocore_profile_cover_photo_wrapper').find('.seaocore_profile_cover_photo').find('.cover_photo').height();
       if(imageHeight > imageMaxHeight){
     $.mobile.activePage.find('.seaocore_profile_cover_photo_wrapper').find('.seaocore_profile_cover_photo').css('max-height', imageMaxHeight);
        $.mobile.activePage.find('.seaocore_profile_cover_photo_wrapper').find('.seaocore_profile_cover_photo').find('.cover_photo').css('top',-(imageHeight -imageMaxHeight)/2);
       }
    },100);

 	});

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#upload_cover_photo_mobile').css('display', 'none');
    } else {
      $.mobile.activePage.find('#upload_cover_photo_mobile').css('display', 'block');
    } 
  });

</script>