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
        <h2 style="color:<?php echo $this->fontcolor;?>"><?php echo $this->subject()->getTitle(); ?></h2>
      <?php else:?>
        <h2 style="color:<?php echo $this->fontcolor;?>"><?php echo $this->translate("Group Title") ?></h2>
      <?php endif;?>
    <?php endif;?>
    <div class="seaocore_profile_cover_info">
      <div class="seaocore_profile_coverinfo_stats seaocore_txt_light" style="color:<?php echo $this->fontcolor;?>">
        <?php if (is_array($this->showContent) && in_array('likeCount', $this->showContent)): ?>
          <a style="color:<?php echo $this->fontcolor;?>" id= "sitegroup_group_num_of_like_<?php echo $this->subject()->group_id;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => 'sitegroup_group', 'resource_id' => $this->subject()->group_id, 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s like', '%s likes', $this->subject()->like_count),$this->locale()->toNumber($this->subject()->like_count)); ?></a>
        <?php endif; ?>

        <?php if (is_array($this->showContent) && in_array('followCount', $this->showContent) && isset($this->subject()->follow_count)): ?>
          <?php if (is_array($this->showContent) && in_array('likeCount', $this->showContent)  && isset($this->subject()->like_count)): ?>
            &middot; 
          <?php endif; ?>
            <a style="color:<?php echo $this->fontcolor;?>" id= "sitegroup_group_num_of_follows_<?php echo $this->subject()->group_id;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> 'sitegroup_group', 'resource_id' => $this->subject()->group_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->subject()->follow_count),$this->locale()->toNumber($this->subject()->follow_count)); ?></a>
        <?php endif; ?>

        <?php if (is_array($this->showContent) && in_array('memberCount', $this->showContent)  && isset($this->subject()->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
           &middot; 
          <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
          if ($this->subject()->member_title && $memberTitle) {
            if ($this->subject()->member_count == 1) : ?>
            <a style="color:<?php echo $this->fontcolor;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('action'=>'member-join', 'group_id' => $this->subject()->group_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitegroupmember_approve'	, true)); ?>'); return false;" ><?php echo $this->subject()->member_count .' '. $this->translate('member'); ?></a>
          <?php	else: ?>
          <a style="color:<?php echo $this->fontcolor;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('action'=>'member-join', 'group_id' => $this->subject()->group_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitegroupmember_approve'	, true)); ?>'); return false;" ><?php echo $this->subject()->member_count . ' ' .  $this->subject()->member_title;?></a>
          <?php 	endif; ?>
          <?php } else { ?>
          <a style="color:<?php echo $this->fontcolor;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('action'=>'member-join', 'group_id' => $this->subject()->group_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitegroupmember_approve'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s member', '%s members', $this->subject()->member_count),$this->locale()->toNumber($this->subject()->member_count)); ?></a>
          <?php 	} ?>
        <?php endif; ?>
      </div>

      <?php if($this->profile_like_button != 2 ):?>
        <?php if((($this->show_phone || $this->show_email || $this->show_website)) || ( !empty($this->subject()->phone) || !empty($this->subject()->email) || !empty($this->subject()->website))):?>
          <div class="seaocore_profile_coverinfo_stats seaocore_txt_light" style="color:<?php echo $this->fontcolor;?>">
            <?php if($this->show_phone && !empty($this->subject()->phone)):?>
              <?php echo $this->subject()->phone ?>
            <?php endif;?>
            <?php if($this->show_email &&  !empty($this->subject()->email)):?>
              <?php if ( empty($this->emailme)) :?>
                &nbsp;
                <a style="color:<?php echo $this->fontcolor;?>" href='mailto:<?php echo $this->subject()->email ?>'><?php echo $this->translate('Email Me') ?></a>
              <?php else: ?>&nbsp;
                <a style="color:<?php echo $this->fontcolor;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('route' => 'sitegroup_profilegroup', 'module' => 'sitegroup', 'controller' => 'profile', 'action' => 'email-me', "id" => $this->subject()->group_id), 'default' , true)); ?>'); return false;"><?php echo $this->translate('Email Me') ?></a>
              <?php endif; ?>
            <?php endif;?>
            <?php if($this->show_website &&  !empty($this->subject()->website)):?>&nbsp;
              <?php if (strstr($this->subject()->website, 'http://') || strstr($this->subject()->website, 'https://')): ?>
                <a style="color:<?php echo $this->fontcolor;?>" href='<?php echo $this->subject()->website ?>' target="_blank" title='<?php echo $this->subject()->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
              <?php else: ?>&nbsp;
                <a style="color:<?php echo $this->fontcolor;?>" href='http://<?php echo $this->subject()->website ?>' target="_blank" title='<?php echo $this->subject()->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
              <?php endif; ?>
            <?php endif;?>
          </div>
        <?php endif; ?>
      <?php endif;?>
    </div>
  </div>
  <?php if(($this->profile_like_button == 1) || (in_array('followButton', $this->showContent)) || (in_array('joinButton', $this->showContent)) || (in_array('addButton', $this->showContent)) || (in_array('optionsButton', $this->showContent))):?>
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

      <?php if (is_array($this->showContent) && in_array('followButton', $this->showContent)): ?>
          <?php echo $this->content()->renderWidget("seaocore.seaocore-follow"); ?>
   
      <?php endif;?>

      <?php if (is_array($this->showContent) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) : ?>
				<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer->getIdentity(), $this->subject()->group_id);
				if (empty($joinMembers) && in_array('joinButton', $this->showContent) && $this->viewer->getIdentity() != $this->subject()->owner_id && Engine_Api::_()->sitegroup()->allowInThisGroup($this->subject(), "sitegroupmember", 'smecreate')): ?>
					<div class="seaocore_button">
					<?php if ($this->viewer->getIdentity()) : ?>
						<?php if (!empty($this->subject()->member_approval)): ?>
							<a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array( 'action' => 'join', 'group_id' => $this->subject()->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><i class="plus_icon"></i><span><?php echo $this->translate("Join Group"); ?></span></a>
						<?php else: ?>
							<a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array( 'action' => 'request', 'group_id' => $this->subject()->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><i class="plus_icon"></i><span><?php echo $this->translate("Join Group"); ?></span></a>
						<?php endif; ?>
					<?php endif; ?>
					</div>
				<?php endif; ?>
				
				<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer->getIdentity(), $this->subject()->group_id, $params = "Leave");
        if (!empty($hasMembers) && in_array('leaveButton', $this->showContent) && $this->viewer->getIdentity() != $this->subject()->owner_id && Engine_Api::_()->sitegroup()->allowInThisGroup($this->subject(), "sitegroupmember", 'smecreate')): ?>
					<div class="seaocore_button">
            <?php if ($this->viewer->getIdentity()) : ?>
              <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array( 'action' => 'leave', 'group_id' => $this->subject()->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><i class="minus_icon"></i><span><?php echo $this->translate("Leave Group"); ?></span></a>
            <?php endif; ?>
					</div>
				<?php endif; ?>
				
				<?php if (is_array($this->showContent) && in_array('addButton', $this->showContent)): ?>
					<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer->getIdentity(), $this->subject()->group_id, $params = 'Invite'); ?>
					<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
					<div>
						<a class="sitegroup_button" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'group_id' => $this->subject()->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>	
					</div>
					<?php elseif (!empty($hasMembers) && empty($this->subject()->member_invite)): ?>
					<div>
						<a class="sitegroup_button" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'group_id' => $this->subject()->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
<?php 
        if (is_array($this->showContent) && in_array('shareOptions', $this->showContent)) {
            $this->subject = $this->subject();
            include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareButtons.tpl';
        }
       ?>
      <?php if (is_array($this->showContent) && in_array('optionsButton', $this->showContent)): ?>
				<?php $this->navigationProfile = $coreMenus->getNavigation("sitegroup_gutter"); ?>
				<?php if(count($this->navigationProfile) > 0):?>
					<div class="seaocore_button seaocore_profile_option_btn prelative">
						<a id="polldown_options_cover_photo" href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i><i class="icon_down"></i></a>
						<ul class="seaocore_profile_options_pulldown" id="sitecontent_cover_settings_options_pulldown" style="display:none;right:0;">
							<li>
								<?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setUlClass('navigation sitegroups_gutter_options')->render(); ?>
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