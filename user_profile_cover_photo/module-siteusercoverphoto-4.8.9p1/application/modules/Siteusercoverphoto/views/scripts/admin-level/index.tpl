<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<h2><?php echo $this->translate('User Profiles - Cover Photo, Banner & Site Branding Plugin'); ?></h2>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/siteusercoverphoto/level/index/id/'+level_id;;
  }
</script>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>



<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<style type="text/css">
#siteusercoverphoto_preview-wrapper{padding-top:0;margin-top:-15px;}
</style>
<script type="text/javascript">

function showPreview() {
  if($('show_default_preview').style.display == 'block') {
		$('show_default_preview').style.display='none';
  } else {
    $('show_default_preview').style.display='block';
  } 
}

function hidePreview() {
  $('show_default_preview').style.display='none';
}

</script>

<style type="text/css">
.admin_file_preview {
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
border-radius: 5px;
position: absolute;
padding: 10px;
background: #555;
margin-top: 5px;
-moz-box-shadow: 0px 0px 5px #aaa;
-webkit-box-shadow: 0px 0px 5px #aaa;
}
</style>