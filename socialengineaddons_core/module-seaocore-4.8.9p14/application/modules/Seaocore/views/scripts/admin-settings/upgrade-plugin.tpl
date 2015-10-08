<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upgrade-plugin.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

  function getLoading() {
    document.getElementById('global_form').style.display = 'none';
    document.getElementById('add_progress_bar').style.display = 'block';
  }

</script>

<?php //if( !empty($this->email_error) ): ?>
<!--  <div class="global_form_popup" >
   <?php //if( !empty($this->wrong_email) ): ?> 
    <ul class="form-errors"><li><?php //echo $this->wrong_email; ?></li></ul>
   <?php //endif; ?>
   <form method="post" onsubmit="return isEmail()" name="formemail" >
    <?php //if( !empty($this->error_message) ): ?>
     <p><?php //echo $this->error_message; ?></p><br />
    <?php //endif; ?>
	<b class="bold">Email:</b> <br /><input type="Text" name="email" id="email" style="width:250px;"/><br />
    <div id="email_error" style="color:red;"></div><br />
      <p>
	<button type="submit" onClick="getLoading()"><?php //echo $this->translate('Continue'); ?></button>
	or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php //echo $this->translate('cancel'); ?></a>
	<span id="loading"></span>
      </p>
   </form>
  </div>-->
<?php //else: ?>
<?php if( empty($this->setUpgradeUrl) || !empty($this->error) ): ?>
  <?php if( empty($this->error) ): ?>

  <div id="add_progress_bar" style="display:none;">
    <div class="settings"><form ><div style="padding:15px;"><div style="font-weight:bold;">Please do not close this page or navigate to another page till you redirect to manage plugin section or error message. <br /><br /><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/progress-bar.gif" alt="Loading.." /></center></div></div></form></div>
  </div>


  <div class="global_form_popup" id="global_form" style="display:block;">
  <form method="post" class="global_form_popup">
      <h3><?php echo $this->translate('Upgrade "%s"?', $this->title); ?></h3>
      <p><?php echo $this->translate('Are you sure that you want to upgrade "%s" on your site to the latest version "%s"?', $this->title, $this->version); ?></p>
      <br />
      <p>
	<button type="submit" onClick="getLoading()"><?php echo $this->translate('Upgrade'); ?></button>
	or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
	<!--<span id="loading"></span>-->
      </p>
  </form>
  </div>
  <?php else: ?>
  <div class="global_form_popup">
    <ul class="form-errors">
      <li>
	<?php
	  switch($this->error_flag){
	    case 1:
	      echo $this->translate("Sorry, you can not upgrade the plugin because only super admin is allowed to upgrade the plugin. Please log in as super admin and try again later.");
	    break;

	    case 2:
	      echo (empty($this->error_message)? 'License not valid.': $this->error_message);
	    break;

	    default:
	      echo $this->translate('Sorry, you can not upgrade the plugin as the license key entered by you is not correct. Please check the license key of this plugin and try again later. If you still have the problem, then please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.');
	    break;
	  }
	?>
      </li>
    </ul>
    <button  onclick='javascript:parent.Smoothbox.close()'> <?php echo $this->translate("Close") ?> </button>
  </div>
  <?php endif; ?>
<?php else: ?>
<?php $getUpgrade = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'set-upgrade-url'), 'admin_default', true); ?>
  <script type="text/javascript">
    window.addEvent('domready', function() {
      parent.window.location.href = '<?php echo $getUpgrade; ?>';
      parent.Smoothbox.close ();
    });
  </script>
<?php endif; ?>
<?php //endif; ?>
