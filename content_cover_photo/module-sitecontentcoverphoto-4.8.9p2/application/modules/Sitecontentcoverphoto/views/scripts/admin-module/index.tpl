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
	$count = 0;$listingtypesTitle='';
	$coreTable = Engine_Api::_()->getDbtable('modules', 'core');
	$coreTableName = $coreTable->info('name');
?>
<?php foreach ($this->paginator as $item): ?>
	<?php
		if($item->module) {
			if($item->module == 'sitereview') {
				if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')) {

				} elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) {
					$moduleTitle = $coreTable->select()->from($coreTableName, array('title'))->where('name =?', 'sitereview')->where('enabled =?', 1)->query()->fetchColumn();
					$item->module = 'sitereview';
				} 
			} 
		}
	?>
	<?php if (in_array($item->module, $this->enabled_modules_array)): ?>
		<?php $count = 1;?>
	<?php endif;?>
<?php endforeach;?>

<script type="text/javascript">
  function multiDelete() {
    return confirm("<?php echo $this->translate("Are you sure you want to remove the selected modules as Cover Photo? Users will not be able to directly ad cover photo their content from these modules after being removed.") ?>");
  }

  function selectAll() {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<h2>
  <?php echo $this->translate('Content Profiles - Cover Photo, Banner & Site Branding Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules for Content Cover Photos"); ?></h3>
<?php
// Show Success message.
if (isset($this->success_message)) {
  echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully create') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Communityad.') . '</li></ul>';
}
?>

<?php $url = $this->url(array('module' => 'sitecontentcoverphoto', 'controller' => 'settings', 'action' => 'faq', 'faq_id' => 'faq_4'), 'admin_default', true) ?>
<p><?php echo $this->translate('Here, you can manage various modules for whose content you want to enable users to upload cover photo. From Member Level Settings, you can also upload default cover photo for content modules enabled from this page. If you do not want the users to upload cover photo for content from a module, then simply disable that module from here.'); ?></p>
<br style="clear:both;" />


<?php
if ($count):
  ?>
<?php
// Show link for "Create Featured Content".
// echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecontentcoverphoto', 'controller' => 'module', 'action' => 'add'), $this->translate("Add New Module"), array('class' => 'buttonlink seaocore_icon_add'));
?>
<!--<br /><br />-->
  <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete();">
    <table class='admin_table' width= "60%" >
      <thead>
        <tr>
         <!-- <th class='admin_table_short' align="center">
            <?php echo $this->translate("ID"); ?>
          </th>-->
          <th align="left">
            <?php echo $this->translate("Module Name"); ?>
          </th>
<!--          <th align="left">
            <?php echo $this->translate("Resource Type"); ?>
          </th>-->
          <th class="center">
            <?php echo $this->translate("Enabled"); ?>
          </th>
<!--          <th align="left">
            <?php echo $this->translate("Options"); ?>
          </th>-->
        </tr>
      </thead>
      <tbody>
        <?php $is_module_flag = 0; ?>
        <?php foreach ($this->paginator as $item):             
                if($item->module == 'sitereview' && Engine_Api::_()->hasModuleBootstrap('sitereview')){
                $explodedResourceType = explode('_', $item->resource_type);
                if(empty($explodedResourceType[2]))
                    continue;
                }

	if($item->module) {

    if($item->module == 'sitereview' ) {
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')) {
				$moduleTitle = $coreTable->select()->from($coreTableName, array('title'))->where('name =?', 'sitereviewlistingtype')->where('enabled =?', 1)->query()->fetchColumn();
      } elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) {
				$moduleTitle = $coreTable->select()->from($coreTableName, array('title'))->where('name =?', 'sitereview')->where('enabled =?', 1)->query()->fetchColumn();
				$item->module = 'sitereview';
      } 
		} else {
			$moduleTitle = $coreTable->select()->from($coreTableName, array('title'))->where('name =?', $item->module)->where('enabled =?', 1)->query()->fetchColumn();
		}
	}
?>
          <?php if (in_array($item->module, $this->enabled_modules_array)) { ?>
            <tr>
             <!-- <td class="admin_table_centered"><?php echo $item->module_id; ?></td>-->
              <td>
<?php 
	if($item->module) {

    if($item->module == 'sitereview') {
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')) {
				$moduleTitle = $coreTable->select()->from($coreTableName, array('title'))->where('name =?', 'sitereviewlistingtype')->where('enabled =?', 1)->query()->fetchColumn();
      } elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) {
				$moduleTitle = $coreTable->select()->from($coreTableName, array('title'))->where('name =?', 'sitereview')->where('enabled =?', 1)->query()->fetchColumn();
      } 
		} else {
			$moduleTitle = $coreTable->select()->from($coreTableName, array('title'))->where('name =?', $item->module)->where('enabled =?', 1)->query()->fetchColumn();
		}
	} else {
		$moduleTitle = '-'; 
	}

?>

<?php if($item->module == 'sitereview' && Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')):?>
	<?php $explodedResourceType = explode('_', $item->resource_type);?>
	<?php $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;?>
	<?php $listingtypesTitle = $moduleTitle . ' ( ' .$listingtypesTitle . ' ) '?>
<?php elseif($item->module == 'sitereview' && Engine_Api::_()->hasModuleBootstrap('sitereview') ):?>
  <?php $explodedResourceType = explode('_', $item->resource_type);?>
  <?php $listingtypesTitle = 'Reviews & Ratings ( ' . Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural . ' )';?>
<?php endif;?>
		<?php 
     if($listingtypesTitle && $item->module == 'sitereview') {
     echo $listingtypesTitle; 
     } else {
     echo $moduleTitle;
     }
      ?></td>
             <!-- <td ><?php
      if (!empty($item->resource_type)) {
        echo $item->resource_type;
      } else {
        echo '-';
      }
      ?></td>-->
              <td class="admin_table_centered">
								<?php if($item->enabled) :?>
									<a title="<?php echo $this->translate('Disable Module for Content Profile Cover Photo');?>" href='<?php echo $this->url(array('route' => 'admin_default', 'module' => 'sitecontentcoverphoto', 'controller' => 'module', 'action' => 'disabled', 'module_id' => $item->module_id)) ?>'>
											<img src="application/modules/Seaocore/externals/images/approved.gif" />
									</a>
								<?php else:?>
									<a title="<?php echo $this->translate('Enable Module for Content Profile Cover Photo');?>" href='<?php echo $this->url(array('route' => 'admin_default', 'module' => 'sitecontentcoverphoto', 'controller' => 'module', 'action' => 'enabled', 'module_id' => $item->module_id, 'resource_type' => $item->resource_type)) ?>' class="smoothbox">
										<img src="application/modules/Seaocore/externals/images/disapproved.gif" />
									</a>
								<?php endif;?>
							</td>
              <!--<td ><?php //if( !empty($item->resource_id) ){ echo $item->resource_id; }else { echo '-'; } ?></td>-->
             <!-- <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecontentcoverphoto', 'controller' => 'module', 'action' => 'edit', 'module_id' => $item->module_id), $this->translate("edit")); ?>
                <?php if (empty($item->default)): ?>
                  | <a href='<?php echo $this->url(array('action' => 'delete', 'resource_type' => $item->resource_type)) ?>' class="smoothbox">
              <?php echo $this->translate("delete") ?>
                  </a>
            <?php endif; ?>
              </td>-->
            </tr>
      <?php $is_module_flag = 1;
    } ?>
  <?php endforeach; ?>
      </tbody>
    </table>
    <br />
  </form>
  <br />
  <div>
      <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate("There are no modules available.") ?>
    </span>
  </div>
<?php endif; ?>