<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<div style="height:490px; width:425px;" id="show_font_color_content" class="global_form_popup">
	<?php echo $this->form->setAttribs(array('class'=> 'global_form', 'id' => 'show_font_color_content_submit'))->render($this) ?>
</div>
<div id="show_font_color_content_loading" class="global_form_popup" style='display:none;'>
	<center><img style="margin-left:200px;margin-top:100px;" src="<?php echo Zend_Registry::get('Zend_View')->layout()->staticBaseUrl?>application/modules/Seaocore/externals/images/loading.gif"></center>
</div>

<script type="text/javascript">

window.addEvent('domready', function() {
	$('show_font_color_content_submit').addEvent('submit', function(event){
		$('show_font_color_content').style.display='none';
		$('show_font_color_content_loading').style.display='block';
	});
});

</script>