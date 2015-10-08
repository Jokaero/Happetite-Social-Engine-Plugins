<div id="bitly_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Bitly Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
	
  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="google-config">
			<li>
				<div class="steps">
          <a href="javascript:void(0);" onClick="guideline_show('bitlystep-1');"><?php echo $this->translate("Step 1");?></a>					
					<div id="bitlystep-1" style='display: none;'>
						<p>
						
						<?php echo $this->translate("Login to your bitly account here :");?> <a href="https://bitly.com/a/sign_in" target="_blank" style="color:#5BA1CD;">https://bitly.com/a/sign_in</a><br />

						</p>					
					</div>
				</div>
			</li>
      
      <li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('bitlystep-2');">Step 2</a>
					<div id="bitlystep-2" style='display: none;'>
						<p>
						
						<?php echo $this->translate("To start using Bitly APIs console go here :");?> <a href="https://bitly.com/a/your_api_key" target="_blank" style="color:#5BA1CD;">https://bitly.com/a/your_api_key</a><br />

						</p>						
					</div>
				</div>
			</li>
      
      <li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('bitlystep-3');">Step 3</a>
					<div id="bitlystep-3" style='display: none;'>
						<p>
						
						<?php echo $this->translate("You will be redirected to “Your bitly API Key” page. Here, you will see your bitly Username and bitly API Key. Copy these keys and paste them in your site's “bitly short URL” settings field.");?> <br />

						</p>
						<img src="https://lh6.googleusercontent.com/-J2DO74rgRCU/UbXiSNNwvmI/AAAAAAAAAY0/a8kVW8nwn0c/s625/1.1.jpg" alt="Step 1" />
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