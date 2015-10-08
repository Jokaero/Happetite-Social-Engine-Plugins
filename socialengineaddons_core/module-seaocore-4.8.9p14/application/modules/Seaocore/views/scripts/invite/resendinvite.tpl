<div id="showinviteform"> 
<?php echo $this->form->render($this) ?>

</div>
<script type="text/javascript">
var resendInvite = function () { 
 $('showinviteform').innerHTML = "<center><img src='application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center>"; 
 var nonsitemembers = new Array ();
 nonsitemembers [0] = '<?php echo $this->social_profileid;?>'; 
  var postData = {
		  'nonsitemembers' : nonsitemembers,
			'task' : 'join_network',
			'socialtype': '<?php echo $this->invite_service;?>'		
		};
    
    en4.core.request.send(new Request({
			url : en4.core.baseUrl + 'seaocore/usercontacts/invitetosite',
			method : 'post',
			data : postData,
			onSuccess : function(responseObject) { 
  			$('showinviteform').innerHTML = '<?php echo $this->translate("Your Invitation has been sent successfully.");?>';
				setTimeout('updateTable();', 1000 );
			}
		}));
  
}

var updateTable = function () {
 window.parent.searchMembers();
 window.parent.Smoothbox.close();
}



</script>