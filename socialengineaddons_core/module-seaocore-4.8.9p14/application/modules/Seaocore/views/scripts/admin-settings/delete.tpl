<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageintegration
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-module.tpl 2012-01-12 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete Socialengineaddons Module ?'); ?></h3>
    <p><?php echo $this->translate("Are you sure that you want to completely remove the old version of “SocialEngineAddOns Core Plugin” and its associated directory installed on your site? If you have done any customization work, then you can take the backup of this plugin."); ?></p>
    <br />
    
    <?php if (!empty($this->meassge)) : ?>
			<div class="tip">
				<span> 
					<?php echo $this->translate('Please log in over FTP and set CHMOD 0777 (recursive) on the /application/modules/Socialengineaddon'); ?>
				</span>
			</div>			
			<button type='submit' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close'); ?></button>			
    <?php endif; ?>

    <?php if (empty($this->meassge)) : ?>
			<p>
				<input type="hidden" name="confirm" value="<?php echo $this->page_id ?>"/>
				<button type='submit'><?php echo $this->translate('Delete'); ?></button>
				or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
			</p>
    <?php endif; ?>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>