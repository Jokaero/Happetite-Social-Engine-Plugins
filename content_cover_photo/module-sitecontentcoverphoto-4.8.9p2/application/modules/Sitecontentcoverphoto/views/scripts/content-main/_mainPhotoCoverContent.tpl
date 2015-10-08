<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-main-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<div class="seaocore_profile_cover_head_section_inner" id="seaocore_profile_cover_head_section_inner">
    <div class="seaocore_profile_coverinfo_status" style="color:<?php echo $this->fontcolor;?>">
  <?php if (is_array($this->showContent) && in_array('title', $this->showContent)): ?>
    <?php if(empty($this->cover_photo_preview)):?>
      <h2><?php echo $this->subject()->getTitle(); ?></h2>
    <?php else:?>
			<h2><?php $getShortType = ucfirst($this->subject()->getShortType()); echo $this->translate("$getShortType Title") ?></h2>
    <?php endif;?>
  <?php endif;?>
  </div>
  <?php if(($this->profile_like_button == 1) || (in_array('optionsButton', $this->showContent))):?>
    <div class="seaocore_profile_coverinfo_buttons">
      <?php if ($this->profile_like_button == 1) : ?>
        <div>
          	<?php if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitelike')): ?>
						<?php echo $this->content()->renderWidget("sitelike.commoncover-like-button"); ?>
          <?php else: ?>
						<?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
          <?php endif; ?>
        </div>	
      <?php endif; ?>
<?php 
            if (is_array($this->showContent) && in_array('shareOptions', $this->showContent)) {
                $this->subject = $this->subject();
                include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareButtons.tpl';
            }
         ?>
      <?php if (is_array($this->showContent) && in_array('optionsButton', $this->showContent)): ?>
				<?php $this->navigationProfile = $coreMenus->getNavigation($moduleName."_gutter"); ?>
        <?php if(count($this->navigationProfile) > 0):?>
					<div class="seaocore_button seaocore_profile_option_btn prelative">
						<a id="polldown_options_cover_photo" href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i><i class="icon_down"></i></a>
						<ul class="seaocore_profile_options_pulldown" id="sitecontent_cover_settings_options_pulldown" style="display:none;right:0;">
							<li>
								<?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setPartial(array('_navIcons.tpl', 'core'))->render(); ?>
							</li>
						</ul>
					</div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  
	
  <?php $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');?>
  <?php if ($fbmodule && !empty($fbmodule->enabled) && ($this->profile_like_button == 2)) : ?>
		<div class="seaocore_profile_cover_fb_like_button"> 
			<?php echo $this->content()->renderWidget("Facebookse.facebookse-commonlike", array('subject' => $this->subject()->getGuid())); ?>
		</div>	
  <?php endif; ?>
</div>