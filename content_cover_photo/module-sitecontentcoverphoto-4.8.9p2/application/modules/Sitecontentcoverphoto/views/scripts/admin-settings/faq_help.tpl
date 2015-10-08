<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
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
<?php if ($this->faq_id): ?>
    window.addEvent('domready',function(){
      faq_show('<?php echo $this->faq_id; ?>');
    });
<?php endif; ?>
</script>

<?php $i = 1; ?>
<div class="admin_seaocore_files_wrapper">
  <ul class="admin_seaocore_files seaocore_faq">

		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('The cover photo I  have uploaded for my content is displaying blurred. What might be the reason?'); ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div class="code">
          <?php echo $this->translate("This may be happening because you might have uploaded a small cover photo. The recommended width for the cover photo is 640 px."); ?>
        </div>
      </div>
    </li>

		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Tabs on Listings / Products created using Multiple Listing Types plugin and Stores / Marketplace - Ecommerce Plugin respectively are not coming fine when I have enabled the tab position of the tabs to be placed on Listing / Product Profile page to be inside the "Content Cover Photo and Information" widget. What might be the reason?'); ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div class="code">
					<?php echo $this->translate('2) This may be happening because you might have chosen Tabs Design Type to be "New Tabs" for Listings and Products on your site. If you want to enable the tab position of the tabs to be placed inside the "Content Cover Photo and Information" widget, then you should choose the tabs design type to be "SocialEnigne - Default Tabs" for Listings / Products on your site. <br />');?>
					<?php echo $this->translate('You can also choose to show tabs outside the "Content Cover Photo and Information" widget.');?>
        </div>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('The tabs in the "Content Cover Photo and Information" widget are coming outside the widget. What should I do?'); ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div class="code">
          <?php echo $this->translate('To do so, please follow the steps below: <br />'); ?>
					<?php echo $this->translate('1) Go to the Layout Editor section. <br />'); ?>
					<?php echo $this->translate('2) Open the desired Content Profile page from Editing section. <br />'); ?>
					<?php echo $this->translate('3) Now, edit the tab container and select the required number of tabs to be shown in "Content Cover Photo and Information" widget.'); ?>
        </div>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('The CSS of this plugin is not coming on my site. What should I do?'); ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div class="code">
          <?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
        </div>
      </div>
    </li>
  </ul>
</div>