<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-module.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(empty($this->isPost)) :  ?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Confirm to install SocialEngineAddOns Plugin ?'); ?></h3>
    <p><?php echo $this->translate("Are you sure that you want to install socialengineaddons plugin at your site ?. After your confirmation we have disabled all SocialEngineAddOns Plugins at your site. After install SocialEngineAddOns core plugin at your site, then you need to upgrade all SocialEngineAddOns Plugin at your site. Go to manage section of your site to enabled all SocialEngineAddOns Plugins at your site."); ?></p>
    <br />
    <p>
      <button type='submit'><?php echo $this->translate('Confirm'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
<?php else: ?>
<script type="text/javascript">
	window.addEvent('domready', function() {
		parent.window.location.reload(); 
		parent.Smoothbox.close () ;
	});
</script>
<?php endif; ?>