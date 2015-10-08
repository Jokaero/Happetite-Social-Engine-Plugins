<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if($this->moduleType == 'seaocore'):?>
	<h2>
		<?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
	</h2>
<?php elseif($this->moduleType == 'siteevent'):?>
	<h2>
		<?php echo $this->translate('Advanced Events Plugin') ?>
	</h2>
<?php endif;?>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render();  ?>
  </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'integrated', 'moduleType' => $this->moduleType), $this->translate("Back to Manage Modules"), array('class'=>'seaocore_icon_back buttonlink')) ?>
<br style="clear:both;" /><br />

<div class='settings'>

	<?php  echo $this->form->render($this); ?>

</div>