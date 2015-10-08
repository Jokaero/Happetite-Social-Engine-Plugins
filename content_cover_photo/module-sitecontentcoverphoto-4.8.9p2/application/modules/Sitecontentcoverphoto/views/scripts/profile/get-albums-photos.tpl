<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-albums-photos.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$moduleName = $this->moduleName;
$fieldName = $this->fieldName;
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css');
?>
<div class="seaocore_popup">
  <div class="seaocore_popup_top">
    <div class="seaocore_popup_des">
      <?php if ($this->album_id || $this->recentAdded): ?>
        <?php if ($this->special == 'cover') : ?>
          <b><?php echo $this->translate("Choose Cover Photo") ?></b>
        <?php else: ?>
          <b><?php echo $this->translate("Choose Profile Picture") ?></b>
        <?php endif; ?>
      <?php else: ?>
        <?php if ($this->special == 'cover') : ?>
          <b><?php echo $this->translate("Select Album to choose Cover Photo") ?></b>
        <?php else: ?>
          <b><?php echo $this->translate("Select Album to choose Profile Picture") ?></b>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($this->album_id || $this->recentAdded): ?>
    <div class="seaocore_popup_options">
      <div class="seaocore_popup_options_left">
        <b><?php echo $this->album_id ? $this->translate("Photos in %s", $this->album->getTitle()) : $this->translate("Recent Photos"); ?></b>
      </div>
      <?php if (Engine_Api::_()->sitecontentcoverphoto()->checkConditionsForAlbum($this->moduleName)): ?>
        <div class="fright"><a class="buttonlink seaocore_profile_cover_icon_photo_view" href="<?php echo $this->url(array('action' => 'get-albums-photos', 'format' => 'smoothbox', 'special' => $this->special, $this->fieldName => $this->subject()->getIdentity(), 'moduleName' => $moduleName, 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName), 'sitecontentcoverphoto_profilepage', true); ?>"><?php echo $this->translate("View Albums") ?></a></div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="seaocore_popup_content">
    <div class="seaocore_popup_content_inner">
      <?php if ($this->album_id || $this->recentAdded): ?>
        <?php if ($this->paginator) : ?>
          <div class="clr seaocore_choose_photos_content">
            <ul class="thumbs">
              <?php foreach ($this->paginator as $photo): ?>
                <li> 
                  <a href="<?php echo $this->url(array('action' => 'upload-cover-photo', $fieldName => $this->subject()->getIdentity(), 'subject' => $this->subject()->getGuid(), 'photo_id' => $photo->photo_id, 'format' => 'smoothbox', 'special' => $this->special, 'fieldName' => $fieldName, 'moduleName' => $moduleName), 'sitecontentcoverphoto_profilepage', true); ?>" title="<?php echo $photo->title; ?>" class="thumbs_photo">
                    <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php else: ?>
          <div class="tip" style="margin-top:10px;">
            <span>
              <?php echo $this->translate("There are currently no photos available.") ?>
            </span>
          </div>
        <?php endif; ?>
      <?php else: ?> 
        <?php if ($this->paginator) : ?>
          <div class="clr seaocore_choose_photos_content">
            <ul class="thumbs">
              <?php foreach ($this->paginator as $albums): ?>
                <?php if ($albums->count() < 1): continue;
                endif; ?>
                <li> 
                  <?php if ($albums->photo_id != 0): ?>
                    <a href="<?php echo $this->url(array('action' => 'get-albums-photos', $this->fieldName => $this->subject()->getIdentity(), 'album_id' => $albums->album_id, 'format' => 'smoothbox', 'special' => $this->special, 'moduleName' => $moduleName, 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName), 'sitecontentcoverphoto_profilepage', true); ?>" title="<?php echo $albums->title; ?>" class="thumbs_photo">
                      <span style="background-image: url(<?php echo $albums->getPhotoUrl('thumb.normal'); ?>);"></span></a>
                  <?php else: ?>
                    <a href="<?php echo $this->url(array('action' => 'get-albums-photos', $this->fieldName => $this->subject()->getIdentity(), 'album_id' => $albums->album_id, 'format' => 'smoothbox', 'special' => $this->special, 'moduleName' => $moduleName, 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName), 'sitecontentcoverphoto_profilepage', true); ?>" class="thumbs_photo" title="<?php echo $albums->title; ?>" >
                      <span><?php echo $this->itemPhoto($albums, 'thumb.normal'); ?></span>
                    </a>
                  <?php endif; ?>
                  <div class="siteuser_profile_album_title">
                    <a href="<?php echo $this->url(array('action' => 'get-albums-photos', $this->fieldName => $this->subject()->getIdentity(), 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $this->identity_temp, 'special' => $this->special, 'moduleName' => $moduleName, 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'format' => 'smoothbox'), 'sitecontentcoverphoto_profilepage', true) ?>" title="<?php echo $albums->title; ?>"><?php echo $albums->title; ?></a>
                  </div>
                </li>		      
              <?php endforeach; ?>
            </ul>
          </div>
        <?php else: ?>
          <div class="tip">
            <span>
              <?php echo $this->translate("There are currently no albums available.") ?>
            </span>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
  <div class="popup_btm fright">
    <button href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Cancel") ?></button>
  </div>
</div>