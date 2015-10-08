<?php ?>
<style type="text/css">
body{
	padding:0px;
	margin:0px;
}
#sugg-aol-signin{
	width:100%;
	float:left;
}
#sugg-aol-signin .top{
	float:left;
	height:77px;
	width:100%;
	background:#eaedf2;
	margin-bottom:15px;
}
#sugg-aol-signin .top .logo{
	margin:13px 0 0 30px;
}
#sugg-aol-signin #signin-form {
	height:auto;
	width:auto;
	margin:15px;
	border:2px solid #dadada;
	clear:both;
}
#sugg-aol-signin #signin-form h3 {
	color:#333333;
	font-family:arial,Helvetica,sans-serif;
	font-size:18px;
	font-weight:bold;
	margin-top:0;
}
#sugg-aol-signin #signin-form label{
	color:#555555;
	font-family:Helvetica,arial,sans-serif;
	font-size:14px;
	font-weight:normal;
	padding-left:0;
}
#sugg-aol-signin #signin-form input{
	border:1px solid #D0D0D0;
	color:#202020;
	font-family:arial,sans-serif;
	font-size:12px;
	height:22px;
	line-height:22px;
	margin:0 0 5px;
	padding:0 4px 0;
	width:225px;
}
#sugg-aol-signin #signin-form button {
	background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/aol-signin-btn.gif);
	background-repeat:no-repeat;
  background-position:top;
	border:medium none;
	filter:none;
	font-family:Helvetica,Arial,sans-serif;
	font-size:18px;
	font-weight:bold;
	height:35px;
	margin:0 2px;
	width:122px;
	color:#fff;
	cursor:pointer;
}
#sugg-aol-signin #signin-form button:hover{
	background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/aol-signin-btn.gif);
	background-position:bottom;

} 
#sugg-aol-signin #signin-form ul,
#sugg-aol-signin #signin-form ul li{
	margin:0px;
	padding:0px;
	list-style:none;
}
#sugg-aol-signin #signin-form ul li{
	color:#C81A1A;
	font-size:11px;
	margin-bottom:10px;
}
</style>
<div id="sugg-aol-signin">
	<div class="top">
		<?php $image_url = $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/aol.png"; ?>
		<img src=<?php echo $image_url;?> alt="" class="logo" />
	</div>
	<div id="signin-form">
	<?php if (!empty($this->error)) {  echo $this->error; } ?>
	<div style="padding:15px 0 0 20px;"><?php echo $this->form->render($this) ?></div>
	</div>

</div>