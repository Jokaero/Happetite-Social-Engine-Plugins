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

<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules"); ?></h3>


<p><?php echo $this->translate('Here, you can manage various modules for enable users to event for that module. If you do not want the users to event for content from a module, then simply disable that module from here.'); ?></p>
<br />
<?php if(count($this->integratedtypeList) > 0):?>
  
  <?php if($this->selectedIntegratedType->type != 'siteevent_create'):?>
		<div class="admin_menus_filter">
			<form action="<?php echo $this->url() ?>" method="get">
				<?php echo $this->formSelect('type', $this->selectedIntegratedType->type, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->integratedtypeList) ?>
			</form>
		</div><br />
  <?php endif;?>
	<?php if($this->selectedIntegratedType->type):?>
		<?php
			echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'integrated', 'action' => 'add', 'type' => $this->selectedIntegratedType->type, 'moduleType' => $this->moduleType), $this->translate("Add New Module"), array('class'=>'buttonlink seaocore_icon_add'));
		?><br /><br />

	<?php endif;?>

	<?php $integratedTableName = Engine_Api::_()->getDbtable('integrated', 'seaocore');?>
	<?php if(count($this->integrated) > 0):?>
		<table class='admin_table'>
			<thead>
				<tr>
					<th align="left">
						<?php echo $this->translate("Module Name"); ?>
					</th>
					<th align="left">
						<?php echo $this->translate("Item Type"); ?>
					</th>
					<th align="left">
						<?php echo $this->translate("Enabled"); ?>
					</th>
					<th align="left">
						<?php echo $this->translate("Options"); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->integrated as $item):?>
				<tr>
					<td class="admin_table_centered"><?php echo $integratedTableName->getModuleTitle($item->item_module);?></td>
					<td class="admin_table_centered"><?php echo $item->item_type;?></td>
					<td class="admin_table_centered">
						<?php if($item->enabled) :?>
							<a title="<?php echo $this->translate('Disable Module');?>" href='<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'integrated', 'action' => 'enabled-disabled', 'id' => $item->id, 'type' => $item->type), 'admin_default', true) ?>'>
									<img src="application/modules/Seaocore/externals/images/approved.gif" />
							</a>
						<?php else:?>
							<a title="<?php echo $this->translate('Enable Module');?>" href='<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'integrated', 'action' => 'enabled-disabled', 'id' => $item->id, 'type' => $item->type), 'admin_default', true) ?>'>
								<img src="application/modules/Seaocore/externals/images/disapproved.gif" />
							</a>
						<?php endif;?>
					</td>
					<td class="admin_table_centered">
						<?php
							echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'integrated', 'action' => 'edit', 'id' => $item->id, 'type' => $item->type, 'item_module' => $item->item_module, 'moduleType' => $this->moduleType), $this->translate("edit")) ;
							echo ' | ' . $this->htmlLink(array('route' => 'admin_default', 'module' => 'seaocore', 'controller' => 'integrated', 'action' => 'delete', 'id' => $item->id, 'type' => $item->type), $this->translate("delete"), array('class' => 'smoothbox'));
						?>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	<?php else:?>
		<div class="tip">
			<span><?php echo $this->translate("You have not integrated any module.");?></span>
		</div>
	<?php endif;?>
<?php else:?>
  <div class="tip">
		<span><?php echo $this->translate("You do not any module which you can integrate for this plugin.");?></span>
  </div>
<?php endif;?>