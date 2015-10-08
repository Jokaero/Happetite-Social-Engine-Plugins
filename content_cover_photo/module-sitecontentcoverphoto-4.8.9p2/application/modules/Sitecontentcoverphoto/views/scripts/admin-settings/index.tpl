<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo $this->translate('<div class="tip"><span>Note: You do not have the latest version of the "%s". Please upgrade it to the latest version to enable its integration with Content Profiles - Cover Photo, Banner & Site Branding Plugin.</span></div>', ucfirst($modName));
	}
endif;
?>

<h2><?php echo $this->translate('Content Profiles - Cover Photo, Banner & Site Branding Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
