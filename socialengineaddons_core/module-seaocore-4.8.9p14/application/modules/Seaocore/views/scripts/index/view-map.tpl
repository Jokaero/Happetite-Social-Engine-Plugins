<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: save-location.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//GET API KEY
	$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey(); 


$this->headScript()
  ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey")
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');

$siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
?>
<?php if(!empty($this->is_mobile)) : ?>
<div class="seaocore_viewmap_content_main">
	<div class="seaocore_viewmap_head">
    <?php echo $this->checkin['location']; ?>
  </div>
  <div class="seaocore_viewmap_content">

    <div id="panel-content-area" class="seaocore_viewmap_search_area">           
      <div id="panel-scroll" class="seaocore_map_dir_cont">
        <div class="seaocore_viewmap_search_options">
          <table>
            <tr>
              <td><strong><?php echo $this->translate('A:'); ?></strong></td>
              <td><input type="text" name='origin' id="origin" /></td>
            </tr>
            <tr>
              <td><strong><?php echo $this->translate('B:'); ?></strong></td>
              <td><?php echo $this->checkin['location'] ?><input type="hidden" name='destination' id="destination" value="<?php echo $this->checkin['location'] ?>" /></td>
            </tr>
            <tr>
              <td></td>
              <td>
	              <button type="button" onclick="getDirection()" style="margin-top:10px;"><?php echo $this->translate('Get Directions'); ?></button>
	              <?php //echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($this->checkin['location']), $this->translate('&nbsp;'), array('target' => '_blank', 'class'=> 'sdf', 'title' => $this->translate('See on Google Map'))) ?>
	            </td>
	<!--			              <td>
	            	<a href="<?php //echo 'https://maps.google.com/?q=' . urlencode($this->checkin['location']); ?>" class="seaocore_map_tip" target="_blank">
	        				<p class="seaocore_map_tip_content"><?php //echo $this->translate('See on Google Maps'); ?></p>
	        				<img src="<?php //echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/map-search.png" alt="" />
	        			</a>
	            </td>-->
            </tr>
          </table>
        </div>
        <div id="panel" class="clr"></div>
      </div>
    </div>
    <div id="sitetagechekin-view-map" class="seaocore_viewmap_map"></div>
    <?php $showContect = "Locations on "; ?>
    <div class="seaocore_map_info"><?php echo $this->translate($showContect); ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
  </div>
</div>
<style type="text/css">
#panel-content-area {
  width: 320px;    
  margin-left: 5px;
}
#global_page_seaocore-index-view-map{
	padding:0px;
	margin:0px;
}
#global_page_seaocore-index-view-map #global_content_simple{
	overflow:visible;
}
</style>





<?php else : ?>
<?php
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<div class="seaocore_viewmap_content_main">
	<div class="seaocore_viewmap_head">
    <?php echo $this->checkin['location']; ?>
    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/icons/closebox.png" onclick="javascript:parent.Smoothbox.close()" title="<?php echo $this->translate('Close'); ?>" class="fright" />
  </div>
  <div class="seaocore_viewmap_content">
    <div id="sitetagechekin-view-map" class="seaocore_viewmap_map"></div>
    <?php $showContect = "Locations on "; ?>
    <div class="seaocore_map_info"><?php echo $this->translate($showContect); ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
    <div id="panel-content-area" class="seaocore_viewmap_search_area">           
      <div id="panel-scroll" class="scroll_content" style="max-height:480px;width:270px !important;">
        <div class="seaocore_viewmap_search_options">
          <table>
            <tr>
              <td><strong><?php echo $this->translate('A:'); ?></strong></td>
              <td><input type="text" name='origin' id="origin" /></td>
            </tr>
            <tr>
              <td><strong><?php echo $this->translate('B:'); ?></strong></td>
              <td><?php echo $this->checkin['location'] ?><input type="hidden" name='destination' id="destination" value="<?php echo $this->checkin['location'] ?>" /></td>
            </tr>
            <tr>
              <td></td>
              <td>
              	<table>
              		<tr>
              			<td>
			                <button type="button" onclick="getDirection()" ><?php echo $this->translate('Get Directions'); ?></button>
			                <?php //echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($this->checkin['location']), $this->translate('&nbsp;'), array('target' => '_blank', 'class'=> 'sdf', 'title' => $this->translate('See on Google Map'))) ?>
			              </td>
			              <td>
			              	<a href="<?php echo 'https://maps.google.com/?q=' . urlencode($this->checkin['location']); ?>" class="seaocore_map_tip" target="_blank">
                				<p class="seaocore_map_tip_content"><?php echo $this->translate('See on Google Maps'); ?></p>
                				<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/map-search.png" alt="" />
                			</a>
			              </td>
			            </tr>
			           </table>     
              </td>
            </tr>
          </table>
        </div>
        <div id="panel" class="clear"></div>
      </div>
    </div>
	  <div class="seaocore_showhide_icon">
	    <span id="collapse_hide" title="<?php echo $this->translate('Hide Panel'); ?>" onclick="toggoleDirectionContent()">
	    	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/map-arrow-right.png" alt="" />
	    </span>
	    <span id="collapse_show" style="display:none;" title="<?php echo $this->translate('Show Panel'); ?>" onclick="toggoleDirectionContent()">
	    	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/map-arrow-left.png" alt="" />
	    </span>
	  </div>
  </div>
</div>
<style type="text/css">
#panel-content-area {
  width: 320px;    
  margin-left: 5px;
}
#global_page_seaocore-index-view-map{
	padding:0px;
	margin:0px;
}
#global_page_seaocore-index-view-map #global_content_simple{
	overflow:visible;
}
</style>

<?php endif; ?>
<script type="text/javascript">
  var myLatlng;
  var directionsService = new google.maps.DirectionsService();
  var directionsDisplay;
  var mapGetDirection;
  var scrollGetDirection;
  function initializeGetDirectionMap() { 
    
    directionsDisplay = new google.maps.DirectionsRenderer();
    var myLatlng = new google.maps.LatLng(<?php echo $this->checkin['latitude'] ? $this->checkin['latitude'] : 0; ?>,<?php echo  $this->checkin['longitude'] ? $this->checkin['longitude'] : 0; ?>);
    var myOptions = {
      zoom: 8 ,
      center: myLatlng,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP, 
    }

    mapGetDirection = new google.maps.Map(document.getElementById("sitetagechekin-view-map"), myOptions);

    directionsDisplay.setMap(mapGetDirection);
    directionsDisplay.setPanel(document.getElementById('panel'));
    var marker = new google.maps.Marker({
      position: myLatlng,
      map: mapGetDirection,
      title: "<?php // echo $this->string()->escapeJavascript($this->checkin['location'])               ?>"
   
    });
  
  }
  window.addEvent('domready',function(){
    initializeGetDirectionMap();
    scrollGetDirection= new SEAOMooVerticalScroll('panel-content-area', 'panel-scroll', {} );
    new google.maps.places.Autocomplete(document.getElementById('origin'));
    
    <?php $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(); ?>
    <?php if(isset($getMyLocationDetailsCookie['location']) && !empty($getMyLocationDetailsCookie['location'])): ?>
        document.getElementById('origin').value = '<?php echo $getMyLocationDetailsCookie['location']; ?>';
        getDirection();
    <?php else: ?>
        if (navigator.geolocation) { 
          navigator.geolocation.getCurrentPosition(function(position){        
            if(!position.address){
              var mapDetect=  new google.maps.Map(new Element('div'), {
                mapTypeId: google.maps.MapTypeId.ROADMAP, 
                center: new google.maps.LatLng(0, 0)
              });
              var service = new google.maps.places.PlacesService(mapDetect);
              var request = {
                location: new google.maps.LatLng(position.coords.latitude,position.coords.longitude), 
                radius: 500
              };
    //           var service = new google.maps.places.PlacesService(mapGetDirection);
    //           var request = {
    //             location: new google.maps.LatLng(position.coords.latitude,position.coords.longitude), 
    //             radius: 500
    //           };
              service.search(request, function(results, status) {
                if (status  ==  'OK') {
                var index = 0;
                var radian = 3.141592653589793/180;
                var my_distance = 1000; 
                var R = 6371; // km
                for (var i = 0; i < results.length; i++){
                  var lat2 = results[i].geometry.location.lat();
                  var lon2 = results[i].geometry.location.lng(); 
                  var dLat = (lat2-position.coords.latitude) * radian;
                  var dLon = (lon2-position.coords.longitude) * radian;
                  var lat1 = position.coords.latitude * radian;
                  lat2 = lat2 * radian;
                  var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2); 
                  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                  var d = R * c;

                  if(d < my_distance) {
                    index = i;
                    my_distance = d;
                  }
                }
                  document.getElementById('origin').value = (results[index].vicinity) ? results[index].vicinity :'';  
                  getDirection();
                }
              });
            }else{
              var delimiter = (position.address && position.address.street !=  '' && position.address.city !=  '') ? ', ' : '';   
              var location= (position.address) ? (position.address.street + delimiter + position.address.city) : '';
              document.getElementById('origin').value =location;
              getDirection();
            }
          });
        } /*else {
          alert('Your browser does not support geolocation api');
        }*/
        
    <?php endif; ?>
        
//    if(typeof(readLocationsDetails) != 'undefined' && readLocationsDetails != null && readLocationsDetails.location != 'undefined' && readLocationsDetails.location != null && readLocationsDetails.location != '') {
//        
//        document.getElementById('origin').value = readLocationsDetails.location;
//        getDirection();
//    }
//    else {
//    else if(typeof(readLocationsDetails) == 'undefined' || readLocationsDetails == null || typeof(readLocationsDetails.latitude) == 'undefined' || typeof(readLocationsDetails.longitude) == 'undefined') {
//        var params = {
//            'detactLocation': 1,
//            'fieldName': 'origin',
//            //'noSendReq':1,
//            'locationmiles' : <?php //echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
//        };
//        en4.seaocore.locationBased.startReq(params);         
//        params.callBack=function(){
//            getDirection();
//        };  
//    }

  });
  var  toggoleDirectionContent= function(){
    document.getElementById('panel-content-area').fade();
    if($('collapse_hide').style.display == 'none'){
      $('collapse_show').style.display ='none';
      $('collapse_hide').style.display ='inline-block';
    }else{
      $('collapse_show').style.display ='inline-block';
      $('collapse_hide').style.display ='none';
    }
  };
  var getDirection=function(){
    document.getElementById("panel").innerHTML="<center><img src='application/modules/Seaocore/externals/images/loading.gif' alt='' /></center>";
    scrollGetDirection.update();
    var request = {
      origin: document.getElementById('origin').value, 
      destination: new google.maps.LatLng(<?php echo $this->checkin['latitude'] ? $this->checkin['latitude'] : 0; ?>,<?php echo  $this->checkin['longitude'] ? $this->checkin['longitude'] : 0; ?>),
      travelMode: google.maps.DirectionsTravelMode.DRIVING,
      unitSystem: <?php if(empty($this->userSettings)): ?> google.maps.UnitSystem.IMPERIAL <?php else: ?> google.maps.UnitSystem.METRIC <?php endif; ?>
    };

    directionsService.route(request, function(response, status) {
      document.getElementById("panel").empty();            
      if (status == google.maps.DirectionsStatus.OK) {       
        directionsDisplay.setDirections(response);  
        (function(){
          scrollGetDirection.update();
        }).delay(100);
      }else{  
        document.getElementById("panel").innerHTML="<ul class='form-errors'><li><?php echo $this->string()->escapeJavascript($this->translate('We could not calculate directions between the origin and destination.')) ?></li></ul>";
      }
    });    
  };
</script>