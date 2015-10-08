<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-main-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$id = null;
$coreMenus = Engine_Api::_()->getApi('menus', 'core');
$this->navigation = $navigation = $coreMenus->getNavigation('user_settings', ( $id ? array('params' => array('id' => $id)) : array()));
$strach_main_photo = Engine_Api::_()->getApi("settings", "core")->getSetting('siteusercoverphoto.strach.main.photo', 1);
?>
<?php if (!Engine_Api::_()->core()->hasSubject('user')): ?>
  <?php $this->navigationProfile = $coreMenus->getNavigation('user_home'); ?>
<?php else: ?>
  <?php $this->navigationProfile = $coreMenus->getNavigation('user_profile'); ?>
<?php endif; ?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteusercoverphoto/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteusercoverphoto/externals/scripts/friends.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css');
?>

<?php if (is_array($this->showContent) && in_array('mainPhoto', $this->showContent)): ?>
  <div class="seaocore_profile_main_photo_wrapper">
    <div class="seaocore_profile_main_photo b_dark">
      <div class="item_photo <?php if ($strach_main_photo): ?> show_photo_box <?php endif; ?>">
        <?php if (empty($this->cover_photo_preview)): ?>
          <table class="siteuser_main_thumb_photo">
            <tr valign="middle">
              <td>
                <?php
                $href = Engine_Api::_()->seaocore()->getUserPhotoHref($this->user);
                ?>
                <?php if (empty($this->can_edit) && $href) : ?>
                  <a href="<?php echo $href; ?>" onclick='openSeaocoreLightBox("<?php echo $href; ?>");
                            return false;'>
                  <?php endif; ?>
                  <?php echo $this->itemPhoto($this->user, 'thumb.profile', '', array('align' => 'left', 'id' => 'user_profile_photo')); ?>
                  <?php if (empty($this->can_edit) && $href) : ?></a><?php endif; ?>
              </td>
            </tr>
          </table>
        <?php else: ?>
          <table class="siteuser_main_thumb_photo">
            <tr valign="middle">
              <td>
                <?php echo '<img src="application/modules/User/externals/images/nophoto_user_thumb_profile.png" alt="" id="user_profile_photo" align = "left"/>' ?>
              </td>
            </tr>
          </table>
        <?php endif; ?>
      </div>
    </div>
    <?php if (!empty($this->can_edit)) : ?>
      <div id="siteusercoverphoto_main_options" class="seaocore_profile_cover_options <?php if (empty($this->user->photo_id) && empty($this->cover_photo_preview)) : ?> dblock <?php endif; ?> <?php if (!empty($this->cover_photo_preview)) : ?> seaocore_profile_main_photo_options dnone <?php else: ?> seaocore_profile_main_photo_options <?php endif; ?>">
        <ul class="edit-button">
          <li> 
            <span class="seaocore_profile_cover_btn">
              <?php if (!empty($this->user->photo_id)) : ?>							
                <i class="seaocore_profile_cover_icon_photo_edit"><?php echo $this->translate("Edit Profile Picture"); ?></i>							
              <?php else: ?>							
                <i class="seaocore_profile_cover_icon_photo_add"><?php echo $this->translate("Add Profile Picture"); ?></i>							
              <?php endif; ?>
            </span>  

            <ul class="seaocore_profile_options_pulldown">
              <li>
                <a href='<?php echo $this->url(array('action' => 'upload-cover-photo', 'user_id' => $this->user->user_id, 'special' => 'profile'), 'siteusercoverphoto_profilepage', true); ?>'  class="seaocore_profile_cover_icon_photo_upload smoothbox"><?php echo $this->translate('Upload Photo'); ?></a>
              </li>
              <li>
                <?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', 'user_id' => $this->user->user_id, 'recent' => 1, 'special' => 'profile'), 'siteusercoverphoto_profilepage', true), $this->translate('Choose from Album Photos'), array(' class' => 'seaocore_profile_cover_icon_photo_view smoothbox')); ?>
              </li> 
              <?php if(!Engine_API::_()->seaocore()->isMobile() && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')):?>
                <li>
                  <?php $URL_webcam = $this->url(array('action' => 'web-cam-image'), 'siteusercoverphoto_profilepage', true);
                  ?>
                  <a href="javascript: void(0);" onclick="uploadWebCamImage('<?php echo $URL_webcam; ?>');" class="seaocore_profile_cover_icon_camera"> <?php echo $this->translate("Take Photo") ?></a>
                </li>
              <?php endif;?>
              <?php if (!empty($this->user->photo_id)) : ?>
                <li>
                  <?php echo $this->htmlLink(array('route' => 'siteusercoverphoto_profilepage', 'action' => 'remove-cover-photo', 'user_id' => $this->user->user_id, 'special' => 'profile'), $this->translate('Remove'), array(' class' => 'smoothbox seaocore_profile_cover_icon_photo_delete')); ?>
                </li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    <?php endif; ?>	
  </div>
<?php endif; ?>

<div class="seaocore_profile_cover_head_section_inner" id="seaocore_profile_cover_head_section_inner">
    <?php if (is_array($this->showContent) && (in_array('title', $this->showContent) || in_array('rating', $this->showContent))): ?>
    <?php if (empty($this->cover_photo_preview)): ?>
      <div class="seaocore_profile_coverinfo_status">
      	<div class="fleft">
        	<h2 style="color:<?php echo $this->fontcolor; ?>">
          <?php if (in_array('title', $this->showContent)): ?>
            <?php echo $this->user->getTitle(); ?>
          <?php endif; ?>
        
          <?php if (Engine_Api::_()->hasModuleBootstrap('siteverify')): ?>
            <?php $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($this->user->user_id); ?>
            <?php $verify_limit = Engine_Api::_()->authorization()->getPermission($this->user->level_id, 'siteverify_verify', 'verify_limit'); ?>
            <?php if (!empty($this->showContent) && in_array("verify", $this->showContent) && ($verify_count >= $verify_limit)): ?>  
            <span class="siteverify_tip_wrapper">
                <i class="sitemember_list_verify_label mleft5"></i>
                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
            </span>
            <?php endif; ?>
        	<?php endif; ?>
          </h2>
        </div>
          
        <?php if (in_array('rating', $this->showContent)): ?>
          <?php if (Engine_Api::_()->hasModuleBootstrap('sitemember')): ?>
            <div class="mtop5"> 
              <?php echo $this->content()->renderWidget("sitemember.user-ratings"); ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="seaocore_profile_coverinfo_status">
        <h2 style="color:<?php echo $this->fontcolor; ?>"><?php echo $this->translate("Display Name") ?></h2>
      </div>    
    <?php endif; ?>
  <?php endif; ?>
  <?php if (is_array($this->showContent) && (($this->profile_like_button == 1) || in_array('friendShipButton', $this->showContent) || in_array('composeMessageButton', $this->showContent) || in_array('updateInfoButton', $this->showContent) || in_array('settingsButton', $this->showContent) || in_array('optionsButton', $this->showContent))): ?>
    <div class="seaocore_profile_coverinfo_buttons">
      <?php if ($this->profile_like_button == 1) : ?>
        <div>
          <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')): ?>
            <?php echo $this->content()->renderWidget("sitelike.commoncover-like-button"); ?>
          <?php else: ?>
            <?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
          <?php endif; ?>
        </div>	
      <?php endif; ?>

      <?php if (in_array('friendShipButton', $this->showContent) && $this->UserFriendshipAjax($this->user)): ?>
        <div class="seaocore_button" id="friendship_user">
          <?php echo $this->UserFriendshipAjax($this->user) ?>
        </div>
      <?php endif; ?>

      <?php if (in_array('composeMessageButton', $this->showContent) && $this->Message($this->user)): ?>   
        <div class="seaocore_button">
          <?php echo $this->Message($this->user) ?>
        </div>
      <?php endif; ?>

      
      <?php if (isset($this->user) && ($this->user->getIdentity() == $this->viewer()->getIdentity())) : ?>
        <?php if (in_array('updateInfoButton', $this->showContent)): ?>
          <div class="seaocore_button">
            <a href="<?php echo $this->url(array('action' => 'profile', 'controller' => 'edit'), 'user_extended', true); ?>">
              <span><?php echo $this->translate("Update Info"); ?></span>
            </a>
          </div>
        <?php endif; ?>

        <?php if (in_array('settingsButton', $this->showContent)): ?>
          <div class="seaocore_button seaocore_profile_settings_btn prelative">
            <a href="javascript:void(0);" onclick="showSettingsOptions();">
              <span><?php echo $this->translate("Settings"); ?></span>
            </a>
            <ul class="seaocore_profile_options_pulldown siteuser_cover_settings" id="siteuser_cover_settings_pulldown" style="display:none;">
              <li>
                <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
              </li>
            </ul>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (in_array('optionsButton', $this->showContent) && $this->viewer()->getIdentity()): ?>
        <div class="seaocore_button seaocore_profile_option_btn prelative">
          <a href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i><i class="icon_down"></i></a>
          <ul class="seaocore_profile_options_pulldown" id="siteuser_cover_settings_options_pulldown" style="display:none;right:0;">
            <li>
              <?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setPartial(array('_navIcons.tpl', 'core'))->render(); ?>
            </li>
          </ul>
        </div>
      <?php endif; ?>

    </div>
  <?php endif; ?>
  <?php if (Engine_Api::_()->hasModuleBootstrap('facebookse') && ($this->profile_like_button == 2)): ?>
    <div class="seaocore_profile_cover_fb_like_button"> 
      <?php echo $this->content()->renderWidget("facebookse.facebookse-commonlike", array('module_current' => 'user', 'requested_uri' => $this->user->getHref())); ?>
    </div>	
  <?php endif; ?>

</div>

<div class="clr"></div>


<script type="text/javascript">

                    function uploadWebCamImage(url) {
                      Smoothbox.open(url);
                    }

                    function showPulDownOptions() {
                      //    $('.seaocore_profile_option_btn')
                      var parent = $('siteuser_cover_settings_options_pulldown').getParent('.seaocore_profile_option_btn');
                      if (parent) {
                        var rightPostion = document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
                        // var optionsPullDown=;
                        $('siteuser_cover_settings_options_pulldown').inject(document.body);
                        $('siteuser_cover_settings_options_pulldown').setStyles({
                          'position': 'absolute',
                          'top': parent.getCoordinates().bottom,
                          'right': rightPostion
                        });
                      }
                      if ($('siteuser_cover_settings_options_pulldown').style.display == 'none') {
                        $('siteuser_cover_settings_options_pulldown').style.display = "block";
                      } else {
                        $('siteuser_cover_settings_options_pulldown').style.display = "none";
                      }
                      if ($('siteuser_cover_settings_pulldown')) {
                        $('siteuser_cover_settings_pulldown').style.display = "none";
                      }
                    }

                    function showSettingsOptions() {

                      var parent = $('siteuser_cover_settings_pulldown').getParent('.seaocore_profile_settings_btn');
                      if (parent) {
                        var rightPostion = document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
                        // var optionsPullDown=;
                        $('siteuser_cover_settings_pulldown').inject(document.body);
                        $('siteuser_cover_settings_pulldown').setStyles({
                          'position': 'absolute',
                          'top': parent.getCoordinates().bottom,
                          'right': rightPostion
                        });
                      }
                      if ($('siteuser_cover_settings_pulldown').style.display == 'none') {
                        $('siteuser_cover_settings_pulldown').style.display = "block";
                      } else {
                        $('siteuser_cover_settings_pulldown').style.display = "none";
                      }
                      if ($('siteuser_cover_settings_options_pulldown')) {
                        $('siteuser_cover_settings_options_pulldown').style.display = "none";
                      }
                    }
</script>