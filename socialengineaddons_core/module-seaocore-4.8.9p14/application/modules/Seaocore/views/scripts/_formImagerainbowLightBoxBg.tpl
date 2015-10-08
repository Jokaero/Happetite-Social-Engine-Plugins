<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowLightBoxBg.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
    var s = new MooRainbow('myRainbow1', {
      id: 'myDemo1',
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.bgcolor', '#000000') ?>"),
      'onChange': function(color) {
        $('seaocore_photolightbox_bgcolor').value = color.hex;
      }
    });
		
    showphotolightboxBg("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.show', 1) ?>")
		
  });
</script>

<?php
echo '
	<div id="seaocore_photolightbox_bgcolor-wrapper" class="form-wrapper">
		<div id="seaocore_photolightbox_bgcolor-label" class="form-label">
			<label for="seaocore_photolightbox_bgcolor" class="optional">
				' . $this->translate('Photos Lightbox Background Color') . '
			</label>
		</div>
		<div id="seaocore_photolightbox_bgcolor-element" class="form-element">
			<p class="description">' . $this->translate('Select a color for the background of the lightbox displaying photos. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="seaocore_photolightbox_bgcolor" id="seaocore_photolightbox_bgcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photolightbox.bgcolor', '#000000') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
  function showphotolightboxBg(option) {
    if(option == 1) {
      $('seaocore_photolightbox_bgcolor-wrapper').style.display = 'block';
    }
    else {
      $('seaocore_photolightbox_bgcolor-wrapper').style.display = 'none';
    }
  }

  function hexcolorTonumbercolor(hexcolor) {
    var hexcolorAlphabets = "0123456789ABCDEF";
    var valueNumber = new Array(3);
    var j = 0;
    if(hexcolor.charAt(0) == "#")
      hexcolor = hexcolor.slice(1);
    hexcolor = hexcolor.toUpperCase();
    for(var i=0;i<6;i+=2) {
      valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i+1));
      j++;
    }
    return(valueNumber);
  }



</script>