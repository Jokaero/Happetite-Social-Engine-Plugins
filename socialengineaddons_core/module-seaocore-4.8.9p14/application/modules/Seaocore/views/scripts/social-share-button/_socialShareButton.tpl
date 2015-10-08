<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowFeatured.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="seaocore_socialshare_buttons">
  <div class="addthis_toolbox addthis_default_style ">
    <a class="addthis_button_preferred_1"></a>
    <a class="addthis_button_preferred_2"></a>
    <a class="addthis_button_preferred_3"></a>
    <a class="addthis_button_preferred_4"></a>
    <a class="addthis_button_preferred_5"></a>
    <a class="addthis_button_compact"></a>
    <a class="addthis_counter addthis_bubble_style"></a>
  </div>
  <script type="text/javascript">  
	
    var addthis_config = {
      services_compact: "facebook, twitter, linkedin, google, digg, more",
      services_exclude: "print, email"
    }
    en4.core.runonce.add(function() {
      if (window.addthis) {
        //  window.addthis = null; 
        window.addthis_share.url = window.location.href;
        window.addthis.toolbox('.addthis_toolbox');
      } else {
        new Asset.javascript( 'https://s7.addthis.com/js/250/addthis_widget.js?domready=1"'); 
      }
    });
  </script>
</div>