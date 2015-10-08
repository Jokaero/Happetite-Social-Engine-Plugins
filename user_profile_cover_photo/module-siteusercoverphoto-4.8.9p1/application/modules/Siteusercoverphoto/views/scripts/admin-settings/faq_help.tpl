<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageintegration
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.php 2012-31-12 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">  
  function faq_show(id) {
    if($(id)){
      if($(id).style.display == 'block') {
        $(id).style.display = 'none';
      } else {
        $(id).style.display = 'block';
      }
    }
  }
  <?php if($this->faq_id): ?>
  window.addEvent('domready',function(){
    faq_show('<?php echo $this->faq_id ; ?>');
  });
  <?php endif; ?>
</script>

<?php $i=1; ?>
<div class="admin_seaocore_files_wrapper">
	<ul class="admin_seaocore_files seaocore_faq">
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I want to configure custom profile fields to be shown on various pages on my site. How can I do this in Mobile, Tablet and Full site?');?></a>
			<div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
				<div class="code"><?php echo $this->translate('To configure custom profile fields on various pages, please follow the steps below:');?></div>
				 <p>
          <b><?php echo $this->translate("Step 1:") ?></b>
				 </p>
         <div class="code">
					 <?php $url= $this->url(array('module' => 'user', 'controller' => 'fields'), 'admin_default', true) ?>
					 <?php echo $this->translate('Go to Profile Questions by %1$s.', "<a href='$url' target='_blank'>clicking here</a>");?>
         </div>
         <br />
					<p>
            <b><?php echo $this->translate("Step 2:") ?></b>
					</p>
         <div class="code">
					 <?php echo $this->translate("Edit the Questions and choose appropriate options for the setting: SHOW IN WIDGETS DISPLAYING USER PROFILE FIELDS IN FULLSITE, MOBILE AND TABLET?");?>
         </div>
					<p>
            <b><?php echo $this->translate("Step 3:") ?></b>
					</p>
         <div class="code">
					 <?php echo $this->translate("Choose number of profile fields to be shown in the fullsite, mobile and tablet from the widgets mention below:");?>
           <?php echo $this->translate("a) For Full site: USER COVER PHOTO AND INFORMATION");?>
           <?php echo $this->translate("b) For Mobile: User Cover Photo, Information and Profile Fields");?>
				   <?php echo $this->translate("c) For Tablet: User Cover Photo, Information and Profile Fields");?>
         </div>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('The tabs in the "User Cover Photo and Information" widget are not coming fine on my full site. What should I do?');?></a>
			<div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
         <div class="code">
					 <?php echo $this->translate('To do so, go the Layout Editor section from the admin panel of your site. Open the Member Profile page from Editing section. Now, edit the tab container and select the number of tabs to be shown in "User Cover Photo and Information" widget.');?>
         </div>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('The CSS of this plugin is not coming on my site. What should I do?');?></a>
			<div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
         <div class="code">
					 <?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
         </div>
			</div>
		</li>
	</ul>
</div>