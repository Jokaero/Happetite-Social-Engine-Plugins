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

<form method="post" class="global_form_popup" action="<?php echo $this->url(array("controller"=>'sitemobile-profile',"action"=>'remove-cover-photo'));?>">
  <div>
    <?php if ($this->special == 'cover') : ?>
      <h3><?php echo $this->translate('Remove Profile Cover Photo?'); ?></h3>
      <p><?php echo $this->translate("Are you sure you want to remove Profile Cover Photo?"); ?></p>
    <?php endif; ?>
    <br />
    <p>
      <input type="hidden" name="confirm" value=""/>
      <button type='submit'><?php echo $this->translate('Remove'); ?></button>
      or <a data-role="button" href="#" data-rel="back"><?php echo $this->translate('Cancel'); ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>