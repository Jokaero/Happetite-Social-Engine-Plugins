<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _upgrade_messages.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$modArray = array("sitepage", "sitepageform", "sitepagealbum", "sitepagebadge", "sitepagedocument", "sitepageevent", "sitepageintegration", "sitepageinvite", "sitepagelikebox", "sitepagemember", "sitepagemusic", "sitepagenote", "sitepageoffer", "sitepagepoll", "sitepagereview", "sitepagevideo", "sitebusiness", "sitebusinessform", "sitebusinessalbum", "sitebusinessbadge", "sitebusinessdocument", "sitebusinessevent", "sitebusinessintegration", "sitebusinessinvite", "sitebusinesslikebox", "sitebusinessmember", "sitebusinessmusic", "sitebusinessnote", "sitebusinessoffer", "sitebusinesspoll", "sitebusinessreview", "sitebusinessvideo", "sitegroup", "sitegroupform", "sitegroupbadge", "sitegroupdocument", "sitegroupevent", "sitegroupintegration", "sitegroupinvite", "sitegrouplikebox", "sitegroupmusic", "sitegroupnote", "sitegroupoffer", "sitegrouppoll", "sitegroupreview", "sitegroupvideo");
$modName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
if( !empty($modName) && in_array($modName, $modArray) ) {
    $getNaviAuth = Engine_Api::_()->getApi('settings', 'core')->getSetting($modName . '.navi.auth', null);
    if( empty($getNaviAuth) )
        return;
}
?>
<script type="text/javascript">
function dismiss(modName) {
  var d = new Date();
  // Expire after 1 Year.
  d.setTime(d.getTime()+(365*24*60*60*1000));
  var expires = "expires="+d.toGMTString();
  document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
	$('dismiss_modules').style.display = 'none';
}

function confirmGoogle() {   
	 var r=confirm("Are you sure you have edited your Google Application?");
   if (r==true) {  
     $('gmail_redirecturl_confirm').style.display='none';
     var req = new Request.JSON( {
        'url' : en4.core.baseUrl + 'seaocore/usercontacts/gmailredirecturlconfirm'            
   });

    req.send();   
   }
   
}

</script>

<?php 
	$moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName(); 
	if( !isset($_COOKIE[$moduleName . '_dismiss']) ):
?>
<div id="dismiss_modules">
	<div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
		</div>
<div style="float:right;">
	<button onclick="dismiss('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
</div>
		<div class="seaocore-notice-text ">
			<?php echo 'To see the latest available version for this plugin and more information, please visit the "Plugins" > "SocialEngineAddOns" section of your site\'s Admin Panel.'; ?>
		</div>	
	</div>
</div>
<?php endif; ?>

<?php 
   //THIS IS THE SPECIAL CASE FOR SITEPAGEINVITE AND SITEBUSINESSINVITE PLUGIN.

   if ($moduleName == 'sitepageinvite' || $moduleName == 'sitebusinessinvite' || $moduleName == 'suggestion'  || $moduleName == 'peopleyoumayknow' || $moduleName == 'sitegroupinvite' || $moduleName == 'siteeventinvite'):
     $gmail_RedirectURL_Confirm = Engine_Api::_()->getApi('settings', 'core')->getSetting('gmail.redirecturl.confirm', 0);
    
     if (!$gmail_RedirectURL_Confirm && Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey')):?>
       
      <div class="seaocore-notice" id="gmail_redirecturl_confirm" >
        <div class="seaocore-notice-icon">
          <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
        </div>
     <div style="float:right;">
      <button onclick="confirmGoogle();"><?php echo $this->translate('Confirm'); ?></button>
    </div>
        <div class="seaocore-notice-text" >
          <?php echo 'If the Gmail invite feature is not working properly on your site, then please follow the steps below to edit your ‘Google Application:<br /><br />a) Go to the URL: <b class="bold"><a href="https://code.google.com/apis/console" target="_blank">https://code.google.com/apis/console</a></b> and login to your google account.<br />b)Click on "Edit Settings" and go to the "Authorized Redirec URLs" section. Copy and paste this URL: <b class="bold">' .  ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->layout()->staticBaseUrl . 'seaocore/usercontacts/getgooglecontacts </b> in the this section.<br />c) If you have pasted the URL, then click on "Confirm” button."';
          
          
          
          
          //echo  'You have created your Google Client ID. So, If your Gmail Invite feature is not working properly then please make sure that you have put the <b class="bold">' . $this->translate("Authorized Redirec URLs") . ' => "' .  ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->layout()->staticBaseUrl . 'seaocore/usercontacts/getgooglecontacts </b> in the App you created at your google account. If not, then please copy this URL, go to your APP at your google account by clicking on this <b class="bold"><a href="https://code.google.com/apis/console" target="_blank" style="color:#5BA1CD;">https://code.google.com/apis/console</a></b> link. Now click on "Edit settings" link and paste this URL in "Authorized Redirec URLs" section.<br />If you have done that already then please click on this confirm button.' ;?><br />
          </div>	
      </div>
       
    <?php endif;endif;
