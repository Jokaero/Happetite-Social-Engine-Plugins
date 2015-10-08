<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('User Profiles - Cover Photo, Banner & Site Branding Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if( !empty($this->getHostTypeArray) && is_array($this->getHostTypeArray) && COUNT($this->getHostTypeArray) ): ?>
<div id="dismiss_modules">
  <div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
		</div>
    <div style="float:right;">
      <button onclick="dismissNote();"><?php echo $this->translate('Dismiss'); ?></button>
    </div>
    <div class="seaocore-notice-text">
      <?php echo $this->translate("Note: It seems that this plugin has been used at multiple domains, because of which this plugin may not work properly on domain configures to use this plugin. Please find the list of other domains below :"); ?></br>
      <ul>
      <?php foreach( $this->getHostTypeArray as $getHostName ):
              if( $this->viewAttapt != $getHostName && !empty($getHostName)):
                echo '<li><b>' . $getHostName . '</b></li>';
              endif;      
             endforeach;
      ?>
      </ul>
      <?php echo $this->translate("1) If you do not want to use this plugin on Multiple Domains, then please click on 'Dismiss' button.<br/> 2) If above is not the case and you want to use this plugin on multiple domains, then please file a support ticket from your SocialEngineAddOns <a href='http://www.socialengineaddons.com/user/login' target='_blank'>client area</a>."); ?>
    </div>
  </div>
</div>
<?php
  endif;
  include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; 
?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<?php
  $count = Engine_Api::_()->siteusercoverphoto()->getCount();
?>

<script type="text/javascript">

   function dismissNote() {
    $('is_remove_note').value = 1;
    document.getElementById("siteusercoverphoto_global_settings").submit();
  }

	<?php if($count == 2) :?>
	  window.addEvent('domready', function() {
			showLayoutOptions('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.setlayout', 1);?>');
		});
    if($("siteusercoverphoto_setlayout-wrapper"))
		$("siteusercoverphoto_setlayout-wrapper").style.display = "block";
    if($("siteusercoverphoto_layout-wrapper"))
		$("siteusercoverphoto_layout-wrapper").style.display = "none";
	<?php else:?>
    if($("siteusercoverphoto_setlayout-wrapper"))
		$("siteusercoverphoto_setlayout-wrapper").style.display = "none";
    if($("siteusercoverphoto_layout-wrapper"))
		$("siteusercoverphoto_layout-wrapper").style.display = "block";
    if($("siteusercoverphoto_setlayout-1"))
    $("siteusercoverphoto_setlayout-1").value = 0;
	<?php endif;?>

  function showLayoutOptions(option) {
    if($("siteusercoverphoto_layout-wrapper")) {
			if(option == 1) {
				$("siteusercoverphoto_layout-wrapper").style.display = "block";
			} else {
				$("siteusercoverphoto_layout-wrapper").style.display = "none";
			}
    }
  }
var form = document.getElementById("form-upload");
window.addEvent('domready', function() {
covercontentFullWidth();
});
    function covercontentFullWidth() {

        if (form.elements["siteusercoverphoto_content_full_width"].value == 0) {
            $('siteusercoverphoto_change_tab_position-wrapper').style.display = 'block';
        } else {
            $('siteusercoverphoto_change_tab_position-wrapper').style.display = 'none';
        }
    }

</script>