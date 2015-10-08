<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-albums-photos.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sm-ui-content sm-choose-cover-photo">
  <div class="top">
    <?php if ($this->album_id || $this->recentAdded): ?>
      <div class="view-album">
        <span class="ui-icon ui-icon-picture"></span>  
        <a class="ui-link-inhirit" href="<?php echo $this->url(array('action' => 'get-albums-photos', 'user_id' => $this->user->user_id, 'special' => $this->special), 'siteusercoverphoto_profilepagemobile', true); ?>"><b><?php echo $this->translate("View Albums") ?></b></a>
      </div> 
    <?php endif; ?>
    <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
    <h3><?php if ($this->special == 'cover'): echo $this->translate("Choose a Cover"); else: echo $this->translate("Choose Profile Picture"); endif; ?></h3>
    <?php endif; ?>
  </div>
  <?php if ($this->album_id || $this->recentAdded): ?>
    <?php if ($this->paginator && $this->paginator->getTotalItemCount() > 0) : ?>
      <div class="clr">
        <ul class="thumbs thumbs_nocaptions">
          <?php foreach ($this->paginator as $photo): ?>
            <li> 
              <a  href="<?php echo $this->url(array('action' => 'upload-cover-photo', 'user_id' => $this->user->user_id, 'photo_id' => $photo->photo_id, 'special' => $this->special), 'siteusercoverphoto_profilepagemobile', true); ?>" title="<?php echo $photo->title; ?>">
                <img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" style="height:80px;width:80px;"/>
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
    <?php if ($this->paginator && $this->paginator->getTotalItemCount() > 0) : ?>
      <div class="album-listing">
        <ul>
          <?php foreach ($this->paginator as $albums): ?>
            <?php if ($albums->count() < 1): continue;
            endif; ?>
            <li> 
              <?php if ($albums->photo_id != 0): ?>
                <a href="<?php echo $this->url(array('user_id' => $this->user->user_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $this->identity_temp, 'special' => $this->special), 'siteusercoverphoto_profilepagemobile') ?>" class="listing-btn">
                  <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/photo_thumb.png'; 
                  $temp_url=$albums->getPhotoUrl('thumb.main'); if(!empty($temp_url)): $url=$albums->getPhotoUrl('thumb.main'); endif;?>
                  <span class="listing-thumb" style="background-image: url(<?php echo $url; ?>);"> </span>
                  <h3><?php echo $this->string()->chunk($this->string()->truncate($albums->getTitle(), 45), 10); ?></h3>
                  <p class="ui-li-aside"><?php echo $this->locale()->toNumber($albums->count())?></p>
                </a> 
              <?php else: ?>
                <a href="<?php echo $this->url(array('action' => 'get-albums-photos', 'user_id' => $this->user->user_id, 'album_id' => $albums->album_id, 'special' => $this->special), 'siteusercoverphoto_profilepagemobile', true); ?>" class="listing-btn">
                  <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/photo_thumb.png'; 
                  $temp_url=$albums->getPhotoUrl('thumb.main'); if(!empty($temp_url)): $url=$albums->getPhotoUrl('thumb.main'); endif;?>
                  <span class="listing-thumb" style="background-image: url(<?php echo $url; ?>);"> </span>
                  <h3><?php echo $this->string()->chunk($this->string()->truncate($albums->getTitle(), 45), 10); ?></h3>
                  <p class="ui-li-aside"><?php echo $this->locale()->toNumber($albums->count())?></p>
                </a> 
              <?php endif; ?>
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
<script type="text/javascript">
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>   
sm4.core.runonce.add(function() { 
             $('.ui-dialog-contain').find('.ui-title').html('<?php if ($this->special == 'cover'): echo $this->translate("Choose a Cover"); else: echo $this->translate("Choose Profile Picture"); endif; ?></h3>');    
          });
          <?php endif; ?>
</script>