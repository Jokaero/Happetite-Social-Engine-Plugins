<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: web-cam-image.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/jpegcam/htdocs/webcam.js');
$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

if (!empty($this->tem_file_main_photo_name)) {
  ?>
  <script language="JavaScript">
    window.parent.document.getElementById('user_profile_photo').src = '<?php echo $this->tem_file_main_photo_name; ?>';
    if(window.parent.document.getElementById('profile_photo'))
      window.parent.document.getElementById('profile_photo').innerHTML = "<img src='<?php echo $this->tem_file_main_photo_name; ?>'>";
    parent.Smoothbox.close();
  </script>
<?php } ?>

<div class="webcam_box">
  <h3><?php echo $this->translate("Take a Profile Picture"); ?></h3>
  <div id="temp_upload_image">
    <!-- Configure a few settings -->
    <script language="JavaScript">
      var baseURL = "<?php echo $base_url; ?>";
      webcam.set_api_url(baseURL + '/seaocore/index/uploadcamimage?profile_photo=1');
      webcam.set_quality( 100 ); // JPEG quality (1 - 100)
      webcam.set_shutter_sound( false ); // play shutter click sound
    </script>

    <!-- Next, write the movie to the page at 320x240 -->
    <script language="JavaScript">
      document.write( webcam.get_html(600, 480) );
    </script>

    <form class="webcam_box_buttons">
      <button type=button value="Capture" id="webcam_capture" onClick="webcam.freeze()" style="display:inline"><?php echo $this->translate("Capture"); ?></button>
      <button type="button" value="Reset" id="webcam_reset" onClick="webcam.reset()" style="display:none"><?php echo $this->translate("Re-take"); ?></button>
      <button type=button value="Save" id="webcam_save" onClick="do_upload()" style="display:none"><?php echo $this->translate("Save"); ?></button>
      <?php echo $this->translate("or"); ?>&nbsp;
      <a href="javascript:void(0)" onClick="parent.Smoothbox.close()"><?php echo $this->translate("Cancel"); ?></a>
    </form>
  </div>

  <script language="JavaScript">
    webcam.set_hook( 'onComplete', 'my_completion' );
  
    function do_upload() {
      webcam.upload();
    }
  
    function my_completion(msg) {
      document.getElementById("temp_upload_image").innerHTML = "<div class='webcam_box_loading'><div class='siteusercoverphoto_main_container_lodding' style='margin-bottom:0px;margin-top:230px;'></div></div>"; 
      window.location.reload();
    }
  </script>

<style type="text/css">
	.webcam_box{
		padding:10px 0 0 10px;
	}
	html[dir="rtl"] .webcam_box{
		padding:10px 10px 0 0;
	}
	.webcam_box h3{
		margin-bottom:6px;
	}
	.webcam_box .webcam_box_buttons{
		margin-top:10px;
	}
	.webcam_box .webcam_box_buttons button{
		margin-right:5px;
	}
	html[dir="rtl"] .webcam_box .webcam_box_buttons button{
		margin-left:5px;
		margin-right:0px;
	}
	.webcam_box_loading{
		width:600px;
		text-align:center;
	}
	html[dir="rtl"] .webcam_box_loading{
		text-align:center;
	}	.siteusercoverphoto_main_container_lodding{background:url(./application/modules/Seaocore/externals/images/loading.gif);height:32px;width:32px;margin:40px auto;}
</style>