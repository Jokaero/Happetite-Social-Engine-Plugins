<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-main-photo.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$moduleName = $this->moduleName;
$fieldName = $this->fieldName;
$coreMenus = Engine_Api::_()->getApi('menus', 'core');
?>

<?php
if ($this->profile_like_button == 1) {
  $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
}
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecontentcoverphoto/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecontentcoverphoto/externals/scripts/friends.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css');
?>

<?php if (is_array($this->showContent) && in_array('mainPhoto', $this->showContent)): ?>
  <div class="seaocore_profile_main_photo_wrapper">
    <div class="seaocore_profile_main_photo b_dark">
      <div class="item_photo <?php if ($this->sitecontentcoverphotoStrachMainPhoto): ?> show_photo_box <?php endif; ?>">
        <?php if (empty($this->cover_photo_preview)): ?>
          <table class="siteuser_main_thumb_photo">
            <tr valign="middle">
              <td>
								<?php 			
									$href = Engine_Api::_()->seaocore()->getContentPhotoHref($this->subject());
								?>
								<?php if (empty($this->can_edit) && $href) : ?>
									<a href="<?php echo $href; ?>" onclick='openSeaocoreLightBox("<?php echo $href; ?>");return false;'>
								<?php endif; ?>
								<?php echo $this->itemPhoto($this->subject(), 'thumb.profile', '', array('align' => 'left', 'id' => 'content_profile_photo')); ?>
								<?php if (empty($this->can_edit) && $href) : ?></a><?php endif; ?>
              </td>
            </tr>
          </table>
        <?php else: ?>
          <table class="siteuser_main_thumb_photo">
            <tr valign="middle">
              <td>
                <?php 
                switch ($moduleName):
                  case "sitepage":
                    echo '<img src="application/modules/Sitepage/externals/images/nophoto_list_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                  case "sitebusiness":
                    echo '<img src="application/modules/Sitebusiness/externals/images/nophoto_list_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                  case "sitegroup":
                    echo '<img src="application/modules/Sitegroup/externals/images/nophoto_list_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                  case "sitestore":
                    echo '<img src="application/modules/Sitestore/externals/images/nophoto_store_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                  case "sitestoreproduct":
                    echo '<img src="application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                  case "siteevent":
                    echo '<img src="application/modules/Siteevent/externals/images/nophoto_event_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                  case "sitereview":
                    echo '<img src="application/modules/Sitereview/externals/images/nophoto_listing_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                   case "album":
                    echo '<img src="application/modules/Sitealbum/externals/images/nophoto_album_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                    break;
                  default:
                    echo '<img src="application/modules/Sitecontentcoverphoto/externals/images/nophoto_listing_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>';
                endswitch;
                ?>
              </td>
            </tr>
          </table>
        <?php endif; ?>
      </div>
    </div>
    <?php if (!empty($this->can_edit)) : ?>
      <div id="sitecontentcoverphoto_main_options" class="seaocore_profile_cover_options <?php if (empty($this->subject()->photo_id) && empty($this->cover_photo_preview)) : ?> dblock <?php endif; ?> <?php if (!empty($this->cover_photo_preview)) : ?> seaocore_profile_main_photo_options dnone <?php else: ?> seaocore_profile_main_photo_options <?php endif; ?>">
        <ul class="edit-button">
          <li> 
            <span class="seaocore_profile_cover_btn">
              <?php if (!empty($this->subject()->photo_id)) : ?>							
                <i class="seaocore_profile_cover_icon_photo_edit"><?php echo $this->translate("Edit Profile Picture"); ?></i>							
              <?php else: ?>							
                <i class="seaocore_profile_cover_icon_photo_add"><?php echo $this->translate("Add Profile Picture"); ?></i>							
              <?php endif; ?>
            </span>  

            <ul class="seaocore_profile_options_pulldown">
              <li>
                <a href='<?php echo $this->url(array('action' => 'upload-cover-photo', $fieldName => $this->subject()->getIdentity(), 'special' => 'profile', 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), 'sitecontentcoverphoto_profilepage', true); ?>'  class="seaocore_profile_cover_icon_photo_upload smoothbox"><?php echo $this->translate('Upload Photo'); ?></a>
              </li>
              <li>
                <?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', $this->fieldName => $this->subject()->getIdentity(), 'recent' => 1, 'special' => 'profile', 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), 'sitecontentcoverphoto_profilepage', true), $this->translate('Choose from Album Photos'), array(' class' => 'seaocore_profile_cover_icon_photo_view smoothbox')); ?>
              </li>

              <?php if (!empty($this->subject()->photo_id)) : ?>
                <li>
                  <?php echo $this->htmlLink(array('route' => 'sitecontentcoverphoto_profilepage', 'action' => 'remove-cover-photo', $this->fieldName => $this->subject()->getIdentity(), 'special' => 'profile', 'subject' => $this->subject()->getGuid(), 'fieldName' => $fieldName, 'moduleName' => $moduleName), $this->translate('Remove'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_delete')); ?>
                </li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    <?php endif; ?>	
  </div>
<?php endif; ?>

<?php 
switch ($moduleName) { 
  case "sitepage":
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSitepage.tpl';
    break;
  case "sitebusiness":
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSitebusiness.tpl';
    break;
  case "sitegroup":
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSitegroup.tpl';
    break;
  case "sitestore":
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSitestore.tpl';
    break;
  case "sitestoreproduct":
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSitestoreproduct.tpl';
    break;
  case "siteevent":
        include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSiteevent.tpl';
    break;
  case "sitereview":
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSitereview.tpl';
    break;
    case "album":
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContentSitealbum.tpl';
    break;
  case $moduleName:
    include APPLICATION_PATH . '/application/modules/Sitecontentcoverphoto/views/scripts/content-main/_mainPhotoCoverContent.tpl';
    break;
}
?>

<div class="clr"></div>

<script type="text/javascript">

  function showPulDownOptions() {
		var parent = $('sitecontent_cover_settings_options_pulldown').getParent('.seaocore_profile_option_btn');
		if(parent) {
			var rightPostion=document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
			$('sitecontent_cover_settings_options_pulldown').inject(document.body);
			$('sitecontent_cover_settings_options_pulldown').setStyles({
				'position':'absolute',
				'top':parent.getCoordinates().bottom,
				'right':rightPostion 
			});
		}

    if($('sitecontent_cover_settings_options_pulldown').style.display == 'none') {
      $('sitecontent_cover_settings_options_pulldown').style.display = "block";
    } else {
      $('sitecontent_cover_settings_options_pulldown').style.display = "none";
    }

// 			var divElement = new Element('div', {
// 				'id' : 'options-loaded',
// 				'styles' : {
// 					'position' : 'fixed',
// 					'left':'0px',
// 					'right':'0px','top':'0px','bottom':'0px'
// 				},
// 				'onclick': 'hideCoverPhotoOptions()'
// 			});	divElement.inject(document.body);

			document.body.removeEvents('click').addEvent('click', function(event) {
        if($('sitecontent_cover_settings_options_pulldown').style.display == 'block' && event.target != '' && event.target.id != 'polldown_options_cover_photo' && event.target.className != 'icon_down' && event.target.className != 'icon_cog') {console.log(event.target);
					$('sitecontent_cover_settings_options_pulldown').style.display = 'none';
				}
			});
  }

//   function hideCoverPhotoOptions() {
//    $('options-loaded').destroy();
// 	 $('sitecontent_cover_settings_options_pulldown').style.display = 'none';
//   }

    
en4.core.runonce.add(function () {
 showShareLinks(1);
});  
</script>