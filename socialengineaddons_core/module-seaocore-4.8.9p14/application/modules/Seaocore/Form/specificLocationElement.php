<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: specificLocationElement.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0)) {
    $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
    $locationsArray = array();
    $locationsArray[0] = '';
    foreach ($locations as $location) {
        $locationsArray[$location->location] = $location->title;
    }

    $locationFieldName = isset($locationFieldName) ? $locationFieldName : 'location';
    $locationFieldType = (isset($locationFieldType) && $locationFieldType == 'Hidden')  ? 'Hidden' : 'Select';
    
    $options = array(
        'label' => 'Location',
        'multioptions' => $locationsArray,
        'onchange' => 'setSpecificLocationDatas(this.value);',
    );
    
    if(isset($elementOrder) && $locationFieldType == 'Hidden') {
        $options['order'] = $elementOrder;
    }
    
    $this->addElement("$locationFieldType", "$locationFieldName", $options); 
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
?>

<script type="text/javascript">
    if ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>' && '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>') {

        function setSpecificLocationDatas(specificLocation) {
            
            if(document.getElementById('submit')) {
                document.getElementById('submit').disabled = true;
            }
            
            var request = new Request.JSON({
                url: '<?php echo $view->url(array('module' => 'seaocore', 'controller' => 'location', 'action' => 'set-specific-location-datas'), "default", true); ?>',
                method: 'post',
                data: {
                    format: 'json',
                    specificLocation: specificLocation
                },
                //responseTree, responseElements, responseHTML, responseJavaScript
                onSuccess: function(responseJSON) {

                    var locationParams = '{"location" :"' + responseJSON.specificLocationDatas.location + '","latitude" :"' + responseJSON.specificLocationDatas.latitude + '","longitude":"' + responseJSON.specificLocationDatas.longitude + '","formatted_address":"' + responseJSON.specificLocationDatas.formatted_address + '","address":"' + responseJSON.specificLocationDatas.address + '","country":"' + responseJSON.specificLocationDatas.country + '","state":"' + responseJSON.specificLocationDatas.state + '","zip_code":"' + responseJSON.specificLocationDatas.zipcode + '","city":"' + responseJSON.specificLocationDatas.city + '"}';
                    if(document.getElementById('locationParams'))
                        document.getElementById('locationParams').value = locationParams;
                    
                    if(document.getElementById('submit')) {
                        document.getElementById('submit').disabled = false;
                    }
                }
            });
            request.send();
        }
    }
</script>    