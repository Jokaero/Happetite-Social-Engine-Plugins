<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _upgrade_messages.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
function dismiss_opengraph(modName) {
	document.cookie= modName + "_opengraphdismiss" + "=" + 1;
	$('dismiss_opengraphmodules').style.display = 'none';
}
</script>

<?php 
  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
  if (empty ($fbmodule) || empty($fbmodule->enabled) || $fbmodule->version <=  '4.2.3')
    return;
  
	$moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
  $metainfos = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo($moduleName);
  if (!empty($metainfos) && !empty($metainfos->opengraph_enable)) {
    
    return;
  }
	if( !isset($_COOKIE[$moduleName . '_opengraphdismiss']) ):
?>
<div id="dismiss_opengraphmodules">
	<div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
		</div>
<div style="float:right;">
	<button onclick="dismiss_opengraph('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
</div>
		<div class="seaocore-notice-text ">
			<?php 
       $url = $this->url(array('module' => 'facebookse', 'controller' => 'adminsettings', 'action' => 'opengraph'), 'facebookse_admin_manage_opengraph', true);
      echo "Note: You have 'Advanced Facebook Integration / Likes, Social Plugins and Open Graph' plugin on your site. Please click <a href='$url' target='_blank' > here </a>, to configure the Open Graph Tags for this module."; ?>
		</div>	
	</div>
</div>
<?php endif; ?>