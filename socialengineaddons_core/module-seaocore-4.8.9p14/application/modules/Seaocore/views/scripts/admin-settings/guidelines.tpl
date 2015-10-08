<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: guidelines.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'lightbox'), 'admin_default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/back.png);padding-left:23px;"><?php echo $this->translate("Back to Advanced Lightbox Viewer Settings"); ?></a>

  <div class='' style="margin-top:15px;">
    <h3><?php echo $this->translate("Guidelines for integrating a new module to display photos belonging to it in Advanced Lightbox Viewer") ?></h3>
    <p><?php echo $this->translate("Please follow the steps below to manually integrate a new module to enable photos belonging to it to be displayed in the Advanced Lightbox Viewer. Even in this case, the photo thumbnails from this plugin should be having SocialEngine's core 'thumbs_photo' CSS class applied on them. After you follow these guidelines for a module / plugin, it will be listed in the 'Modules / Plugins' field in the Advanced Lightbox Viewer Settings.");?>
    <br />
    <?php echo $this->translate("You will need to follow these guidelines for these 2 cases:");?><br />
    <?php echo $this->translate('1) If you have chosen "No" for the field "Modules / Plugins" in Advanced Lightbox Viewer Settings and want to specifically enable Lightbox Viewer for photo thumbnails from a desired 3rd party plugin.');?><br />
    <?php echo $this->translate('2) If you have selected "Yes" for the field "Modules / Plugins" in Advanced Lightbox Viewer Settings and want the Edit and Delete links for photos from the desired plugin to be visible in the Lightbox Viewer.');?><br />
    </p><br />
    <div class="admin_seaocore_guidelines_wrapper">
      <ul class="admin_seaocore_guidelines">
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-1');"><?php echo $this->translate("Step 1") ?></a>              
            <div id="step-1" style='display: none;'>
              <p>
                <?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/Form/Admin/Lightbox.php'."); ?><br />
                <?php echo $this->translate('b) Go to the line around line number 47 starting with <b style="font-weight:bold;">$includeThirdPartyModules</b> .'); ?><br />
                <?php echo $this->translate("c) Add an entry in this line, separated by comma (,), for the desired module / plugin, similar to the existing entry for Groups: <b style='font-weight:bold;'>'group' => 'Group'</b> ."); ?><br />
              </p>								
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-2');"><?php echo $this->translate("Step 2") ?></a>              
            <div id="step-2" style='display: none;'>
              <p>
                <?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/Api/Core.php'."); ?><br />
                <?php echo $this->translate('b) Go to the line around line number 118 having: <b style="font-weight:bold;">define("SEA_EVENT_LIGHTBOX", in_array("event", $showLightboxOptionDisplay));</b> .'); ?><br />
                <?php echo $this->translate('c) Add a new entry below this line for the desired module / plugin, similar to the existing entry for Events: <b style="font-weight:bold;">define("SEA_EVENT_LIGHTBOX", in_array("event", $showLightboxOptionDisplay));</b> .') ?><br />
								<?php echo $this->translate('d) Go to the line around line number 121 having: <b style="font-weight:bold;">define("SEA_EVENT_LIGHTBOX", 0);</b> .'); ?><br />          
                <?php echo $this->translate('e) Add a new entry below this line for the desired module / plugin, similar to the existing entry for Events: <b style="font-weight:bold;">define("SEA_EVENT_LIGHTBOX", 0);</b> .');?>
              </p>								
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-3');"><?php echo $this->translate("Step 3") ?></a>              
            <div id="step-3" style='display: none;'>
              <p>
                <?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/widgets/seaocores-lightbox/controller.php'."); ?><br />
                <?php echo $this->translate('b) Go to the line around line number 28 having: <b style="font-weight:bold;">elseif (SITEALBUM_ENABLED && SEA_EVENT_LIGHTBOX && $module == "event") { $flag = 1; }</b> .'); ?><br />
                <?php echo $this->translate('c) Add a new entry below this line for the desired module / plugin, similar to the existing entry for Events: <b style="font-weight:bold;">elseif (SITEALBUM_ENABLED && SEA_EVENT_LIGHTBOX && $module == "event") { $flag = 1; }</b> .'); ?><br />
              </p>								
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-4');"><?php echo $this->translate("Step 4") ?></a>              
            <div id="step-4" style='display: none;'>
              <p>
                <?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/Api/PhotoInLightbox.php'."); ?><br />
                <?php echo $this->translate('b) Go to the line around line number 185 containing: <b style="font-weight:bold;">$resourceType == "event_photo"</b>.'); ?><br />
                <?php echo $this->translate('c) Add an entry in this line, separated by double-pipe (||), for the desired module / plugin, similar to the existing entry for Events: <b style="font-weight:bold;">|| $resourceType == "event_photo"</b> .'); ?><br />
              </p>								
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-5');"><?php echo $this->translate("Step 5") ?></a>              
            <div id="step-5" style='display: none;'>
              <p>
                <?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/controllers/PhotoController.php'."); ?><br />
                <?php echo $this->translate('b) Go to the line around line number 116 having: <b style="font-weight:bold;">case "event": .</b>');?><br />
                <?php echo $this->translate('c) Copy the code from this line to around line number 137 having: <b style="font-weight:bold;">break; .</b>');?><br />
                <?php echo $this->translate('d) Paste this code at around line number 138, i.e., after the line mentioned in the previous point, after modifying the code according to the desired module / plugin. I.e., add the code for the desired module / plugin, similar to the code for Events:<br /><b style="font-weight:bold;">case "event"<br /><br />//CHECKING THE PRIVACY IF EVENT HAVE PRIVACY THEN PHOTOS WILL BE SHOWN IN THE LIGHTBOX<br />if (!$this->_helper->requireAuth()->setAuthParams($photo->getEvent(), null, "view")->isValid()) {<br />return;<br />}<br /><br />//GET TAG,UNTAG,EDIT,DELETE PRIVACY<br />$this->view->canTag = $this->view->canDelete = $this->view->canUntagGlobal = $this->view->canEdit = $photo->canEdit(Engine_Api::_()->user()->getViewer());<br /><br />//GET EDIT AND DELETE ROUTE FROM THE MANIFEST<br />$this->view->deleteRoute = $this->view->editRoute = $this->_module_name . "_extended";<br /><br />//GET DELETE ACTION<br />$this->view->deleteAction = "delete";<br /><br />//GET EDIT 
ACTION<br />$this->view->editAction = "edit";<br /><br />//GET COMMENT<br />$this->view->canComment = 1;<br />break</b>;');?><br />
              </p>								
            </div>
          </div>
        </li>

        <?php 
          $seaocoremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('seaocore');
          $seaocoremoduleversion = $seaocoremodule->version; 
        ?>
        <?php if($seaocoremoduleversion < '4.2.5p1') :?>
					<li>	
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('step-6');"><?php echo $this->translate("Step 6") ?></a>              
							<div id="step-6" style='display: none;'>
								<p>
									<?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/externals/scripts/lightBox.js'."); ?><br />
	<?php echo $this->translate("b) Go to the line around line number 37 having: <b style='font-weight:bold;'>if($$('.feed_attachment_event_photo')) { </b>.");?><br />
	<?php echo $this->translate("c) Copy the code of this line and the 8 lines following it, i.e., till around line number 45 having the closing curly bracket: } .");?><br />
	<?php echo $this->translate("d) Paste this code at around line number 46, i.e., after the line mentioned in the previous point, after modifying the code according to the desired module / plugin. I.e., add the code for the desired module / plugin, similar to the code for Events: <br /><b style='font-weight:bold;'>if($$('.feed_attachment_event_photo')) {<br />//DISPLAY ACTIVITY FEED IMAGES IN THE LIGHTBOX FOR THE EVENT <br />if($$('.feed_attachment_event_photo')) {<br />$$('.feed_attachment_event_photo').each(function(el){<br />el.getElement('.thumb_normal').removeEvents('click').addEvent('click', function(e) {<br />e.stop();<br />href = openLightboxforActivityFeed(el);<br />openSeaocoreLightBox(href);<br />});<br />});<br />}</b>;")?>

	<br />
								</p>								
							</div>
						</div>
					</li>
        <?php endif;?>
        <?php if($seaocoremoduleversion >= '4.2.5p1') :?>
					<li>	
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('step-6');"><?php echo $this->translate("Step 6") ?></a>              
							<div id="step-6" style='display: none;'>
								<p>
									<?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/externals/scripts/lightBox.js'."); ?><br />
									<?php echo $this->translate('b) Go to the line around line number 39 having: <b style="font-weight:bold;">if( activityfeed_lightbox != 0 ) {</b>');?><br />
									<?php echo $this->translate('c) Add an entry in the function addSEAOPhotoOpenEventLightbox, separated by comma (,), for the desired module / plugin, similar to the existing entry for Events: , "feed_attachment_event_photo", .');?>
								<br />
								</p>								
							</div>
						</div>
					</li>

					<li>	
						<div class="steps">
							<a href="javascript:void(0);" onClick="guideline_show('step-7');"><?php echo $this->translate("Step 7") ?></a>              
							<div id="step-7" style='display: none;'>
								<p>
									<?php echo $this->translate("a) Open the file: 'application/modules/Seaocore/externals/scripts/lightbox/fixWidthLightBox.js'."); ?><br />
									<?php echo $this->translate('b) Go to the line around line number 36 having: <b style="font-weight:bold;">if( activityfeed_lightbox != 0 ) {</b>');?><br />
									<?php echo $this->translate('c) Add an entry in the function addSEAOPhotoOpenEvent, separated by comma (,), for the desired module / plugin, similar to the existing entry for Events: , "feed_attachment_event_photo", .');?>
								<br />
								</p>								
							</div>
						</div>
					</li>

        <?php endif;?>

      </ul>        
    </div>
  </div> 
  
  <div class='' style="margin-top:15px;" id="high-resolution-and-large-size-photos">
    <h3><?php echo $this->translate("Guidelines for uploading high resolution and large size photos") ?></h3><br />
    <p><?php echo $this->translate("1. To upload high resolution photos, follow the steps below :"); ?>
      </p><br />
    <div class="admin_seaocore_files_wrapper" >
      <ul class="admin_seaocore_guidelines">
        <li>	
          <div class="steps">
            <a></a>
            <div id="guidline2-step-1">
              <p>
                <?php echo $this->translate("a) Open the file: <b>'application/modules/[MODULE_NAME]/Model/Photo.php'</b>. (Like for Photo Albums Plugin => <b>\"application/modules/Album/Model/Photo.php\"</b>)") ?>
              </p>            
              <p>
                <?php echo $this->translate('b) Search for the line having: <b>->write($mainPath)</b>.') ?>
              </p>            
              <p>
                <?php echo $this->translate("c)  Now, in the function: <b>'->resize(x,y)'</b>,  just before the above code, enter the values for the height (x) and width (y) for the photos you want to be uploaded on your site. (Note : It is recommended to set the maximum dimension of the photos to '->resize(1600,1600)'.)") ?>
              </p>            
              <p>
                <?php echo $this->translate("d) Save and close the file.") ?>
              </p>
            </div>
          </div>
        </li>
      </ul>
    </div>
      <br /><br />
    <p><?php echo $this->translate("2. To upload large size photos on your site, please follow the below steps : "); ?>
      </p><br />
    <div class="admin_seaocore_guidelines_wrapper" >
      <ul class="admin_seaocore_guidelines">
        <li>	
          <div class="steps">
              <p>
                <?php echo $this->translate("The upload size is bound by the value of PHP directive, “upload_max_filesize” in your server’s “php.ini” file. By default the value for this directive is set to 2MB. To increase the value for this directive, follow below mentioned guidelines in two cases:") ?>
              </p>       
          </div>
        </li>
        <li>	
          <div class="steps">
            <p>
              <b><?php echo $this->translate("Case 1:") ?></b>
            </p>
            <p>
            <?php echo $this->translate("To increase the upload limit in php.ini file:") ?></p>
            <a></a>
            <div id="guidline2-step-1">
              <?php echo $this->translate(nl2br("a) Go to Server Information section from the <b>“Stats”</b> Dropdown in the Admin Panel.
b) Now, search for <b>“upload_max_filesize”</b> directive. 
c) If the <b>“Local Value”</b> for this directive is less than the value you want to set, then please contact your hosting provider and have them increase the value of this directive for you.
")) ?>
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <p>
              <b><?php echo $this->translate("Case 2:") ?></b>
            </p>
            <p>
            <?php echo $this->translate("If you are not able to change the <b>“Local Value”</b> for <b>“upload_max_filesize”</b> directive in <b>php.ini</b> file, then open <b>.htaccess</b> file in your SocialEngine root directory and follow below steps:
") ?></p>
            <a></a>
            <div id="guidline2-step-1">
              <?php echo $this->translate(nl2br("a) Paste the code: <b>\"php_value upload_max_filesize xM\"</b>, at the end of the file where the value of x can be in the range of 1 MB to 10 MB. (Note: It is recommended to enter the maximum value of x to 6 MB.)
b) Save and close the file.
")) ?>
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <p>
              <?php echo $this->translate("For more information, please refer to SocialEngine’s ") ?>
              <?php echo '<a href="http://www.socialengine.net/support/documentation/article?q=219&question=Admin-Panel---Stats--Server-Information" target="_blank"> KB article</a>.'; ?>
            </p>
          </div>
        </li>
      </ul>
    </div>
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

