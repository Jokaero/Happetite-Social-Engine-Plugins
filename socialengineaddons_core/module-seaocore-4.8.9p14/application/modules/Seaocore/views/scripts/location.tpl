<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


google.maps.event.addListener(autocompleteSECreateLocation, 'place_changed', function() {
	var place = autocompleteSECreateLocation.getPlace();
	if (!place.geometry) {
		return;
	}
	var address = '';
  var country = '';
  var state = '';
  var zipcode = '';
  var city = '';
  var len_add= '';
  var types_location = '';
	if (place.address_components) {
		len_add = place.address_components.length;

		for (var i = 0; i < len_add; i++) {
			types_location = place.address_components[i]['types'][0];
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
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'locality') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'room') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'route') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'sublocality') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'street_number') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'postal_town') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'postal_code') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'subpremise') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'neighborhood') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'post_box') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'park') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			} else if (types_location === 'natural_feature') {
				if (address === '')
					address = place.address_components[i]['long_name'];
				else
					address = address + ',' + place.address_components[i]['long_name'];
			}
		}
	}
	var locationParams = '{"location" :"' + document.getElementById('location').value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zipcode":"' + zipcode + '","city":"' + city + '"}';
  
	document.getElementById('locationParams').value = locationParams;
});
