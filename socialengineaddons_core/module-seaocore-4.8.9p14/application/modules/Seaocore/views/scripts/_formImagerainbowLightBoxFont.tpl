<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowLightBoxFont.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow2', {
			id: 'myDemo2',
			'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.fontcolor','#FFFFFF') ?>"),
			'onChange': function(color) {
				$('seaocore_photolightbox_fontcolor').value = color.hex;
			}
		});
		
		showphotolightboxFont("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.show', 1) ?>")
		
	});
</script>

<?php 
echo '
	<div id="seaocore_photolightbox_fontcolor-wrapper" class="form-wrapper">
		<div id="seaocore_photolightbox_fontcolor-label" class="form-label">
			<label for="seaocore_photolightbox_fontcolor" class="optional">
				'. $this->translate('Photos Lightbox Font Color').'
			</label>
		</div>
		<div id="seaocore_photolightbox_fontcolor-element" class="form-element">
			<p class="description">'.$this->translate('Select a font color for the text in the lightbox displaying photos. (Click on the rainbow below to choose your color.)').'</p>
			<input name="seaocore_photolightbox_fontcolor" id="seaocore_photolightbox_fontcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.fontcolor','#FFFFFF') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showphotolightboxFont(option) {
		if(option == 1) {
			$('seaocore_photolightbox_fontcolor-wrapper').style.display = 'block';
		}
		else {
			$('seaocore_photolightbox_fontcolor-wrapper').style.display = 'none';
		}
	}
</script>