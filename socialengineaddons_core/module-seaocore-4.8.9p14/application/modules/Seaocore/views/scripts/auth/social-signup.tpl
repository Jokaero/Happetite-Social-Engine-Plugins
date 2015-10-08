<div class='layout_middle'>
  <?php 
   if (isset($this->form))
  echo $this->form->render($this) ?>
</div>
<script language="javascript">
function checkvalidEmail() {
var email = document.getElementById('email');
var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
if (!filter.test(email.value)) { 
alert(en4.core.language.translate('Please provide a valid email address'));
email.focus;
return false;
}
}
</script>