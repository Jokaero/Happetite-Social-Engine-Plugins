<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

var photoLightbox = 1;

</script>

<?php

   $this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');

	$this->headScript()
					->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/core_map.js');
	$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();

	$this->headScript()
					->appendFile("https://maps.googleapis.com/maps/api/js?sensor=true&libraries=places&key=$apiKey");

?>


<div class="stcheckin_photo_tooltip" id="sitetagcheckin_autosuggest_tooltiplocations"></div>

<script type="text/javascript">

  function tagAutosuggestSTMap() {
     var tagAutoSuggestMap = new TagAutoSuggestionMap({
      checkInOptions : {
        'previousLocation' : '',
        'tagParams' : 0,
        'linkDisplay' : '',
        'locationDiv' : 'stcheckin_photo_tooltip',
        'subject': '<?php echo $this->subject; ?>',
        'showSuggest' : 1,
        'displayLocation' : '',
        'content_page_id' : 0,
        'content_business_id' : 0
      },
   });
  }

  if(typeof photoLightbox != 'undefined' || (typeof is_location_ajax != 'undefined' && is_location_ajax == 1)) {
		en4.core.runonce.add(function()
		{
			var cssUrl = "<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css' ?>";
			new Asset.css(cssUrl);
			if(!(typeof TagAutoSuggestionMap == 'function')){
				new Asset.javascript( en4.core.staticBaseUrl+'application/modules/Sitetagcheckin/externals/scripts/core_map.js',{
					onLoad :tagAutosuggestSTMap
				});
			} else {
				tagAutosuggestSTMap();
			}
		});
  } else {
    tagAutosuggestSTMap();
  }

  function initSitetagging() {}

</script>
