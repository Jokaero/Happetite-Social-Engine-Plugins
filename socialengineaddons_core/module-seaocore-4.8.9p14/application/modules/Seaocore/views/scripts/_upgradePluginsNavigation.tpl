<?php 
  $tempUrl = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'upgrade'), 'admin_default', false);
?>

<div class='tabs seaocore_sub_tabs'>
  <ul class="navigation">    
      <li  class="<?php echo ($this->selectedMenuType == 'all')? 'active': ''; ?>">
        <a href="<?php echo $tempUrl; ?>?type=all"><?php echo $this->translate("Plugins"); ?></a>
      </li>
  
    
    <?php if(!empty($this->enabledPluginsArray) && in_array("sitepage", $this->enabledPluginsArray)): ?>
      <li  class="<?php echo ($this->selectedMenuType == 'page')? 'active': ''; ?>">
        <a href="<?php echo $tempUrl; ?>?type=page"><?php echo $this->translate("Directory / Pages Ext"); ?></a>
      </li>
    <?php endif; ?>
    
    <?php if(!empty($this->enabledPluginsArray) && in_array("sitebusiness", $this->enabledPluginsArray)): ?>
      <li  class="<?php echo ($this->selectedMenuType == 'business')? 'active': ''; ?>">
        <a href="<?php echo $tempUrl; ?>?type=business"><?php echo $this->translate("Directory / Businesses Ext"); ?></a>
      </li>
    <?php endif; ?>
    
    <?php if(!empty($this->enabledPluginsArray) && in_array("sitegroup", $this->enabledPluginsArray)): ?>
      <li  class="<?php echo ($this->selectedMenuType == 'group')? 'active': ''; ?>">
        <a href="<?php echo $tempUrl; ?>?type=group"><?php echo $this->translate("Groups / Communities Ext"); ?></a>
      </li>
    <?php endif; ?>
    
    <?php if(!empty($this->enabledPluginsArray) && in_array("siteevent", $this->enabledPluginsArray)): ?>
      <li  class="<?php echo ($this->selectedMenuType == 'event')? 'active': ''; ?>">
        <a href="<?php echo $tempUrl; ?>?type=event"><?php echo $this->translate("Advanced Events Ext"); ?></a>
      </li>
    <?php endif; ?>
   
    <?php if(!empty($this->enabledPluginsArray) && in_array("sitereview", $this->enabledPluginsArray)): ?>
      <li  class="<?php echo ($this->selectedMenuType == 'review')? 'active': ''; ?>">
        <a href="<?php echo $tempUrl; ?>?type=review"><?php echo $this->translate("Multiple Listing Types Ext"); ?></a>
      </li>
    <?php endif; ?>
      
      <li  class="<?php echo ($this->selectedMenuType == 'themes')? 'active': ''; ?>">
        <a href="<?php echo $tempUrl; ?>?type=themes"><?php echo $this->translate("Themes"); ?></a>
      </li>
    
  </ul>
</div>

<style>
  .tabs ul li a{
    font-size: 12px;
  }
</style>