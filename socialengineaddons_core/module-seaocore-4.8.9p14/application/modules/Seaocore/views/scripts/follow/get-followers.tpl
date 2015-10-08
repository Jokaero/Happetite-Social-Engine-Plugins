<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-followers.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php $paginater_vari = 0; if( !empty($this->paginator)) {  $paginater_vari = $this->paginator->getCurrentPageNumber(); }  ?>

<script type="text/javascript">

  var likeMemberPage = <?php if(empty($this->no_result_msg)){ echo sprintf('%d', $paginater_vari); } else { echo 1; } ?>;
  var call_status = '<?php echo $this->call_status; ?>';
  var resource_id = '<?php echo $this->resource_id; ?>';
  var resource_type = '<?php echo $this->resource_type; ?>';
  var url = en4.core.baseUrl + 'seaocore/follow/get-followers';
 
  function show_myfriend () {
    $('likes_popup_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/load.gif" /></center>';

    var request = new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'resource_type' : resource_type,
          'resource_id' : resource_id,
          'call_status' : call_status,
          'search' : this.value,
          'is_ajax':1
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          document.getElementById('likes_popup_content').innerHTML = responseHTML;
          en4.core.runonce.trigger();
        }
      });

      request.send();
    }

    en4.core.runonce.add(function() {

      document.getElementById('like_members_search_input').addEvent('keyup', function(e) {
        $('likes_popup_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/load.gif" alt="" style="margin-top:10px;" /></center>';

        var request = new Request.HTML({
          'url' : url,
          'data' : {
            'format' : 'html',
            'resource_type' : resource_type,
            'resource_id' : resource_id,
            'call_status' : call_status,
            'search' : this.value,
            'is_ajax':1
          },
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            document.getElementById('likes_popup_content').innerHTML = responseHTML;
            en4.core.runonce.trigger();
          }
        });
        request.send();
      });
    });

    var paginateLikeMembers = function(page, call_status) {
      var search_value = $('like_members_search_input').value;
      if (search_value == '') {
        search_value = '';
      }

      var request = new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'resource_type' : resource_type,
          'resource_id' : resource_id,
          'search' : search_value,
          'call_status' : call_status,
          'page' : page,
          'is_ajax':1
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          document.getElementById('likes_popup_content').innerHTML = responseHTML;
          en4.core.runonce.trigger();
        }
      });
      request.send();
    }

    var likedStatus = function(call_status) {
      var request = new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'resource_type' : resource_type,
          'resource_id' : resource_id,
          'call_status' : call_status
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          document.getElementById('like_members_profile').getParent().innerHTML = responseHTML;
          en4.core.runonce.trigger();
        }
      });
    request.send();
  }
</script>
</div>

<?php if(empty($this->is_ajax)): ?>
  <a id="like_members_profile" style="posituin:absolute;"></a>
  <div class="seaocore_members_popup">
    <div class="top">
      <?php
      if( $this->resource_type == 'sitestoreproduct_wishlist' || $this->resource_type == 'sitereview_wishlist')
      {
        if($this->call_status == 'public')	{
          $title = $this->translate('People Who Follow This Wishlist');
        }	else	{
          $title = $this->translate('Friends Who Follow This Wishlist');
        }
      }   
      else {
        if($this->call_status == 'public')	{
          $title = $this->translate('People Who Follow This');
        }	else	{
          $title = $this->translate('Friends Who Follow This');
        }
      }
        
      ?>
      <div class="heading"><?php echo $title; ?></div>
      <div class="seaocore_members_search_box">
        <div class="link">
          <a href="javascript:void(0);" class="<?php if($this->call_status == 'public') { echo 'selected'; } ?>" id="show_all" onclick="likedStatus('public');"><?php echo $this->translate('All '); ?>(<?php echo number_format($this->totalFollowCount); ?>)</a>
          <a href="javascript:void(0);" class="<?php if($this->call_status == 'friend') { echo 'selected'; } ?>" onclick="likedStatus('friend');"><?php echo $this->translate('Friends '); ?>(<?php echo number_format($this->totalFriendsFollow); ?>)</a>
        </div>

        <div class="seaocore_members_search fright">
          <input id="like_members_search_input" type="text" value="<?php echo $this->search; ?>" onfocus="if(this.value=='')this.value='';" onblur="if(this.value=='')this.value='';"/>
        </div>
      </div>
    </div>
    <div class="seaocore_members_popup_content" id="likes_popup_content">
  <?php endif; ?>
      
  <?php if( $this->paginator->getTotalItemCount() > 1 ): ?>
    <?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
      <div class="seaocore_members_popup_paging">
        <div id="user_like_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'paginateLikeMembers(likeMemberPage - 1, call_status)'
          )); ?>
        </div>
      </div>
    <?php endif; ?>
  <?php  endif; ?>
      
  <?php if($this->paginator->getTotalItemCount()): ?>
    <?php	foreach( $this->paginator as $user_info ): ?>
      <div class="item_member">
        <div class="item_member_thumb">
          <?php echo $this->htmlLink($user_info->getHref(), $this->itemPhoto($user_info, 'thumb.icon', $user_info->getTitle()), array('class' => 'item_photo sea_add_tooltip_link', 'target' => '_parent', 'title' => $user_info->getTitle(), 'rel'=> 'user'.' '.$user_info->getIdentity()));?>
        </div>
        <div class="item_member_details">
          <div class="item_member_name">
            <?php  $title1 = $user_info->getTitle(); ?>
            <?php  $truncatetitle = Engine_String::strlen($title1) > 20 ? Engine_String::substr($title1, 0, 20) . '..' : $title1?>
            <?php echo $this->htmlLink($user_info->getHref(), $truncatetitle, array('title' => $user_info->getTitle(), 'target' => '_parent', 'class' => 'sea_add_tooltip_link', 'rel'=> 'user'.' '.$user_info->getIdentity())); ?>
          </div>
        </div>	
      </div>
		<?php	endforeach; ?>
	<?php else: ?>
    <div class='tip' style="margin:10px 0 0 140px;">
      <span>
        <?php echo $this->translate('No results were found.') ?>
      </span>
    </div>
	<?php endif; ?>
      
  <?php if( $this->paginator->getTotalItemCount() > 1 ): ?>
    <?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
      <div class="seaocore_members_popup_paging">
        <div id="user_like_members_next" class="paginator_next" style="border-top-width:1px;">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'paginateLikeMembers(likeMemberPage + 1, call_status)'
          )); ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
      
  <?php if(empty($this->is_ajax)): ?>
	</div>
  </div>
  <div class="seaocore_members_popup_bottom">
    <button onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
<?php endif; ?>
