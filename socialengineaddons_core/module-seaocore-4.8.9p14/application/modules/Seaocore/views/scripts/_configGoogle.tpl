<div id="google_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Google Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
	
  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="google-config">
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('googlestep-1');"><?php echo $this->translate("Step 1") ?></a>
					<div id="googlestep-1" style='display: none;'>
						<p>
						
						<?php echo $this->translate("1) To start using Google APIs console go here :");?> <a href="https://code.google.com/apis/console" target="_blank" style="color:#5BA1CD;">https://code.google.com/apis/console</a><br />
						
						 <?php echo $this->translate("2) Go to this URL for steps to configure Google Integration on your SocialEngine website : ");?><a href="https://www.youtube.com/watch?v=89HR_TjoPy4 
" target="_blank" style="color:#5BA1CD;">https://www.youtube.com/watch?v=89HR_TjoPy4</a><br/>					
						</p>					
					</div>
				</div>
			</li>

			<li>		
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('googlestep-2');"><?php echo $this->translate("Step 2") ?></a>
					<div id="googlestep-2" style='display: none;'>
            <h3><?php echo $this->translate(" Details to be filled while creating Google Application: ");?><br /></h3>
						<p>
						   	<?php echo  '<b>' . $this->translate("1) Authorized Redirec URLs") . ' => ' .  ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/seaocore/usercontacts/getgooglecontacts </b>' ;?><br />
							 
							<?php echo '<b>' . $this->translate("2) Authorized JavaScript Origins") . ' => ' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']. '</b>' ;?><br />
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