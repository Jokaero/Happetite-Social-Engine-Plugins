<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove-cover-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $remove_url = $this->url(array('route' => 'siteusercoverphoto_profilepagemobile', 'action' => 'remove-cover-photo', 'user_id' => $this->user_id)); ?>
<form method="post" class="global_form_popup" action="<?php echo $remove_url ?>">
  <div>
    <?php if ($this->special == 'cover') : ?>
        <h3><?php echo $this->translate('Remove Profile Cover Photo?'); ?></h3>
				<p><?php echo $this->translate("Are you sure you want to remove your Profile Cover Photo?"); ?></p>
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