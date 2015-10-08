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
<?php
$this->headTranslate(array('Leave Event', 'Join Event', 'Request Invite'));
?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<div class="seaocore_profile_cover_head_section_inner" id="seaocore_profile_cover_head_section_inner">
  <div class="seaocore_profile_coverinfo_status">
    <?php if (is_array($this->showContent) && in_array('title', $this->showContent)): ?>
      <?php if(empty($this->cover_photo_preview)):?>
        <h2><?php echo $this->subject()->getTitle(); ?></h2>
      <?php else:?>
          <h2><?php echo $this->translate("Event Title") ?></h2>
      <?php endif;?>
    <?php endif;?>
    <?php if (is_array($this->showContent) && in_array('hostName', $this->showContent) && $this->subject()->getHost()): ?>
        <div class="siteevent_listings_stats">
          <div class="o_hidden f_small">
            <i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="<?php echo $this->translate("Host") ?>"></i>
            <?php if($this->contentFullWidth):?><?php echo $this->translate('Hosted By');?><?php endif;?> <?php echo $this->htmlLink($this->subject()->getHost()->getHref(), $this->subject()->getHost()->getTitle()); ?>
          </div>
        </div>
      <?php endif;?>
    <div class="seaocore_profile_cover_info">   

        <?php if (is_array($this->showContent) && in_array('likeCount', $this->showContent)): ?>
          <a style="color:<?php echo $this->fontcolor;?>" id= "siteevent_event_num_of_like_<?php echo $this->subject()->event_id;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => 'siteevent_event', 'resource_id' => $this->subject()->event_id, 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s like', '%s likes', $this->subject()->like_count),$this->locale()->toNumber($this->subject()->like_count)); ?></a>
        <?php endif; ?>

        <?php if (isset($this->subject()->follow_count) && is_array($this->showContent) && in_array('followCount', $this->showContent) && isset($this->subject()->follow_count)): ?>
          <?php if (is_array($this->showContent) && in_array('likeCount', $this->showContent)  && isset($this->subject()->like_count)): ?>
            &middot; 
          <?php endif; ?>
            <a style="color:<?php echo $this->fontcolor;?>" id= "siteevent_event_num_of_follows_<?php echo $this->subject()->event_id;?>" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> 'siteevent_event', 'resource_id' => $this->subject()->event_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->subject()->follow_count),$this->locale()->toNumber($this->subject()->follow_count)); ?></a>
        <?php endif; ?>
   
       
      <!--//IF EVENT REPEAT MODULE EXIST THEN SHOW EVENT REPEAT INFO WIDGET-->
      <?php $siteeventrepeat = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
         if($siteeventrepeat) { 
            $showrepeatinfo = is_array($this->showContent) &&  in_array('showrepeatinfo', $this->showContent) ? true : false;
            echo $this->content()->renderWidget("siteeventrepeat.event-profile-repeateventdate",  array("showrepeatinfo" => $showrepeatinfo));

         }
      ?>

      <?php if (is_array($this->showContent) && in_array('category', $this->showContent)): ?> 
        <div class="siteevent_listings_stats">
         
          <div class="o_hidden f_small" title="<?php if( !empty($this->sitecontentcoverphotoChangeTabPosition) ) : echo $this->translate("Category"); endif; ?>"> 
               <i class="siteevent_icon_strip siteevent_icon siteevent_icon_tag" title="<?php echo $this->translate("Category") ?>"></i>
            <a href="<?php echo $this->url(array('category_id' => $this->subject()->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->subject()->category_id)->getCategorySlug()), "siteevent_general_category" ); ?>"><?php echo $this->translate(Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($this->subject()->category_id)->category_name); ?></a>
          </div>
        </div> 
      <?php endif;?>

      <?php if (is_array($this->showContent) && in_array('venue', $this->showContent) && !$this->subject()->is_online && $this->subject()->venue_name): ?> 
        <div class="siteevent_listings_stats">
          
          <div class="o_hidden f_small" title="<?php if( !empty($this->sitecontentcoverphotoChangeTabPosition) ) : echo $this->translate("Venue"); endif; ?>">
<i class="siteevent_icon_strip siteevent_icon siteevent_icon_venue" title="<?php echo $this->translate("Venue") ?>"></i>           
 <?php echo $this->subject()->venue_name; ?>
          </div>
        </div> 
      <?php endif;?>

      <?php  if (in_array('startDate', $this->showContent) || in_array('endDate', $this->showContent)) : ?>
        <?php $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null; ?>
        <?php $dateTimeInfo = array(); ?>
        <?php $dateTimeInfo['occurrence_id'] = $occurrence_id; ?>
        <?php $dateTimeInfo['showStartDateTime'] = in_array('startDate', $this->showContent); ?>
        <?php $dateTimeInfo['showEndDateTime'] = in_array('endDate', $this->showContent); ?>
        <?php $dateTimeInfo['showDateTimeLabel'] = true; ?>
        <?php $dateTimeInfo['contentFullWidth'] = $this->contentFullWidth; ?>
        <?php 
				$showrepeatinfo = true;
				if($siteeventrepeat) { 
						$showrepeatinfo = is_array($this->showContent) &&  in_array('showrepeatinfo', $this->showContent) ? false : true;				
				} ?>
				<?php $dateTimeInfo['showMultipleText'] = $showrepeatinfo; ?>
        <?php $this->eventDateTime($this->subject(), $dateTimeInfo); ?> 
      <?php endif; ?>

			<?php if (is_array($this->showContent) && in_array('location', $this->showContent) && $this->subject()->location): ?> 
        <div class="siteevent_listings_stats">
          
          <div class="o_hidden f_small" title="<?php if( !empty($this->sitecontentcoverphotoChangeTabPosition) ) : echo $this->translate("Location"); endif; ?>"> 
              <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
            <?php echo $this->subject()->location; ?>
          </div>
        </div> 
      <?php endif;?>

     <?php if (is_array($this->showContent) && in_array('price', $this->showContent)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)): ?>

        <?php if($this->subject()->price > 0):?>
        <div class="siteevent_listings_stats">
          
          <div class="o_hidden f_small" title="<?php if( !empty($this->sitecontentcoverphotoChangeTabPosition) ) : echo $this->translate("Price"); endif; ?>">   
              <i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
            <?php echo $this->locale()->toCurrency($this->subject()->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
          </div>
        </div>
        <?php else:?>
					<div class="siteevent_listings_stats">
						
						<div class="o_hidden f_small siteevent_listings_price_free" title="<?php if( !empty($this->sitecontentcoverphotoChangeTabPosition) ) : echo $this->translate("Price"); endif; ?>">   
                                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate("Price") ?>"></i>
							<?php echo $this->translate("FREE"); ?>
						</div>
					</div>
				<?php endif;?>
      <?php endif; ?>  

      <?php if (is_array($this->showContent) && in_array('ledBy', $this->showContent)): ?>
        <div class="siteevent_listings_stats">
          
          <div class="o_hidden f_small" title="<?php if( !empty($this->sitecontentcoverphotoChangeTabPosition) ) : echo $this->translate("Leader"); endif; ?>">   
              <i class="siteevent_icon_strip siteevent_icon siteevent_icon_user" title="<?php echo $this->translate("Leader") ?>"></i>
            <?php 
              $ledBys = $this->subject()->getLedBys();
              if( !empty($ledBys) ) :
                echo $this->subject()->getLedBys();
              endif;
              ?>
          </div>
        </div>
      <?php endif; ?>  
      

      <?php if($this->profile_like_button != 2 ):?>
        <?php
          $row = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getOtherinfo($this->subject()->event_id);
        ?>
        <?php if((($this->show_phone || $this->show_email || $this->show_website)) || ( !empty($row->phone) || !empty($row->email) || !empty($row->website))):?>
          <div class="siteevent_listings_stats_wrap">
            <?php if($this->show_phone && !empty($row->phone)):?>
              <div class="siteevent_listings_stats">
                
                <div class="fleft f_small">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_contact" title="<?php echo $this->translate('Phone') ?>"></i>
                  <?php echo $row->phone ?>
                </div>
              </div>
            <?php endif;?>
            <?php if($this->show_email && !empty($row->email)):?>
              <div class="siteevent_listings_stats">
                
                <div class="fleft f_small">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_mail" title="<?php echo $this->translate('E-mail') ?>"></i>
                  <a href='mailto:<?php echo $row->email ?>'><?php echo $this->translate('Email Me') ?></a>
                </div>
              </div>
            <?php endif;?>
            <?php if($this->show_website &&  !empty($row->website)):?>
              <div class="siteevent_listings_stats">
                
                <div class="fleft f_small">
                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_web" title="<?php echo $this->translate('Website') ?>"></i>
                  <?php if (strstr($row->website, 'http://') || strstr($row->website, 'https://')): ?>
                    <a href='<?php echo $row->website ?>' target="_blank" title='<?php echo $row->website ?>'><?php echo $this->translate('Visit Website') ?></a>
                  <?php else: ?>
                    <a href='http://<?php echo $row->website ?>' target="_blank" title='<?php echo $row->website ?>'><?php echo $this->translate('Visit Website') ?></a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif;?>
          </div>
        <?php endif; ?>
      <?php endif;?>
    </div>
  </div>
  <?php if(is_array($this->showContent) && (($this->profile_like_button == 1) || in_array('inviteGuest', $this->showContent) || in_array('joinButton', $this->showContent) || in_array('updateInfoButton', $this->showContent) || in_array('inviteRsvpButton', $this->showContent)  || (in_array('optionsButton', $this->showContent) ))):?>
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

      <?php if(isset($this->subject()->follow_count) && is_array($this->showContent) && in_array('followButton', $this->showContent)): ?>
				<?php 
					$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/follow.js'); 
				?>
          <?php echo $this->content()->renderWidget("seaocore.seaocore-follow"); ?>
        
      <?php endif;?>
    <?php if (is_array($this->showContent) && in_array('joinButton', $this->showContent)): ?>
        <div id="event_membership" class="seaocore_like_button">
                <?php echo $this->eventLinks($this->subject())?>
        </div>
    <?php endif;?>
        <?php if (is_array($this->showContent) && in_array('addToMyCalendar', $this->showContent)): ?>
      <?php
            $this->googlelink = Engine_Api::_()->siteevent()->getGoogleCalenderLink($this->subject());
            $this->yahoolink = Engine_Api::_()->siteevent()->getYahooCalenderLink($this->subject());
        
        ?>
        <div class="siteevent_addcalender">
          <div id="calendar" class="siteevent_calender_button">
   <?php if(!$this->contentFullWidth):?><div class="seaocore_like_button"><?php endif;?><a <?php if(!$this->contentFullWidth):?> style="background-color: #fff;line-height:20px;" <?php endif;?> class="siteevent_buttonlink" onclick="AddToMyCalendar();return false;"><span><?php echo $this->translate("Add to Calendar") ?></span></a><?php if(!$this->contentFullWidth):?></div><?php endif;?>
    <ul id="my-dropdown-menu" class="dropdown_menu" style="display:none;<?php if(!$this->contentFullWidth):?>top:30px;<?php endif;?>">
            <li><?php echo $this->googlelink; ?></li>  
            <li><a title="<?php echo $this->translate("Add to iCal"); ?>" href="<?php echo $this->url(array('action' => 'ical-outlook', 'event_id' => $this->subject()->getIdentity()), 'siteevent_dashboard', true); ?>"><span class="seao_icon_ical">&nbsp;</span><?php echo $this->translate("iCal"); ?></a></li>
            <li><a title="<?php echo $this->translate("Add to Outlook Calendar"); ?>" href="<?php echo $this->url(array('action' => 'ical-outlook', 'event_id' => $this->subject()->getIdentity()), 'siteevent_dashboard', true); ?>"><span class="seao_icon_outlook">&nbsp;</span><?php echo $this->translate("Outlook Calendar"); ?></a></li>
            <li><?php echo $this->yahoolink; ?></a></li>
    </ul>
</div>
            
        </div>
        <?php endif;?>
        

				<?php 
					$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
					$occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
					//CHECK IF THE EVENT IS PAST EVENT THEN ALSO DO NOT SHOW THE INVITE AND PROMOTE LINK
					$endDate = $view->locale()->toEventDateTime(Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->subject()->getIdentity(), 'DESC', $occurrence_id));
					$currentDate = $view->locale()->toEventDateTime(time());
					if (is_array($this->showContent) && (in_array('inviteGuest', $this->showContent)) && $this->subject()->authorization()->isAllowed($this->viewer(), 'invite') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.guests', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.automatically', 1) && strtotime($endDate) > strtotime($currentDate)): 
				?>
				<?php if (Engine_Api::_()->hasModuleBootstrap('siteeventinvite')):?>
					<div class="seaocore_like_button">
						<a href ="<?php echo $this->url(array('controller' => 'index', 'action' => 'friendseventinvite','siteevent_id' => $this->subject()->getIdentity(), 'occurrence_id' => $occurrence_id, 'user_id' => $this->subject()->owner_id), 'siteeventinvite_invite', true);?>">
							<span><?php echo $this->translate('Invite Guests') ?></span>
						</a>
					</div>
				<?php else:?>
					<div class="seaocore_like_button">
						<a href ="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $this->subject()->getIdentity(), 'occurrence_id' => $occurrence_id, 'format' => 'smoothbox'), 'siteevent_extended', true);?>")'>
							<span><?php echo $this->translate('Invite Guests') ?></span>
						</a>
					</div>
				<?php endif;?>
      <?php endif;?>

        <?php 
            if (is_array($this->showContent) && in_array('shareOptions', $this->showContent)) {
                $this->subject = $this->subject();
            include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareButtons.tpl';
            }
         ?>
        
      <?php if (is_array($this->showContent) && in_array('updateInfoButton', $this->showContent) && $this->can_edit) : ?>
				<div class="seaocore_button">
					<a href="<?php echo $this->url(array('action' => 'edit', $this->tablePrimaryFieldName => $this->subject()->getIdentity()), $moduleName."_specific", true); ?>">
						<span><?php echo $this->translate("Dashboard"); ?></span>
					</a>
				</div>
      <?php endif; ?>
  
      <?php if (is_array($this->showContent) && in_array('inviteRsvpButton', $this->showContent)) : ?>
				<div>
					<?php echo $this->content()->renderWidget("siteevent.invite-rsvp-siteevent"); ?>
				</div>
      <?php endif;?>

      <?php if (is_array($this->showContent) && in_array('optionsButton', $this->showContent)): ?>
				<?php $this->navigationProfile = $coreMenus->getNavigation($moduleName."_gutter"); ?>
				<?php if(count($this->navigationProfile) > 0):?>
					<div class="seaocore_button seaocore_profile_option_btn prelative">
						<a id="polldown_options_cover_photo" href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i><i class="icon_down"></i></a>
						<ul class="seaocore_profile_options_pulldown" id="sitecontent_cover_settings_options_pulldown" style="display:none;right:0;">
							<li>
							<?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setUlClass('navigation siteevents_gutter_options')->render(); ?>
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
<style>
.seaocore_profile_coverinfo_status *,
.seaocore_profile_coverinfo_status a:link{
    color:<?php echo $this->fontcolor;?> !important;
  }
</style>

<script type="text/javascript">
    var addToMyCalendarEnable = true;
    function AddToMyCalendar() {
        if( $('my-dropdown-menu'))
        $('my-dropdown-menu').toggle();
        addToMyCalendarEnable = true;
    }
    en4.core.runonce.add(function () {
     var addToMyCalendarHideClickEvent=function() {
        if(!addToMyCalendarEnable && $('my-dropdown-menu'))
          $('my-dropdown-menu').style.display = 'none';
        addToMyCalendarEnable=false; 
      };
      //hide on body clicdk
      $(document.body).addEvent('click',addToMyCalendarHideClickEvent.bind());
    });

</script>