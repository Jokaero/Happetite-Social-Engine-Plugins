
 <?php $test_url = $_GET['redirect'];
 if(isset($_GET['community_title']) && !empty($_GET['community_title']))
     $community_title = $_GET['community_title'];
 else
     $community_title = 'MY COMMUNITY';
     
 ?>

 <div class="wrapper">
	<div class="container">
            <h1>Please click the button below to use your invite and signup on <?php echo $community_title; ?></h1>
		<form  class="form" action="<?php echo $test_url; ?>">
      <button type="button" id="login-button" onclick="redirect_to_site();">Continue</button>
		</form>
	</div>
</div>
<style type="text/css">
h1 {
 font-family: arial;
 font-weight: normal;
}
.wrapper{
	background: #e9eaed;
	position: absolute;
	left: 0;
	width: 100%;
	height: 100%;
	overflow: hidden;
}
.container{
	max-width: 740px;
	margin: 0 auto;
	padding: 175px 0 0;
	height: 250px;
	text-align: center;
}
#login-button {
  background: #3a5795;
  border-radius: 5px;
	border: 2px solid #ccc;
  font-family: Arial;
  color: #ffffff;
  font-size: 20px;
  padding: 5px 20px;
  text-decoration: none;
}
#login-button:hover {
  background: #314776;
}
form{
	padding: 20px 0;
	position: relative;
	z-index: 2;
}
 </style>
     
<!--  You are being redirected. If not <a href="<?php echo $test_url;?>" target="_top">click here</a>.-->
  <script>
      function redirect_to_site(){
          window.top.location.href = "<?php echo $test_url;?>";
      }
  </script>