<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add.tpl 6590 2014-06-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
    <?php echo $this->form->render($this) ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>
    
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");?>    

<script type="text/javascript">
    var countrycities = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>';
    if(countrycities) { 
        var options = {
            types: ['(cities)'],
            componentRestrictions: {country: countrycities}
        };
    }
    else {
        var options = {
            types: ['(cities)']
        }; 
    }
    var autocomplete = new google.maps.places.Autocomplete($('location'), options);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        
        var address = '', country = '', state = '', zipcode = '', city = '';
        console.log(place.address_components);
        
        if (place.address_components) {
            var len_add = place.address_components.length;

            for (var i = 0; i < len_add; i++) {
                var types_location = place.address_components[i]['types'][0];                         
                
                if (types_location === 'country') {
                    country = place.address_components[i]['long_name'];
                } else if (types_location === 'administrative_area_level_1') {
                    state = place.address_components[i]['long_name'];
                } else if (types_location === 'administrative_area_level_2') {
                    city = place.address_components[i]['long_name'];
                } else if (types_location === 'postal_code' || types_location === 'zip_code') {
                    zipcode = place.address_components[i]['long_name'];
                } else if (types_location === 'street_address') {
                    if (address === '')                         
                        address = place.address_components[i]['long_name'];                                                 else
                        address = address + ',' + place.address_components[i]['long_name'];
                } else if (types_location === 'locality') {
                    if (address === '')
                        address = place.address_components[i]['long_name'];                                                 else
                        address = address + ',' + place.address_components[i]['long_name'];
                } else if (types_location === 'route') {
                    if (address === '')
                        address = place.address_components[i]['long_name'];                                                 else
                        address = address + ',' + place.address_components[i]['long_name'];
                } else if (types_location === 'sublocality') {
                    if (address === '')                                 
                        address = place.address_components[i]['long_name'];                                                 else
                        address = address + ',' + place.address_components[i]['long_name'];
                }
            }
        }        
                
        $('zipcode').value = zipcode;       
        $('city').value = city;        
        $('state').value = state;        
        $('country').value = country;        
        $('address').value = address;
        $('formatted_address').value = place.formatted_address;
        $('latitude').value = place.geometry.location.lat();
        $('longitude').value = place.geometry.location.lng();
    });
    
    function unsetLatLng() {
        $('latitude').value = 0;
        $('longitude').value = 0;
    }    
</script>        