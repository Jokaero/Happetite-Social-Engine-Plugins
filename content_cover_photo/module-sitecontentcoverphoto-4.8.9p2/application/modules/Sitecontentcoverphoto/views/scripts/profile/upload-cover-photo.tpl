<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-cover-photo.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css');
?>

<div class="seaocore_upload_photo_popup global_form_popup">
  <div id="form_photo_cover" <?php if ($this->status): ?>class="dnone"<?php endif; ?>>
    <?php echo $this->form->setAttrib('class', 'seaocore_upload_photo_popup_form')->render($this) ?>
    <?php if ($this->special == 'cover' && empty($this->cover_photo_preview)): ?>
      <button name="siteuser_cancel" id="siteuser_cancel" type="button" onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Cancel"); ?></button>
    <?php endif; ?>
  </div>
  <div id="loading_content" <?php if (!$this->status): ?>class="dnone"<?php endif; ?>>
    <div class="seaocore_content_loader"></div>
  </div>
</div>

<?php if ($this->status): ?>
  <script type="text/javascript">
  <?php if ($this->special == 'cover'): ?>
      parent.document.sitecontentCoverPhoto.getCoverPhoto(1, '<?php echo $this->sitecontentcoverphoto_setdefaultcoverphoto ?>');
  <?php else: ?>
      parent.document.sitecontentMainPhoto.getMainPhoto();
  <?php endif; ?>
  </script>
<?php else: ?>
  <script type="text/javascript">
    function uploadPhoto(){
      document.getElementById('cover_photo_form').submit();
      document.getElementById('form_photo_cover').addClass('dnone');
      document.getElementById('loading_content').removeClass('dnone');
    }
  </script>
<?php endif; ?>