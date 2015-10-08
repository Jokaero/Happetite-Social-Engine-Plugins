<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    seaocore
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');?>
<div id="sr_scroll_bar_top_content_<?php echo $this->identity ?>"> </div>
<a id="back_to_top_seaocore_button_common_<?php echo $this->identity ?>" href="#" class="seaocore_up_button Offscreen" title="<?php echo $this->translate("%s", $this->mouseOverText); ?>">
		<span></span>
 </a>

<script type="text/javascript"> 
  ScrollToTopSeao('sr_scroll_bar_top_content_<?php echo $this->identity ?>','back_to_top_seaocore_button_common_<?php echo $this->identity ?>');
</script>
