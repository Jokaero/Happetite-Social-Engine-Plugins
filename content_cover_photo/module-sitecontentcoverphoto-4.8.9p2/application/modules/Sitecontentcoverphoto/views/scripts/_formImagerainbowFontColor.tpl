<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowFontColor.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<script type="text/javascript">

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

  window.addEvent('domready', function() {

    var r = new MooRainbow('myRainbow1', {
      id: 'myDemo1',
      'startColor':hexcolorTonumbercolor($('hiddenfontcolor').value),
      'onChange': function(color) {
        $('sitecontentcover_font_color').value = color.hex;
				//r.layout.setStyles({'position':'relative','clear':'both'});
				//r.show();
      }
    });
		$('sitecontentcover_font_color').value  = $('hiddenfontcolor').value;

    r.layout.inject($('sitecontentcover_font_color-wrapper'), 'before');
    r.layout.setStyles({'position':'relative','clear':'both'});
    r.show();

  });	


</script>

<div id="sitecontentcover_font_color-wrapper" class="form-wrapper">
	<input name="myRainbow1" id="myRainbow1" style="display:none;">
	<div id="sitecontentcover_font_color-label" class="form-label">
		<label for="sitecontentcover_font_color" class="optional">
			<?php //echo $this->translate("Edit Font Color");?>
		</label>
	</div>
	<div id="sitecontentcover_font_color-element" class="form-element">
		<p class="description"></p>
		<input name="sitecontentcover_font_color" id="sitecontentcover_font_color" type="text" value=""/>
	</div>
</div>

<style type="text/css">

	.form-wrapper{
		clear:both;
	}

	.form-label{
		display:none;
	}

	.moor-okButton{
		display:none;
	}

	.moor-cursor {
		background-image: url(./application/modules/Seaocore/externals/images/moor_cursor.gif);
		width: 12px;
		height: 12px;
	}

	.moor-arrows {
		background-image: url(./application/modules/Seaocore/externals/images/moor_arrows.gif);
		top: 9px;
		left: 270px;
		width: 41px;
		height: 9px;
	}

</style>