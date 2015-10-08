<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove-cover-photo.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <?php if ($this->special == 'cover') : ?>
      <?php if (empty($this->cover_photo_preview)): ?>
        <h3><?php echo $this->translate('Remove Cover Photo?'); ?></h3>
        <p><?php echo $this->translate("Are you sure you want to remove Cover Photo?"); ?></p>
      <?php else: ?>
        <h3><?php echo $this->translate('Remove Default Cover Photo?'); ?></h3>
        <p><?php echo $this->translate("Are you sure you want to remove Default Cover Photo?"); ?></p><br />
        <?php if ($this->count > 1): ?>
          <input type="checkbox" name="sitecontentcoverphoto_removedefaultcoverphoto" value="1"/>
          <label for="sitecontentcoverphoto_removedefaultcoverphoto" class="optional"><?php echo $this->translate("Remove this default cover photo from content of this module for all Member Levels on your site."); ?></label>
        <?php endif; ?>
      <?php endif; ?>
    <?php else: ?>
      <h3><?php echo $this->translate('Remove Profile Picture?'); ?></h3>
      <p><?php echo $this->translate("Are you sure you want to remove Profile Picture?"); ?></p>
    <?php endif; ?>
    <br />
    <p>
      <input type="hidden" name="confirm" value=""/>
      <button type='submit'><?php echo $this->translate('Remove'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>