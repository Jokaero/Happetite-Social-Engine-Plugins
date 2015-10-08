<div id="twitter_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Twitter Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
	
  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="twitter-config">
      <li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('twitterstep-1');"><?php echo $this->translate("Step 1") ?></a>
					<div id="twitterstep-1" style='display: none;'>
						<p>
						
						<?php echo $this->translate("Link your mobile no. with your twitter profile if you have not linked it already. It is necessary for creating twitter application. For linking this go to this URL: ");?> <a href="https://twitter.com/settings/devices" target="_blank" style="color:#5BA1CD;">https://twitter.com/settings/devices</a><br />			
						
						</p>
						
					</div>
				</div>
			</li>
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('twitterstep-2');"><?php echo $this->translate("Step 2") ?></a>
					<div id="twitterstep-2" style='display: none;'>
						<p>
						
						<?php echo $this->translate("Go to this URL for steps to configure basic Twitter Integration on your SocialEngine website : ");?> <a href="http://www.youtube.com/watch?v=yzdKhboaPjM" target="_blank" style="color:#5BA1CD;">http://www.youtube.com/watch?v=yzdKhboaPjM</a><br />			
						
						</p>
						
					</div>
				</div>
			</li>
			
<!--			<li>	
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('twitterstep-3');"><?php echo $this->translate("Step 3") ?></a>
					<div id="twitterstep-3" style='display: none;'>
						<p>
							 <?php echo $this->translate("1) Go to this URL:");?><a href="https://dev.twitter.com/apps" target="_blank" style="color:#5BA1CD;">https://dev.twitter.com/apps</a><br />
							 <?php echo $this->translate("2) Now, select your application created in above step and go to its 'Settings' section.");?><<br />
							 <?php echo $this->translate("3) Upload a logo for your site from the 'Change Icon' field in the 'Application Icon' section.");?><<br />
							 <?php echo $this->translate("4) Select the option, 'Read, Write and Access direct messages' from the 'Access' field in the 'Application Type' section. ");?><<br />
						
						</p>
						<img src="https://lh3.googleusercontent.com/-RWtnTuAcasA/T6vEBJH7uQI/AAAAAAAAAMg/tNKTbLlUQo8/s621/twitter-app.jpg" alt="Step 2" />
					</div>
				</div>
			</li>	-->
  	</ul>
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
</div>