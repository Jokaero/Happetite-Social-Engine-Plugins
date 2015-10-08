<div id="yahoo_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Yahoo Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="guideline_2">
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('yahoostep-1');"><?php echo $this->translate("Step 1") ?></a>
					<div id="yahoostep-1" style='display: none;'>
						<p>
						<?php echo $this->translate("1) Go here to register your application:");?> <a href="https://developer.apps.yahoo.com/projects" target="_blank" style="color:#5BA1CD;">https://developer.apps.yahoo.com/projects</a><br />
						<?php echo $this->translate("2) Go to this URL for steps to configure Yahoo Integration on your SocialEngine website : ");?><a href="http://www.youtube.com/watch?v=j4AqXCHm5Ro 
" target="_blank" style="color:#5BA1CD;">http://www.youtube.com/watch?v=j4AqXCHm5Ro</a><br/>						
					</div>
				</div>
			</li>			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('yahoostep-2');"><?php echo $this->translate("Step 2") ?></a>
					<div id="yahoostep-2" style='display: none;'>
						<p><?php echo $this->translate("Copy below 'Consumer Key (API Key)' and 'Consumer Secret (Shared Secret Key)' and paste these values in your site's Yahoo contact importer settings fields.");?><br /></p>
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/yahst3.gif" alt="Step 2" />
					</div>
				</div>
			</li>	
			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('yahoostep-3');"><?php echo $this->translate("Step 3") ?></a>
					<div id="yahoostep-3" style='display: none;'>
						<p><?php echo $this->translate("Open the hidden file '.htaccess', from your root directory and search for the lines given below:");?></p>
						<div class="code_box">
						  <b>
								<?php echo $this->translate("&lt;IfModule mod_rewrite.c&gt;<br /><br />
	
	                  Options +FollowSymLinks<br /><br />
	                
	                  RewriteEngine On");?><br /><br />
	             </b>      
	           </div><br />
	           <p>Now replace 'yourdomain.com' with your site's domain name in the below lines, and insert them just after the above mentioned lines.</p>
	          <div  class="code_box">
						  <b>
								<?php echo $this->translate("<br /><br />RewriteCond %{HTTP_HOST} ^yourdomain.com [NC]<br /><br />  
	              RewriteRule ^(.*)$ http://www.yourdomain.com/$1 [L,R=301]");?><br /><br />  
	             </b>
	              
	          </div>
						</p>
						
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