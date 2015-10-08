<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-cover-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php

$fullSiteMode = !Engine_API::_()->seaocore()->isMobile() && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode');

?>

<script type="text/javascript">
    var fullSiteMode = '<?php echo $fullSiteMode;?>';
</script>   

<?php

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteusercoverphoto/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteusercoverphoto/externals/scripts/friends.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css');
?>
<?php $user_level_id = $this->user->level_id;?>
<?php if( empty($this->cover_photo_preview)):?>
	<?php if ( Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $this->user, 'upload') && $this->photo): ?>
		<div class="seaocore_profile_cover_photo cover_photo_wap b_dark">
			<?php if (empty($this->can_edit)) : ?>
				<a href="<?php echo $this->photo->getHref(); ?>" onclick='openSeaocoreLightBox("<?php echo $this->photo->getHref(); ?>");return false;'>
				<?php endif; ?>
				<?php echo $this->itemPhoto($this->photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo', 'style' => 'top:' . $this->coverTop . 'px')); ?>
				<?php if (empty($this->can_edit)) : ?></a><?php endif; ?>
			<?php if (!empty($this->can_edit)) : ?>
				<div class="cover_tip_wrap dnone">
					<div class="cover_tip"><?php echo $this->translate("Drag to Reposition Cover Photo") ?></div>
				</div>
			<?php endif; ?>
		</div> <!--//empty($this->user->user_cover) && !$this->photo &&--> 
		<?php elseif(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id")):?>
			<div class="seaocore_profile_cover_photo cover_photo_wap b_dark">
				<img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id"), 'thumb.cover')->map()?>" align="left" class="cover_photo" style="top:<?php echo $this->coverTop?>px" />
			</div>
	<?php else: ?>
		<div class="seaocore_profile_cover_photo_empty"></div>
	<?php endif; ?>
                
                 <?php if($this->contentFullWidth):?>
  <div id="siteusercover_middle_content"></div>
  <?php endif;?>
  
	<?php if (!empty($this->can_edit)) : ?>
		<div id="siteusercoverphoto_cover_options" class="seaocore_profile_cover_options <?php if (empty($this->user->user_cover) || empty($this->photo)) : ?> dblock <?php endif; ?>">
			<ul class="edit-button">
				<li>
					<?php if (!empty($this->user->user_cover) && $this->photo) : ?>
						<span class="seaocore_profile_cover_btn">
							<i class="seaocore_profile_cover_icon_photo_edit"><?php echo $this->translate("Edit Cover Photo"); ?></i>
						</span>
					<?php else: ?>
						<span class="seaocore_profile_cover_btn">
							<i class="seaocore_profile_cover_icon_photo_add"><?php echo $this->translate("Add Cover Photo"); ?></i>
						</span>
					<?php endif; ?>

					<ul class="seaocore_profile_options_pulldown">
						<li>
							<a href='<?php echo $this->url(array('action' => 'upload-cover-photo', 'user_id' => $this->user->user_id), 'siteusercoverphoto_profilepage', true); ?>'  class="seaocore_profile_cover_icon_photo_upload smoothbox"><?php echo $this->translate('Upload Cover Photo'); ?></a>
						</li>
						<li>
							<?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', 'user_id' => $this->user->user_id, 'recent' => 1), 'siteusercoverphoto_profilepage', true), $this->translate('Choose from Album Photos'), array(' class' => 'seaocore_profile_cover_icon_photo_view smoothbox')); ?>
						</li>
						<?php if (!empty($this->user->user_cover) && $this->photo) : ?>
            
               <?php if(!Engine_API::_()->seaocore()->isMobile() && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')):?>                                   
               <?php if($this->siteusercoverphotoChangeTabPosition && $this->editFontColor):?>
								<li>
									<?php echo $this->htmlLink(array('route' => 'siteusercoverphoto_profilepage', 'action' => 'edit-font-color','user_id' => $this->user->user_id ), $this->translate('Edit Font Color'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_color')); ?>
								</li>
              <?php endif;?><?php endif;?>
              <?php if(!Engine_API::_()->seaocore()->isMobile() && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')):?> 
							<li><a  href="javascript:document.seaoCoverPhoto.reposition.start()" class="cover_reposition seaocore_profile_cover_icon_move">
									<?php echo $this->translate("Reposition"); ?></a>
							</li><?php endif;?>
							<li>
								<?php echo $this->htmlLink(array('route' => 'siteusercoverphoto_profilepage', 'action' => 'remove-cover-photo', 'user_id' => $this->user->user_id), $this->translate('Remove Cover Photo'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_delete')); ?>
							</li>
						<?php endif; ?>
					</ul>
				</li>
			</ul>
			<?php if (!empty($this->user->user_cover)) : ?>
				<div class="save-button dnone">
					<span class="positions-save seaocore_profile_cover_btn"><i><?php echo $this->translate("Save Position"); ?></i></span>
					<span class="positions-cancel seaocore_profile_cover_btn"><i><?php echo $this->translate("Cancel"); ?></i></span>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="clr"></div>
<?php else:?>
  <?php $user_level_id = $this->level_id;?>
	<div class="seaocore_profile_cover_photo cover_photo_wap b_dark">
    <?php if(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id")):?>
			<img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id"), 'thumb.cover')->map()?>" align="left" class="cover_photo" style="top:<?php echo $this->coverTop?>px" />
			<?php //if (!empty($this->can_edit)) : ?>
				<div class="cover_tip_wrap dnone">
					<div class="cover_tip"><?php echo $this->translate("Drag to Reposition Default Cover Photo") ?></div>
				</div>
			<?php //endif; ?>
		<?php else: ?>
			<div class="seaocore_profile_cover_photo_empty"></div>
		<?php endif; ?>
	</div>
	<?php //if (!empty($this->can_edit)) : ?>
		<div id="siteusercoverphoto_cover_options" class="seaocore_profile_cover_options">
			<ul class="edit-button">
				<li>
					<?php if (Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id")) : ?>
						<span class="seaocore_profile_cover_btn">
							<i class="seaocore_profile_cover_icon_photo_edit"><?php echo $this->translate("Edit Default Cover Photo"); ?></i>
						</span>
					<?php else: ?>
						<span class="seaocore_profile_cover_btn">
							<i class="seaocore_profile_cover_icon_photo_add"><?php echo $this->translate("Add Default Cover Photo"); ?></i>
						</span>
					<?php endif; ?>
					<ul class="seaocore_profile_options_pulldown">
						<li>
							<a href='<?php echo $this->url(array('action' => 'upload-cover-photo', 'user_id' => $this->user->user_id, 'special' => 'cover', 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id), 'siteusercoverphoto_profilepage', true); ?>'  class="seaocore_profile_cover_icon_photo_upload smoothbox"><?php echo $this->translate('Upload Default Cover Photo'); ?></a>
						</li>
            
						<?php if (Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id")) : ?>
            
            <?php if($this->siteusercoverphotoChangeTabPosition):?>
							<li>
								<?php echo $this->htmlLink(array('route' => 'siteusercoverphoto_profilepage', 'action' => 'edit-font-color', 'cover_photo_preview' => $this->cover_photo_preview, 'user_id' => $this->user->user_id), $this->translate('Edit Default Font Color'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_color')); ?>
							</li>
            <?php endif;?>
            
							<li><a  href="javascript:document.seaoCoverPhoto.reposition.start()" class="cover_reposition seaocore_profile_cover_icon_move">
									<?php echo $this->translate("Reposition"); ?></a>
							</li>
							<li>
								<?php echo $this->htmlLink(array('route' => 'siteusercoverphoto_profilepage', 'action' => 'remove-cover-photo', 'user_id' => $this->user->user_id, 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id), $this->translate('Remove Default Cover Photo'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_delete')); ?>
							</li>
						<?php endif; ?>
					</ul>
				</li>
			</ul>
			<?php if (!empty($this->cover_photo_preview)) : ?>
				<div class="save-button dnone">
					<span class="positions-save seaocore_profile_cover_btn"><i><?php echo $this->translate("Save Position"); ?></i></span>
					<span class="positions-cancel seaocore_profile_cover_btn"><i><?php echo $this->translate("Cancel"); ?></i></span>
				</div>
			<?php endif; ?>
		</div>
	<?php //endif; ?>
	<div class="clr"></div>
<?php endif;?>