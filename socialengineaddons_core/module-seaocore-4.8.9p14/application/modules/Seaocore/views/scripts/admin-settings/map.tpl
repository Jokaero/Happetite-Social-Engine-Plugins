<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upgrade.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
 <?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
?>
<script type="text/javascript">
	window.addEvent('domready', function() {
		new google.maps.places.Autocomplete(document.getElementById('seaocore_locationdefault'));
	});
</script>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<h2>
  <?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='seaocore_admin_tabs'>
      <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
      ?>
    </div>
<?php endif; ?>

<?php if(Engine_Api::_()->seaocore()->getLocationsTabs()): ?>
    <div class='tabs'>
      <ul class="navigation">
        <li class="active">
        <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'seaocore','controller'=>'settings','action'=>'map'), $this->translate('General Settings'), array())
        ?>
        </li>
        <li>
        <?php
          echo $this->htmlLink(array('route'=>'admin_default','module'=>'seaocore','controller'=>'geo-locations','action'=>'manage'), $this->translate('Manage Locations'), array())
        ?>
        </li>			
      </ul>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
	<a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map-guidelines'), 'admin_default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/admin/help.gif);padding-left:23px;"><?php echo $this->translate("Guidelines for configuring Google Places API key"); ?></a><br /><br />
</div>

<div class="seaocore_settings_form">
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
</div>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
    function showLocationSpecific(value) {
        if ($('seaocore_locationdefaultmiles-wrapper')) {
            if (value == 1) {
                $('seaocore_locationdefault-wrapper').style.display = 'none';
                $('seaocore_locationdefaultspecific-wrapper').style.display = 'block';
                $('seaocore_locationspecificcontent-wrapper').style.display = 'block';
                $('seaocore_locationspecificorder-wrapper').style.display = 'block';
            } else {
                $('seaocore_locationdefault-wrapper').style.display = 'block';
                $('seaocore_locationdefaultspecific-wrapper').style.display = 'none';
                $('seaocore_locationspecificcontent-wrapper').style.display = 'none';
                $('seaocore_locationspecificorder-wrapper').style.display = 'none';
            }
        }        
    }
    window.addEvent('domready', function() {
        showLocationSpecific('<?php echo $settings->getSetting('seaocore.locationspecific', 0) ?>');
    });
    
    function setLocationDefault(value) {
        if(value == 0) {value = '';}
        $('seaocore_locationdefault').value = value;
    }    
</script>    