<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: import.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(empty( $this->error)): ?>
<script type="text/javascript">
	function showLightbox()
	{


    $('import_form_load').innerHTML = "<div><center><b class='bold'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing file content...")) ?>' + "</b></center><center class='mtop10'><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/admin/loader.gif' alt='<?php echo $this->string()->escapeJavascript($this->translate("Importing file content...")) ?>' /></center></div>";
		$('import_form').style.display = 'none';
	}
</script>
<div id="import_form_load"  class=""></div>
<form method="post" class="global_form_popup " id="import_form">
  <div>
    <h3><?php echo $this->translate('Import Data?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to import.'); ?>
    </p>
    <br />
    <p>     
      <button type='submit' onclick="showLightbox()"><?php echo $this->translate('Import'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
<?php else: ?>
<div class="tip">
  <span>
<?php echo  $this->error; ?>
  </span>
</div>
<?php endif; ?>