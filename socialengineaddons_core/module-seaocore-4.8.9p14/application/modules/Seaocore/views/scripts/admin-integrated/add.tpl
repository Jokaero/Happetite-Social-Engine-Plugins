<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8988 2011-06-15 01:35:25Z john $
 * @author     Sami
 */
?>

<script type="text/javascript">

  function setModuleName(item_module, type){
   window.location.href="<?php echo $this->url(array('module'=>'seaocore', 'controller'=>'integrated', 'action'=>'add', 'moduleType' => $this->moduleType),'admin_default',true)?>/item_module/"+item_module+'/type/'+ type;
 }

</script>

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