<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-cover-photo.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
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
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecontentcoverphoto/externals/scripts/core.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css');
?>
<?php
$user_level_id = $this->level_id;
$moduleName = $this->moduleName;
$fieldName = $this->fieldName;
$photoName='';?>
<?php if( $this->subject()->category_id) : ?>
	<?php if($moduleName == 'siteevent') :?>
		<?php	 $category = Engine_Api::_()->getItem('siteevent_category', $this->subject()->category_id); ?>
		<?php if(isset($category->banner_id) && $category->banner_id)
						$photoName = Engine_Api::_()->storage()->get($category->banner_id, '')->getPhotoUrl(); ?>
	<?php elseif($moduleName == 'sitereview'):?>
		<?php	$category = Engine_Api::_()->getItem('sitereview_category', $this->subject()->category_id); ?>
			<?php 
			if(isset($category->banner_id) && $category->banner_id)
			$photoName = Engine_Api::_()->storage()->get($category->banner_id, '')->getPhotoUrl(); ?>
  <?php endif;?>
<?php endif;?>
<?php
	$user = Engine_Api::_()->getItem('user', $this->subject()->getOwner()->getIdentity());
  $photo='';
	if (Engine_Api::_()->hasModuleBootstrap('advalbum') && isset($user->user_cover) && $user->user_cover) {
		$photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
	} elseif(isset($user->user_cover) && $user->user_cover) {
		$photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
	}
?> 
<?php if (empty($this->cover_photo_preview)): ?>
  <?php if ($this->photo):?>
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
    </div>
  <?php elseif ($this->showMemberLevelBasedPhoto): ?>
    <?php if(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0)) :?>
    <div class="seaocore_profile_cover_photo cover_photo_wap b_dark">
      <img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0), 'thumb.cover')->map() ?>" align="left" class="cover_photo" style="top:<?php echo $this->coverTop ?>px" />
      
    </div>
		<?php elseif(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id")):?>
			<div class="seaocore_profile_cover_photo cover_photo_wap b_dark">
				<img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id"), 'thumb.cover')->map()?>" align="left" class="cover_photo" style="top:<?php echo $this->coverTop?>px" />
                                
			</div>
            <?php else:?>
            <?php if($this->membersCount && $this->showMember && !Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0)): ?>
		<div class="seaocore_members_cover_listing" style="border-bottom: none;">
			<?php $width= 100 /$this->membersCountView; 
				if($this->membersCountView > $this->membersCount && ($this->membersCountView - $this->membersCount) <=2 ):
					$width= 100 /$this->membersCount;
				endif;
			?>
			<?php $i=1; foreach ($this->members as $member):
				if($i > $this->membersCountView):
					break;
				endif;
				$i++;
				$user = Engine_Api::_()->getItem('user', $member->user_id); ?>
				<div class="seaocore_members_cover_member" style="width:<?php echo $width?>%;"> 
					<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user->getOwner(), 'thumb.profile')) ?>
					<span class="seaocore_members_cover_member_name"><a href="<?php echo $user->getHref() ?>" ><?php echo $user->getTitle() ?></a></span>
				</div>
			<?php endforeach; ?>
		</div>
<?php else:?>
<div class="seaocore_profile_cover_photo cover_photo_wap b_dark"><div class="seaocore_profile_cover_photo_empty"></div></div>
<?php endif;?>
    <?php endif;?>
  <?php elseif(!$this->showMemberLevelBasedPhoto && $photo):?>
		<?php $album_id = $photo->album_id;
			if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
				$album = Engine_Api::_()->getItem('advalbum_album', $album_id);
			} else {
				$album = Engine_Api::_()->getItem('album', $album_id);
			}

      if ($album && $album->cover_params && is_array($album->cover_params) && isset($album->cover_params['top'])) {
        $this->coverTop = $album->cover_params['top'];
      } else if (!is_array($album->cover_params) && $album->cover_params) {
				$decodedArray = Zend_Json_Decoder::decode($album->cover_params);
        $this->coverTop = $decodedArray['top'];
			}
    ?>
		<div class="seaocore_profile_cover_photo cover_photo_wap b_dark">
      <?php if (empty($this->can_edit)) : ?>
        <a href="<?php echo $photo->getHref(); ?>" onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;'>
        <?php endif; ?>
				<?php echo $this->itemPhoto($photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo', 'style' => 'top:' . $this->coverTop . 'px')); ?>
				<?php if (empty($this->can_edit)) : ?></a><?php endif; ?>
		</div>
	<?php elseif($this->membersCount && $this->showMember && !Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0)): ?>
		<div class="seaocore_members_cover_listing" style="border-bottom: none;">
			<?php $width= 100 /$this->membersCountView; 
				if($this->membersCountView > $this->membersCount && ($this->membersCountView - $this->membersCount) <=2 ):
					$width= 100 /$this->membersCount;
				endif;
			?>
			<?php $i=1; foreach ($this->members as $member):
				if($i > $this->membersCountView):
					break;
				endif;
				$i++;
				$user = Engine_Api::_()->getItem('user', $member->user_id); ?>
				<div class="seaocore_members_cover_member" style="width:<?php echo $width?>%;"> 
					<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user->getOwner(), 'thumb.profile')) ?>
					<span class="seaocore_members_cover_member_name"><a href="<?php echo $user->getHref() ?>" ><?php echo $user->getTitle() ?></a></span>
				</div>
			<?php endforeach; ?>
		</div>
  <?php elseif ($photoName): ?>
  

		<div class="seaocore_profile_cover_photo cover_photo_wap b_dark">
			<img src="<?php echo $photoName;?>" align="left" class="cover_photo" style="top:<?php echo $this->coverTop ?>px" /> />
                        
   
  <?php else: ?>
    <div class="seaocore_profile_cover_photo cover_photo_wap b_dark"><div class="seaocore_profile_cover_photo_empty"></div></div>
  <?php endif; ?>
  <?php if($this->contentFullWidth):?>
  <div id="sitecontentcover_middle_content"></div>
  <?php endif;?>
  <?php if( $this->sitecontentcoverphotoChangeTabPosition):?>
  <div class="seaocore_profile_cover_gradient"></div>
  <?php endif;?>
  
  <?php if (!empty($this->can_edit)) : ?>
    <div id="sitecontentcoverphoto_cover_options" class="seaocore_profile_cover_options <?php if (empty($this->fieldName) || empty($this->photo)) : ?> dblock <?php endif; ?>">
      <ul class="edit-button">
        <li>
          <?php if ($this->photo) : ?>
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
              <a href='<?php echo $this->url(array('action' => 'upload-cover-photo', $fieldName => $this->subject()->getIdentity(), 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), 'sitecontentcoverphoto_profilepage', true); ?>'  class="seaocore_profile_cover_icon_photo_upload smoothbox"><?php echo $this->translate('Upload Cover Photo'); ?></a>
            </li>
            <li>
              <?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', $this->fieldName => $this->subject()->getIdentity(), 'recent' => 1, 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), 'sitecontentcoverphoto_profilepage', true), $this->translate('Choose from Album Photos'), array(' class' => 'seaocore_profile_cover_icon_photo_view smoothbox')); ?>
            </li>
            <?php if ($this->photo) : ?>
<?php if(!Engine_API::_()->seaocore()->isMobile() && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')):?>
							<?php if($this->sitecontentcoverphotoChangeTabPosition && $this->editFontColor):?>
								<li>
									<?php echo $this->htmlLink(array('route' => 'sitecontentcoverphoto_profilepage', 'action' => 'edit-font-color', $fieldName => $this->subject()->getIdentity(), 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), $this->translate('Edit Font Color'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_color')); ?>
								</li>
              <?php endif;?><?php endif;?>
             <?php if(!Engine_API::_()->seaocore()->isMobile() && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')):?>                                                   
              <li><a  href="javascript:document.sitecontentCoverPhoto.reposition.start()" class="cover_reposition seaocore_profile_cover_icon_move">
                  <?php echo $this->translate("Reposition"); ?></a>
              </li><?php endif;?>
              <li>
                <?php echo $this->htmlLink(array('route' => 'sitecontentcoverphoto_profilepage', 'action' => 'remove-cover-photo', $fieldName => $this->subject()->getIdentity(), 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), $this->translate('Remove Cover Photo'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_delete')); ?>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
      <?php //if (!empty($this->subject()->$fieldName)) : ?>
        <div class="save-button dnone">
          <span class="positions-save seaocore_profile_cover_btn"><i><?php echo $this->translate("Save Position"); ?></i></span>
          <span class="positions-cancel seaocore_profile_cover_btn"><i><?php echo $this->translate("Cancel"); ?></i></span>
        </div>
      <?php //endif; ?>
    </div>
  <?php endif; ?>
  <div class="clr"></div>
<?php else: ?> 
  <?php $user_level_id = $this->level_id; ?>
  <div class="seaocore_profile_cover_photo cover_photo_wap b_dark">

    <?php if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0)): ?>
      <img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0), 'thumb.cover')->map() ?>" align="left" class="cover_photo" style="top:<?php echo $this->coverTop ?>px" />
      <?php //if (!empty($this->can_edit)) :  ?>
      <div class="cover_tip_wrap dnone">
        <div class="cover_tip"><?php echo $this->translate("Drag to Reposition Default Cover Photo") ?></div>
      </div>
      <?php //endif; ?>
    <?php else: ?>
      <div class="seaocore_profile_cover_photo_empty"></div>
    <?php endif; ?>
  </div>
  <?php if( $this->sitecontentcoverphotoChangeTabPosition):?>
  <div class="seaocore_profile_cover_gradient"></div>
  <?php endif;?>
  <div id="sitecontentcoverphoto_cover_options" class="seaocore_profile_cover_options">
    <ul class="edit-button">
      <li>
        <?php if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0)) : ?>
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
            <a href='<?php echo $this->url(array('action' => 'upload-cover-photo', $fieldName => $this->subject()->getIdentity(), 'special' => 'cover', 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id, 'moduleName' => $moduleName, 'subject' => $this->subject()->getGuid()), 'sitecontentcoverphoto_profilepage', true); ?>'  class="seaocore_profile_cover_icon_photo_upload smoothbox"><?php echo $this->translate('Upload Default Cover Photo'); ?></a>
          </li>
          <?php if (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $user_level_id, 0)) : ?>

						<?php if($this->sitecontentcoverphotoChangeTabPosition):?>
							<li>
								<?php echo $this->htmlLink(array('route' => 'sitecontentcoverphoto_profilepage', 'action' => 'edit-font-color', $fieldName => $this->subject()->getIdentity(), 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName, 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id), $this->translate('Edit Default Font Color'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_color')); ?>
							</li>
            <?php endif;?>

            <li> 
							<a href="javascript:document.sitecontentCoverPhoto.reposition.start()" class="cover_reposition seaocore_profile_cover_icon_move">
							<?php echo $this->translate("Reposition"); ?></a>
            </li>

            <li>
              <?php echo $this->htmlLink(array('route' => 'sitecontentcoverphoto_profilepage', 'action' => 'remove-cover-photo', $fieldName => $this->subject()->getIdentity(), 'cover_photo_preview' => $this->cover_photo_preview, 'level_id' => $this->level_id, 'moduleName' => $moduleName, "fieldName" => $fieldName, 'subject' => $this->subject()->getGuid()), $this->translate('Remove Default Cover Photo'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_edit')); ?>
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
  <div class="clr"></div>
<?php endif; ?>