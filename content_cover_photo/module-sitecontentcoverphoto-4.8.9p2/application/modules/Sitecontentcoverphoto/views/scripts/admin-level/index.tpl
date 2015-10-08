<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Content Profiles - Cover Photo, Banner & Site Branding Plugin'); ?></h2>

<script type="text/javascript">
  var resource_type = '<?php echo $this->resource_type; ?>';
  
  var fetchLevelSettings=function(level_id){
    var listingtype_id= '<?php echo $this->listingtype_id; ?>';
    if(listingtype_id != 0 && resource_type == 'sitereview_listing')  {
      window.location.href= en4.core.baseUrl+'admin/sitecontentcoverphoto/level/index/id/'+level_id+'/resource_type/'+'<?php echo $this->resource_type; ?>'+'/listingtype_id/'+listingtype_id;
    } else {
      window.location.href= en4.core.baseUrl+'admin/sitecontentcoverphoto/level/index/id/'+level_id+'/resource_type/'+'<?php echo $this->resource_type; ?>';
    }
  }

  var fetchModuleName =function(value, listingtype_id){

    
    if(listingtype_id != 0 && value == 'sitereview_listing') {
      window.location.href= en4.core.baseUrl+'admin/sitecontentcoverphoto/level/index/id/'+<?php echo $this->level_id; ?>+'/resource_type/'+value+'/listingtype_id/'+listingtype_id;
    } else {
      window.location.href= en4.core.baseUrl+'admin/sitecontentcoverphoto/level/index/id/'+<?php echo $this->level_id; ?>+'/resource_type/'+value;
    }
  }

  var fetchListingtypeLevelSettings=function(level_id){
    var listingtype_id = 1;
    if($('listingtype_id')) {
      listingtype_id = $('listingtype_id').value;
    }
    window.location.href= en4.core.baseUrl+'admin/sitecontentcoverphoto/level/index/id/'+<?php echo $this->level_id; ?>+'/listingtype_id/'+listingtype_id+'/resource_type/'+'<?php echo $this->resource_type; ?>';
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
  #sitecontentcoverphoto_preview-wrapper{padding-top:0;margin-top:-15px;}
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