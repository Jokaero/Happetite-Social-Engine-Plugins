<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: guidelines.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'settings', "action" => "map"), 'admin_default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/back.png);padding-left:23px;"><?php echo $this->translate("Back to Locations & Maps Settings"); ?></a>

  <div class='' style="margin-top:15px;">
    <h3><?php echo $this->translate("Guidelines for configuring Google Places API key") ?></h3>
    <p><?php echo $this->translate("Below, you can find the guidelines to configure your Google Places API key:");?>
    <br /><br />
    <div class="admin_seaocore_guidelines_wrapper">
      <ul class="admin_seaocore_guidelines">

        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-1');"><?php echo $this->translate("Step 1") ?></a>              
            <div id="step-1" style='display: none;'>
              <p>
                 
						   <?php echo $this->translate("A) Go to this URL for steps to configure Google Places API key for your SocialEngine website:");?> <a href="https://www.youtube.com/watch?v=PkVWu7fEtBw" target="_blank" style="color:#5BA1CD;">https://www.youtube.com/watch?v=PkVWu7fEtBw</a><br /><br /> 
               <?php echo $this->translate("B) To start using Google APIs console go here :");?> <a href="https://code.google.com/apis/console" target="_blank" style="color:#5BA1CD;">https://code.google.com/apis/console</a>
              </p>		
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-2');"><?php echo $this->translate("Step 2") ?></a>              
            <div id="step-2" style='display: none;'>
              <p>
                <?php echo $this->translate('These are the following Google API Services you have to enabled for your SocialEngine website:');?>
              </p>	
               <ul>
                   <li>Google Maps JavaScript API v3</li>
                   <li>Geocoding API</li>
                   <li>Places API</li>
               <ul>                  
          </div>
        </li>          
          
<!--        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-1');"><?php echo $this->translate("Step 1") ?></a>              
            <div id="step-1" style='display: none;'>
              <p>
						   <?php echo $this->translate("To start using Google APIs console go here :");?> <a href="https://code.google.com/apis/console" target="_blank" style="color:#5BA1CD;">https://code.google.com/apis/console</a><br />
						   <?php echo $this->translate("Login to your google account and click on the 'Create project...' to select API access.");?><br />
              </p>		
              <img src="https://lh4.googleusercontent.com/-0C-ZCp981Vg/UDJVEnyL3bI/AAAAAAAAANk/gULOVVcDS-E/s374/create_project.jpg"  />
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-2');"><?php echo $this->translate("Step 2") ?></a>              
            <div id="step-2" style='display: none;'>
              <p>
                <?php echo $this->translate('Now click on "Services" tab from the left menu.');?>
              </p>	
              <img src="https://lh4.googleusercontent.com/-e9UXcJWCA5M/UDJVFxEKV8I/AAAAAAAAAN0/34Y3V9hIk1M/s206/services.jpg" alt="Step 2" />	</div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-3');"><?php echo $this->translate("Step 3") ?></a>              
            <div id="step-3" style='display: none;'>
              <p>
                <?php echo $this->translate('Now, search for the field "Google Maps API v3" and activate it by clicking on "OFF" option available in the button next to it. After clicking on this button a popup will open where you will be asked to agree to the “Google Maps/Google Earth APIs Terms of Service”. Click on the checkbox to agree to the terms and click on “Accept” button.');?>
              </p>		
              <img src="https://lh4.googleusercontent.com/-KordKeoEXGo/UDJVEVDoh2I/AAAAAAAAANY/COh7v4d7D3I/s802/google_map_api_v3.jpg" alt="Step 3" />
            </div>
          </div>
        </li>
				<li>	
					<div class="steps">
						<a href="javascript:void(0);" onClick="guideline_show('step-4');"><?php echo $this->translate("Step 4") ?></a>              
						<div id="step-4" style='display: none;'>
							<p>
								<?php echo $this->translate('Now, search for the field "Places API" and activate it by clicking on "OFF" option available in the button next to it. After activating it, a “Register Your Organization ” popup will open where you will be asked to enter "Company or organization" and your "Website URL". So enter your website URL and Company or organization name and then click on "Submit" button to submit the settings.');?>
							</p>			
							<img src="https://lh3.googleusercontent.com/-YIqCornPyNk/UDJVFxBpGII/AAAAAAAAANs/NaBseOhaReo/s776/place_api.jpg" alt="Step 4" />	
						</div>
					</div>
				</li>
				<li>	
					<div class="steps">
						<a href="javascript:void(0);" onClick="guideline_show('step-5');"><?php echo $this->translate("Step 5") ?></a>              
						<div id="step-5" style='display: none;'>
							<p>
								<?php echo $this->translate('Now, search for the field "Geocoding API" and activate it by clicking on "OFF" option available in the button next to it. After clicking on this button a popup will open where you will be asked to agree to the “Google Maps/Google Earth APIs Terms of Service”. Click on the checkbox to agree to the terms and click on “Accept” button.');?>
							</p>			
							<img src="https://lh3.googleusercontent.com/-PcxX_2L1mEA/U0UczapAkWI/AAAAAAAAA2g/aPSBewZFHFU/w794-h139-no/geocode_api.png" alt="Step 5" />	
						</div>
					</div>
				</li>        
				<li>	
					<div class="steps">
						<a href="javascript:void(0);" onClick="guideline_show('step-6');"><?php echo $this->translate("Step 6") ?></a>              
						<div id="step-6" style='display: none;'>
							<p>
								<?php echo $this->translate('Go to the "API Access" tab from the left menu and copy the generated API key from the Simple API Access section. Now, paste this key in the "Google Places API" field available in the "Global Settings" section of this plugin.');?>
							</p>	
							<img src="https://lh4.googleusercontent.com/-ThA6Xj4rSRc/UDJVEoJ73jI/AAAAAAAAANc/qfqDeaDW8qk/s452/generetadkey.jpg" />
						</div>
					</div>
				</li>-->
			</ul>
    </div>
</div>

<script type="text/javascript">
  function guideline_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>