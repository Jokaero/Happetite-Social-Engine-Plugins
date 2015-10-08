<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-cover-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css');
?>

<div class="seaocore_upload_photo_popup">
  <div id="form_photo_cover" <?php if ($this->status): ?>class="dnone"<?php endif; ?>>
    <?php echo $this->form->setAttrib('class', 'seaocore_upload_photo_popup_form')->render($this) ?>
    <?php if($this->special == 'cover' && empty($this->cover_photo_preview)):?>
			<button name="siteuser_cancel" id="siteuser_cancel" type="button" onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Cancel"); ?></button>
    <?php endif;?>
  </div>
  <div id="loading_content" <?php if (!$this->status): ?>class="dnone"<?php endif; ?>>
    <div class="seaocore_content_loader"></div>
  </div>
</div>

<?php if ($this->status): ?>
  <script type="text/javascript">
  <?php if ($this->special == 'cover'): ?>
     parent.document.seaoCoverPhoto.getCoverPhoto(1, '<?php echo $this->siteusercoverphoto_setdefaultcoverphoto?>');
  <?php else: ?>
     parent.document.seaoMainPhoto.getMainPhoto();
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