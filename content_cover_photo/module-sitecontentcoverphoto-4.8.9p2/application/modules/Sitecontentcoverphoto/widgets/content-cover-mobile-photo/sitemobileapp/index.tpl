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
<script type="text/javascript">
  var seaocore_content_type = '<?php echo $this->subject()->getType(); ?>';
</script>


<?php
$minHeight = 60;
$tablePrimaryFieldName = $this->tablePrimaryFieldName;
$moduleName = $this->moduleName;
$fieldName = $this->fieldName;
?>
<?php
	$user = Engine_Api::_()->getItem('user', $this->subject()->getOwner()->getIdentity());
  $photo='';
	if (Engine_Api::_()->hasModuleBootstrap('advalbum') && isset($user->user_cover)) {
		$photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
	} elseif(isset($user->user_cover)) {
		$photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
	}
?> 
<?php $level_id = $this->level_id; ?>
<div class="content_cover_wrapper">
  <?php if (($this->can_edit) && ($this->photo)): ?>
    <a href="<?php echo $this->photo->getHref(); ?>" data-linktype='photo-gallery' >
    <!--<a href="#icons_options_cover_photo" data-rel="popup" data-transition="pop">-->
    <?php endif; ?>
    <div class='content_cover_photo_wrapper' id="sitecontent_cover_photo" style='min-height:<?php echo $minHeight; ?>px;'>
      <?php if ($this->photo) : ?>
        <div class="content_cover_photo">
          <?php if (empty($this->can_edit)): ?>
            <a href="<?php echo $this->photo->getHref(); ?>" class="thumbs_photo" >
            <?php endif; ?>
            <?php echo $this->itemPhoto($this->photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo')); ?>
            <?php if (empty($this->can_edit)) : ?>
            </a>
          <?php endif; ?>
        </div>
      <?php elseif (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $level_id, 0) && $this->showMemberLevelBasedPhoto): ?>
        <div class="content_cover_photo">
          <img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->subject(), $moduleName, $level_id, 0), 'thumb.cover')->map() ?>" align="left" class="cover_photo" style="top:<?php //echo $this->coverTop ?>px" />
        </div>
			<?php elseif(!$this->showMemberLevelBasedPhoto && $photo):?>
				<?php $album_id = $photo->album_id;
					if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
						$album = Engine_Api::_()->getItem('advalbum_album', $album_id);
					} else {
						$album = Engine_Api::_()->getItem('album', $album_id);
					}

					if ($album && $album->cover_params) {
						$decoded_cover_param = Zend_Json_Decoder::decode($album->cover_params);
						$this->coverTop = $decoded_cover_param['top'];
					}
				?>
				<div class="content_cover_photo_empty ui-bar-c">
					<?php if (empty($this->can_edit)): ?>
            <a href="<?php echo $photo->getHref(); ?>" class="thumbs_photo" >
            <?php endif; ?>
						<?php echo $this->itemPhoto($photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo', 'style' => 'top:' . $this->coverTop . 'px')); ?>
 <?php if (empty($this->can_edit)) : ?>
            </a>
          <?php endif; ?>
				</div>
      <?php else: ?>
        <div class="seaocore_profile_cover_photo_empty ui-bar-c"></div>
      <?php endif; ?>
      <?php if ($this->can_edit): ?>  
<!--        <div class="seaocore_profile_cover_upload_op"><span></span></div>-->
      <?php endif; ?>
    </div>
    <?php if ($this->can_edit): ?>
    </a>
  <?php endif; ?>

  <?php
  switch ($moduleName) {
    case "sitepage":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSitepage.tpl';
      break;
    case "sitebusiness":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSitebusiness.tpl';
      break;
    case "sitegroup":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSitegroup.tpl';
      break;
    case "sitereview":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSitereview.tpl';
      break;
    case "sitestore":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSitestore.tpl';
      break;
    case "sitestoreproduct":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSitestoreproduct.tpl';
      break;
    case "siteevent":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSiteevent.tpl';
      break;
    case "album":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-app-main/_mainPhotoCoverContentSitealbum.tpl';
      break;
    case "default":
      include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/sitemobile/scripts/_mainPhotoCoverContent.tpl';
      break;
  }
  ?>

</div>

<div data-role="popup" id="icons_options_cover_photo" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15" data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window" >
  <div data-inset="true" style="min-width:150px;" class="sm-options-popup change-cover-photo-options">
    <?php if ($this->photo): ?>
      <a href="<?php echo $this->photo->getHref(); ?>" data-linktype='photo-gallery' class="ui-btn-default ui-btn-corner-all"><?php echo $this->translate("View Photo"); ?></a>
    <?php endif; ?>
      <form id="upload_cover_photo_mobile" enctype="multipart/form-data" method="post" class="upload_cover_photo_mobile ui-btn-default">
      <label for="Filedata" data-mini="true" class="ui-btn-default"><?php echo $this->translate("Upload Cover Photo"); ?></label>
      <input id="MAX_FILE_SIZE" type="hidden" value="1073741824" name="MAX_FILE_SIZE">
      <input id="Filedata" type="file" onchange="uploadCoverPhoto();" name="Filedata">
    </form>
    <?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', $this->fieldName => $this->subject()->getIdentity(), 'recent' => 1, 'special' => 'cover', 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), 'sitecontentcoverphoto_profilepagemobile', true), $this->translate('Choose from Album Photos'), array(' class' => 'ui-btn-default ui-btn-corner-all')); ?>
    <?php if (!empty($this->subject()->$fieldName) && $this->photo) : ?>
      <?php echo $this->htmlLink(array('route' => 'sitecontentcoverphoto_profilepagemobile', 'action' => 'remove-cover-photo', $this->fieldName => $this->subject()->getIdentity(), 'special' => 'cover', 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), $this->translate('Remove'), array(' class' => 'smoothbox ui-btn-default ui-btn-corner-all')); ?>
    <?php endif; ?>
    <a href="#" data-rel="back" class="ui-btn-default ui-btn-corner-all">
      <?php echo $this->translate('Cancel'); ?>
    </a>
  </div>
</div>
<script type="text/javascript">

  function uploadCoverPhoto() {
    $('#upload_cover_photo_mobile').attr("action", "<?php echo $this->url(array('action' => 'upload-cover-photo', $fieldName => $this->subject()->getIdentity(), 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), 'sitecontentcoverphoto_profilepagemobile', true); ?>");
    $('#upload_cover_photo_mobile').submit();
  }

  sm4.core.runonce.add(function() {
    setTimeout(function(){
      var imageMaxHeight = $('body').width();
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
