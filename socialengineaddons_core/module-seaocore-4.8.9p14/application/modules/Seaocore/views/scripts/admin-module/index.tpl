<?php
if( empty($this->unlicense_modules_array) ) {
?>
<?php
	if( !empty($this->close_msg) ) {?>
  <div class='settings seaocore_installation_form'>
		<div class="global_form">
		  <div>
		    <div>
		    	<ul class="form-sucess">
		    		<li>
							<?php
								if($this->calling_from == 'install') {
									echo 'Your license key has been accepted. Please click on the "Continue" button below to install this plugin on your site. After that, you will see a message window like in the image below. Click on the "Install Package" button in it to continue installation.<br /><br />After successful installation of this plugin, you will need to go to the Admin section of this plugin, and activate the plugin over there.<br /><br /><center><img src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/install_2.1.jpg" /></center>';
								} else if( $this->calling_from == 'queary' ) {
									echo 'Your license key has been accepted. Please click on the "Continue" button below to proceed to the next step of plugin installation on your site.<br /><br />After successful installation of this plugin, you will need to go to the Admin section of this plugin, and activate the plugin over there.';
								}
							?> 
						</li>
					</ul>
					<br /><center><button onclick="smoothboxclose();"><?php echo $this->translate("Continue") ?></button> </center>
				</div>
			</div>
		</div>	
  </div>
				<?php }
				else {
					?><div class="clr">
						<div class='settings seaocore_installation_form'>
							<?php echo $this->form->render($this); ?>
						</div>
					</div><?php
				}
			?>
			<script type="text/javascript">
			 function smoothboxclose () {
			  parent.window.location.reload(); 
			  parent.Smoothbox.close () ;	
			 }
			</script>
			<?php }else { ?>
		<style type="text/css">
		.global_form_1 {
			clear:both;
			overflow:hidden;
		}
		.global_form_1 > div {
			-moz-border-radius:7px 7px 7px 7px;
			background-color:#E9F4FA;
			float:left;
			max-width:500px;
			overflow:hidden;
			padding:10px;
		}
		.global_form_1 > div > div {
			background:none repeat scroll 0 0 #FFFFFF;
			border:1px solid #D7E8F1;
			overflow:hidden;
			padding:20px;
		}
		.global_form_1 .form-sucess {
			margin-bottom:10px;
		}
		.global_form_1 .form-sucess li {
			-moz-border-radius:4px 4px 4px 4px;
			background:#C8E4B6;
			border:2px solid #95b780;
			color:#666666;
			font-weight:bold;
			padding:0.5em 0.8em;
		}
table td
{
	border-bottom:1px solid #f1f1f1; 
	padding:5px;
	vertical-align:top;
}

 .button{
  +rounded(3px);
  padding: 5px;
  font-weight: bold;
  border: none;
  background-color: #619dbe;
  border: 1px solid #50809b;
  color: #fff;
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/buttonbg.png);
  background-repeat: repeat-x;
  background-position: 0px 1px;
  font-family: tahoma, verdana, arial, sans-serif;
	font-size:12px;
}
.button:hover
{
  background-color: #7eb6d5;
  cursor: pointer;
	text-decoration:none;
}
ul.form-not > li {
	background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/error.png);
	background-repeat:no-repeat;
	padding-left:30px;
	background-position:8px 1px;
}
</style>
				<div class="global_form_1">
					<div>
						<div>
							<div><ul class="form-not"><li>
							<?php echo '<b style="color:red;">Attention!</b><br/>'; ?><br />
							<?php echo $this->translate('Unlicensed plugin(s) were detected on your site. You will not be able to install this plugin on your site till one of the below is done:<br /><br />1) Delete all unlicensed copies of SocialEngineAddOns plugins from your site.<br />2) Purchase valid plugin license(s) and enter the license key(s) in the Global Settings of those plugin(s) on your site.<br /><br />After completing any of these steps, you can try again to install this plugin from the "Manage Plugins" section. For any questions, please <a href="http://www.socialengineaddons.com/contact-us" target="_blank">contact SocialEngineAddOns</a>.');
							echo $this->translate('<br /><br />You are using unlicensed copies of the following plugin(s) on your site:');?>
							<table>
								<?php foreach( $this->unlicense_modules_array as $modules_name ) {
									echo '<tr><td style="width:90%;font-weight:bold;">' . ucfirst( $modules_name['product_title'] ) . '</td><td><a href="'.$modules_name['product_link'].'" class="seaocore_type_seaocore" target="_blank">' . $this->translate('View') . '</a></td></tr>'; 
								} ?>
							</table><br/><br/>
							<?php echo '<a href="' . $this->base_url . '/install/manage" target="_parent" class="button">Manage Packages</a>'; ?>
							<?php echo '<a href="http://www.socialengineaddons.com/contact-us" target="_blank" class="button">Contact SocialEngineAddOns</a>'; ?>
							<?php echo '<a href="javascript void(0);" onclick="parent.Smoothbox.close () ;" class="button"> '. $this->translate("Close") . ' </a>'; ?>
						</li></ul></div></div>
					</div>
				</div>
				<?php
			} ?>
		