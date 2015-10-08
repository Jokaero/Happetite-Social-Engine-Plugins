<?php 
$this->headTranslate(array(
'remove tag', 'Cancel', 'delete',	
));
?>
<div style="display: none;">
<style type="text/css">
  .photo_lightbox_left, 
  .seaocore_lightbox_image_content {background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .seaocore_lightbox_user_options{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .seaocore_lightbox_user_right_options{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .seaocore_lightbox_photo_detail{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .seaocore_lightbox_user_options a,
  .seaocore_lightbox_photo_detail,
  .seaocore_lightbox_photo_detail a{color:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.fontcolor','#FFFFFF') ?>;}
</style> 
</div>
<div class='photo_lightbox' id='seaocore_photo_lightbox' style='display: none;'> 
   <?php if (!$this->fixedwindowEnable): ?>  
   <!--  WITHOUT FIX WINDOW DISPLAY-->
  <input type='hidden' id='canReloadSeaocore' value='0' />
  <div class='photo_lightbox_overlay'></div>
  <div class='photo_lightbox_white_content_wrapper' onclick = 'closeSEALightBoxAlbum()'>
    <div class='photo_lightbox_white_content' id='white_content_default_sea_lightbox'>
      <div class='photo_lightbox_options'>
        <a onclick = 'closeSEALightBoxAlbum();' class='close' title='<?php echo $this->translate('Close') ?>' ></a>
      </div>
      <div id='image_div_sea_lightbox'>
        <div class='photo_lightbox_image_content album_viewmedia_container seaocore_lightbox_image_content' id='media_image_div_seaocore'></div>        
        <div id='photo_sea_lightbox_user_options'></div>
        <div class='' id='photo_sea_lightbox_user_right_options'></div>
        <div class='photo_lightbox_text_content' id='photo_sea_lightbox_text'></div>
      </div>
    </div>
  </div>
  <?php endif;?>
</div>
<script type="text/javascript">
  var activityfeed_lightbox = '<?php echo $this->SEA_ACTIVITYFEED_LIGHTBOX ?>';
  var flag = '<?php echo $this->flag ?>';
</script>
