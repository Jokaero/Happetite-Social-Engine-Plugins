<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowTooltipBg.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
    var s = new MooRainbow('myRainbow1', {
      id: 'myDemo1',
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#000000') ?>"),
      'onChange': function(color) {
        $('sitetagcheckin_tooltip_bgcolor').value = color.hex;
      }
    });

  });
</script>

<?php
echo '
  <div id="sitetagcheckin_tooltip_bgcolor-wrapper" class="form-wrapper">
    <div id="sitetagcheckin_tooltip_bgcolor-label" class="form-label">
      <label for="sitetagcheckin_tooltip_bgcolor" class="optional">
              ' . $this->translate('Tooltip Background Color') . '
      </label>
    </div>
    <div id="sitetagcheckin_tooltip_bgcolor-element" class="form-element">
      <p class="description">' . $this->translate('Select a background color for the tooltips that are displayed on clicking location markers on maps. (Click on the rainbow below to choose your color.)') . '</p>
      <input name="sitetagcheckin_tooltip_bgcolor" id="sitetagcheckin_tooltip_bgcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#ffffff') . ' type="text">
      <input name="myRainbow1" id="myRainbow1" src="' . $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
    </div>
  </div>
'
?>