<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>



<form method="post" class="global_form_popup" action='<?php echo $this->url(array('route' => 'admin_default', 'module' => 'sitecontentcoverphoto', 'controller' => 'module', 'action' => 'enabled', 'module_id' => $this->module_id, 'resource_type' => $this->moduleName))?>'>
  <div class="tip">
		<?php $url= $this->url(array( 'module' => 'core', 'controller' => 'content'), 'admin_default', true);?>
		<span>
     <?php echo $this->translate('To enable users to upload cover photo for content of this module, please place "Content Cover Photo and Information" widget on the Content Profile page of this module from the %1$sLayout Editor%2$s section.', "<a target='_blank' href='$url'>", '</a>');?>
    </span>
		<input type="hidden" name="module_id" value="<?php echo $this->module_id ?>"/>
		<button type='submit'><?php echo $this->translate('Ok, Enable this module'); ?></button>
		or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>