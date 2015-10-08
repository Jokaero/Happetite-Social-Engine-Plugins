<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: module-create.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function setModuleName(module_name){
    window.location.href="<?php echo $this->url(array('module' => 'sitecontentcoverphoto', 'controller' => 'module', 'action' => 'add'), 'admin_default', true) ?>/module_name/"+module_name;
  }
</script>

<h2><?php echo $this->translate('Content Profiles - Cover Photo, Banner & Site Branding Plugin') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecontentcoverphoto', 'controller' => 'module', 'action' => 'index'), $this->translate("Back to Manage Modules for Content Cover Photos"), array('class' => 'seaocore_icon_back buttonlink')) ?>

<br style="clear:both;" /><br />

<div class="seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>