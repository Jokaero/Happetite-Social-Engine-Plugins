<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getyahoocontacts.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 if (!$this->errormessage) {
$isUesrLoggden = Engine_Api::_()->user()->getViewer()->getIdentity();
if (!empty($this->addtofriend) && !empty($isUesrLoggden)) {?>

  <div id='show_sitefriend' style="display:block;">
		
<?php
} 
else { ?>
  <div id='show_sitefriend' style="display:none;">
<?php
}  
$total = count($this->addtofriend);
if ($total > 0) { ?>
  <div class="header">	
			<div class="title">
       	
			<?php echo $this->translate("You have %s Linkedin contacts that you can add as your friends on",$total) . ' ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . "."; ?>
			</div>
			<div>
				<br /><?php echo $this->translate("Select the contacts to add as friends from the list below.");?>
			</div>		
		</div>
	
<?php }
if (!empty($this->addtofriend)) { ?>
	<div class="seaocore_user_contacts">
		<div class="seaocore_user_contacts_top">
			<div>
				 <input type="checkbox" name="select_all"  id="select_all" onclick="checkedAll();" checked="checked">
			</div>
			<div>
				<b><?php echo $this->translate("Select all");?></b>
			</div>
		</div>
		<div class="seaocore_user_contacts_list">
			<table width="100%" cellpadding="0" cellspacing="0">
			  <?php
			  $total_contacts = 0;
			   foreach($this->addtofriend as $values) { 
					
					$total_contacts++;?>
					<tr>
						<td width="4%">
							<input type="checkbox" name="contactname_"<?php echo $total_contacts;?>  id="contact_<?php echo $total_contacts;?>" value="<?php echo $values['user_id'];?>" checked="checked">
						</td>
						<td width="10%">
							<?php $user = Engine_Api::_()->user()->getUser($values['user_id']);
							
							echo $this->itemPhoto($user, 'thumb.icon');?>
						</td>
						<td>
							<b><?php echo $values['displayname'];?></b>
						</td>
					</tr>
			<?php	
			  } ?>
			  </table>
			</div>		  
		</div>
		<div class="seaocore_user_contacts_buttons">
			<button name="addtofriends"  id="addtofriends" onclick="sendFriendRequests();"><?php echo $this->translate("Add as Friends");?></button> <?php echo $this->translate("or");  ?> 
			<?php
					if( empty($isUesrLoggden) ) {
						echo '<a name="skiplink" id="skiplink" type="button" href="javascript:void(0);" onclick="skipForm(); return false;">' . $this->translate('skip') . '</a>';
					}else {
						echo '<button class="disabled" name="skip_addtofriends"  id="skip_addtofriends" onclick="skip_addtofriends();">' . $this->translate("Skip") . '</button>';
					}
			?>
		</div>
	
  <input type="hidden" name="total_contacts"  id="total_contacts" value="<?php echo $total_contacts;?>" >
<?php
}
?>

</div>

<?php 
if (empty($this->addtofriend) || empty($isUesrLoggden)) { ?>
  <div id='show_nonsitefriends' style="display:block;">
		
<?php
} else { ?>
  <div id='show_nonsitefriends' style="display:none;">
<?php
}
$total = count($this->addtononfriend);
if ($total > 0) { ?>
		<div class="header">	
					<?php if  (empty($this->moduletype)) : ?>	
			 <div class="title">	
				  <?php echo $this->translate("You have ");?> <?php echo $total . $this->translate(" Linkedin contacts that are not members of ") .  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . "."; ?>
				</div>
  			<div>
  				<br /><?php echo $this->translate("Select the contacts to invite from the list below.");?>
  			</div>
		<?php else : ?>	
			
			 <div class="title">	
				<?php echo $this->translate("Found %1s Linkedin contacts you can promote this %2s to.", $total, ucfirst($this->moduletype));?>
			</div>
			<div>
				<br /><?php echo $this->translate("Select the contacts to invite to your %s from the list below.", ucfirst($this->moduletype));?>
			</div>
			
			<?php endif;?>	
		</div>
<?php  
	
}
if (!empty($this->addtononfriend)) { ?>
	<div class="seaocore_user_contacts">
		<div class="seaocore_user_contacts_top">
			<div>
				<input type="checkbox" name="nonsiteselect_all"  id="nonsiteselect_all" onclick="nonsitecheckedAll();" checked="checked">
			</div>
			<div>
				<b><?php echo $this->translate("Select all");?></b>
			</div>
		</div>
		<div class="seaocore_user_contacts_list">
			<table width="100%" cellpadding="0" cellspacing="0">
				<?php
				  $total_contacts = 0;
				  foreach($this->addtononfriend as $values) {
					$total_contacts++;?>
					<tr>
						<td width="4%">
							<input type="checkbox" name="nonsitecontactname_"<?php echo $total_contacts;?>  id="nonsitecontact_<?php echo $total_contacts;?>" checked="checked" value='<?php echo $values['id'] . '#' . $values['name'];?>'>
						</td>
						<td width="10%">
							<b><?php if (!empty ($values['picture'])) { ?>
							
							 <img src="<?php echo $values['picture'];?>" alt="<?php echo $values['name'];?>" />
							<?php } else { ?>
							    <img src="http://s3.licdn.com/scds/common/u/img/icon/icon_no_photo_no_border_60x60.png" alt="<?php echo $values['name'];?>" />
							<?php } ?>
						</td>
						<td>
							<?php echo $values['name']; ?>
						</td>
					</tr>	
					
					<?php
					
				  } ?>
			  	<input type="hidden" name="nonsitetotal_contacts"  id="nonsitetotal_contacts" value="<?php echo $total_contacts;?>" >
			  </table>
  		</div>
		</div>
		<div class="seaocore_user_contacts_buttons">
		 <button name="invitefriends"  id="invitefriends" onclick="inviteFriends('linkedin');" style="float:left;margin-right:4px;"><?php echo $this->translate("Invite to Join");?></button>	
			<form action="" method="post" >	
			<?php echo $this->translate("or");  ?> <button class="disabled" name="skip_invite"  id="skip_invite"  type="submit"><?php echo $this->translate("Skip");?></button>
			</form>
		</div>
 
<?php
} ?>
</div>
<?php
}
else {
	echo "<div>" . $this->translate("All your imported contacts are already members of") .  ' ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . ".</div>";
}
?>

