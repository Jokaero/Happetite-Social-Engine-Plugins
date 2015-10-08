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
if (!$this->errormessage) :
  $isUesrLoggden = Engine_Api::_()->user()->getViewer()->getIdentity();
  if (!empty($this->addtofriend) && !empty($isUesrLoggden)) :
    ?>

    <div id='show_sitefriend' style="display:block;">

    <?php else: ?>
      <div id='show_sitefriend' style="display:none;">
      <?php
      endif;

      $total = count($this->addtofriend);
      if ($total > 0) :
        ?>
        <div class="header">	
          <div class="title">

            <?php echo $this->translate("You have %s Twitter contacts that you can add as your friends on", $total) . ' ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . "."; ?>
          </div>
          <div>
            <br /><?php echo $this->translate("Select the contacts to add as friends from the list below."); ?>
          </div>		
        </div>

        <?php
      endif;
      if (!empty($this->addtofriend)) :
        ?>
        <div class="seaocore_user_contacts">  
          <div class="seaocore_user_contacts_top">
            <div>
              <input type="checkbox" name="select_all"  id="select_all" onclick="checkedAll();" checked="checked" >
            </div>
            <div>
              <b><?php echo $this->translate("Select all"); ?></b>
            </div>
          </div>        
          <div class="seaocore_user_contacts_list" >
            <table width="100%" cellpadding="0" cellspacing="0">
              <?php
              $total_contacts = 0;
              foreach ($this->addtofriend as $values) :

                $total_contacts++;
                ?>
                <tr >
                  <td width="4%">
                    <input type="checkbox" name="contactname_"<?php echo $total_contacts; ?>  id="contact_<?php echo $total_contacts; ?>" value="<?php echo $values['user_id']; ?>" checked="checked">
                  </td>
                  <td width="10%">
                    <?php
                    $user = Engine_Api::_()->user()->getUser($values['user_id']);
                    echo $this->itemPhoto($user, 'thumb.icon');
                    ?>
                  </td>
                  <td>
                    <b><?php echo $values['displayname']; ?></b>
                  </td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>		  
        </div>
        <div class="seaocore_user_contacts_buttons">
          <button name="addtofriends"  id="addtofriends" onclick="sendFriendRequests();"><?php echo $this->translate("Add as Friends"); ?></button> <?php echo $this->translate("or"); ?> 
          <?php
          if (empty($isUesrLoggden)) :
            echo '<a name="skiplink" id="skiplink" type="button" href="javascript:void(0);" onclick="skipForm(); return false;">' . $this->translate('skip') . '</a>';
          else :
            echo '<button class="disabled" name="skip_addtofriends"  id="skip_addtofriends" onclick="skip_addtofriends();">' . $this->translate("Skip") . '</button>';
          endif;
          ?>
        </div>

        <input type="hidden" name="total_contacts"  id="total_contacts" value="<?php echo $total_contacts; ?>" >
        <?php
      endif;
      ?>

    </div>

    <?php if (empty($this->addtofriend) || empty($isUesrLoggden)) : ?>
      <div id='show_nonsitefriends' style="display:block;">

      <?php else : ?>
        <div id='show_nonsitefriends' style="display:none;">
        <?php
        endif;
        $total = count($this->addtononfriend);
        if ($total > 0) :
          ?>
          <div class="header">	
            <?php if (empty($this->moduletype)) : ?>	
              <div class="title">	
                <?php echo $this->translate("You have "); ?> <?php echo $total . $this->translate(" Twitter contacts that are not members of ") . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . "."; ?>
              </div>
              <div>
                <br /><?php echo $this->translate("Select the contacts to invite from the list below."); ?>
                <br /><?php echo '(' . $this->translate("Please note that you can send invitations to at max 20 contacts at a time.") . ')'; ?>
              </div>
            <?php else : ?>	

              <div class="title">	
                <?php echo $this->translate("Found %s Twitter contacts you can promote this Page to.", $total); ?>
              </div>
              <div>
                <br /><?php echo $this->translate("Select the contacts to invite to your Page from the list below."); ?>
              </div>

            <?php endif; ?>
          </div>
          <?php
        endif;
        if (!empty($this->addtononfriend)) :
          ?>
          <div class="seaocore_user_contacts">
            <?php if ($total <= 20) : ?>
              <div class="seaocore_user_contacts_top">
                <div>
                  <input type="checkbox" name="nonsiteselect_all"  id="nonsiteselect_all" onclick="nonsitecheckedAll();" >
                </div>
                <div>
                  <b><?php echo $this->translate("Select all"); ?></b>
                </div>
              </div>
            <?php endif; ?>
            <div class="seaocore_user_contacts_list" id="seaocore_user_contacts_list">
              <table width="100%" cellpadding="0" cellspacing="0">
                <?php
                $total_contacts = 0;
                foreach ($this->addtononfriend as $values) :
                  $total_contacts++;
                  ?>
                  <tr id="user_<?php echo $values['id']. '#' . $values['name'];?>">
                    <td width="4%">
                      <input type="checkbox" name="nonsitecontactname_<?php echo $total_contacts; ?>"  id="nonsitecontact_<?php echo $total_contacts; ?>" value='<?php echo $values['id']. '#' . $values['name'];; ?>'>
                    </td>
                    <td width="10%">
                      <b><?php if (!empty($values['picture'])) : ?>

                          <img src="<?php echo $values['picture']; ?>" alt="<?php echo $values['name']; ?>" />
                        <?php else : ?>
                          <img src="http://s3.licdn.com/scds/common/u/img/icon/icon_no_photo_no_border_60x60.png" alt="<?php echo $values['name']; ?>" />
                        <?php endif; ?>
                    </td>
                    <td>
                      <?php echo $values['name']; ?>
                    </td>
                  </tr>	

                <?php endforeach; ?>
                <input type="hidden" name="nonsitetotal_contacts"  id="nonsitetotal_contacts" value="<?php echo $total_contacts; ?>" >
              </table>
            </div>
          </div>
          <div class="seaocore_user_contacts_buttons">
            <button name="invitefriends"  id="invitefriends" onclick="inviteFriends('twitter');" style="float:left;margin-right:4px;"><?php echo $this->translate("Invite to Join"); ?></button>
            <form action="" method="post" >	
              <?php echo $this->translate("or"); ?> <button class="disabled" name="skip_invite"  id="skip_invite"  type="submit"><?php echo $this->translate("Skip"); ?></button>
            </form>
          </div>

        <?php endif; ?>
      </div>
      <?php
    else :
      echo "<div>" . $this->translate("All your imported contacts are already members of") . ' ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . ".</div>";
    endif;
    ?>

    <script type="text/javascript">
      var count_twiteuser = 0;
      var count_twit_request_sent = 0;
      function selectFriends(self) {
        if (self.checked)
          count_twiteuser++;
        if (self.checked == false) {
          count_twiteuser = parseInt(count_twiteuser) - parseInt(1);
        }
        if (parseInt(count_twiteuser) > parseInt(20)) {
          self.checked = false;
          count_twiteuser = parseInt(count_twiteuser) - parseInt(1);
          alert('<?php echo $this->translate("You have already selected 20 contacts from the list.") ?>')
        }
      }

      en4.core.runonce.add(function() {
        if (typeof $('show_nonsitefriends') != 'undefined') {
          $('show_nonsitefriends').getElements("input, checkbox", true).each(function(el) {

            el.addEvent('click', function() {
              selectFriends(this);

            })
          })
        }
      });



    </script>