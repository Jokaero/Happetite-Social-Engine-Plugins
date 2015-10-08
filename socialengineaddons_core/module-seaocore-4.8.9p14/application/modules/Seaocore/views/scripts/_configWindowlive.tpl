<div id="windowlive_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Windows Live Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="guideline_4">
			<li>			
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-1');"><?php echo $this->translate("Step 1") ?></a>
					<div id="msnstep-1" style='display: none;'>
						<p><?php echo $this->translate("1) To register your application, go here :");?> <a href="http://msdn.microsoft.com/en-us/library/cc287659.aspx" target="_blank" style="color:#5BA1CD;">http://msdn.microsoft.com/en-us/library/cc287659.aspx.</a><br /><br />
              
              <?php echo $this->translate("2) Go to this URL for steps to configure Windowslive Integration on your SocialEngine website : ");?><a href="http://www.youtube.com/watch?v=20xuUG1RAeg 
" target="_blank" style="color:#5BA1CD;">http://www.youtube.com/watch?v=20xuUG1RAeg</a><br/>				
	</p>						
					</div>
				</div>
			</li>
			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-2');"><?php echo $this->translate("Step 2") ?></a>
					<div id="msnstep-2" style='display: none;'>
            <h3><?php echo $this->translate(" Details to be filled while creating Windowslive Application: ");?><br /></h3>
						<p>
							<?php echo $this->translate("d) <b>Your Redirect URLs:</b>") . ' => <b>' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/seaocore/auth/windowslive</b> in the 'Redirect URLs:' field in the 'API Settings' section.";?><br />
						</p>						
					</div>
				</div>
			</li>	
						
		

			<li>		
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-3');"><?php echo $this->translate("Step 3") ?></a>
					<div id="msnstep-3" style='display: none;'>
						<p>
								<?php echo $this->translate("Copy below 'Client ID' and 'Secret Key' and paste these values in your site's Windows Live contact importer settings fields.");?><br/>
						</p>
						<img src="https://lh5.googleusercontent.com/-KHI2dUeLOC0/T5kyye66n7I/AAAAAAAAALM/jq_RbguZX3w/s711/4.jpg" alt="Step 4" />
					</div>
				</div>
			</li>	
						
		
			
		
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